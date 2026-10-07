<?php
// Streams one case study document to the external website-audit service
// (tools/website_audit/case_study_library.py), which extracts its text for
// the AI's case-study matching. Signed requests only (AuditSignature). POST,
// so the .htaccess GET-only clean-URL redirect never touches it.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/utilities/AuditSignature.php';
require_once __DIR__ . '/../../app/utilities/AuditSpawner.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$body = file_get_contents('php://input');
if (!AuditSignature::verifyRequest($body)) {
    http_response_code(401);
    exit;
}

$payload = json_decode($body, true);
$caseStudyId = is_array($payload) ? (int) ($payload['id'] ?? 0) : 0;
if ($caseStudyId <= 0) {
    http_response_code(400);
    exit;
}

try {
    $stmt = Database::getInstance()->prepare("SELECT file_path FROM case_studies WHERE id = ?");
    $stmt->execute([$caseStudyId]);
    $filePath = $stmt->fetchColumn();
} catch (Exception $e) {
    http_response_code(500);
    exit;
}

$absPath = $filePath ? AuditSpawner::resolveCaseStudyFile((string) $filePath) : null;
if ($absPath === null) {
    http_response_code(404);
    exit;
}

// Drop any output buffering/compression so a multi-MB file streams as-is
// and the Content-Length below stays accurate.
while (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($absPath));
readfile($absPath);
