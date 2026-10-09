<?php
// Records the lead's explicit opt-in (from the closing slide of the audit
// story in leadform.php) to being kept in touch - a deliberate "yes,
// actually follow up with me" signal distinct from just having submitted
// the lead form, so the team can prioritize outreach. Shown back in
// app/admin/website_audits.php.
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $token = $_POST['token'] ?? '';
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }

    $value = (isset($_POST['value']) && $_POST['value'] === '1') ? 1 : 0;

    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('UPDATE website_audits SET keep_in_touch = ? WHERE token = ?');
    $stmt->execute([$value, $token]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Audit not found']);
        exit;
    }

    echo json_encode(['success' => true, 'keep_in_touch' => (bool) $value]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
