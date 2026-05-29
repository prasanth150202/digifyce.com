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

<!-- Webpage Management -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-globe me-2"></i>Webpage Content Management
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-tools fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Services</h6>
                                <p class="card-text text-muted small">Manage services page content.</p>
                                <a href="page_service.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-microchip fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Technology</h6>
                                <p class="card-text text-muted small">Manage technology page content.</p>
                                <a href="page_technology.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Testimonials</h6>
                                <p class="card-text text-muted small">Manage testimonials page content.</p>
                                <a href="page_testimonial.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Products</h6>
                                <p class="card-text text-muted small">Manage products page content.</p>
                                <a href="page_products.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">D2C Branding</h6>
                                <p class="card-text text-muted small">Manage D2C branding page content.</p>
                                <a href="page_d2c_branding.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-camera fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Commercial Shoot</h6>
                                <p class="card-text text-muted small">Manage brand shoot page content.</p>
                                <a href="page_commercial_shoot.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-palette fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Creative Development</h6>
                                <p class="card-text text-muted small">Manage creative dev page content.</p>
                                <a href="page_creative_development.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Performance Marketing</h6>
                                <p class="card-text text-muted small">Manage performance marketing page.</p>
                                <a href="page_performance_marketing.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">E-Commerce Marketing</h6>
                                <p class="card-text text-muted small">Manage e-commerce marketing page.</p>
                                <a href="page_ecommerce_marketing.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-store fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Marketplace Mgmt</h6>
                                <p class="card-text text-muted small">Manage marketplace management page.</p>
                                <a href="page_marketplace_management.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-pen-nib fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Content Marketing</h6>
                                <p class="card-text text-muted small">Manage content marketing page.</p>
                                <a href="page_content_marketing.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 border border-primary border-opacity-25">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">About Us</h6>
                                <p class="card-text text-muted small">Manage about us page.</p>
                                <a href="page_about_us.php" class="btn btn-primary btn-sm">Manage</a>
                            </div>
                        </div>
                    </div>
                </div>
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
