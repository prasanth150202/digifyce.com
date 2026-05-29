<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/PermissionManager.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pdo = Database::getInstance();
$permissionManager = new PermissionManager($pdo);

// Check permission
$permissionManager->requirePermission('user.delete');

$user_id = $_GET['id'] ?? null;

if (!$user_id || $user_id == $_SESSION['user_id']) {
    header('Location: users.php?error=Invalid user');
    exit;
}

try {
    $permissionManager->deleteUser($user_id);
    header('Location: users.php?message=User deleted successfully');
} catch (Exception $e) {
    header('Location: users.php?error=' . urlencode($e->getMessage()));
}
exit;
