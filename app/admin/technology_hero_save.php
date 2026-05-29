<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_technology.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

Database::getInstance()->prepare("UPDATE technology_hero SET badge=?, headline=?, description=? WHERE id=1")
    ->execute([$_POST['badge'] ?? '', $_POST['headline'] ?? '', $_POST['description'] ?? '']);
header('Location: page_technology.php?saved=1');
exit;
