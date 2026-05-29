<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();

// Create table if it doesn't exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS job_openings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        division VARCHAR(128),
        location VARCHAR(128),
        description TEXT,
        requirements TEXT,
        position INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {}

$jobs = $pdo->query("SELECT * FROM job_openings ORDER BY position ASC")->fetchAll();

$pageTitle = 'Job Openings';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-briefcase me-2"></i>Job Openings
        </div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
            <i class="fas fa-plus me-1"></i>Add Opening
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Division</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No job openings yet. Create one to get started!</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                            <td><?= htmlspecialchars($job['division'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($job['location'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $job['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $job['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= $job['position'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editJob(<?= htmlspecialchars(json_encode($job)) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="job_openings_delete.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this job opening?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Job Modal -->
<div class="modal fade" id="addJobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Job Opening</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="job_openings_save.php" id="jobForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="jobId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Job Title *</label>
                        <input type="text" name="title" id="jobTitle" class="form-control" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Division</label>
                            <input type="text" name="division" id="jobDivision" class="form-control" placeholder="e.g., Growth Division">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" id="jobLocation" class="form-control" placeholder="e.g., Remote">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="jobDescription" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" id="jobRequirements" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Position Order</label>
                            <input type="number" name="position" id="jobPosition" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" id="jobActive" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Job Opening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJob(job) {
    document.getElementById('jobId').value = job.id;
    document.getElementById('jobTitle').value = job.title;
    document.getElementById('jobDivision').value = job.division || '';
    document.getElementById('jobLocation').value = job.location || '';
    document.getElementById('jobDescription').value = job.description || '';
    document.getElementById('jobRequirements').value = job.requirements || '';
    document.getElementById('jobPosition').value = job.position;
    document.getElementById('jobActive').value = job.is_active;
    document.getElementById('modalTitle').textContent = 'Edit Job Opening';
    
    const modal = new bootstrap.Modal(document.getElementById('addJobModal'));
    modal.show();
}

document.getElementById('addJobModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('jobForm').reset();
    document.getElementById('jobId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Job Opening';
});
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
