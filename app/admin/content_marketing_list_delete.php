<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_content_marketing.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['content_solutions','content_hero_stats','content_challenges','content_pillars','content_metrics','content_why_points'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_content_marketing.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_content_marketing.php?deleted=1');
exit;
