"""Scrapy spider that crawls a lead's submitted website (same host only,
robots.txt-obeying, time/page/size-boxed) and collects per-page SEO/
technical/content signals used by audit_rules.py to score the site."""

from urllib.parse import urlparse, urlunparse

import scrapy


def _normalize_url(url):
    """Strips the query string (and any fragment) so URL parameter variants
    of the same logical page - most commonly Shopify's per-variant
    ?variant=<id> on product pages - don't each count as a distinct page
    against the crawl budget. Without this, a handful of products with many
    color/size variants can burn nearly the entire page budget on nine
    near-identical copies of themselves before the crawler ever reaches
    other sections of the site (e.g. a men's product's variants, at the
    expense of ever reaching the women's catalog)."""
    parsed = urlparse(url)
    return urlunparse((parsed.scheme, parsed.netloc, parsed.path, '', '', ''))


class AuditSpider(scrapy.Spider):
    name = 'audit_spider'

    custom_settings = {
        'ROBOTSTXT_OBEY': True,
        'CLOSESPIDER_TIMEOUT': 240,
        'CLOSESPIDER_PAGECOUNT': 60,
        'DOWNLOAD_TIMEOUT': 15,
        'DOWNLOAD_MAXSIZE': 5 * 1024 * 1024,
        'CONCURRENT_REQUESTS': 4,
        'DOWNLOAD_DELAY': 0.5,
        'REDIRECT_MAX_TIMES': 5,
        'RETRY_ENABLED': False,
        'HTTPERROR_ALLOW_ALL': True,
        'LOG_LEVEL': 'WARNING',
        'USER_AGENT': 'DigifyceAuditBot/1.0 (+https://digifyce.com; automated website audit)',
        'DOWNLOADER_MIDDLEWARES': {
            'middlewares.SsrfProtectionMiddleware': 100,
        },
    }

    def __init__(self, seed_url, results, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.start_urls = [seed_url]
        self.allowed_domains = [urlparse(seed_url).hostname]
        self.results = results
        self.seen_normalized = {_normalize_url(seed_url)}

    def parse(self, response):
        if response.status >= 400:
            self.results.append({
                'url': response.url,
                'status_code': response.status,
                'error': True,
            })
            return

        content_type = response.headers.get('Content-Type', b'').decode('utf-8', 'ignore')
        if 'text/html' not in content_type:
            return

        latency = response.meta.get('download_latency', 0) or 0

        title = (response.css('title::text').get() or '').strip()
        meta_description = (response.css('meta[name="description"]::attr(content)').get() or '').strip()
        og_description = (response.css('meta[property="og:description"]::attr(content)').get() or '').strip()
        has_viewport = bool(response.css('meta[name="viewport"]').get())
        canonical_url = response.css('link[rel="canonical"]::attr(href)').get()
        has_og_tags = bool(response.css('meta[property^="og:"]').get())

        h1_list = [
            (h.xpath('string(.)').get() or '').strip()
            for h in response.css('h1')
        ]
        h1_list = [h for h in h1_list if h]

        images = response.css('img')
        images_total = len(images)
        images_missing_alt = 0
        for img in images:
            alt = img.attrib.get('alt')
            if not alt or not alt.strip():
                images_missing_alt += 1

        body_text_nodes = response.xpath(
            '//body//text()[not(ancestor::script) and not(ancestor::style)]'
        ).getall()
        body_text = ' '.join(' '.join(body_text_nodes).split())
        word_count = len(body_text.split())

        # Real prose lives in <p> tags, not in nav/header/footer/button/form
        # chrome - used for the brand brief so it reads as actual sentences
        # instead of scraped navigation/price/menu fragments.
        content_paragraphs = []
        seen = set()
        for p in response.xpath(
            '//p[not(ancestor::nav) and not(ancestor::header) and not(ancestor::footer) '
            'and not(ancestor::button) and not(ancestor::form) and not(ancestor::aside)]'
        ):
            text = ' '.join((p.xpath('string(.)').get() or '').split())
            if len(text.split()) >= 8 and text not in seen:
                seen.add(text)
                content_paragraphs.append(text)
            if len(content_paragraphs) >= 5:
                break

        page_host = urlparse(response.url).hostname
        internal_links = 0
        external_links = 0
        to_follow = []
        for href in response.css('a::attr(href)').getall():
            if not href or href.startswith('#') or href.lower().startswith(('mailto:', 'tel:', 'javascript:')):
                continue
            absolute = response.urljoin(href)
            link_host = urlparse(absolute).hostname
            if link_host == page_host:
                internal_links += 1
                normalized = _normalize_url(absolute)
                if normalized not in self.seen_normalized:
                    self.seen_normalized.add(normalized)
                    to_follow.append(absolute)
            else:
                external_links += 1

        self.results.append({
            'url': response.url,
            'status_code': response.status,
            'error': False,
            'response_time_ms': round(latency * 1000),
            'title': title,
            'meta_description': meta_description,
            'og_description': og_description,
            'has_viewport_meta': has_viewport,
            'canonical_url': canonical_url,
            'has_og_tags': has_og_tags,
            'h1_list': h1_list,
            'images_total': images_total,
            'images_missing_alt': images_missing_alt,
            'word_count': word_count,
            'content_paragraphs': content_paragraphs,
            'internal_links_count': internal_links,
            'external_links_count': external_links,
            'is_https': response.url.startswith('https://'),
        })

        for link in to_follow:
            yield scrapy.Request(link, callback=self.parse)
