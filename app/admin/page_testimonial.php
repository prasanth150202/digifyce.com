<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$items = $pdo->query("SELECT * FROM testimonial_items ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$saved = isset($_GET['saved']); $deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Testimonials';
include __DIR__ . '/../views/admin_header.php';
?>
<?php if ($saved): ?><div class="alert alert-success alert-dismissible fade show mx-3 mt-2">Saved. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-warning alert-dismissible fade show mx-3 mt-2">Deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="p-3">
<div class="card border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-star me-2"></i>Testimonials</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tModal" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Add Testimonial
        </button>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Client</th><th>Story Label</th><th>Video</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($items as $t): ?>
            <tr>
                <td><?= (int)$t['sort_order'] ?></td>
                <td><strong><?= htmlspecialchars($t['client_name']) ?></strong></td>
                <td><?= htmlspecialchars($t['story_label']) ?></td>
                <td><small class="text-muted"><?= htmlspecialchars(basename($t['video_path'])) ?></small></td>
                <td><?= $t['is_active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>' ?></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editItem(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)">Edit</button>
                    <form method="post" action="testimonial_items_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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

<div class="modal fade" id="tModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="tModalTitle">Add Testimonial</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="testimonial_items_save.php">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id" id="t_id">
                    <div class="col-md-3"><label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="t_sort_order" class="form-control" value="0"></div>
                    <div class="col-md-7"><label class="form-label">Client Name</label>
                        <input type="text" name="client_name" id="t_client_name" class="form-control" required></div>
                    <div class="col-md-2"><label class="form-label">Active</label>
                        <select name="is_active" id="t_is_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
                    <div class="col-12"><label class="form-label">Quote</label>
                        <textarea name="quote" id="t_quote" class="form-control" rows="3"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Story Label</label>
                        <input type="text" name="story_label" id="t_story_label" class="form-control" placeholder="Client Story 01"></div>
                    <div class="col-md-6"><label class="form-label">Video Path</label>
                        <input type="text" name="video_path" id="t_video_path" class="form-control" placeholder="public/assets/testimonials/videos/Name.mp4"></div>
                    <div class="col-md-6"><label class="form-label">Thumbnail Path</label>
                        <input type="text" name="thumbnail_path" id="t_thumbnail_path" class="form-control" placeholder="public/assets/testimonials/thumbnails/Name.jpg"></div>
                    <div class="col-md-6"><label class="form-label">Logo Path</label>
                        <input type="text" name="logo_path" id="t_logo_path" class="form-control" placeholder="public/assets/cl_logos/Name.png"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function resetForm() {
    document.getElementById('tModalTitle').textContent = 'Add Testimonial';
    ['id','sort_order','client_name','quote','story_label','video_path','thumbnail_path','logo_path'].forEach(f => {
        const el = document.getElementById('t_'+f); if (el) el.value = '';
    });
    document.getElementById('t_is_active').value = '1';
}
function editItem(t) {
    document.getElementById('tModalTitle').textContent = 'Edit Testimonial';
    document.getElementById('t_id').value = t.id;
    document.getElementById('t_sort_order').value = t.sort_order;
    document.getElementById('t_client_name').value = t.client_name;
    document.getElementById('t_is_active').value = t.is_active;
    document.getElementById('t_quote').value = t.quote || '';
    document.getElementById('t_story_label').value = t.story_label || '';
    document.getElementById('t_video_path').value = t.video_path || '';
    document.getElementById('t_thumbnail_path').value = t.thumbnail_path || '';
    document.getElementById('t_logo_path').value = t.logo_path || '';
    new bootstrap.Modal(document.getElementById('tModal')).show();
}
</script>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
