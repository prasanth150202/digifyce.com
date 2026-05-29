<?php
// Bootstrap admin header include
$dotenv = __DIR__ . '/../../.env';
if (!isset($_ENV['APP_URL']) && file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        if (strpos($line, '=') === false)
            continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
    }
}
$appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');

// Initialize PermissionManager
if (!isset($permissionManager) && isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../utilities/PermissionManager.php';
    require_once __DIR__ . '/../../config/database.php';
    try {
        $pdo = Database::getInstance();
        $permissionManager = new PermissionManager($pdo);
    } catch (Exception $e) {
        // Database not available
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066ff;
            --dark: #0f1419;
            --light: #f8fafc;
        }

        * {
            font-family: 'Space Grotesk', sans-serif;
        }

        body {
            background: var(--light);
            color: #333;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, var(--dark) 0%, #1a1f29 100%);
            color: #fff;
            padding: 0;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            background: rgba(0, 102, 255, 0.1);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 18px;
            color: var(--primary);
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(0, 102, 255, 0.1);
            border-left-color: var(--primary);
        }

        .nav-link.active {
            color: #fff;
            background: rgba(0, 102, 255, 0.15);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        main {
            margin-left: 250px;
            min-height: 100vh;
            background: var(--light);
        }

        .admin-header {
            background: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .admin-header h1 {
            color: var(--dark);
            font-weight: 700;
            font-size: 28px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-content {
            padding: 30px;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: #0052cc;
            border-color: #0052cc;
        }

        .card {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: var(--dark);
        }

        .table {
            border-collapse: collapse;
        }

        .table thead th {
            background: #f8fafc;
            border-top: 1px solid #e9ecef;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: var(--dark);
            padding: 15px;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .form-control,
        .form-select {
            border: 1px solid #e9ecef;
            padding: 10px 12px;
            font-size: 14px;
            border-radius: 6px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 255, 0.25);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, #0052cc 100%);
            color: #fff;
            border: none;
        }

        .modal-title {
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-cog"></i> DIGIFYCE Admin
                </div>
            </div>
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? ' active' : '' ?>"
                        href="<?= $appUrl ?>/app/admin/dashboard.php">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </li>

                <!-- Content Management Section -->
                <?php if (isset($permissionManager) && $permissionManager->hasAnyPermission(['blog.view', 'page.view', 'category.view', 'tag.view', 'author.view'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['blogs.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/blogs.php">
                            <i class="fas fa-file-alt"></i> Blogs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['pages.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/pages.php">
                            <i class="fas fa-file-lines"></i> Pages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['categories.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/categories.php">
                            <i class="fas fa-folder"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['tags.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/tags.php">
                            <i class="fas fa-tags"></i> Tags
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['authors.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/authors.php">
                            <i class="fas fa-users"></i> Authors
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Settings Section -->
                <?php if (isset($permissionManager) && $permissionManager->hasAnyPermission(['settings.edit', 'settings.view', 'navigation.edit'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['navigation.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/navigation.php">
                            <i class="fas fa-bars"></i> Navigation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['hero_edit.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/hero_edit.php">
                            <i class="fas fa-image"></i> Hero Section
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                    <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['metrics_edit.php']) ? ' active' : '' ?>" href="<?= $appUrl ?>/app/admin/metrics_edit.php">
                        <i class="fas fa-chart-bar"></i> Metrics
                    </a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['site_settings.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/site_settings.php">
                            <i class="fas fa-sliders-h"></i> Site Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['trusted_brands.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/trusted_brands.php">
                            <i class="fas fa-handshake"></i> Trusted Brands
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Jobs Section -->
                <?php if (isset($permissionManager) && $permissionManager->hasAnyPermission(['job.view'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['job_openings.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/job_openings.php">
                            <i class="fas fa-briefcase"></i> Job Openings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['job_applications.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/job_applications.php">
                            <i class="fas fa-user-tie"></i> Job Applications
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Leads Section -->
                <?php if (isset($permissionManager) && $permissionManager->hasAnyPermission(['lead.view'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['lead_submissions.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/lead_submissions.php">
                            <i class="fas fa-envelope"></i> Lead Submissions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['pdf_email_leads.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/pdf_email_leads.php">
                            <i class="fas fa-file-pdf"></i> PDF Leads
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Administration Section -->
                <?php if (isset($permissionManager) && $permissionManager->hasAnyPermission(['user.view', 'role.view'])): ?>
                    <li class="nav-item border-top border-secondary mt-3 pt-3">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['users.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/users.php">
                            <i class="fas fa-user-shield"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']), ['roles_permissions.php']) ? ' active' : '' ?>"
                            href="<?= $appUrl ?>/app/admin/roles_permissions.php">
                            <i class="fas fa-shield-alt"></i> Roles & Permissions
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item border-top border-secondary mt-3 pt-3">
                    <a class="nav-link text-danger" href="<?= $appUrl ?>/app/admin/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
        <main>
            <div class="admin-header d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-<?php
                    $pageName = basename($_SERVER['PHP_SELF']);
                    switch ($pageName) {
                        case 'dashboard.php':
                            echo 'chart-line';
                            break;
                        case 'blogs.php':
                            echo 'file-alt';
                            break;
                        case 'pages.php':
                            echo 'file-lines';
                            break;
                        case 'categories.php':
                            echo 'folder';
                            break;
                        case 'tags.php':
                            echo 'tags';
                            break;
                        case 'authors.php':
                            echo 'users';
                            break;
                        case 'navigation.php':
                            echo 'bars';
                            break;
                        case 'site_settings.php':
                            echo 'sliders-h';
                            break;
                        case 'trusted_brands.php':
                            echo 'handshake';
                            break;
                        case 'job_openings.php':
                            echo 'briefcase';
                            break;
                        case 'users.php':
                            echo 'user-shield';
                            break;
                        case 'roles_permissions.php':
                            echo 'shield-alt';
                            break;
                        default:
                            echo 'cog';
                            break;
                    }
                    ?>"></i>
                    <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Panel' ?>
                </h1>

                <!-- User Info Section -->
                <?php if (isset($permissionManager)): ?>
                    <?php
                    $currentUser = $permissionManager->getCurrentUser();
                    if ($currentUser):
                        ?>
                        <div class="text-end">
                            <small class="text-muted d-block">Logged in as:</small>
                            <strong><?= htmlspecialchars($currentUser['full_name'] ?? $currentUser['username']) ?></strong>
                            <span class="badge bg-info ms-2"><?= htmlspecialchars($currentUser['role_name']) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="admin-content">