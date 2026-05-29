<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_testimonial.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$fields = [
    'sort_order'     => (int)($_POST['sort_order'] ?? 0),
    'is_active'      => (int)($_POST['is_active'] ?? 1),
    'client_name'    => $_POST['client_name'] ?? '',
    'quote'          => $_POST['quote'] ?? '',
    'story_label'    => $_POST['story_label'] ?? '',
    'video_path'     => $_POST['video_path'] ?? '',
    'thumbnail_path' => $_POST['thumbnail_path'] ?? '',
    'logo_path'      => $_POST['logo_path'] ?? '',
];

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE testimonial_items SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO testimonial_items ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_testimonial.php?saved=1');
exit;
