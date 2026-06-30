<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pageTitle = 'Blog PDF Leads';

$pdo = Database::getInstance();

// Auto-create table if missing
$pdo->exec("CREATE TABLE IF NOT EXISTS blog_pdf_leads (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    blog_id       INT          DEFAULT NULL,
    blog_slug     VARCHAR(255) DEFAULT NULL,
    blog_title    VARCHAR(500) DEFAULT NULL,
    pdf_filename  VARCHAR(255) DEFAULT NULL,
    pdf_label     VARCHAR(255) DEFAULT NULL,
    email         VARCHAR(255) DEFAULT NULL,
    phone         VARCHAR(50)  DEFAULT NULL,
    custom_fields JSON         DEFAULT NULL,
    ip_address    VARCHAR(45)  DEFAULT NULL,
    user_agent    TEXT,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_blog (blog_id),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Handle delete
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM blog_pdf_leads WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header('Location: blog_pdf_leads.php?deleted=1');
    exit;
}

// Fetch all leads
$leads      = $pdo->query("SELECT * FROM blog_pdf_leads ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$total      = count($leads);
$todayCount = count(array_filter($leads, fn($r) => !empty($r['created_at']) && date('Y-m-d', strtotime($r['created_at'])) === date('Y-m-d')));
$weekCount  = count(array_filter($leads, fn($r) => !empty($r['created_at']) && strtotime($r['created_at']) >= strtotime('-7 days')));

include __DIR__ . '/../views/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3"><i class="fas fa-file-download me-2"></i>Blog PDF Leads</h1>
    <a href="<?= $appUrl ?>/blog_list" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-external-link-alt me-1"></i>View Blogs
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i>Lead deleted.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-white-50 text-uppercase mb-2">Total Leads</h6><h2 class="mb-0"><?= $total ?></h2></div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-white-50 text-uppercase mb-2">Today</h6><h2 class="mb-0"><?= $todayCount ?></h2></div>
                    <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-white-50 text-uppercase mb-2">Last 7 Days</h6><h2 class="mb-0"><?= $weekCount ?></h2></div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <button onclick="exportCSV()" class="btn btn-success btn-sm">
        <i class="fas fa-download me-2"></i>Export to CSV
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">All Blog PDF Leads (<?= $total ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
            <p>No PDF leads yet. Add a PDF button with Lead Capture enabled to a blog post.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="leadsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Blog</th>
                        <th>PDF</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Custom Fields</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($leads as $lead): ?>
                    <?php $cf = !empty($lead['custom_fields']) ? json_decode($lead['custom_fields'], true) : []; ?>
                    <tr>
                        <td class="text-muted small" style="white-space:nowrap">
                            <?php if (!empty($lead['created_at'])): ?>
                                <?= date('M d, Y', strtotime($lead['created_at'])) ?><br>
                                <small><?= date('g:i A', strtotime($lead['created_at'])) ?></small>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($lead['blog_slug'])): ?>
                                <a href="<?= $appUrl ?>/blog/<?= htmlspecialchars($lead['blog_slug']) ?>" target="_blank"
                                   class="text-decoration-none small">
                                    <?= htmlspecialchars($lead['blog_title'] ?: $lead['blog_slug']) ?>
                                    <i class="fas fa-external-link-alt ms-1 opacity-50" style="font-size:10px"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($lead['pdf_label'])): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($lead['pdf_label']) ?></span>
                            <?php elseif (!empty($lead['pdf_filename'])): ?>
                                <small class="text-muted"><?= htmlspecialchars($lead['pdf_filename']) ?></small>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($lead['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($lead['email']) ?>">
                                    <?= htmlspecialchars($lead['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="small">
                            <?= !empty($lead['phone']) ? htmlspecialchars($lead['phone']) : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td>
                            <?php if (!empty($cf)): ?>
                                <div class="small">
                                <?php foreach ($cf as $key => $val): ?>
                                    <div><span class="text-muted"><?= htmlspecialchars($key) ?>:</span> <?= htmlspecialchars($val) ?></div>
                                <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars($lead['ip_address'] ?? '—') ?></td>
                        <td>
                            <a href="?action=delete&id=<?= $lead['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this lead?')">
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
function exportCSV() {
    var rows = [['Date','Blog','PDF Label','Email','Phone','Custom Fields','IP']];
    document.querySelectorAll('#leadsTable tbody tr').forEach(function(tr) {
        var cells = tr.querySelectorAll('td');
        rows.push([
            cells[0].textContent.trim().replace(/\s+/g,' '),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim().replace(/\s+/g,' '),
            cells[6].textContent.trim(),
        ].map(function(v){ return '"' + v.replace(/"/g,'""') + '"'; }));
    });
    var blob = new Blob([rows.map(function(r){return r.join(',');}).join('\n')], {type:'text/csv'});
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'blog_pdf_leads_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    URL.revokeObjectURL(a.href);
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
