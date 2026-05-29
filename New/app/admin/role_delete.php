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
$permissionManager->requirePermission('role.delete');

$role_id = $_GET['id'] ?? null;

if (!$role_id) {
    header('Location: roles_permissions.php?error=Invalid role');
    exit;
}

try {
    $permissionManager->deleteRole($role_id);
    header('Location: roles_permissions.php?message=Role deleted successfully');
} catch (Exception $e) {
    header('Location: roles_permissions.php?error=' . urlencode($e->getMessage()));
}
exit;
