<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$brands = $pdo->query('SELECT * FROM trusted_brands ORDER BY position ASC')->fetchAll();
$maxPosition = (int)$pdo->query('SELECT COALESCE(MAX(position), 0) AS max_pos FROM trusted_brands')->fetchColumn();
$nextPosition = $maxPosition + 1;
$pageTitle = 'Trusted Brands';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="fas fa-handshake me-2"></i>Add Brand Logo</div>
        <form method="post" action="trusted_brand_import.php" class="mb-0">
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-upload me-1"></i>Import From Folder
            </button>
        </form>
    </div>
    <div class="card-body">
        <form method="post" action="trusted_brand_save.php" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Brand Name</label>
                <input type="text" name="name" class="form-control" placeholder="Brand name" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Position</label>
                <input type="number" name="position" class="form-control" value="<?= $nextPosition ?>" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Logo File</label>
                <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp,.gif" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>Brand Logos
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Logo</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brands as $brand): ?>
                        <?php
                            $logoUrl = $brand['logo_url'] ?? '';
                            if ($logoUrl && !preg_match('/^https?:\/\//i', $logoUrl)) {
                                $logoUrl = rtrim($appUrl, '/') . '/' . ltrim($logoUrl, '/');
                            }
                        ?>
                        <tr>
                            <td>
                                <?php if (!empty($logoUrl)): ?>
                                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" style="max-height: 40px; max-width: 120px;" class="img-fluid">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($brand['name']) ?></td>
                            <td><?= (int)$brand['position'] ?></td>
                            <td class="text-end">
                                <a href="trusted_brand_delete.php?id=<?= (int)$brand['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this brand logo?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($brands)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No logos yet. Add a logo or import from folder.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
