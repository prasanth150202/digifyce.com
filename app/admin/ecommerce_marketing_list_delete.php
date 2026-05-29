<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_ecommerce_marketing.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['ecom_challenges','ecom_approaches','ecom_steps','ecom_solutions','ecom_why_points'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_ecommerce_marketing.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_ecommerce_marketing.php?deleted=1');
exit;
