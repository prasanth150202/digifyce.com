<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/utilities/AuditSpawner.php';
require_once __DIR__ . '/../../app/utilities/AppUrl.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $token = $_GET['token'] ?? '';
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }

    $pdo = Database::getInstance();
    $auditConfig = require __DIR__ . '/../../config/website_audit.php';
    $appUrl = AppUrl::resolve();

    $stmt = $pdo->prepare(
        "SELECT wa.id, wa.status, wa.score, wa.findings_json, wa.brand_brief, wa.scraped_data_json,
                wa.executive_summary, wa.ai_priority_actions_json, wa.ai_ad_strategy_json, wa.ai_case_study_matches_json,
                l.has_ads, l.ad_spend, l.roas
         FROM website_audits wa
         JOIN lead_form_submissions l ON l.id = wa.lead_id
         WHERE wa.token = ?"
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Audit not found']);
        exit;
    }

    if ($row['status'] === 'running') {
        // Self-heal: if the audit service never reports back (restarted or
        // redeployed mid-job, or its result callback couldn't reach us),
        // give up gracefully after a generous timeout so the lead's UI never
        // spins forever.
        $timeoutStmt = $pdo->prepare(
            "UPDATE website_audits
             SET status = 'failed', error_message = 'Audit timed out.', updated_at = NOW()
             WHERE id = ? AND status = 'running' AND updated_at < (NOW() - INTERVAL ? SECOND)"
        );
        $timeoutStmt->execute([$row['id'], $auditConfig['max_running_seconds']]);
        if ($timeoutStmt->rowCount() === 1) {
            $row['status'] = 'failed';
        }
    }

    if ($row['status'] === 'pending') {
        // Self-heal: still 'pending' means the audit service never accepted
        // the job (unreachable, or still waking from free-tier sleep), so
        // re-dispatch it here (atomic compare-and-swap so two concurrent
        // pollers can't double-dispatch; the service also ignores a job
        // it already has).
        $respawn = $pdo->prepare(
            "UPDATE website_audits
             SET attempts = attempts + 1, updated_at = NOW()
             WHERE id = ? AND status = 'pending' AND attempts < ?
               AND updated_at < (NOW() - INTERVAL ? SECOND)"
        );
        $respawn->execute([$row['id'], $auditConfig['max_attempts'], $auditConfig['respawn_threshold_seconds']]);

        if ($respawn->rowCount() === 1) {
            AuditSpawner::spawn((int) $row['id']);
        } else {
            // Attempts exhausted and still stuck: give up gracefully so the
            // client never spins forever.
            $giveUp = $pdo->prepare(
                "UPDATE website_audits
                 SET status = 'failed', error_message = 'Audit could not be started after multiple attempts.', updated_at = NOW()
                 WHERE id = ? AND status = 'pending' AND attempts >= ?
                   AND updated_at < (NOW() - INTERVAL ? SECOND)"
            );
            $giveUp->execute([$row['id'], $auditConfig['max_attempts'], $auditConfig['respawn_threshold_seconds']]);
            if ($giveUp->rowCount() === 1) {
                $row['status'] = 'failed';
            }
        }
    }

    $findings = $row['findings_json'] ? json_decode($row['findings_json'], true) : [];

    // Mirrors the option labels in leadform.php's $adSpendOptions/$roasOptions
    // so the lead's own submitted numbers can be shown back to them in
    // plain language alongside the AI's ad-spend recommendations.
    $adSpendLabels = ['under-25k' => 'Under ₹25k', '25k-50k' => '₹25k - ₹50k', '50k-1l' => '₹50k - ₹1L', '1l-3l' => '₹1L - ₹3L', '3l-plus' => '₹3L+'];
    $roasLabels = ['below-1x' => 'Below 1x', '1x-2x' => '1x - 2x', '2x-4x' => '2x - 4x', '4x-6x' => '4x - 6x', '6x-plus' => '6x+', 'not-sure' => 'Not sure'];

    $currentAdSpend = null;
    if (($row['has_ads'] ?? null) === 'yes') {
        $currentAdSpend = [
            'runs_ads' => true,
            'ad_spend' => $adSpendLabels[$row['ad_spend']] ?? $row['ad_spend'],
            'roas' => $roasLabels[$row['roas']] ?? $row['roas'],
            // Raw key alongside the display label - the "your growth now vs
            // with us" story slide needs to branch on this programmatically
            // (good/bad ROAS), which the human-readable label isn't safe to
            // string-match against.
            'roas_key' => $row['roas'],
        ];
    } elseif (($row['has_ads'] ?? null) === 'no') {
        $currentAdSpend = ['runs_ads' => false, 'ad_spend' => null, 'roas' => null, 'roas_key' => null];
    }

    // Only the screenshot path is ever exposed publicly from scraped_data_json -
    // the rest of that blob (per-page crawl details, etc.) stays admin-only.
    $screenshotUrl = null;
    if ($row['scraped_data_json']) {
        $scrapedData = json_decode($row['scraped_data_json'], true);
        if (!empty($scrapedData['screenshot_path'])) {
            $screenshotUrl = $appUrl . '/' . ltrim($scrapedData['screenshot_path'], '/');
        }
    }

    echo json_encode([
        'success' => true,
        'status' => $row['status'],
        'score' => $row['score'] !== null ? (int) $row['score'] : null,
        'findings' => $findings,
        'screenshot_url' => $screenshotUrl,
        'brand_brief' => $row['brand_brief'],
        'executive_summary' => $row['executive_summary'],
        'priority_actions' => $row['ai_priority_actions_json'] ? json_decode($row['ai_priority_actions_json'], true) : null,
        'ad_strategy' => $row['ai_ad_strategy_json'] ? json_decode($row['ai_ad_strategy_json'], true) : null,
        'case_study_matches' => $row['ai_case_study_matches_json'] ? json_decode($row['ai_case_study_matches_json'], true) : [],
        'current_ad_spend' => $currentAdSpend,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred.']);
}
