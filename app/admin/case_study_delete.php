<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: case_studies.php');
    exit;
}

$stmt = $pdo->prepare('SELECT file_path FROM case_studies WHERE id = ?');
$stmt->execute([$id]);
$study = $stmt->fetch();

if ($study) {
    $deleteStmt = $pdo->prepare('DELETE FROM case_studies WHERE id = ?');
    $deleteStmt->execute([$id]);

    $filePath = $study['file_path'] ?? '';
    if ($filePath && strpos($filePath, 'storage/case_studies/') !== false) {
        $path = __DIR__ . '/../../' . ltrim($filePath, '/');
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

header('Location: case_studies.php');
exit;
