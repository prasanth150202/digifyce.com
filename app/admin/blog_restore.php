<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id) {
    Database::getInstance()
        ->prepare("UPDATE blogs SET status = 'draft', updated_at = NOW() WHERE id = ?")
        ->execute([$id]);
}
header('Location: blogs.php?restored=1');
exit;
