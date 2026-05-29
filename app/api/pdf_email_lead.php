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
    if (!isset($data['email']) || empty($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    $source = $data['source'] ?? 'strategy_matrix';
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO pdf_email_leads (email, source, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $source, $ipAddress, $userAgent]);
    // ---------------------------
    // CONFIG (per client script)
    // ---------------------------
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim(trim($value), '"\'');
                if (!getenv($name)) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                }
            }
        }
    }
    $BREVO_API_KEY = getenv('BREVO_API_KEY') ?: ($_ENV['BREVO_API_KEY'] ?? "");
 
    $SENDER_EMAIL = "no-reply@info.digifyce.com";
    $SENDER_NAME  = "Digifyce";
 
    // ---------------------------
    // Rate safety (IMPORTANT)
    // ---------------------------
    usleep(500000);
 
    $mailSubject = "Your Strategy Matrix Framework is Ready - Digifyce";
    $mailTags = ["Website_PDF"];
    $mailBody = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:Poppins,Arial,sans-serif;">
<center>
<table width="100%" style="background:#ffffff;padding:28px 16px;">
<tr><td align="center">
 
<table width="520" style="background:#0b1e3a;border-radius:18px;box-shadow:0 8px 24px rgba(0,0,0,0.15);overflow:hidden;">
<tr>
<td align="center" style="padding:26px 0 10px;">
<img src="https://digifyce.com/public/assets/site/wlogo---Copy-1770034043.png" width="140" />
</td>
</tr>
 
<tr>
<td style="padding:28px 26px;text-align:center;color:#ffffff;">
 
<p style="font-size:22px;font-weight:600;margin:0 0 16px;color:#ffffff;">
Your Strategy Matrix Framework is Ready
</p>
 
<p style="font-size:16px;line-height:1.7;margin:0 0 16px;color:#e6edf7;">
Thank you for your interest in Digifyce Performance Marketing Strategy Matrix.
</p>
 
<p style="font-size:16px;line-height:1.7;margin:0 0 16px;color:#e6edf7;">
This is not a generic guide. It is a structured diagnostic framework used to evaluate and scale advertising performance by analyzing two critical variables click-through rate and conversion rate together, not in isolation.
</p>
 
<p style="font-size:16px;line-height:1.7;margin:0 0 16px;color:#e6edf7;">
Inside, you will learn how to identify whether your campaigns are operating in a zone of efficiency, friction, wasted spend, or untapped opportunity and exactly what actions to take in each case.
</p>
 
<p style="font-size:15px;line-height:1.6;margin:0 0 20px;color:#c9d6ea;">
You can access the full framework below.
</p>
 
<a href="https://digifyce.com/app/api/Digifyce.pdf"
   target="_blank"
   style="display:inline-block;background:#2f6fed;color:#ffffff;
   padding:14px 28px;border-radius:8px;font-size:15px;
   font-weight:600;text-decoration:none;">
   Access the Strategy Matrix
</a>
 
<tr>
<td style="padding:0 24px 26px;text-align:center;font-size:12px;color:#9fb3d1;">
Team Digifyce<br>
 
</td>
</tr>
 
</table>
 
</td></tr>
</table>
</center>
</body>
</html>';
 
    $payload = [
        "sender" => [
            "email" => $SENDER_EMAIL,
            "name"  => $SENDER_NAME
        ],
        "to" => [
            ["email" => $email]
        ],
        "subject" => $mailSubject,
        "htmlContent" => $mailBody,
        "tags" => $mailTags
    ];
 
    $ch = curl_init("https://api.brevo.com/v3/smtp/email");
 
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "api-key: $BREVO_API_KEY",
            "content-type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);
 
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
    curl_close($ch);
 
    if ($status >= 200 && $status < 300) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Check your email for the PDF.',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email. Please try again later.',
            'error' => 'Brevo API error',
            'brevo_response' => json_decode($response)
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.',
        'error' => $e->getMessage()
    ]);
}