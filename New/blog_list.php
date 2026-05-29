<?php
// blog_list.php - Blog listing entry point
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

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;
$offset = ($page-1)*$perPage;
$where = 'WHERE b.status="published"';
$params = [];

if (!empty($_GET['q'])) {
    $where .= ' AND (b.title LIKE ? OR b.excerpt LIKE ?)';
    $params[] = '%' . $_GET['q'] . '%';
    $params[] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['category'])) {
    $where .= ' AND c.slug=?';
    $params[] = $_GET['category'];
}
if (!empty($_GET['tag'])) {
    // Join with tags table
    $whereJoin = ' INNER JOIN blog_tag_map m ON b.id=m.blog_id INNER JOIN blog_tags t ON m.tag_id=t.id WHERE b.status="published" AND t.slug=?';
    $tagParams = [$_GET['tag']];
    $sql = "SELECT DISTINCT b.id, b.title, b.slug, b.excerpt, b.featured_image, b.published_at, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $whereJoin ORDER BY b.published_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($tagParams);
} else {
    $sql = "SELECT b.id, b.title, b.slug, b.excerpt, b.featured_image, b.published_at, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $where ORDER BY b.published_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$categoriesStmt = $pdo->query('SELECT id, name, slug FROM blog_categories ORDER BY name');
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get all tags for filter
$tagsStmt = $pdo->query('SELECT id, name, slug FROM blog_tags ORDER BY name');
$tags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
if (!empty($_GET['tag'])) {
    $countSql = "SELECT COUNT(DISTINCT b.id) as total FROM blogs b INNER JOIN blog_tag_map m ON b.id=m.blog_id INNER JOIN blog_tags t ON m.tag_id=t.id WHERE b.status='published' AND t.slug=?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$_GET['tag']]);
} else {
    $countSql = "SELECT COUNT(*) as total FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $where";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
}
$totalBlogs = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalBlogs / $perPage);

// Format dates
foreach ($blogs as &$blog) {
    $blog['publishedDate'] = date('M d, Y', strtotime($blog['published_at']));
    $blog['estimatedRead'] = max(1, ceil(str_word_count(strip_tags($blog['excerpt'])) / 100));
}
unset($blog);


$currentTag = $_GET['tag'] ?? null;
$currentCategory = $_GET['category'] ?? null;

include __DIR__ . '/app/views/blog_list.php';
