"""Supplies Digifyce's case study documents to ai_analyzer.py. The files
live on the PHP site (uploaded via app/admin/case_studies.php); the job
payload only lists them (id, title, description, file_name, version), and
each one is downloaded from the PHP site and text-extracted the first time
it's seen.

Extracted text is cached on local disk keyed by id + version, so a
re-uploaded document (new version) is picked up on the next audit while
unchanged ones aren't re-downloaded for every audit. The cache is on this
instance's temporary disk - losing it (redeploy, free-tier spin-down) only
costs one re-download per document."""

import hashlib
import os
import sys
import tempfile

import case_study_extractor
import php_client

CACHE_DIR = os.path.join(tempfile.gettempdir(), 'digifyce_case_study_cache')


def _cache_path(case_study):
    key = f"{case_study.get('id')}:{case_study.get('version')}"
    return os.path.join(CACHE_DIR, hashlib.sha256(key.encode('utf-8')).hexdigest() + '.txt')


def _extract(case_study):
    cache_path = _cache_path(case_study)
    if os.path.exists(cache_path):
        with open(cache_path, 'r', encoding='utf-8') as f:
            return f.read() or None

    os.makedirs(CACHE_DIR, exist_ok=True)
    # The extractor picks its parser from the file extension.
    ext = os.path.splitext(case_study.get('file_name') or '')[1].lower()
    fd, download_path = tempfile.mkstemp(suffix=ext)
    os.close(fd)
    try:
        php_client.download_case_study(case_study['id'], download_path)
        content = case_study_extractor.extract_text(download_path)
    finally:
        try:
            os.remove(download_path)
        except OSError:
            pass

    # Cached even when empty (e.g. a scanned PDF with no text layer), so an
    # unparseable document isn't re-downloaded on every audit. Written to a
    # temp file first so a concurrent job never reads a half-written cache.
    fd, tmp_cache = tempfile.mkstemp(dir=CACHE_DIR, suffix='.tmp')
    with os.fdopen(fd, 'w', encoding='utf-8') as f:
        f.write(content or '')
    os.replace(tmp_cache, cache_path)
    return content


def iter_documents(case_studies):
    """Yields {'title', 'description', 'content'} in the given order. Lazy,
    so a caller that stops early (library size budget reached) doesn't
    download the rest. A document that fails to download/parse is skipped -
    one bad upload never fails the audit."""
    for case_study in case_studies or []:
        try:
            content = _extract(case_study)
        except Exception as exc:  # noqa: BLE001 - supplementary context, never fatal
            print(f"case study {case_study.get('id')} unavailable: {exc}", file=sys.stderr)
            continue
        if content:
            yield {
                'title': case_study.get('title'),
                'description': case_study.get('description') or '',
                'content': content,
            }
