<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$uploadDir = __DIR__ . '/../../public/assets/site';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function saveSetting($pdo, $key, $value) {
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}

function handleUpload($fileKey, $allowedExts, $uploadDir) {
    if (empty($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $upload = $_FILES[$fileKey];
    $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return null;
    }
    $baseName = pathinfo($upload['name'], PATHINFO_FILENAME);
    $baseName = preg_replace('/[^a-z0-9-_]/i', '-', $baseName);
    $baseName = trim($baseName, '-');
    if ($baseName === '') {
        $baseName = $fileKey;
    }
    $filename = $baseName . '-' . time() . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($upload['tmp_name'], $destination)) {
        return null;
    }
    return 'public/assets/site/' . $filename;
}

$logoPath = handleUpload('site_logo', ['png','jpg','jpeg','svg','webp'], $uploadDir);
if ($logoPath) {
    saveSetting($pdo, 'site_logo', $logoPath);
}

$faviconPath = handleUpload('site_favicon', ['png','jpg','jpeg','svg','ico'], $uploadDir);
if ($faviconPath) {
    saveSetting($pdo, 'site_favicon', $faviconPath);
}

$footerLogoPath = handleUpload('footer_logo', ['png','jpg','jpeg','svg','webp'], $uploadDir);
if ($footerLogoPath) {
    saveSetting($pdo, 'footer_logo', $footerLogoPath);
}

if (isset($_POST['footer_copyright'])) {
    saveSetting($pdo, 'footer_copyright', $_POST['footer_copyright']);
}

if (isset($_POST['footer_description'])) {
    saveSetting($pdo, 'footer_description', $_POST['footer_description']);
}

if (isset($_POST['nav_cta_label'])) {
    saveSetting($pdo, 'nav_cta_label', $_POST['nav_cta_label']);
}

if (isset($_POST['nav_cta_url'])) {
    saveSetting($pdo, 'nav_cta_url', $_POST['nav_cta_url']);
}

if (isset($_POST['home_cta_label'])) {
    saveSetting($pdo, 'home_cta_label', $_POST['home_cta_label']);
}

if (isset($_POST['home_cta_url'])) {
    saveSetting($pdo, 'home_cta_url', $_POST['home_cta_url']);
}

if (isset($_POST['home_cta_note'])) {
    saveSetting($pdo, 'home_cta_note', $_POST['home_cta_note']);
}

if (isset($_POST['service_cta_heading'])) {
    saveSetting($pdo, 'service_cta_heading', $_POST['service_cta_heading']);
}

if (isset($_POST['service_cta_text'])) {
    saveSetting($pdo, 'service_cta_text', $_POST['service_cta_text']);
}

if (isset($_POST['service_cta_primary_label'])) {
    saveSetting($pdo, 'service_cta_primary_label', $_POST['service_cta_primary_label']);
}

if (isset($_POST['service_cta_primary_url'])) {
    saveSetting($pdo, 'service_cta_primary_url', $_POST['service_cta_primary_url']);
}

if (isset($_POST['service_cta_secondary_label'])) {
    saveSetting($pdo, 'service_cta_secondary_label', $_POST['service_cta_secondary_label']);
}

if (isset($_POST['service_cta_secondary_url'])) {
    saveSetting($pdo, 'service_cta_secondary_url', $_POST['service_cta_secondary_url']);
}

header('Location: site_settings.php');
exit;
