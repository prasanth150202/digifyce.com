<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

$studies = $pdo->query('SELECT * FROM case_studies ORDER BY position ASC')->fetchAll();
$maxPosition = (int) $pdo->query('SELECT COALESCE(MAX(position), 0) AS max_pos FROM case_studies')->fetchColumn();
$nextPosition = $maxPosition + 1;

$pageTitle = 'Case Studies';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 mb-4">
    <div class="card-header">
        <i class="fas fa-folder-open me-2"></i>Add Case Study
    </div>
    <div class="card-body">
        <div class="alert alert-info small mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Upload the source document for a past client win (PDF, Word, PowerPoint, or Excel). The website-audit
            AI reads these to reference real Digifyce work when it's genuinely relevant to a prospective client's
            business. Supported formats: <strong>.pdf, .docx, .pptx, .xlsx</strong> — older binary
            <code>.doc</code>/<code>.ppt</code>/<code>.xls</code> files aren't supported; save/export to one of the
            formats above first.
        </div>
        <form method="post" action="case_study_save.php" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. D2C Skincare Brand — Conversion Overhaul" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Short Description <span class="text-muted">(optional)</span></label>
                <input type="text" name="description" class="form-control" placeholder="One line about what this case study covers">
            </div>
            <div class="col-md-2">
                <label class="form-label">Position</label>
                <input type="number" name="position" class="form-control" value="<?= $nextPosition ?>" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Document</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.docx,.pptx,.xlsx" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>Case Studies
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Document</th>
                        <th>Position</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studies as $study): ?>
                        <tr>
                            <td><?= htmlspecialchars($study['title']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($study['description'] ?: '-') ?></td>
                            <td>
                                <?php if (!empty($study['file_path'])): ?>
                                    <a href="<?= htmlspecialchars(rtrim($appUrl, '/') . '/' . ltrim($study['file_path'], '/')) ?>" target="_blank" rel="noopener">
                                        <i class="fas fa-file-arrow-down me-1"></i><?= htmlspecialchars(basename($study['file_path'])) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No document</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $study['position'] ?></td>
                            <td class="text-end">
                                <a href="case_study_delete.php?id=<?= (int) $study['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this case study?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($studies)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No case studies yet. Upload one above.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
