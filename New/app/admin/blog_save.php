<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: 'n-a';
}

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$slug = $_POST['slug'] ?? slugify($title);
$excerpt = $_POST['excerpt'] ?? '';
$content = $_POST['content'] ?? '';
$author_id = $_POST['author_id'] ?: null;
$category_id = $_POST['category_id'] ?: null;
$status = $_POST['status'] ?? 'draft';
$scheduled_at = $_POST['scheduled_at'] ?: null;
$tags = $_POST['tags'] ?? [];

// Handle image upload
$featured_image = null;
if (!empty($_FILES['featured_image']['name'])) {
    $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('blog_', true) . '.' . $ext;
    $dest = __DIR__ . '/../../storage/uploads/' . $filename;
    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $dest)) {
        $featured_image = $filename;
    }
}

if ($id) {
    // Update
    $sql = 'UPDATE blogs SET title=?, slug=?, excerpt=?, content=?, author_id=?, category_id=?, status=?, scheduled_at=?, updated_at=NOW()';
    $params = [$title, $slug, $excerpt, $content, $author_id, $category_id, $status, $scheduled_at];
    if ($featured_image) {
        $sql .= ', featured_image=?';
        $params[] = $featured_image;
    }
    $sql .= ' WHERE id=?';
    $params[] = $id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $blog_id = $id;
    // Remove old tags
    $pdo->prepare('DELETE FROM blog_tag_map WHERE blog_id=?')->execute([$blog_id]);
} else {
    // Insert
    $stmt = $pdo->prepare('INSERT INTO blogs (title, slug, excerpt, content, author_id, category_id, status, scheduled_at, featured_image, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())');
    $stmt->execute([$title, $slug, $excerpt, $content, $author_id, $category_id, $status, $scheduled_at, $featured_image]);
    $blog_id = $pdo->lastInsertId();
}
// Insert tags
if ($tags && $blog_id) {
    $tag_stmt = $pdo->prepare('INSERT INTO blog_tag_map (blog_id, tag_id) VALUES (?, ?)');
    foreach ($tags as $tag_id) {
        $tag_stmt->execute([$blog_id, $tag_id]);
    }
}
header('Location: blogs.php');
exit;
