<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: trusted_brands.php');
    exit;
}

$stmt = $pdo->prepare('SELECT logo_url FROM trusted_brands WHERE id = ?');
$stmt->execute([$id]);
$brand = $stmt->fetch();

if ($brand) {
    $deleteStmt = $pdo->prepare('DELETE FROM trusted_brands WHERE id = ?');
    $deleteStmt->execute([$id]);

    $logoUrl = $brand['logo_url'] ?? '';
    if ($logoUrl && strpos($logoUrl, 'public/assets/cl_logos/') !== false) {
        $path = __DIR__ . '/../../' . ltrim($logoUrl, '/');
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

header('Location: trusted_brands.php');
exit;
