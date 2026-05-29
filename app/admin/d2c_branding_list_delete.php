<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_d2c_branding.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['d2c_intro_tags','d2c_challenges','d2c_pillars','d2c_solutions','d2c_steps','d2c_metrics','d2c_why_features'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_d2c_branding.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_d2c_branding.php?deleted=1');
exit;
