<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pdo = Database::getInstance();
$auditId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT wa.*, l.full_name, l.email, l.phone, l.company, l.industry, l.has_ads, l.ad_spend, l.roas
     FROM website_audits wa
     JOIN lead_form_submissions l ON l.id = wa.lead_id
     WHERE wa.id = ?"
);
$stmt->execute([$auditId]);
$audit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$audit) {
    header('Location: website_audits.php');
    exit;
}

$findings = $audit['findings_json'] ? json_decode($audit['findings_json'], true) : [];
$scrapedData = $audit['scraped_data_json'] ? json_decode($audit['scraped_data_json'], true) : null;
$pages = $scrapedData['pages'] ?? [];
$screenshotUrl = !empty($scrapedData['screenshot_path']) ? $appUrl . '/' . ltrim($scrapedData['screenshot_path'], '/') : null;
$layoutData = $scrapedData['layout_data'] ?? null;
$aiPlan = !empty($audit['ai_plan_json']) ? json_decode($audit['ai_plan_json'], true) : null;
$priorityActions = !empty($audit['ai_priority_actions_json']) ? json_decode($audit['ai_priority_actions_json'], true) : null;
$adStrategy = !empty($audit['ai_ad_strategy_json']) ? json_decode($audit['ai_ad_strategy_json'], true) : null;
$caseStudyMatches = !empty($audit['ai_case_study_matches_json']) ? json_decode($audit['ai_case_study_matches_json'], true) : [];

$effortBadge = ['low' => 'bg-success', 'medium' => 'bg-warning text-dark', 'high' => 'bg-danger'];
$adPriorityBadge = ['high' => 'bg-primary', 'medium' => 'bg-secondary', 'low' => 'bg-light text-dark border'];

$pageTitle = 'Website Audit Detail';
include __DIR__ . '/../views/admin_header.php';

$severityBadge = [
    'critical' => 'bg-danger',
    'high' => 'bg-warning text-dark',
    'medium' => 'bg-info text-dark',
    'low' => 'bg-secondary',
    'info' => 'bg-primary',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-magnifying-glass-chart me-2"></i>Website Audit
    </h1>
    <a href="website_audits.php" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Back to Audits
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">Website</h6>
                <p class="mb-1">
                    <a href="<?= htmlspecialchars($audit['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($audit['url']) ?></a>
                </p>
                <h6 class="text-muted text-uppercase small mb-2 mt-3">Lead</h6>
                <p class="mb-0"><strong><?= htmlspecialchars($audit['full_name']) ?></strong> — <?= htmlspecialchars($audit['email']) ?></p>
                <?php if ($audit['company']): ?><p class="text-muted small mb-0"><?= htmlspecialchars($audit['company']) ?></p><?php endif; ?>
                <?php if (!empty($audit['industry'])): ?><p class="text-muted small mb-0">Industry: <?= htmlspecialchars($audit['industry']) ?></p><?php endif; ?>
                <?php if (($audit['has_ads'] ?? null) === 'yes'): ?>
                    <p class="text-muted small mb-0">Runs ads: <?= htmlspecialchars($audit['ad_spend'] ?: '-') ?> spend, <?= htmlspecialchars($audit['roas'] ?: '-') ?> ROAS</p>
                <?php elseif (($audit['has_ads'] ?? null) === 'no'): ?>
                    <p class="text-muted small mb-0">Runs ads: No</p>
                <?php endif; ?>
                <p class="mt-2">
                    <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($audit['status'])) ?></span>
                    <?php if (!empty($audit['ai_model_used'])): ?>
                        <span class="badge bg-light text-dark border ms-2">
                            <?= $audit['ai_model_used'] === 'rule-based-fallback' ? 'Rule-based (fallback)' : htmlspecialchars($audit['ai_model_used']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($audit['keep_in_touch'])): ?>
                        <span class="badge bg-success ms-2"><i class="fas fa-handshake me-1"></i>Wants follow-up</span>
                    <?php endif; ?>
                    <span class="text-muted small ms-2">Submitted <?= date('M d, Y g:i A', strtotime($audit['created_at'])) ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body d-flex flex-column justify-content-center">
                <?php if ($audit['score'] !== null): ?>
                    <div class="display-4 fw-bold text-primary"><?= (int) $audit['score'] ?></div>
                    <div class="text-muted text-uppercase small">out of 100</div>
                <?php else: ?>
                    <div class="text-muted">No score (see findings below)</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($audit['status'] === 'failed'): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <?= htmlspecialchars($audit['error_message'] ?: 'This audit failed for an unknown reason.') ?>
</div>
<?php endif; ?>

<?php if (!empty($audit['brand_brief'])): ?>
<div class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body">
        <h6 class="text-muted text-uppercase small mb-2">About This Business</h6>
        <p class="mb-0"><?= htmlspecialchars($audit['brand_brief']) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($audit['executive_summary'])): ?>
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
    <div class="card-body">
        <h6 class="text-muted text-uppercase small mb-2">The Verdict</h6>
        <p class="mb-0"><?= htmlspecialchars($audit['executive_summary']) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($aiPlan): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">AI Audit Plan</h5>
        <span class="badge bg-light text-dark border"><?= htmlspecialchars($audit['ai_model_used'] ?? 'unknown') ?></span>
    </div>
    <div class="card-body">
        <?php if (!empty($aiPlan['business_type'])): ?>
            <p class="mb-2"><strong>Business type:</strong> <?= htmlspecialchars($aiPlan['business_type']) ?></p>
        <?php endif; ?>
        <?php if (!empty($aiPlan['priorities'])): ?>
            <h6 class="text-muted text-uppercase small mb-2 mt-3">Priorities</h6>
            <ul class="list-group list-group-flush">
                <?php foreach ($aiPlan['priorities'] as $p): ?>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= htmlspecialchars($p['category'] ?? '') ?></strong>
                        <div class="text-muted small"><?= htmlspecialchars($p['reason'] ?? '') ?></div>
                    </div>
                    <span class="badge <?= $severityBadge[$p['priority'] ?? ''] ?? 'bg-secondary' ?> text-uppercase"><?= htmlspecialchars($p['priority'] ?? '') ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (!empty($aiPlan['focus_notes'])): ?>
            <h6 class="text-muted text-uppercase small mb-2 mt-3">Notes</h6>
            <p class="mb-0 small"><?= nl2br(htmlspecialchars($aiPlan['focus_notes'])) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($priorityActions)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Where To Focus First</h5>
    </div>
    <div class="card-body p-0">
        <ol class="list-group list-group-numbered list-group-flush">
            <?php foreach ($priorityActions as $action): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold"><?= htmlspecialchars($action['action'] ?? '') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($action['why'] ?? '') ?></div>
                </div>
                <?php if (!empty($action['effort'])): ?>
                    <span class="badge <?= $effortBadge[$action['effort']] ?? 'bg-secondary' ?> text-uppercase"><?= htmlspecialchars($action['effort']) ?> effort</span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</div>
<?php endif; ?>

<?php if ($screenshotUrl): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Homepage Screenshot</h5>
    </div>
    <div class="card-body text-center">
        <a href="<?= htmlspecialchars($screenshotUrl) ?>" target="_blank" rel="noopener">
            <img src="<?= htmlspecialchars($screenshotUrl) ?>" alt="Homepage screenshot" class="img-fluid rounded border" style="max-height: 400px;">
        </a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($findings)):
    $groupedFindings = [];
    foreach ($findings as $finding) {
        $groupedFindings[$finding['category']][] = $finding;
    }
?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Findings (<?= count($findings) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="accordion accordion-flush" id="findingsAccordion">
            <?php $catIndex = 0; foreach ($groupedFindings as $category => $items):
                $catIndex++;
                $hasCriticalOrHigh = false;
                foreach ($items as $item) {
                    if (in_array($item['severity'], ['critical', 'high'], true)) {
                        $hasCriticalOrHigh = true;
                        break;
                    }
                }
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button<?= $hasCriticalOrHigh ? '' : ' collapsed' ?>" type="button"
                        data-bs-toggle="collapse" data-bs-target="#findingCat<?= $catIndex ?>">
                        <?= htmlspecialchars($category) ?>
                        <span class="badge bg-secondary ms-2"><?= count($items) ?></span>
                    </button>
                </h2>
                <div id="findingCat<?= $catIndex ?>" class="accordion-collapse collapse<?= $hasCriticalOrHigh ? ' show' : '' ?>"
                    data-bs-parent="#findingsAccordion">
                    <div class="accordion-body">
                        <?php foreach ($items as $finding): ?>
                        <div class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <span class="badge <?= $severityBadge[$finding['severity']] ?? 'bg-secondary' ?> mt-1"><?= htmlspecialchars(strtoupper($finding['severity'])) ?></span>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($finding['title']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($finding['description']) ?></div>
                                <?php if (!empty($finding['page'])): ?>
                                    <div class="text-muted small mt-1"><i class="fas fa-link me-1"></i><?= htmlspecialchars($finding['page']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($caseStudyMatches)): ?>
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-award me-2 text-primary"></i>Recommended Implementations (<?= count($caseStudyMatches) ?>)</h5>
    </div>
    <div class="card-body">
        <?php foreach ($caseStudyMatches as $i => $item): ?>
        <div class="<?= $i > 0 ? 'border-top pt-3 mt-3' : '' ?>">
            <p class="fw-bold mb-2"><?= htmlspecialchars($item['title'] ?? '') ?></p>
            <?php if (!empty($item['description'])): ?>
                <p class="mb-2"><?= htmlspecialchars($item['description']) ?></p>
            <?php endif; ?>
            <?php $proof = $item['proof'] ?? []; ?>
            <?php if (!empty($proof)): ?>
                <div class="bg-light rounded p-2 small">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Proven with</span>
                        <?php if (!empty($proof['industry'])): ?>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($proof['industry']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($proof['case_study_title'])): ?>
                        <p class="mb-1 text-muted"><em><?= htmlspecialchars($proof['case_study_title']) ?></em></p>
                    <?php endif; ?>
                    <?php if (!empty($proof['summary'])): ?>
                        <p class="mb-1"><?= htmlspecialchars($proof['summary']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($proof['metrics'])): ?>
                        <div>
                            <?php foreach ($proof['metrics'] as $metric): ?>
                                <span class="badge bg-primary me-1 mb-1"><?= htmlspecialchars($metric['value'] ?? '') ?> <?= htmlspecialchars($metric['label'] ?? '') ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($adStrategy): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Advertising Strategy</h5>
    </div>
    <div class="card-body">
        <?php if (isset($adStrategy['should_invest_in_ads'])): ?>
            <p class="mb-2">
                <strong>Recommendation:</strong>
                <span class="badge <?= $adStrategy['should_invest_in_ads'] ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $adStrategy['should_invest_in_ads'] ? 'Invest in paid ads' : 'Hold off on paid ads' ?>
                </span>
            </p>
        <?php endif; ?>
        <?php if (!empty($adStrategy['landing_page_readiness'])): ?>
            <p class="text-muted small mb-3"><?= htmlspecialchars($adStrategy['landing_page_readiness']) ?></p>
        <?php endif; ?>
        <?php if (!empty($adStrategy['growth_potential'])): ?>
            <div class="alert alert-primary py-2 small mb-3"><strong>Growth potential:</strong> <?= htmlspecialchars($adStrategy['growth_potential']) ?></div>
        <?php endif; ?>
        <?php if (!empty($adStrategy['target_pages'])): ?>
            <h6 class="text-muted text-uppercase small mb-2">Best Pages To Advertise</h6>
            <ul class="list-group list-group-flush mb-3">
                <?php foreach ($adStrategy['target_pages'] as $tp): ?>
                <li class="list-group-item px-0">
                    <div class="fw-bold text-break"><?= htmlspecialchars($tp['page'] ?? '') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($tp['why'] ?? '') ?></div>
                    <?php if (!empty($tp['action_needed'])): ?>
                        <div class="text-muted small"><strong>Before advertising:</strong> <?= htmlspecialchars($tp['action_needed']) ?></div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (!empty($adStrategy['targeting_ideas'])): ?>
            <h6 class="text-muted text-uppercase small mb-2">Who To Target</h6>
            <p class="small mb-3"><?= htmlspecialchars($adStrategy['targeting_ideas']) ?></p>
        <?php endif; ?>
        <?php if (!empty($adStrategy['recommended_channels'])): ?>
            <h6 class="text-muted text-uppercase small mb-2">Recommended Channels</h6>
            <ul class="list-group list-group-flush mb-3">
                <?php foreach ($adStrategy['recommended_channels'] as $channel): ?>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= htmlspecialchars($channel['channel'] ?? '') ?></strong>
                        <div class="text-muted small"><?= htmlspecialchars($channel['reason'] ?? '') ?></div>
                    </div>
                    <span class="badge <?= $adPriorityBadge[$channel['priority'] ?? ''] ?? 'bg-secondary' ?> text-uppercase"><?= htmlspecialchars($channel['priority'] ?? '') ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (!empty($adStrategy['budget_guidance'])): ?>
            <h6 class="text-muted text-uppercase small mb-2">Budget Guidance</h6>
            <p class="mb-0 small"><?= nl2br(htmlspecialchars($adStrategy['budget_guidance'])) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($pages)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Crawled Pages (<?= count($pages) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Response Time</th>
                        <th>Title</th>
                        <th>Words</th>
                        <th>Images (missing alt)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td class="text-truncate" style="max-width: 260px;">
                            <a href="<?= htmlspecialchars($page['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($page['url']) ?></a>
                        </td>
                        <td><?= (int) $page['status_code'] ?></td>
                        <td><?= isset($page['response_time_ms']) ? (int) $page['response_time_ms'] . ' ms' : '-' ?></td>
                        <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($page['title'] ?? '-') ?></td>
                        <td><?= isset($page['word_count']) ? (int) $page['word_count'] : '-' ?></td>
                        <td><?= isset($page['images_total']) ? (int) $page['images_total'] . ' (' . (int) ($page['images_missing_alt'] ?? 0) . ')' : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($layoutData): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Layout &amp; Design Signals</h5>
        <small class="text-muted">Measured from the rendered homepage (desktop + mobile viewport)</small>
    </div>
    <div class="card-body">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= $layoutData['has_nav'] ? 'Yes' : 'No' ?></div>
                <div class="text-muted small">Navigation menu</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= $layoutData['has_footer'] ? 'Yes' : 'No' ?></div>
                <div class="text-muted small">Footer</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= $layoutData['has_cta_above_fold'] ? 'Yes' : 'No' ?></div>
                <div class="text-muted small">CTA above the fold</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= $layoutData['mobile_overflow'] ? 'Yes' : 'No' ?></div>
                <div class="text-muted small">Mobile overflow</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= (int) ($layoutData['font_family_count'] ?? 0) ?></div>
                <div class="text-muted small">Font families used</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= $layoutData['contrast_ratio'] !== null ? number_format((float) $layoutData['contrast_ratio'], 1) . ':1' : '-' ?></div>
                <div class="text-muted small">Text contrast ratio</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= round(((float) ($layoutData['small_tap_target_ratio'] ?? 0)) * 100) ?>%</div>
                <div class="text-muted small">Small tap targets</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold"><?= round(((float) ($layoutData['small_font_ratio'] ?? 0)) * 100) ?>%</div>
                <div class="text-muted small">Small body text</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
