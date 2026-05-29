<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_commercial_shoot.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['commercial_shoot_challenges','commercial_shoot_services','commercial_shoot_steps','commercial_shoot_impacts','cs_hero_features','cs_approach_panels','cs_why_bullets'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_commercial_shoot.php'); exit; }

Database::getInstance()->prepare("DELETE FROM `$table` WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_commercial_shoot.php?deleted=1');
exit;
