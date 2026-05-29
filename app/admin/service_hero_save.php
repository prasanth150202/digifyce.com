<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_service.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$pdo->prepare("UPDATE service_hero SET badge=?, headline_line1=?, headline_line2=?, description=?, stat_label=?, stat_value=?, stat_suffix=? WHERE id=1")
    ->execute([
        $_POST['badge'] ?? '',
        $_POST['headline_line1'] ?? '',
        $_POST['headline_line2'] ?? '',
        $_POST['description'] ?? '',
        $_POST['stat_label'] ?? '',
        $_POST['stat_value'] ?? '',
        $_POST['stat_suffix'] ?? '',
    ]);

header('Location: page_service.php?saved=1');
exit;
