<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_products.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['products_crm_features', 'products_zingbot_features'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_products.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_products.php?deleted=1');
exit;
