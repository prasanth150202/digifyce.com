<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT section_key, content FROM page_content WHERE page_slug = 'home'");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// -------------------------------------------------------------
// FORM CONFIGURATION: Automatically generates the UI tabs
// -------------------------------------------------------------
$formStructure = [
    'Hero' => [
        'hero_subtitle' => ['type' => 'text', 'label' => 'Pre-Title (e.g. Our Vision)'],
        'hero_title' => ['type' => 'textarea', 'label' => 'Main Title (HTML allowed)'],
        'hero_subtext' => ['type' => 'textarea', 'label' => 'Subtext'],
        'hero_cta_text' => ['type' => 'text', 'label' => 'CTA Button Text'],
        'hero_cta_url' => ['type' => 'text', 'label' => 'CTA URL'],
        'hero_services_title' => ['type' => 'text', 'label' => 'Form Box Title'],
        'hero_services_cta_text' => ['type' => 'text', 'label' => 'Form Submit Button Text'],
    ],
    'Hero Checkboxes' => [
        'hero_chk_1' => ['type' => 'text', 'label' => 'Checkbox 1'],
        'hero_chk_2' => ['type' => 'text', 'label' => 'Checkbox 2'],
        'hero_chk_3' => ['type' => 'text', 'label' => 'Checkbox 3'],
        'hero_chk_4' => ['type' => 'text', 'label' => 'Checkbox 4'],
        'hero_chk_5' => ['type' => 'text', 'label' => 'Checkbox 5'],
        'hero_chk_6' => ['type' => 'text', 'label' => 'Checkbox 6'],
        'hero_chk_7' => ['type' => 'text', 'label' => 'Checkbox 7'],
        'hero_chk_8' => ['type' => 'text', 'label' => 'Checkbox 8'],
        'hero_chk_9' => ['type' => 'text', 'label' => 'Checkbox 9'],
        'hero_chk_10' => ['type' => 'text', 'label' => 'Checkbox 10'],
    ],
    'Who We Are' => [
        'who_title' => ['type' => 'textarea', 'label' => 'Main Title (HTML allowed)'],
        'who_sub_1' => ['type' => 'textarea', 'label' => 'Sentence 1'],
        'who_sub_2' => ['type' => 'textarea', 'label' => 'Sentence 2'],
        'who_sub_3' => ['type' => 'textarea', 'label' => 'Sentence 3'],
        'who_cta_text' => ['type' => 'text', 'label' => 'CTA Text'],
        'who_cta_url' => ['type' => 'text', 'label' => 'CTA URL'],
    ],
    'Metrics (Revenue)' => [
        'rev1_title' => ['type' => 'text', 'label' => 'Rev 1 Value'],
        'rev1_sub' => ['type' => 'text', 'label' => 'Rev 1 Subtext'],
        'rev2_sub' => ['type' => 'text', 'label' => 'Rev 2 Top Label'],
        'rev2_title' => ['type' => 'text', 'label' => 'Rev 2 Value'],
        'rev2_sub2' => ['type' => 'text', 'label' => 'Rev 2 Bottom Label'],
        'rev3_title' => ['type' => 'text', 'label' => 'Rev 3 Value'],
        'rev3_sub' => ['type' => 'text', 'label' => 'Rev 3 Subtext'],
        'rev4_title' => ['type' => 'text', 'label' => 'Rev 4 Value'],
        'rev4_sub' => ['type' => 'text', 'label' => 'Rev 4 Subtext'],
        'rev5_title' => ['type' => 'text', 'label' => 'Rev 5 Value'],
        'rev5_sub' => ['type' => 'text', 'label' => 'Rev 5 Subtext'],
    ],
    'Stats Grid' => [
        'grid1_sub' => ['type' => 'text', 'label' => 'Grid 1 Tag'],
        'grid1_title' => ['type' => 'text', 'label' => 'Grid 1 Value'],
        'grid1_text' => ['type' => 'textarea', 'label' => 'Grid 1 Text'],
        'grid2_sub' => ['type' => 'text', 'label' => 'Grid 2 Tag'],
        'grid2_title' => ['type' => 'text', 'label' => 'Grid 2 Value'],
        'grid2_text' => ['type' => 'textarea', 'label' => 'Grid 2 Text'],
        'grid3_sub' => ['type' => 'text', 'label' => 'Grid 3 Tag'],
        'grid3_title' => ['type' => 'text', 'label' => 'Grid 3 Value'],
        'grid3_text' => ['type' => 'textarea', 'label' => 'Grid 3 Text'],
        'grid4_sub' => ['type' => 'text', 'label' => 'Grid 4 Tag'],
        'grid4_title' => ['type' => 'text', 'label' => 'Grid 4 Value'],
        'grid4_text' => ['type' => 'textarea', 'label' => 'Grid 4 Text'],
    ],
    'Methodology Matrix' => [
        'matrix_subtitle' => ['type' => 'text', 'label' => 'Matrix Subheading'],
        'matrix_main_title' => ['type' => 'text', 'label' => 'Matrix Main Heading'],
        'matrix_qA_title' => ['type' => 'text', 'label' => 'Quad A Title'],
        'matrix_qA_sub' => ['type' => 'text', 'label' => 'Quad A Sub'],
        'matrix_qA_side_sub1' => ['type' => 'text', 'label' => 'Quad A Side Tag 1'],
        'matrix_qA_side_title' => ['type' => 'text', 'label' => 'Quad A Side Title'],
        'matrix_qA_side_text' => ['type' => 'textarea', 'label' => 'Quad A Side Text'],
        'matrix_qA_side_sub2' => ['type' => 'text', 'label' => 'Quad A Side Tag 2'],
        'matrix_qA_pt1' => ['type' => 'text', 'label' => 'Quad A Point 1'],
        'matrix_qA_pt2' => ['type' => 'text', 'label' => 'Quad A Point 2'],
        'matrix_qB_title' => ['type' => 'text', 'label' => 'Quad B Title'],
        'matrix_qB_sub' => ['type' => 'text', 'label' => 'Quad B Sub'],
        'matrix_qB_side_sub1' => ['type' => 'text', 'label' => 'Quad B Side Tag 1'],
        'matrix_qB_side_title' => ['type' => 'text', 'label' => 'Quad B Side Title'],
        'matrix_qB_side_text' => ['type' => 'textarea', 'label' => 'Quad B Side Text'],
        'matrix_qB_side_sub2' => ['type' => 'text', 'label' => 'Quad B Side Tag 2'],
        'matrix_qB_pt1' => ['type' => 'text', 'label' => 'Quad B Point 1'],
        'matrix_qB_pt2' => ['type' => 'text', 'label' => 'Quad B Point 2'],
        'matrix_qC_title' => ['type' => 'text', 'label' => 'Quad C Title'],
        'matrix_qC_sub' => ['type' => 'text', 'label' => 'Quad C Sub'],
        'matrix_qC_side_sub1' => ['type' => 'text', 'label' => 'Quad C Side Tag 1'],
        'matrix_qC_side_title' => ['type' => 'text', 'label' => 'Quad C Side Title'],
        'matrix_qC_side_text' => ['type' => 'textarea', 'label' => 'Quad C Side Text'],
        'matrix_qC_side_sub2' => ['type' => 'text', 'label' => 'Quad C Side Tag 2'],
        'matrix_qC_pt1' => ['type' => 'text', 'label' => 'Quad C Point 1'],
        'matrix_qC_pt2' => ['type' => 'text', 'label' => 'Quad C Point 2'],
        'matrix_qD_title' => ['type' => 'text', 'label' => 'Quad D Title'],
        'matrix_qD_sub' => ['type' => 'text', 'label' => 'Quad D Sub'],
        'matrix_qD_side_sub1' => ['type' => 'text', 'label' => 'Quad D Side Tag 1'],
        'matrix_qD_side_title' => ['type' => 'text', 'label' => 'Quad D Side Title'],
        'matrix_qD_side_text' => ['type' => 'textarea', 'label' => 'Quad D Side Text'],
        'matrix_qD_side_sub2' => ['type' => 'text', 'label' => 'Quad D Side Tag 2'],
        'matrix_qD_pt1' => ['type' => 'text', 'label' => 'Quad D Point 1'],
        'matrix_qD_pt2' => ['type' => 'text', 'label' => 'Quad D Point 2'],
    ],
    'Services List' => [
        'stack_title' => ['type' => 'text', 'label' => 'Tech Stack Title'],
        'serv_sub' => ['type' => 'text', 'label' => 'Services Subtitle'],
        'serv_title' => ['type' => 'text', 'label' => 'Services Main Title'],
        'serv1_title' => ['type' => 'text', 'label' => 'Service 1 Title'],
        'serv1_text' => ['type' => 'textarea', 'label' => 'Service 1 Text'],
        'serv1_cta' => ['type' => 'text', 'label' => 'Service 1 CTA Text'],
        'serv1_url' => ['type' => 'text', 'label' => 'Service 1 URL'],
        'serv2_title' => ['type' => 'text', 'label' => 'Service 2 Title'],
        'serv2_text' => ['type' => 'textarea', 'label' => 'Service 2 Text'],
        'serv2_cta' => ['type' => 'text', 'label' => 'Service 2 CTA Text'],
        'serv2_url' => ['type' => 'text', 'label' => 'Service 2 URL'],
        'serv3_title' => ['type' => 'text', 'label' => 'Service 3 Title'],
        'serv3_text' => ['type' => 'textarea', 'label' => 'Service 3 Text'],
        'serv3_cta' => ['type' => 'text', 'label' => 'Service 3 CTA Text'],
        'serv3_url' => ['type' => 'text', 'label' => 'Service 3 URL'],
        'serv4_title' => ['type' => 'text', 'label' => 'Service 4 Title'],
        'serv4_text' => ['type' => 'textarea', 'label' => 'Service 4 Text'],
        'serv4_cta' => ['type' => 'text', 'label' => 'Service 4 CTA Text'],
        'serv4_url' => ['type' => 'text', 'label' => 'Service 4 URL'],
        'serv5_title' => ['type' => 'text', 'label' => 'Service 5 Title'],
        'serv5_text' => ['type' => 'textarea', 'label' => 'Service 5 Text'],
        'serv5_cta' => ['type' => 'text', 'label' => 'Service 5 CTA Text'],
        'serv5_url' => ['type' => 'text', 'label' => 'Service 5 URL'],
        'serv6_title' => ['type' => 'text', 'label' => 'Service 6 Title'],
        'serv6_text' => ['type' => 'textarea', 'label' => 'Service 6 Text'],
        'serv6_cta' => ['type' => 'text', 'label' => 'Service 6 CTA Text'],
        'serv6_url' => ['type' => 'text', 'label' => 'Service 6 URL'],
        'serv7_title' => ['type' => 'text', 'label' => 'Service 7 Title'],
        'serv7_text' => ['type' => 'textarea', 'label' => 'Service 7 Text'],
        'serv7_cta' => ['type' => 'text', 'label' => 'Service 7 CTA Text'],
        'serv7_url' => ['type' => 'text', 'label' => 'Service 7 URL'],
    ],
    'Process' => [
        'proc_title' => ['type' => 'text', 'label' => 'Process Title'],
        'proc_sub' => ['type' => 'textarea', 'label' => 'Process Subtext'],
        'proc1_sub' => ['type' => 'text', 'label' => 'Step 1 Pre-Title'],
        'proc1_title' => ['type' => 'text', 'label' => 'Step 1 Title'],
        'proc1_text' => ['type' => 'textarea', 'label' => 'Step 1 Text'],
        'proc2_sub' => ['type' => 'text', 'label' => 'Step 2 Pre-Title'],
        'proc2_title' => ['type' => 'text', 'label' => 'Step 2 Title'],
        'proc2_text' => ['type' => 'textarea', 'label' => 'Step 2 Text'],
        'proc3_sub' => ['type' => 'text', 'label' => 'Step 3 Pre-Title'],
        'proc3_title' => ['type' => 'text', 'label' => 'Step 3 Title'],
        'proc3_text' => ['type' => 'textarea', 'label' => 'Step 3 Text'],
        'proc4_sub' => ['type' => 'text', 'label' => 'Step 4 Pre-Title'],
        'proc4_title' => ['type' => 'text', 'label' => 'Step 4 Title'],
        'proc4_text' => ['type' => 'textarea', 'label' => 'Step 4 Text'],
        'proc5_sub' => ['type' => 'text', 'label' => 'Step 5 Pre-Title'],
        'proc5_title' => ['type' => 'text', 'label' => 'Step 5 Title'],
        'proc5_text' => ['type' => 'textarea', 'label' => 'Step 5 Text'],
    ],
    'Press, Case & CTA' => [
        'press_title' => ['type' => 'text', 'label' => 'Press Title'],
        'case_title' => ['type' => 'text', 'label' => 'Case Study Title'],
        'case_sub' => ['type' => 'textarea', 'label' => 'Case Study Text'],
        'case_rev1_val' => ['type' => 'text', 'label' => 'Case Stat 1 Value'],
        'case_rev1_sub' => ['type' => 'text', 'label' => 'Case Stat 1 Label'],
        'case_rev2_val' => ['type' => 'text', 'label' => 'Case Stat 2 Value'],
        'case_rev2_sub' => ['type' => 'text', 'label' => 'Case Stat 2 Label'],
        'case_img_path' => ['type' => 'file', 'label' => 'Case Study Image (Data Visualisation / Graph)'],
        'why_title' => ['type' => 'text', 'label' => 'Why Us Title'],
        'why_sub' => ['type' => 'textarea', 'label' => 'Why Us Subtext'],
        'why1_head' => ['type' => 'text', 'label' => 'Card 1 Tag'],
        'why1_title' => ['type' => 'text', 'label' => 'Card 1 Title'],
        'why1_text' => ['type' => 'textarea', 'label' => 'Card 1 Text'],
        'why2_head' => ['type' => 'text', 'label' => 'Card 2 Tag'],
        'why2_title' => ['type' => 'text', 'label' => 'Card 2 Title'],
        'why2_text' => ['type' => 'textarea', 'label' => 'Card 2 Text'],
        'why3_head' => ['type' => 'text', 'label' => 'Card 3 Tag'],
        'why3_title' => ['type' => 'text', 'label' => 'Card 3 Title'],
        'why3_text' => ['type' => 'textarea', 'label' => 'Card 3 Text'],
        'why4_head' => ['type' => 'text', 'label' => 'Card 4 Tag'],
        'why4_title' => ['type' => 'text', 'label' => 'Card 4 Title'],
        'why4_text' => ['type' => 'textarea', 'label' => 'Card 4 Text'],
        'last_title' => ['type' => 'textarea', 'label' => 'Final CTA Title (HTML allowed)'],
        'last_cta_text' => ['type' => 'text', 'label' => 'Final Button Text'],
        'last_subtext' => ['type' => 'text', 'label' => 'Final Bottom Text'],
    ]
];

$pageTitle = 'Edit Homepage Sections';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-dark text-white d-flex align-items-center">
        <i class="fas fa-globe me-2"></i> Homepage Master Control
    </div>
    <div class="card-body bg-light">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> Data saved successfully!</div>
        <?php endif; ?>

        <form method="post" action="page_home_save.php" enctype="multipart/form-data">

            <ul class="nav nav-pills mb-4 border-bottom pb-2" id="homeTabs" role="tablist">
                <?php $i = 0;
                foreach ($formStructure as $tabName => $fields): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $i === 0 ? 'active' : '' ?> fw-bold" id="tab-<?= $i ?>"
                            data-bs-toggle="tab" data-bs-target="#pane-<?= $i ?>" type="button"
                            role="tab"><?= $tabName ?></button>
                    </li>
                    <?php $i++; endforeach; ?>
            </ul>

            <div class="tab-content bg-white p-4 border rounded" id="homeTabsContent">
                <?php $i = 0;
                foreach ($formStructure as $tabName => $fields): ?>
                    <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="pane-<?= $i ?>" role="tabpanel">
                        <div class="row g-3">
                            <?php foreach ($fields as $key => $config): ?>
                                <div class="<?= $config['type'] === 'textarea' ? 'col-12' : 'col-md-6' ?>">
                                    <label
                                        class="form-label text-muted small fw-bold text-uppercase mb-1"><?= $config['label'] ?></label>

                                    <?php if ($config['type'] === 'textarea'): ?>
                                        <textarea name="content[<?= $key ?>]" class="form-control bg-light"
                                            rows="3"><?= htmlspecialchars($results[$key] ?? '') ?></textarea>
                                    <?php elseif ($config['type'] === 'file'): ?>
                                        <input type="file" name="<?= $key ?>" class="form-control bg-light">
                                        <?php if (!empty($results[$key])): ?>
                                            <div class="mt-2">
                                                <span class="text-muted small">Current: <code><?= htmlspecialchars($results[$key]) ?></code></span>
                                                <br/>
                                                <img src="../../<?= htmlspecialchars($results[$key]) ?>" class="img-thumbnail mt-1" style="max-height: 120px;">
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <input type="text" name="content[<?= $key ?>]" class="form-control bg-light"
                                            value="<?= htmlspecialchars($results[$key] ?? '') ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php $i++; endforeach; ?>
            </div>

            <div class="mt-4 pt-3 text-end">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save All Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>