<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqlFiles = [
    'service_tables.sql',
    'technology_tables.sql',
    'testimonial_tables.sql',
    'products_tables.sql',
    'd2c_branding_tables.sql',
    'creative_development_tables.sql',
    'commercial_shoot_tables.sql',
    'ecommerce_marketing_tables.sql',
    'marketplace_management_tables.sql',
    'content_marketing_tables.sql',
    'performance_marketing_tables.sql',
    'performance_marketing_hero_tables.sql',
    'brand_shoot_extra_tables.sql',
    'creative_dev_extra_tables.sql',
    'ecom_extra_tables.sql',
    'marketplace_extra_tables.sql',
    'content_marketing_extra_tables.sql',
    'd2c_extra_tables.sql',
    'cd_section_headers.sql',
    'ecom_section_headers.sql',
    'mktplace_section_headers.sql',
    'content_section_headers.sql',
    'about_us_tables.sql',
];

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($sqlFiles as $file) {
        $path = __DIR__ . '/../../app/sql/' . $file;
        if (!file_exists($path)) {
            $results[] = ['file' => $file, 'status' => 'error', 'msg' => 'File not found'];
            continue;
        }
        $sql = file_get_contents($path);
        // Strip single-line SQL comments before splitting so a leading comment
        // doesn't get bundled with the first CREATE TABLE and cause it to be skipped
        $sql = preg_replace('/^[ \t]*--[^\n]*$/m', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $errors = [];
        foreach ($statements as $stmt) {
            if (empty($stmt)) continue;
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }
        $results[] = [
            'file'   => $file,
            'status' => empty($errors) ? 'ok' : 'warning',
            'msg'    => empty($errors) ? 'All statements executed successfully' : implode('<br>', $errors),
        ];
    }
}

$pageTitle = 'Install Database Tables';
include __DIR__ . '/../views/admin_header.php';
?>
<div class="p-4">
    <div class="card border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-database me-2"></i>Install / Refresh Database Tables</span>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                This will run all <strong><?= count($sqlFiles) ?></strong> SQL files using <code>CREATE TABLE IF NOT EXISTS</code> and <code>INSERT IGNORE</code>.
                Existing tables and data will <strong>not</strong> be overwritten.
            </div>

            <?php if (!empty($results)): ?>
                <div class="mb-4">
                    <?php foreach ($results as $r): ?>
                        <div class="alert alert-<?= $r['status'] === 'ok' ? 'success' : ($r['status'] === 'warning' ? 'warning' : 'danger') ?> py-2 mb-2">
                            <?php if ($r['status'] === 'ok'): ?>
                                <i class="fas fa-check-circle me-2"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($r['file']) ?></strong> — <?= $r['msg'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php $allOk = !array_filter($results, fn($r) => $r['status'] === 'error'); ?>
                <?php if ($allOk): ?>
                    <div class="alert alert-success"><i class="fas fa-rocket me-2"></i><strong>Done!</strong> All tables are ready. <a href="dashboard.php" class="alert-link">Go to Dashboard</a></div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post">
                <p class="text-muted mb-3">SQL files to be executed:</p>
                <ul class="list-group mb-4">
                    <?php foreach ($sqlFiles as $f): ?>
                        <li class="list-group-item py-2"><i class="fas fa-file-code me-2 text-primary"></i><?= htmlspecialchars($f) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-play me-2"></i>Run All SQL Files Now
                </button>
                <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../views/admin_footer.php'; ?>
