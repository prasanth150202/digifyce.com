#!/usr/bin/env python3
"""Digifyce blog autopilot, step 2: insert internal links and calls to action, then run quality checks.

Usage:
    py -3 postprocess.py draft.json [--out ready.json] [--no-network]

draft.json (written by the writing step):
    {
      "topic_id": "custom-shopify-theme-development",
      "title": "...",              # becomes the blog title / H1
      "slug": "...",
      "meta_title": "...", "meta_description": "...", "excerpt": "...",
      "primary_keyword": "...",
      "category": "Shopify", "tags": ["Shopify"], "author": "Digifyce",
      "body_html": "<p>..</p><h2>..</h2>...<h2>Frequently Asked Questions</h2><h3>Q</h3><p>A</p>..."
    }

Output ready.json is the same plus "content" (final HTML) and "lint". Exit code 0 = passed, 2 = errors
(publish.py refuses to send a post with errors). Standard library only.
"""
import argparse
import json
import re
import sys
import urllib.request
import urllib.error
from html import unescape
from pathlib import Path

ROOT = Path(__file__).resolve().parent
CFG = json.loads((ROOT / "config.json").read_text(encoding="utf-8"))
LIM = CFG["limits"]

TAG_RE = re.compile(r"(<[^>]+>)")
SKIP_TAGS = {"a", "h1", "h2", "h3", "h4", "h5", "h6", "script", "style", "button", "table"}
DASH_RE = re.compile(r"[-‐-―−]")
ALLOWED_TAGS = {"p", "h2", "h3", "h4", "ul", "ol", "li", "strong", "b", "em", "i", "a", "table", "thead",
                "tbody", "tr", "th", "td", "blockquote", "br", "span"}


def text_of(html_str):
    return re.sub(r"\s+", " ", unescape(re.sub(r"<[^>]+>", " ", html_str))).strip()


def words(s):
    return len(s.split())


# ---------------------------------------------------------------- internal links
def live(url, cache={}):
    """HEAD the link target; links to pages that do not exist are skipped, not inserted."""
    if url in cache:
        return cache[url]
    ok = False
    try:
        req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "DigifyceBlogAutopilot/1.0"})
        with urllib.request.urlopen(req, timeout=20) as r:
            ok = r.status == 200
    except urllib.error.HTTPError as e:
        ok = e.code in (200, 301, 302)
    except Exception:
        ok = False
    cache[url] = ok
    return ok


def insert_links(body, own_slug, network=True):
    rules = [dict(r, used=False) for r in CFG["link_rules"]]
    used_urls = set()
    inserted = []
    tokens = TAG_RE.split(body)
    stack = []            # open tags that stop linking (a, headings, table ...)
    in_faq = False
    pending_h2 = False
    out = []
    for tok in tokens:
        if TAG_RE.fullmatch(tok):
            m = re.match(r"<\s*(/?)\s*([a-zA-Z0-9]+)", tok)
            if m:
                closing, name = m.group(1) == "/", m.group(2).lower()
                if name in SKIP_TAGS:
                    if closing:
                        if name in stack:
                            stack.remove(name)
                    elif not tok.endswith("/>"):
                        stack.append(name)
                if name == "h2":
                    pending_h2 = not closing
            out.append(tok)
            continue
        # text node
        if pending_h2 and re.search(r"frequently asked|faq", tok, re.I):
            in_faq = True
        if stack or in_faq or len(inserted) >= CFG["max_auto_links"] or not tok.strip():
            out.append(tok)
            continue
        # find every rule that matches this text, then take the LONGEST matching phrase, so
        # "Shopify development cost in India" links to the cost guide, not to the generic page.
        best = None
        for r in rules:
            if r["used"] or r["url"] in used_urls or r["url"].rstrip("/").endswith("/" + own_slug):
                continue
            for phrase in r["phrases"]:
                m = re.search(r"(?<![\w-])(" + re.escape(phrase) + r")(?![\w-])", tok, re.I)
                if m and (best is None or len(m.group(1)) > len(best[1].group(1))):
                    best = (r, m)
        if best:
            r, hit = best
            if network and not live(r["url"]):
                r["used"] = True       # target does not exist (yet): skip the rule for this post
            else:
                tok = tok[:hit.start()] + '<a href="%s">%s</a>' % (r["url"], hit.group(1)) + tok[hit.end():]
                r["used"] = True
                used_urls.add(r["url"])
                inserted.append((hit.group(1), r["url"]))
        out.append(tok)
    return "".join(out), inserted


# ---------------------------------------------------------------- calls to action
def cta_html(slug, text, label):
    c = CFG["cta"]
    url = "%s?%s&amp;utm_content=%s" % (c["base_url"], c["utm"], slug)
    return ('<p>%s</p>\n<p style="text-align: center; margin: 2rem 0;"><a href="%s" style="%s" '
            'target="_blank" rel="noopener">%s</a></p>\n' % (text, url, c["button_style"], label))


def insert_ctas(body, slug, variant):
    v = CFG["cta"]["variants"].get(variant) or CFG["cta"]["variants"]["general"]
    h2s = [m for m in re.finditer(r"<h2[^>]*>(.*?)</h2>", body, re.S | re.I)]
    faq = next((m for m in h2s if re.search(r"frequently asked|faq", m.group(1), re.I)), None)
    body_h2s = [m for m in h2s if m is not faq]
    placed = []
    # end call to action goes just before the FAQ (or at the very end)
    end_block = cta_html(slug, v["end_text"], v["end_label"])
    if faq:
        body = body[:faq.start()] + end_block + body[faq.start():]
    else:
        body = body + "\n" + end_block
    placed.append("end")
    # mid call to action goes before the third content section
    if len(body_h2s) >= 4:
        pos = body_h2s[2].start()
        body = body[:pos] + cta_html(slug, v["mid_text"], v["mid_label"]) + body[pos:]
        placed.append("mid")
    return body, placed


# ---------------------------------------------------------------- lint
def existing_slugs(network=True):
    if not network:
        return set()
    try:
        req = urllib.request.Request(CFG["sitemap"], headers={"User-Agent": "DigifyceBlogAutopilot/1.0"})
        xml = urllib.request.urlopen(req, timeout=30).read().decode("utf-8", "replace")
        return set(re.findall(r"/blog/([^<\s]+)</loc>", xml))
    except Exception:
        return None


def lint(d, body, network=True):
    errs, warns = [], []
    title, meta = d["title"], d["meta_description"]
    mt = d["meta_title"]
    kw = d["primary_keyword"].lower()

    # required fields
    for k in ("title", "slug", "meta_title", "meta_description", "excerpt", "primary_keyword", "body_html"):
        if not str(d.get(k, "")).strip():
            errs.append("missing field: " + k)
    if re.search(r"<h1[\s>]", body, re.I):
        errs.append("body must not contain an H1 (the template renders the title as H1)")
    if re.search(r"<(script|iframe|style|form|input|button|img|video)\b", body, re.I):
        errs.append("body contains a tag that is not allowed (script, iframe, style, form, input, button, img, video)")
    bad_tags = {t.lower() for t in re.findall(r"<\s*([a-zA-Z0-9]+)", body)} - ALLOWED_TAGS
    if bad_tags:
        errs.append("tags not on the allowed list: " + ", ".join(sorted(bad_tags)))

    # the no hyphens rule: visible text, title, meta, excerpt
    visible = " ".join(t for t in TAG_RE.split(body) if not TAG_RE.fullmatch(t))
    for label, s in (("title", title), ("meta_title", mt), ("meta_description", meta), ("excerpt", d["excerpt"]), ("body", visible)):
        hits = DASH_RE.findall(unescape(s))
        if hits:
            ctx = re.search(r".{0,25}[-‐-―−].{0,25}", unescape(s))
            errs.append("hyphen or dash found in %s (rule: none allowed): ...%s..." % (label, ctx.group(0) if ctx else ""))

    # lengths
    n = len(mt)
    if n > LIM["title_hard_max"] or n < LIM["title_hard_min"]:
        errs.append("meta_title is %d characters (must be %d to %d)" % (n, LIM["title_min"], LIM["title_max"]))
    elif n > LIM["title_max"] or n < LIM["title_min"]:
        warns.append("meta_title is %d characters (ideal %d to %d)" % (n, LIM["title_min"], LIM["title_max"]))
    n = len(meta)
    if n > LIM["meta_hard_max"] or n < LIM["meta_hard_min"]:
        errs.append("meta_description is %d characters (must be %d to %d)" % (n, LIM["meta_min"], LIM["meta_max"]))
    elif n > LIM["meta_max"] or n < LIM["meta_min"]:
        warns.append("meta_description is %d characters (ideal %d to %d)" % (n, LIM["meta_min"], LIM["meta_max"]))
    wc = words(text_of(body))
    if wc < LIM["words_hard_min"]:
        errs.append("only %d words (minimum %d)" % (wc, LIM["words_hard_min"]))
    elif wc < LIM["words_min"]:
        warns.append("%d words (target %d or more)" % (wc, LIM["words_min"]))

    # keyword placement
    first_p = re.search(r"<p[^>]*>(.*?)</p>", body, re.S | re.I)
    first_text = text_of(first_p.group(1)).lower() if first_p else ""
    if kw not in title.lower() and kw not in mt.lower():
        errs.append("primary keyword is not in the title or meta title")
    if kw not in first_text:
        errs.append("primary keyword is not in the first paragraph")
    if kw not in meta.lower():
        warns.append("primary keyword is not in the meta description")
    if kw.replace(" ", "-") not in d["slug"]:
        warns.append("primary keyword is not in the slug")
    if text_of(body).lower().count(kw) < 2:
        warns.append("primary keyword appears only once in the body")

    # FAQ structure and answer lengths
    h2s = list(re.finditer(r"<h2[^>]*>(.*?)</h2>", body, re.S | re.I))
    faq = next((m for m in h2s if re.search(r"frequently asked|faq", m.group(1), re.I)), None)
    if not faq:
        errs.append("no FAQ section (an H2 'Frequently Asked Questions' with H3 questions)")
    else:
        tail = body[faq.end():]
        nxt = re.search(r"<h2[\s>]", tail, re.I)
        tail = tail[:nxt.start()] if nxt else tail
        parts = re.split(r"(<h3[^>]*>.*?</h3>)", tail, flags=re.S | re.I)
        qs = [(text_of(parts[i]), words(text_of(parts[i + 1]))) for i in range(1, len(parts) - 1, 2)]
        if len(qs) < LIM["faq_min_questions"]:
            errs.append("FAQ has %d questions (need at least %d, each as an H3)" % (len(qs), LIM["faq_min_questions"]))
        for q, w in qs:
            if w < LIM["faq_answer_hard_min"] or w > LIM["faq_answer_hard_max"]:
                errs.append("FAQ answer is %d words (target %d to %d): %s" % (w, LIM["faq_answer_min"], LIM["faq_answer_max"], q[:60]))
            elif w < LIM["faq_answer_min"] or w > LIM["faq_answer_max"]:
                warns.append("FAQ answer is %d words (target %d to %d): %s" % (w, LIM["faq_answer_min"], LIM["faq_answer_max"], q[:60]))

    # claims that need a source
    for para in re.findall(r"<(?:p|li)[^>]*>(.*?)</(?:p|li)>", body, re.S | re.I):
        t = text_of(para)
        has_source = bool(re.search(r'href="https?://(?!digifyce\.com)', para))
        if not has_source and (re.search(r"\b\d{1,3}(?:\.\d+)?\s?%", t) or re.search(r"\b(study|survey|according to|research shows|statistics show|report found)\b", t, re.I)):
            warns.append("statistic or claim without a source link, check or remove: " + t[:90])
    for ph in CFG["banned_phrases"]:
        if ph.lower() in text_of(body).lower():
            warns.append("filler phrase to rewrite: '%s'" % ph)

    # duplicates
    slugs = existing_slugs(network)
    if slugs is None:
        warns.append("could not read the sitemap to check for duplicate slugs")
    elif d["slug"] in slugs:
        errs.append("a post with this slug already exists on the site")
    return errs, warns, wc


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("draft")
    ap.add_argument("--out", default=None)
    ap.add_argument("--no-network", action="store_true", help="skip link and duplicate checks that call the live site")
    a = ap.parse_args()
    net = not a.no_network

    d = json.loads(Path(a.draft).read_text(encoding="utf-8"))
    body = d["body_html"]
    variant = "shopify" if re.search(r"shopify", d.get("category", "") + d.get("primary_keyword", ""), re.I) else "general"

    body, links = insert_links(body, d["slug"], net)
    body, ctas = insert_ctas(body, d["slug"], variant)
    errs, warns, wc = lint(d, body, net)
    if variant == "shopify" and "https://digifyce.com/shopify-development\"" not in body:
        warns.append("no link to the Shopify development page was inserted: mention 'Shopify development' naturally once in the post")

    out = dict(d, content=body, lint={"errors": errs, "warnings": warns, "words": wc,
                                      "links_inserted": [{"text": t, "url": u} for t, u in links], "ctas": ctas})
    out_path = Path(a.out) if a.out else Path(a.draft).with_name(Path(a.draft).stem + ".ready.json")
    out_path.write_text(json.dumps(out, ensure_ascii=False, indent=2), encoding="utf-8")

    print("Words: %d | links inserted: %d | CTAs: %s" % (wc, len(links), ", ".join(ctas)))
    for t, u in links:
        print("  link: '%s' -> %s" % (t, u))
    for w in warns:
        print("  WARN:  " + w)
    for e in errs:
        print("  ERROR: " + e)
    print("RESULT: %s (%s)" % ("FAILED" if errs else "PASSED", out_path))
    sys.exit(2 if errs else 0)


if __name__ == "__main__":
    main()
