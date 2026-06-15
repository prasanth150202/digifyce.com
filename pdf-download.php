<?php
$filename = basename($_GET['file'] ?? '');

if (!$filename || !preg_match('/^pdf_[a-zA-Z0-9._-]+\.pdf$/i', $filename)) {
    http_response_code(400);
    exit('Invalid request.');
}

$filepath = __DIR__ . '/storage/uploads/' . $filename;

if (!file_exists($filepath) || !is_file($filepath)) {
    http_response_code(404);
    exit('File not found.');
}

$downloadName = basename($_GET['name'] ?? $filename);
if (!preg_match('/\.pdf$/i', $downloadName)) {
    $downloadName .= '.pdf';
}

// Clear any buffered output so headers can be sent cleanly
while (ob_get_level()) { ob_end_clean(); }

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');

// Explicit binary-mode read — prevents Windows newline translation corrupting PDFs
$fp = fopen($filepath, 'rb');
if ($fp) {
    fpassthru($fp);
    fclose($fp);
}
exit;
