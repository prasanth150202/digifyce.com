<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (empty($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['pdf']['error'] ?? -1;
    $msg = match((int)$code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File too large (server limit: ' . ini_get('upload_max_filesize') . ').',
        UPLOAD_ERR_NO_TMP_DIR  => 'Server missing temp folder — contact your host.',
        UPLOAD_ERR_CANT_WRITE  => 'Server could not write the temp file.',
        default                => 'Upload failed (PHP error code ' . $code . ').',
    };
    echo json_encode(['error' => $msg]);
    exit;
}

$ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['error' => 'Only PDF files are allowed.']);
    exit;
}

// Validate actual MIME type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($_FILES['pdf']['tmp_name']);
if (!in_array($mime, ['application/pdf', 'application/x-pdf'], true)) {
    echo json_encode(['error' => 'File does not appear to be a valid PDF.']);
    exit;
}

$uploadDir = __DIR__ . '/../../storage/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!is_writable($uploadDir)) {
    echo json_encode(['error' => 'Upload folder is not writable (storage/uploads/). Set permissions to 755.']);
    exit;
}

$filename = uniqid('pdf_', true) . '.pdf';
$dest     = $uploadDir . '/' . $filename;

if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $dest)) {
    echo json_encode(['error' => 'Failed to move uploaded file — check server permissions.']);
    exit;
}

echo json_encode(['ok' => true, 'filename' => $filename]);
