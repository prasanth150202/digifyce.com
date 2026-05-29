<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();

// Create tables on first visit
$pdo->exec("
    CREATE TABLE IF NOT EXISTS builder_pages (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        title       VARCHAR(255) NOT NULL,
        slug        VARCHAR(255) NOT NULL,
        meta_title  VARCHAR(255) DEFAULT NULL,
        meta_desc   VARCHAR(500) DEFAULT NULL,
        status      ENUM('draft','published') DEFAULT 'draft',
        created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS builder_sections (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        page_id      INT NOT NULL,
        section_type VARCHAR(64) NOT NULL,
        sort_order   INT DEFAULT 0,
        config       LONGTEXT NOT NULL DEFAULT '{}',
        FOREIGN KEY (page_id) REFERENCES builder_pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$pages = $pdo->query("
    SELECT p.*, COUNT(s.id) AS section_count
    FROM builder_pages p
    LEFT JOIN builder_sections s ON s.page_id = p.id
    GROUP BY p.id
    ORDER BY p.updated_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$appUrl   = rtrim($_ENV['APP_URL'] ?? '', '/');
$pageTitle = 'Page Builder';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Build custom pages by composing pre-designed sections.</p>
    <a href="page_builder_edit" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> New Page
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    Page deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($pages)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-file-alt fa-3x mb-3 d-block opacity-25"></i>
            No pages yet. <a href="page_builder_edit">Create your first page</a>.
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Sections</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th style="width:160px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pages as $p): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($p['title']) ?></td>
                <td><code class="text-primary small"><?= htmlspecialchars($p['slug']) ?></code></td>
                <td><span class="badge bg-secondary"><?= $p['section_count'] ?></span></td>
                <td>
                    <?php if ($p['status'] === 'published'): ?>
                        <span class="badge" style="background:#d4edda;color:#155724">Published</span>
                    <?php else: ?>
                        <span class="badge" style="background:#fff3cd;color:#856404">Draft</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted small"><?= date('d M Y', strtotime($p['updated_at'])) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="page_builder_edit?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <?php if ($p['status'] === 'published'): ?>
                        <a href="<?= $appUrl ?>/<?= htmlspecialchars(ltrim($p['slug'],'/')) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="View">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>')"
                            title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Delete confirm modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0">
                <h6 class="modal-title">Delete Page</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Delete <strong id="deletePageTitle"></strong>? This cannot be undone.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteConfirmBtn" href="#" class="btn btn-danger btn-sm">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('deletePageTitle').textContent = title;
    document.getElementById('deleteConfirmBtn').href = 'page_builder_delete?id=' + id;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
