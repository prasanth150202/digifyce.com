<?php
// blog_import.php - Secure import of an automatically written blog post as a DRAFT.
//
// Used by tools/blog_autopilot/publish.py. It can only ever create drafts: nothing it
// receives goes live until a person opens the post in the admin panel and publishes it.
//
// Security:
//  - Disabled until BLOG_IMPORT_TOKEN is set in the server's .env (no token = HTTP 503).
//  - The caller must send "Authorization: Bearer <token>"; compared in constant time.
//  - POST + JSON only, size limited, HTML is reduced to a safe whitelist of tags.
//  - Existing slugs are never overwritten (HTTP 409).

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function import_len(string $s): int {
    return function_exists('mb_strlen') ? mb_strlen($s) : strlen($s);
}

function import_fail(int $code, string $message, array $extra = []): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message] + $extra, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    import_fail(405, 'POST only');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/AppUrl.php';

try {
    $pdo = Database::getInstance(); // also loads .env into $_ENV
} catch (Throwable $e) {
    import_fail(500, 'Database unavailable');
}

$token = trim((string) ($_ENV['BLOG_IMPORT_TOKEN'] ?? ''));
if ($token === '' || strlen($token) < 24) {
    import_fail(503, 'Blog import is not configured');
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if ($authHeader === '' && function_exists('getallheaders')) {
    foreach (getallheaders() as $k => $v) {
        if (strcasecmp($k, 'Authorization') === 0) { $authHeader = $v; break; }
    }
}
$given = preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m) ? trim($m[1]) : '';
if ($given === '') {
    // Some hosts strip the Authorization header before PHP sees it; accept the same token here too.
    $given = trim((string) ($_SERVER['HTTP_X_IMPORT_TOKEN'] ?? ''));
}
if ($given === '' || !hash_equals($token, $given)) {
    usleep(300000); // slow down guessing
    import_fail(401, 'Unauthorized');
}

$raw = file_get_contents('php://input', false, null, 0, 600001);
if ($raw === false || strlen($raw) > 600000) {
    import_fail(413, 'Payload too large');
}
$in = json_decode($raw, true);
if (!is_array($in)) {
    import_fail(400, 'Body must be JSON');
}

// ---- Validate fields ---------------------------------------------------------------
$title    = trim((string) ($in['title'] ?? ''));
$slug     = strtolower(trim((string) ($in['slug'] ?? '')));
$excerpt  = trim((string) ($in['excerpt'] ?? ''));
$metaT    = trim((string) ($in['meta_title'] ?? ''));
$metaD    = trim((string) ($in['meta_description'] ?? ''));
$content  = (string) ($in['content'] ?? '');

if ($title === '' || import_len($title) > 255)  import_fail(422, 'title is required (max 255 characters)');
if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || strlen($slug) > 120) import_fail(422, 'slug must be lowercase letters, numbers and single hyphens');
if (import_len($metaT) > 255 || import_len($metaD) > 255)  import_fail(422, 'meta fields are too long');
if (import_len($content) < 800) import_fail(422, 'content is too short');

// Keep only the tags the blog template and editor already use.
$allowed = '<p><h2><h3><h4><ul><ol><li><strong><b><em><i><a><table><thead><tbody><tr><th><td><blockquote><br><span>';
$content = strip_tags($content, $allowed);
// Remove event handler attributes and javascript: links that strip_tags leaves inside allowed tags.
$content = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content);
$content = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*\2/i', '$1=$2#$2', $content);

// ---- Never overwrite an existing post ------------------------------------------------
$chk = $pdo->prepare('SELECT id FROM blogs WHERE slug = ? LIMIT 1');
$chk->execute([$slug]);
if ($existing = $chk->fetchColumn()) {
    import_fail(409, 'A post with this slug already exists', ['id' => (int) $existing]);
}

// ---- Optional author, category and tags (matched by name, never created) ------------
$authorId = null;
if (!empty($in['author'])) {
    $q = $pdo->prepare('SELECT id FROM blog_authors WHERE LOWER(name) = LOWER(?) LIMIT 1');
    $q->execute([trim((string) $in['author'])]);
    $authorId = $q->fetchColumn() ?: null;
}
$categoryId = null;
if (!empty($in['category'])) {
    $q = $pdo->prepare('SELECT id FROM blog_categories WHERE LOWER(name) = LOWER(?) OR slug = ? LIMIT 1');
    $q->execute([trim((string) $in['category']), strtolower(trim((string) $in['category']))]);
    $categoryId = $q->fetchColumn() ?: null;
}
$tagIds = [];
$tagsIgnored = [];
foreach ((array) ($in['tags'] ?? []) as $tagName) {
    $tagName = trim((string) $tagName);
    if ($tagName === '') continue;
    $q = $pdo->prepare('SELECT id FROM blog_tags WHERE LOWER(name) = LOWER(?) OR slug = ? LIMIT 1');
    $q->execute([$tagName, strtolower($tagName)]);
    $tid = $q->fetchColumn();
    if ($tid) { $tagIds[] = (int) $tid; } else { $tagsIgnored[] = $tagName; }
}

// ---- Insert as a draft ------------------------------------------------------------------
try {
    $nextOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order) + 1, 0) FROM blogs')->fetchColumn();
    $words = str_word_count(strip_tags($content));
    $readingTime = max(1, (int) ceil($words / 200));
    $ins = $pdo->prepare(
        'INSERT INTO blogs (title, slug, excerpt, content, meta_title, meta_description, author_id, category_id,
                            status, scheduled_at, featured_image, reading_time, sort_order, created_at, updated_at)
         VALUES (?,?,?,?,?,?,?,?, "draft", NULL, NULL, ?, ?, NOW(), NOW())'
    );
    $ins->execute([$title, $slug, $excerpt, $content, $metaT, $metaD, $authorId, $categoryId, $readingTime, $nextOrder]);
    $blogId = (int) $pdo->lastInsertId();

    if ($tagIds) {
        $tm = $pdo->prepare('INSERT IGNORE INTO blog_tag_map (blog_id, tag_id) VALUES (?, ?)');
        foreach (array_unique($tagIds) as $tid) $tm->execute([$blogId, $tid]);
    }
} catch (Throwable $e) {
    import_fail(500, 'Could not save the draft');
}

// ---- Small audit trail ------------------------------------------------------------------------
$logDir = __DIR__ . '/../../storage/logs';
if (is_dir($logDir) || @mkdir($logDir, 0755, true)) {
    @file_put_contents(
        $logDir . '/blog_import.log',
        date('c') . "\tdraft\t" . $blogId . "\t" . $slug . "\t" . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n",
        FILE_APPEND | LOCK_EX
    );
}

echo json_encode([
    'ok'           => true,
    'id'           => $blogId,
    'slug'         => $slug,
    'status'       => 'draft',
    'edit_url'     => AppUrl::resolve() . '/app/admin/blog_edit.php?id=' . $blogId,
    'tags_ignored' => $tagsIgnored,
    'words'        => $words,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
