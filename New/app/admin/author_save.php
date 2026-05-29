<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$name = $_POST['name'] ?? '';
$bio = $_POST['bio'] ?? '';
$avatar_url = null;
if (!empty($_FILES['avatar']['name'])) {
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('author_', true) . '.' . $ext;
    $dest = __DIR__ . '/../../storage/uploads/' . $filename;
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
        $avatar_url = $filename;
    }
}
if ($name) {
    $stmt = $pdo->prepare('INSERT INTO blog_authors (name, bio, avatar_url) VALUES (?, ?, ?)');
    $stmt->execute([$name, $bio, $avatar_url]);
}
header('Location: authors.php');
exit;
