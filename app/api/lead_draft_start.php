<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/utilities/AuditTrigger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;

    $hasWebsite = isset($_POST['has_website']) && in_array($_POST['has_website'], ['yes', 'no'], true)
        ? $_POST['has_website']
        : null;
    $website = ($hasWebsite === 'yes' && isset($_POST['website'])) ? trim($_POST['website']) : null;
    $wantAudit = isset($_POST['want_audit']) && in_array($_POST['want_audit'], ['yes', 'no'], true)
        ? $_POST['want_audit']
        : null;

    // This endpoint exists solely to front-load an audit - if the request
    // doesn't actually describe an audit-eligible answer, there's nothing
    // useful for it to do (the normal end-of-form submit already handles
    // every other case).
    if (empty($fullName) || $hasWebsite !== 'yes' || $wantAudit !== 'yes' || empty($website)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Not eligible for an early audit start.']);
        exit;
    }

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        "INSERT INTO lead_form_submissions (status, full_name, email, phone, has_website, website, message, ip_address, user_agent)
         VALUES ('draft', ?, ?, ?, ?, ?, '', ?, ?)"
    );
    $stmt->execute([$fullName, $email, $phone, $hasWebsite, $website, $ipAddress, $userAgent]);
    $leadId = (int) $pdo->lastInsertId();

    $_SESSION['draft_lead_id'] = $leadId;

    $auditResult = AuditTrigger::maybeStart($pdo, $leadId, $ipAddress, $hasWebsite, $wantAudit, $website);
    // Stashed so the final submit (which reuses this draft row instead of
    // calling maybeStart again) can still show the right message if this
    // early attempt was quota-blocked.
    $_SESSION['draft_audit_reason'] = $auditResult['reason'];

    echo json_encode(['success' => true, 'audit_token' => $auditResult['token'], 'audit_reason' => $auditResult['reason']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred.']);
}
