<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$stmt = $pdo->query("SELECT * FROM hero_section WHERE id = 1 LIMIT 1");
$hero = $stmt ? $stmt->fetch() : null;

$pageTitle = 'Edit Hero Section';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-image me-2"></i>Edit Hero Section
    </div>
    <div class="card-body">
        <form method="post" action="hero_save.php" class="row g-3">
            <div class="col-12">
                <label class="form-label">Headline</label>
                <input type="text" name="headline" class="form-control" value="<?= htmlspecialchars($hero['headline'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Subtext</label>
                <textarea name="subtext" class="form-control" rows="3"><?= htmlspecialchars($hero['subtext'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA Label</label>
                <input type="text" name="cta_label" class="form-control" value="<?= htmlspecialchars($hero['cta_label'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA URL</label>
                <input type="text" name="cta_url" class="form-control" value="<?= htmlspecialchars($hero['cta_url'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
