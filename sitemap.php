<?php
// sitemap.php - Serves /sitemap.xml (see .htaccess).
// Static pages are listed below; blog posts come live from the blogs table, so a newly
// published post appears here automatically with its real modified date.
require_once __DIR__ . '/config/database.php';

$blogs = [];
try {
    $pdo = Database::getInstance();
    $blogs = $pdo->query(
        'SELECT slug, published_at, updated_at, created_at FROM blogs
         WHERE status = "published" ORDER BY published_at DESC, id DESC'
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // DB unavailable: still serve the static pages rather than a broken sitemap
}

// Database::getInstance() loads .env into $_ENV even when the connection itself fails
$appUrl = rtrim($_ENV['APP_URL'] ?? 'https://digifyce.com', '/');

// [path, lastmod or null, priority]
$staticPages = [
    // Homepage
    ['/',                                '2026-06-29', '1.00'],
    // Company
    ['/about-us',                        '2026-06-29', '0.80'],
    ['/careers',                         '2026-06-29', '0.70'],
    // Services
    ['/service',                         '2026-06-29', '0.90'],
    ['/d2c-branding-service',            '2026-09-26', '0.80'],
    ['/commercial-shoot-service',        '2026-09-26', '0.80'],
    ['/creative-development',            '2026-09-26', '0.80'],
    ['/shopify-development',             '2026-10-05', '0.80'],
    ['/performance-marketing-service',   '2026-09-26', '0.80'],
    ['/e-commerce-marketing-service',    '2026-09-26', '0.80'],
    ['/marketplace-management-service',  '2026-09-26', '0.80'],
    ['/content-marketing-service',       '2026-09-26', '0.80'],
    ['/lead_generations',                '2026-06-29', '0.80'],
    ['/instavideos',                     '2026-06-29', '0.70'],
    // Products & Technology
    ['/products',                        '2026-06-29', '0.80'],
    ['/technology',                      '2026-09-26', '0.80'],
    // Other public pages
    ['/testimonial',                     '2026-09-26', '0.70'],
    ['/d2c',                             '2026-06-29', '0.70'],
    // Blog
    ['/blog_list',                       '2026-06-29', '0.80'],
    // Legal
    ['/pages/privacy-policy',            '2026-06-29', '0.50'],
    ['/pages/terms-and-conditions',      '2026-06-29', '0.50'],
];

function sitemap_url(string $loc, ?string $lastmod, string $priority): string {
    $out  = "<url>\n";
    $out .= '  <loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if ($lastmod) {
        $out .= "  <lastmod>$lastmod</lastmod>\n";
    }
    $out .= "  <priority>$priority</priority>\n";
    $out .= "</url>\n";
    return $out;
}

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=3600');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($staticPages as [$path, $lastmod, $priority]) {
    echo sitemap_url($appUrl . $path, $lastmod, $priority);
}

foreach ($blogs as $blog) {
    // Same date blog_posting_schema() uses for dateModified, so the sitemap and the
    // on-page schema never disagree
    $modified = $blog['updated_at'] ?: ($blog['published_at'] ?: $blog['created_at']);
    $lastmod  = $modified ? date('Y-m-d', strtotime($modified)) : null;
    echo sitemap_url($appUrl . '/blog/' . rawurlencode($blog['slug']), $lastmod, '0.60');
}

echo "</urlset>\n";
