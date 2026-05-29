<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$metrics = $pdo->query("SELECT * FROM metrics ORDER BY id ASC")->fetchAll();

$pageTitle = 'Edit Metrics';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-chart-bar me-2"></i>Edit Metrics
    </div>
    <div class="card-body">
        <form method="post" action="metrics_save.php" class="row g-4">
            <?php foreach ($metrics as $i => $m): ?>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <h6 class="mb-3">Metric <?= $i + 1 ?></h6>
                        <input type="hidden" name="ids[]" value="<?= $m['id'] ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Label</label>
                                <input type="text" name="labels[]" class="form-control" value="<?= htmlspecialchars($m['label']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Value</label>
                                <input type="text" name="values[]" class="form-control" value="<?= htmlspecialchars($m['value']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Description</label>
                                <input type="text" name="descriptions[]" class="form-control" value="<?= htmlspecialchars($m['description']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
