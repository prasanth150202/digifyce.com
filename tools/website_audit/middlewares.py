"""Scrapy downloader middleware that re-validates the SSRF safety of every
single outgoing request (not just the seed URL). This closes the gap where
a lead's own subdomain could be pointed at an internal IP mid-crawl -
allowed_domains alone would not catch that, since it only checks the
hostname string, not what it currently resolves to."""

from scrapy.exceptions import IgnoreRequest

from ssrf_guard import is_url_safe


class SsrfProtectionMiddleware:
    def process_request(self, request, spider):
        safe, reason = is_url_safe(request.url)
        if not safe:
            spider.logger.warning('Blocked unsafe request %s: %s', request.url, reason)
            raise IgnoreRequest(f'SSRF guard blocked {request.url}: {reason}')
        return None
