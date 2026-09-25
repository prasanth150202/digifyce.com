"""Standalone script: python.exe screenshot_worker.py <url> <screenshot_path> <layout_json_path>

Captures a single viewport screenshot of the homepage, and while the same
page is already rendered, also extracts a handful of layout/design signals
(nav/footer presence, above-the-fold CTA, font diversity, text contrast,
mobile tap-target sizes, mobile overflow) via page.evaluate() - no extra
network requests, just DOM/CSS inspection of the page we already loaded.

Runs as a headless, SSRF-hardened Chromium instance, invoked as a separate
OS subprocess (not in-process) by audit_runner.py so a genuine browser hang
can be time-boxed and killed without threatening the parent process.

The URL passed in has already been SSRF-validated by audit_runner.py before
the crawl started - it is never new/untrusted input. This script re-validates
anyway (defense in depth) and, more importantly, guards every *subsequent*
request the rendered page's JS makes (images, XHR, iframes, etc.), since a
real browser executing JS can trigger requests our own crawl never would.
"""

import json
import os
import sys
import tempfile
from urllib.parse import urlparse

from playwright.sync_api import sync_playwright

from ssrf_guard import is_url_safe, resolve_all_ips

NAV_TIMEOUT_MS = 15000
SETTLE_MS = 1500
SCREENSHOT_TIMEOUT_MS = 5000
VIEWPORT = {'width': 1366, 'height': 800}
MOBILE_VIEWPORT = {'width': 375, 'height': 667}

LAYOUT_METRICS_JS = """
() => {
    const doc = document;
    const win = window;

    const fontFamilies = new Set();
    let totalTextEls = 0;
    let smallFontCount = 0;
    doc.querySelectorAll('body *').forEach((el) => {
        if (el.children.length === 0 && el.textContent && el.textContent.trim().length > 0) {
            const style = win.getComputedStyle(el);
            fontFamilies.add(style.fontFamily.split(',')[0].trim().replace(/["']/g, ''));
            const size = parseFloat(style.fontSize);
            if (!isNaN(size)) {
                totalTextEls++;
                if (size < 14) smallFontCount++;
            }
        }
    });

    const hasNav = !!doc.querySelector('nav, [role="navigation"], header nav');
    const hasFooter = !!doc.querySelector('footer, [role="contentinfo"]');

    const ctaPattern = /contact|get started|sign up|buy now|learn more|book now|call us|subscribe|shop now|order now|request a|schedule|start free|try free|get a quote/i;
    let hasCtaAboveFold = false;
    doc.querySelectorAll('a, button').forEach((el) => {
        if (hasCtaAboveFold) return;
        const rect = el.getBoundingClientRect();
        const inViewport = rect.top >= 0 && rect.top < win.innerHeight && rect.width > 0 && rect.height > 0;
        if (inViewport && ctaPattern.test(el.textContent || '')) {
            hasCtaAboveFold = true;
        }
    });

    let totalTapTargets = 0;
    let smallTapTargets = 0;
    doc.querySelectorAll('a, button').forEach((el) => {
        const rect = el.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
            totalTapTargets++;
            if (rect.width < 44 || rect.height < 44) smallTapTargets++;
        }
    });

    function parseRgb(str) {
        const m = (str || '').match(/rgba?\\((\\d+),\\s*(\\d+),\\s*(\\d+)/);
        return m ? [parseInt(m[1], 10), parseInt(m[2], 10), parseInt(m[3], 10)] : null;
    }
    function relLuminance([r, g, b]) {
        const a = [r, g, b].map((v) => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
    }
    let contrastRatio = null;
    const sampleText = doc.querySelector('p, li, span');
    if (sampleText) {
        const textColor = parseRgb(win.getComputedStyle(sampleText).color);
        let bgEl = sampleText;
        let bgColor = null;
        while (bgEl && !bgColor) {
            const bg = win.getComputedStyle(bgEl).backgroundColor;
            const parsed = parseRgb(bg);
            if (parsed && bg !== 'rgba(0, 0, 0, 0)') bgColor = parsed;
            bgEl = bgEl.parentElement;
        }
        if (!bgColor) bgColor = [255, 255, 255];
        if (textColor) {
            const l1 = relLuminance(textColor) + 0.05;
            const l2 = relLuminance(bgColor) + 0.05;
            contrastRatio = Math.round((Math.max(l1, l2) / Math.min(l1, l2)) * 100) / 100;
        }
    }

    return {
        font_family_count: fontFamilies.size,
        small_font_ratio: totalTextEls ? Math.round((smallFontCount / totalTextEls) * 100) / 100 : 0,
        has_nav: hasNav,
        has_footer: hasFooter,
        has_cta_above_fold: hasCtaAboveFold,
        small_tap_target_ratio: totalTapTargets ? Math.round((smallTapTargets / totalTapTargets) * 100) / 100 : 0,
        contrast_ratio: contrastRatio,
    };
}
"""

MOBILE_OVERFLOW_JS = '() => document.documentElement.scrollWidth > window.innerWidth + 5'

LAUNCH_ARGS = [
    '--disable-background-networking',
    '--disable-domain-reliability',
    '--disable-component-update',
    '--disable-features=OptimizationHints,Prerender2,BackForwardCache',
    '--mute-audio',
    '--disable-notifications',
    '--no-first-run',
]

BLOCKED_RESOURCE_TYPES = {'font', 'media'}


def make_route_handler():
    def handler(route, request):
        if request.resource_type in BLOCKED_RESOURCE_TYPES:
            route.abort()
            return
        safe, _reason = is_url_safe(request.url)
        if not safe:
            route.abort()
            return
        route.continue_()
    return handler


def capture(url, screenshot_path, layout_json_path):
    hostname = urlparse(url).hostname
    ips = resolve_all_ips(hostname) if hostname else []
    launch_args = list(LAUNCH_ARGS)
    if ips:
        # Pin Chromium's own DNS resolution to the IP we already validated,
        # closing the gap between "we checked this hostname" and "the
        # browser fetches it" (a stronger guarantee than a bare hostname check).
        launch_args.append(f'--host-resolver-rules=MAP {hostname} {ips[0]}')

    tmp_fd, tmp_screenshot = tempfile.mkstemp(suffix='.png', dir=os.path.dirname(screenshot_path) or '.')
    os.close(tmp_fd)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, args=launch_args)
        try:
            context = browser.new_context(
                service_workers='block',
                accept_downloads=False,
                viewport=VIEWPORT,
                device_scale_factor=1,
            )
            context.set_default_navigation_timeout(NAV_TIMEOUT_MS)
            context.route('**/*', make_route_handler())

            page = context.new_page()
            # Close any popup opened by the page's own JS (window.open(), target=_blank)
            # - this must be a 'popup' listener, not context.on('page', ...), which
            # would also fire (and race) for the new_page() call above.
            page.on('popup', lambda popup: popup.close())
            page.on('dialog', lambda dialog: dialog.dismiss())

            page.goto(url, wait_until='domcontentloaded', timeout=NAV_TIMEOUT_MS)
            page.wait_for_timeout(SETTLE_MS)
            page.screenshot(path=tmp_screenshot, timeout=SCREENSHOT_TIMEOUT_MS, full_page=False)

            # Layout/design signals are best-effort: a failure here must never
            # cost us the screenshot we already captured above.
            try:
                layout_data = page.evaluate(LAYOUT_METRICS_JS)
                page.set_viewport_size(MOBILE_VIEWPORT)
                page.wait_for_timeout(300)
                layout_data['mobile_overflow'] = bool(page.evaluate(MOBILE_OVERFLOW_JS))

                tmp_fd2, tmp_layout = tempfile.mkstemp(suffix='.json', dir=os.path.dirname(layout_json_path) or '.')
                os.close(tmp_fd2)
                with open(tmp_layout, 'w', encoding='utf-8') as f:
                    json.dump(layout_data, f)
                os.replace(tmp_layout, layout_json_path)
            except Exception as exc:  # noqa: BLE001 - layout metrics are optional
                print(f'layout analysis failed (screenshot still saved): {exc}', file=sys.stderr)
        finally:
            browser.close()

    os.replace(tmp_screenshot, screenshot_path)


if __name__ == '__main__':
    if len(sys.argv) < 4:
        sys.exit('usage: screenshot_worker.py <url> <screenshot_path> <layout_json_path>')

    target_url = sys.argv[1]
    target_screenshot = sys.argv[2]
    target_layout_json = sys.argv[3]

    safe, reason = is_url_safe(target_url)
    if not safe:
        sys.exit(f'refusing to capture unsafe URL: {reason}')

    try:
        capture(target_url, target_screenshot, target_layout_json)
    except Exception as exc:  # noqa: BLE001 - any failure just means "no screenshot"
        print(f'screenshot capture failed for {target_url}: {exc}', file=sys.stderr)
        sys.exit(1)
