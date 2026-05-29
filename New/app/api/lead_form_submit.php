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
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['full_name', 'email', 'message'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            exit;
        }
    }
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    $fullName = trim($data['full_name']);
    $phone = isset($data['phone']) ? trim($data['phone']) : null;
    $company = isset($data['company']) ? trim($data['company']) : null;
    $budget = isset($data['budget']) ? trim($data['budget']) : null;
    $website = isset($data['website']) ? trim($data['website']) : null;
    $message = trim($data['message']);
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO lead_form_submissions (full_name, email, phone, company, budget, website, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullName, $email, $phone, $company, $budget, $website, $message, $ipAddress, $userAgent]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your details were submitted successfully. We will get back to you within 24 hours.',
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
