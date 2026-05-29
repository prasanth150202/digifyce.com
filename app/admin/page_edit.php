<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$pageId = $_GET['id'] ?? null;
$page = null;

if ($pageId) {
    $stmt = $pdo->prepare('SELECT id, title, slug, content, meta_title, meta_description FROM pages WHERE id = ?');
    $stmt->execute([$pageId]);
    $page = $stmt->fetch();
    if (!$page) {
        header('Location: pages.php');
        exit;
    }
}

$pageTitle = $page ? 'Edit Page' : 'Create Page';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4"><?= $pageTitle ?></h1>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Page Details</h5>
        </div>
        <div class="card-body">
            <form method="post" action="page_save.php" class="row g-4">
                <?php if ($page): ?>
                    <input type="hidden" name="id" value="<?= $page['id'] ?>">
                <?php endif; ?>
                
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" placeholder="privacy-policy" required>
                    <small class="text-muted">Used in URLs: /pages/your-slug</small>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="15" required><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" placeholder="SEO page title (60 chars max)">
                    <small class="text-muted">Recommended: 50-60 characters</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="2" placeholder="SEO page description (160 chars max)"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                    <small class="text-muted">Recommended: 150-160 characters</small>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Page</button>
                    <a href="pages.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
