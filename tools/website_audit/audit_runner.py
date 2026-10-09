"""Runs one website audit, invoked by api.py as:
    python audit_runner.py <job_json_path> <result_json_path>

No database access: the PHP site sends everything needed in the job (audit
id, token, URL, lead context, case study list), and the result is written
to <result_json_path> for api.py to POST back to the PHP site, which saves
it to MySQL. Each audit runs in its own process because Scrapy's reactor
can only be started once per process.

Re-validates the URL is safe to fetch, runs the Scrapy crawl, captures the
homepage screenshot, and scores the result. Always writes a terminal result
(completed/failed) - never raises uncaught, so a job can never get stuck.
"""

import base64
import json
import os
import re
import subprocess
import sys
import urllib.request
from urllib.parse import urljoin

from scrapy.crawler import CrawlerProcess

import ai_analyzer
import audit_rules
import brand_brief
from spider import AuditSpider
from ssrf_guard import is_url_safe

SCREENSHOT_WORKER = os.path.join(os.path.dirname(__file__), 'screenshot_worker.py')
SCREENSHOT_TIMEOUT_SECONDS = 30
TOKEN_PATTERN = re.compile(r'^[a-f0-9]{32}$')
# Where app/api/website_audit_callback.php saves the screenshot on the PHP
# site - recorded in scraped_data_json so the report can link to it.
SCREENSHOT_PUBLIC_PATH = 'storage/audits/screenshots/{token}/homepage.png'


def check_url_exists(base_url, path):
    try:
        req = urllib.request.Request(urljoin(base_url, path), method='GET',
                                      headers={'User-Agent': 'DigifyceAuditBot/1.0'})
        with urllib.request.urlopen(req, timeout=8) as resp:
            return 200 <= resp.status < 400
    except Exception:
        return False


def capture_screenshot_and_layout(url, work_dir):
    """Best-effort: any failure/timeout here never fails the audit itself.
    Returns (screenshot_png_base64, layout_data) - either may be None independently."""
    screenshot_output = os.path.join(work_dir, 'homepage.png')
    layout_output = os.path.join(work_dir, 'layout.json')

    screenshot_b64 = None
    layout_data = None

    try:
        result = subprocess.run(
            [sys.executable, SCREENSHOT_WORKER, url, screenshot_output, layout_output],
            timeout=SCREENSHOT_TIMEOUT_SECONDS,
            capture_output=True,
        )
        if result.returncode == 0 and os.path.exists(screenshot_output):
            with open(screenshot_output, 'rb') as f:
                screenshot_b64 = base64.b64encode(f.read()).decode('ascii')
        if os.path.exists(layout_output):
            with open(layout_output, 'r', encoding='utf-8') as f:
                layout_data = json.load(f)
    except Exception as exc:  # noqa: BLE001 - screenshot/layout data is optional, never fatal
        print(f'screenshot/layout capture errored: {exc}', file=sys.stderr)

    return screenshot_b64, layout_data


def run(job, work_dir):
    url = job.get('url')
    token = job.get('token')
    if not isinstance(url, str) or not isinstance(token, str) or not TOKEN_PATTERN.match(token):
        return {'status': 'failed', 'error_message': 'Malformed audit job.'}

    lead_context = job.get('lead_context') or {}

    safe, reason = is_url_safe(url)
    if not safe:
        return {'status': 'failed', 'error_message': f'URL failed safety check: {reason}'}

    robots_txt_found = check_url_exists(url, '/robots.txt')
    sitemap_found = check_url_exists(url, '/sitemap.xml')

    results = []
    process = CrawlerProcess(settings={'TELNETCONSOLE_ENABLED': False}, install_root_handler=False)
    process.crawl(AuditSpider, seed_url=url, results=results)
    process.start()

    has_good_page = any(not p.get('error') for p in results)
    screenshot_b64, layout_data = (
        capture_screenshot_and_layout(url, work_dir) if has_good_page else (None, None)
    )

    plan_json = None
    model_used = None
    executive_summary = None
    priority_actions_json = None
    ad_strategy_json = None
    case_study_matches_json = None

    if not has_good_page:
        outcome = audit_rules.evaluate(results, robots_txt_found, sitemap_found, layout_data)
        score = None
        findings = [{
            'category': 'Terminal',
            'severity': 'info',
            'title': 'Audit unavailable',
            'description': outcome['terminal_message'],
        }]
        brief = None
    else:
        try:
            ai_result = ai_analyzer.analyze(
                results, robots_txt_found, sitemap_found, layout_data, lead_context, job.get('case_studies'),
            )
            score = ai_result['score']
            findings = ai_result['findings']
            brief = ai_result['brand_brief']
            plan_json = json.dumps(ai_result['plan'])
            model_used = ai_result['model_used']
            executive_summary = ai_result['executive_summary']
            priority_actions_json = json.dumps(ai_result['priority_actions'])
            ad_strategy_json = json.dumps(ai_result['ad_strategy'])
            if ai_result.get('case_study_matches'):
                case_study_matches_json = json.dumps(ai_result['case_study_matches'])
        except Exception as exc:  # noqa: BLE001 - any AI failure falls back to the deterministic engine
            print(f'AI analysis failed, falling back to rule-based scoring: {exc}', file=sys.stderr)
            outcome = audit_rules.evaluate(results, robots_txt_found, sitemap_found, layout_data)
            score = outcome['score']
            findings = outcome['findings']
            homepage = next((p for p in results if not p.get('error')), None)
            brief = brand_brief.build_brief(homepage)
            model_used = 'rule-based-fallback'

    scraped_data = {
        'pages': results,
        'robots_txt_found': robots_txt_found,
        'sitemap_found': sitemap_found,
        'screenshot_path': SCREENSHOT_PUBLIC_PATH.format(token=token) if screenshot_b64 else None,
        'layout_data': layout_data,
    }

    # Keys are website_audits column names; JSON columns are pre-serialized
    # here exactly as they were when this script wrote to MySQL directly.
    return {
        'status': 'completed',
        'fields': {
            'score': score,
            'findings_json': json.dumps(findings),
            'brand_brief': brief,
            'scraped_data_json': json.dumps(scraped_data),
            'ai_plan_json': plan_json,
            'ai_model_used': model_used,
            'executive_summary': executive_summary,
            'ai_priority_actions_json': priority_actions_json,
            'ai_ad_strategy_json': ad_strategy_json,
            'ai_case_study_matches_json': case_study_matches_json,
        },
        'screenshot_png_base64': screenshot_b64,
    }


def write_result(result_path, result):
    tmp_path = result_path + '.tmp'
    with open(tmp_path, 'w', encoding='utf-8') as f:
        json.dump(result, f)
    os.replace(tmp_path, result_path)


if __name__ == '__main__':
    if len(sys.argv) < 3:
        sys.exit('usage: audit_runner.py <job_json_path> <result_json_path>')

    job_path, result_path = sys.argv[1], sys.argv[2]

    try:
        with open(job_path, 'r', encoding='utf-8') as f:
            job_data = json.load(f)
        audit_result = run(job_data, os.path.dirname(os.path.abspath(result_path)))
    except Exception as exc:  # noqa: BLE001 - top-level safety net, must never leave a job without a result
        print(f'audit_runner failed for {job_path}: {exc}', file=sys.stderr)
        audit_result = {'status': 'failed', 'error_message': str(exc)[:1000]}

    write_result(result_path, audit_result)
