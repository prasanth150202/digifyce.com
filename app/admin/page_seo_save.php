<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: page_seo.php');
    exit;
}

$pdo    = Database::getInstance();
$id     = intval($_POST['id'] ?? 0);
$source = in_array($_POST['source'] ?? '', ['static', 'builder']) ? $_POST['source'] : 'static';
$slug   = trim($_POST['slug'] ?? '');
$meta_title       = trim($_POST['meta_title']       ?? '');
$meta_description = trim($_POST['meta_description'] ?? '');

// Normalize slug
if ($slug !== '' && $slug[0] !== '/') {
    $slug = '/' . $slug;
}

if ($id > 0) {
    if ($source === 'builder') {
        // builder_pages uses meta_desc (not meta_description)
        $stmt = $pdo->prepare("
            UPDATE builder_pages
            SET meta_title = ?, meta_desc = ?, slug = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $meta_title       ?: null,
            $meta_description ?: null,
            $slug             ?: null,
            $id,
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE page_seo
            SET meta_title = ?, meta_description = ?, slug = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $meta_title       ?: null,
            $meta_description ?: null,
            $slug             ?: null,
            $id,
        ]);
    }
}

header('Location: page_seo.php?saved=1');
exit;
