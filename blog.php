<?php
// blog.php - Blog post entry point
require_once __DIR__ . '/config/database.php';

// Load environment variables
$envFile = __DIR__ . '/.env';
$appUrl = 'http://localhost/digifyce2';
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

// Get related posts (same category, limit 3, exclude current)
$relatedStmt = $pdo->prepare('SELECT b.id, b.title, b.slug, b.featured_image FROM blogs b WHERE b.category_id=? AND b.id != ? AND b.status="published" ORDER BY b.published_at DESC LIMIT 1');
$relatedStmt->execute([$blog['category_id'], $blog['id']]);
$nextBlog = $relatedStmt->fetch(PDO::FETCH_ASSOC);

// Increment view count
$pdo->prepare('UPDATE blogs SET view_count=view_count+1 WHERE id=?')->execute([$blog['id']]);

// Format published date
$publishedDate = date('M d, Y', strtotime($blog['published_at']));
$estimatedRead = max(1, ceil(str_word_count(strip_tags($blog['content'])) / 200));

include __DIR__ . '/app/views/blog.php';
