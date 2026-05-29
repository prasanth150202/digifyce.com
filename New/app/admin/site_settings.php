<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$settings = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
$settingsMap = [];
foreach ($settings as $row) {
    $settingsMap[$row['setting_key']] = $row['setting_value'];
}

$siteLogo = $settingsMap['site_logo'] ?? '';
$siteFavicon = $settingsMap['site_favicon'] ?? '';
$footerLogo = $settingsMap['footer_logo'] ?? '';
$footerDescription = $settingsMap['footer_description'] ?? '';
$footerCopyright = $settingsMap['footer_copyright'] ?? '© ' . date('Y') . ' Digifyce Performance. All rights reserved.';
$homeCtaLabel = $settingsMap['home_cta_label'] ?? 'Book Your Audit';
$homeCtaUrl = $settingsMap['home_cta_url'] ?? '#';
$homeCtaNote = $settingsMap['home_cta_note'] ?? 'Only 3 slots remaining for Q4';
$serviceCtaHeading = $settingsMap['service_cta_heading'] ?? 'Ready to Scale Your Digital Infrastructure?';
$serviceCtaText = $settingsMap['service_cta_text'] ?? "Request a technical audit. We'll analyze your stack, your CAC/LTV benchmarks, and your competitor's marketplace velocity.";
$serviceCtaPrimaryLabel = $settingsMap['service_cta_primary_label'] ?? 'Request Audit';
$serviceCtaPrimaryUrl = $settingsMap['service_cta_primary_url'] ?? '#';
$serviceCtaSecondaryLabel = $settingsMap['service_cta_secondary_label'] ?? 'View Methodology';
$serviceCtaSecondaryUrl = $settingsMap['service_cta_secondary_url'] ?? '#';

$pageTitle = 'Site Settings';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 mb-4">
    <div class="card-header">
        <i class="fas fa-sliders-h me-2"></i>Branding
    </div>
    <div class="card-body">
        <form method="post" action="site_settings_save.php" enctype="multipart/form-data" class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Logo</label>
                <?php if (!empty($siteLogo)): ?>
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($siteLogo, '/')) ?>" alt="Site Logo" style="max-height: 60px;" class="img-fluid">
                    </div>
                <?php endif; ?>
                <input type="file" name="site_logo" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                <div class="form-text">Recommended: SVG or PNG with transparent background.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Favicon</label>
                <?php if (!empty($siteFavicon)): ?>
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($siteFavicon, '/')) ?>" alt="Favicon" style="max-height: 40px;" class="img-fluid">
                    </div>
                <?php endif; ?>
                <input type="file" name="site_favicon" class="form-control" accept=".png,.jpg,.jpeg,.svg,.ico">
                <div class="form-text">Recommended: 32x32 PNG or ICO.</div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-header">
        <i class="fas fa-shoe-prints me-2"></i>Footer Content
    </div>
    <div class="card-body">
        <form method="post" action="site_settings_save.php" enctype="multipart/form-data" class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Footer Logo</label>
                <?php if (!empty($footerLogo)): ?>
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($footerLogo, '/')) ?>" alt="Footer Logo" style="max-height: 50px;" class="img-fluid">
                    </div>
                <?php endif; ?>
                <input type="file" name="footer_logo" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                <div class="form-text">Optional footer logo. Leave empty to use site logo.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Copyright Text</label>
                <input type="text" name="footer_copyright" class="form-control" value="<?= htmlspecialchars($footerCopyright) ?>" placeholder="© 2024 Company Name. All rights reserved.">
            </div>
            <div class="col-12">
                <label class="form-label">Footer Description</label>
                <textarea name="footer_description" class="form-control" rows="3" placeholder="Premium performance agency description..."><?= htmlspecialchars($footerDescription) ?></textarea>
                <div class="form-text">Short description shown in footer.</div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Footer Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-header">
        <i class="fas fa-bullhorn me-2"></i>CTA Settings
    </div>
    <div class="card-body">
        <form method="post" action="site_settings_save.php" class="row g-4">
            <div class="col-12">
                <h6 class="text-uppercase text-muted">Home CTA</h6>
            </div>
            <div class="col-md-6">
                <label class="form-label">Home CTA Label</label>
                <input type="text" name="home_cta_label" class="form-control" value="<?= htmlspecialchars($homeCtaLabel) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Home CTA URL</label>
                <input type="text" name="home_cta_url" class="form-control" value="<?= htmlspecialchars($homeCtaUrl) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Home CTA Note</label>
                <input type="text" name="home_cta_note" class="form-control" value="<?= htmlspecialchars($homeCtaNote) ?>">
            </div>

            <div class="col-12 mt-2">
                <h6 class="text-uppercase text-muted">Service Page CTA</h6>
            </div>
            <div class="col-12">
                <label class="form-label">Service CTA Heading</label>
                <input type="text" name="service_cta_heading" class="form-control" value="<?= htmlspecialchars($serviceCtaHeading) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Service CTA Text</label>
                <textarea name="service_cta_text" class="form-control" rows="3"><?= htmlspecialchars($serviceCtaText) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Primary CTA Label</label>
                <input type="text" name="service_cta_primary_label" class="form-control" value="<?= htmlspecialchars($serviceCtaPrimaryLabel) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Primary CTA URL</label>
                <input type="text" name="service_cta_primary_url" class="form-control" value="<?= htmlspecialchars($serviceCtaPrimaryUrl) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Secondary CTA Label</label>
                <input type="text" name="service_cta_secondary_label" class="form-control" value="<?= htmlspecialchars($serviceCtaSecondaryLabel) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Secondary CTA URL</label>
                <input type="text" name="service_cta_secondary_url" class="form-control" value="<?= htmlspecialchars($serviceCtaSecondaryUrl) ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save CTA Settings</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
