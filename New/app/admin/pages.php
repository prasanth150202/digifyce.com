<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Pages Management';
include __DIR__ . '/../views/admin_header.php';

$pdo = Database::getInstance();
$pages = $pdo->query('SELECT id, title, slug FROM pages ORDER BY created_at DESC')->fetchAll();
?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4">Pages Management</h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pages</h5>
        </div>
        <div class="card-body">
            <a href="page_edit.php" class="btn btn-success mb-3">+ Create New Page</a>
            
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr>
                            <td><?= htmlspecialchars($page['title']) ?></td>
                            <td><code><?= htmlspecialchars($page['slug']) ?></code></td>
                            <td>
                                <a href="page_edit.php?id=<?= $page['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="page_delete.php?id=<?= $page['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($pages)): ?>
                <p class="text-muted mt-3">No pages yet. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
