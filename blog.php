<?php
// blog.php - Blog post entry point
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';

// Load environment variables
$envFile = __DIR__ . '/.env';
require_once __DIR__ . '/app/utilities/AppUrl.php';
$appUrl = AppUrl::resolve();
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') === false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($key === 'APP_URL') {
                $appUrl = rtrim($value, '/');
            }
        }
    }
}

$pdo = Database::getInstance();

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    http_response_code(404);
    echo 'Blog post not found.';
    exit;
}

// Redirect old /blog?slug=X to clean /blog/slug
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#/blog/[^/]+$#', $requestPath)) {
    header('Location: ' . $appUrl . '/blog/' . rawurlencode($slug), true, 301);
    exit;
}

$stmt = $pdo->prepare('SELECT b.*, a.name as author_name, a.avatar_url as author_avatar, a.bio as author_bio, c.name as category_name, c.slug as category_slug FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id WHERE b.slug=? AND b.status="published"');
$stmt->execute([$slug]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$blog) {
    http_response_code(404);
    echo 'Blog post not found.';
    exit;
}

// Get tags
$tagsStmt = $pdo->prepare('SELECT t.name, t.slug FROM blog_tag_map m JOIN blog_tags t ON m.tag_id=t.id WHERE m.blog_id=?');
$tagsStmt->execute([$blog['id']]);
$blog['tags'] = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

// Set page meta tags for SEO
$pageTitle = $blog['meta_title'] ?: ($blog['title'] . ' | Digifyce');
$pageDescription = $blog['meta_description'] ?: (substr(strip_tags($blog['excerpt'] ?? $blog['content']), 0, 160));

// Get 3 related posts — same category first, filled with recent others
$relatedStmt = $pdo->prepare('SELECT b.id, b.title, b.slug, b.featured_image, c.name as category_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.id != ? AND b.status = "published" ORDER BY (b.category_id = ?) DESC, b.published_at DESC LIMIT 3');
$relatedStmt->execute([$blog['id'], $blog['category_id']]);
$relatedBlogs = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Increment view count
$pdo->prepare('UPDATE blogs SET view_count=view_count+1 WHERE id=?')->execute([$blog['id']]);

// Format published date (fall back to created_at when published_at wasn't set)
$publishedDate = date('M d, Y', strtotime($blog['published_at'] ?: $blog['created_at']));
$estimatedRead = max(1, ceil(str_word_count(strip_tags($blog['content'])) / 200));

include __DIR__ . '/app/views/blog.php';
