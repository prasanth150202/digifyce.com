<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pageTitle = 'Job Applications';

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['id']) && isset($_POST['status'])) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], (int)$_POST['id']]);
    header('Location: job_applications.php?updated=1');
    exit;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo = Database::getInstance();
    
    // Get CV filepath before deleting
    $stmt = $pdo->prepare("SELECT cv_filepath FROM job_applications WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete CV file if exists
    if ($app && $app['cv_filepath']) {
        $filePath = __DIR__ . '/../../' . $app['cv_filepath'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // Delete database record
    $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    
    header('Location: job_applications.php?deleted=1');
    exit;
}

// Fetch all job applications
$pdo = Database::getInstance();
$stmt = $pdo->query("
    SELECT ja.*, jo.title as job_title 
    FROM job_applications ja 
    LEFT JOIN job_openings jo ON ja.job_opening_id = jo.id 
    ORDER BY ja.created_at DESC
");
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$totalApps = count($applications);
$pendingApps = count(array_filter($applications, fn($app) => $app['status'] === 'pending'));
$reviewedApps = count(array_filter($applications, fn($app) => $app['status'] === 'reviewed'));
$shortlistedApps = count(array_filter($applications, fn($app) => $app['status'] === 'shortlisted'));

include __DIR__ . '/../views/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-briefcase me-2"></i>Job Applications
    </h1>
    <a href="<?= $appUrl ?>/careers.php#application-section" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-external-link-alt me-2"></i>View Form
    </a>
</div>

<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Application status updated successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Application deleted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Total</h6>
                        <h2 class="mb-0"><?= $totalApps ?></h2>
                    </div>
                    <i class="fas fa-inbox fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Pending</h6>
                        <h2 class="mb-0"><?= $pendingApps ?></h2>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Reviewed</h6>
                        <h2 class="mb-0"><?= $reviewedApps ?></h2>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Shortlisted</h6>
                        <h2 class="mb-0"><?= $shortlistedApps ?></h2>
                    </div>
                    <i class="fas fa-star fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Applications Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">All Applications (<?= $totalApps ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($applications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
            <p>No job applications yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Portfolio</th>
                        <th>Job Position</th>
                        <th>CV</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td class="text-muted small">
                            <?= date('M d, Y', strtotime($app['created_at'])) ?><br>
                            <small><?= date('g:i A', strtotime($app['created_at'])) ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($app['email']) ?>">
                                <?= htmlspecialchars($app['email']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= htmlspecialchars($app['portfolio_url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                        <td>
                            <?php if ($app['job_title']): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($app['job_title']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-info">Spontaneous</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['cv_filepath']): ?>
                                <a href="<?= $appUrl . '/' . htmlspecialchars($app['cv_filepath']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> CV
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                <select name="status" class="form-select form-select-sm status-select" 
                                        onchange="this.form.submit()" style="min-width: 120px;">
                                    <option value="pending" <?= $app['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="reviewed" <?= $app['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                    <option value="shortlisted" <?= $app['status'] === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                                    <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="viewApplication(<?= htmlspecialchars(json_encode($app)) ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="?action=delete&id=<?= $app['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this application? The CV file will also be deleted.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Application Modal -->
<div class="modal fade" id="viewApplicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Full Name</label>
                        <p class="fw-bold" id="modal-name"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p id="modal-email"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Portfolio/LinkedIn URL</label>
                        <p id="modal-portfolio"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Job Position</label>
                        <p id="modal-job"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Cover Letter</label>
                    <div class="border p-3 bg-light" id="modal-cover" style="white-space: pre-wrap;"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">CV/Resume</label>
                        <p id="modal-cv"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Status</label>
                        <p id="modal-status"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-muted small">IP Address</label>
                        <p id="modal-ip"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Submitted At</label>
                        <p id="modal-date"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewApplication(app) {
    document.getElementById('modal-name').textContent = app.full_name;
    document.getElementById('modal-email').textContent = app.email;
    document.getElementById('modal-portfolio').innerHTML = 
        `<a href="${app.portfolio_url}" target="_blank">${app.portfolio_url}</a>`;
    document.getElementById('modal-job').textContent = app.job_title || 'Spontaneous Application';
    document.getElementById('modal-cover').textContent = app.cover_letter;
    document.getElementById('modal-cv').innerHTML = app.cv_filepath ? 
        `<a href="<?= $appUrl ?>/${app.cv_filepath}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-download me-2"></i>${app.cv_filename}</a>` : 
        'No CV uploaded';
    
    const statusBadges = {
        pending: '<span class="badge bg-warning">Pending</span>',
        reviewed: '<span class="badge bg-info">Reviewed</span>',
        shortlisted: '<span class="badge bg-success">Shortlisted</span>',
        rejected: '<span class="badge bg-danger">Rejected</span>'
    };
    document.getElementById('modal-status').innerHTML = statusBadges[app.status] || app.status;
    
    document.getElementById('modal-ip').textContent = app.ip_address || '-';
    document.getElementById('modal-date').textContent = new Date(app.created_at).toLocaleString();
    
    new bootstrap.Modal(document.getElementById('viewApplicationModal')).show();
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
