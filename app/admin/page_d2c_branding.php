<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo      = Database::getInstance();
$hero     = $pdo->query("SELECT * FROM d2c_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cta      = $pdo->query("SELECT * FROM d2c_cta WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$tags     = $pdo->query("SELECT * FROM d2c_intro_tags ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$chals    = $pdo->query("SELECT * FROM d2c_challenges ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$pillars  = $pdo->query("SELECT * FROM d2c_pillars ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$sols     = $pdo->query("SELECT * FROM d2c_solutions ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps    = $pdo->query("SELECT * FROM d2c_steps ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$metrics  = $pdo->query("SELECT * FROM d2c_metrics ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyFeats   = $pdo->query("SELECT * FROM d2c_why_features ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$d2cSecHdrs = $pdo->query("SELECT * FROM d2c_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage D2C Branding Page';
include __DIR__ . '/../views/admin_header.php';

function d2cBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function d2cTable(array $rows, string $tbl, array $cols): string {
    $head = '<thead><tr>';
    foreach ($cols as $c) $head .= "<th>$c</th>";
    $head .= '<th>Active</th><th>Actions</th></tr></thead>';
    $body = '<tbody>';
    foreach ($rows as $r) {
        $enc = htmlspecialchars(json_encode($r), ENT_QUOTES);
        $body .= "<tr>";
        foreach ($cols as $key => $label) {
            $val = htmlspecialchars($r[$key] ?? '');
            $body .= "<td>" . (mb_strlen($val) > 60 ? mb_substr($val,0,60).'…' : $val) . "</td>";
        }
        $body .= "<td>" . d2cBadge((int)($r['is_active'] ?? 1)) . "</td>";
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='d2cEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='d2c_branding_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2" id="d2cTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-tags">Intro Tags</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-chals">Challenges</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pillars">Pillars</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sols">Solutions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-metrics">Metrics</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Us</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-image me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="d2c_branding_singles_save.php" class="row g-3">
            <input type="hidden" name="section" value="hero">
            <div class="col-12"><label class="form-label">Badge Text</label>
                <input type="text" name="badge_text" class="form-control" value="<?= htmlspecialchars($hero['badge_text'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Headline Main</label>
                <input type="text" name="headline_main" class="form-control" value="<?= htmlspecialchars($hero['headline_main'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Headline Accent (blue)</label>
                <input type="text" name="headline_accent" class="form-control" value="<?= htmlspecialchars($hero['headline_accent'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Sub Description</label>
                <textarea name="sub_description" class="form-control" rows="3"><?= htmlspecialchars($hero['sub_description'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($hero['btn1_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($hero['btn1_url'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($hero['btn2_label'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($hero['btn2_url'] ?? '') ?>"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
        </form></div></div>
    </div>

    <!-- INTRO TAGS -->
    <div class="tab-pane fade" id="tab-tags">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Intro Tags</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_intro_tags')" data-bs-toggle="modal" data-bs-target="#tagModal">
                <i class="fas fa-plus me-1"></i>Add Tag</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($tags, 'd2c_intro_tags', ['sort_order'=>'Sort','tag_name'=>'Tag Name']) ?>
        </div></div>
    </div>

    <!-- CHALLENGES -->
    <div class="tab-pane fade" id="tab-chals">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Challenges</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_challenges')" data-bs-toggle="modal" data-bs-target="#chalModal">
                <i class="fas fa-plus me-1"></i>Add Challenge</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($chals, 'd2c_challenges', ['sort_order'=>'Sort','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- PILLARS -->
    <div class="tab-pane fade" id="tab-pillars">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Pillars</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_pillars')" data-bs-toggle="modal" data-bs-target="#pillarModal">
                <i class="fas fa-plus me-1"></i>Add Pillar</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($pillars, 'd2c_pillars', ['sort_order'=>'Sort','number'=>'No.','name'=>'Name','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- SOLUTIONS -->
    <div class="tab-pane fade" id="tab-sols">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Solutions</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_solutions')" data-bs-toggle="modal" data-bs-target="#solModal">
                <i class="fas fa-plus me-1"></i>Add Solution</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($sols, 'd2c_solutions', ['sort_order'=>'Sort','name'=>'Name','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- STEPS -->
    <div class="tab-pane fade" id="tab-steps">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Process Steps</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_steps')" data-bs-toggle="modal" data-bs-target="#stepModal">
                <i class="fas fa-plus me-1"></i>Add Step</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($steps, 'd2c_steps', ['sort_order'=>'Sort','step_number'=>'Step #','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- METRICS -->
    <div class="tab-pane fade" id="tab-metrics">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Metrics</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_metrics')" data-bs-toggle="modal" data-bs-target="#metricModal">
                <i class="fas fa-plus me-1"></i>Add Metric</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($metrics, 'd2c_metrics', ['sort_order'=>'Sort','value'=>'Value','unit'=>'Unit','label'=>'Label','bar_pct'=>'Bar%']) ?>
        </div></div>
    </div>

    <!-- WHY FEATURES -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span>Why Us Features</span>
            <button class="btn btn-primary btn-sm" onclick="d2cReset('d2c_why_features')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Feature</button>
        </div>
        <div class="card-body p-0">
            <?= d2cTable($whyFeats, 'd2c_why_features', ['sort_order'=>'Sort','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($d2cSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="d2c_branding_singles_save.php">
                <input type="hidden" name="section" value="d2c_section_headers">
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

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header">CTA Section</div>
        <div class="card-body">
        <form method="post" action="d2c_branding_singles_save.php" class="row g-3">
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

<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="tagModalTitle">Tag</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="tag_id">
            <input type="hidden" name="table_name" value="d2c_intro_tags">
            <div class="col-md-3"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="tag_sort" class="form-control" value="0"></div>
            <div class="col-md-9"><label class="form-label">Tag Name</label>
                <input type="text" name="tag_name" id="tag_name" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="tag_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Challenge Modal -->
<div class="modal fade" id="chalModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="chalModalTitle">Challenge</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="chal_id">
            <input type="hidden" name="table_name" value="d2c_challenges">
            <div class="col-md-3"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="chal_sort" class="form-control" value="0"></div>
            <div class="col-md-9"><label class="form-label">Title</label>
                <input type="text" name="title" id="chal_title" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" id="chal_desc" class="form-control" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="chal_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Pillar Modal -->
<div class="modal fade" id="pillarModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="pillarModalTitle">Pillar</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="pillar_id">
            <input type="hidden" name="table_name" value="d2c_pillars">
            <div class="col-md-2"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="pillar_sort" class="form-control" value="0"></div>
            <div class="col-md-2"><label class="form-label">Number</label>
                <input type="text" name="number" id="pillar_num" class="form-control" placeholder="01"></div>
            <div class="col-md-8"><label class="form-label">Name</label>
                <input type="text" name="name" id="pillar_name" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Text</label>
                <textarea name="text" id="pillar_text" class="form-control" rows="4"></textarea></div>
            <div class="col-12"><label class="form-label">Dots (JSON array, e.g. ["Brand Purpose","Audience"])</label>
                <textarea name="dots_json" id="pillar_dots" class="form-control" rows="2">[]</textarea></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="pillar_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Solution Modal -->
<div class="modal fade" id="solModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="solModalTitle">Solution</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="sol_id">
            <input type="hidden" name="table_name" value="d2c_solutions">
            <div class="col-md-3"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="sol_sort" class="form-control" value="0"></div>
            <div class="col-md-9"><label class="form-label">Name</label>
                <input type="text" name="name" id="sol_name" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" id="sol_desc" class="form-control" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="sol_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Step Modal -->
<div class="modal fade" id="stepModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="stepModalTitle">Process Step</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="step_id">
            <input type="hidden" name="table_name" value="d2c_steps">
            <div class="col-md-2"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="step_sort" class="form-control" value="0"></div>
            <div class="col-md-2"><label class="form-label">Step #</label>
                <input type="text" name="step_number" id="step_num" class="form-control" placeholder="01"></div>
            <div class="col-md-8"><label class="form-label">Title</label>
                <input type="text" name="title" id="step_title" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" id="step_desc" class="form-control" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="step_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Metric Modal -->
<div class="modal fade" id="metricModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="metricModalTitle">Metric</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="metric_id">
            <input type="hidden" name="table_name" value="d2c_metrics">
            <div class="col-md-2"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="metric_sort" class="form-control" value="0"></div>
            <div class="col-md-3"><label class="form-label">Value</label>
                <input type="text" name="value" id="metric_val" class="form-control" placeholder="95" required></div>
            <div class="col-md-2"><label class="form-label">Unit</label>
                <input type="text" name="unit" id="metric_unit" class="form-control" placeholder="%"></div>
            <div class="col-md-5"><label class="form-label">Label</label>
                <input type="text" name="label" id="metric_label" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Bar %</label>
                <input type="number" name="bar_pct" id="metric_bar" class="form-control" value="100" min="0" max="100"></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="metric_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<!-- Why Feature Modal -->
<div class="modal fade" id="whyModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="whyModalTitle">Why Feature</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="post" action="d2c_branding_list_save.php">
        <div class="modal-body row g-3">
            <input type="hidden" name="id" id="why_id">
            <input type="hidden" name="table_name" value="d2c_why_features">
            <div class="col-md-3"><label class="form-label">Sort</label>
                <input type="number" name="sort_order" id="why_sort" class="form-control" value="0"></div>
            <div class="col-md-9"><label class="form-label">Title</label>
                <input type="text" name="title" id="why_title" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" id="why_desc" class="form-control" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Active</label>
                <select name="is_active" id="why_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div></div></div>

<script>
function d2cReset(tbl) { /* called on Add — id cleared automatically since modal inputs are reset by each modal */ }

function d2cEdit(r, tbl) {
    if (tbl === 'd2c_intro_tags') {
        document.getElementById('tag_id').value = r.id;
        document.getElementById('tag_sort').value = r.sort_order;
        document.getElementById('tag_name').value = r.tag_name;
        document.getElementById('tag_active').value = r.is_active;
        document.getElementById('tagModalTitle').textContent = 'Edit Tag';
        new bootstrap.Modal(document.getElementById('tagModal')).show();
    } else if (tbl === 'd2c_challenges') {
        document.getElementById('chal_id').value = r.id;
        document.getElementById('chal_sort').value = r.sort_order;
        document.getElementById('chal_title').value = r.title;
        document.getElementById('chal_desc').value = r.description || '';
        document.getElementById('chal_active').value = r.is_active;
        document.getElementById('chalModalTitle').textContent = 'Edit Challenge';
        new bootstrap.Modal(document.getElementById('chalModal')).show();
    } else if (tbl === 'd2c_pillars') {
        document.getElementById('pillar_id').value = r.id;
        document.getElementById('pillar_sort').value = r.sort_order;
        document.getElementById('pillar_num').value = r.number || '';
        document.getElementById('pillar_name').value = r.name;
        document.getElementById('pillar_text').value = r.text || '';
        document.getElementById('pillar_dots').value = r.dots_json || '[]';
        document.getElementById('pillar_active').value = r.is_active;
        document.getElementById('pillarModalTitle').textContent = 'Edit Pillar';
        new bootstrap.Modal(document.getElementById('pillarModal')).show();
    } else if (tbl === 'd2c_solutions') {
        document.getElementById('sol_id').value = r.id;
        document.getElementById('sol_sort').value = r.sort_order;
        document.getElementById('sol_name').value = r.name;
        document.getElementById('sol_desc').value = r.description || '';
        document.getElementById('sol_active').value = r.is_active;
        document.getElementById('solModalTitle').textContent = 'Edit Solution';
        new bootstrap.Modal(document.getElementById('solModal')).show();
    } else if (tbl === 'd2c_steps') {
        document.getElementById('step_id').value = r.id;
        document.getElementById('step_sort').value = r.sort_order;
        document.getElementById('step_num').value = r.step_number || '';
        document.getElementById('step_title').value = r.title;
        document.getElementById('step_desc').value = r.description || '';
        document.getElementById('step_active').value = r.is_active;
        document.getElementById('stepModalTitle').textContent = 'Edit Step';
        new bootstrap.Modal(document.getElementById('stepModal')).show();
    } else if (tbl === 'd2c_metrics') {
        document.getElementById('metric_id').value = r.id;
        document.getElementById('metric_sort').value = r.sort_order;
        document.getElementById('metric_val').value = r.value || '';
        document.getElementById('metric_unit').value = r.unit || '';
        document.getElementById('metric_label').value = r.label || '';
        document.getElementById('metric_bar').value = r.bar_pct || 100;
        document.getElementById('metric_active').value = r.is_active;
        document.getElementById('metricModalTitle').textContent = 'Edit Metric';
        new bootstrap.Modal(document.getElementById('metricModal')).show();
    } else if (tbl === 'd2c_why_features') {
        document.getElementById('why_id').value = r.id;
        document.getElementById('why_sort').value = r.sort_order;
        document.getElementById('why_title').value = r.title;
        document.getElementById('why_desc').value = r.description || '';
        document.getElementById('why_active').value = r.is_active;
        document.getElementById('whyModalTitle').textContent = 'Edit Feature';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    }
}

// Clear id on Add button clicks
document.querySelectorAll('[data-bs-target]').forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.bsTarget;
        const modal = document.querySelector(target);
        if (!modal) return;
        const idField = modal.querySelector('input[name="id"]');
        if (idField && !btn.getAttribute('onclick')?.includes('d2cEdit')) idField.value = '';
    });
});
</script>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
