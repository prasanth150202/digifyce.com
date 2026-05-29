<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo        = Database::getInstance();
$solutions  = $pdo->query("SELECT * FROM content_solutions ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ctHero     = $pdo->query("SELECT * FROM content_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$heroStats  = $pdo->query("SELECT * FROM content_hero_stats ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$challenges = $pdo->query("SELECT * FROM content_challenges ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$pillars    = $pdo->query("SELECT * FROM content_pillars ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$metrics    = $pdo->query("SELECT * FROM content_metrics ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyPoints  = $pdo->query("SELECT * FROM content_why_points ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ctCta       = $pdo->query("SELECT * FROM content_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ctSecHdrs   = $pdo->query("SELECT * FROM content_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
$ctSigPoints = $pdo->query("SELECT * FROM content_signal_points ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Content Marketing Page';
include __DIR__ . '/../views/admin_header.php';

function ctBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function ctTable(array $rows, string $tbl, array $cols): string {
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
        $body .= '<td>' . ctBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='ctEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='content_marketing_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2" id="ctTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-stats">Hero Stats</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-solutions">Solutions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-challenges">Challenges</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pillars">Pillars</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-metrics">Metrics</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Points</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-signal">Signal Points</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="content_marketing_list_save.php">
            <input type="hidden" name="table_name" value="content_hero">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Kicker (eyebrow text)</label>
                  <input type="text" name="kicker" class="form-control" value="<?= htmlspecialchars($ctHero['kicker']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($ctHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (gradient)</label>
                  <input type="text" name="h1_line2_gradient" class="form-control" value="<?= htmlspecialchars($ctHero['h1_line2_gradient']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Hero Sub Copy</label>
                  <textarea name="hero_sub" class="form-control" rows="4"><?= htmlspecialchars($ctHero['hero_sub']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($ctHero['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($ctHero['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($ctHero['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($ctHero['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- HERO STATS -->
    <div class="tab-pane fade" id="tab-stats">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-bar me-2"></i>Hero Stats</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_hero_stats')" data-bs-toggle="modal" data-bs-target="#statModal">
                <i class="fas fa-plus me-1"></i>Add Stat</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($heroStats, 'content_hero_stats', ['sort_order'=>'Sort','label'=>'Label','value'=>'Value','description'=>'Description (chip only)']) ?>
        </div></div>
    </div>

    <!-- SOLUTIONS -->
    <div class="tab-pane fade" id="tab-solutions">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-pen-nib me-2"></i>Content Solutions (Stack Cards)</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_solutions')" data-bs-toggle="modal" data-bs-target="#solModal">
                <i class="fas fa-plus me-1"></i>Add Solution</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($solutions, 'content_solutions', ['sort_order'=>'Sort','number'=>'No.','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- CHALLENGES -->
    <div class="tab-pane fade" id="tab-challenges">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-triangle me-2"></i>Challenges</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_challenges')" data-bs-toggle="modal" data-bs-target="#chalModal">
                <i class="fas fa-plus me-1"></i>Add Challenge</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($challenges, 'content_challenges', ['sort_order'=>'Sort','number_label'=>'No.','title'=>'Title','description'=>'Description','progress_width'=>'Progress Width']) ?>
        </div></div>
    </div>

    <!-- PILLARS -->
    <div class="tab-pane fade" id="tab-pillars">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-columns me-2"></i>Content Pillars</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_pillars')" data-bs-toggle="modal" data-bs-target="#pillarModal">
                <i class="fas fa-plus me-1"></i>Add Pillar</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($pillars, 'content_pillars', ['sort_order'=>'Sort','number_label'=>'No.','name'=>'Name','panel_title'=>'Panel Title','panel_description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- METRICS -->
    <div class="tab-pane fade" id="tab-metrics">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-tachometer-alt me-2"></i>Animated Metrics</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_metrics')" data-bs-toggle="modal" data-bs-target="#metricModal">
                <i class="fas fa-plus me-1"></i>Add Metric</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($metrics, 'content_metrics', ['sort_order'=>'Sort','target_num'=>'Target Number','label'=>'Label']) ?>
        </div></div>
    </div>

    <!-- WHY POINTS -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Why Choose Digifyce Points</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_why_points')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Point</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($whyPoints, 'content_why_points', ['sort_order'=>'Sort','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="content_marketing_list_save.php">
            <input type="hidden" name="table_name" value="content_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Background Text (watermark)</label>
                  <input type="text" name="bg_text" class="form-control" value="<?= htmlspecialchars($ctCta['bg_text']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($ctCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($ctCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($ctCta['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($ctCta['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>


    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($ctSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="content_marketing_list_save.php">
                <input type="hidden" name="table_name" value="content_section_headers">
                <input type="hidden" name="slug" value="<?= htmlspecialchars($sh['slug']) ?>">
                <div class="row g-2">
                    <div class="col-md-4"><label class="form-label">Eyebrow</label>
                      <input type="text" name="eyebrow" class="form-control" value="<?= htmlspecialchars($sh['eyebrow'] ?? '') ?>"></div>
                    <div class="col-md-8"><label class="form-label">Heading</label>
                      <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($sh['heading'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Sub Text</label>
                      <textarea name="sub_text" class="form-control" rows="2"><?= htmlspecialchars($sh['sub_text'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label">Extra Text (JSON or plain)</label>
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

    <!-- SIGNAL POINTS -->
    <div class="tab-pane fade" id="tab-signal">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-signal me-2"></i>Hero Signal Points</span>
            <button class="btn btn-primary btn-sm" onclick="ctReset('content_signal_points')" data-bs-toggle="modal" data-bs-target="#signalModal">
                <i class="fas fa-plus me-1"></i>Add Signal Point</button>
        </div>
        <div class="card-body p-0">
            <?= ctTable($ctSigPoints, 'content_signal_points', ['sort_order'=>'Sort','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

</div>

<!-- HERO STAT MODAL -->
<div class="modal fade" id="statModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hero Stat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_hero_stats">
        <input type="hidden" name="id" id="stat_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="stat_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="stat_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Label (e.g. Avg Lift)</label>
          <input type="text" name="label" id="stat_label" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Value (e.g. 3.2x)</label>
          <input type="text" name="value" id="stat_value" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description (leave empty for plain stat, fill for chip-style)</label>
          <textarea name="description" id="stat_desc" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SOLUTION MODAL -->
<div class="modal fade" id="solModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Content Solution</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_solutions">
        <input type="hidden" name="id" id="sol_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="sol_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="sol_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="sol_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="sol_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="sol_description" class="form-control" rows="4"></textarea></div>
        <div class="col-12"><label class="form-label">Background Color (hex, e.g. #0f172a)</label>
          <input type="text" name="bg_color" id="sol_bg_color" class="form-control" value="#0f172a"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- CHALLENGE MODAL -->
<div class="modal fade" id="chalModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Challenge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_challenges">
        <input type="hidden" name="id" id="chal_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="chal_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="chal_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number Label (e.g. 01)</label>
          <input type="text" name="number_label" id="chal_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="chal_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="chal_desc" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Progress Width (Tailwind class, e.g. w-3/5)</label>
          <input type="text" name="progress_width" id="chal_progress" class="form-control" value="w-1/2"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- PILLAR MODAL -->
<div class="modal fade" id="pillarModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Content Pillar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_pillars">
        <input type="hidden" name="id" id="pil_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="pil_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="pil_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number Label (e.g. 01)</label>
          <input type="text" name="number_label" id="pil_number" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Name (tab label, e.g. Visibility)</label>
          <input type="text" name="name" id="pil_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Panel Title</label>
          <input type="text" name="panel_title" id="pil_panel_title" class="form-control"></div>
        <div class="col-12"><label class="form-label">Panel Description</label>
          <textarea name="panel_description" id="pil_panel_desc" class="form-control" rows="4"></textarea></div>
        <div class="col-12"><label class="form-label">Bullets (JSON array, e.g. ["Item A","Item B"])</label>
          <textarea name="bullets_json" id="pil_bullets" class="form-control" rows="3">[]</textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- METRIC MODAL -->
<div class="modal fade" id="metricModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Animated Metric</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_metrics">
        <input type="hidden" name="id" id="met_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="met_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="met_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Target Number (animates to this)</label>
          <input type="number" name="target_num" id="met_target" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Label</label>
          <input type="text" name="label" id="met_label" class="form-control" required></div>
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
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_why_points">
        <input type="hidden" name="id" id="why_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="why_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="why_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Icon (Material Symbols name, e.g. manage_search)</label>
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

<!-- SIGNAL POINT MODAL -->
<div class="modal fade" id="signalModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Signal Point</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="content_marketing_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="content_signal_points">
        <input type="hidden" name="id" id="sig_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="sig_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="sig_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="sig_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="sig_desc" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function ctReset(tbl) {
    if (tbl === 'content_hero_stats') {
        ['stat_id','stat_label','stat_value','stat_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('stat_sort').value = 0;
        document.getElementById('stat_active').value = 1;
    } else if (tbl === 'content_solutions') {
        ['sol_id','sol_number','sol_title','sol_description'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('sol_sort').value = 0;
        document.getElementById('sol_active').value = 1;
        document.getElementById('sol_bg_color').value = '#0f172a';
    } else if (tbl === 'content_challenges') {
        ['chal_id','chal_number','chal_title','chal_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('chal_sort').value = 0;
        document.getElementById('chal_active').value = 1;
        document.getElementById('chal_progress').value = 'w-1/2';
    } else if (tbl === 'content_pillars') {
        ['pil_id','pil_number','pil_name','pil_panel_title','pil_panel_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('pil_sort').value = 0;
        document.getElementById('pil_active').value = 1;
        document.getElementById('pil_bullets').value = '[]';
    } else if (tbl === 'content_metrics') {
        ['met_id','met_label'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('met_sort').value = 0;
        document.getElementById('met_active').value = 1;
        document.getElementById('met_target').value = 0;
    } else if (tbl === 'content_why_points') {
        ['why_id','why_icon','why_title','why_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('why_sort').value = 0;
        document.getElementById('why_active').value = 1;
    } else if (tbl === 'content_signal_points') {
        ['sig_id','sig_title','sig_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('sig_sort').value = 0;
        document.getElementById('sig_active').value = 1;
    }
}

function ctEdit(r, tbl) {
    if (tbl === 'content_hero_stats') {
        document.getElementById('stat_id').value = r.id || '';
        document.getElementById('stat_sort').value = r.sort_order || 0;
        document.getElementById('stat_active').value = r.is_active ?? 1;
        document.getElementById('stat_label').value = r.label || '';
        document.getElementById('stat_value').value = r.value || '';
        document.getElementById('stat_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('statModal')).show();
    } else if (tbl === 'content_solutions') {
        document.getElementById('sol_id').value = r.id || '';
        document.getElementById('sol_sort').value = r.sort_order || 0;
        document.getElementById('sol_active').value = r.is_active ?? 1;
        document.getElementById('sol_number').value = r.number || '';
        document.getElementById('sol_title').value = r.title || '';
        document.getElementById('sol_description').value = r.description || '';
        document.getElementById('sol_bg_color').value = r.bg_color || '#0f172a';
        new bootstrap.Modal(document.getElementById('solModal')).show();
    } else if (tbl === 'content_challenges') {
        document.getElementById('chal_id').value = r.id || '';
        document.getElementById('chal_sort').value = r.sort_order || 0;
        document.getElementById('chal_active').value = r.is_active ?? 1;
        document.getElementById('chal_number').value = r.number_label || '';
        document.getElementById('chal_title').value = r.title || '';
        document.getElementById('chal_desc').value = r.description || '';
        document.getElementById('chal_progress').value = r.progress_width || 'w-1/2';
        new bootstrap.Modal(document.getElementById('chalModal')).show();
    } else if (tbl === 'content_pillars') {
        document.getElementById('pil_id').value = r.id || '';
        document.getElementById('pil_sort').value = r.sort_order || 0;
        document.getElementById('pil_active').value = r.is_active ?? 1;
        document.getElementById('pil_number').value = r.number_label || '';
        document.getElementById('pil_name').value = r.name || '';
        document.getElementById('pil_panel_title').value = r.panel_title || '';
        document.getElementById('pil_panel_desc').value = r.panel_description || '';
        document.getElementById('pil_bullets').value = r.bullets_json || '[]';
        new bootstrap.Modal(document.getElementById('pillarModal')).show();
    } else if (tbl === 'content_metrics') {
        document.getElementById('met_id').value = r.id || '';
        document.getElementById('met_sort').value = r.sort_order || 0;
        document.getElementById('met_active').value = r.is_active ?? 1;
        document.getElementById('met_target').value = r.target_num || 0;
        document.getElementById('met_label').value = r.label || '';
        new bootstrap.Modal(document.getElementById('metricModal')).show();
    } else if (tbl === 'content_why_points') {
        document.getElementById('why_id').value = r.id || '';
        document.getElementById('why_sort').value = r.sort_order || 0;
        document.getElementById('why_active').value = r.is_active ?? 1;
        document.getElementById('why_icon').value = r.icon || '';
        document.getElementById('why_title').value = r.title || '';
        document.getElementById('why_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    } else if (tbl === 'content_signal_points') {
        document.getElementById('sig_id').value = r.id || '';
        document.getElementById('sig_sort').value = r.sort_order || 0;
        document.getElementById('sig_active').value = r.is_active ?? 1;
        document.getElementById('sig_title').value = r.title || '';
        document.getElementById('sig_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('signalModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
