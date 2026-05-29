<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }

require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$allowed = ['pm_challenges','pm_approaches','pm_services','pm_leadgen_tabs','pm_seo_panels','pm_steps','pm_impacts','pm_hero_metrics','pm_benchmark_groups'];
$table = $_POST['table_name'] ?? '';
$id    = (int)($_POST['id'] ?? 0);

if (!in_array($table, $allowed, true) || $id <= 0) {
    http_response_code(400); exit('Invalid request');
}

$pdo->prepare("DELETE FROM `$table` WHERE id=?")->execute([$id]);

header('Location: page_performance_marketing.php?deleted=1');
exit;
