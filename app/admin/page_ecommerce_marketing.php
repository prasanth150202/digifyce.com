<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo        = Database::getInstance();
$challenges = $pdo->query("SELECT * FROM ecom_challenges ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$approaches = $pdo->query("SELECT * FROM ecom_approaches ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps      = $pdo->query("SELECT * FROM ecom_steps ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$emHero     = $pdo->query("SELECT * FROM ecom_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$solutions  = $pdo->query("SELECT * FROM ecom_solutions WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyPoints  = $pdo->query("SELECT * FROM ecom_why_points WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$emCta      = $pdo->query("SELECT * FROM ecom_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$emSecHdrs  = $pdo->query("SELECT * FROM ecom_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage E-Commerce Marketing Page';
include __DIR__ . '/../views/admin_header.php';

function emBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function emTable(array $rows, string $tbl, array $cols): string {
    $head = '<thead><tr>';
    foreach ($cols as $label) $head .= "<th>$label</th>";
    $head .= '<th>Active</th><th>Actions</th></tr></thead>';
    $body = '<tbody>';
    foreach ($rows as $r) {
        $enc = htmlspecialchars(json_encode($r), ENT_QUOTES);
        $body .= '<tr>';
        foreach ($cols as $key => $label) {
            $val = htmlspecialchars($r[$key] ?? '');
            $body .= '<td>' . (mb_strlen($val) > 60 ? mb_substr($val,0,60).'…' : $val) . '</td>';
        }
        $body .= '<td>' . emBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='emEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='ecommerce_marketing_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
                <input type='hidden' name='id' value='{$r['id']}'><input type='hidden' name='table_name' value='$tbl'>
                <button class='btn btn-sm btn-outline-danger'>Delete</button>
            </form></td></tr>";
    }
    $body .= '</tbody>';
    return "<table class='table mb-0'>$head$body</table>";
}
?>
<?php if ($saved): ?><div class="alert alert-success alert-dismissible fade show mx-3 mt-2">Saved. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($deleted): ?><div class="alert alert-warning alert-dismissible fade show mx-3 mt-2">Deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<ul class="nav nav-tabs px-3 pt-2" id="emTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-challenges">Challenges</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-approaches">Approaches</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-solutions">Solutions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Points</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="ecommerce_marketing_list_save.php">
            <input type="hidden" name="table_name" value="ecom_hero">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Badge Text</label>
                  <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($emHero['badge']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($emHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (accent)</label>
                  <input type="text" name="h1_line2_accent" class="form-control" value="<?= htmlspecialchars($emHero['h1_line2_accent']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Hero Sub Copy</label>
                  <textarea name="hero_sub" class="form-control" rows="4"><?= htmlspecialchars($emHero['hero_sub']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($emHero['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($emHero['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- CHALLENGES -->
    <div class="tab-pane fade" id="tab-challenges">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-circle me-2"></i>Challenges</span>
            <button class="btn btn-primary btn-sm" onclick="emReset('ecom_challenges')" data-bs-toggle="modal" data-bs-target="#chalModal">
                <i class="fas fa-plus me-1"></i>Add Challenge</button>
        </div>
        <div class="card-body p-0">
            <?= emTable($challenges, 'ecom_challenges', ['sort_order'=>'Sort','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- APPROACHES -->
    <div class="tab-pane fade" id="tab-approaches">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-sitemap me-2"></i>Approaches</span>
            <button class="btn btn-primary btn-sm" onclick="emReset('ecom_approaches')" data-bs-toggle="modal" data-bs-target="#approachModal">
                <i class="fas fa-plus me-1"></i>Add Approach</button>
        </div>
        <div class="card-body p-0">
            <?= emTable($approaches, 'ecom_approaches', ['sort_order'=>'Sort','number'=>'No.','title'=>'Title','tag'=>'Tag','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- STEPS -->
    <div class="tab-pane fade" id="tab-steps">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list-ol me-2"></i>Process Steps</span>
            <button class="btn btn-primary btn-sm" onclick="emReset('ecom_steps')" data-bs-toggle="modal" data-bs-target="#stepModal">
                <i class="fas fa-plus me-1"></i>Add Step</button>
        </div>
        <div class="card-body p-0">
            <?= emTable($steps, 'ecom_steps', ['sort_order'=>'Sort','step_number'=>'Step #','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- SOLUTIONS -->
    <div class="tab-pane fade" id="tab-solutions">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-layer-group me-2"></i>Platform Solutions</span>
            <button class="btn btn-primary btn-sm" onclick="emReset('ecom_solutions')" data-bs-toggle="modal" data-bs-target="#solModal">
                <i class="fas fa-plus me-1"></i>Add Solution</button>
        </div>
        <div class="card-body p-0">
            <?= emTable($solutions, 'ecom_solutions', ['sort_order'=>'Sort','tag_label'=>'Tag','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- WHY POINTS -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Why Choose Digifyce Points</span>
            <button class="btn btn-primary btn-sm" onclick="emReset('ecom_why_points')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Point</button>
        </div>
        <div class="card-body p-0">
            <?= emTable($whyPoints, 'ecom_why_points', ['sort_order'=>'Sort','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="ecommerce_marketing_list_save.php">
            <input type="hidden" name="table_name" value="ecom_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Background Text (watermark)</label>
                  <input type="text" name="bg_text" class="form-control" value="<?= htmlspecialchars($emCta['bg_text']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($emCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($emCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($emCta['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($emCta['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>


    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($emSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="ecommerce_marketing_list_save.php">
                <input type="hidden" name="table_name" value="ecom_section_headers">
                <input type="hidden" name="slug" value="<?= htmlspecialchars($sh['slug']) ?>">
                <div class="row g-2">
                    <div class="col-md-4"><label class="form-label">Eyebrow</label>
                      <input type="text" name="eyebrow" class="form-control" value="<?= htmlspecialchars($sh['eyebrow'] ?? '') ?>"></div>
                    <div class="col-md-8"><label class="form-label">Heading</label>
                      <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($sh['heading'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Sub Text</label>
                      <textarea name="sub_text" class="form-control" rows="2"><?= htmlspecialchars($sh['sub_text'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label">Extra Text</label>
                      <textarea name="extra_text" class="form-control" rows="2"><?= htmlspecialchars($sh['extra_text'] ?? '') ?></textarea></div>
                    <div class="col-md-6"><label class="form-label">Button Label</label>
                      <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($sh['btn_label'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Button URL</label>
                      <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($sh['btn_url'] ?? '') ?>"></div>
                    <div class="col-12"><button type="submit" class="btn btn-sm btn-primary">Save</button></div>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
        </div></div>
    </div>

</div>

<!-- CHALLENGE MODAL -->
<div class="modal fade" id="chalModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Challenge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="ecommerce_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="ecom_challenges">
        <input type="hidden" name="id" id="chal_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="chal_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="chal_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Icon (Material Symbols name, e.g. speed)</label>
          <input type="text" name="icon" id="chal_icon" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="chal_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="chal_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- APPROACH MODAL -->
<div class="modal fade" id="approachModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approach</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="ecommerce_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="ecom_approaches">
        <input type="hidden" name="id" id="ap_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="ap_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="ap_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="ap_number" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Title</label>
          <input type="text" name="title" id="ap_title" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Tag Label</label>
          <input type="text" name="tag" id="ap_tag" class="form-control"></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="ap_desc" class="form-control" rows="4"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- STEP MODAL -->
<div class="modal fade" id="stepModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Process Step</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="ecommerce_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="ecom_steps">
        <input type="hidden" name="id" id="step_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="step_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="step_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Step Number</label>
          <input type="text" name="step_number" id="step_num" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="step_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="step_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SOLUTION MODAL -->
<div class="modal fade" id="solModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Platform Solution</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="ecommerce_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="ecom_solutions">
        <input type="hidden" name="id" id="sol_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="sol_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="sol_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Tag Label (e.g. Shopify)</label>
          <input type="text" name="tag_label" id="sol_tag_label" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Tag Color (blue/purple/pink)</label>
          <input type="text" name="tag_color" id="sol_tag_color" class="form-control" value="blue"></div>
        <div class="col-md-6"><label class="form-label">Title</label>
          <input type="text" name="title" id="sol_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="sol_desc" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Bullets (JSON array, e.g. ["Item A","Item B"])</label>
          <textarea name="bullets_json" id="sol_bullets" class="form-control" rows="3">[]</textarea></div>
        <div class="col-12"><label class="form-label">Background Image Path</label>
          <input type="text" name="bg_image" id="sol_bg_image" class="form-control"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHY POINT MODAL -->
<div class="modal fade" id="whyModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Why Point</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="ecommerce_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="ecom_why_points">
        <input type="hidden" name="id" id="why_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="why_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="why_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Icon (Material Symbols name, e.g. storefront)</label>
          <input type="text" name="icon" id="why_icon" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="why_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="why_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function emReset(tbl) {
    if (tbl === 'ecom_challenges') {
        ['chal_id','chal_icon','chal_title','chal_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('chal_sort').value = 0;
        document.getElementById('chal_active').value = 1;
    } else if (tbl === 'ecom_approaches') {
        ['ap_id','ap_number','ap_title','ap_tag','ap_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('ap_sort').value = 0;
        document.getElementById('ap_active').value = 1;
    } else if (tbl === 'ecom_steps') {
        ['step_id','step_num','step_title','step_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('step_sort').value = 0;
        document.getElementById('step_active').value = 1;
    } else if (tbl === 'ecom_solutions') {
        ['sol_id','sol_tag_label','sol_title','sol_desc','sol_bg_image'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('sol_sort').value = 0;
        document.getElementById('sol_active').value = 1;
        document.getElementById('sol_tag_color').value = 'blue';
        document.getElementById('sol_bullets').value = '[]';
    } else if (tbl === 'ecom_why_points') {
        ['why_id','why_icon','why_title','why_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('why_sort').value = 0;
        document.getElementById('why_active').value = 1;
    }
}

function emEdit(r, tbl) {
    if (tbl === 'ecom_challenges') {
        document.getElementById('chal_id').value = r.id || '';
        document.getElementById('chal_sort').value = r.sort_order || 0;
        document.getElementById('chal_active').value = r.is_active ?? 1;
        document.getElementById('chal_icon').value = r.icon || '';
        document.getElementById('chal_title').value = r.title || '';
        document.getElementById('chal_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('chalModal')).show();
    } else if (tbl === 'ecom_approaches') {
        document.getElementById('ap_id').value = r.id || '';
        document.getElementById('ap_sort').value = r.sort_order || 0;
        document.getElementById('ap_active').value = r.is_active ?? 1;
        document.getElementById('ap_number').value = r.number || '';
        document.getElementById('ap_title').value = r.title || '';
        document.getElementById('ap_tag').value = r.tag || '';
        document.getElementById('ap_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('approachModal')).show();
    } else if (tbl === 'ecom_steps') {
        document.getElementById('step_id').value = r.id || '';
        document.getElementById('step_sort').value = r.sort_order || 0;
        document.getElementById('step_active').value = r.is_active ?? 1;
        document.getElementById('step_num').value = r.step_number || '';
        document.getElementById('step_title').value = r.title || '';
        document.getElementById('step_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('stepModal')).show();
    } else if (tbl === 'ecom_solutions') {
        document.getElementById('sol_id').value = r.id || '';
        document.getElementById('sol_sort').value = r.sort_order || 0;
        document.getElementById('sol_active').value = r.is_active ?? 1;
        document.getElementById('sol_tag_label').value = r.tag_label || '';
        document.getElementById('sol_tag_color').value = r.tag_color || 'blue';
        document.getElementById('sol_title').value = r.title || '';
        document.getElementById('sol_desc').value = r.description || '';
        document.getElementById('sol_bullets').value = r.bullets_json || '[]';
        document.getElementById('sol_bg_image').value = r.bg_image || '';
        new bootstrap.Modal(document.getElementById('solModal')).show();
    } else if (tbl === 'ecom_why_points') {
        document.getElementById('why_id').value = r.id || '';
        document.getElementById('why_sort').value = r.sort_order || 0;
        document.getElementById('why_active').value = r.is_active ?? 1;
        document.getElementById('why_icon').value = r.icon || '';
        document.getElementById('why_title').value = r.title || '';
        document.getElementById('why_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
