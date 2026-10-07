"""AI-powered audit analysis via the OpenAI API.

Three sequential calls: (1) generate a tailored plan from whole-site crawl
data + the lead's business context, (2) execute that plan against the full
crawl data to produce a detailed, whole-site audit: scored findings (each
attributed to the page it came from), a brand brief, an executive summary, a
prioritized action plan, and paid-advertising channel/budget guidance, then
(3) a dedicated call matching the audit against Digifyce's own case-study
library - kept separate from (2) so it isn't shortchanged by competing for
attention with everything else in one giant combined response.

This is the primary analysis step; audit_rules.py/brand_brief.py remain the
deterministic fallback audit_runner.py uses when this call fails for any
reason (no API key, disabled via env, network/timeout error, unparseable or
invalid response) - an audit must always reach a terminal state, never get
stuck waiting on an external API.
"""

import json
import sys

from openai import BadRequestError, OpenAI

import case_study_library
import settings

MODEL = 'gpt-4o-mini'
# 0, not a small-but-nonzero value: these are structured analysis/extraction
# calls (score a site, match against a fixed library), not creative writing.
# Re-running the same audit still won't be byte-identical - the crawl itself
# picks up real variance (response times, occasionally different pages) -
# but this removes the model's own sampling as a second, compounding source
# of inconsistency on top of that.
TEMPERATURE = 0
PLAN_TIMEOUT_SECONDS = 45
EXECUTE_TIMEOUT_SECONDS = 120
CASE_STUDY_TIMEOUT_SECONDS = 75
PLAN_MAX_TOKENS = 2000
EXECUTE_MAX_TOKENS = 8000
CASE_STUDY_MAX_TOKENS = 6000
MAX_PAGES_IN_PROMPT = 60
MAX_PARAGRAPHS_PER_PAGE = 5
MAX_CASE_STUDIES_IN_PROMPT = 20
# Bounds the combined size of the whole case-study library in one audit
# call, on top of case_study_extractor's own per-document cap - protects
# against many large documents adding up, while still comfortably fitting
# gpt-4o-mini's 128k-token context alongside the crawl summary.
MAX_TOTAL_CASE_STUDY_CHARS = 150000

ALLOWED_SEVERITIES = {'critical', 'high', 'medium', 'low', 'info'}
ALLOWED_EFFORTS = {'low', 'medium', 'high'}
ALLOWED_AD_PRIORITIES = {'high', 'medium', 'low'}

PLAN_SYSTEM_PROMPT = """You are a senior digital marketing strategist creating a tailored audit plan for a prospective client's website, based only on data already collected by an automated crawler (you did not browse the site yourself and have no other information about it). The crawl covers the whole site it could reach (homepage plus every other page it found), not just the homepage.

Given the business's context (industry, whether they run paid ads, current ad spend/ROAS if any) and what the crawl found across all pages, decide what should be prioritized when auditing THIS specific business - a local service business, an e-commerce store, and a B2B SaaS company have different priorities, and a business already spending significant ad budget cares more about conversion/landing-page issues and ad-spend efficiency than one with no ads. Also decide whether advertising channel/budget strategy is something worth addressing for this business given its context. Do not produce a generic one-size-fits-all checklist.

Respond with ONLY a single JSON object of this exact shape, no markdown formatting, no commentary before or after it:
{
  "business_type": "short description of what kind of business/site this looks like",
  "priorities": [
    {"category": "SEO|Technical|Mobile|Content|Social|Layout & Design|Conversion", "reason": "why this matters for this specific business", "priority": "high|medium|low"}
  ],
  "focus_notes": "1-3 sentences of additional guidance for whoever executes this audit, including anything relevant to advertising strategy"
}"""

EXECUTE_SYSTEM_PROMPT = """You are an expert website auditor and digital marketing strategist for a marketing agency, producing a detailed, whole-site audit for a prospective client who filled out a contact form and requested a free website audit. This report is shown directly to that business owner, so write it in plain, client-friendly language - specific and honest, never generic filler, never internal jargon.

You are given (1) an audit plan describing what to prioritize for this business, and (2) the raw crawled data - content/metadata for every page the crawler reached on this site, plus measured layout/design signals (has_nav, has_footer, has_cta_above_fold, font_family_count, contrast_ratio, small_tap_target_ratio, mobile_overflow) from the rendered homepage. You do not have the screenshot image itself, only these measured signals - reason only from them for layout/design findings.

Produce a genuinely thorough, whole-site audit, not a homepage-only skim:
- Review every page provided, not just the homepage. Cite the specific page URL a finding came from in its "page" field (use "Site-wide" only for issues that apply across multiple/all pages).
- Follow the plan's priorities, adjusting only if the crawled data clearly warrants it. Cover SEO, technical health, mobile-friendliness, content quality, social sharing, layout/visual design, and conversion optimization.
- Be specific to this site - cite the actual title, missing element, or metric you observed; never write advice generic enough for any website.
- Balance criticism with what's working - note real strengths, not just problems.
- Produce as many findings as are genuinely warranted by what you observed across all pages - typically 10-25 for a multi-page audit. Don't pad with filler, but don't artificially cap either; a bigger site with more real issues should get more findings.
- Score 0-100 for overall site quality.
- brand_brief: 2-4 sentences on what this business does, who it's for, and what it sells - read like a colleague's quick summary, not marketing filler. Base "who it's for" on the full range of pages provided, not just the first few - if the crawled pages show products/services for multiple audiences (e.g. both men and women, multiple industries, several distinct service lines), say so explicitly rather than generalizing to whichever audience happens to be most represented in what you were given.
- executive_summary: 3-5 sentences giving the business owner a plain-language TL;DR verdict on their site's overall health and the single biggest opportunity.
- priority_actions: the top 3-6 things this business should fix first, ordered by impact (most impactful first) - these should be a curated highlight reel of the most important findings, phrased as direct actions.
- ad_strategy: give this business a concrete, always-actionable plan for where and how to spend on advertising - never simply tell them to hold off or not advertise. Ground this in the ACTUAL pages/products/services you saw in the crawl data, not generic advice:
  - target_pages: pick specific existing pages (real URLs from the crawl) that are the best candidates to send paid traffic to - e.g. a strong product/service page, a well-written landing page - or that would be good candidates once a specific fix is made. For each, say why it's a good target and what (if anything) needs fixing first.
  - targeting_ideas: based on the actual products/services/content you saw, suggest who to target and what search terms or interests/audiences make sense - specific to this business, never generic ("your target audience" is not acceptable - name the actual audience/products).
  - growth_potential: give an honest, hedged estimate of the improvement they could realistically see (e.g. a conversion rate or ROAS range) if they act on your recommendations - reference their current spend/ROAS for context if provided. Frame this explicitly as an estimate/opportunity based on the specific issues you found, never a guarantee, and never invent precise statistics you have no basis for.
  - Base the overall plan on their industry, current ad situation (if provided), and whether their site is ready to convert ad traffic well (tie this to your conversion/CTA findings - sending paid traffic to a site with no clear CTA wastes budget, so account for that rather than using it as a reason to stop). If they already run ads, factor in their stated spend/ROAS.
  - recommended_channels: specific channels (e.g. Google Search Ads, Google Shopping, Meta/Instagram Ads, local/Google Business Profile, SEO/organic as an alternative to paid) with a reason and priority for each - only recommend channels that genuinely fit this business type.
  - budget_guidance must always be forward-looking and constructive: if conversion issues would currently waste ad spend, frame it as a phased plan (e.g. "start with a small test budget on X while fixing Y, then scale up") rather than advising them to wait.

Respond with ONLY a single JSON object of this exact shape, no markdown formatting, no commentary before or after it:
{
  "score": 0,
  "brand_brief": "2-4 sentences on what this business does, who it's for, and what it sells",
  "executive_summary": "3-5 sentence plain-language TL;DR verdict",
  "findings": [
    {"category": "SEO|Technical|Mobile|Content|Social|Layout & Design|Conversion", "severity": "critical|high|medium|low|info", "title": "short finding title", "description": "1-3 sentences: what's wrong (or working well) and the specific, actionable fix", "page": "the page URL this applies to, or 'Site-wide'"}
  ],
  "priority_actions": [
    {"action": "short, direct action title", "why": "1-2 sentences on the impact of doing this", "effort": "low|medium|high"}
  ],
  "ad_strategy": {
    "should_invest_in_ads": true,
    "landing_page_readiness": "1-3 sentences on whether the site is currently ready to convert paid traffic well, and why",
    "target_pages": [
      {"page": "actual URL from the crawled data", "why": "why this page is a good ad destination, or what it promotes", "action_needed": "what to fix first, or 'Ready to use as-is'"}
    ],
    "targeting_ideas": "2-3 sentences on who/what to target, specific to this business's actual products/services/audience",
    "growth_potential": "1-2 sentences: a realistic, hedged estimate of the improvement possible if these recommendations are followed",
    "recommended_channels": [
      {"channel": "e.g. Google Search Ads", "reason": "why this channel fits this business", "priority": "high|medium|low"}
    ],
    "budget_guidance": "2-4 sentences of plain-language guidance on how this business should think about ad budget allocation given their context"
  }
}"""

CASE_STUDY_SYSTEM_PROMPT = """You are a digital marketing strategist at Digifyce. Your job is retrieval-augmented: STUDY Digifyce's own library of real past proposals and case studies written for OTHER brands - full documents describing what Digifyce actually planned or did for them - and use that knowledge to figure out what Digifyce could implement for the brand that was just audited. Then turn that into a list of CONCRETE THINGS DIGIFYCE WOULD IMPLEMENT for this brand, each one backed by proof pulled from a specific document in the library.

You are given (1) a summary of the business being audited (business type/industry, brand brief, executive summary, current ad/marketing situation, and the website audit findings), and (2) Digifyce's document library (a mix of completed case studies with reported outcomes, AND forward-looking pitches/proposals for prospective work with no reported outcome yet).

This is the ONLY task in this call - give it your full attention and reasoning effort, do not shortchange it.

READ THE FULL DOCUMENTS, NOT JUST THE WEBSITE-RELATED PARTS: these library documents are real, full marketing proposals and case studies - most of them cover far more than a website. They can include branding and positioning, social media strategy and content calendars, paid ad campaigns (Google, Meta, Instagram, etc.), influencer or offline/on-ground marketing, customer engagement and retention plans, sales-funnel and lead-generation design, launch strategy, and more. Study each document's full content for ideas, not just whichever paragraph happens to overlap with a website audit finding.

DO NOT LIMIT YOURSELF TO WEBSITE FIXES: the website audit findings are only one input signal about this business's current state - they are not the boundary of what you may recommend. Think about this brand ENTIRELY, the way a full agency proposal would: if a matched document's plan included a social media push, an ad campaign, a rebrand, a content strategy, an offline activation, or anything else Digifyce could plausibly also bring to this brand, and the underlying goal or challenge genuinely applies, include it. The purpose of this section is to show the client the full range of what Digifyce can implement for their brand's overall growth - not a narrow "here are your website bugs" list (that already exists elsewhere in this audit).

NEVER just restate a website finding as an "implementation": if a recommendation would read as a rephrasing of something in the findings list - fixing alt text, meta descriptions, page titles, canonical tags, page load speed, technical SEO, broken links, or similar on-page/technical items - do not include it, no matter how well the library backs it. Those are already covered elsewhere in this report; a reader who sees them again here will conclude this section is just the audit repeated back at them. This section exists specifically to answer "what else would Digifyce do for my brand" - content and storytelling, social media, paid/organic channel strategy, retention and CRM/lifecycle automation, branding and positioning, offline activation, funnel and launch design - the kinds of work the findings list structurally cannot capture.

CRITICAL - get the emphasis right: the output is a recommendation list for THIS business, not a set of case-study summaries. Do NOT write this as "here's a business we helped and what we did for them." Instead, lead with the actual implementation - the specific thing Digifyce would build/run/launch for THIS business - and use the matched document only as the proof backing that recommendation. A reader should come away thinking "here's exactly what Digifyce would do for my brand, and here's evidence it works" - not "here's a story about someone else's business."

Ground each implementation in an underlying library document, matched on the UNDERLYING CHALLENGE, GOAL, OR TACTIC, not on industry or on "is this about a website" - a business in a completely different industry with a similar underlying goal (growing awareness, generating leads, improving conversion, building a content/social presence, running a launch) is a genuinely good match, since the strategy and reasoning transfer across industries even when the products don't. Prefer same-industry proof if it exists, but don't require it.

Be generous rather than perfectionist about what counts as usable proof: nearly every document in a library this size contains at least one applicable growth or marketing idea for this business, given its industry and current situation. If a document's plan could plausibly also work for this business, even partially, that is enough to build a recommendation around it - you do not need every detail to line up. Empty recommended_implementations should be rare with a library this size; reserve it for cases where the library genuinely contains nothing usable. Do not pad with implementations that have no real backing, but do not hold out for a perfect match either.

Return an array of up to 3-5 implementations, ordered by impact/relevance to this business - most important first, and covering DIFFERENT kinds of work where the library supports it (don't return 3 variations on the same website tweak if the library also shows we could pitch social media, ads, or content work for this brand). For EACH implementation:
- title: a short, concrete, actionable title for what Digifyce would implement for THIS business (e.g. "Run a phased paid-ads launch for your best-selling products", "Build a content + social media engine around your brand story", "Rebuild product-page SEO around the terms your customers actually search") - this is the headline, not a case study name.
- description: 3-5 sentences of REAL detail on what Digifyce would specifically build/run/launch for this business - the concrete tactics, channels, and steps, covering whatever scope genuinely fits (website, ads, social, content, branding, offline, funnel, etc.) - not artificially narrowed to the website. Reference this business's own context/products/findings where relevant (from the summary you were given). Someone should be able to picture the actual work from this description, and it must read as a plan FOR THIS BUSINESS, not a description of someone else's project.
- proof: an object grounding this recommendation in a real library document -
  - case_study_title: the matched document's title, exactly as given in the library (or the specific named project/business inside it, if the document is a compilation covering several).
  - industry: what kind of business the matched document was actually for, in a few words (e.g. "Water pump manufacturer", "Taxi service app"). Be upfront when it differs from this business's own industry.
  - summary: 1-2 sentences of brief context only - the problem that other business had and what Digifyce did about it - just enough to establish this recommendation isn't invented. Keep this short; the description field above is where the real detail on THIS business's plan belongs, not here.
  - metrics: an array of {"label", "value"} pairs for EVERY concrete number, percentage, multiplier, or count actually written in that document's text (e.g. [{"label": "Reach increase", "value": "3X"}, {"label": "Conversion rate", "value": "23%"}, {"label": "Cost per acquisition", "value": "Rs.200"}]). Include as many as are genuinely stated - pull them all out, don't stop at one. Search the source text for these figures before writing anything; never invent a number that isn't literally in the text. If the matched document is a proposal/pitch with no reported outcome, metrics MUST be an empty array - that's the correct, expected answer for most of this library, not a failure.

Respond with ONLY a single JSON object of this exact shape, no markdown formatting, no commentary before or after it:
{
  "recommended_implementations": [
    {
      "title": "short, concrete, actionable title for what Digifyce would implement for THIS business",
      "description": "3-5 sentences on the specific plan Digifyce would execute for THIS business",
      "proof": {
        "case_study_title": "the matched document's title, exactly as given in the library",
        "industry": "what kind of business the matched document was for",
        "summary": "1-2 sentences of brief context on the matched document's own challenge/approach",
        "metrics": [{"label": "e.g. Reach increase", "value": "e.g. 3X"}]
      }
    }
  ]
}
recommended_implementations should be an empty array [] if nothing in the library gives genuine backing for a recommendation, or no library was provided."""


class AIAnalysisError(Exception):
    """Raised for any anticipated AI-path failure. The caller (audit_runner.py)
    catches this - and any other unexpected exception, via its existing broad
    except - and falls back to the deterministic engine."""


def _get_client():
    if settings.get('WEBSITE_AUDIT_AI_ENABLED', 'true').strip().lower() == 'false':
        raise AIAnalysisError('AI analysis disabled via WEBSITE_AUDIT_AI_ENABLED')

    api_key = settings.get('OPENAI_API_KEY')
    if not api_key:
        raise AIAnalysisError('OPENAI_API_KEY not configured')

    # max_retries=1 (vs the SDK's default of 2) bounds worst-case latency per
    # call to roughly 2x its timeout instead of 3x, so a slow patch can't blow
    # well past the audit's overall time budget.
    return OpenAI(api_key=api_key, max_retries=1)


def _condense_page(page):
    if page.get('error'):
        return {'url': page.get('url'), 'error': True, 'status_code': page.get('status_code')}
    return {
        'url': page.get('url'),
        'status_code': page.get('status_code'),
        'title': page.get('title'),
        'meta_description': page.get('meta_description'),
        'og_description': page.get('og_description'),
        'has_viewport_meta': page.get('has_viewport_meta'),
        'canonical_url': page.get('canonical_url'),
        'has_og_tags': page.get('has_og_tags'),
        'h1_list': page.get('h1_list'),
        'images_total': page.get('images_total'),
        'images_missing_alt': page.get('images_missing_alt'),
        'word_count': page.get('word_count'),
        'content_paragraphs': (page.get('content_paragraphs') or [])[:MAX_PARAGRAPHS_PER_PAGE],
        'internal_links_count': page.get('internal_links_count'),
        'external_links_count': page.get('external_links_count'),
        'is_https': page.get('is_https'),
        'response_time_ms': page.get('response_time_ms'),
    }


def _build_crawl_summary(pages, robots_txt_found, sitemap_found, layout_data):
    return {
        'pages': [_condense_page(p) for p in pages[:MAX_PAGES_IN_PROMPT]],
        'robots_txt_found': robots_txt_found,
        'sitemap_found': sitemap_found,
        'layout_signals': layout_data,
    }


def _extract_json_object(text):
    """Defensive fallback for when the model wraps its JSON in a code fence
    or adds stray commentary despite instructions not to: scans for the
    first balanced {...} block, tracking string/escape state so nested
    braces inside string values don't break the scan."""
    start = text.find('{')
    while start != -1:
        depth = 0
        in_string = False
        escape = False
        for i in range(start, len(text)):
            ch = text[i]
            if in_string:
                if escape:
                    escape = False
                elif ch == '\\':
                    escape = True
                elif ch == '"':
                    in_string = False
                continue
            if ch == '"':
                in_string = True
            elif ch == '{':
                depth += 1
            elif ch == '}':
                depth -= 1
                if depth == 0:
                    return text[start:i + 1]
        start = text.find('{', start + 1)
    raise AIAnalysisError('No balanced JSON object found in AI response')


def _parse_json_response(text):
    text = (text or '').strip()
    try:
        return json.loads(text)
    except json.JSONDecodeError:
        pass
    try:
        return json.loads(_extract_json_object(text))
    except (json.JSONDecodeError, AIAnalysisError) as exc:
        raise AIAnalysisError(f'Could not parse JSON from AI response: {exc}') from exc


def _chat_json(client, system_prompt, user_prompt, timeout_seconds, max_tokens):
    messages = [
        {'role': 'system', 'content': system_prompt},
        {'role': 'user', 'content': user_prompt},
    ]

    try:
        response = client.chat.completions.create(
            model=MODEL,
            messages=messages,
            temperature=TEMPERATURE,
            max_tokens=max_tokens,
            timeout=timeout_seconds,
            response_format={'type': 'json_object'},
        )
    except BadRequestError:
        # This model/endpoint combination rejected the response_format param -
        # retry once without it, relying on prompt instructions + defensive
        # parsing instead. Any other exception (timeout, connection error,
        # 5xx) is not retried, so worst-case latency stays bounded.
        response = client.chat.completions.create(
            model=MODEL,
            messages=messages,
            temperature=TEMPERATURE,
            max_tokens=max_tokens,
            timeout=timeout_seconds,
        )

    choice = response.choices[0] if response.choices else None
    text = choice.message.content if choice and choice.message else None
    if not text:
        raise AIAnalysisError('Empty response from AI model')

    return _parse_json_response(text)


def _chat_json_with_retry(client, system_prompt, user_prompt, timeout_seconds, max_tokens, label):
    """Wraps _chat_json with one application-level retry, logged either way.

    The OpenAI client's own max_retries=1 (see _get_client) only covers
    network-level failures (timeouts, connection errors, 5xx) within a
    single .create() call - it can't help when the call succeeds but
    returns something our own parsing/validation rejects, and more
    importantly it doesn't stop a single transient upstream error (a plain
    500 from OpenAI, observed in production logs) from taking down this
    entire call and forcing the whole audit to fall back to the much
    weaker rule-based engine. One retry here, on any of the three calls
    (plan/execute/case-study), is what actually prevents that cliff."""
    last_error = None
    for attempt in (1, 2):
        try:
            return _chat_json(client, system_prompt, user_prompt, timeout_seconds, max_tokens)
        except Exception as exc:  # noqa: BLE001 - retried once, then re-raised for the caller to handle
            last_error = exc
            print(f'{label} call failed (attempt {attempt}/2): {exc}', file=sys.stderr)

    raise AIAnalysisError(f'{label} call failed twice: {last_error}') from last_error


def _generate_plan(client, crawl_summary, lead_context):
    user_prompt = (
        'Business context:\n' + json.dumps(lead_context or {}, indent=2) +
        '\n\nCrawled data - every page the crawler reached on this site:\n' +
        json.dumps(crawl_summary, indent=2)
    )

    plan = _chat_json_with_retry(client, PLAN_SYSTEM_PROMPT, user_prompt, PLAN_TIMEOUT_SECONDS, PLAN_MAX_TOKENS, 'plan')

    if not isinstance(plan, dict) or not plan.get('priorities'):
        raise AIAnalysisError('AI plan response missing required "priorities"')

    return plan


def _fetch_case_study_library(case_studies):
    """Gathers the text of Digifyce's own case study documents (uploaded via
    app/admin/case_studies.php, listed in the job payload by the PHP site,
    downloaded by case_study_library.py), for the dedicated
    case-study-matching call to reference. A document that can't be
    downloaded or parsed is just left out - this is supplementary context,
    not something the audit depends on."""
    library = []
    total_chars = 0
    for document in case_study_library.iter_documents((case_studies or [])[:MAX_CASE_STUDIES_IN_PROMPT]):
        content = document['content']
        if total_chars + len(content) > MAX_TOTAL_CASE_STUDY_CHARS:
            remaining = MAX_TOTAL_CASE_STUDY_CHARS - total_chars
            if remaining < 500:  # not enough room left for a usable excerpt
                break
            content = content[:remaining]
        total_chars += len(content)
        library.append({
            'title': document['title'],
            'description': document['description'],
            'content': content,
        })
    return library


def _execute_plan(client, plan, crawl_summary):
    user_prompt = (
        'Audit plan to follow:\n' + json.dumps(plan, indent=2) +
        '\n\nCrawled data - every page the crawler reached on this site:\n' +
        json.dumps(crawl_summary, indent=2)
    )

    return _chat_json_with_retry(client, EXECUTE_SYSTEM_PROMPT, user_prompt, EXECUTE_TIMEOUT_SECONDS, EXECUTE_MAX_TOKENS, 'execute')


def _generate_recommended_implementations(client, plan, execute_result, lead_context, case_study_library):
    """Dedicated 3rd call, kept separate from _execute_plan so this task gets
    the model's full attention instead of competing for it as the last, most
    detail-demanding field in one already-huge combined response - splitting
    it out this way is what fixed matches coming back empty far more often
    than the library's actual content warranted.

    business_summary deliberately covers more than just the website findings
    (business type/industry, brand brief, current ad situation, ad strategy
    ideas already generated) - the library documents are often full brand
    proposals covering social, ads, branding, content, etc., not just
    website fixes, so the model needs the whole-business picture, not just
    a list of site bugs, to recommend across that full range."""
    if not case_study_library:
        return []

    ad_strategy = execute_result.get('ad_strategy') or {}
    business_summary = {
        'business_type': plan.get('business_type'),
        'industry': (lead_context or {}).get('industry'),
        'brand_brief': execute_result.get('brand_brief'),
        'executive_summary': execute_result.get('executive_summary'),
        'current_ad_situation': lead_context or {},
        'targeting_ideas_already_identified': ad_strategy.get('targeting_ideas'),
        'website_findings': [
            {'category': f.get('category'), 'severity': f.get('severity'), 'title': f.get('title')}
            for f in (execute_result.get('findings') or [])
        ],
    }

    user_prompt = (
        'Business just audited (website findings are only ONE input signal here, not the boundary '
        'of what to recommend):\n' + json.dumps(business_summary, indent=2) +
        "\n\nDigifyce's own document library - full past proposals/case studies, study them in full "
        "(build up to 3-5 genuine, proof-backed implementation recommendations for THIS business "
        "covering its full growth, not just the website, most important first, or an empty array if "
        "the library genuinely gives no backing):\n" + json.dumps(case_study_library, indent=2)
    )

    # This section is now the centerpiece of the lead-facing report (it
    # replaced the old Ad Strategy tab), so - unlike a genuinely optional
    # nice-to-have - a single transient hiccup shouldn't silently blank it
    # out. _chat_json_with_retry covers the network/upstream failure case;
    # validation failures on either attempt propagate up to analyze()'s own
    # last-resort catch, same as a plan/execute failure would.
    result = _chat_json_with_retry(client, CASE_STUDY_SYSTEM_PROMPT, user_prompt, CASE_STUDY_TIMEOUT_SECONDS, CASE_STUDY_MAX_TOKENS, 'recommended-implementations')
    _validate_recommended_implementations(result)
    return result.get('recommended_implementations') or []


def _validate_execute_result(data):
    if not isinstance(data, dict):
        raise AIAnalysisError('AI execute response was not a JSON object')

    required_top_level = ('score', 'brand_brief', 'executive_summary', 'findings', 'priority_actions', 'ad_strategy')
    if any(not data.get(k) for k in required_top_level):
        raise AIAnalysisError('AI execute response missing required fields')

    try:
        int(data['score'])
    except (TypeError, ValueError) as exc:
        raise AIAnalysisError('AI execute response "score" is not a number') from exc

    for finding in data['findings']:
        if not isinstance(finding, dict):
            raise AIAnalysisError('AI finding is not a JSON object')
        if not all(finding.get(k) for k in ('category', 'severity', 'title', 'description')):
            raise AIAnalysisError('AI finding missing a required field')
        if finding['severity'] not in ALLOWED_SEVERITIES:
            raise AIAnalysisError(f'AI finding has invalid severity: {finding["severity"]!r}')

    if not isinstance(data['priority_actions'], list):
        raise AIAnalysisError('AI "priority_actions" is not a list')
    for action in data['priority_actions']:
        if not isinstance(action, dict) or not all(action.get(k) for k in ('action', 'why', 'effort')):
            raise AIAnalysisError('AI priority action missing a required field')
        if action['effort'] not in ALLOWED_EFFORTS:
            raise AIAnalysisError(f'AI priority action has invalid effort: {action["effort"]!r}')

    ad_strategy = data['ad_strategy']
    if not isinstance(ad_strategy, dict) or 'should_invest_in_ads' not in ad_strategy:
        raise AIAnalysisError('AI "ad_strategy" missing required fields')
    if not ad_strategy.get('targeting_ideas') or not ad_strategy.get('growth_potential'):
        raise AIAnalysisError('AI "ad_strategy" missing targeting_ideas/growth_potential')

    for channel in ad_strategy.get('recommended_channels') or []:
        if not isinstance(channel, dict) or not all(channel.get(k) for k in ('channel', 'reason', 'priority')):
            raise AIAnalysisError('AI ad_strategy channel missing a required field')
        if channel['priority'] not in ALLOWED_AD_PRIORITIES:
            raise AIAnalysisError(f'AI ad_strategy channel has invalid priority: {channel["priority"]!r}')

    if not ad_strategy.get('target_pages'):
        raise AIAnalysisError('AI "ad_strategy" missing target_pages')
    for tp in ad_strategy['target_pages']:
        if not isinstance(tp, dict) or not tp.get('page') or not tp.get('why'):
            raise AIAnalysisError('AI ad_strategy target_page missing a required field')


def _validate_recommended_implementations(data):
    if not isinstance(data, dict):
        raise AIAnalysisError('AI case study response was not a JSON object')

    # An empty list is the correct, expected answer for plenty of audits -
    # not every business has genuine library backing for a recommendation.
    # "metrics" is separately allowed to be empty within a present proof:
    # many uploaded documents are proposals/pitches with no reported
    # outcome, and the model is instructed to say so honestly (an empty
    # metrics array) rather than invent a plausible-sounding number.
    implementations = data.get('recommended_implementations')
    if implementations is None:
        data['recommended_implementations'] = []
        return
    if not isinstance(implementations, list):
        raise AIAnalysisError('AI "recommended_implementations" is not a list')
    for item in implementations:
        if not isinstance(item, dict) or not item.get('title') or not item.get('description'):
            raise AIAnalysisError('AI recommended implementation missing title/description')
        proof = item.get('proof')
        if not isinstance(proof, dict) or not all(
            proof.get(k) for k in ('case_study_title', 'industry', 'summary')
        ):
            raise AIAnalysisError('AI recommended implementation missing a required proof field')
        metrics = proof.get('metrics')
        if metrics is None:
            proof['metrics'] = []
        elif not isinstance(metrics, list):
            raise AIAnalysisError('AI recommended implementation proof "metrics" is not a list')
        else:
            for metric in metrics:
                if not isinstance(metric, dict) or not metric.get('label') or not metric.get('value'):
                    raise AIAnalysisError('AI recommended implementation proof metric missing label/value')


def analyze(pages, robots_txt_found, sitemap_found, layout_data, lead_context=None, case_studies=None):
    """Returns {'score', 'findings', 'brand_brief', 'executive_summary',
    'priority_actions', 'ad_strategy', 'case_study_matches', 'plan',
    'model_used'} on success - 'case_study_matches' holds proof-backed
    implementation recommendations for this business (see
    _generate_recommended_implementations), not case-study summaries.
    case_studies is the job payload's case study list (see
    case_study_library.py).
    Raises AIAnalysisError (or lets an unexpected exception propagate) on
    any failure - the caller is responsible for
    catching and falling back to the rule-based engine."""
    client = _get_client()

    crawl_summary = _build_crawl_summary(pages, robots_txt_found, sitemap_found, layout_data)

    plan = _generate_plan(client, crawl_summary, lead_context)
    result = _execute_plan(client, plan, crawl_summary)
    _validate_execute_result(result)

    # _generate_recommended_implementations already retries once and logs
    # its own failures - this outer catch is only the last-resort safety net
    # (e.g. an unexpected error building the library) so one bad audit never
    # throws away an otherwise-successful score/findings result. It should
    # rarely fire; when it does, the stderr log above already explains why.
    try:
        library = _fetch_case_study_library(case_studies)
        recommended_implementations = _generate_recommended_implementations(client, plan, result, lead_context, library)
    except Exception as exc:  # noqa: BLE001
        print(f'giving up on recommended implementations for this audit: {exc}', file=sys.stderr)
        recommended_implementations = []

    return {
        'score': max(0, min(100, int(result['score']))),
        'findings': result['findings'],
        'brand_brief': result['brand_brief'],
        'executive_summary': result['executive_summary'],
        'priority_actions': result['priority_actions'],
        'ad_strategy': result['ad_strategy'],
        'case_study_matches': recommended_implementations,
        'plan': plan,
        'model_used': MODEL,
    }
