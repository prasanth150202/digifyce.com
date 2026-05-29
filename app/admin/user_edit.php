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
$permissionManager->requirePermission('user.create');

$user_id = $_GET['id'] ?? null;
$user = null;
$is_edit = false;

if ($user_id) {
    $permissionManager->requirePermission('user.edit');
    $user = $permissionManager->getUserById($user_id);
    if (!$user) {
        die('User not found');
    }
    $is_edit = true;
}

$roles = $permissionManager->getAllRoles();
$pageTitle = $is_edit ? 'Edit User' : 'Add New User';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i><?= $is_edit ? 'Edit User' : 'Create New User' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="user_save.php" method="post">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= $is_edit ? htmlspecialchars($user['username']) : '' ?>" 
                               <?= $is_edit ? 'readonly' : 'required' ?>>
                        <small class="form-text text-muted">
                            <?= $is_edit ? 'Username cannot be changed' : 'Must be unique' ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $is_edit ? htmlspecialchars($user['email']) : '' ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= $is_edit ? htmlspecialchars($user['full_name'] ?? '') : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" 
                               <?= !$is_edit ? 'required' : '' ?>
                               placeholder="<?= $is_edit ? 'Leave blank to keep current password' : 'Enter password' ?>">
                        <small class="form-text text-muted">
                            <?= $is_edit ? 'Only fill to change password' : 'Minimum 8 characters' ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Select Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" 
                                        <?= ($is_edit && $user['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($is_edit): ?>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>
                                    Active
                                </option>
                                <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= $is_edit ? 'Update User' : 'Create User' ?>
                        </button>
                        <a href="users.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($is_edit): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">
                            <?= date('M d, Y g:i A', strtotime($user['created_at'])) ?>
                        </dd>
                        
                        <dt class="col-sm-4">Updated:</dt>
                        <dd class="col-sm-8">
                            <?= date('M d, Y g:i A', strtotime($user['updated_at'])) ?>
                        </dd>
                        
                        <dt class="col-sm-4">Last Login:</dt>
                        <dd class="col-sm-8">
                            <?= $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                        </dd>
                        
                        <dt class="col-sm-4">Current Role:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($user['role_name']) ?>
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
