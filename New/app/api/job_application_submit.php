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
    // Handle multipart/form-data (because of file upload)
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $portfolioUrl = isset($_POST['portfolio_url']) ? trim($_POST['portfolio_url']) : '';
    $coverLetter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : '';
    $jobOpeningId = isset($_POST['job_opening_id']) ? intval($_POST['job_opening_id']) : null;
    
    // Validate required fields
    if (empty($fullName) || empty($email) || empty($portfolioUrl) || empty($coverLetter)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    
    // Validate email
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Validate URL
    if (!filter_var($portfolioUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid portfolio URL']);
        exit;
    }
    
    $cvFilename = null;
    $cvFilepath = null;
    
    // Handle file upload if present
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../storage/uploads/cv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only PDF, DOC, or DOCX files are allowed']);
            exit;
        }
        
        // Check file size (10MB max)
        if ($_FILES['cv']['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File size must be less than 10MB']);
            exit;
        }
        
        $cvFilename = uniqid('cv_') . '_' . time() . '.' . $fileExtension;
        $cvFilepath = $uploadDir . $cvFilename;
        
        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cvFilepath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to upload CV']);
            exit;
        }
        
        // Store relative path
        $cvFilepath = 'storage/uploads/cv/' . $cvFilename;
    }
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO job_applications (full_name, email, portfolio_url, cover_letter, cv_filename, cv_filepath, job_opening_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullName, $email, $portfolioUrl, $coverLetter, $cvFilename, $cvFilepath, $jobOpeningId, $ipAddress, $userAgent]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your application! We will review it and get back to you soon.',
        'id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.',
        'error' => $e->getMessage()
    ]);
}
