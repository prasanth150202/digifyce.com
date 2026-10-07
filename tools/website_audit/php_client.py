"""Outbound calls from this service back to the PHP site: posting audit
status/results (app/api/website_audit_callback.php) and downloading case
study documents (app/api/audit_case_study_file.php). Both are POSTs signed
with signing.py - POST because the site's .htaccess rewrites GET *.php URLs
to clean URLs, and a redirect would drop the body anyway."""

import json
import sys
import time
import urllib.error
import urllib.request

import settings
import signing

USER_AGENT = 'DigifyceAuditService/1.0'
CALLBACK_PATH = '/app/api/website_audit_callback.php'
CASE_STUDY_FILE_PATH = '/app/api/audit_case_study_file.php'
CALLBACK_TIMEOUT_SECONDS = 60
# A finished audit's result is the whole point of the job, so a brief
# Hostinger hiccup shouldn't throw it away: 4 attempts over ~1 minute.
CALLBACK_RETRY_DELAYS_SECONDS = (5, 15, 45)
DOWNLOAD_TIMEOUT_SECONDS = 90
MAX_DOWNLOAD_BYTES = 30 * 1024 * 1024


class _NoRedirect(urllib.request.HTTPRedirectHandler):
    """urllib would silently turn a redirected POST into a bodyless GET
    (e.g. http -> https, www -> apex), which the PHP side then rejects with
    a confusing 405. Fail loudly with the real cause instead."""

    def redirect_request(self, req, fp, code, msg, headers, newurl):
        raise urllib.error.HTTPError(
            req.full_url, code,
            f'redirected to {newurl} - set PHP_BASE_URL to the final URL', headers, fp,
        )


_opener = urllib.request.build_opener(_NoRedirect)


def _signed_post(path, payload, timeout):
    base_url = settings.get('PHP_BASE_URL')
    secret = settings.get('AUDIT_SHARED_SECRET')
    if not base_url or not secret:
        raise RuntimeError('PHP_BASE_URL / AUDIT_SHARED_SECRET not configured')

    body = json.dumps(payload).encode('utf-8')
    headers = {'Content-Type': 'application/json', 'User-Agent': USER_AGENT}
    headers.update(signing.signed_headers(secret, body))
    req = urllib.request.Request(base_url.rstrip('/') + path, data=body, headers=headers, method='POST')
    return _opener.open(req, timeout=timeout)


def send_callback(payload):
    """POSTs one status update/result. Returns True once the PHP side
    accepted it. 5xx and network errors are retried; anything else (bad
    signature, unknown audit, redirect) is a config problem retrying can't fix."""
    label = f"audit {payload.get('audit_id')} ({payload.get('status')})"
    attempts = len(CALLBACK_RETRY_DELAYS_SECONDS) + 1

    for attempt in range(1, attempts + 1):
        try:
            with _signed_post(CALLBACK_PATH, payload, CALLBACK_TIMEOUT_SECONDS):
                return True
        except urllib.error.HTTPError as exc:
            print(f'callback for {label} rejected: HTTP {exc.code} {exc.reason}', file=sys.stderr)
            if exc.code < 500:
                return False
        except Exception as exc:  # noqa: BLE001 - network errors are retried below
            print(f'callback for {label} failed (attempt {attempt}/{attempts}): {exc}', file=sys.stderr)

        if attempt < attempts:
            time.sleep(CALLBACK_RETRY_DELAYS_SECONDS[attempt - 1])

    return False


def download_case_study(case_study_id, dest_path):
    """Streams one case study document to dest_path. Raises on any failure."""
    total = 0
    with _signed_post(CASE_STUDY_FILE_PATH, {'id': case_study_id}, DOWNLOAD_TIMEOUT_SECONDS) as resp:
        with open(dest_path, 'wb') as f:
            while True:
                chunk = resp.read(64 * 1024)
                if not chunk:
                    break
                total += len(chunk)
                if total > MAX_DOWNLOAD_BYTES:
                    raise RuntimeError(f'case study {case_study_id} exceeds {MAX_DOWNLOAD_BYTES} bytes')
                f.write(chunk)
