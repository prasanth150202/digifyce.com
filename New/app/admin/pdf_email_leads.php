<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pageTitle = 'PDF Email Leads';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("DELETE FROM pdf_email_leads WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header('Location: pdf_email_leads.php?deleted=1');
    exit;
}

// Fetch all PDF email leads
$pdo = Database::getInstance();
$stmt = $pdo->query("SELECT * FROM pdf_email_leads ORDER BY created_at DESC");
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
        <i class="fas fa-file-pdf me-2"></i>PDF Email Leads
    </h1>
    <a href="<?= $appUrl ?>/#strategy-matrix" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-external-link-alt me-2"></i>View Form
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Email lead deleted successfully.
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
                    <i class="fas fa-envelope fa-2x opacity-50"></i>
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

<!-- Export Button -->
<div class="mb-3">
    <button onclick="exportToCSV()" class="btn btn-success btn-sm">
        <i class="fas fa-download me-2"></i>Export to CSV
    </button>
</div>

<!-- Leads Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">All Email Leads (<?= $totalLeads ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
            <p>No PDF email leads yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="leadsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Email Address</th>
                        <th>Source</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td class="text-muted">
                            <?= date('M d, Y', strtotime($lead['created_at'])) ?><br>
                            <small><?= date('g:i A', strtotime($lead['created_at'])) ?></small>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($lead['email']) ?>">
                                <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($lead['email']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($lead['source']) ?></span>
                        </td>
                        <td class="text-muted small">
                            <?= htmlspecialchars($lead['ip_address']) ?>
                        </td>
                        <td>
                            <a href="?action=delete&id=<?= $lead['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this email lead?')">
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

<script>
function exportToCSV() {
    const table = document.getElementById('leadsTable');
    let csv = [];
    
    // Headers
    csv.push(['Date', 'Email', 'Source', 'IP Address'].join(','));
    
    // Data rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('td');
        const dateText = cols[0].textContent.trim().replace(/\n/g, ' ');
        const email = cols[1].textContent.trim().replace('✉ ', '');
        const source = cols[2].textContent.trim();
        const ip = cols[3].textContent.trim();
        csv.push([dateText, email, source, ip].join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pdf_email_leads_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
