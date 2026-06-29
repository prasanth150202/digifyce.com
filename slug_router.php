<?php
// Routes clean URLs: page_seo → builder_pages → PHP file fallback (subdirs only) → 404
require_once __DIR__ . '/config/database.php';

try {
    $pdo = Database::getInstance();
    $uri = '/' . ltrim($_GET['uri'] ?? '', '/');

    // 1. Check static page SEO aliases
    $stmt = $pdo->prepare("SELECT php_file FROM page_seo WHERE slug = ? AND php_file != '' LIMIT 1");
    $stmt->execute([$uri]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['php_file'])) {
        $target = __DIR__ . '/' . $row['php_file'];
        if (file_exists($target)) { include $target; exit; }
    }

    // 2. Check Page Builder custom pages
    $stmt2 = $pdo->prepare("SELECT id FROM builder_pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt2->execute([$uri]);
    if ($stmt2->fetch()) {
        $_GET['builder_slug'] = $uri;
        include __DIR__ . '/custom-page.php';
        exit;
    }

} catch (Exception $e) {
    // fall through
}

$uri_clean = ltrim($_GET['uri'] ?? '', '/');

// 3. Whitelisted top-level public pages
$publicPages = [
    'leadform', 'thankyou', 'about-us', 'about-us-new',
    'brand-shoot', 'careers', 'content-marketing', 'creative-dev',
    'd2c-branding', 'd2c', 'e-com-marketing', 'instavideos',
    'lead_generations', 'market-manage', 'performance-marketing', 'products',
    'service', 'technology', 'testimonial',
    'blog_list',
];
if (in_array($uri_clean, $publicPages, true)) {
    $phpFile = __DIR__ . '/' . $uri_clean . '.php';
    if (file_exists($phpFile) && is_file($phpFile)) {
        include $phpFile;
        exit;
    }
}

// 4. PHP file fallback — only for sub-directory paths (admin, config, etc.)
if (substr_count($uri_clean, '/') > 0 && strpos($uri_clean, '..') === false && strpos($uri_clean, "\0") === false) {
    $phpFile = __DIR__ . '/' . $uri_clean . '.php';
    if (file_exists($phpFile) && is_file($phpFile)) {
        include $phpFile;
        exit;
    }
}

http_response_code(404);
include __DIR__ . '/404.php';
