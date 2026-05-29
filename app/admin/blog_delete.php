<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('UPDATE blogs SET status="trashed" WHERE id=?');
    $stmt->execute([$_GET['id']]);
}
header('Location: blogs.php');
exit;
