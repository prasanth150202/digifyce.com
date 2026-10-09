<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$position = (int) ($_POST['position'] ?? 0);

if ($title === '' || $position <= 0 || empty($_FILES['document'])) {
    header('Location: case_studies.php');
    exit;
}

$upload = $_FILES['document'];
if ($upload['error'] !== UPLOAD_ERR_OK) {
    header('Location: case_studies.php');
    exit;
}

// Modern Office Open XML formats + PDF only - these are the formats
// tools/website_audit/case_study_extractor.py knows how to read text out
// of. Legacy binary .doc/.ppt/.xls aren't supported.
$allowed = ['pdf', 'docx', 'pptx', 'xlsx'];
$ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed, true)) {
    header('Location: case_studies.php');
    exit;
}

$baseName = pathinfo($upload['name'], PATHINFO_FILENAME);
$baseName = preg_replace('/[^a-z0-9-_]/i', '-', $baseName);
$baseName = trim($baseName, '-');
if ($baseName === '') {
    $baseName = 'case-study';
}
$filename = $baseName . '-' . time() . '.' . $ext;

// storage/, not public/assets/ - these are internal source documents for
// the AI to read, not brand assets meant to be linked/displayed site-wide.
$uploadDir = __DIR__ . '/../../storage/case_studies';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$destination = $uploadDir . '/' . $filename;
if (!move_uploaded_file($upload['tmp_name'], $destination)) {
    header('Location: case_studies.php');
    exit;
}

$filePath = 'storage/case_studies/' . $filename;
$stmt = $pdo->prepare('INSERT INTO case_studies (title, description, file_path, position) VALUES (?, ?, ?, ?)');
$stmt->execute([$title, $description ?: null, $filePath, $position]);

header('Location: case_studies.php');
exit;
