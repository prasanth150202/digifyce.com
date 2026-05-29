<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }

require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pm_hero      = $pdo->query("SELECT * FROM pm_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$pm_metrics   = $pdo->query("SELECT * FROM pm_hero_metrics ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_benchmark = $pdo->query("SELECT * FROM pm_benchmark_groups ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$_sh_rows     = $pdo->query("SELECT * FROM pm_section_headers")->fetchAll(PDO::FETCH_ASSOC);
$pm_shdrs     = [];
foreach ($_sh_rows as $r) { $pm_shdrs[$r['slug']] = $r; }
$challenges   = $pdo->query("SELECT * FROM pm_challenges ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$approaches   = $pdo->query("SELECT * FROM pm_approaches ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$services     = $pdo->query("SELECT * FROM pm_services ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$leadgen_tabs = $pdo->query("SELECT * FROM pm_leadgen_tabs ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$seo_panels   = $pdo->query("SELECT * FROM pm_seo_panels ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$steps        = $pdo->query("SELECT * FROM pm_steps ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$impacts      = $pdo->query("SELECT * FROM pm_impacts ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Performance Marketing Page';
include __DIR__ . '/../views/admin_header.php';

function pmBadge(int $v): string {
    return $v ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Hidden</span>';
}
?>

<div class="p-4">
<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>Saved successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-warning alert-dismissible fade show"><i class="fas fa-trash me-2"></i>Record deleted.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-chart-line me-2"></i>Performance Marketing Page</span>
        <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/performance-marketing.php" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt me-1"></i>View Page</a>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" id="pmTabs" style="flex-wrap:wrap;">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero">Hero</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-benchmark">Benchmark Table</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-sections">Section Headers</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-challenges">Challenges</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-approaches">Approach</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-services">Services</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-leadgen">Lead Gen Tabs</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-seo">SEO Panels</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-steps">Process Steps</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-impacts">Impact Cards</a></li>
        </ul>

        <div class="tab-content">

            <!-- HERO & METRICS -->
            <div class="tab-pane fade show active" id="tab-hero">
                <h6 class="mb-3">Hero Section</h6>
                <form method="post" action="performance_marketing_list_save.php" class="mb-5">
                    <input type="hidden" name="table_name" value="pm_hero">
                    <input type="hidden" name="id" value="1">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label fw-bold">Kicker Text</label><input type="text" class="form-control" name="kicker" value="<?= htmlspecialchars($pm_hero['kicker'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Headline Main</label><input type="text" class="form-control" name="headline_main" value="<?= htmlspecialchars($pm_hero['headline_main'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Headline Accent</label><input type="text" class="form-control" name="headline_accent" value="<?= htmlspecialchars($pm_hero['headline_accent'] ?? '') ?>"></div>
                        <div class="col-12"><label class="form-label fw-bold">Sub Text</label><textarea class="form-control" name="sub_text" rows="3"><?= htmlspecialchars($pm_hero['sub_text'] ?? '') ?></textarea></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Button 1 Label</label><input type="text" class="form-control" name="btn1_label" value="<?= htmlspecialchars($pm_hero['btn1_label'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Button 1 URL</label><input type="text" class="form-control" name="btn1_url" value="<?= htmlspecialchars($pm_hero['btn1_url'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Button 2 Label</label><input type="text" class="form-control" name="btn2_label" value="<?= htmlspecialchars($pm_hero['btn2_label'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Button 2 URL</label><input type="text" class="form-control" name="btn2_url" value="<?= htmlspecialchars($pm_hero['btn2_url'] ?? '') ?>"></div>
                        <div class="col-12"><label class="form-label fw-bold">Card Heading</label><input type="text" class="form-control" name="card_heading" value="<?= htmlspecialchars($pm_hero['card_heading'] ?? '') ?>"></div>
                        <div class="col-12"><label class="form-label fw-bold">Card Body (paragraph 1)</label><textarea class="form-control" name="card_body" rows="3"><?= htmlspecialchars($pm_hero['card_body'] ?? '') ?></textarea></div>
                        <div class="col-12"><label class="form-label fw-bold">Card Body 2 (paragraph 2, highlight)</label><textarea class="form-control" name="card_body2" rows="2"><?= htmlspecialchars($pm_hero['card_body2'] ?? '') ?></textarea></div>
                        <div class="col-12"><button type="submit" class="btn btn-primary">Save Hero</button></div>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Hero Metric Counters</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_hero_metrics')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Value</th><th>Text</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($pm_metrics as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><strong><?= $r['value'] ?></strong></td>
                        <td><?= htmlspecialchars($r['text']) ?></td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_hero_metrics')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_hero_metrics">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- BENCHMARK TABLE -->
            <div class="tab-pane fade" id="tab-benchmark">
                <div class="alert alert-info small"><i class="fas fa-info-circle me-2"></i>Each row is an industry group. The <code>rows_json</code> field is a JSON array of objects with keys: <code>metric</code>, <code>benchmark</code>, <code>digifyce_avg</code>, <code>lift</code>.</div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Industry Benchmark Groups</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_benchmark_groups')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Industry</th><th>Rows (preview)</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($pm_benchmark as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['industry_icon']) ?></code></td>
                        <td><?= htmlspecialchars($r['industry_label']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['rows_json'],0,60)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_benchmark_groups')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_benchmark_groups">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- SECTION HEADERS -->
            <div class="tab-pane fade" id="tab-sections">
                <p class="text-muted small mb-4">Each section header controls the kicker badge, heading, and sub-text for a page section.</p>
                <div class="accordion" id="shAccordion">
                <?php
                $shDefs = [
                    'problem'  => ['label'=>'Problem Section','fields'=>['kicker','heading','sub_text']],
                    'approach' => ['label'=>'Approach Section','fields'=>['kicker','heading','sub_text']],
                    'services' => ['label'=>'Services Section','fields'=>['kicker','heading']],
                    'leadgen'  => ['label'=>'Lead Gen Section','fields'=>['kicker','heading','sub_text']],
                    'seo'      => ['label'=>'SEO Section','fields'=>['kicker','heading','sub_text']],
                    'process'  => ['label'=>'Process Section','fields'=>['kicker','heading','sub_text']],
                    'impact'   => ['label'=>'Impact / Why Us Section','fields'=>['kicker','heading','extra_text','btn_label','btn_url']],
                    'cta'      => ['label'=>'Final CTA Section','fields'=>['heading','sub_text','btn_label','btn_url','btn2_label','btn2_url']],
                ];
                foreach ($shDefs as $slug => $def):
                    $sh = $pm_shdrs[$slug] ?? [];
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sh-<?= $slug ?>">
                            <strong><?= $def['label'] ?></strong>&nbsp;<span class="text-muted small ms-2"><?= htmlspecialchars(mb_substr($sh['heading'] ?? '',0,60)) ?>…</span>
                        </button>
                    </h2>
                    <div id="sh-<?= $slug ?>" class="accordion-collapse collapse" data-bs-parent="#shAccordion">
                        <div class="accordion-body">
                            <form method="post" action="performance_marketing_list_save.php">
                                <input type="hidden" name="table_name" value="pm_section_headers">
                                <input type="hidden" name="slug" value="<?= $slug ?>">
                                <input type="hidden" name="id" value="0">
                                <div class="row g-3">
                                <?php if (in_array('kicker',$def['fields'])): ?><div class="col-md-4"><label class="form-label fw-bold">Kicker</label><input type="text" class="form-control" name="kicker" value="<?= htmlspecialchars($sh['kicker'] ?? '') ?>"></div><?php endif; ?>
                                <?php if (in_array('heading',$def['fields'])): ?><div class="col-md-8"><label class="form-label fw-bold">Heading</label><input type="text" class="form-control" name="heading" value="<?= htmlspecialchars($sh['heading'] ?? '') ?>"></div><?php endif; ?>
                                <?php if (in_array('sub_text',$def['fields'])): ?><div class="col-12"><label class="form-label fw-bold">Sub Text</label><textarea class="form-control" name="sub_text" rows="2"><?= htmlspecialchars($sh['sub_text'] ?? '') ?></textarea></div><?php endif; ?>
                                <?php if (in_array('extra_text',$def['fields'])): ?><div class="col-12"><label class="form-label fw-bold">Panel Text</label><textarea class="form-control" name="extra_text" rows="2"><?= htmlspecialchars($sh['extra_text'] ?? '') ?></textarea></div><?php endif; ?>
                                <?php if (in_array('btn_label',$def['fields'])): ?><div class="col-md-6"><label class="form-label fw-bold">Button Label</label><input type="text" class="form-control" name="btn_label" value="<?= htmlspecialchars($sh['btn_label'] ?? '') ?>"></div><?php endif; ?>
                                <?php if (in_array('btn_url',$def['fields'])): ?><div class="col-md-6"><label class="form-label fw-bold">Button URL</label><input type="text" class="form-control" name="btn_url" value="<?= htmlspecialchars($sh['btn_url'] ?? '') ?>"></div><?php endif; ?>
                                <?php if (in_array('btn2_label',$def['fields'])): ?><div class="col-md-6"><label class="form-label fw-bold">Button 2 Label</label><input type="text" class="form-control" name="btn2_label" value="<?= htmlspecialchars($sh['btn2_label'] ?? '') ?>"></div><?php endif; ?>
                                <?php if (in_array('btn2_url',$def['fields'])): ?><div class="col-md-6"><label class="form-label fw-bold">Button 2 URL</label><input type="text" class="form-control" name="btn2_url" value="<?= htmlspecialchars($sh['btn2_url'] ?? '') ?>"></div><?php endif; ?>
                                <div class="col-12"><button type="submit" class="btn btn-primary btn-sm">Save <?= $def['label'] ?></button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>

            <!-- CHALLENGES -->
            <div class="tab-pane fade" id="tab-challenges">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Challenge Cards</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_challenges')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Text</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($challenges as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['icon']) ?></code></td>
                        <td><?= htmlspecialchars($r['text']) ?></td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_challenges')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_challenges">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- APPROACHES -->
            <div class="tab-pane fade" id="tab-approaches">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Approach Steps</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_approaches')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Step Label</th><th>Heading</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($approaches as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['step_label']) ?></td>
                        <td><?= htmlspecialchars($r['heading']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['description'],0,60)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_approaches')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_approaches">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- SERVICES -->
            <div class="tab-pane fade" id="tab-services">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Service Ribbons</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_services')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Tag</th><th>Heading</th><th>Chips</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($services as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($r['tag']) ?></span></td>
                        <td><?= htmlspecialchars(mb_substr($r['heading'],0,50)) ?>…</td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['chips_json'],0,50)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_services')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_services">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- LEAD GEN TABS -->
            <div class="tab-pane fade" id="tab-leadgen">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Lead Generation Tabs</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_leadgen_tabs')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Title</th><th>Conclusion</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($leadgen_tabs as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['tab_icon']) ?></code></td>
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['conclusion'],0,60)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_leadgen_tabs')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_leadgen_tabs">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- SEO PANELS -->
            <div class="tab-pane fade" id="tab-seo">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">SEO Accordion Panels</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_seo_panels')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Title</th><th>Conclusion</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($seo_panels as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['panel_icon']) ?></code></td>
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['conclusion'],0,60)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_seo_panels')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_seo_panels">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PROCESS STEPS -->
            <div class="tab-pane fade" id="tab-steps">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Process Steps</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_steps')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Heading</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($steps as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['icon']) ?></code></td>
                        <td><?= htmlspecialchars($r['heading']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['description'],0,60)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_steps')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_steps">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- IMPACT CARDS -->
            <div class="tab-pane fade" id="tab-impacts">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Why Choose Digifyce Cards</h6>
                    <button class="btn btn-primary btn-sm" onclick="pmEdit({},0,'pm_impacts')"><i class="fas fa-plus me-1"></i>Add New</button>
                </div>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Icon</th><th>Color Class</th><th>Heading</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($impacts as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><code><?= htmlspecialchars($r['icon']) ?></code></td>
                        <td class="small"><code><?= htmlspecialchars($r['icon_color']) ?></code></td>
                        <td><?= htmlspecialchars($r['heading']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars(mb_substr($r['description'],0,50)) ?>…</td>
                        <td><?= $r['sort_order'] ?></td>
                        <td><?= pmBadge((int)$r['is_active']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="pmEdit(<?= htmlspecialchars(json_encode($r)) ?>,1,'pm_impacts')">Edit</button>
                            <form method="post" action="performance_marketing_list_delete.php" class="d-inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="table_name" value="pm_impacts">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Del</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /tab-content -->
    </div><!-- /card-body -->
</div><!-- /card -->
</div><!-- /p-4 -->

<!-- SHARED MODAL -->
<div class="modal fade" id="pmModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="performance_marketing_list_save.php">
                <input type="hidden" name="table_name" id="pm_table_name">
                <input type="hidden" name="id" id="pm_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="pmModalTitle">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="pmModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function pmEdit(row, isEdit, table) {
    document.getElementById('pm_table_name').value = table;
    document.getElementById('pm_id').value = row.id || '';
    document.getElementById('pmModalTitle').textContent = (isEdit ? 'Edit' : 'Add New') + ' — ' + table.replace('pm_','').replace(/_/g,' ');

    const body = document.getElementById('pmModalBody');
    let html = '';
    const v = (field, fallback) => {
        const val = row[field] !== undefined ? row[field] : (fallback !== undefined ? fallback : '');
        return String(val).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    };
    const activeChecked = row.is_active !== undefined ? (row.is_active == 1 ? 'checked' : '') : 'checked';

    const fld = (label, name, val, type='text') =>
        `<div class="mb-3"><label class="form-label fw-bold">${label}</label><input type="${type}" class="form-control" name="${name}" value="${val}"></div>`;
    const ta = (label, name, val) =>
        `<div class="mb-3"><label class="form-label fw-bold">${label}</label><textarea class="form-control" name="${name}" rows="3">${val}</textarea></div>`;
    const jsonTa = (label, name, val) =>
        `<div class="mb-3"><label class="form-label fw-bold">${label} <small class="text-muted">(JSON array e.g. ["Item 1","Item 2"])</small></label><textarea class="form-control font-monospace" name="${name}" rows="4">${val}</textarea></div>`;
    const orderActive = `
        <div class="row g-3 mb-3">
            <div class="col-md-6">${fld('Sort Order','sort_order',v('sort_order','0'),'number')}</div>
            <div class="col-md-6 d-flex align-items-end pb-1"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" ${activeChecked}><label class="form-check-label">Active</label></div></div>
        </div>`;

    switch (table) {
        case 'pm_challenges':
            html = fld('Material Icon Name','icon',v('icon')) +
                   ta('Text','text',v('text')) +
                   orderActive;
            break;
        case 'pm_approaches':
            html = fld('Step Label (e.g. Step 1: Acquisition)','step_label',v('step_label')) +
                   fld('Heading','heading',v('heading')) +
                   ta('Description','description',v('description')) +
                   orderActive;
            break;
        case 'pm_services':
            html = fld('Tag','tag',v('tag')) +
                   fld('Heading','heading',v('heading')) +
                   ta('Description','description',v('description')) +
                   jsonTa('Chips (JSON array)','chips_json',v('chips_json','[]')) +
                   orderActive;
            break;
        case 'pm_leadgen_tabs':
            html = fld('Tab Icon (Material Symbol)','tab_icon',v('tab_icon')) +
                   fld('Title','title',v('title')) +
                   ta('Intro Text','intro_text',v('intro_text')) +
                   jsonTa('Bullets (JSON array)','bullets_json',v('bullets_json','[]')) +
                   ta('Conclusion','conclusion',v('conclusion')) +
                   orderActive;
            break;
        case 'pm_seo_panels':
            html = fld('Panel Icon (Material Symbol)','panel_icon',v('panel_icon')) +
                   fld('Title','title',v('title')) +
                   ta('Intro Text','intro_text',v('intro_text')) +
                   jsonTa('Bullets (JSON array)','bullets_json',v('bullets_json','[]')) +
                   ta('Conclusion','conclusion',v('conclusion')) +
                   orderActive;
            break;
        case 'pm_steps':
            html = fld('Icon (Material Symbol)','icon',v('icon')) +
                   fld('Heading','heading',v('heading')) +
                   ta('Description','description',v('description')) +
                   orderActive;
            break;
        case 'pm_hero_metrics':
            html = fld('Counter Value (number)','value',v('value','0'),'number') +
                   ta('Text (description below counter)','text',v('text')) +
                   orderActive;
            break;
        case 'pm_benchmark_groups':
            html = fld('Industry Icon (Material Symbol)','industry_icon',v('industry_icon')) +
                   fld('Industry Label','industry_label',v('industry_label')) +
                   jsonTa('Rows JSON — array of {metric, benchmark, digifyce_avg, lift}','rows_json',v('rows_json','[]')) +
                   orderActive;
            break;
        case 'pm_impacts':
            html = fld('Icon (Material Symbol)','icon',v('icon')) +
                   fld('Icon Color Class (e.g. text-4xl text-[var(--pm-accent)])','icon_color',v('icon_color')) +
                   fld('Heading','heading',v('heading')) +
                   ta('Description','description',v('description')) +
                   orderActive;
            break;
    }

    body.innerHTML = html;
    new bootstrap.Modal(document.getElementById('pmModal')).show();
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
