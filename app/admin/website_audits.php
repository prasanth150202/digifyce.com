<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/AuditSpawner.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pageTitle = 'Website Audits';

$pdo = Database::getInstance();

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM website_audits WHERE id = ?");
    $stmt->execute([(int) $_GET['id']]);
    header('Location: website_audits.php?deleted=1');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'retry' && isset($_GET['id'])) {
    $auditId = (int) $_GET['id'];
    $stmt = $pdo->prepare("UPDATE website_audits SET status = 'pending', attempts = 0, error_message = NULL WHERE id = ? AND status = 'failed'");
    $stmt->execute([$auditId]);
    if ($stmt->rowCount() === 1) {
        AuditSpawner::spawn($auditId);
    }
    header('Location: website_audits.php?retried=1');
    exit;
}

$audits = $pdo->query(
    "SELECT wa.*, l.full_name, l.email
     FROM website_audits wa
     JOIN lead_form_submissions l ON l.id = wa.lead_id
     ORDER BY wa.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$totalAudits = count($audits);
$completedAudits = count(array_filter($audits, fn($a) => $a['status'] === 'completed'));
$failedAudits = count(array_filter($audits, fn($a) => $a['status'] === 'failed'));
$pendingAudits = count(array_filter($audits, fn($a) => in_array($a['status'], ['pending', 'running'], true)));

include __DIR__ . '/../views/admin_header.php';
?>

<style>
    .audit-table th,
    .audit-table td {
        padding: 10px 12px;
        white-space: normal;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-magnifying-glass-chart me-2"></i>Website Audits
    </h1>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Audit record deleted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['retried'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Audit re-queued.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase mb-2">Total</h6>
                <h2 class="mb-0"><?= $totalAudits ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase mb-2">Completed</h6>
                <h2 class="mb-0"><?= $completedAudits ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase mb-2">In Progress</h6>
                <h2 class="mb-0"><?= $pendingAudits ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase mb-2">Failed</h6>
                <h2 class="mb-0"><?= $failedAudits ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">All Audits (<?= $totalAudits ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($audits)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-magnifying-glass-chart fa-3x mb-3 opacity-25"></i>
            <p>No website audits yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 audit-table">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Lead</th>
                        <th>Website</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Keep In Touch</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audits as $audit): ?>
                    <tr>
                        <td class="text-muted small text-nowrap">
                            <?= date('M d, Y', strtotime($audit['created_at'])) ?><br>
                            <small><?= date('g:i A', strtotime($audit['created_at'])) ?></small>
                        </td>
                        <td>
                            <div class="fw-bold text-truncate" style="max-width: 200px;"><?= htmlspecialchars($audit['full_name']) ?></div>
                            <div class="small text-truncate" style="max-width: 200px;">
                                <a href="mailto:<?= htmlspecialchars($audit['email']) ?>"><?= htmlspecialchars($audit['email']) ?></a>
                            </div>
                        </td>
                        <td>
                            <a class="text-truncate d-inline-block" style="max-width: 220px;" href="<?= htmlspecialchars($audit['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($audit['url']) ?></a>
                        </td>
                        <td>
                            <?php
                                $badgeClass = [
                                    'pending' => 'bg-secondary',
                                    'running' => 'bg-info',
                                    'completed' => 'bg-success',
                                    'failed' => 'bg-danger',
                                ][$audit['status']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($audit['status'])) ?></span>
                        </td>
                        <td>
                            <?php if ($audit['score'] !== null): ?>
                                <span class="fw-bold"><?= (int) $audit['score'] ?>/100</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($audit['keep_in_touch'])): ?>
                                <span class="badge bg-success"><i class="fas fa-handshake me-1"></i>Yes</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="website_audit_view.php?id=<?= $audit['id'] ?>">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($audit['status'] === 'failed'): ?>
                                <a class="btn btn-sm btn-outline-warning" href="?action=retry&id=<?= $audit['id'] ?>" onclick="return confirm('Retry this audit?')">
                                    <i class="fas fa-rotate-right"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?= $audit['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this audit record?')">
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

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
