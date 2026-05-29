<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo        = Database::getInstance();
$pains      = $pdo->query("SELECT * FROM creative_pains ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$pillars    = $pdo->query("SELECT * FROM creative_pillars ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$services   = $pdo->query("SELECT * FROM creative_services ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps      = $pdo->query("SELECT * FROM creative_steps ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$metrics    = $pdo->query("SELECT * FROM creative_metrics ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyCards   = $pdo->query("SELECT * FROM creative_why_cards ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cdHero     = $pdo->query("SELECT * FROM creative_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$statChips  = $pdo->query("SELECT * FROM creative_stat_chips ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cdCta      = $pdo->query("SELECT * FROM creative_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cdSecHdrs  = $pdo->query("SELECT * FROM cd_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
$cdVideos   = $pdo->query("SELECT * FROM cd_video_cards ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Creative Development Page';
include __DIR__ . '/../views/admin_header.php';

function cdBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function cdTable(array $rows, string $tbl, array $cols): string {
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
        $body .= '<td>' . cdBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='cdEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='creative_dev_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2" id="cdTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pains">Pain Points</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pillars">Pillars</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-services">Services</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-metrics">Metrics</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Us Cards</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-stat-chips">Stat Chips</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-video-cards">Video Cards</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="creative_dev_list_save.php">
            <input type="hidden" name="table_name" value="creative_hero">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Kicker (eyebrow text)</label>
                  <input type="text" name="kicker" class="form-control" value="<?= htmlspecialchars($cdHero['kicker']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($cdHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (accent)</label>
                  <input type="text" name="h1_line2_accent" class="form-control" value="<?= htmlspecialchars($cdHero['h1_line2_accent']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Hero Sub Copy</label>
                  <textarea name="hero_sub" class="form-control" rows="4"><?= htmlspecialchars($cdHero['hero_sub']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($cdHero['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($cdHero['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($cdHero['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($cdHero['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- PAIN POINTS -->
    <div class="tab-pane fade" id="tab-pains">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-triangle me-2"></i>Pain Points (Common Challenges)</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_pains')" data-bs-toggle="modal" data-bs-target="#painModal">
                <i class="fas fa-plus me-1"></i>Add Pain Point</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($pains, 'creative_pains', ['sort_order'=>'Sort','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- PILLARS -->
    <div class="tab-pane fade" id="tab-pillars">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-columns me-2"></i>Creative Pillars</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_pillars')" data-bs-toggle="modal" data-bs-target="#pillarModal">
                <i class="fas fa-plus me-1"></i>Add Pillar</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($pillars, 'creative_pillars', ['sort_order'=>'Sort','number'=>'No.','name'=>'Name','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- SERVICES -->
    <div class="tab-pane fade" id="tab-services">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-palette me-2"></i>Creative Services</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_services')" data-bs-toggle="modal" data-bs-target="#serviceModal">
                <i class="fas fa-plus me-1"></i>Add Service</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($services, 'creative_services', ['sort_order'=>'Sort','name'=>'Name','subtitle'=>'Subtitle','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- STEPS -->
    <div class="tab-pane fade" id="tab-steps">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list-ol me-2"></i>Process Steps</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_steps')" data-bs-toggle="modal" data-bs-target="#stepModal">
                <i class="fas fa-plus me-1"></i>Add Step</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($steps, 'creative_steps', ['sort_order'=>'Sort','step_number'=>'Step #','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- METRICS -->
    <div class="tab-pane fade" id="tab-metrics">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-bar me-2"></i>Metrics</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_metrics')" data-bs-toggle="modal" data-bs-target="#metricModal">
                <i class="fas fa-plus me-1"></i>Add Metric</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($metrics, 'creative_metrics', ['sort_order'=>'Sort','value'=>'Value','unit'=>'Unit','label'=>'Label']) ?>
        </div></div>
    </div>

    <!-- WHY US CARDS -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Why Choose Digifyce Cards</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_why_cards')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($whyCards, 'creative_why_cards', ['sort_order'=>'Sort','number'=>'No.','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- STAT CHIPS -->
    <div class="tab-pane fade" id="tab-stat-chips">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-tags me-2"></i>Hero Stat Chips</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('creative_stat_chips')" data-bs-toggle="modal" data-bs-target="#statChipModal">
                <i class="fas fa-plus me-1"></i>Add Stat Chip</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($statChips, 'creative_stat_chips', ['sort_order'=>'Sort','chip_num'=>'Number','chip_lbl'=>'Label']) ?>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="creative_dev_list_save.php">
            <input type="hidden" name="table_name" value="creative_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Background Text (large watermark)</label>
                  <input type="text" name="bg_text" class="form-control" value="<?= htmlspecialchars($cdCta['bg_text']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($cdCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($cdCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($cdCta['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($cdCta['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>


    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($cdSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="creative_dev_list_save.php">
                <input type="hidden" name="table_name" value="cd_section_headers">
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

    <!-- VIDEO CARDS -->
    <div class="tab-pane fade" id="tab-video-cards">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-play-circle me-2"></i>Video Cards</span>
            <button class="btn btn-primary btn-sm" onclick="cdReset('cd_video_cards')" data-bs-toggle="modal" data-bs-target="#videoModal">
                <i class="fas fa-plus me-1"></i>Add Video Card</button>
        </div>
        <div class="card-body p-0">
            <?= cdTable($cdVideos, 'cd_video_cards', ['sort_order'=>'Sort','track'=>'Track','title'=>'Title']) ?>
        </div></div>
    </div>

</div>

<!-- PAIN MODAL -->
<div class="modal fade" id="painModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Pain Point</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_pains">
        <input type="hidden" name="id" id="pain_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="pain_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="pain_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Text</label>
          <textarea name="text" id="pain_text" class="form-control" rows="3" required></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- PILLAR MODAL -->
<div class="modal fade" id="pillarModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Creative Pillar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_pillars">
        <input type="hidden" name="id" id="pillar_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="pillar_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="pillar_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="pillar_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Name</label>
          <input type="text" name="name" id="pillar_name" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="pillar_description" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Tags (JSON array, e.g. ["Tag A","Tag B"])</label>
          <textarea name="tags_json" id="pillar_tags_json" class="form-control" rows="2">[]</textarea></div>
        <div class="col-12"><label class="form-label">SVG Path (d attribute)</label>
          <textarea name="svg_path" id="pillar_svg_path" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SERVICE MODAL -->
<div class="modal fade" id="serviceModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Creative Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_services">
        <input type="hidden" name="id" id="service_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="service_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="service_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Name</label>
          <input type="text" name="name" id="service_name" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Subtitle</label>
          <input type="text" name="subtitle" id="service_subtitle" class="form-control"></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="service_description" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Bullets (JSON array, e.g. ["Item A","Item B"])</label>
          <textarea name="bullets_json" id="service_bullets_json" class="form-control" rows="3">[]</textarea></div>
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
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_steps">
        <input type="hidden" name="id" id="step_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="step_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="step_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Step Number</label>
          <input type="text" name="step_number" id="step_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="step_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="step_description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- METRIC MODAL -->
<div class="modal fade" id="metricModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Metric</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_metrics">
        <input type="hidden" name="id" id="metric_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="metric_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="metric_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Value (e.g. 320)</label>
          <input type="text" name="value" id="metric_value" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Unit (e.g. % or x)</label>
          <input type="text" name="unit" id="metric_unit" class="form-control"></div>
        <div class="col-12"><label class="form-label">Label</label>
          <input type="text" name="label" id="metric_label" class="form-control"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHY CARD MODAL -->
<div class="modal fade" id="whyModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Why Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_why_cards">
        <input type="hidden" name="id" id="why_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="why_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="why_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="why_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="why_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="why_description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- STAT CHIP MODAL -->
<div class="modal fade" id="statChipModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Stat Chip</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="creative_stat_chips">
        <input type="hidden" name="id" id="chip_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="chip_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="chip_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Number (e.g. 8+)</label>
          <input type="text" name="chip_num" id="chip_num" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Label (e.g. Service Types)</label>
          <input type="text" name="chip_lbl" id="chip_lbl" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- VIDEO CARD MODAL -->
<div class="modal fade" id="videoModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Video Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="creative_dev_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="cd_video_cards">
        <input type="hidden" name="id" id="vid_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="vid_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="vid_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Track (1 or 2)</label>
          <input type="number" name="track" id="vid_track" class="form-control" value="1" min="1" max="2"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="vid_title" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function cdReset(tbl) {
    if (tbl === 'creative_pains') {
        document.getElementById('pain_id').value = '';
        document.getElementById('pain_sort').value = 0;
        document.getElementById('pain_active').value = 1;
        document.getElementById('pain_text').value = '';
    } else if (tbl === 'creative_pillars') {
        document.getElementById('pillar_id').value = '';
        document.getElementById('pillar_sort').value = 0;
        document.getElementById('pillar_active').value = 1;
        document.getElementById('pillar_number').value = '';
        document.getElementById('pillar_name').value = '';
        document.getElementById('pillar_description').value = '';
        document.getElementById('pillar_tags_json').value = '[]';
        document.getElementById('pillar_svg_path').value = '';
    } else if (tbl === 'creative_services') {
        document.getElementById('service_id').value = '';
        document.getElementById('service_sort').value = 0;
        document.getElementById('service_active').value = 1;
        document.getElementById('service_name').value = '';
        document.getElementById('service_subtitle').value = '';
        document.getElementById('service_description').value = '';
        document.getElementById('service_bullets_json').value = '[]';
    } else if (tbl === 'creative_steps') {
        document.getElementById('step_id').value = '';
        document.getElementById('step_sort').value = 0;
        document.getElementById('step_active').value = 1;
        document.getElementById('step_number').value = '';
        document.getElementById('step_title').value = '';
        document.getElementById('step_description').value = '';
    } else if (tbl === 'creative_metrics') {
        document.getElementById('metric_id').value = '';
        document.getElementById('metric_sort').value = 0;
        document.getElementById('metric_active').value = 1;
        document.getElementById('metric_value').value = '';
        document.getElementById('metric_unit').value = '';
        document.getElementById('metric_label').value = '';
    } else if (tbl === 'creative_why_cards') {
        document.getElementById('why_id').value = '';
        document.getElementById('why_sort').value = 0;
        document.getElementById('why_active').value = 1;
        document.getElementById('why_number').value = '';
        document.getElementById('why_title').value = '';
        document.getElementById('why_description').value = '';
    } else if (tbl === 'creative_stat_chips') {
        document.getElementById('chip_id').value = '';
        document.getElementById('chip_sort').value = 0;
        document.getElementById('chip_active').value = 1;
        document.getElementById('chip_num').value = '';
        document.getElementById('chip_lbl').value = '';
    } else if (tbl === 'cd_video_cards') {
        document.getElementById('vid_id').value = '';
        document.getElementById('vid_sort').value = 0;
        document.getElementById('vid_active').value = 1;
        document.getElementById('vid_track').value = 1;
        document.getElementById('vid_title').value = '';
    }
}

function cdEdit(r, tbl) {
    if (tbl === 'creative_pains') {
        document.getElementById('pain_id').value = r.id || '';
        document.getElementById('pain_sort').value = r.sort_order || 0;
        document.getElementById('pain_active').value = r.is_active ?? 1;
        document.getElementById('pain_text').value = r.text || '';
        new bootstrap.Modal(document.getElementById('painModal')).show();
    } else if (tbl === 'creative_pillars') {
        document.getElementById('pillar_id').value = r.id || '';
        document.getElementById('pillar_sort').value = r.sort_order || 0;
        document.getElementById('pillar_active').value = r.is_active ?? 1;
        document.getElementById('pillar_number').value = r.number || '';
        document.getElementById('pillar_name').value = r.name || '';
        document.getElementById('pillar_description').value = r.description || '';
        document.getElementById('pillar_tags_json').value = r.tags_json || '[]';
        document.getElementById('pillar_svg_path').value = r.svg_path || '';
        new bootstrap.Modal(document.getElementById('pillarModal')).show();
    } else if (tbl === 'creative_services') {
        document.getElementById('service_id').value = r.id || '';
        document.getElementById('service_sort').value = r.sort_order || 0;
        document.getElementById('service_active').value = r.is_active ?? 1;
        document.getElementById('service_name').value = r.name || '';
        document.getElementById('service_subtitle').value = r.subtitle || '';
        document.getElementById('service_description').value = r.description || '';
        document.getElementById('service_bullets_json').value = r.bullets_json || '[]';
        new bootstrap.Modal(document.getElementById('serviceModal')).show();
    } else if (tbl === 'creative_steps') {
        document.getElementById('step_id').value = r.id || '';
        document.getElementById('step_sort').value = r.sort_order || 0;
        document.getElementById('step_active').value = r.is_active ?? 1;
        document.getElementById('step_number').value = r.step_number || '';
        document.getElementById('step_title').value = r.title || '';
        document.getElementById('step_description').value = r.description || '';
        new bootstrap.Modal(document.getElementById('stepModal')).show();
    } else if (tbl === 'creative_metrics') {
        document.getElementById('metric_id').value = r.id || '';
        document.getElementById('metric_sort').value = r.sort_order || 0;
        document.getElementById('metric_active').value = r.is_active ?? 1;
        document.getElementById('metric_value').value = r.value || '';
        document.getElementById('metric_unit').value = r.unit || '';
        document.getElementById('metric_label').value = r.label || '';
        new bootstrap.Modal(document.getElementById('metricModal')).show();
    } else if (tbl === 'creative_why_cards') {
        document.getElementById('why_id').value = r.id || '';
        document.getElementById('why_sort').value = r.sort_order || 0;
        document.getElementById('why_active').value = r.is_active ?? 1;
        document.getElementById('why_number').value = r.number || '';
        document.getElementById('why_title').value = r.title || '';
        document.getElementById('why_description').value = r.description || '';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    } else if (tbl === 'creative_stat_chips') {
        document.getElementById('chip_id').value = r.id || '';
        document.getElementById('chip_sort').value = r.sort_order || 0;
        document.getElementById('chip_active').value = r.is_active ?? 1;
        document.getElementById('chip_num').value = r.chip_num || '';
        document.getElementById('chip_lbl').value = r.chip_lbl || '';
        new bootstrap.Modal(document.getElementById('statChipModal')).show();
    } else if (tbl === 'cd_video_cards') {
        document.getElementById('vid_id').value = r.id || '';
        document.getElementById('vid_sort').value = r.sort_order || 0;
        document.getElementById('vid_active').value = r.is_active ?? 1;
        document.getElementById('vid_track').value = r.track || 1;
        document.getElementById('vid_title').value = r.title || '';
        new bootstrap.Modal(document.getElementById('videoModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
