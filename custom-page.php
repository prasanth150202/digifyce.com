<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/views/render_section.php';

$pdo = Database::getInstance();

// Accept slug from URL param (set by slug_router) or REQUEST_URI
$slug = $_GET['builder_slug'] ?? null;
if (!$slug) {
    $uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = rtrim(parse_url($_ENV['APP_URL'] ?? '', PHP_URL_PATH), '/');
    $slug = '/' . ltrim(str_replace($base, '', $uri), '/');
}

$stmt = $pdo->prepare("SELECT * FROM builder_pages WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// Load sections
$rows = $pdo->prepare("SELECT section_type, config FROM builder_sections WHERE page_id = ? ORDER BY sort_order ASC");
$rows->execute([$page['id']]);
$sections = $rows->fetchAll(PDO::FETCH_ASSOC);

$pageTitle       = $page['meta_title'] ?: ($page['title'] . ' | Digifyce');
$pageDescription = $page['meta_desc']  ?: '';

include __DIR__ . '/app/views/header.php';

foreach ($sections as $sec) {
    $cfg = json_decode($sec['config'], true) ?: [];
    echo render_section($sec['section_type'], $cfg);
}

include __DIR__ . '/app/views/footer.php';
