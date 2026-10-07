<?php
// Receives progress and finished results from the external website-audit
// service (tools/website_audit, deployed on Render - see AuditSpawner) and
// writes them to MySQL; the service itself never touches the database.
// Every request must carry a valid HMAC signature (AuditSignature), and the
// audit is matched on both id and token.
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/utilities/AuditSignature.php';

// The only columns a 'completed' callback may set - anything else in the
// payload is ignored.
const RESULT_COLUMNS = [
    'score', 'findings_json', 'brand_brief', 'scraped_data_json', 'ai_plan_json', 'ai_model_used',
    'executive_summary', 'ai_priority_actions_json', 'ai_ad_strategy_json', 'ai_case_study_matches_json',
];
const MAX_SCREENSHOT_BYTES = 10 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$body = file_get_contents('php://input');
if (!AuditSignature::verifyRequest($body)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid signature']);
    exit;
}

$payload = json_decode($body, true);
$auditId = is_array($payload) ? (int) ($payload['audit_id'] ?? 0) : 0;
$token = is_array($payload) ? (string) ($payload['token'] ?? '') : '';
$status = is_array($payload) ? ($payload['status'] ?? '') : '';

if ($auditId <= 0 || !preg_match('/^[a-f0-9]{32}$/', $token) || !in_array($status, ['running', 'completed', 'failed'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

// Writes the screenshot where scraped_data_json's screenshot_path (set by
// audit_runner.py) expects it. Returns false on anything that isn't a sane PNG.
function saveAuditScreenshot(string $token, string $base64): bool
{
    $png = base64_decode($base64, true);
    if ($png === false || strlen($png) > MAX_SCREENSHOT_BYTES || strncmp($png, "\x89PNG\r\n\x1a\n", 8) !== 0) {
        return false;
    }
    $dir = __DIR__ . '/../../storage/audits/screenshots/' . $token;
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        return false;
    }
    return file_put_contents($dir . '/homepage.png', $png) !== false;
}

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("SELECT id FROM website_audits WHERE id = ? AND token = ?");
    $stmt->execute([$auditId, $token]);
    if ($stmt->fetchColumn() === false) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Audit not found']);
        exit;
    }

    if ($status === 'running') {
        // The service has actually started the job (it may have waited in
        // its queue). Refreshing updated_at makes the status endpoint's
        // stuck-audit timeout count from now.
        $pdo->prepare("UPDATE website_audits SET status = 'running', updated_at = NOW() WHERE id = ? AND status IN ('pending', 'running')")
            ->execute([$auditId]);
    } elseif ($status === 'failed') {
        // A duplicate run failing must never wipe out a result that already completed.
        $errorMessage = mb_substr((string) ($payload['error_message'] ?? 'Audit failed.'), 0, 1000);
        $pdo->prepare("UPDATE website_audits SET status = 'failed', error_message = ?, updated_at = NOW() WHERE id = ? AND status <> 'completed'")
            ->execute([$errorMessage, $auditId]);
    } else {
        $fields = is_array($payload['fields'] ?? null) ? array_intersect_key($payload['fields'], array_flip(RESULT_COLUMNS)) : [];
        foreach ($fields as $column => $value) {
            if ($value !== null && !is_scalar($value)) {
                $fields[$column] = null;
            }
        }
        if (isset($fields['score'])) {
            $fields['score'] = max(0, min(100, (int) $fields['score']));
        }

        $screenshot = $payload['screenshot_png_base64'] ?? null;
        if (is_string($screenshot) && $screenshot !== '' && !saveAuditScreenshot($token, $screenshot)) {
            // The screenshot is optional - just don't link to a file that isn't there.
            $scrapedData = json_decode((string) ($fields['scraped_data_json'] ?? ''), true);
            if (is_array($scrapedData)) {
                $scrapedData['screenshot_path'] = null;
                $fields['scraped_data_json'] = json_encode($scrapedData);
            }
        }

        // Accepted even if the row was already marked failed (e.g. the status
        // endpoint timed it out while the job waited in the service's queue):
        // a real result arriving late is still worth keeping for the admin.
        $setClause = '';
        foreach (array_keys($fields) as $column) {
            $setClause .= ", `$column` = ?";
        }
        $pdo->prepare("UPDATE website_audits SET status = 'completed', error_message = NULL$setClause, updated_at = NOW() WHERE id = ?")
            ->execute(array_merge(array_values($fields), [$auditId]));
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred.']);
}
