"""Entry point invoked by AuditSpawner.php as:
    python.exe audit_runner.py <audit_id>

Loads the audit row's URL from the DB by ID (the URL never travels via
argv/shell), re-validates it's safe to fetch, runs the Scrapy crawl, scores
the result, and writes it back. Always leaves the row in a terminal state
(completed/failed) - never raises uncaught, so a job can never get stuck.
"""

import json
import os
import subprocess
import sys
import urllib.request
from urllib.parse import urljoin

from scrapy.crawler import CrawlerProcess

import ai_analyzer
import audit_rules
import brand_brief
import db
from spider import AuditSpider
from ssrf_guard import is_url_safe

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
SCREENSHOT_WORKER = os.path.join(os.path.dirname(__file__), 'screenshot_worker.py')
SCREENSHOT_TIMEOUT_SECONDS = 30


def check_url_exists(base_url, path):
    try:
        req = urllib.request.Request(urljoin(base_url, path), method='GET',
                                      headers={'User-Agent': 'DigifyceAuditBot/1.0'})
        with urllib.request.urlopen(req, timeout=8) as resp:
            return 200 <= resp.status < 400
    except Exception:
        return False


def set_status(conn, audit_id, **fields):
    columns = list(fields.keys())
    set_clause = ', '.join(f'{c} = %s' for c in columns) + ', updated_at = NOW()'
    values = [fields[c] for c in columns]
    values.append(audit_id)
    with conn.cursor() as cur:
        cur.execute(f'UPDATE website_audits SET {set_clause} WHERE id = %s', values)


def capture_screenshot_and_layout(url, token):
    """Best-effort: any failure/timeout here never fails the audit itself.
    Returns (screenshot_path, layout_data) - either may be None independently."""
    screenshot_dir = os.path.join(REPO_ROOT, 'storage', 'audits', 'screenshots', token)
    screenshot_output = os.path.join(screenshot_dir, 'homepage.png')
    layout_output = os.path.join(screenshot_dir, 'layout.json')

    screenshot_path = None
    layout_data = None

    try:
        os.makedirs(screenshot_dir, exist_ok=True)
        result = subprocess.run(
            [sys.executable, SCREENSHOT_WORKER, url, screenshot_output, layout_output],
            timeout=SCREENSHOT_TIMEOUT_SECONDS,
            capture_output=True,
        )
        if result.returncode == 0 and os.path.exists(screenshot_output):
            screenshot_path = 'storage/audits/screenshots/' + token + '/homepage.png'
        if os.path.exists(layout_output):
            with open(layout_output, 'r', encoding='utf-8') as f:
                layout_data = json.load(f)
    except Exception as exc:  # noqa: BLE001 - screenshot/layout data is optional, never fatal
        print(f'screenshot/layout capture errored: {exc}', file=sys.stderr)

    return screenshot_path, layout_data


def main(audit_id):
    conn = db.get_connection()

    with conn.cursor() as cur:
        cur.execute(
            '''SELECT wa.url, wa.token, l.industry, l.has_ads, l.ad_spend, l.roas
               FROM website_audits wa
               JOIN lead_form_submissions l ON l.id = wa.lead_id
               WHERE wa.id = %s''',
            (audit_id,)
        )
        row = cur.fetchone()

    if not row:
        return

    url = row['url']
    token = row['token']
    lead_context = {
        'industry': row.get('industry') or None,
        'runs_ads': row.get('has_ads') == 'yes',
        'ad_spend': row.get('ad_spend') if row.get('has_ads') == 'yes' else None,
        'roas': row.get('roas') if row.get('has_ads') == 'yes' else None,
    }

    safe, reason = is_url_safe(url)
    if not safe:
        set_status(conn, audit_id, status='failed', error_message=f'URL failed safety check: {reason}')
        return

    set_status(conn, audit_id, status='running')

    robots_txt_found = check_url_exists(url, '/robots.txt')
    sitemap_found = check_url_exists(url, '/sitemap.xml')

    results = []
    process = CrawlerProcess(settings={'TELNETCONSOLE_ENABLED': False}, install_root_handler=False)
    process.crawl(AuditSpider, seed_url=url, results=results)
    process.start()

    has_good_page = any(not p.get('error') for p in results)
    screenshot_path, layout_data = (
        capture_screenshot_and_layout(url, token) if has_good_page else (None, None)
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
            ai_result = ai_analyzer.analyze(results, robots_txt_found, sitemap_found, layout_data, lead_context)
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
        'screenshot_path': screenshot_path,
        'layout_data': layout_data,
    }

    set_status(
        conn, audit_id,
        status='completed',
        score=score,
        findings_json=json.dumps(findings),
        brand_brief=brief,
        scraped_data_json=json.dumps(scraped_data),
        ai_plan_json=plan_json,
        ai_model_used=model_used,
        executive_summary=executive_summary,
        ai_priority_actions_json=priority_actions_json,
        ai_ad_strategy_json=ad_strategy_json,
        ai_case_study_matches_json=case_study_matches_json,
    )


if __name__ == '__main__':
    if len(sys.argv) < 2:
        sys.exit('usage: audit_runner.py <audit_id>')

    audit_id_arg = int(sys.argv[1])

    try:
        main(audit_id_arg)
    except Exception as exc:  # noqa: BLE001 - top-level safety net, must never leave a row stuck
        print(f'audit_runner failed for audit_id={audit_id_arg}: {exc}', file=sys.stderr)
        try:
            conn = db.get_connection()
            set_status(conn, audit_id_arg, status='failed', error_message=str(exc)[:1000])
        except Exception as inner_exc:
            print(f'also failed to record failure state: {inner_exc}', file=sys.stderr)
