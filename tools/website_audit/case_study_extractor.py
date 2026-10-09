"""Extracts plain text from case study source documents (PDF/DOCX/PPTX/XLSX)
uploaded through the admin panel (app/admin/case_studies.php), so
ai_analyzer.py can hand Digifyce's own case study library to the model as
reference material when auditing a prospective client's site.

Works on a local copy downloaded from the PHP site by case_study_library.py,
which also caches the extracted text per document version - so a re-upload
takes effect on the next audit with no separate re-processing step.
"""

import os

# A per-document ceiling only, to stop one absurdly large file from eating
# the whole budget itself - real pitch decks have turned out to run 40+
# slides/30,000+ characters and often bundle several distinct mini case
# studies in one file, so this needs real headroom, not just "the gist".
# gpt-4o-mini's 128k-token context window has plenty of room: even the
# combined case-study library total (bounded separately in ai_analyzer.py's
# MAX_TOTAL_CASE_STUDY_CHARS) plus a 60-page crawl summary comes nowhere
# close to it.
MAX_CHARS_PER_DOCUMENT = 20000


def _extract_pdf(path):
    from pypdf import PdfReader
    reader = PdfReader(path)
    return '\n'.join((page.extract_text() or '') for page in reader.pages)


def _extract_docx(path):
    import docx
    document = docx.Document(path)
    parts = [p.text for p in document.paragraphs if p.text.strip()]
    for table in document.tables:
        for row in table.rows:
            parts.append(' | '.join(cell.text for cell in row.cells))
    return '\n'.join(parts)


def _extract_pptx(path):
    from pptx import Presentation
    presentation = Presentation(path)
    parts = []
    for slide in presentation.slides:
        for shape in slide.shapes:
            if shape.has_text_frame:
                text = '\n'.join(p.text for p in shape.text_frame.paragraphs if p.text.strip())
                if text:
                    parts.append(text)
    return '\n'.join(parts)


def _extract_xlsx(path):
    from openpyxl import load_workbook
    workbook = load_workbook(path, data_only=True, read_only=True)
    parts = []
    # read_only mode keeps the file handle open until close() - which would
    # stop case_study_library.py deleting the downloaded copy on Windows.
    try:
        for sheet in workbook.worksheets:
            for row in sheet.iter_rows(values_only=True):
                cells = [str(c) for c in row if c is not None]
                if cells:
                    parts.append(' | '.join(cells))
    finally:
        workbook.close()
    return '\n'.join(parts)


_EXTRACTORS = {
    '.pdf': _extract_pdf,
    '.docx': _extract_docx,
    '.pptx': _extract_pptx,
    '.xlsx': _extract_xlsx,
}


def extract_text(abs_path):
    """Returns extracted plain text, or None if the file is missing,
    unsupported, or fails to parse for any reason - callers should just skip
    that one document rather than fail the whole audit over a single bad
    upload."""
    if not abs_path or not os.path.exists(abs_path):
        return None

    ext = os.path.splitext(abs_path)[1].lower()
    extractor = _EXTRACTORS.get(ext)
    if not extractor:
        return None

    try:
        text = extractor(abs_path)
    except Exception:
        return None

    text = ' '.join(text.split())
    if not text:
        return None

    return text[:MAX_CHARS_PER_DOCUMENT]
