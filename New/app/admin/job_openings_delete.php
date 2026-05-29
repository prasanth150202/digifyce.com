<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'Invalid job opening ID';
    header('Location: job_openings.php');
    exit;
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('DELETE FROM job_openings WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Job opening deleted successfully';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error deleting job opening: ' . $e->getMessage();
}

header('Location: job_openings.php');
exit;
?>
