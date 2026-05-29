<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();

// Create table and seed defaults on first visit
$pdo->exec("
    CREATE TABLE IF NOT EXISTS page_seo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_identifier VARCHAR(128) NOT NULL,
        page_label VARCHAR(255) NOT NULL,
        php_file VARCHAR(255) NOT NULL DEFAULT '',
        meta_title VARCHAR(255) DEFAULT NULL,
        meta_description VARCHAR(500) DEFAULT NULL,
        slug VARCHAR(255) DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_page_identifier (page_identifier)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$defaultPages = [
    ['home',                 'Home Page',              'index.php',                '/'],
    ['service',              'Services',               'service.php',              '/service'],
    ['technology',           'Technology',             'technology.php',           '/technology'],
    ['testimonial',          'Testimonials',           'testimonial.php',          '/testimonial'],
    ['products',             'Products',               'products.php',             '/products'],
    ['d2c-branding',         'D2C Branding',           'd2c-branding.php',         '/d2c-branding'],
    ['brand-shoot',          'Commercial Shoot',       'brand-shoot.php',          '/brand-shoot'],
    ['creative-dev',         'Creative Development',   'creative-dev.php',         '/creative-dev'],
    ['performance-marketing','Performance Marketing',  'performance-marketing.php','/performance-marketing'],
    ['e-com-marketing',      'E-Commerce Marketing',   'e-com-marketing.php',      '/e-com-marketing'],
    ['market-manage',        'Marketplace Management', 'market-manage.php',        '/market-manage'],
    ['content-marketing',    'Content Marketing',      'content-marketing.php',    '/content-marketing'],
    ['about-us',             'About Us',               'about-us.php',             '/about-us'],
    ['careers',              'Careers',                'careers.php',              '/careers'],
    ['blog',                 'Blog Listing',           'blog_list.php',            '/blog'],
    ['d2c',                  'D2C',                    'd2c.php',                  '/d2c'],
    ['instavideos',          'Insta Videos',           'instavideos.php',          '/instavideos'],
];

$ins = $pdo->prepare("
    INSERT IGNORE INTO page_seo (page_identifier, page_label, php_file, slug)
    VALUES (?, ?, ?, ?)
");
foreach ($defaultPages as $p) {
    $ins->execute($p);
}

$pages = $pdo->query("SELECT * FROM page_seo ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Load builder pages
$builderRows = [];
try {
    $builderRows = $pdo->query(
        "SELECT id, title, slug, meta_title, meta_desc AS meta_description, status FROM builder_pages ORDER BY title ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $builderRows = []; }

$saved     = isset($_GET['saved']);
$pageTitle = 'SEO Settings';
include __DIR__ . '/../views/admin_header.php';
?>

<?php if ($saved): ?>
<div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> SEO settings saved successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-search"></i> Page SEO Settings
        <span class="ms-auto text-muted small">Click Edit to update each page's meta title, description and URL slug</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:180px">Page</th>
                        <th style="width:160px">Slug (URL Path)</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th style="width:80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($p['page_label']) ?></td>
                        <td>
                            <code class="text-primary small"><?= htmlspecialchars($p['slug'] ?? '') ?></code>
                        </td>
                        <td>
                            <?php if (!empty($p['meta_title'])): ?>
                                <span class="small"><?= htmlspecialchars($p['meta_title']) ?></span>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['meta_description'])): ?>
                                <span class="small text-truncate d-block" style="max-width:300px">
                                    <?= htmlspecialchars($p['meta_description']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-edit-seo"
                                data-id="<?= $p['id'] ?>"
                                data-source="static"
                                data-label="<?= htmlspecialchars($p['page_label'], ENT_QUOTES) ?>"
                                data-title="<?= htmlspecialchars($p['meta_title'] ?? '', ENT_QUOTES) ?>"
                                data-desc="<?= htmlspecialchars($p['meta_description'] ?? '', ENT_QUOTES) ?>"
                                data-slug="<?= htmlspecialchars($p['slug'] ?? '', ENT_QUOTES) ?>">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($builderRows)): ?>
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-layer-group"></i> Builder Pages SEO
        <span class="ms-auto text-muted small">Custom pages created with the Page Builder</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:180px">Page</th>
                        <th style="width:80px">Status</th>
                        <th style="width:160px">Slug (URL Path)</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th style="width:80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($builderRows as $b): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($b['title']) ?></td>
                    <td>
                        <?php if ($b['status'] === 'published'): ?>
                            <span class="badge" style="background:#d4edda;color:#155724">Live</span>
                        <?php else: ?>
                            <span class="badge" style="background:#fff3cd;color:#856404">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td><code class="text-primary small"><?= htmlspecialchars($b['slug'] ?? '') ?></code></td>
                    <td>
                        <?php if (!empty($b['meta_title'])): ?>
                            <span class="small"><?= htmlspecialchars($b['meta_title']) ?></span>
                        <?php else: ?>
                            <span class="text-muted small fst-italic">Not set</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($b['meta_description'])): ?>
                            <span class="small text-truncate d-block" style="max-width:300px">
                                <?= htmlspecialchars($b['meta_description']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small fst-italic">Not set</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-edit-seo"
                            data-id="<?= $b['id'] ?>"
                            data-source="builder"
                            data-label="<?= htmlspecialchars($b['title'], ENT_QUOTES) ?>"
                            data-title="<?= htmlspecialchars($b['meta_title'] ?? '', ENT_QUOTES) ?>"
                            data-desc="<?= htmlspecialchars($b['meta_description'] ?? '', ENT_QUOTES) ?>"
                            data-slug="<?= htmlspecialchars($b['slug'] ?? '', ENT_QUOTES) ?>">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Modal -->
<div class="modal fade" id="seoModal" tabindex="-1" aria-labelledby="seoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seoModalLabel">
                    <i class="fas fa-search me-2"></i> Edit SEO – <span id="modalPageLabel"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="page_seo_save">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="source" id="editSource">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Slug <small class="text-muted fw-normal">(URL path — e.g. /digital-marketing-services)</small>
                        </label>
                        <input type="text" name="slug" id="editSlug" class="form-control font-monospace"
                               placeholder="/page-url-here">
                        <div class="form-text">
                            Changing the slug creates a new URL route for this page. The original URL continues to work.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            Meta Title
                            <span id="titleCount" class="text-muted small fw-normal">0 / 60</span>
                        </label>
                        <input type="text" name="meta_title" id="editMetaTitle" class="form-control"
                               maxlength="120" placeholder="Page title for search engines (50–60 chars recommended)">
                        <div id="titleBar" class="mt-1" style="height:4px;border-radius:2px;background:#e9ecef;overflow:hidden">
                            <div id="titleBarFill" style="height:100%;width:0;background:#0066ff;transition:width 0.2s"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            Meta Description
                            <span id="descCount" class="text-muted small fw-normal">0 / 160</span>
                        </label>
                        <textarea name="meta_description" id="editMetaDesc" class="form-control" rows="3"
                                  maxlength="320" placeholder="Page description for search engines (150–160 chars recommended)"></textarea>
                        <div id="descBar" class="mt-1" style="height:4px;border-radius:2px;background:#e9ecef;overflow:hidden">
                            <div id="descBarFill" style="height:100%;width:0;background:#0066ff;transition:width 0.2s"></div>
                        </div>
                    </div>

                    <!-- SERP Preview -->
                    <div class="border rounded p-3 bg-light mt-4">
                        <div class="text-muted small mb-2 fw-bold">SERP Preview</div>
                        <div id="serpTitle" class="text-primary fw-semibold" style="font-size:18px;cursor:pointer"></div>
                        <div id="serpUrl" class="text-success small"></div>
                        <div id="serpDesc" class="text-muted small mt-1" style="max-width:540px"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i> Save SEO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const appUrl = '<?= htmlspecialchars(rtrim($_ENV['APP_URL'] ?? '', '/')) ?>';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-seo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('editId').value               = d.id;
            document.getElementById('editSource').value           = d.source || 'static';
            document.getElementById('modalPageLabel').textContent = d.label;
            document.getElementById('editMetaTitle').value        = d.title;
            document.getElementById('editMetaDesc').value         = d.desc;
            document.getElementById('editSlug').value             = d.slug;
            updateCounters();
            updateSerp();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('seoModal')).show();
        });
    });
});

function updateCounters() {
    const title = document.getElementById('editMetaTitle').value;
    const desc  = document.getElementById('editMetaDesc').value;
    const slug  = document.getElementById('editSlug').value;

    // Title counter
    const tLen = title.length;
    document.getElementById('titleCount').textContent = tLen + ' / 60';
    const tPct = Math.min(tLen / 60 * 100, 100);
    const tFill = document.getElementById('titleBarFill');
    tFill.style.width = tPct + '%';
    tFill.style.background = tLen > 70 ? '#dc3545' : tLen > 60 ? '#fd7e14' : '#0066ff';

    // Desc counter
    const dLen = desc.length;
    document.getElementById('descCount').textContent = dLen + ' / 160';
    const dPct = Math.min(dLen / 160 * 100, 100);
    const dFill = document.getElementById('descBarFill');
    dFill.style.width = dPct + '%';
    dFill.style.background = dLen > 180 ? '#dc3545' : dLen > 160 ? '#fd7e14' : '#0066ff';
}

function updateSerp() {
    const title = document.getElementById('editMetaTitle').value || '(no title set)';
    const desc  = document.getElementById('editMetaDesc').value || '(no description set)';
    const slug  = document.getElementById('editSlug').value || '/';
    document.getElementById('serpTitle').textContent = title;
    document.getElementById('serpUrl').textContent   = appUrl + slug;
    document.getElementById('serpDesc').textContent  = desc;
}

document.getElementById('editMetaTitle').addEventListener('input', () => { updateCounters(); updateSerp(); });
document.getElementById('editMetaDesc').addEventListener('input', () => { updateCounters(); updateSerp(); });
document.getElementById('editSlug').addEventListener('input', updateSerp);
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
