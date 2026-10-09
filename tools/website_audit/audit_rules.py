"""Rule-based scoring engine: turns aggregated crawl data into a 0-100
score plus a list of human-readable findings. Deterministic checks only -
this is the fallback audit_runner.py uses when ai_analyzer.py's AI-driven
analysis is unavailable (no API key, network error, invalid response),
so an audit always reaches a terminal state."""

SEVERITY_DEDUCTIONS = {
    'critical': 20,
    'high': 12,
    'medium': 6,
    'low': 3,
}


def _finding(category, severity, title, description):
    return {
        'category': category,
        'severity': severity,
        'title': title,
        'description': description,
    }


def evaluate(pages, robots_txt_found, sitemap_found, layout_data=None):
    """pages: list of per-page dicts from AuditSpider (may include error pages).
    layout_data: optional dict from screenshot_worker.py's homepage render
    (nav/footer/CTA/font/contrast/tap-target/mobile-overflow signals) - None
    when the screenshot step was skipped or failed; those checks are simply
    omitted rather than treated as failures.
    Returns {score: int|None, findings: [...], terminal_message: str|None}.
    score/terminal_message are mutually exclusive: a terminal_message means
    the crawl produced no usable data, so no score is computed.
    """
    good_pages = [p for p in pages if not p.get('error')]
    error_pages = [p for p in pages if p.get('error')]

    if not good_pages:
        return {
            'score': None,
            'findings': [],
            'terminal_message': (
                "We couldn't retrieve any pages from this site automatically "
                "(it may block automated tools, or was unreachable at the time "
                "of the audit). Our team will take a manual look instead."
            ),
        }

    findings = []
    homepage = good_pages[0]

    # --- SEO ---
    if not homepage.get('title'):
        findings.append(_finding('SEO', 'critical', 'Missing homepage title',
            'Add a unique, descriptive <title> tag to your homepage.'))
    elif any(len(p.get('title') or '') and (len(p['title']) < 30 or len(p['title']) > 60) for p in good_pages):
        findings.append(_finding('SEO', 'medium', 'Title length not optimal',
            'Keep page titles between 30-60 characters so they are not cut off in search results.'))

    if any(not p.get('meta_description') for p in good_pages):
        findings.append(_finding('SEO', 'high', 'Missing meta description',
            'Add a 120-160 character meta description to every page to improve click-through rate from search results.'))
    elif any(len(p.get('meta_description') or '') < 70 or len(p.get('meta_description') or '') > 160 for p in good_pages):
        findings.append(_finding('SEO', 'low', 'Meta description length not optimal',
            'Adjust meta description length (aim for 120-160 characters) for the best search snippet display.'))

    if any(not p.get('canonical_url') for p in good_pages):
        findings.append(_finding('SEO', 'low', 'Missing canonical tag',
            'Add a rel="canonical" tag to each page to prevent duplicate-content confusion.'))

    titles = [p.get('title') for p in good_pages if p.get('title')]
    if len(titles) != len(set(titles)):
        findings.append(_finding('SEO', 'medium', 'Duplicate page titles',
            'Give each page a unique, page-specific title instead of repeating the same one.'))

    # --- Technical ---
    if not homepage.get('is_https'):
        findings.append(_finding('Technical', 'critical', 'No HTTPS',
            'Install an SSL certificate and force HTTPS site-wide to protect visitors and improve search ranking.'))

    if error_pages:
        findings.append(_finding('Technical', 'high', 'Broken pages found',
            f'{len(error_pages)} page(s) returned an error during the crawl. Fix or redirect broken links.'))

    response_times = [p['response_time_ms'] for p in good_pages if p.get('response_time_ms') is not None]
    avg_response_time = sum(response_times) / len(response_times) if response_times else 0
    if avg_response_time > 2500:
        findings.append(_finding('Technical', 'high', 'Very slow page load',
            'Average response time is critically slow. Consider a CDN, caching, and image optimization.'))
    elif avg_response_time > 1000:
        findings.append(_finding('Technical', 'medium', 'Slow page load',
            'Average response time is slower than ideal. Improve hosting and caching for a faster experience.'))

    if not robots_txt_found:
        findings.append(_finding('Technical', 'low', 'Missing robots.txt',
            'Add a robots.txt file to guide search engine crawlers.'))

    if not sitemap_found:
        findings.append(_finding('Technical', 'low', 'Missing sitemap.xml',
            'Add an XML sitemap and submit it to Google Search Console.'))

    # --- Mobile ---
    if any(not p.get('has_viewport_meta') for p in good_pages):
        findings.append(_finding('Mobile', 'high', 'Missing mobile viewport tag',
            'Add a responsive viewport meta tag so the site displays correctly on mobile devices.'))

    # --- Content ---
    if any((p.get('word_count') or 0) < 300 for p in good_pages):
        findings.append(_finding('Content', 'medium', 'Thin content',
            'Expand thin pages with more useful, original content (aim for 300+ words).'))

    if any((p.get('images_missing_alt') or 0) > 0 for p in good_pages):
        findings.append(_finding('Content', 'medium', 'Images missing alt text',
            'Add descriptive alt text to images for accessibility and image SEO.'))

    if any(len(p.get('h1_list') or []) == 0 for p in good_pages):
        findings.append(_finding('Content', 'medium', 'Missing H1 heading',
            'Add exactly one clear <h1> heading per page describing its main topic.'))

    if any(len(p.get('h1_list') or []) > 1 for p in good_pages):
        findings.append(_finding('Content', 'low', 'Multiple H1 headings',
            'Use a single <h1> per page; demote extra headings to <h2>/<h3>.'))

    # --- Social ---
    if not homepage.get('has_og_tags'):
        findings.append(_finding('Social', 'low', 'Missing Open Graph tags',
            'Add og:title, og:description, and og:image tags for better social share previews.'))

    # --- Layout & Design (from the rendered homepage screenshot, when available) ---
    if layout_data:
        if not layout_data.get('has_nav'):
            findings.append(_finding('Layout & Design', 'medium', 'No navigation menu detected',
                'Add a clear navigation menu so visitors can easily find your key pages.'))

        if not layout_data.get('has_footer'):
            findings.append(_finding('Layout & Design', 'low', 'No footer detected',
                'Add a footer with contact details, key links, and copyright information.'))

        if not layout_data.get('has_cta_above_fold'):
            findings.append(_finding('Layout & Design', 'high', 'No clear call-to-action above the fold',
                "Place a prominent call-to-action (e.g. \"Get Started\", \"Contact Us\") where visitors see it without scrolling."))

        if (layout_data.get('font_family_count') or 0) > 3:
            findings.append(_finding('Layout & Design', 'medium', 'Too many fonts in use',
                'Limit your site to 2-3 font families for a more consistent, professional look.'))

        contrast_ratio = layout_data.get('contrast_ratio')
        if contrast_ratio is not None and contrast_ratio < 4.5:
            findings.append(_finding('Layout & Design', 'high', 'Low text contrast',
                'Increase the contrast between text and background colors (aim for at least 4.5:1) so content is easy to read.'))

        if (layout_data.get('small_tap_target_ratio') or 0) > 0.3:
            findings.append(_finding('Layout & Design', 'medium', 'Small tap targets on mobile',
                'Increase button and link sizes to at least 44x44 pixels so they are easy to tap on mobile devices.'))

        if layout_data.get('mobile_overflow'):
            findings.append(_finding('Layout & Design', 'high', 'Content overflows on mobile',
                'Some content is wider than the mobile screen, causing horizontal scrolling. Review your responsive layout.'))

    score = 100
    for f in findings:
        score -= SEVERITY_DEDUCTIONS.get(f['severity'], 0)
    score = max(0, min(100, score))

    return {'score': score, 'findings': findings, 'terminal_message': None}
