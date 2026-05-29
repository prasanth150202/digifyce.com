<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$uploadDir = __DIR__ . '/../../public/assets/cl_logos';
if (!is_dir($uploadDir)) {
    header('Location: trusted_brands.php');
    exit;
}

$allowed = ['png','jpg','jpeg','svg','webp','gif'];
$files = array_filter(glob($uploadDir . '/*'), function ($file) use ($allowed) {
    if (!is_file($file)) return false;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($ext, $allowed, true);
});

$maxPosition = (int)$pdo->query('SELECT COALESCE(MAX(position), 0) AS max_pos FROM trusted_brands')->fetchColumn();
$position = $maxPosition + 1;

foreach ($files as $file) {
    $filename = basename($file);
    $logoUrl = 'public/assets/cl_logos/' . $filename;

    $existsStmt = $pdo->prepare('SELECT COUNT(*) FROM trusted_brands WHERE logo_url = ?');
    $existsStmt->execute([$logoUrl]);
    if ($existsStmt->fetchColumn() > 0) {
        continue;
    }

    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = str_replace(['-', '_'], ' ', $name);
    $name = ucwords(trim($name));
    if ($name === '') {
        $name = 'Brand';
    }

    $insertStmt = $pdo->prepare('INSERT INTO trusted_brands (name, logo_url, position) VALUES (?, ?, ?)');
    $insertStmt->execute([$name, $logoUrl, $position]);
    $position++;
}

header('Location: trusted_brands.php');
exit;
