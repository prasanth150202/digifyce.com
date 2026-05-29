<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

// List tables: keep MIN(id) per sort_order, delete the rest
$listTables = [
    'creative_pains',
    'creative_pillars',
    'creative_services',
    'creative_steps',
    'creative_metrics',
    'creative_why_cards',
    'commercial_shoot_challenges',
    'commercial_shoot_services',
    'commercial_shoot_steps',
    'commercial_shoot_impacts',
    'ecom_challenges',
    'ecom_approaches',
    'ecom_steps',
    'mktplace_challenges',
    'mktplace_steps',
    'content_solutions',
    // D2C Branding tables
    'd2c_intro_tags',
    'd2c_challenges',
    'd2c_pillars',
    'd2c_solutions',
    'd2c_steps',
    'd2c_metrics',
    'd2c_why_features',
    // Creative Dev
    'cd_video_cards',
    // Marketplace
    'mktplace_hero_icons',
    'mktplace_service_blocks',
    'mktplace_service_block_cards',
    // Content Marketing
    'content_signal_points',
    // About Us
    'about_hero_stats',
    'about_why_cards',
    'about_who_sub_cards',
    'about_what_we_do',
    'about_approach_pillars',
    'about_why_digi_cards',
];

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = Database::getInstance();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($listTables as $tbl) {
        try {
            // Count before
            $before = $pdo->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn();

            // Delete duplicates: keep only the lowest id per sort_order group
            $pdo->exec("
                DELETE FROM `$tbl`
                WHERE id NOT IN (
                    SELECT min_id FROM (
                        SELECT MIN(id) AS min_id FROM `$tbl` GROUP BY sort_order
                    ) AS keep
                )
            ");

            $after = $pdo->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn();
            $removed = $before - $after;

            $results[] = [
                'table'   => $tbl,
                'status'  => 'ok',
                'msg'     => "Before: $before rows → After: $after rows (removed $removed duplicates)",
            ];
        } catch (PDOException $e) {
            $results[] = [
                'table'  => $tbl,
                'status' => 'error',
                'msg'    => $e->getMessage(),
            ];
        }
    }
}

$pageTitle = 'Clean Up Duplicate Data';
include __DIR__ . '/../views/admin_header.php';
?>
<div class="p-4">
    <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-broom me-2"></i>Clean Up Duplicate Seed Data</span>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This removes duplicate rows caused by running the installer multiple times.
                For each table it keeps the <strong>oldest (lowest id)</strong> copy of each <code>sort_order</code> group and deletes the rest.
                Run it <strong>once</strong> — it is safe to re-run if needed.
            </div>

            <?php if (!empty($results)): ?>
                <div class="mb-4">
                    <?php foreach ($results as $r): ?>
                        <div class="alert alert-<?= $r['status'] === 'ok' ? 'success' : 'danger' ?> py-2 mb-2">
                            <?= $r['status'] === 'ok' ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="fas fa-times-circle me-2"></i>' ?>
                            <strong><?= htmlspecialchars($r['table']) ?></strong> — <?= htmlspecialchars($r['msg']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="alert alert-success">
                    <i class="fas fa-rocket me-2"></i><strong>Done!</strong>
                    <a href="dashboard.php" class="alert-link">Go to Dashboard</a> or
                    <a href="install_tables.php" class="alert-link">Back to Installer</a>
                </div>
            <?php endif; ?>

            <form method="post">
                <p class="text-muted mb-3">Tables that will be deduplicated:</p>
                <ul class="list-group mb-4">
                    <?php foreach ($listTables as $t): ?>
                        <li class="list-group-item py-2"><i class="fas fa-table me-2 text-warning"></i><?= htmlspecialchars($t) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-broom me-2"></i>Remove Duplicates Now
                </button>
                <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
