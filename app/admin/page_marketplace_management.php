<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo         = Database::getInstance();
$challenges  = $pdo->query("SELECT * FROM mktplace_challenges ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps       = $pdo->query("SELECT * FROM mktplace_steps ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mmHero      = $pdo->query("SELECT * FROM mktplace_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$approaches  = $pdo->query("SELECT * FROM mktplace_approach_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$impacts     = $pdo->query("SELECT * FROM mktplace_impacts WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$whyBullets  = $pdo->query("SELECT * FROM mktplace_why_bullets WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mmCta       = $pdo->query("SELECT * FROM mktplace_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$mmSecHdrs   = $pdo->query("SELECT * FROM mktplace_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
$mmHeroIcons = $pdo->query("SELECT * FROM mktplace_hero_icons ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mmSvcBlocks = $pdo->query("SELECT * FROM mktplace_service_blocks ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mmSvcCards  = $pdo->query("SELECT * FROM mktplace_service_block_cards ORDER BY service_block_id, sort_order")->fetchAll(PDO::FETCH_ASSOC);

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage Marketplace Management Page';
include __DIR__ . '/../views/admin_header.php';

function mmBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function mmTable(array $rows, string $tbl, array $cols): string {
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
        $body .= '<td>' . mmBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='mmEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='marketplace_management_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-challenges">Challenges</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-approaches">Approach Cards</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-impacts">Impacts</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why">Why Bullets</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-hero-icons">Hero Icons</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-svc-blocks">Service Blocks</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-svc-cards">Service Block Cards</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="marketplace_management_list_save.php">
            <input type="hidden" name="table_name" value="mktplace_hero">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Badge Text</label>
                  <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($mmHero['badge']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($mmHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (accent)</label>
                  <input type="text" name="h1_line2_accent" class="form-control" value="<?= htmlspecialchars($mmHero['h1_line2_accent']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Hero Sub Copy</label>
                  <textarea name="hero_sub" class="form-control" rows="4"><?= htmlspecialchars($mmHero['hero_sub']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($mmHero['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($mmHero['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <div class="tab-pane fade" id="tab-challenges">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-circle me-2"></i>Challenges</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_challenges')" data-bs-toggle="modal" data-bs-target="#chalModal">
                <i class="fas fa-plus me-1"></i>Add Challenge</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($challenges, 'mktplace_challenges', ['sort_order'=>'Sort','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <div class="tab-pane fade" id="tab-steps">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list-ol me-2"></i>Process Steps</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_steps')" data-bs-toggle="modal" data-bs-target="#stepModal">
                <i class="fas fa-plus me-1"></i>Add Step</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($steps, 'mktplace_steps', ['sort_order'=>'Sort','step_number'=>'Step #','title'=>'Title','icon'=>'Icon','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- APPROACH CARDS -->
    <div class="tab-pane fade" id="tab-approaches">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-layer-group me-2"></i>Approach Cards</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_approach_cards')" data-bs-toggle="modal" data-bs-target="#approachModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($approaches, 'mktplace_approach_cards', ['sort_order'=>'Sort','number_label'=>'No.','title'=>'Title','icon'=>'Icon','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- IMPACTS -->
    <div class="tab-pane fade" id="tab-impacts">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-bar me-2"></i>Impact Items</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_impacts')" data-bs-toggle="modal" data-bs-target="#impactModal">
                <i class="fas fa-plus me-1"></i>Add Impact</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($impacts, 'mktplace_impacts', ['sort_order'=>'Sort','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- WHY BULLETS -->
    <div class="tab-pane fade" id="tab-why">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle me-2"></i>Why Bullets</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_why_bullets')" data-bs-toggle="modal" data-bs-target="#whyModal">
                <i class="fas fa-plus me-1"></i>Add Bullet</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($whyBullets, 'mktplace_why_bullets', ['sort_order'=>'Sort','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="marketplace_management_list_save.php">
            <input type="hidden" name="table_name" value="mktplace_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Background Text (watermark)</label>
                  <input type="text" name="bg_text" class="form-control" value="<?= htmlspecialchars($mmCta['bg_text']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($mmCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($mmCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($mmCta['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($mmCta['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($mmCta['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($mmCta['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>


    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($mmSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="marketplace_management_list_save.php">
                <input type="hidden" name="table_name" value="mktplace_section_headers">
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

    <!-- HERO ICONS -->
    <div class="tab-pane fade" id="tab-hero-icons">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-icons me-2"></i>Hero Floating Icons</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_hero_icons')" data-bs-toggle="modal" data-bs-target="#heroIconModal">
                <i class="fas fa-plus me-1"></i>Add Icon</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($mmHeroIcons, 'mktplace_hero_icons', ['sort_order'=>'Sort','title'=>'Title','svg_file'=>'SVG File']) ?>
        </div></div>
    </div>

    <!-- SERVICE BLOCKS -->
    <div class="tab-pane fade" id="tab-svc-blocks">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-cubes me-2"></i>Service Blocks</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_service_blocks')" data-bs-toggle="modal" data-bs-target="#svcBlockModal">
                <i class="fas fa-plus me-1"></i>Add Block</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($mmSvcBlocks, 'mktplace_service_blocks', ['sort_order'=>'Sort','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- SERVICE BLOCK CARDS -->
    <div class="tab-pane fade" id="tab-svc-cards">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-th me-2"></i>Service Block Cards</span>
            <button class="btn btn-primary btn-sm" onclick="mmReset('mktplace_service_block_cards')" data-bs-toggle="modal" data-bs-target="#svcCardModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= mmTable($mmSvcCards, 'mktplace_service_block_cards', ['sort_order'=>'Sort','service_block_id'=>'Block ID','icon'=>'Icon','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

</div>

<!-- CHALLENGE MODAL -->
<div class="modal fade" id="chalModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Challenge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_challenges">
        <input type="hidden" name="id" id="chal_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="chal_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="chal_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Icon (Material Symbols name)</label>
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

<!-- STEP MODAL -->
<div class="modal fade" id="stepModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Process Step</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_steps">
        <input type="hidden" name="id" id="step_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="step_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="step_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Step Number (e.g. 01)</label>
          <input type="text" name="step_number" id="step_num" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Title</label>
          <input type="text" name="title" id="step_title" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Icon (Material Symbols name)</label>
          <input type="text" name="icon" id="step_icon" class="form-control"></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="step_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- APPROACH CARD MODAL -->
<div class="modal fade" id="approachModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approach Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_approach_cards">
        <input type="hidden" name="id" id="ap_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="ap_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="ap_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Number Label (e.g. 01)</label>
          <input type="text" name="number_label" id="ap_number" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Title</label>
          <input type="text" name="title" id="ap_title" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Icon (Material Symbols name)</label>
          <input type="text" name="icon" id="ap_icon" class="form-control"></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="ap_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- IMPACT MODAL -->
<div class="modal fade" id="impactModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Impact Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_impacts">
        <input type="hidden" name="id" id="impact_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="impact_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="impact_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Icon (Material Symbols name)</label>
          <input type="text" name="icon" id="impact_icon" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="impact_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="impact_desc" class="form-control" rows="3"></textarea></div>
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
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_why_bullets">
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

<!-- HERO ICON MODAL -->
<div class="modal fade" id="heroIconModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hero Icon</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_hero_icons">
        <input type="hidden" name="id" id="hicon_id">
        <div class="col-md-6"><label class="form-label">Sort Order (1-4 for positions)</label>
          <input type="number" name="sort_order" id="hicon_sort" class="form-control" value="1" min="1" max="4"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="hicon_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Title (tooltip label)</label>
          <input type="text" name="title" id="hicon_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">SVG File (filename in /public/assets/svg-logo/)</label>
          <input type="text" name="svg_file" id="hicon_svg" class="form-control" placeholder="brand-logo.svg"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SERVICE BLOCK MODAL -->
<div class="modal fade" id="svcBlockModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Service Block</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_service_blocks">
        <input type="hidden" name="id" id="sblk_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="sblk_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="sblk_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Title (sidebar label)</label>
          <input type="text" name="title" id="sblk_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="sblk_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- SERVICE BLOCK CARD MODAL -->
<div class="modal fade" id="svcCardModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Service Block Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="marketplace_management_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="mktplace_service_block_cards">
        <input type="hidden" name="id" id="scrd_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="scrd_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="scrd_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Service Block ID</label>
          <input type="number" name="service_block_id" id="scrd_block_id" class="form-control" value="1" required></div>
        <div class="col-md-6"><label class="form-label">Icon (Material Symbols name)</label>
          <input type="text" name="icon" id="scrd_icon" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Title</label>
          <input type="text" name="title" id="scrd_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="scrd_desc" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><label class="form-label">Bullets (JSON array, e.g. ["Item A","Item B"])</label>
          <textarea name="bullets_json" id="scrd_bullets" class="form-control" rows="2">[]</textarea></div>
        <div class="col-md-6"><label class="form-label">Wide Card?</label>
          <select name="is_wide" id="scrd_wide" class="form-select"><option value="0">No</option><option value="1">Yes</option></select></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function mmReset(tbl) {
    if (tbl === 'mktplace_challenges') {
        ['chal_id','chal_icon','chal_title','chal_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('chal_sort').value = 0;
        document.getElementById('chal_active').value = 1;
    } else if (tbl === 'mktplace_steps') {
        ['step_id','step_num','step_title','step_icon','step_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('step_sort').value = 0;
        document.getElementById('step_active').value = 1;
    } else if (tbl === 'mktplace_approach_cards') {
        ['ap_id','ap_number','ap_title','ap_icon','ap_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('ap_sort').value = 0;
        document.getElementById('ap_active').value = 1;
    } else if (tbl === 'mktplace_impacts') {
        ['impact_id','impact_icon','impact_title','impact_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('impact_sort').value = 0;
        document.getElementById('impact_active').value = 1;
    } else if (tbl === 'mktplace_why_bullets') {
        document.getElementById('why_id').value = '';
        document.getElementById('why_text').value = '';
        document.getElementById('why_sort').value = 0;
        document.getElementById('why_active').value = 1;
    } else if (tbl === 'mktplace_hero_icons') {
        ['hicon_id','hicon_title','hicon_svg'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('hicon_sort').value = 1;
        document.getElementById('hicon_active').value = 1;
    } else if (tbl === 'mktplace_service_blocks') {
        ['sblk_id','sblk_title','sblk_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('sblk_sort').value = 0;
        document.getElementById('sblk_active').value = 1;
    } else if (tbl === 'mktplace_service_block_cards') {
        ['scrd_id','scrd_icon','scrd_title','scrd_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('scrd_sort').value = 0;
        document.getElementById('scrd_active').value = 1;
        document.getElementById('scrd_block_id').value = 1;
        document.getElementById('scrd_bullets').value = '[]';
        document.getElementById('scrd_wide').value = 0;
    }
}

function mmEdit(r, tbl) {
    if (tbl === 'mktplace_challenges') {
        document.getElementById('chal_id').value = r.id || '';
        document.getElementById('chal_sort').value = r.sort_order || 0;
        document.getElementById('chal_active').value = r.is_active ?? 1;
        document.getElementById('chal_icon').value = r.icon || '';
        document.getElementById('chal_title').value = r.title || '';
        document.getElementById('chal_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('chalModal')).show();
    } else if (tbl === 'mktplace_steps') {
        document.getElementById('step_id').value = r.id || '';
        document.getElementById('step_sort').value = r.sort_order || 0;
        document.getElementById('step_active').value = r.is_active ?? 1;
        document.getElementById('step_num').value = r.step_number || '';
        document.getElementById('step_title').value = r.title || '';
        document.getElementById('step_icon').value = r.icon || '';
        document.getElementById('step_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('stepModal')).show();
    } else if (tbl === 'mktplace_approach_cards') {
        document.getElementById('ap_id').value = r.id || '';
        document.getElementById('ap_sort').value = r.sort_order || 0;
        document.getElementById('ap_active').value = r.is_active ?? 1;
        document.getElementById('ap_number').value = r.number_label || '';
        document.getElementById('ap_title').value = r.title || '';
        document.getElementById('ap_icon').value = r.icon || '';
        document.getElementById('ap_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('approachModal')).show();
    } else if (tbl === 'mktplace_impacts') {
        document.getElementById('impact_id').value = r.id || '';
        document.getElementById('impact_sort').value = r.sort_order || 0;
        document.getElementById('impact_active').value = r.is_active ?? 1;
        document.getElementById('impact_icon').value = r.icon || '';
        document.getElementById('impact_title').value = r.title || '';
        document.getElementById('impact_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('impactModal')).show();
    } else if (tbl === 'mktplace_why_bullets') {
        document.getElementById('why_id').value = r.id || '';
        document.getElementById('why_sort').value = r.sort_order || 0;
        document.getElementById('why_active').value = r.is_active ?? 1;
        document.getElementById('why_text').value = r.text || '';
        new bootstrap.Modal(document.getElementById('whyModal')).show();
    } else if (tbl === 'mktplace_hero_icons') {
        document.getElementById('hicon_id').value = r.id || '';
        document.getElementById('hicon_sort').value = r.sort_order || 1;
        document.getElementById('hicon_active').value = r.is_active ?? 1;
        document.getElementById('hicon_title').value = r.title || '';
        document.getElementById('hicon_svg').value = r.svg_file || '';
        new bootstrap.Modal(document.getElementById('heroIconModal')).show();
    } else if (tbl === 'mktplace_service_blocks') {
        document.getElementById('sblk_id').value = r.id || '';
        document.getElementById('sblk_sort').value = r.sort_order || 0;
        document.getElementById('sblk_active').value = r.is_active ?? 1;
        document.getElementById('sblk_title').value = r.title || '';
        document.getElementById('sblk_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('svcBlockModal')).show();
    } else if (tbl === 'mktplace_service_block_cards') {
        document.getElementById('scrd_id').value = r.id || '';
        document.getElementById('scrd_sort').value = r.sort_order || 0;
        document.getElementById('scrd_active').value = r.is_active ?? 1;
        document.getElementById('scrd_block_id').value = r.service_block_id || 1;
        document.getElementById('scrd_icon').value = r.icon || '';
        document.getElementById('scrd_title').value = r.title || '';
        document.getElementById('scrd_desc').value = r.description || '';
        document.getElementById('scrd_bullets').value = r.bullets_json || '[]';
        document.getElementById('scrd_wide').value = r.is_wide || 0;
        new bootstrap.Modal(document.getElementById('svcCardModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
