<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo       = Database::getInstance();
$abHero    = $pdo->query("SELECT * FROM about_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$abStats   = $pdo->query("SELECT * FROM about_hero_stats ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abSecHdrs = $pdo->query("SELECT * FROM about_section_headers ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
$abWhy     = $pdo->query("SELECT * FROM about_why_cards ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abWho     = $pdo->query("SELECT * FROM about_who_sub_cards ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abWhat    = $pdo->query("SELECT * FROM about_what_we_do ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abPillars = $pdo->query("SELECT * FROM about_approach_pillars ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abWhyDigi = $pdo->query("SELECT * FROM about_why_digi_cards ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$abMV      = $pdo->query("SELECT * FROM about_mission_vision WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$abCta     = $pdo->query("SELECT * FROM about_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];

$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$pageTitle = 'Manage About Us Page';
include __DIR__ . '/../views/admin_header.php';

function abBadge(int $active): string {
    return $active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
}
function abTable(array $rows, string $tbl, array $cols): string {
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
        $body .= '<td>' . abBadge((int)($r['is_active'] ?? 1)) . '</td>';
        $body .= "<td>
            <button class='btn btn-sm btn-outline-primary' onclick='abEdit($enc,\"$tbl\")'>Edit</button>
            <form method='post' action='about_us_list_delete.php' class='d-inline' onsubmit='return confirm(\"Delete?\")'>
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

<ul class="nav nav-tabs px-3 pt-2 flex-wrap" id="abTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-stats">Hero Stats</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sec-headers">Section Headers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why-brands">Why Brands</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-who">Who Sub-Cards</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-what">What We Do</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pillars">Approach Pillars</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-why-digi">Why Digifyce</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-mv">Mission & Vision</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-cta">CTA</a></li>
</ul>
<div class="tab-content p-3">

    <!-- HERO -->
    <div class="tab-pane fade show active" id="tab-hero">
        <div class="card border-0"><div class="card-header"><i class="fas fa-home me-2"></i>Hero Section</div>
        <div class="card-body">
        <form method="post" action="about_us_list_save.php">
            <input type="hidden" name="table_name" value="about_hero">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Headline Line 1</label>
                  <input type="text" name="h1_line1" class="form-control" value="<?= htmlspecialchars($abHero['h1_line1']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Headline Line 2 (accent)</label>
                  <input type="text" name="h1_line2_accent" class="form-control" value="<?= htmlspecialchars($abHero['h1_line2_accent']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Headline Line 3</label>
                  <input type="text" name="h1_line3" class="form-control" value="<?= htmlspecialchars($abHero['h1_line3']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Sub Text</label>
                  <textarea name="subtext" class="form-control" rows="3"><?= htmlspecialchars($abHero['subtext']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button 1 Label</label>
                  <input type="text" name="btn1_label" class="form-control" value="<?= htmlspecialchars($abHero['btn1_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 1 URL</label>
                  <input type="text" name="btn1_url" class="form-control" value="<?= htmlspecialchars($abHero['btn1_url']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 Label</label>
                  <input type="text" name="btn2_label" class="form-control" value="<?= htmlspecialchars($abHero['btn2_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button 2 URL</label>
                  <input type="text" name="btn2_url" class="form-control" value="<?= htmlspecialchars($abHero['btn2_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- HERO STATS -->
    <div class="tab-pane fade" id="tab-stats">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-bar me-2"></i>Hero Stats</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_hero_stats')" data-bs-toggle="modal" data-bs-target="#statModal">
                <i class="fas fa-plus me-1"></i>Add Stat</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abStats, 'about_hero_stats', ['sort_order'=>'Sort','badge'=>'Badge','number'=>'Number','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- SECTION HEADERS -->
    <div class="tab-pane fade" id="tab-sec-headers">
        <div class="card border-0"><div class="card-header"><i class="fas fa-heading me-2"></i>Section Headers (edit by slug)</div>
        <div class="card-body">
        <?php foreach ($abSecHdrs as $sh): ?>
        <div class="border rounded p-3 mb-3">
            <h6 class="fw-bold mb-3">Slug: <code><?= htmlspecialchars($sh['slug']) ?></code></h6>
            <form method="post" action="about_us_list_save.php">
                <input type="hidden" name="table_name" value="about_section_headers">
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

    <!-- WHY BRANDS CARDS -->
    <div class="tab-pane fade" id="tab-why-brands">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-layer-group me-2"></i>Why Brands Choose Us Cards</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_why_cards')" data-bs-toggle="modal" data-bs-target="#whyBrandModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abWhy, 'about_why_cards', ['sort_order'=>'Sort','card_number'=>'Card No.','body_text'=>'Body Text']) ?>
        </div></div>
    </div>

    <!-- WHO WE ARE SUB-CARDS -->
    <div class="tab-pane fade" id="tab-who">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users me-2"></i>Who We Are Sub-Cards</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_who_sub_cards')" data-bs-toggle="modal" data-bs-target="#whoModal">
                <i class="fas fa-plus me-1"></i>Add Sub-Card</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abWho, 'about_who_sub_cards', ['sort_order'=>'Sort','badge'=>'Badge','text'=>'Text']) ?>
        </div></div>
    </div>

    <!-- WHAT WE DO -->
    <div class="tab-pane fade" id="tab-what">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-briefcase me-2"></i>What We Do Cards</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_what_we_do')" data-bs-toggle="modal" data-bs-target="#whatModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abWhat, 'about_what_we_do', ['sort_order'=>'Sort','number'=>'Number','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- APPROACH PILLARS -->
    <div class="tab-pane fade" id="tab-pillars">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-columns me-2"></i>Approach Pillars</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_approach_pillars')" data-bs-toggle="modal" data-bs-target="#pillarModal">
                <i class="fas fa-plus me-1"></i>Add Pillar</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abPillars, 'about_approach_pillars', ['sort_order'=>'Sort','number'=>'Number','badge'=>'Badge','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- WHY DIGIFYCE CARDS -->
    <div class="tab-pane fade" id="tab-why-digi">
        <div class="card border-0"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-star me-2"></i>Why Digifyce Cards</span>
            <button class="btn btn-primary btn-sm" onclick="abReset('about_why_digi_cards')" data-bs-toggle="modal" data-bs-target="#whyDigiModal">
                <i class="fas fa-plus me-1"></i>Add Card</button>
        </div>
        <div class="card-body p-0">
            <?= abTable($abWhyDigi, 'about_why_digi_cards', ['sort_order'=>'Sort','badge'=>'Badge','title'=>'Title','description'=>'Description']) ?>
        </div></div>
    </div>

    <!-- MISSION & VISION -->
    <div class="tab-pane fade" id="tab-mv">
        <div class="card border-0"><div class="card-header"><i class="fas fa-compass me-2"></i>Mission & Vision</div>
        <div class="card-body">
        <form method="post" action="about_us_list_save.php">
            <input type="hidden" name="table_name" value="about_mission_vision">
            <div class="row g-3">
                <div class="col-12"><h6 class="fw-semibold">Mission</h6></div>
                <div class="col-md-4"><label class="form-label">Mission Badge</label>
                  <input type="text" name="mission_badge" class="form-control" value="<?= htmlspecialchars($abMV['mission_badge']??'') ?>"></div>
                <div class="col-md-8"><label class="form-label">Mission Title</label>
                  <input type="text" name="mission_title" class="form-control" value="<?= htmlspecialchars($abMV['mission_title']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Mission Text</label>
                  <textarea name="mission_text" class="form-control" rows="4"><?= htmlspecialchars($abMV['mission_text']??'') ?></textarea></div>
                <div class="col-12"><hr><h6 class="fw-semibold">Vision</h6></div>
                <div class="col-md-4"><label class="form-label">Vision Badge</label>
                  <input type="text" name="vision_badge" class="form-control" value="<?= htmlspecialchars($abMV['vision_badge']??'') ?>"></div>
                <div class="col-md-8"><label class="form-label">Vision Title</label>
                  <input type="text" name="vision_title" class="form-control" value="<?= htmlspecialchars($abMV['vision_title']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Vision Text</label>
                  <textarea name="vision_text" class="form-control" rows="4"><?= htmlspecialchars($abMV['vision_text']??'') ?></textarea></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save Mission & Vision</button></div>
            </div>
        </form>
        </div></div>
    </div>

    <!-- CTA -->
    <div class="tab-pane fade" id="tab-cta">
        <div class="card border-0"><div class="card-header"><i class="fas fa-bullhorn me-2"></i>CTA Section</div>
        <div class="card-body">
        <form method="post" action="about_us_list_save.php">
            <input type="hidden" name="table_name" value="about_cta">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Badge Text</label>
                  <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($abCta['badge']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= htmlspecialchars($abCta['heading']??'') ?>"></div>
                <div class="col-12"><label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($abCta['description']??'') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Button Label</label>
                  <input type="text" name="btn_label" class="form-control" value="<?= htmlspecialchars($abCta['btn_label']??'') ?>"></div>
                <div class="col-md-6"><label class="form-label">Button URL</label>
                  <input type="text" name="btn_url" class="form-control" value="<?= htmlspecialchars($abCta['btn_url']??'') ?>"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Save CTA</button></div>
            </div>
        </form>
        </div></div>
    </div>

</div>

<!-- HERO STAT MODAL -->
<div class="modal fade" id="statModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hero Stat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_hero_stats">
        <input type="hidden" name="id" id="stat_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="stat_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="stat_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Badge</label>
          <input type="text" name="badge" id="stat_badge" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Number (e.g. 320+)</label>
          <input type="text" name="number" id="stat_number" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Description</label>
          <input type="text" name="description" id="stat_desc" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHY BRAND MODAL -->
<div class="modal fade" id="whyBrandModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Why Brands Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_why_cards">
        <input type="hidden" name="id" id="wb_id">
        <div class="col-md-4"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="wb_sort" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
          <select name="is_active" id="wb_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-4"><label class="form-label">Card Number (e.g. 01)</label>
          <input type="text" name="card_number" id="wb_num" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Body Text</label>
          <textarea name="body_text" id="wb_body" class="form-control" rows="4"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHO SUB-CARD MODAL -->
<div class="modal fade" id="whoModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Who Sub-Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_who_sub_cards">
        <input type="hidden" name="id" id="who_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="who_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="who_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Badge</label>
          <input type="text" name="badge" id="who_badge" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Text</label>
          <input type="text" name="text" id="who_text" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHAT WE DO MODAL -->
<div class="modal fade" id="whatModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">What We Do Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_what_we_do">
        <input type="hidden" name="id" id="what_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="what_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="what_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="what_number" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="what_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="what_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- PILLAR MODAL -->
<div class="modal fade" id="pillarModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approach Pillar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_approach_pillars">
        <input type="hidden" name="id" id="pil_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="pil_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="pil_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-md-6"><label class="form-label">Number (e.g. 01)</label>
          <input type="text" name="number" id="pil_number" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Badge</label>
          <input type="text" name="badge" id="pil_badge" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="pil_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="pil_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<!-- WHY DIGIFYCE MODAL -->
<div class="modal fade" id="whyDigiModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Why Digifyce Card</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="about_us_list_save.php">
      <div class="modal-body row g-3">
        <input type="hidden" name="table_name" value="about_why_digi_cards">
        <input type="hidden" name="id" id="wd_id">
        <div class="col-md-6"><label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" id="wd_sort" class="form-control" value="0"></div>
        <div class="col-md-6"><label class="form-label">Active</label>
          <select name="is_active" id="wd_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="col-12"><label class="form-label">Badge</label>
          <input type="text" name="badge" id="wd_badge" class="form-control"></div>
        <div class="col-12"><label class="form-label">Title</label>
          <input type="text" name="title" id="wd_title" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Description</label>
          <textarea name="description" id="wd_desc" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div></div>
</div>

<script>
function abReset(tbl) {
    if (tbl === 'about_hero_stats') {
        ['stat_id','stat_badge','stat_number','stat_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('stat_sort').value = 0;
        document.getElementById('stat_active').value = 1;
    } else if (tbl === 'about_why_cards') {
        ['wb_id','wb_num','wb_body'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('wb_sort').value = 0;
        document.getElementById('wb_active').value = 1;
    } else if (tbl === 'about_who_sub_cards') {
        ['who_id','who_badge','who_text'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('who_sort').value = 0;
        document.getElementById('who_active').value = 1;
    } else if (tbl === 'about_what_we_do') {
        ['what_id','what_number','what_title','what_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('what_sort').value = 0;
        document.getElementById('what_active').value = 1;
    } else if (tbl === 'about_approach_pillars') {
        ['pil_id','pil_number','pil_badge','pil_title','pil_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('pil_sort').value = 0;
        document.getElementById('pil_active').value = 1;
    } else if (tbl === 'about_why_digi_cards') {
        ['wd_id','wd_badge','wd_title','wd_desc'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('wd_sort').value = 0;
        document.getElementById('wd_active').value = 1;
    }
}

function abEdit(r, tbl) {
    if (tbl === 'about_hero_stats') {
        document.getElementById('stat_id').value = r.id || '';
        document.getElementById('stat_sort').value = r.sort_order || 0;
        document.getElementById('stat_active').value = r.is_active ?? 1;
        document.getElementById('stat_badge').value = r.badge || '';
        document.getElementById('stat_number').value = r.number || '';
        document.getElementById('stat_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('statModal')).show();
    } else if (tbl === 'about_why_cards') {
        document.getElementById('wb_id').value = r.id || '';
        document.getElementById('wb_sort').value = r.sort_order || 0;
        document.getElementById('wb_active').value = r.is_active ?? 1;
        document.getElementById('wb_num').value = r.card_number || '';
        document.getElementById('wb_body').value = r.body_text || '';
        new bootstrap.Modal(document.getElementById('whyBrandModal')).show();
    } else if (tbl === 'about_who_sub_cards') {
        document.getElementById('who_id').value = r.id || '';
        document.getElementById('who_sort').value = r.sort_order || 0;
        document.getElementById('who_active').value = r.is_active ?? 1;
        document.getElementById('who_badge').value = r.badge || '';
        document.getElementById('who_text').value = r.text || '';
        new bootstrap.Modal(document.getElementById('whoModal')).show();
    } else if (tbl === 'about_what_we_do') {
        document.getElementById('what_id').value = r.id || '';
        document.getElementById('what_sort').value = r.sort_order || 0;
        document.getElementById('what_active').value = r.is_active ?? 1;
        document.getElementById('what_number').value = r.number || '';
        document.getElementById('what_title').value = r.title || '';
        document.getElementById('what_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('whatModal')).show();
    } else if (tbl === 'about_approach_pillars') {
        document.getElementById('pil_id').value = r.id || '';
        document.getElementById('pil_sort').value = r.sort_order || 0;
        document.getElementById('pil_active').value = r.is_active ?? 1;
        document.getElementById('pil_number').value = r.number || '';
        document.getElementById('pil_badge').value = r.badge || '';
        document.getElementById('pil_title').value = r.title || '';
        document.getElementById('pil_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('pillarModal')).show();
    } else if (tbl === 'about_why_digi_cards') {
        document.getElementById('wd_id').value = r.id || '';
        document.getElementById('wd_sort').value = r.sort_order || 0;
        document.getElementById('wd_active').value = r.is_active ?? 1;
        document.getElementById('wd_badge').value = r.badge || '';
        document.getElementById('wd_title').value = r.title || '';
        document.getElementById('wd_desc').value = r.description || '';
        new bootstrap.Modal(document.getElementById('whyDigiModal')).show();
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
