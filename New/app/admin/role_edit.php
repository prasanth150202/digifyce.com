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
$permissionManager->requirePermission('role.create');

$role_id = $_GET['id'] ?? null;
$role = null;
$is_edit = false;

if ($role_id) {
    $permissionManager->requirePermission('role.edit');
    $role = $permissionManager->getRoleWithPermissions($role_id);
    if (!$role) {
        die('Role not found');
    }
    $is_edit = true;
}

// Get all permissions grouped by module
$all_permissions = $permissionManager->getAllPermissions();
$permissions_by_module = [];
foreach ($all_permissions as $perm) {
    if (!isset($permissions_by_module[$perm['module']])) {
        $permissions_by_module[$perm['module']] = [];
    }
    $permissions_by_module[$perm['module']][] = $perm;
}

// Get selected permission IDs
$selected_permission_ids = [];
if ($is_edit && isset($role['permissions'])) {
    foreach ($role['permissions'] as $perm) {
        $selected_permission_ids[] = $perm['id'];
    }
}

$pageTitle = $is_edit ? 'Edit Role' : 'Create New Role';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i><?= $is_edit ? 'Edit Role' : 'Create New Role' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="role_save.php" method="post">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= $is_edit ? htmlspecialchars($role['name']) : '' ?>" 
                               required maxlength="64">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= $is_edit ? htmlspecialchars($role['description'] ?? '') : '' ?></textarea>
                    </div>

                    <?php if ($is_edit): ?>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $role['status'] === 'active' ? 'selected' : '' ?>>
                                    Active
                                </option>
                                <option value="inactive" <?= $role['status'] === 'inactive' ? 'selected' : '' ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= $is_edit ? 'Update Role' : 'Create Role' ?>
                        </button>
                        <a href="roles_permissions.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-lock me-2"></i>Permissions
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="role_save.php" method="post">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                        <input type="hidden" name="save_permissions" value="1">
                    <?php else: ?>
                        <p class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-2"></i>Create the role first, then assign permissions.
                        </p>
                    <?php endif; ?>

                    <?php if ($is_edit): ?>
                        <div class="row">
                            <?php foreach ($permissions_by_module as $module => $perms): ?>
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-primary mb-3"><?= htmlspecialchars($module) ?></h6>
                                    <?php foreach ($perms as $perm): ?>
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" 
                                                   class="form-check-input" id="perm<?= $perm['id'] ?>"
                                                   <?= in_array($perm['id'], $selected_permission_ids) ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="perm<?= $perm['id'] ?>">
                                                <strong><?= htmlspecialchars($perm['action']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($perm['description']) ?></small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Permissions
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
