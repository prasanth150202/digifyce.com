<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queries = [
    // D2C Branding
    'd2c_metrics (label)'              => "DELETE FROM d2c_metrics WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_metrics GROUP BY label) t)",
    'd2c_intro_tags (tag_name)'        => "DELETE FROM d2c_intro_tags WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_intro_tags GROUP BY tag_name) t)",
    'd2c_challenges (title)'           => "DELETE FROM d2c_challenges WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_challenges GROUP BY title) t)",
    'd2c_pillars (name)'               => "DELETE FROM d2c_pillars WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_pillars GROUP BY name) t)",
    'd2c_solutions (name)'             => "DELETE FROM d2c_solutions WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_solutions GROUP BY name) t)",
    'd2c_steps (title)'                => "DELETE FROM d2c_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_steps GROUP BY title) t)",
    'd2c_why_features (title)'         => "DELETE FROM d2c_why_features WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM d2c_why_features GROUP BY title) t)",

    // Brand Shoot
    'commercial_shoot_challenges (text)'   => "DELETE FROM commercial_shoot_challenges WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM commercial_shoot_challenges GROUP BY text) t)",
    'commercial_shoot_services (heading)'  => "DELETE FROM commercial_shoot_services WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM commercial_shoot_services GROUP BY heading) t)",
    'commercial_shoot_steps (title)'       => "DELETE FROM commercial_shoot_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM commercial_shoot_steps GROUP BY title) t)",
    'commercial_shoot_impacts (title)'     => "DELETE FROM commercial_shoot_impacts WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM commercial_shoot_impacts GROUP BY title) t)",

    // Creative Dev
    'creative_pains (text)'            => "DELETE FROM creative_pains WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_pains GROUP BY text) t)",
    'creative_pillars (name)'          => "DELETE FROM creative_pillars WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_pillars GROUP BY name) t)",
    'creative_services (name)'         => "DELETE FROM creative_services WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_services GROUP BY name) t)",
    'creative_steps (title)'           => "DELETE FROM creative_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_steps GROUP BY title) t)",
    'creative_metrics (label)'         => "DELETE FROM creative_metrics WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_metrics GROUP BY label) t)",
    'creative_why_cards (title)'       => "DELETE FROM creative_why_cards WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM creative_why_cards GROUP BY title) t)",

    // E-com Marketing
    'ecom_challenges (title)'          => "DELETE FROM ecom_challenges WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM ecom_challenges GROUP BY title) t)",
    'ecom_approaches (title)'          => "DELETE FROM ecom_approaches WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM ecom_approaches GROUP BY title) t)",
    'ecom_steps (title)'               => "DELETE FROM ecom_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM ecom_steps GROUP BY title) t)",

    // Marketplace Management
    'mktplace_challenges (title)'      => "DELETE FROM mktplace_challenges WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM mktplace_challenges GROUP BY title) t)",
    'mktplace_steps (title)'           => "DELETE FROM mktplace_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM mktplace_steps GROUP BY title) t)",

    // Content Marketing
    'content_solutions (title)'        => "DELETE FROM content_solutions WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM content_solutions GROUP BY title) t)",

    // Performance Marketing
    'pm_challenges (text)'             => "DELETE FROM pm_challenges WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_challenges GROUP BY text) t)",
    'pm_approaches (heading)'          => "DELETE FROM pm_approaches WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_approaches GROUP BY heading) t)",
    'pm_services (heading)'            => "DELETE FROM pm_services WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_services GROUP BY heading) t)",
    'pm_leadgen_tabs (title)'          => "DELETE FROM pm_leadgen_tabs WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_leadgen_tabs GROUP BY title) t)",
    'pm_seo_panels (title)'            => "DELETE FROM pm_seo_panels WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_seo_panels GROUP BY title) t)",
    'pm_steps (heading)'               => "DELETE FROM pm_steps WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_steps GROUP BY heading) t)",
    'pm_impacts (heading)'             => "DELETE FROM pm_impacts WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_impacts GROUP BY heading) t)",
    'pm_hero_metrics (text)'           => "DELETE FROM pm_hero_metrics WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_hero_metrics GROUP BY text) t)",
    'pm_benchmark_groups (industry_label)' => "DELETE FROM pm_benchmark_groups WHERE id NOT IN (SELECT min_id FROM (SELECT MIN(id) as min_id FROM pm_benchmark_groups GROUP BY industry_label) t)",
];

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($queries as $label => $sql) {
        try {
            $affected = $pdo->exec($sql);
            $results[] = ['label' => $label, 'status' => 'ok', 'affected' => $affected];
        } catch (PDOException $e) {
            $results[] = ['label' => $label, 'status' => 'error', 'msg' => $e->getMessage()];
        }
    }
}

$pageTitle = 'Fix Duplicate Data';
include __DIR__ . '/../views/admin_header.php';
?>
<div class="p-4">
    <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-broom me-2"></i>Fix Duplicate Database Entries</span>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This will <strong>delete duplicate rows</strong> from all content tables, keeping only the first (lowest ID) copy of each entry.
                Run this once to fix data that was inserted multiple times by running migrations more than once.
            </div>

            <?php if (!empty($results)): ?>
                <div class="mb-4">
                    <?php $totalDeleted = 0; ?>
                    <?php foreach ($results as $r): ?>
                        <?php if ($r['status'] === 'ok'): $totalDeleted += $r['affected']; ?>
                            <div class="alert alert-<?= $r['affected'] > 0 ? 'success' : 'secondary' ?> py-2 mb-1">
                                <i class="fas fa-<?= $r['affected'] > 0 ? 'check-circle' : 'minus-circle' ?> me-2"></i>
                                <strong><?= htmlspecialchars($r['label']) ?></strong>
                                — <?= $r['affected'] > 0 ? $r['affected'] . ' duplicate(s) removed' : 'no duplicates found' ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger py-2 mb-1">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong><?= htmlspecialchars($r['label']) ?></strong> — <?= htmlspecialchars($r['msg']) ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="alert alert-success">
                    <i class="fas fa-rocket me-2"></i>
                    <strong>Done!</strong> <?= $totalDeleted ?> duplicate row(s) removed in total.
                    <a href="dashboard.php" class="alert-link ms-2">Go to Dashboard</a>
                </div>
            <?php endif; ?>

            <form method="post">
                <p class="text-muted mb-3">Tables to be deduplicated (<?= count($queries) ?> total):</p>
                <ul class="list-group mb-4" style="max-height:400px;overflow-y:auto;">
                    <?php foreach (array_keys($queries) as $label): ?>
                        <li class="list-group-item py-2"><i class="fas fa-table me-2 text-warning"></i><?= htmlspecialchars($label) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-warning text-white">
                    <i class="fas fa-broom me-2"></i>Remove All Duplicates Now
                </button>
                <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
