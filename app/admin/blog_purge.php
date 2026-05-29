<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id) {
    // Only permanently delete posts that are already trashed
    Database::getInstance()
        ->prepare("DELETE FROM blogs WHERE id = ? AND status = 'trashed'")
        ->execute([$id]);
}
header('Location: blogs.php?purged=1');
exit;
