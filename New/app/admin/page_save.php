<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$slug = $_POST['slug'] ?? '';
$content = $_POST['content'] ?? '';

if (!$title || !$slug || !$content) {
    header('Location: page_edit.php?error=missing_fields');
    exit;
}

// Validate slug format
$slug = strtolower(preg_replace('/[^a-z0-9-]/', '-', $slug));
$slug = trim($slug, '-');

if ($id) {
    $stmt = $pdo->prepare('UPDATE pages SET title = ?, slug = ?, content = ? WHERE id = ?');
    $stmt->execute([$title, $slug, $content, $id]);
} else {
    $stmt = $pdo->prepare('INSERT INTO pages (title, slug, content) VALUES (?, ?, ?)');
    $stmt->execute([$title, $slug, $content]);
}

header('Location: pages.php');
exit;
