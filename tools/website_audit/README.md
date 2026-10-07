# Website audit service

The Python half of the free website audit offered on the lead form: Scrapy
crawl, Playwright screenshot, OpenAI analysis and case study matching. It
runs as its own web service on Render; the PHP site and MySQL stay on
Hostinger. This service never connects to MySQL.

## How a job flows

```
Lead form (PHP)  -> INSERT website_audits row (pending)
AuditSpawner.php -> POST {service}/audit  (signed job: url, token, lead context, case study list)
                    service replies 202 at once, row -> running
Service          -> POST {PHP_BASE_URL}/app/api/website_audit_callback.php   status: running
                 -> crawl, screenshot, OpenAI (a few minutes)
                 -> POST {PHP_BASE_URL}/app/api/audit_case_study_file.php    downloads each case study (cached)
                 -> POST {PHP_BASE_URL}/app/api/website_audit_callback.php   status: completed/failed + results + screenshot
PHP              -> saves results to MySQL, screenshot to storage/audits/screenshots/<token>/
Browser          -> keeps polling app/api/website_audit_status.php until completed/failed
```

Every request in both directions is signed with HMAC-SHA256 using
`AUDIT_SHARED_SECRET` (`signing.py` / `app/utilities/AuditSignature.php`).
If the service can't be reached, the row stays `pending` and the status
endpoint re-dispatches it (`max_attempts` in `config/website_audit.php`).

## Settings

Both sides need the same `AUDIT_SHARED_SECRET`. Generate one with
`php -r "echo bin2hex(random_bytes(32));"`.

| Where | Key | Value |
|---|---|---|
| Hostinger `.env` | `AUDIT_SERVICE_URL` | the Render service URL, e.g. `https://digifyce-website-audit.onrender.com` |
| Hostinger `.env` | `AUDIT_SHARED_SECRET` | the shared secret |
| Render env | `AUDIT_SHARED_SECRET` | the same shared secret |
| Render env | `PHP_BASE_URL` | the PHP site's final public URL, e.g. `https://digifyce.com` (no redirects: use https and the apex domain) |
| Render env | `OPENAI_API_KEY` | OpenAI key (no longer needed on Hostinger) |
| Render env (optional) | `AUDIT_MAX_CONCURRENT_JOBS` | audits run at once, default `2` |
| Render env (optional) | `WEBSITE_AUDIT_AI_ENABLED` | `false` to force rule-based scoring |

## Deploying on Render

1. Push this branch to GitHub.
2. Render dashboard → **New → Blueprint** → pick the repo. Render reads
   `render.yaml` at the repo root (Docker, `tools/website_audit/Dockerfile`,
   branch `feature/website-audit-v2`). Fill in the three secret env vars
   when prompted.
3. Wait for the first build (several minutes: it installs Chromium) and
   check `https://<service>.onrender.com/health` returns `{"ok": true}`.
4. Put `AUDIT_SERVICE_URL` and `AUDIT_SHARED_SECRET` in Hostinger's `.env`.
5. Submit the lead form with a website and watch the Render logs.

To deploy manually instead of the Blueprint: New → Web Service → Docker,
Dockerfile path `tools/website_audit/Dockerfile`, Docker context
`tools/website_audit`, health check path `/health`, same env vars.

## Running locally (XAMPP)

```
cd tools/website_audit
venv\Scripts\python.exe -m pip install -r requirements.txt
venv\Scripts\python.exe -m playwright install chromium
venv\Scripts\python.exe api.py
```

Keep it running while testing the lead form. The repo-root `.env` supplies
everything (the Python side reads it when the variables aren't set in the
environment):

```
AUDIT_SERVICE_URL=http://127.0.0.1:8000
AUDIT_SHARED_SECRET=<any random value>
PHP_BASE_URL=http://localhost/digifyce
OPENAI_API_KEY=...
```

## Things to know

- **Free plan sleeps** after ~15 minutes without requests; the first audit
  after that waits for a cold start (often a minute or more). The PHP side
  re-dispatches until the service is awake. A paid instance doesn't sleep.
- **Memory**: each running audit starts a headless Chromium. Keep
  `AUDIT_MAX_CONCURRENT_JOBS` at 1-2 on the free plan's 512 MB.
- **Redeploys drop in-flight audits**: the queue is in memory. Rows left
  `running` are failed by the status endpoint after `max_running_seconds`
  and can be retried from the admin panel.
- **Case study files** are downloaded from the PHP site and cached by
  version on the service's temporary disk, so a re-upload in the admin
  panel takes effect on the next audit.
