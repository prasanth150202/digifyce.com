<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-file-alt text-primary fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Blogs</h5>
                </div>
                <p class="card-text text-muted small">Create, edit, and manage blog posts.</p>
                <a href="blogs.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>Manage Blogs
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-folder text-success fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Categories</h5>
                </div>
                <p class="card-text text-muted small">Organize blog posts by category.</p>
                <a href="categories.php" class="btn btn-success btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>Manage Categories
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-tags text-warning fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Tags</h5>
                </div>
                <p class="card-text text-muted small">Manage tags for search and filtering.</p>
                <a href="tags.php" class="btn btn-warning btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>Manage Tags
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="fas fa-users text-info fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Authors</h5>
                </div>
                <p class="card-text text-muted small">Manage blog authors and bios.</p>
                <a href="authors.php" class="btn btn-info btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>Manage Authors
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fas fa-envelope text-danger fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Lead Forms</h5>
                </div>
                <p class="card-text text-muted small">Manage all lead form submissions.</p>
                <a href="lead_submissions.php" class="btn btn-danger btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>View Submissions
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-file-pdf text-warning fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">PDF Leads</h5>
                </div>
                <p class="card-text text-muted small">View PDF email download requests.</p>
                <a href="pdf_email_leads.php" class="btn btn-warning btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>View Leads
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="fas fa-user-tie text-secondary fa-lg"></i>
                    </div>
                    <h5 class="card-title ms-3 mb-0">Applications</h5>
                </div>
                <p class="card-text text-muted small">Review job applications & CVs.</p>
                <a href="job_applications.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-2"></i>View Applications
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-cog me-2"></i>Site Configuration
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="navigation.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-bars me-2"></i>Navigation Menu</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="hero_edit.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-image me-2"></i>Hero Section</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="metrics_edit.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-bar me-2"></i>Metrics</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="site_settings.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-sliders-h me-2"></i>Site Settings</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="trusted_brands.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-handshake me-2"></i>Trusted Brands</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="job_openings.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-briefcase me-2"></i>Job Openings</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0">
            <div class="card-header bg-success text-white">
                <i class="fas fa-info-circle me-2"></i>Quick Info
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Welcome to the Digifyce Admin Dashboard. Use the navigation menu on the left to manage your website content.</p>
                <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip:</strong> All changes are saved to the database immediately.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
