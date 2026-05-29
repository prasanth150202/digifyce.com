<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_builder'); exit; }

$pdo    = Database::getInstance();
$pageId = intval($_POST['page_id'] ?? 0);
$title  = trim($_POST['title'] ?? '');
$slug   = trim($_POST['slug'] ?? '');
$status = in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft';
$meta_title = trim($_POST['meta_title'] ?? '');
$meta_desc  = trim($_POST['meta_desc']  ?? '');

if (!$title || !$slug) {
    header('Location: page_builder_edit' . ($pageId ? "?id=$pageId" : '') . '&err=missing');
    exit;
}

// Normalise slug
$slug = '/' . ltrim(preg_replace('/[^a-z0-9\-\/]/', '-', strtolower($slug)), '/');

$sections = json_decode($_POST['sections_json'] ?? '[]', true);
if (!is_array($sections)) $sections = [];

if ($pageId) {
    // Update
    $stmt = $pdo->prepare("UPDATE builder_pages SET title=?, slug=?, meta_title=?, meta_desc=?, status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$title, $slug, $meta_title ?: null, $meta_desc ?: null, $status, $pageId]);
} else {
    // Insert
    $stmt = $pdo->prepare("INSERT INTO builder_pages (title, slug, meta_title, meta_desc, status) VALUES (?,?,?,?,?)");
    $stmt->execute([$title, $slug, $meta_title ?: null, $meta_desc ?: null, $status]);
    $pageId = $pdo->lastInsertId();
}

// Replace sections (delete + re-insert in order)
$pdo->prepare("DELETE FROM builder_sections WHERE page_id = ?")->execute([$pageId]);

$ins = $pdo->prepare("INSERT INTO builder_sections (page_id, section_type, sort_order, config) VALUES (?,?,?,?)");
foreach ($sections as $i => $sec) {
    $type   = preg_replace('/[^a-z_]/', '', $sec['type'] ?? '');
    $config = json_encode($sec['config'] ?? []);
    $ins->execute([$pageId, $type, $i, $config]);
}

header("Location: page_builder_edit?id=$pageId&saved=1");
exit;
