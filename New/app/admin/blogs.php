<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/PermissionManager.php';

$pdo = Database::getInstance();
$permissionManager = new PermissionManager($pdo);

// Check permission
$permissionManager->requirePermission('blog.view');

$blogs = $pdo->query('SELECT b.id, b.title, b.status, b.published_at, a.name as author FROM blogs b LEFT JOIN blog_authors a ON b.author_id=a.id ORDER BY b.created_at DESC')->fetchAll();
?>
<?php
$pageTitle = 'Manage Blogs';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Manage Blogs</h2>
    <?php if ($permissionManager->hasPermission('blog.create')): ?>
        <a href="blog_edit.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>New Blog
        </a>
    <?php endif; ?>
</div>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-file-alt me-2"></i>All Blog Posts
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td><?= htmlspecialchars($blog['title']) ?></td>
                        <td><?= htmlspecialchars($blog['author'] ?? '—') ?></td>
                        <td>
                            <?php
                                $status = htmlspecialchars($blog['status']);
                                $badgeClass = $status === 'published' ? 'badge-success' : ($status === 'scheduled' ? 'badge-warning' : 'badge-danger');
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                        </td>
                        <td><?= $blog['published_at'] ? htmlspecialchars($blog['published_at']) : '—' ?></td>
                        <td class="text-end">
                            <?php if ($permissionManager->hasPermission('blog.edit')): ?>
                                <a href="blog_edit.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($permissionManager->hasPermission('blog.delete')): ?>
                                <a href="blog_delete.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Move to trash?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
