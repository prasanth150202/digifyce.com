<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id  = intval($_GET['id'] ?? 0);
$pdo = Database::getInstance();
if ($id) {
    $pdo->prepare("DELETE FROM builder_pages WHERE id = ?")->execute([$id]);
}
header('Location: page_builder?deleted=1');
exit;
