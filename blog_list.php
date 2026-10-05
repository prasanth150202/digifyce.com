<?php
// blog_list.php - Blog listing entry point
require_once __DIR__ . '/config/database.php';

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

// Redirect /blog?slug=X to /blog/X (old query-string blog post links)
if (!empty($_GET['slug'])) {
    header('Location: ' . $appUrl . '/blog/' . rawurlencode($_GET['slug']), true, 301);
    exit;
}

// Redirect old ?-style URLs to clean path-based URLs
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#/blog_list/(category|tag|page)/#', $requestPath)) {
    $cleanUrl = $appUrl . '/blog_list';
    $redirectNeeded = false;
    if (!empty($_GET['category'])) {
        $cleanUrl .= '/category/' . rawurlencode($_GET['category']);
        $redirectNeeded = true;
    } elseif (!empty($_GET['tag'])) {
        $cleanUrl .= '/tag/' . rawurlencode($_GET['tag']);
        $redirectNeeded = true;
    }
    if (!empty($_GET['page']) && intval($_GET['page']) > 1) {
        $cleanUrl .= '/page/' . intval($_GET['page']);
        $redirectNeeded = true;
    }
    if (!empty($_GET['sort'])) {
        $cleanUrl .= '?sort=' . rawurlencode($_GET['sort']);
    }
    if ($redirectNeeded) {
        header('Location: ' . $cleanUrl, true, 301);
        exit;
    }
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;
$offset = ($page-1)*$perPage;
$where = 'WHERE b.status="published"';
$params = [];

// ── Sort mode (matches admin panel) ────────────────────────────────────────
$validSorts      = ['a-z','z-a','old-new','new-old','manual'];
$defaultSort     = 'new-old';
$defaultSortSaved = false;
try {
    $dsRow = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key='blog_default_sort' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($dsRow && in_array($dsRow['setting_value'], $validSorts)) {
        $defaultSort      = $dsRow['setting_value'];
        $defaultSortSaved = true;
    }
} catch (Exception $e) {}

// Auto-detect: if admin never explicitly saved a default sort but has manual sort_order
// values in the DB, use manual ordering automatically.
if (!$defaultSortSaved) {
    try {
        $hasOrdered = $pdo->query("SELECT 1 FROM blogs WHERE sort_order IS NOT NULL LIMIT 1")->fetch();
        if ($hasOrdered) $defaultSort = 'manual';
    } catch (Exception $e) {}
}

$sort = in_array($_GET['sort'] ?? '', $validSorts) ? $_GET['sort'] : $defaultSort;

$orderBy = match($sort) {
    'a-z'     => 'b.title ASC',
    'z-a'     => 'b.title DESC',
    'old-new' => 'COALESCE(b.published_at, b.created_at) ASC',
    'manual'  => 'ISNULL(b.sort_order) ASC, b.sort_order ASC, b.id ASC',
    default   => 'COALESCE(b.published_at, b.created_at) DESC',  // new-old
};

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
    $sql = "SELECT DISTINCT b.id, b.title, b.slug, b.excerpt, b.featured_image, b.published_at, b.created_at, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $whereJoin ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($tagParams);
} else {
    $sql = "SELECT b.id, b.title, b.slug, b.excerpt, b.featured_image, b.published_at, b.created_at, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $where ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
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

// Format dates (fall back to created_at when published_at wasn't set)
foreach ($blogs as &$blog) {
    $blog['publishedDate'] = date('M d, Y', strtotime($blog['published_at'] ?: $blog['created_at']));
    $blog['estimatedRead'] = max(1, ceil(str_word_count(strip_tags($blog['excerpt'])) / 100));
}
unset($blog);


$currentTag = $_GET['tag'] ?? null;
$currentCategory = $_GET['category'] ?? null;

include __DIR__ . '/app/views/blog_list.php';
