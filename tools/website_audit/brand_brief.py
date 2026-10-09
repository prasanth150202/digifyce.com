"""Builds a short "what does this business do" summary purely from
already-scraped homepage data. Deterministic extraction, used as the
fallback brand brief when ai_analyzer.py's AI-driven analysis is
unavailable.

Real prose is preferred over raw body text: og:description and meta
description are usually hand-written by the site owner, and content_paragraphs
(spider.py) is limited to actual <p> tags outside nav/header/footer/button/
form chrome - never the grab-bag of every visible text node on the page,
which reads as a jumble of nav links, prices, and menu items on content-heavy
(e.g. e-commerce) pages."""

MAX_BRIEF_LENGTH = 400
MIN_DESCRIPTION_LENGTH = 30


def _strip_leading_duplicate(text, title):
    if title and text.lower().startswith(title.lower()):
        return text[len(title):].strip(' .-|—')
    return text


def _truncate(text, budget):
    if len(text) <= budget:
        return text
    snippet = text[:budget]
    if ' ' in snippet:
        snippet = snippet.rsplit(' ', 1)[0]
    # Unicode ellipsis, not "...", so it survives the rstrip('.') in build_brief.
    return snippet.strip() + '…'


def build_brief(homepage):
    if not homepage:
        return None

    title = (homepage.get('title') or '').strip()
    meta_description = (homepage.get('meta_description') or '').strip()
    og_description = (homepage.get('og_description') or '').strip()
    paragraphs = homepage.get('content_paragraphs') or []

    description = None
    for candidate in (og_description, meta_description):
        if candidate and len(candidate) >= MIN_DESCRIPTION_LENGTH:
            description = candidate
            break

    if not description:
        for paragraph in paragraphs:
            cleaned = _strip_leading_duplicate(paragraph, title)
            if len(cleaned) >= MIN_DESCRIPTION_LENGTH:
                description = cleaned
                break

    parts = []
    if title:
        parts.append(title)
    if description:
        budget = MAX_BRIEF_LENGTH - sum(len(p) for p in parts) - 2 * len(parts)
        parts.append(_truncate(description, max(budget, 0)))

    if not parts:
        return None

    return '. '.join(p.rstrip('.') for p in parts) + '.'
