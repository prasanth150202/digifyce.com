<?php
/**
 * Sync existing leads to CRM webhook
 * Usage: php sync_existing_leads.php [limit]
 * For web access: https://yourdomain.com/app/api/sync_existing_leads.php?limit=100&auth_key=YOUR_KEY
 */

require_once __DIR__ . '/../../config/database.php';

// Security: Check authorization (optional - remove for CLI-only use)
$isWebAccess = isset($_GET['auth_key']);
$isCliAccess = (php_sapi_name() === 'cli');

if ($isWebAccess) {
    // Simple auth check for web access
    $expectedKey = 'sync_leads_secret_key_change_this';
    $providedKey = $_GET['auth_key'] ?? '';
    if ($providedKey !== $expectedKey) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Get limit from CLI argument or GET parameter
$limit = 100;
if ($isCliAccess && isset($argv[1])) {
    $limit = (int)$argv[1];
} elseif ($isWebAccess && isset($_GET['limit'])) {
    $limit = (int)$_GET['limit'];
}

$crmWebhookUrl = 'https://crm.zingbot.io/api/external/webhook_receiver_dynamic.php?org_id=10&conn_id=47674d8c390bb72696198f81797f238f&api_key=1e3d1ca5c5fe8226550f05824b3b9b83c152dafc4ccb665f30bb798693671fd4';

try {
    $pdo = Database::getInstance();
    
    // Fetch leads from database
    $stmt = $pdo->prepare("SELECT * FROM lead_form_submissions ORDER BY id DESC LIMIT ?");
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($leads)) {
        $message = 'No leads found to sync.';
        if ($isWebAccess) {
            echo json_encode(['success' => false, 'message' => $message, 'leads_count' => 0]);
        } else {
            echo $message . PHP_EOL;
        }
        exit;
    }
    
    $successCount = 0;
    $failureCount = 0;
    $errors = [];
    
    foreach ($leads as $lead) {
        $leadData = [
            'id' => $lead['id'],
            'full_name' => $lead['full_name'],
            'email' => $lead['email'],
            'phone' => $lead['phone'],
            'company' => $lead['company'],
            'budget' => $lead['budget'],
            'website' => $lead['website'],
            'message' => $lead['message'],
            'ip_address' => $lead['ip_address'],
            'user_agent' => $lead['user_agent'],
            'submitted_at' => $lead['created_at'] ?? $lead['submission_date'] ?? date('Y-m-d H:i:s'),
            'sync_type' => 'historical_sync'
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
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $successCount++;
            $logMsg = "✓ Lead ID {$lead['id']} ({$lead['email']}) sent successfully (HTTP $httpCode)";
        } else {
            $failureCount++;
            $errorMsg = $curlError ?: "HTTP $httpCode";
            $errors[] = "Lead ID {$lead['id']}: $errorMsg";
            $logMsg = "✗ Lead ID {$lead['id']} ({$lead['email']}) failed: $errorMsg";
        }
        
        if ($isCliAccess) {
            echo $logMsg . PHP_EOL;
        }
        error_log($logMsg);
    }
    
    $result = [
        'success' => true,
        'message' => "Sync completed: $successCount successful, $failureCount failed",
        'total_leads' => count($leads),
        'successful' => $successCount,
        'failed' => $failureCount,
        'errors' => $errors
    ];
    
    if ($isWebAccess) {
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        echo PHP_EOL . "=== Sync Summary ===" . PHP_EOL;
        echo "Total Leads: " . $result['total_leads'] . PHP_EOL;
        echo "Successful: " . $result['successful'] . PHP_EOL;
        echo "Failed: " . $result['failed'] . PHP_EOL;
        if (!empty($errors)) {
            echo PHP_EOL . "Errors:" . PHP_EOL;
            foreach ($errors as $error) {
                echo "  - $error" . PHP_EOL;
            }
        }
    }
    
} catch (Exception $e) {
    $error = [
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ];
    
    if ($isWebAccess) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode($error);
    } else {
        echo "ERROR: " . $e->getMessage() . PHP_EOL;
    }
    error_log("Sync leads error: " . $e->getMessage());
}
