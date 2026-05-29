<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$hero     = $pdo->query("SELECT * FROM products_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$crm      = $pdo->query("SELECT * FROM products_crm_section WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$crmF     = $pdo->query("SELECT * FROM products_crm_features ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$zing     = $pdo->query("SELECT * FROM products_zingbot_section WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$zingF    = $pdo->query("SELECT * FROM products_zingbot_features ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cta      = $pdo->query("SELECT * FROM products_cta WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$saved = isset($_GET['saved']); $deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Products Page';
include __DIR__ . '/../views/admin_header.php';
?>
<?php if ($saved): ?><div class="alert alert-success alert-dismissible fade show mx-3 mt-2">Saved. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-warning alert-dismissible fade show mx-3 mt-2">Deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<ul class="nav nav-tabs px-3 pt-2">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-crm">CRM Section</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-zing">Zingbot Section</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-image me-2"></i>Hero</div>
        <div class="card-body">
        <form method="post" action="products_singles_save.php" class="row g-3">
            <input type="hidden" name="section" value="hero">
            <div class="col-md-6"><label class="form-label">Headline Main</label>
                <input type="text" name="headline_main" class="form-control" value="<?= htmlspecialchars($hero['headline_main'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Headline Highlight (blue)</label>
                <input type="text" name="headline_highlight" class="form-control" value="<?= htmlspecialchars($hero['headline_highlight'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($hero['description'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">CRM Button Label</label>
                <input type="text" name="crm_btn_label" class="form-control" value="<?= htmlspecialchars($hero['crm_btn_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Zingbot Button Label</label>
                <input type="text" name="zingbot_btn_label" class="form-control" value="<?= htmlspecialchars($hero['zingbot_btn_label'] ?? '') ?>"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
        </form></div></div>
    </div>

    <!-- CRM SECTION -->
    <div class="tab-pane fade" id="tab-crm">
        <div class="card border-0 mb-3"><div class="card-header">CRM Section Info</div>
        <div class="card-body">
        <form method="post" action="products_singles_save.php" class="row g-3">
            <input type="hidden" name="section" value="crm">
            <div class="col-md-3"><label class="form-label">Label</label>
                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($crm['label'] ?? '') ?>"></div>
            <div class="col-md-9"><label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($crm['heading'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Sub Description</label>
                <textarea name="sub_desc" class="form-control" rows="2"><?= htmlspecialchars($crm['sub_desc'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Label</label>
                <input type="text" name="cta_label" class="form-control" value="<?= htmlspecialchars($crm['cta_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">CTA URL</label>
                <input type="text" name="cta_url" class="form-control" value="<?= htmlspecialchars($crm['cta_url'] ?? '') ?>"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save CRM Section</button></div>
        </form></div></div>

        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>CRM Features</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#featModal" onclick="resetFeat('crm_features')">
                <i class="fas fa-plus me-1"></i>Add Feature</button>
        </div>
        <div class="card-body p-0"><?= renderFeatTable($crmF, 'products_crm_features') ?></div></div>
    </div>

    <!-- ZINGBOT SECTION -->
    <div class="tab-pane fade" id="tab-zing">
        <div class="card border-0 mb-3"><div class="card-header">Zingbot Section Info</div>
        <div class="card-body">
        <form method="post" action="products_singles_save.php" class="row g-3">
            <input type="hidden" name="section" value="zingbot">
            <div class="col-md-3"><label class="form-label">Label</label>
                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($zing['label'] ?? '') ?>"></div>
            <div class="col-md-9"><label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($zing['heading'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Sub Description</label>
                <textarea name="sub_desc" class="form-control" rows="2"><?= htmlspecialchars($zing['sub_desc'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Label</label>
                <input type="text" name="cta_label" class="form-control" value="<?= htmlspecialchars($zing['cta_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">CTA URL</label>
                <input type="text" name="cta_url" class="form-control" value="<?= htmlspecialchars($zing['cta_url'] ?? '') ?>"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save Zingbot Section</button></div>
        </form></div></div>

        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Zingbot Features</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#featModal" onclick="resetFeat('zingbot_features')">
                <i class="fas fa-plus me-1"></i>Add Feature</button>
        </div>
        <div class="card-body p-0"><?= renderFeatTable($zingF, 'products_zingbot_features') ?></div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header">CTA Section</div>
        <div class="card-body">
        <form method="post" action="products_singles_save.php" class="row g-3">
            <input type="hidden" name="section" value="cta">
            <div class="col-12"><label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($cta['heading'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($cta['description'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">Button Label</label>
                <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($cta['btn_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Button URL</label>
                <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($cta['btn_url'] ?? '') ?>"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
        </form></div></div>
    </div>
</div>

<!-- Feature Modal -->
<div class="modal fade" id="featModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="featModalTitle">Add Feature</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="products_feature_save.php">
            <div class="modal-body row g-3">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="table_name" id="f_table">
                <div class="col-md-3"><label class="form-label">Sort</label>
                    <input type="number" name="sort_order" id="f_sort" class="form-control" value="0"></div>
                <div class="col-md-9"><label class="form-label">Icon Class (FA)</label>
                    <input type="text" name="icon_class" id="f_icon" class="form-control" placeholder="fa-solid fa-address-book"></div>
                <div class="col-12"><label class="form-label">Title</label>
                    <input type="text" name="title" id="f_title" class="form-control" required></div>
                <div class="col-12"><label class="form-label">Description</label>
                    <textarea name="description" id="f_desc" class="form-control" rows="3"></textarea></div>
                <div class="col-md-3"><label class="form-label">Active</label>
                    <select name="is_active" id="f_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>

<?php
function renderFeatTable(array $feats, string $tbl): string {
    $rows = '';
    foreach ($feats as $f) {
        $enc = htmlspecialchars(json_encode($f), ENT_QUOTES);
        $badge = $f['is_active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
        $rows .= "<tr><td>{$f['sort_order']}</td><td>".htmlspecialchars($f['title'])."</td><td><small>".htmlspecialchars($f['icon_class'])."</small></td><td>$badge</td><td>
            <button class='btn btn-sm btn-outline-primary' onclick='editFeat($enc,\"{$tbl}\")'>Edit</button>
            <form method='post' action='products_feature_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
                <input type='hidden' name='id' value='{$f['id']}'><input type='hidden' name='table_name' value='{$tbl}'>
                <button class='btn btn-sm btn-outline-danger'>Delete</button>
            </form></td></tr>";
    }
    return "<table class='table mb-0'><thead><tr><th>#</th><th>Title</th><th>Icon</th><th>Active</th><th>Actions</th></tr></thead><tbody>$rows</tbody></table>";
}
?>

<script>
function resetFeat(tbl) {
    document.getElementById('featModalTitle').textContent = 'Add Feature';
    document.getElementById('f_id').value = '';
    document.getElementById('f_table').value = tbl;
    document.getElementById('f_sort').value = '0';
    document.getElementById('f_icon').value = '';
    document.getElementById('f_title').value = '';
    document.getElementById('f_desc').value = '';
    document.getElementById('f_active').value = '1';
}
function editFeat(f, tbl) {
    document.getElementById('featModalTitle').textContent = 'Edit Feature';
    document.getElementById('f_id').value = f.id;
    document.getElementById('f_table').value = tbl;
    document.getElementById('f_sort').value = f.sort_order;
    document.getElementById('f_icon').value = f.icon_class || '';
    document.getElementById('f_title').value = f.title;
    document.getElementById('f_desc').value = f.description || '';
    document.getElementById('f_active').value = f.is_active;
    new bootstrap.Modal(document.getElementById('featModal')).show();
}
</script>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
