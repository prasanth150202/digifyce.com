<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/PermissionManager.php';

$pdo = Database::getInstance();
$permissionManager = new PermissionManager($pdo);
$permissionManager->requirePermission('blog.view');

// Add sort_order column if missing
try { $pdo->exec("ALTER TABLE blogs ADD COLUMN sort_order INT DEFAULT 0"); } catch (Exception $e) {}

// ── Blog Listing Page SEO ────────────────────────────────────────────────────
// Handle SEO save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_seo_save'])) {
    $seoId  = intval($_POST['seo_id'] ?? 0);
    $seoMt  = trim($_POST['seo_meta_title'] ?? '');
    $seoMd  = trim($_POST['seo_meta_desc']  ?? '');
    if ($seoId) {
        $pdo->prepare("UPDATE page_seo SET meta_title=?, meta_description=? WHERE id=?")
            ->execute([$seoMt ?: null, $seoMd ?: null, $seoId]);
    } else {
        // Upsert: ensure the blog row exists
        $pdo->prepare("INSERT INTO page_seo (page_identifier,page_label,php_file,slug,meta_title,meta_description)
                       VALUES ('blog','Blog Listing','blog_list.php','/blog',?,?)
                       ON DUPLICATE KEY UPDATE meta_title=VALUES(meta_title), meta_description=VALUES(meta_description)")
            ->execute([$seoMt ?: null, $seoMd ?: null]);
    }
    header('Location: blogs.php?seo_saved=1');
    exit;
}

$blogSeo = [];
try {
    $blogSeo = $pdo->query("SELECT * FROM page_seo WHERE page_identifier='blog' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {}

// ── Sort mode ────────────────────────────────────────────────────────────────
$validSorts = ['a-z','z-a','old-new','new-old','manual'];
$sort       = in_array($_GET['sort'] ?? '', $validSorts) ? $_GET['sort'] : 'new-old';

$orderBy = match($sort) {
    'a-z'     => 'b.title ASC',
    'z-a'     => 'b.title DESC',
    'old-new' => 'COALESCE(b.published_at, b.created_at) ASC',
    'manual'  => 'b.sort_order ASC, b.id ASC',
    default   => 'b.updated_at DESC',   // new-old
};

$blogs = $pdo->query("
    SELECT b.id, b.title, b.slug, b.status, b.published_at, b.updated_at,
           b.sort_order, b.meta_title, b.meta_description, a.name AS author
    FROM blogs b
    LEFT JOIN blog_authors a ON b.author_id = a.id
    WHERE b.status != 'trashed'
    ORDER BY $orderBy
")->fetchAll(PDO::FETCH_ASSOC);

// Trashed posts (separate query, always by updated_at)
$trashed = $pdo->query("
    SELECT b.id, b.title, b.slug, b.updated_at, a.name AS author
    FROM blogs b
    LEFT JOIN blog_authors a ON b.author_id = a.id
    WHERE b.status = 'trashed'
    ORDER BY b.updated_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Blogs';
include __DIR__ . '/../views/admin_header.php';
?>

<style>
.sort-btn { border-radius: 6px; padding: 5px 14px; font-size: 13px; font-weight: 600; border: 1.5px solid #dee2e6; background: #fff; color: #495057; cursor: pointer; transition: all .15s; }
.sort-btn:hover  { border-color: #0066ff; color: #0066ff; }
.sort-btn.active { background: #0066ff; border-color: #0066ff; color: #fff; }
.drag-handle { cursor: grab; color: #adb5bd; font-size: 18px; padding: 0 8px; user-select: none; }
.drag-handle:active { cursor: grabbing; }
.sortable-ghost { opacity: .35; background: #e8f0fe !important; }
.seo-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
</style>

<?php if (isset($_GET['seo_saved'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> Blog listing SEO saved.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (isset($_GET['restored'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-undo me-2"></i> Blog post restored to drafts.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (isset($_GET['purged'])): ?>
<div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-trash me-2"></i> Blog post permanently deleted.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Blog Listing Page SEO ───────────────────────────────────────────────── -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#seoCard">
        <i class="fas fa-search me-2 text-primary"></i>
        <span class="fw-semibold">Blog Listing Page – Meta Title &amp; Description</span>
        <?php if (!empty($blogSeo['meta_title'])): ?>
            <span class="badge bg-success ms-2" style="font-size:10px">SEO Set</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark ms-2" style="font-size:10px">Not Set</span>
        <?php endif; ?>
        <i class="fas fa-chevron-down ms-auto text-muted" style="font-size:12px"></i>
    </div>
    <div class="collapse" id="seoCard">
        <div class="card-body">
            <form method="post" action="blogs.php?sort=<?= htmlspecialchars($sort) ?>">
                <input type="hidden" name="_seo_save" value="1">
                <input type="hidden" name="seo_id"   value="<?= intval($blogSeo['id'] ?? 0) ?>">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Meta Title
                            <span id="mtCount" class="text-muted fw-normal ms-1"></span>
                        </label>
                        <input type="text" name="seo_meta_title" id="seoMt" class="form-control"
                               maxlength="120" placeholder="Blog listing page title (50–60 chars)"
                               value="<?= htmlspecialchars($blogSeo['meta_title'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Meta Description
                            <span id="mdCount" class="text-muted fw-normal ms-1"></span>
                        </label>
                        <input type="text" name="seo_meta_desc" id="seoMd" class="form-control"
                               maxlength="320" placeholder="Blog listing page description (150–160 chars)"
                               value="<?= htmlspecialchars($blogSeo['meta_description'] ?? '') ?>">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </div>
                </div>
                <!-- SERP preview -->
                <div class="border rounded p-3 bg-light mt-3">
                    <div class="text-muted small fw-bold mb-1">SERP Preview</div>
                    <div id="srpTitle" class="text-primary fw-semibold" style="font-size:17px"></div>
                    <div id="srpUrl"   class="text-success small"><?= htmlspecialchars(rtrim($_ENV['APP_URL'] ?? '', '/')) ?>/blog</div>
                    <div id="srpDesc"  class="text-muted small mt-1" style="max-width:540px"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Header row ──────────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <!-- Sort buttons -->
    <div class="d-flex gap-1 flex-wrap">
        <?php
        $sortLabels = ['new-old'=>'New → Old','old-new'=>'Old → New','a-z'=>'A → Z','z-a'=>'Z → A','manual'=>'Manual'];
        foreach ($sortLabels as $key => $label):
        ?>
        <a href="blogs.php?sort=<?= $key ?>"
           class="sort-btn text-decoration-none <?= $sort === $key ? 'active' : '' ?>">
            <?php if ($key === 'manual'): ?><i class="fas fa-grip-lines me-1"></i><?php endif; ?>
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="d-flex gap-2 align-items-center">
        <?php if (!empty($trashed)): ?>
        <a href="#trashSection" data-bs-toggle="collapse"
           class="btn btn-sm btn-outline-danger text-decoration-none">
            <i class="fas fa-trash me-1"></i> Trash
            <span class="badge bg-danger ms-1"><?= count($trashed) ?></span>
        </a>
        <?php endif; ?>
        <?php if ($permissionManager->hasPermission('blog.create')): ?>
        <a href="blog_edit.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Blog
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($sort === 'manual'): ?>
<div class="alert alert-info py-2 mb-3 d-flex align-items-center justify-content-between" role="alert">
    <span><i class="fas fa-grip-lines me-2"></i>Drag rows to reorder, then click <strong>Save Order</strong>.</span>
    <button id="saveOrderBtn" class="btn btn-sm btn-primary">
        <i class="fas fa-save me-1"></i> Save Order
    </button>
</div>
<div id="saveToast" class="alert alert-success py-2 mb-3 d-none" role="alert">
    <i class="fas fa-check-circle me-2"></i> Order saved successfully.
</div>
<?php endif; ?>

<!-- ── Blog table ──────────────────────────────────────────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <?php if ($sort === 'manual'): ?><th style="width:40px"></th><?php endif; ?>
                        <th>Title</th>
                        <th style="width:110px">Author</th>
                        <th style="width:100px">Status</th>
                        <th style="width:110px">Published</th>
                        <th style="width:140px">Last Saved</th>
                        <th style="width:60px" class="text-center" title="SEO: meta title + description set">SEO</th>
                        <th style="width:110px" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="blogTableBody">
                    <?php foreach ($blogs as $blog):
                        $hasSeo = !empty($blog['meta_title']) && !empty($blog['meta_description']);
                        $partSeo = !empty($blog['meta_title']) || !empty($blog['meta_description']);
                        $statusClass = match($blog['status']) {
                            'published' => 'badge-success',
                            'scheduled' => 'badge-warning',
                            'trashed'   => 'badge-danger',
                            default     => 'badge-danger',
                        };
                    ?>
                    <tr data-id="<?= $blog['id'] ?>">
                        <?php if ($sort === 'manual'): ?>
                        <td><span class="drag-handle" title="Drag to reorder">⠿</span></td>
                        <?php endif; ?>
                        <td>
                            <div class="fw-semibold small"><?= htmlspecialchars($blog['title'] ?: '(no title)') ?></div>
                            <?php if (!empty($blog['slug'])): ?>
                            <div class="text-muted" style="font-size:11px;font-family:monospace">/<?= htmlspecialchars($blog['slug']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= htmlspecialchars($blog['author'] ?? '—') ?></td>
                        <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($blog['status']) ?></span></td>
                        <td class="small text-muted"><?= $blog['published_at'] ? date('d M Y', strtotime($blog['published_at'])) : '—' ?></td>
                        <td class="small text-muted"><?= $blog['updated_at'] ? date('d M Y, H:i', strtotime($blog['updated_at'])) : '—' ?></td>
                        <td class="text-center">
                            <?php if ($hasSeo): ?>
                                <span class="seo-dot bg-success" title="Meta title &amp; description set"></span>
                            <?php elseif ($partSeo): ?>
                                <span class="seo-dot bg-warning" title="Only one SEO field set"></span>
                            <?php else: ?>
                                <span class="seo-dot bg-danger" title="No SEO fields set"></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="blog_preview?id=<?= $blog['id'] ?>" target="_blank"
                               class="btn btn-sm btn-outline-warning" title="Preview"><i class="fas fa-eye"></i></a>
                            <?php if ($permissionManager->hasPermission('blog.edit')): ?>
                            <a href="blog_edit.php?id=<?= $blog['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <?php endif; ?>
                            <?php if ($permissionManager->hasPermission('blog.delete')): ?>
                            <a href="blog_delete.php?id=<?= $blog['id'] ?>"
                               class="btn btn-sm btn-outline-danger" title="Delete"
                               onclick="return confirm('Move to trash?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($blogs)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">No blog posts yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SortableJS (loaded only for manual mode) -->
<?php if ($sort === 'manual'): ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
var sortable = Sortable.create(document.getElementById('blogTableBody'), {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
});

document.getElementById('saveOrderBtn').addEventListener('click', function () {
    var ids = Array.from(document.querySelectorAll('#blogTableBody tr[data-id]'))
                   .map(function (tr) { return tr.dataset.id; });

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

    fetch('blog_sort_save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids: ids })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        document.getElementById('saveOrderBtn').disabled = false;
        document.getElementById('saveOrderBtn').innerHTML = '<i class="fas fa-save me-1"></i> Save Order';
        if (data.ok) {
            var t = document.getElementById('saveToast');
            t.classList.remove('d-none');
            setTimeout(function () { t.classList.add('d-none'); }, 3000);
        }
    });
});
</script>
<?php endif; ?>

<script>
// SERP preview + character counters for blog listing SEO
(function () {
    var mt = document.getElementById('seoMt');
    var md = document.getElementById('seoMd');
    if (!mt || !md) return;

    function update() {
        document.getElementById('mtCount').textContent = mt.value.length + '/60';
        document.getElementById('mdCount').textContent = md.value.length + '/160';
        document.getElementById('srpTitle').textContent = mt.value || '(no title set)';
        document.getElementById('srpDesc').textContent  = md.value || '(no description set)';
    }
    mt.addEventListener('input', update);
    md.addEventListener('input', update);
    update();
})();
</script>

<?php if (!empty($trashed)): ?>
<!-- ── Trash section ──────────────────────────────────────────────────────── -->
<div class="collapse mt-4" id="trashSection">
    <div class="card border-0 shadow-sm border-danger" style="border-top:3px solid #dc3545 !important">
        <div class="card-header d-flex align-items-center gap-2 bg-danger bg-opacity-10">
            <i class="fas fa-trash text-danger"></i>
            <span class="fw-semibold text-danger">Trash</span>
            <span class="badge bg-danger"><?= count($trashed) ?></span>
            <small class="text-muted ms-2">Permanently delete or restore posts from here.</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th style="width:120px">Author</th>
                        <th style="width:150px">Moved to Trash</th>
                        <th style="width:160px" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($trashed as $t): ?>
                <tr>
                    <td>
                        <span class="fw-semibold small text-muted"><?= htmlspecialchars($t['title'] ?: '(no title)') ?></span>
                        <?php if (!empty($t['slug'])): ?>
                        <div style="font-size:11px;font-family:monospace;color:#aaa">/<?= htmlspecialchars($t['slug']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="small text-muted"><?= htmlspecialchars($t['author'] ?? '—') ?></td>
                    <td class="small text-muted"><?= $t['updated_at'] ? date('d M Y, H:i', strtotime($t['updated_at'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="blog_restore.php?id=<?= $t['id'] ?>"
                           class="btn btn-sm btn-outline-success" title="Restore to drafts">
                            <i class="fas fa-undo me-1"></i> Restore
                        </a>
                        <a href="blog_purge.php?id=<?= $t['id'] ?>"
                           class="btn btn-sm btn-danger ms-1" title="Delete permanently"
                           onclick="return confirm('Permanently delete this post? This cannot be undone.')">
                            <i class="fas fa-times me-1"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
