<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
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
$name = $_POST['name'] ?? '';
$slug = $_POST['slug'] ?: slugify($name);
if ($name) {
    $stmt = $pdo->prepare('INSERT INTO blog_categories (name, slug) VALUES (?, ?)');
    $stmt->execute([$name, $slug]);
}
header('Location: categories.php');
exit;
