<?php
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
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    $email        = trim($data['email'] ?? '');
    $phone        = trim($data['phone'] ?? '');
    $customFields = $data['custom_fields'] ?? null;
    $pdfFilename  = basename($data['pdf_filename'] ?? '');
    $pdfLabel     = trim($data['pdf_label'] ?? '');
    $blogId       = intval($data['blog_id'] ?? 0) ?: null;
    $blogSlug     = trim($data['blog_slug'] ?? '') ?: null;
    $blogTitle    = trim($data['blog_title'] ?? '') ?: null;
    $ipAddress    = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent    = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Validate email format if provided
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    $pdo = Database::getInstance();

    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_pdf_leads (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        blog_id       INT          DEFAULT NULL,
        blog_slug     VARCHAR(255) DEFAULT NULL,
        blog_title    VARCHAR(500) DEFAULT NULL,
        pdf_filename  VARCHAR(255) DEFAULT NULL,
        pdf_label     VARCHAR(255) DEFAULT NULL,
        email         VARCHAR(255) DEFAULT NULL,
        phone         VARCHAR(50)  DEFAULT NULL,
        custom_fields JSON         DEFAULT NULL,
        ip_address    VARCHAR(45)  DEFAULT NULL,
        user_agent    TEXT,
        created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_blog   (blog_id),
        INDEX idx_date   (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmt = $pdo->prepare(
        "INSERT INTO blog_pdf_leads
         (blog_id, blog_slug, blog_title, pdf_filename, pdf_label, email, phone, custom_fields, ip_address, user_agent)
         VALUES (?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->execute([
        $blogId,
        $blogSlug,
        $blogTitle,
        $pdfFilename ?: null,
        $pdfLabel    ?: null,
        $email       ?: null,
        $phone       ?: null,
        is_array($customFields) && count($customFields) ? json_encode($customFields) : null,
        $ipAddress,
        $userAgent,
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.', 'error' => $e->getMessage()]);
}
