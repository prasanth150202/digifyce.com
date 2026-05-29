<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: pages.php');
    exit;
}

$pdo = Database::getInstance();
$stmt = $pdo->prepare('DELETE FROM pages WHERE id = ?');
$stmt->execute([$_GET['id']]);

header('Location: pages.php');
exit;
