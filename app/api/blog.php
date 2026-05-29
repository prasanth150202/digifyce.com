<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

function sanitize($str) {
    return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
}

if ($method === 'GET') {
    if (isset($_GET['slug'])) {
        // GET /api/blog?slug=...
        $stmt = $pdo->prepare('SELECT b.*, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id WHERE b.slug=? AND b.status="published"');
        $stmt->execute([$_GET['slug']]);
        $blog = $stmt->fetch();
        if ($blog) {
            // Increment view count
            $pdo->prepare('UPDATE blogs SET view_count=view_count+1 WHERE id=?')->execute([$blog['id']]);
            // Get tags
            $tags = $pdo->prepare('SELECT t.name, t.slug FROM blog_tag_map m JOIN blog_tags t ON m.tag_id=t.id WHERE m.blog_id=?');
            $tags->execute([$blog['id']]);
            $blog['tags'] = $tags->fetchAll();
            echo json_encode(['success'=>true,'data'=>$blog]);
        } else {
            http_response_code(404);
            echo json_encode(['success'=>false,'error'=>'Not found']);
        }
        exit;
    }
    // GET /api/blogs (list)
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10;
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
    $sql = "SELECT b.id, b.title, b.slug, b.excerpt, b.featured_image, b.published_at, a.name as author_name, c.name as category_name FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id LEFT JOIN blog_categories c ON b.category_id=c.id $where ORDER BY b.published_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $blogs = $stmt->fetchAll();
    echo json_encode(['success'=>true,'data'=>$blogs]);
    exit;
}

http_response_code(405);
echo json_encode(['success'=>false,'error'=>'Method not allowed']);
