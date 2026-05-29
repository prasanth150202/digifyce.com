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

$role_id = $_POST['role_id'] ?? null;
$save_permissions = $_POST['save_permissions'] ?? false;

if ($role_id && $save_permissions) {
    // Saving permissions for existing role
    $permissionManager->requirePermission('role.edit');

    $permission_ids = $_POST['permissions'] ?? [];
    
    try {
        $permissionManager->assignPermissionsToRole($role_id, $permission_ids);
        PermissionManager::clearCache();
        
        header('Location: roles_permissions.php?message=Permissions updated successfully');
        exit;
    } catch (Exception $e) {
        header('Location: role_edit.php?id=' . $role_id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
} elseif ($role_id) {
    // Editing existing role
    $permissionManager->requirePermission('role.edit');

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if (empty($name)) {
        header('Location: role_edit.php?id=' . $role_id . '&error=Role name is required');
        exit;
    }

    try {
        $permissionManager->updateRole($role_id, $name, $description, $status);
        header('Location: roles_permissions.php?message=Role updated successfully');
        exit;
    } catch (Exception $e) {
        header('Location: role_edit.php?id=' . $role_id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Creating new role
    $permissionManager->requirePermission('role.create');

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($name)) {
        header('Location: role_edit.php?error=Role name is required');
        exit;
    }

    try {
        $new_role_id = $permissionManager->createRole($name, $description);
        header('Location: role_edit.php?id=' . $new_role_id . '&message=Role created. Now assign permissions.');
        exit;
    } catch (Exception $e) {
        header('Location: role_edit.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
