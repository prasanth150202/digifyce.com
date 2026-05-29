<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_about_us.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['about_hero_stats','about_why_cards','about_who_sub_cards','about_what_we_do','about_approach_pillars','about_why_digi_cards'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_about_us.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id < 1) { header('Location: page_about_us.php'); exit; }

$pdo = Database::getInstance();
$pdo->prepare("DELETE FROM `$table` WHERE id=?")->execute([$id]);
header('Location: page_about_us.php?deleted=1');
exit;
