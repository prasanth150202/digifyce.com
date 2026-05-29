<?php
// Toggles a blog post's status without touching content.
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id     = intval($_GET['id'] ?? 0);
$status = in_array($_GET['status'] ?? '', ['draft', 'published', 'scheduled']) ? $_GET['status'] : 'draft';
$pdo    = Database::getInstance();

if ($id) {
    $publishedAt = $status === 'published' ? ', published_at = NOW()' : '';
    $pdo->prepare("UPDATE blogs SET status = ?, updated_at = NOW() $publishedAt WHERE id = ?")
        ->execute([$status, $id]);
}

header("Location: blog_preview?id=$id");
exit;
