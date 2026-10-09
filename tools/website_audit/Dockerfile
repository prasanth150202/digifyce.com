# Website audit service for Render - see README.md.
#
# Debian bookworm because Playwright supports it for `install --with-deps`,
# which installs Chromium plus every system library it needs. Installing the
# browser at build time (rather than starting from a prebuilt Playwright
# image) keeps it matched to the playwright version pinned in requirements.txt.
FROM python:3.11-slim-bookworm

ENV PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1 \
    PLAYWRIGHT_BROWSERS_PATH=/ms-playwright

WORKDIR /app

COPY requirements.txt .
RUN pip install -r requirements.txt \
    && python -m playwright install --with-deps chromium \
    && rm -rf /var/lib/apt/lists/*

COPY . .

# Exactly one worker process: the job queue and duplicate-job check live in
# its memory (see api.py). Threads only serve the quick /audit and /health
# requests - audits themselves run in background subprocesses.
CMD ["sh", "-c", "exec gunicorn --workers 1 --threads 4 --timeout 120 --bind 0.0.0.0:${PORT:-10000} api:app"]
