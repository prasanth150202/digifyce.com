<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$name = trim($_POST['name'] ?? '');
$position = (int)($_POST['position'] ?? 0);

if ($name === '' || $position <= 0 || empty($_FILES['logo'])) {
    header('Location: trusted_brands.php');
    exit;
}

$upload = $_FILES['logo'];
if ($upload['error'] !== UPLOAD_ERR_OK) {
    header('Location: trusted_brands.php');
    exit;
}

$allowed = ['png','jpg','jpeg','svg','webp','gif'];
$ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed, true)) {
    header('Location: trusted_brands.php');
    exit;
}

$baseName = pathinfo($upload['name'], PATHINFO_FILENAME);
$baseName = preg_replace('/[^a-z0-9-_]/i', '-', $baseName);
$baseName = trim($baseName, '-');
if ($baseName === '') {
    $baseName = 'logo';
}
$filename = $baseName . '-' . time() . '.' . $ext;

$uploadDir = __DIR__ . '/../../public/assets/cl_logos';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$destination = $uploadDir . '/' . $filename;
if (!move_uploaded_file($upload['tmp_name'], $destination)) {
    header('Location: trusted_brands.php');
    exit;
}

$logoUrl = 'public/assets/cl_logos/' . $filename;
$stmt = $pdo->prepare('INSERT INTO trusted_brands (name, logo_url, position) VALUES (?, ?, ?)');
$stmt->execute([$name, $logoUrl, $position]);

header('Location: trusted_brands.php');
exit;
