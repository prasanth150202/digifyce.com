<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../views/render_section.php';

$pdo = Database::getInstance();
$id  = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: page_builder'); exit; }

$stmt = $pdo->prepare("SELECT * FROM builder_pages WHERE id = ?");
$stmt->execute([$id]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$page) { header('Location: page_builder'); exit; }

$rows = $pdo->prepare("SELECT section_type, config FROM builder_sections WHERE page_id = ? ORDER BY sort_order ASC");
$rows->execute([$id]);
$sections = $rows->fetchAll(PDO::FETCH_ASSOC);

$pageTitle       = $page['meta_title'] ?: ($page['title'] . ' | Preview');
$pageDescription = $page['meta_desc'] ?: '';
$isDraft         = $page['status'] === 'draft';
$appUrl          = rtrim($_ENV['APP_URL'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?= htmlspecialchars($page['title']) ?></title>
    <!-- Replicate frontend assets -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Space Grotesk', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #05070a; color: #fff; }
    </style>
</head>
<body>

<!-- ── Preview Bar ─────────────────────────────────────────────────────── -->
<div id="previewBar" style="position:fixed;top:0;left:0;right:0;z-index:99999;
    background:#111;border-bottom:2px solid <?= $isDraft ? '#f59e0b' : '#22c55e' ?>;
    padding:0 20px;height:52px;display:flex;align-items:center;justify-content:space-between;
    font-family:'Space Grotesk',sans-serif;box-shadow:0 2px 16px rgba(0,0,0,.5)">

    <div style="display:flex;align-items:center;gap:12px">
        <span style="background:<?= $isDraft ? '#f59e0b' : '#22c55e' ?>;color:#000;
            padding:3px 12px;border-radius:4px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase">
            <?= $isDraft ? 'Draft Preview' : 'Published' ?>
        </span>
        <span style="color:rgba(255,255,255,.8);font-size:14px;font-weight:500">
            <?= htmlspecialchars($page['title']) ?>
        </span>
        <?php if ($page['slug']): ?>
        <span style="color:rgba(255,255,255,.35);font-size:12px;font-family:monospace">
            <?= htmlspecialchars($page['slug']) ?>
        </span>
        <?php endif; ?>
    </div>

    <div style="display:flex;gap:10px;align-items:center">
        <?php if ($isDraft): ?>
        <a href="page_builder_publish?id=<?= $id ?>&status=published"
           style="padding:7px 20px;background:#0066ff;color:#fff;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px"
           onclick="return confirm('Publish this page? It will be visible to the public.')">
            <i class="fas fa-globe" style="font-size:11px"></i> Publish
        </a>
        <?php else: ?>
        <a href="page_builder_publish?id=<?= $id ?>&status=draft"
           style="padding:7px 20px;background:#374151;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px"
           onclick="return confirm('Move this page back to draft?')">
            <i class="fas fa-eye-slash" style="font-size:11px"></i> Unpublish
        </a>
        <?php endif; ?>
        <a href="page_builder_edit?id=<?= $id ?>"
           style="padding:7px 18px;background:rgba(255,255,255,.1);color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="fas fa-pencil-alt" style="font-size:11px"></i> Edit
        </a>
        <button onclick="toggleBar()" title="Hide bar"
            style="width:32px;height:32px;background:rgba(255,255,255,.08);border:none;border-radius:6px;color:#aaa;cursor:pointer;font-size:14px">
            ×
        </button>
    </div>
</div>

<!-- Spacer so content isn't hidden under bar -->
<div id="previewSpacer" style="height:52px"></div>

<?php if (empty($sections)): ?>
<div style="min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;color:rgba(255,255,255,.3)">
    <i class="fas fa-layer-group" style="font-size:48px;margin-bottom:20px;opacity:.3"></i>
    <p style="font-size:18px">This page has no sections yet.</p>
    <a href="page_builder_edit?id=<?= $id ?>"
       style="margin-top:16px;padding:12px 28px;background:#0066ff;color:#fff;border-radius:8px;text-decoration:none;font-weight:700">
        Add Sections
    </a>
</div>
<?php else: ?>
<?php foreach ($sections as $sec):
    $cfg = json_decode($sec['config'], true) ?: [];
    echo render_section($sec['section_type'], $cfg);
endforeach; ?>
<?php endif; ?>

<script>
function toggleBar() {
    const bar    = document.getElementById('previewBar');
    const spacer = document.getElementById('previewSpacer');
    const hidden = bar.style.display === 'none';
    bar.style.display    = hidden ? 'flex'  : 'none';
    spacer.style.height  = hidden ? '52px'  : '0';
}
</script>
</body>
</html>
