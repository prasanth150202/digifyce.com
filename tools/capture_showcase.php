<?php
/**
 * Takes homepage screenshots for the "Shopify Stores We've Built" section on
 * /shopify-development, using the Chrome or Edge installed on this machine.
 *
 * Shopify stores refuse to load inside iframes (X-Frame-Options: DENY), so the
 * page shows a scrolling screenshot of each store instead of a live embed.
 *
 * Each store is opened in a normal 1440x900 desktop window, scrolled through so
 * lazy-loaded images appear, cleared of pop-ups / cookie banners / chat widgets,
 * then captured one screen at a time (up to MAX_HEIGHT), stitched into a single
 * image and saved as a WebP.
 *
 * Run from the project root:
 *   php tools/capture_showcase.php              capture stores that have no screenshot yet
 *   php tools/capture_showcase.php --force      re-capture every store
 *   php tools/capture_showcase.php brand-name   re-capture one store (its slug)
 *
 * Stores are listed in app/webContent/shopify-showcase.php. Set CHROME_PATH to
 * use a browser that isn't in a standard install location.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// GD crops and resizes the screenshot. XAMPP ships it but leaves it off for the
// CLI, so re-run once with it enabled rather than asking the user to edit php.ini.
if (!extension_loaded('gd')) {
    if (getenv('SD_SHOWCASE_GD_RETRY')) {
        fwrite(STDERR, "The GD extension could not be loaded. Enable extension=gd in php.ini and try again.\n");
        exit(1);
    }
    putenv('SD_SHOWCASE_GD_RETRY=1');
    passthru(escapeshellarg(PHP_BINARY) . ' -d extension=gd ' . implode(' ', array_map('escapeshellarg', $argv)), $code);
    exit($code);
}

const CAPTURE_WIDTH   = 1440;   // desktop layout width the store is rendered at
const VIEWPORT_HEIGHT = 900;    // window height, so "full screen" hero banners keep their real size
const MAX_HEIGHT      = 4200;   // tallest part of the page kept (about 4-5 screens)
const OUTPUT_WIDTH    = 960;    // saved image width; the preview frame is narrower
const WEBP_QUALITY    = 80;

// Runs inside the store page before the screenshot.
const PREPARE_PAGE_JS = <<<'JS'
(async () => {
    const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
    const vh = window.innerHeight;

    // Scroll down in steps so lazy-loaded images and sections render, then back up.
    const bottom = Math.min(document.documentElement.scrollHeight, __MAX_HEIGHT__);
    for (let y = 0; y < bottom; y += Math.round(vh * 0.75)) {
        window.scrollTo({ top: y, behavior: 'instant' });
        await sleep(300);
    }
    window.scrollTo({ top: 0, behavior: 'instant' });
    await sleep(800);

    // Every element on the page, including those inside app widgets' shadow DOM
    // (e.g. floating "Shop Best Sellers" buttons), which querySelectorAll skips.
    window.__sdAll = (root = document.body, out = []) => {
        for (const el of root.querySelectorAll('*')) {
            out.push(el);
            if (el.shadowRoot) window.__sdAll(el.shadowRoot, out);
        }
        return out;
    };
    // Hidden with opacity (not visibility, which a child can switch back on);
    // the element keeps its space so the page doesn't shift between screens.
    const conceal = (el) => el.style.setProperty('opacity', '0', 'important');

    // Hide pop-ups, cookie banners, chat widgets and dimmed backdrops. Re-run
    // before every screen, since some pop-ups only appear after a delay.
    const noise = /cookie|consent|gdpr|popup|pop-up|modal|newsletter|klaviyo|privy|justuno|omnisend|wisepops|optin|opt-in|overlay|backdrop|lightbox|geolocation|localization|country|age-?verif|spin|chat|whatsapp|tidio|gorgias|zendesk|intercom|inbox|toast|notification/i;
    window.__sdHideNoise = () => {
        const viewportArea = window.innerWidth * window.innerHeight;
        for (const el of window.__sdAll()) {
            const isModal = el.matches('dialog[open], [aria-modal="true"]');
            if (!isModal && getComputedStyle(el).position !== 'fixed') continue;
            const label = `${el.id} ${typeof el.className === 'string' ? el.className : ''}`;
            // Large fixed layers are overlays, except scroll-animation pins, which hold real content.
            const isPinned = !!el.closest('.pin-spacer');
            const rect = el.getBoundingClientRect();
            if (isModal || el.tagName === 'IFRAME' || noise.test(label) || (!isPinned && rect.width * rect.height > viewportArea * 0.25)) {
                el.style.setProperty('display', 'none', 'important');
            }
        }
        // Pop-ups often lock scrolling on the page behind them.
        for (const el of [document.documentElement, document.body]) {
            el.style.setProperty('overflow', 'visible', 'important');
        }
    };

    // Small floating widgets (back-to-top buttons, review tabs, sticky
    // add-to-cart bars) would repeat on every screen of the stitched image.
    window.__sdHideFloating = () => {
        for (const el of window.__sdAll()) {
            if (getComputedStyle(el).position !== 'fixed' || el.closest('.pin-spacer')) continue;
            const rect = el.getBoundingClientRect();
            if (rect.top > 4 && rect.height < 260) conceal(el);
        }
    };

    // Headers that stick to the top would repeat on every screen of the
    // stitched image, so they are hidden after the first. Some themes wrap the
    // header in a zero-height sticky element, so height 0 still counts.
    window.__sdHideHeaders = () => {
        for (const el of window.__sdAll()) {
            const position = getComputedStyle(el).position;
            if ((position !== 'fixed' && position !== 'sticky') || el.closest('.pin-spacer')) continue;
            const rect = el.getBoundingClientRect();
            if (rect.top <= 4 && rect.height < 260) conceal(el);
        }
    };

    window.__sdHideNoise();
    await sleep(400);
})()
JS;

// Scrolls to one screen of the page and returns where it actually landed.
const SHOW_SCREEN_JS = <<<'JS'
(async () => {
    window.scrollTo({ top: __Y__, behavior: 'instant' });
    await new Promise((resolve) => setTimeout(resolve, 700));
    window.__sdHideNoise();
    window.__sdHideFloating();
    if (__HIDE_HEADERS__) window.__sdHideHeaders();
    await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
    return Math.round(window.scrollY);
})()
JS;

// Scroll-animation pins (GSAP ScrollTrigger wraps each in a .pin-spacer): page
// position, total scroll length, and height of the section that stays on screen.
// With pinSpacing the spacer itself is stretched to the scroll length; without
// it, a parent "track" that holds only the spacer provides that length.
const PINS_JS = <<<'JS'
JSON.stringify(Array.from(document.querySelectorAll('.pin-spacer')).map((spacer) => {
    const pinned = spacer.firstElementChild ? spacer.firstElementChild.offsetHeight : 0;
    const track = spacer.parentElement;
    const region = spacer.offsetHeight > pinned + 50 ? spacer
        : (track && track.children.length === 1 ? track : spacer);
    return {
        top: Math.round(region.getBoundingClientRect().top + window.scrollY),
        height: region.offsetHeight,
        pinned,
    };
}).filter((pin) => pin.height > pin.pinned + 50).sort((a, b) => a.top - b.top))
JS;

$root = dirname(__DIR__);
require $root . '/app/helpers/shopify_showcase.php';

$args  = array_slice($argv, 1);
$force = in_array('--force', $args, true);
$only  = array_values(array_filter($args, fn($a) => $a !== '' && $a[0] !== '-'));

$items = shopify_showcase_items($root);
if (!$items) {
    echo "No stores listed yet. Add them to app/webContent/shopify-showcase.php first.\n";
    exit(0);
}

$browser = find_browser();
if ($browser === null) {
    fwrite(STDERR, "Chrome or Edge not found. Set CHROME_PATH to the browser executable.\n");
    exit(1);
}

$outDir = $root . '/' . SHOPIFY_SHOWCASE_DIR;
if (!is_dir($outDir)) mkdir($outDir, 0775, true);

$failed = 0;
foreach ($items as $item) {
    if ($only && !in_array($item['slug'], $only, true)) continue;

    if ($item['custom_image']) {
        echo "skip  {$item['name']}: uses its own image ({$item['image']})\n";
        continue;
    }
    $dest = $root . '/' . $item['image'];
    if (!$force && !$only && is_file($dest)) {
        echo "skip  {$item['name']}: screenshot exists (use --force to replace)\n";
        continue;
    }

    echo "shot  {$item['name']} ({$item['url']}) ... ";
    $error = capture($browser, $item['url'], $dest);
    if ($error !== null) {
        $failed++;
        echo "FAILED: $error\n";
        continue;
    }
    [$w, $h] = getimagesize($dest);
    printf("saved %s (%dx%d, %d KB)\n", $item['image'], $w, $h, filesize($dest) / 1024);
}

echo $failed
    ? "\n$failed store(s) failed. Check the URL opens in a normal browser, then run again.\n"
    : "\nDone. Open each screenshot and check it before publishing.\n";
exit($failed ? 1 : 0);


function find_browser(): ?string {
    $localAppData = getenv('LOCALAPPDATA');
    $candidates = [
        getenv('CHROME_PATH') ?: null,
        'C:\Program Files\Google\Chrome\Application\chrome.exe',
        'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
        $localAppData ? $localAppData . '\Google\Chrome\Application\chrome.exe' : null,
        'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
        'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/usr/bin/google-chrome',
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
    ];
    foreach ($candidates as $path) {
        if ($path && is_file($path)) return $path;
    }
    return null;
}

/** Screenshots $url into a WebP at $dest. Returns an error message, or null on success. */
function capture(string $browser, string $url, string $dest): ?string {
    $work = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sd-showcase-' . bin2hex(random_bytes(4));
    $profile = $work . DIRECTORY_SEPARATOR . 'profile';
    mkdir($profile, 0777, true);

    $null = DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
    $proc = proc_open([
        $browser,
        '--headless=new',
        '--disable-gpu',
        '--hide-scrollbars',
        '--mute-audio',
        '--no-first-run',
        '--no-default-browser-check',
        '--remote-debugging-port=0',
        '--user-data-dir=' . $profile,
        '--window-size=' . CAPTURE_WIDTH . ',' . VIEWPORT_HEIGHT,
        'about:blank',
    ], [0 => ['file', $null, 'r'], 1 => ['file', $null, 'w'], 2 => ['file', $null, 'w']], $pipes);
    if (!is_resource($proc)) return 'could not start the browser';

    $cdp = null;
    try {
        $cdp = new Cdp(page_socket_url($profile));
        return shoot($cdp, $url, $dest);
    } catch (Throwable $e) {
        return $e->getMessage();
    } finally {
        if ($cdp) $cdp->close();
        stop_process($proc);
        remove_dir($work);
    }
}

function shoot(Cdp $cdp, string $url, string $dest): ?string {
    // Headless Chrome names itself in its user agent, which some stores block.
    $ua = str_replace('HeadlessChrome', 'Chrome', (string) ($cdp->call('Browser.getVersion')['userAgent'] ?? ''));
    if ($ua !== '') $cdp->call('Emulation.setUserAgentOverride', ['userAgent' => $ua]);
    $cdp->call('Emulation.setDeviceMetricsOverride', [
        'width' => CAPTURE_WIDTH, 'height' => VIEWPORT_HEIGHT, 'deviceScaleFactor' => 1, 'mobile' => false,
    ]);
    $cdp->call('Page.enable');

    $nav = $cdp->call('Page.navigate', ['url' => $url], 45);
    if (!empty($nav['errorText'])) return "could not open the page ({$nav['errorText']})";
    $cdp->waitFor('Page.loadEventFired', 40);   // some stores never finish loading; carry on anyway
    usleep(2000000);

    $cdp->call('Runtime.evaluate', [
        'expression'   => str_replace('__MAX_HEIGHT__', (string) MAX_HEIGHT, PREPARE_PAGE_JS),
        'awaitPromise' => true,
    ], 90);
    $pageHeight = max(VIEWPORT_HEIGHT, (int) page_value($cdp, 'Math.max(document.documentElement.scrollHeight, document.body ? document.body.scrollHeight : 0)'));
    $pins = json_decode((string) page_value($cdp, PINS_JS), true) ?: [];

    // Capture one screen at a time, exactly as a visitor sees it while
    // scrolling, and stitch the screens together (a single full-page capture
    // leaves scroll-animated sections blank).
    $plan = capture_plan($pageHeight, $pins);
    $last = end($plan);
    $outHeight = min(MAX_HEIGHT, $last['out'] + $last['rows']);
    $page = imagecreatetruecolor(CAPTURE_WIDTH, max(VIEWPORT_HEIGHT, $outHeight));

    foreach ($plan as $i => $tile) {
        if ($tile['out'] >= $outHeight) break;
        $landed = (int) page_value($cdp, strtr(SHOW_SCREEN_JS, [
            '__Y__'            => (string) $tile['scroll'],
            '__HIDE_HEADERS__' => $i > 0 ? 'true' : 'false',
        ]), true);

        $shot = $cdp->call('Page.captureScreenshot', ['format' => 'png'], 60);
        $screen = @imagecreatefromstring(base64_decode((string) ($shot['data'] ?? '')));
        if (!$screen) return 'no screenshot produced';

        // Near the bottom the browser can't scroll as far as asked, so the
        // wanted rows start lower down the screen.
        $srcY = max(0, $tile['scroll'] - $landed);
        $rows = min($tile['rows'], imagesy($screen) - $srcY, $outHeight - $tile['out']);
        if ($rows > 0) imagecopy($page, $screen, 0, $tile['out'], 0, $srcY, CAPTURE_WIDTH, $rows);
    }
    return save_webp($page, $dest);
}

/**
 * Which screens to capture and where each goes in the final image.
 *
 * A pinned section stays fixed on screen while the visitor scrolls through a
 * long animation, and screens taken part-way through it show half-finished
 * frames. So each pinned section is captured once, at the moment it locks in
 * place, and the rest of its scroll distance is left out of the image.
 */
function capture_plan(int $pageHeight, array $pins): array {
    $plan = [];
    $skipped = 0;   // pinned scroll distance left out above the current point
    $from = 0;
    $addFlow = function (int $from, int $to) use (&$plan, &$skipped) {
        for ($s = $from; $s < $to; $s += VIEWPORT_HEIGHT) {
            $plan[] = ['scroll' => $s, 'out' => $s - $skipped, 'rows' => min(VIEWPORT_HEIGHT, $to - $s)];
        }
    };

    foreach ($pins as $pin) {
        if ($pin['top'] < $from) continue;   // nested inside a pin already handled
        $addFlow($from, $pin['top']);
        $shown = min($pin['pinned'], VIEWPORT_HEIGHT);
        $plan[] = ['scroll' => $pin['top'], 'out' => $pin['top'] - $skipped, 'rows' => $shown];
        $skipped += $pin['height'] - $shown;
        $from = $pin['top'] + $pin['height'];
    }
    $addFlow($from, $pageHeight);
    return $plan;
}

function page_value(Cdp $cdp, string $expression, bool $await = false) {
    return $cdp->call('Runtime.evaluate', [
        'expression'    => $expression,
        'awaitPromise'  => $await,
        'returnByValue' => true,
    ], 30)['result']['value'] ?? null;
}

/** Finds the DevTools WebSocket of the browser's tab once it has started. */
function page_socket_url(string $profile): string {
    $portFile = $profile . DIRECTORY_SEPARATOR . 'DevToolsActivePort';
    $deadline = microtime(true) + 20;
    while (microtime(true) < $deadline) {
        $port = is_file($portFile) ? (int) strtok((string) file_get_contents($portFile), "\n") : 0;
        if ($port) {
            $targets = json_decode(local_http_get($port, '/json/list'), true) ?: [];
            foreach ($targets as $target) {
                if (($target['type'] ?? '') === 'page' && !empty($target['webSocketDebuggerUrl'])) {
                    return $target['webSocketDebuggerUrl'];
                }
            }
        }
        usleep(200000);
    }
    throw new RuntimeException('the browser did not start');
}

/**
 * GET from the browser's DevTools HTTP server. Reads exactly Content-Length
 * bytes: the server keeps the connection open, so file_get_contents() would
 * sit waiting for it to close until the 60s socket timeout.
 */
function local_http_get(int $port, string $path): string {
    $sock = @stream_socket_client("tcp://127.0.0.1:$port", $errno, $errstr, 5);
    if (!$sock) return '';
    stream_set_timeout($sock, 5);
    fwrite($sock, "GET $path HTTP/1.1\r\nHost: 127.0.0.1:$port\r\n\r\n");
    $length = 0;
    while (($line = fgets($sock)) !== false && rtrim($line) !== '') {
        if (stripos($line, 'Content-Length:') === 0) $length = (int) trim(substr($line, 15));
    }
    $body = $length > 0 ? stream_get_contents($sock, $length) : '';
    fclose($sock);
    return (string) $body;
}

function stop_process($proc): void {
    $deadline = microtime(true) + 5;
    while (proc_get_status($proc)['running'] && microtime(true) < $deadline) usleep(100000);
    if (proc_get_status($proc)['running']) proc_terminate($proc);
    proc_close($proc);
}

/** Trims blank space below the page, scales to OUTPUT_WIDTH and writes a WebP. */
function save_webp($src, string $dest): ?string {
    $w = imagesx($src);
    $h = content_height($src, $w, imagesy($src));
    if ($h < 600) return 'page looks blank (it may block automated browsers)';

    $outH = (int) round($h * OUTPUT_WIDTH / $w);
    $dst = imagecreatetruecolor(OUTPUT_WIDTH, $outH);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, OUTPUT_WIDTH, $outH, $w, $h);
    return imagewebp($dst, $dest, WEBP_QUALITY) ? null : 'could not write ' . $dest;
}

/**
 * Walks up from the bottom until a row differs from the last row, so a page
 * that ends in flat background colour isn't padded with empty space.
 */
function content_height($im, int $w, int $h): int {
    $xs = range(0, $w - 1, max(1, intdiv($w, 32)));
    $ref = array_map(fn($x) => imagecolorat($im, $x, $h - 1), $xs);
    for ($y = $h - 2; $y > 0; $y--) {
        foreach ($xs as $i => $x) {
            if (!colors_close(imagecolorat($im, $x, $y), $ref[$i])) return min($h, $y + 24);
        }
    }
    return 0;
}

function colors_close(int $a, int $b): bool {
    foreach ([16, 8, 0] as $shift) {
        if (abs((($a >> $shift) & 255) - (($b >> $shift) & 255)) > 8) return false;
    }
    return true;
}

function remove_dir(string $dir): void {
    // The browser can hold files for a moment after it exits.
    for ($attempt = 0; $attempt < 10 && is_dir($dir); $attempt++) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }
        @rmdir($dir);
        if (is_dir($dir)) usleep(300000);
    }
}

/** Minimal Chrome DevTools Protocol client over a WebSocket (no extra extensions needed). */
final class Cdp {
    private $sock;
    private int $nextId = 1;
    private array $seenEvents = [];

    public function __construct(string $wsUrl) {
        $u = parse_url($wsUrl);
        $this->sock = @stream_socket_client("tcp://{$u['host']}:{$u['port']}", $errno, $errstr, 10);
        if (!$this->sock) throw new RuntimeException("could not connect to the browser ($errstr)");
        stream_set_timeout($this->sock, 60);

        $key = base64_encode(random_bytes(16));
        $this->write("GET {$u['path']} HTTP/1.1\r\nHost: {$u['host']}:{$u['port']}\r\n"
            . "Upgrade: websocket\r\nConnection: Upgrade\r\n"
            . "Sec-WebSocket-Key: $key\r\nSec-WebSocket-Version: 13\r\n\r\n");
        $status = fgets($this->sock);
        if (!$status || !str_contains($status, ' 101 ')) throw new RuntimeException('the browser refused the DevTools connection');
        while (($line = fgets($this->sock)) !== false && rtrim($line) !== '') {}
    }

    public function call(string $method, array $params = [], float $timeout = 30): array {
        $id = $this->nextId++;
        $this->sendFrame(json_encode(['id' => $id, 'method' => $method, 'params' => (object) $params], JSON_UNESCAPED_SLASHES));
        $deadline = microtime(true) + $timeout;
        while (($msg = $this->nextMessage($deadline)) !== null) {
            if (($msg['id'] ?? null) === $id) {
                if (isset($msg['error'])) throw new RuntimeException("$method failed: " . ($msg['error']['message'] ?? 'unknown error'));
                return $msg['result'] ?? [];
            }
            if (isset($msg['method'])) $this->seenEvents[$msg['method']] = true;
        }
        throw new RuntimeException("$method timed out");
    }

    /** Waits for an event (or returns true if it already arrived). False on timeout. */
    public function waitFor(string $event, float $timeout): bool {
        $deadline = microtime(true) + $timeout;
        while (empty($this->seenEvents[$event])) {
            $msg = $this->nextMessage($deadline);
            if ($msg === null) return false;
            if (isset($msg['method'])) $this->seenEvents[$msg['method']] = true;
        }
        return true;
    }

    public function close(): void {
        try { $this->call('Browser.close', [], 5); } catch (Throwable $e) {}
        if (is_resource($this->sock)) fclose($this->sock);
    }

    private function sendFrame(string $payload): void {
        $len = strlen($payload);
        if ($len < 126)       $header = chr(0x81) . chr(0x80 | $len);
        elseif ($len < 65536) $header = chr(0x81) . chr(0x80 | 126) . pack('n', $len);
        else                  $header = chr(0x81) . chr(0x80 | 127) . pack('J', $len);
        $mask = random_bytes(4);   // client frames must be masked
        $this->write($header . $mask . ($payload ^ str_repeat($mask, intdiv($len, 4) + 1)));
    }

    /** Next complete message, or null if none starts before $deadline. */
    private function nextMessage(float $deadline): ?array {
        $data = '';
        while (true) {
            if ($data === '' && !$this->waitReadable($deadline)) return null;
            $head = $this->read(2);
            $fin = (ord($head[0]) & 0x80) !== 0;
            $opcode = ord($head[0]) & 0x0f;
            $len = ord($head[1]) & 0x7f;
            if ($len === 126)     $len = unpack('n', $this->read(2))[1];
            elseif ($len === 127) $len = unpack('J', $this->read(8))[1];
            $payload = $this->read($len);

            if ($opcode === 0x8) throw new RuntimeException('the browser closed the connection');
            if ($opcode === 0x9 || $opcode === 0xA) continue;   // ping / pong
            $data .= $payload;
            if ($fin) return json_decode($data, true) ?: [];
        }
    }

    private function waitReadable(float $deadline): bool {
        // PHP may already hold buffered bytes that stream_select can't see.
        if (stream_get_meta_data($this->sock)['unread_bytes'] > 0) return true;
        $left = $deadline - microtime(true);
        if ($left <= 0) return false;
        $read = [$this->sock];
        $write = $except = null;
        return (bool) stream_select($read, $write, $except, (int) $left, (int) (($left - floor($left)) * 1e6));
    }

    private function read(int $n): string {
        $buf = '';
        while (strlen($buf) < $n) {
            $chunk = fread($this->sock, $n - strlen($buf));
            if ($chunk === false || ($chunk === '' && (feof($this->sock) || stream_get_meta_data($this->sock)['timed_out']))) {
                throw new RuntimeException('lost the connection to the browser');
            }
            $buf .= $chunk;
        }
        return $buf;
    }

    private function write(string $bytes): void {
        while ($bytes !== '') {
            $n = fwrite($this->sock, $bytes);
            if (!$n) throw new RuntimeException('could not talk to the browser');
            $bytes = substr($bytes, $n);
        }
    }
}
