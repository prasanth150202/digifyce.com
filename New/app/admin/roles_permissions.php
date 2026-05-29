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
$permissionManager->requirePermission('role.view');

$pageTitle = 'Roles & Permissions';
include __DIR__ . '/../views/admin_header.php';

$roles = $permissionManager->getAllRoles();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-shield-alt me-2"></i>Roles & Permissions</h1>
    <?php if ($permissionManager->hasPermission('role.create')): ?>
        <a href="role_edit.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Role
        </a>
    <?php endif; ?>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars(urldecode($_GET['message'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars(urldecode($_GET['error'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($roles as $role): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= htmlspecialchars($role['name']) ?></h5>
                    <span class="badge bg-light text-dark"><?= $role['status'] ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted">
                        <?= htmlspecialchars($role['description'] ?? 'No description') ?>
                    </p>
                    <p class="small text-muted">
                        <i class="fas fa-calendar me-2"></i>
                        Created: <?= date('M d, Y', strtotime($role['created_at'])) ?>
                    </p>

                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" 
                                data-bs-target="#permissionsModal<?= $role['id'] ?>">
                            <i class="fas fa-list me-1"></i>View Permissions
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-light border-top">
                    <div class="btn-group btn-group-sm w-100">
                        <?php if ($permissionManager->hasPermission('role.edit')): ?>
                            <a href="role_edit.php?id=<?= $role['id'] ?>" class="btn btn-outline-primary flex-grow-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        <?php endif; ?>
                        <?php if ($permissionManager->hasPermission('role.delete')): ?>
                            <a href="role_delete.php?id=<?= $role['id'] ?>" class="btn btn-outline-danger flex-grow-1" 
                               onclick="return confirm('Delete this role?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Permissions Modal -->
            <div class="modal fade" id="permissionsModal<?= $role['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($role['name']) ?> - Permissions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php 
                            $roleWithPerms = $permissionManager->getRoleWithPermissions($role['id']);
                            if (!empty($roleWithPerms['permissions'])):
                            ?>
                                <div class="row">
                                    <?php 
                                    $current_module = '';
                                    foreach ($roleWithPerms['permissions'] as $perm):
                                        if ($perm['module'] !== $current_module):
                                            if ($current_module !== ''): echo '</div></div>'; endif;
                                            $current_module = $perm['module'];
                                            echo '<div class="col-md-6"><div class="mb-3"><h6 class="text-uppercase">' . htmlspecialchars($perm['module']) . '</h6>';
                                        endif;
                                    ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" disabled checked id="perm<?= $perm['id'] ?>">
                                            <label class="form-check-label small" for="perm<?= $perm['id'] ?>">
                                                <?= htmlspecialchars($perm['action']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    </div></div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No permissions assigned</p>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
