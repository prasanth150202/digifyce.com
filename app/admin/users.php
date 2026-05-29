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
$permissionManager->requirePermission('user.view');

$pageTitle = 'User Management';
include __DIR__ . '/../views/admin_header.php';

// Get all users
$users = $permissionManager->getAllUsers();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users me-2"></i>User Management</h1>
    <?php if ($permissionManager->hasPermission('user.create')): ?>
        <a href="user_edit.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    <?php endif; ?>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars(urldecode($_GET['message'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['full_name'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($user['role_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                            </td>
                            <td>
                                <?= date('M d, Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($permissionManager->hasPermission('user.edit')): ?>
                                        <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($permissionManager->hasPermission('user.delete') && $user['id'] != $_SESSION['user_id']): ?>
                                        <a href="user_delete.php?id=<?= $user['id'] ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No users found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
