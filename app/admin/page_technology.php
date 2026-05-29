<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$hero   = $pdo->query("SELECT * FROM technology_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$panels = $pdo->query("SELECT * FROM technology_panels ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$saved = isset($_GET['saved']); $deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Technology Page';
include __DIR__ . '/../views/admin_header.php';
?>
<?php if ($saved): ?><div class="alert alert-success alert-dismissible fade show mx-3 mt-2">Saved. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-warning alert-dismissible fade show mx-3 mt-2">Deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<ul class="nav nav-tabs px-3 pt-2">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-panels">Tech Panels</a></li>
</ul>
<div class="tab-content p-3">

    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-image me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="technology_hero_save.php" class="row g-3">
            <div class="col-12"><label class="form-label">Badge</label>
                <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($hero['badge'] ?? '') ?>">
            </div>
            <div class="col-12"><label class="form-label">Headline</label>
                <input type="text" name="headline" class="form-control" value="<?= htmlspecialchars($hero['headline'] ?? '') ?>">
            </div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($hero['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
        </form>
        </div></div>
    </div>

    <div class="tab-pane fade" id="tab-panels">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-columns me-2"></i>Technology Panels</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#panelModal" onclick="resetForm()"><i class="fas fa-plus me-1"></i>Add Panel</button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>#</th><th>Number</th><th>Title</th><th>Category</th><th>Active</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($panels as $p): ?>
                    <tr>
                        <td><?= (int)$p['sort_order'] ?></td>
                        <td><?= htmlspecialchars($p['panel_number']) ?></td>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= htmlspecialchars($p['category_label']) ?></td>
                        <td><?= $p['is_active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>' ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editPanel(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">Edit</button>
                            <form method="post" action="technology_panels_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="panelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="panelModalTitle">Add Panel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="technology_panels_save.php">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id" id="p_id">
                    <div class="col-md-3"><label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="p_sort_order" class="form-control" value="0"></div>
                    <div class="col-md-3"><label class="form-label">Panel Number</label>
                        <input type="text" name="panel_number" id="p_panel_number" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Category Label</label>
                        <input type="text" name="category_label" id="p_category_label" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Title</label>
                        <input type="text" name="title" id="p_title" class="form-control" required></div>
                    <div class="col-md-2"><label class="form-label">Active</label>
                        <select name="is_active" id="p_is_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
                    <div class="col-12"><label class="form-label">Description <small class="text-muted">(single paragraph, leave blank if using bullets)</small></label>
                        <textarea name="description" id="p_description" class="form-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">Bullets JSON <small class="text-muted">[{"h4":"Title","p":"Body"},...]</small></label>
                        <textarea name="bullets_json" id="p_bullets_json" class="form-control font-monospace" rows="5"></textarea></div>
                    <div class="col-12"><label class="form-label">Image Paths <small class="text-muted">(comma-separated relative paths)</small></label>
                        <textarea name="image_paths" id="p_image_paths" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Panel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function resetForm() {
    document.getElementById('panelModalTitle').textContent = 'Add Panel';
    ['id','sort_order','panel_number','category_label','title','description','bullets_json','image_paths'].forEach(f => {
        const el = document.getElementById('p_'+f); if (el) el.value = '';
    });
    document.getElementById('p_is_active').value = '1';
}
function editPanel(p) {
    document.getElementById('panelModalTitle').textContent = 'Edit Panel';
    document.getElementById('p_id').value = p.id;
    document.getElementById('p_sort_order').value = p.sort_order;
    document.getElementById('p_panel_number').value = p.panel_number;
    document.getElementById('p_category_label').value = p.category_label;
    document.getElementById('p_title').value = p.title;
    document.getElementById('p_is_active').value = p.is_active;
    document.getElementById('p_description').value = p.description || '';
    document.getElementById('p_bullets_json').value = p.bullets_json || '';
    document.getElementById('p_image_paths').value = p.image_paths || '';
    new bootstrap.Modal(document.getElementById('panelModal')).show();
}
</script>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
