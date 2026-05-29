<?php
// Toggles a builder page's status (draft ↔ published) without touching its sections.
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id     = intval($_GET['id'] ?? 0);
$status = in_array($_GET['status'] ?? '', ['draft','published']) ? $_GET['status'] : 'published';
$pdo    = Database::getInstance();

if ($id) {
    $pdo->prepare("UPDATE builder_pages SET status = ?, updated_at = NOW() WHERE id = ?")
        ->execute([$status, $id]);
}

// Redirect back to preview so the banner reflects the new status
header("Location: page_builder_preview?id=$id");
exit;
