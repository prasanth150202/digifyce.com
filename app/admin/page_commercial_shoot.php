<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo          = Database::getInstance();
$challenges   = $pdo->query("SELECT * FROM commercial_shoot_challenges ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$services     = $pdo->query("SELECT * FROM commercial_shoot_services ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps        = $pdo->query("SELECT * FROM commercial_shoot_steps ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$impacts      = $pdo->query("SELECT * FROM commercial_shoot_impacts ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$csHero       = $pdo->query("SELECT * FROM cs_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$heroFeatures = $pdo->query("SELECT * FROM cs_hero_features WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$secHeaders   = $pdo->query("SELECT * FROM cs_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
$approaches   = $pdo->query("SELECT * FROM cs_approach_panels WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyBullets   = $pdo->query("SELECT * FROM cs_why_bullets WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$csCta        = $pdo->query("SELECT * FROM cs_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Commercial Shoot Page';
include __DIR__ . '/../views/admin_header.php';

function csBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function csTable(array $rows, string $tbl, array $cols): string {
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
        $body .= '<td>' . csBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='csEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='commercial_shoot_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2" id="csTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-hero-features">Hero Features</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-challenges">Challenges</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-services">Services</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-impacts">Impact Cards</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-approach">Approach Panels</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Bullets</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="commercial_shoot_list_save.php">
            <input type="hidden" name="table_name" value="cs_hero">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Eyebrow (kicker text)</label>
                  <input type="text" name="eyebrow" class="form-control" value="<?= htmlspecialchars($csHero['eyebrow']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($csHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (accent)</label>
                  <input type="text" name="h1_line2_accent" class="form-control" value="<?= htmlspecialchars($csHero['h1_line2_accent']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Hero Copy</label>
                  <textarea name="hero_copy" class="form-control" rows="4"><?= htmlspecialchars($csHero['hero_copy']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($csHero['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($csHero['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($csHero['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($csHero['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- HERO FEATURES -->
    <div class="tab-pane fade" id="tab-hero-features">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Hero Feature Cards</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('cs_hero_features')" data-bs-toggle="modal" data-bs-target="#heroFeatModal">
                <i class="fas fa-plus me-1"></i>Add Feature</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($heroFeatures, 'cs_hero_features', ['sort_order'=>'Sort','label'=>'Label','title'=>'Title','copy'=>'Copy']) ?>
        </div></div>
    </div>

    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($secHeaders as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="commercial_shoot_list_save.php">
                <input type="hidden" name="table_name" value="cs_section_headers">
                <input type="hidden" name="slug" value="<?= htmlspecialchars($sh['slug']) ?>">
                <div class="row g-2">
                    <div class="col-md-4"><label class="form-label">Eyebrow</label>
                      <input type="text" name="eyebrow" class="form-control" value="<?= htmlspecialchars($sh['eyebrow']) ?>"></div>
                    <div class="col-md-8"><label class="form-label">Heading</label>
                      <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($sh['heading']) ?>"></div>
                    <div class="col-12"><label class="form-label">Sub Text</label>
                      <textarea name="sub_text" class="form-control" rows="2"><?= htmlspecialchars($sh['sub_text']) ?></textarea></div>
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

    <!-- CHALLENGES -->
    <div class="tab-pane fade" id="tab-challenges">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-triangle me-2"></i>Common Challenges</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('commercial_shoot_challenges')" data-bs-toggle="modal" data-bs-target="#challengeModal">
                <i class="fas fa-plus me-1"></i>Add Challenge</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($challenges, 'commercial_shoot_challenges', ['sort_order'=>'Sort','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- SERVICES -->
    <div class="tab-pane fade" id="tab-services">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-camera me-2"></i>Services</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('commercial_shoot_services')" data-bs-toggle="modal" data-bs-target="#serviceModal">
                <i class="fas fa-plus me-1"></i>Add Service</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($services, 'commercial_shoot_services', ['sort_order'=>'Sort','eyebrow'=>'Eyebrow','heading'=>'Heading','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- STEPS -->
    <div class="tab-pane fade" id="tab-steps">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list-ol me-2"></i>Process Steps</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('commercial_shoot_steps')" data-bs-toggle="modal" data-bs-target="#stepModal">
                <i class="fas fa-plus me-1"></i>Add Step</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($steps, 'commercial_shoot_steps', ['sort_order'=>'Sort','step_number'=>'Step #','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- IMPACTS -->
    <div class="tab-pane fade" id="tab-impacts">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-line me-2"></i>Impact Cards</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('commercial_shoot_impacts')" data-bs-toggle="modal" data-bs-target="#impactModal">
                <i class="fas fa-plus me-1"></i>Add Impact Card</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($impacts, 'commercial_shoot_impacts', ['sort_order'=>'Sort','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- APPROACH PANELS -->
    <div class="tab-pane fade" id="tab-approach">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-layer-group me-2"></i>Approach Panels</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('cs_approach_panels')" data-bs-toggle="modal" data-bs-target="#approachModal">
                <i class="fas fa-plus me-1"></i>Add Panel</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($approaches, 'cs_approach_panels', ['sort_order'=>'Sort','step_label'=>'Step Label','heading'=>'Heading','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- WHY BULLETS -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle me-2"></i>Why Bullets</span>
            <button class="btn btn-primary btn-sm" onclick="csReset('cs_why_bullets')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Bullet</button>
        </div>
        <div class="card-body p-0">
            <?= csTable($whyBullets, 'cs_why_bullets', ['sort_order'=>'Sort','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="commercial_shoot_list_save.php">
            <input type="hidden" name="table_name" value="cs_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Background Text (watermark)</label>
                  <input type="text" name="bg_text" class="form-control" value="<?= htmlspecialchars($csCta['bg_text']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($csCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($csCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($csCta['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($csCta['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($csCta['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($csCta['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>

</div>

<!-- CHALLENGE MODAL -->
<div class="modal fade" id="challengeModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Challenge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="commercial_shoot_challenges">
        <input type="hidden" name="id" id="chal_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="chal_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="chal_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Text</label>
          <textarea name="text" id="chal_text" class="form-control" rows="3" required></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SERVICE MODAL -->
<div class="modal fade" id="serviceModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="commercial_shoot_services">
        <input type="hidden" name="id" id="svc_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="svc_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="svc_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Eyebrow (category label)</label>
          <input type="text" name="eyebrow" id="svc_eyebrow" class="form-control"></div>
        <div class="col-12"><label class="form-label">Heading</label>
          <input type="text" name="heading" id="svc_heading" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="svc_description" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Chips (JSON array, e.g. ["Chip A","Chip B"])</label>
          <textarea name="chips_json" id="svc_chips_json" class="form-control" rows="2">[]</textarea></div>
        <div class="col-12"><label class="form-label">Image Path (relative, e.g. public/assets/img/file.png)</label>
          <input type="text" name="img_src" id="svc_img_src" class="form-control"></div>
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
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="commercial_shoot_steps">
        <input type="hidden" name="id" id="step_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="step_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="step_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Step Number</label>
          <input type="text" name="step_number" id="step_number_field" class="form-control"></div>
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

<!-- IMPACT MODAL -->
<div class="modal fade" id="impactModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Impact Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="commercial_shoot_impacts">
        <input type="hidden" name="id" id="impact_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="impact_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="impact_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="impact_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="impact_description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- HERO FEATURE MODAL -->
<div class="modal fade" id="heroFeatModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hero Feature Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="cs_hero_features">
        <input type="hidden" name="id" id="hf_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="hf_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="hf_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Label (e.g. Commercial Focus)</label>
          <input type="text" name="label" id="hf_label" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Title (e.g. Attention)</label>
          <input type="text" name="title" id="hf_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Copy (body text)</label>
          <textarea name="copy" id="hf_copy" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Footer Text</label>
          <input type="text" name="footer_text" id="hf_footer" class="form-control"></div>
        <div class="col-12"><label class="form-label">Chips (JSON array, e.g. ["Item A","Item B"])</label>
          <textarea name="chips_json" id="hf_chips" class="form-control" rows="2">[]</textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- APPROACH PANEL MODAL -->
<div class="modal fade" id="approachModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approach Panel</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="cs_approach_panels">
        <input type="hidden" name="id" id="ap_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="ap_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="ap_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Full Width?</label>
          <select name="is_full_width" id="ap_full_width" class="form-select"><option value="0">No</option><option value="1">Yes</option></select></div>
        <div class="col-md-6"><label class="form-label">Step Label (e.g. 1. Attention)</label>
          <input type="text" name="step_label" id="ap_step_label" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Heading</label>
          <input type="text" name="heading" id="ap_heading" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="ap_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHY BULLET MODAL -->
<div class="modal fade" id="whyModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Why Bullet</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="commercial_shoot_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="cs_why_bullets">
        <input type="hidden" name="id" id="why_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="why_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="why_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Text</label>
          <textarea name="text" id="why_text" class="form-control" rows="2" required></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function csReset(tbl) {
    if (tbl === 'commercial_shoot_challenges') {
        document.getElementById('chal_id').value = '';
        document.getElementById('chal_sort').value = 0;
        document.getElementById('chal_active').value = 1;
        document.getElementById('chal_text').value = '';
    } else if (tbl === 'commercial_shoot_services') {
        document.getElementById('svc_id').value = '';
        document.getElementById('svc_sort').value = 0;
        document.getElementById('svc_active').value = 1;
        document.getElementById('svc_eyebrow').value = '';
        document.getElementById('svc_heading').value = '';
        document.getElementById('svc_description').value = '';
        document.getElementById('svc_chips_json').value = '[]';
        document.getElementById('svc_img_src').value = '';
    } else if (tbl === 'commercial_shoot_steps') {
        document.getElementById('step_id').value = '';
        document.getElementById('step_sort').value = 0;
        document.getElementById('step_active').value = 1;
        document.getElementById('step_number_field').value = '';
        document.getElementById('step_title').value = '';
        document.getElementById('step_description').value = '';
    } else if (tbl === 'commercial_shoot_impacts') {
        document.getElementById('impact_id').value = '';
        document.getElementById('impact_sort').value = 0;
        document.getElementById('impact_active').value = 1;
        document.getElementById('impact_title').value = '';
        document.getElementById('impact_description').value = '';
    } else if (tbl === 'cs_hero_features') {
        ['hf_id','hf_label','hf_title','hf_copy','hf_footer'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('hf_sort').value = 0;
        document.getElementById('hf_active').value = 1;
        document.getElementById('hf_chips').value = '[]';
    } else if (tbl === 'cs_approach_panels') {
        ['ap_id','ap_step_label','ap_heading','ap_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('ap_sort').value = 0;
        document.getElementById('ap_active').value = 1;
        document.getElementById('ap_full_width').value = 0;
    } else if (tbl === 'cs_why_bullets') {
        document.getElementById('why_id').value = '';
        document.getElementById('why_text').value = '';
        document.getElementById('why_sort').value = 0;
        document.getElementById('why_active').value = 1;
    }
}

function csEdit(r, tbl) {
    if (tbl === 'commercial_shoot_challenges') {
        document.getElementById('chal_id').value = r.id || '';
        document.getElementById('chal_sort').value = r.sort_order || 0;
        document.getElementById('chal_active').value = r.is_active ?? 1;
        document.getElementById('chal_text').value = r.text || '';
        new bootstrap.Modal(document.getElementById('challengeModal')).show();
    } else if (tbl === 'commercial_shoot_services') {
        document.getElementById('svc_id').value = r.id || '';
        document.getElementById('svc_sort').value = r.sort_order || 0;
        document.getElementById('svc_active').value = r.is_active ?? 1;
        document.getElementById('svc_eyebrow').value = r.eyebrow || '';
        document.getElementById('svc_heading').value = r.heading || '';
        document.getElementById('svc_description').value = r.description || '';
        document.getElementById('svc_chips_json').value = r.chips_json || '[]';
        document.getElementById('svc_img_src').value = r.img_src || '';
        new bootstrap.Modal(document.getElementById('serviceModal')).show();
    } else if (tbl === 'commercial_shoot_steps') {
        document.getElementById('step_id').value = r.id || '';
        document.getElementById('step_sort').value = r.sort_order || 0;
        document.getElementById('step_active').value = r.is_active ?? 1;
        document.getElementById('step_number_field').value = r.step_number || '';
        document.getElementById('step_title').value = r.title || '';
        document.getElementById('step_description').value = r.description || '';
        new bootstrap.Modal(document.getElementById('stepModal')).show();
    } else if (tbl === 'commercial_shoot_impacts') {
        document.getElementById('impact_id').value = r.id || '';
        document.getElementById('impact_sort').value = r.sort_order || 0;
        document.getElementById('impact_active').value = r.is_active ?? 1;
        document.getElementById('impact_title').value = r.title || '';
        document.getElementById('impact_description').value = r.description || '';
        new bootstrap.Modal(document.getElementById('impactModal')).show();
    } else if (tbl === 'cs_hero_features') {
        document.getElementById('hf_id').value = r.id || '';
        document.getElementById('hf_sort').value = r.sort_order || 0;
        document.getElementById('hf_active').value = r.is_active ?? 1;
        document.getElementById('hf_label').value = r.label || '';
        document.getElementById('hf_title').value = r.title || '';
        document.getElementById('hf_copy').value = r.copy || '';
        document.getElementById('hf_footer').value = r.footer_text || '';
        document.getElementById('hf_chips').value = r.chips_json || '[]';
        new bootstrap.Modal(document.getElementById('heroFeatModal')).show();
    } else if (tbl === 'cs_approach_panels') {
        document.getElementById('ap_id').value = r.id || '';
        document.getElementById('ap_sort').value = r.sort_order || 0;
        document.getElementById('ap_active').value = r.is_active ?? 1;
        document.getElementById('ap_full_width').value = r.is_full_width || 0;
        document.getElementById('ap_step_label').value = r.step_label || '';
        document.getElementById('ap_heading').value = r.heading || '';
        document.getElementById('ap_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('approachModal')).show();
    } else if (tbl === 'cs_why_bullets') {
        document.getElementById('why_id').value = r.id || '';
        document.getElementById('why_sort').value = r.sort_order || 0;
        document.getElementById('why_active').value = r.is_active ?? 1;
        document.getElementById('why_text').value = r.text || '';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
