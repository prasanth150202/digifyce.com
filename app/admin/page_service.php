<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$hero = $pdo->query("SELECT * FROM service_hero WHERE id = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$blocks = $pdo->query("SELECT * FROM service_blocks ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$saved = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Services Page';
include __DIR__ . '/../views/admin_header.php';
?>

<?php if ($saved): ?>
<div class="alert alert-success alert-dismissible fade show mx-3 mt-2" role="alert">
    Saved successfully. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($deleted): ?>
<div class="alert alert-warning alert-dismissible fade show mx-3 mt-2" role="alert">
    Item deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<ul class="nav nav-tabs px-3 pt-2" id="serviceTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero Section</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-blocks">Service Blocks</a></li>
</ul>

<div class="tab-content p-3">

    <!-- HERO TAB -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0">
            <div class="card-header"><i class="fas fa-image me-2"></i>Hero Section</div>
            <div class="card-body">
                <form method="post" action="service_hero_save.php" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Badge Text</label>
                        <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($hero['badge'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Headline Line 1</label>
                        <input type="text" name="headline_line1" class="form-control" value="<?= htmlspecialchars($hero['headline_line1'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Headline Line 2</label>
                        <input type="text" name="headline_line2" class="form-control" value="<?= htmlspecialchars($hero['headline_line2'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($hero['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stat Label</label>
                        <input type="text" name="stat_label" class="form-control" value="<?= htmlspecialchars($hero['stat_label'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stat Value</label>
                        <input type="text" name="stat_value" class="form-control" value="<?= htmlspecialchars($hero['stat_value'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stat Suffix</label>
                        <input type="text" name="stat_suffix" class="form-control" value="<?= htmlspecialchars($hero['stat_suffix'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Hero</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SERVICE BLOCKS TAB -->
    <div class="tab-pane fade" id="tab-blocks">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-layer-group me-2"></i>Service Blocks</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#blockModal" onclick="resetBlockForm()">
                    <i class="fas fa-plus me-1"></i>Add Block
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Number</th><th>Title</th><th>Tech Badges</th><th>Active</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blocks as $b): ?>
                        <tr>
                            <td><?= (int)$b['sort_order'] ?></td>
                            <td><?= htmlspecialchars($b['section_number']) ?></td>
                            <td><?= htmlspecialchars($b['title_line1'] . ' ' . $b['title_line2']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($b['tech_badges'] ?? '') ?></small></td>
                            <td><?= $b['is_active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>' ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="editBlock(<?= htmlspecialchars(json_encode($b), ENT_QUOTES) ?>)">Edit</button>
                                <form method="post" action="service_blocks_delete.php" class="d-inline"
                                    onsubmit="return confirm('Delete this block?')">
                                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
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

<!-- Block Modal -->
<div class="modal fade" id="blockModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blockModalTitle">Add Service Block</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="service_blocks_save.php">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id" id="block_id">
                    <div class="col-md-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="block_sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Number</label>
                        <input type="text" name="section_number" id="block_section_number" class="form-control" placeholder="01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title Line 1</label>
                        <input type="text" name="title_line1" id="block_title_line1" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title Line 2</label>
                        <input type="text" name="title_line2" id="block_title_line2" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Active</label>
                        <select name="is_active" id="block_is_active" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="block_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tech Badges <small class="text-muted">(comma-separated)</small></label>
                        <input type="text" name="tech_badges" id="block_tech_badges" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Left Column Heading</label>
                        <input type="text" name="left_col_heading" id="block_left_col_heading" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Right Column Heading</label>
                        <input type="text" name="right_col_heading" id="block_right_col_heading" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Left Metrics JSON</label>
                        <textarea name="left_metrics_json" id="block_left_metrics_json" class="form-control font-monospace" rows="4" placeholder='[{"label":"LCP","value":"0.8s","bar_pct":90,"color":"green"}]'></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Right Metrics JSON <small class="text-muted">(empty [] for stat-grid blocks)</small></label>
                        <textarea name="right_metrics_json" id="block_right_metrics_json" class="form-control font-monospace" rows="4" placeholder='[{"label":"Conv. Rate Lift","value":"+28%","bar_pct":70,"color":"blue"}]'></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Case Study Icon</label>
                        <input type="text" name="case_study_icon" id="block_case_study_icon" class="form-control" placeholder="terminal">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Case Study Section Label</label>
                        <input type="text" name="case_study_section_label" id="block_case_study_section_label" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Case Study Quote</label>
                        <input type="text" name="case_study_quote" id="block_case_study_quote" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Case Study Body</label>
                        <textarea name="case_study_body" id="block_case_study_body" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Case Study Image URL</label>
                        <input type="text" name="case_study_image_url" id="block_case_study_image_url" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Case Study Stats JSON</label>
                        <textarea name="case_study_stats_json" id="block_case_study_stats_json" class="form-control font-monospace" rows="2" placeholder='[{"label":"Annual UV","value":"4.2M","color":"white"}]'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetBlockForm() {
    document.getElementById('blockModalTitle').textContent = 'Add Service Block';
    document.getElementById('block_id').value = '';
    ['sort_order','section_number','title_line1','title_line2','description','tech_badges',
     'left_col_heading','left_metrics_json','right_col_heading','right_metrics_json',
     'case_study_icon','case_study_section_label','case_study_quote','case_study_body',
     'case_study_image_url','case_study_stats_json'].forEach(f => {
        const el = document.getElementById('block_' + f);
        if (el) el.value = '';
    });
    document.getElementById('block_is_active').value = '1';
}
function editBlock(b) {
    document.getElementById('blockModalTitle').textContent = 'Edit Service Block';
    document.getElementById('block_id').value = b.id;
    document.getElementById('block_sort_order').value = b.sort_order;
    document.getElementById('block_section_number').value = b.section_number;
    document.getElementById('block_title_line1').value = b.title_line1;
    document.getElementById('block_title_line2').value = b.title_line2 || '';
    document.getElementById('block_is_active').value = b.is_active;
    document.getElementById('block_description').value = b.description || '';
    document.getElementById('block_tech_badges').value = b.tech_badges || '';
    document.getElementById('block_left_col_heading').value = b.left_col_heading || '';
    document.getElementById('block_left_metrics_json').value = b.left_metrics_json || '';
    document.getElementById('block_right_col_heading').value = b.right_col_heading || '';
    document.getElementById('block_right_metrics_json').value = b.right_metrics_json || '';
    document.getElementById('block_case_study_icon').value = b.case_study_icon || '';
    document.getElementById('block_case_study_section_label').value = b.case_study_section_label || '';
    document.getElementById('block_case_study_quote').value = b.case_study_quote || '';
    document.getElementById('block_case_study_body').value = b.case_study_body || '';
    document.getElementById('block_case_study_image_url').value = b.case_study_image_url || '';
    document.getElementById('block_case_study_stats_json').value = b.case_study_stats_json || '';
    const modal = new bootstrap.Modal(document.getElementById('blockModal'));
    modal.show();
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
