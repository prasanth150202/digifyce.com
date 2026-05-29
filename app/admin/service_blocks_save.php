<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_service.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$fields = [
    'sort_order'              => (int)($_POST['sort_order'] ?? 0),
    'is_active'               => (int)($_POST['is_active'] ?? 1),
    'section_number'          => $_POST['section_number'] ?? '',
    'title_line1'             => $_POST['title_line1'] ?? '',
    'title_line2'             => $_POST['title_line2'] ?? '',
    'description'             => $_POST['description'] ?? '',
    'tech_badges'             => $_POST['tech_badges'] ?? '',
    'left_col_heading'        => $_POST['left_col_heading'] ?? '',
    'left_metrics_json'       => $_POST['left_metrics_json'] ?? '[]',
    'right_col_heading'       => $_POST['right_col_heading'] ?? '',
    'right_metrics_json'      => $_POST['right_metrics_json'] ?? '[]',
    'case_study_icon'         => $_POST['case_study_icon'] ?? '',
    'case_study_section_label'=> $_POST['case_study_section_label'] ?? '',
    'case_study_quote'        => $_POST['case_study_quote'] ?? '',
    'case_study_body'         => $_POST['case_study_body'] ?? '',
    'case_study_image_url'    => $_POST['case_study_image_url'] ?? '',
    'case_study_stats_json'   => $_POST['case_study_stats_json'] ?? '[]',
];

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE service_blocks SET $sets WHERE id=?")
        ->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO service_blocks ($cols) VALUES ($phs)")
        ->execute(array_values($fields));
}

header('Location: page_service.php?saved=1#tab-blocks');
exit;
