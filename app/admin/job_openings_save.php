<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$division = $_POST['division'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$requirements = $_POST['requirements'] ?? '';
$position = $_POST['position'] ?? 0;
$is_active = $_POST['is_active'] ?? 1;

if (!$title) {
    $_SESSION['error'] = 'Job title is required';
    header('Location: job_openings.php');
    exit;
}

try {
    if ($id) {
        // Update existing job
        $stmt = $pdo->prepare('UPDATE job_openings SET title = ?, division = ?, location = ?, description = ?, requirements = ?, position = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$title, $division, $location, $description, $requirements, $position, $is_active, $id]);
        $_SESSION['success'] = 'Job opening updated successfully';
    } else {
        // Insert new job
        $stmt = $pdo->prepare('INSERT INTO job_openings (title, division, location, description, requirements, position, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $division, $location, $description, $requirements, $position, $is_active]);
        $_SESSION['success'] = 'Job opening created successfully';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error saving job opening: ' . $e->getMessage();
}

header('Location: job_openings.php');
exit;
?>
