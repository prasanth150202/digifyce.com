<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_creative_development.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['creative_pains','creative_pillars','creative_services','creative_steps','creative_metrics','creative_why_cards','creative_stat_chips'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_creative_development.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_creative_development.php?deleted=1');
exit;
