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
    
    $leadId = $pdo->lastInsertId();
    
    // Send lead to CRM webhook
    $crmWebhookUrl = 'https://crm.zingbot.io/api/external/webhook_receiver_dynamic.php?org_id=10&conn_id=47674d8c390bb72696198f81797f238f&api_key=1e3d1ca5c5fe8226550f05824b3b9b83c152dafc4ccb665f30bb798693671fd4';
    
    $leadData = [
        'id' => $leadId,
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'budget' => $budget,
        'website' => $website,
        'message' => $message,
        'ip_address' => $ipAddress,
        'user_agent' => $userAgent,
        'submitted_at' => date('Y-m-d H:i:s')
    ];
    
    $ch = curl_init($crmWebhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($leadData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Log webhook response for debugging
    $logMessage = "CRM webhook sent: HTTP $httpCode - Response: " . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : '') . ($curlError ? " - cURL Error: $curlError" : "");
    error_log($logMessage);
    
    $crmSuccess = ($httpCode >= 200 && $httpCode < 300);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your details were submitted successfully. We will get back to you within 24 hours.',
        'id' => $leadId,
        'crm_webhook' => [
            'sent' => true,
            'status_code' => $httpCode,
            'success' => $crmSuccess,
            'error' => $curlError ?: null
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.',
        'error' => $e->getMessage()
    ]);
}
