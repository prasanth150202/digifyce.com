"""HTTP entry point for the website audit service (deployed on Render).

The PHP site POSTs a signed job to /audit (app/utilities/AuditSpawner.php)
and gets 202 back immediately; the audit then runs in the background and
its status/result is POSTed back to the PHP site (php_client.py), which
owns the database - nothing here touches MySQL.

Each audit runs as its own audit_runner.py subprocess (Scrapy's reactor can
only start once per process, and a hung crawl/browser can be killed on a
timeout). AUDIT_MAX_CONCURRENT_JOBS bounds how many run at once, since each
launches a headless Chromium; extra jobs wait in an in-memory queue. A job
that's already queued or running is acknowledged but not queued twice, so
the PHP side re-dispatching after a timeout can't double-run an audit.

Run locally:  python api.py   (Flask dev server on 127.0.0.1:8000)
On Render:    gunicorn with exactly one worker process (see Dockerfile),
              since the queue and duplicate check live in its memory.
"""

import json
import os
import queue
import re
import shutil
import signal
import subprocess
import sys
import tempfile
import threading

from flask import Flask, jsonify, request

import php_client
import settings
import signing

RUNNER_SCRIPT = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'audit_runner.py')
MAX_CONCURRENT_JOBS = max(1, int(settings.get('AUDIT_MAX_CONCURRENT_JOBS', '2')))
# Under the PHP side's max_running_seconds (1350, counted from the
# 'running' callback), so an overrunning job is reported failed from here
# before PHP gives up on it.
JOB_TIMEOUT_SECONDS = int(settings.get('AUDIT_JOB_TIMEOUT_SECONDS', '1260'))
TOKEN_PATTERN = re.compile(r'^[a-f0-9]{32}$')

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 1024 * 1024

_jobs = queue.Queue()
_active_ids = set()
_active_lock = threading.Lock()


def _validate_job(job):
    if not isinstance(job, dict):
        return 'job must be a JSON object'
    audit_id = job.get('audit_id')
    if not isinstance(audit_id, int) or isinstance(audit_id, bool) or audit_id <= 0:
        return 'audit_id must be a positive integer'
    if not isinstance(job.get('token'), str) or not TOKEN_PATTERN.match(job['token']):
        return 'token must be 32 hex characters'
    if not isinstance(job.get('url'), str) or not job['url'].startswith(('http://', 'https://')):
        return 'url must be an http(s) URL'
    if not isinstance(job.get('lead_context') or {}, dict):
        return 'lead_context must be an object'
    case_studies = job.get('case_studies') or []
    if not isinstance(case_studies, list) or not all(
        isinstance(cs, dict) and isinstance(cs.get('id'), int) for cs in case_studies
    ):
        return 'case_studies must be a list of objects with an integer id'
    return None


def _kill_process_tree(proc):
    # audit_runner launches screenshot_worker -> Chromium; on Linux the
    # runner leads its own process group, so one signal takes all of them.
    if os.name == 'posix':
        try:
            os.killpg(proc.pid, signal.SIGKILL)
        except ProcessLookupError:
            pass
    else:
        proc.kill()


def _run_job(job):
    audit_id = job['audit_id']
    php_client.send_callback({'audit_id': audit_id, 'token': job['token'], 'status': 'running'})

    work_dir = tempfile.mkdtemp(prefix=f'audit_{audit_id}_')
    job_path = os.path.join(work_dir, 'job.json')
    result_path = os.path.join(work_dir, 'result.json')
    try:
        with open(job_path, 'w', encoding='utf-8') as f:
            json.dump(job, f)

        # stdout/stderr are inherited, so the runner's logs stream straight
        # into the service's own log (the Render dashboard).
        proc = subprocess.Popen(
            [sys.executable, RUNNER_SCRIPT, job_path, result_path],
            cwd=os.path.dirname(RUNNER_SCRIPT),
            start_new_session=(os.name == 'posix'),
        )
        try:
            proc.wait(timeout=JOB_TIMEOUT_SECONDS)
            with open(result_path, 'r', encoding='utf-8') as f:
                result = json.load(f)
        except subprocess.TimeoutExpired:
            _kill_process_tree(proc)
            proc.wait()
            result = {'status': 'failed', 'error_message': 'Audit timed out.'}
        except (OSError, ValueError) as exc:
            result = {'status': 'failed', 'error_message': f'Audit worker exited without a result (code {proc.returncode}): {exc}'[:1000]}

        result.update({'audit_id': audit_id, 'token': job['token']})
        print(f'[audit {audit_id}] finished: {result["status"]}', file=sys.stderr)
        if not php_client.send_callback(result):
            print(f'[audit {audit_id}] result could not be delivered to the PHP site', file=sys.stderr)
    finally:
        shutil.rmtree(work_dir, ignore_errors=True)


def _worker():
    while True:
        job = _jobs.get()
        try:
            _run_job(job)
        except Exception as exc:  # noqa: BLE001 - one bad job must never kill the worker thread
            print(f'[audit {job["audit_id"]}] worker error: {exc}', file=sys.stderr)
        finally:
            with _active_lock:
                _active_ids.discard(job['audit_id'])


for _ in range(MAX_CONCURRENT_JOBS):
    threading.Thread(target=_worker, daemon=True).start()

if not settings.get('AUDIT_SHARED_SECRET') or not settings.get('PHP_BASE_URL'):
    print('WARNING: AUDIT_SHARED_SECRET / PHP_BASE_URL not set - every job will be rejected', file=sys.stderr)


@app.get('/health')
def health():
    return jsonify({'ok': True})


@app.post('/audit')
def submit_audit():
    body = request.get_data()
    if not signing.verify(
        settings.get('AUDIT_SHARED_SECRET'),
        request.headers.get('X-Audit-Timestamp'),
        request.headers.get('X-Audit-Signature'),
        body,
    ):
        return jsonify({'error': 'invalid signature'}), 401

    try:
        job = json.loads(body)
    except ValueError:
        return jsonify({'error': 'invalid JSON'}), 400

    error = _validate_job(job)
    if error:
        return jsonify({'error': error}), 400

    with _active_lock:
        if job['audit_id'] in _active_ids:
            return jsonify({'accepted': True, 'duplicate': True}), 202
        _active_ids.add(job['audit_id'])

    _jobs.put(job)
    print(f'[audit {job["audit_id"]}] queued ({_jobs.qsize()} waiting)', file=sys.stderr)
    return jsonify({'accepted': True}), 202


if __name__ == '__main__':
    app.run(host='127.0.0.1', port=int(settings.get('PORT', '8000')), threaded=True)
