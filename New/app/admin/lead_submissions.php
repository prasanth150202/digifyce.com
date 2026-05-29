<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pageTitle = 'Lead Form Submissions';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("DELETE FROM lead_form_submissions WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header('Location: lead_submissions.php?deleted=1');
    exit;
}

// Fetch all lead submissions
$pdo = Database::getInstance();
$stmt = $pdo->query("SELECT * FROM lead_form_submissions ORDER BY created_at DESC");
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$totalLeads = count($leads);
$todayLeads = count(array_filter($leads, function($lead) {
    return date('Y-m-d', strtotime($lead['created_at'])) === date('Y-m-d');
}));
$weekLeads = count(array_filter($leads, function($lead) {
    return strtotime($lead['created_at']) >= strtotime('-7 days');
}));

include __DIR__ . '/../views/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-envelope me-2"></i>Lead Form Submissions
    </h1>
    <a href="<?= $appUrl ?>/leadform.php" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-external-link-alt me-2"></i>View Form
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Lead submission deleted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Total Leads</h6>
                        <h2 class="mb-0"><?= $totalLeads ?></h2>
                    </div>
                    <i class="fas fa-inbox fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Today</h6>
                        <h2 class="mb-0"><?= $todayLeads ?></h2>
                    </div>
                    <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-2">Last 7 Days</h6>
                        <h2 class="mb-0"><?= $weekLeads ?></h2>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leads Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">All Submissions (<?= $totalLeads ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
            <p>No lead submissions yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Budget</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td class="text-muted small">
                            <?= date('M d, Y', strtotime($lead['created_at'])) ?><br>
                            <small><?= date('g:i A', strtotime($lead['created_at'])) ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($lead['full_name']) ?></strong>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($lead['email']) ?>">
                                <?= htmlspecialchars($lead['email']) ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($lead['phone']): ?>
                                <a href="tel:<?= htmlspecialchars($lead['phone']) ?>">
                                    <?= htmlspecialchars($lead['phone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $lead['company'] ? htmlspecialchars($lead['company']) : '<span class="text-muted">-</span>' ?></td>
                        <td>
                            <?php if ($lead['budget']): ?>
                                <span class="badge bg-success"><?= htmlspecialchars($lead['budget']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="viewLead(<?= htmlspecialchars(json_encode($lead)) ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="?action=delete&id=<?= $lead['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this lead submission?')">
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

<!-- View Lead Modal -->
<div class="modal fade" id="viewLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details</h5>
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
                        <label class="text-muted small">Phone</label>
                        <p id="modal-phone"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Company</label>
                        <p id="modal-company"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Budget</label>
                        <p id="modal-budget"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Website</label>
                        <p id="modal-website"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Message</label>
                    <p class="border p-3 bg-light" id="modal-message"></p>
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
function viewLead(lead) {
    document.getElementById('modal-name').textContent = lead.full_name;
    document.getElementById('modal-email').textContent = lead.email;
    document.getElementById('modal-phone').textContent = lead.phone || '-';
    document.getElementById('modal-company').textContent = lead.company || '-';
    document.getElementById('modal-budget').textContent = lead.budget || '-';
    document.getElementById('modal-website').innerHTML = lead.website ? 
        `<a href="${lead.website}" target="_blank">${lead.website}</a>` : '-';
    document.getElementById('modal-message').textContent = lead.message;
    document.getElementById('modal-ip').textContent = lead.ip_address || '-';
    document.getElementById('modal-date').textContent = new Date(lead.created_at).toLocaleString();
    
    new bootstrap.Modal(document.getElementById('viewLeadModal')).show();
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
