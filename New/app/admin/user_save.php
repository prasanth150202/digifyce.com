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

$user_id = $_POST['user_id'] ?? null;

if ($user_id) {
    // Editing existing user
    $permissionManager->requirePermission('user.edit');
    
    $email = $_POST['email'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($role_id)) {
        header('Location: user_edit.php?id=' . $user_id . '&error=Missing required fields');
        exit;
    }

    try {
        // Update user
        $permissionManager->updateUser($user_id, $email, $full_name, $role_id, $status);

        // Update password if provided
        if (!empty($password)) {
            if (strlen($password) < 8) {
                header('Location: user_edit.php?id=' . $user_id . '&error=Password must be at least 8 characters');
                exit;
            }
            $permissionManager->updatePassword($user_id, $password);
        }

        // Clear permission cache
        PermissionManager::clearCache();

        header('Location: users.php?message=User updated successfully');
        exit;
    } catch (Exception $e) {
        header('Location: user_edit.php?id=' . $user_id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Creating new user
    $permissionManager->requirePermission('user.create');
    
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($role_id)) {
        header('Location: user_edit.php?error=Missing required fields');
        exit;
    }

    if (strlen($password) < 8) {
        header('Location: user_edit.php?error=Password must be at least 8 characters');
        exit;
    }

    try {
        $permissionManager->createUser($username, $email, $full_name, $password, $role_id);
        header('Location: users.php?message=User created successfully');
        exit;
    } catch (Exception $e) {
        header('Location: user_edit.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
