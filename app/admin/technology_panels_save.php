<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_technology.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$fields = [
    'sort_order'     => (int)($_POST['sort_order'] ?? 0),
    'is_active'      => (int)($_POST['is_active'] ?? 1),
    'panel_number'   => $_POST['panel_number'] ?? '',
    'category_label' => $_POST['category_label'] ?? '',
    'title'          => $_POST['title'] ?? '',
    'description'    => $_POST['description'] ?? '',
    'bullets_json'   => $_POST['bullets_json'] ?? '[]',
    'image_paths'    => $_POST['image_paths'] ?? '',
];

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE technology_panels SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO technology_panels ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_technology.php?saved=1#tab-panels');
exit;
