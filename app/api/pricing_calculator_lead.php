<?php
/**
 * Lead capture for the public pricing calculator (/pricing-calculator).
 *
 * The visitor enters their product costs, then name / phone / email before the
 * selling price is revealed. This saves that contact plus a readable summary of
 * what they priced into lead_form_submissions, so it shows up in
 * Admin → Lead Form Submissions (business_type = 'pricing_calculator'), and sends it to
 * the CRM unless the budget is ₹50,000 (then it is saved for admin only).
 */
header('Content-Type: application/json');

// Set to true to start saving calculator leads to the database. While false the
// form is still validated and the visitor still sees their price, but nothing is stored.
const PC_SAVE_LEADS = true;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

function pc_fail($code, $message) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Trim, collapse whitespace and cap the length of a free-text value.
function pc_text($value, $max) {
    $value = is_scalar($value) ? preg_replace('/\s+/u', ' ', trim((string)$value)) : '';
    return mb_substr($value, 0, $max);
}

function pc_money($value) {
    return '₹' . number_format(round((float)$value), 0, '.', ',');
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    pc_fail(400, 'Invalid request.');
}

// Honeypot: a field real visitors never see. Bots that fill it get a fake success.
if (!empty($data['website'])) {
    echo json_encode(['success' => true]);
    exit;
}

// Light throttle: at most 5 submissions per browser session every 10 minutes.
session_start();
$now = time();
$recent = array_filter($_SESSION['pc_lead_times'] ?? [], function ($t) use ($now) { return $t > $now - 600; });
if (count($recent) >= 5) {
    pc_fail(429, 'Too many attempts. Please try again in a few minutes.');
}

$fullName = pc_text($data['full_name'] ?? '', 128);
$email    = pc_text($data['email'] ?? '', 255);
$phone    = pc_text($data['phone'] ?? '', 32);
$product  = pc_text($data['product'] ?? '', 255); // comma-separated product names

// "What does your brand sell?" Same keys and labels as leadform.php.
$industryOptions = [
    'beauty_personal_care'    => 'Beauty & personal care products',
    'food_beverage_fmcg'      => 'Food, beverages & packaged consumer goods',
    'fashion_apparel_textile' => 'Fashion, apparel & textiles',
    'other_consumer_product'  => 'Other consumer product',
];
$industry = $data['industry'] ?? '';
if (!is_string($industry) || !isset($industryOptions[$industry])) {
    pc_fail(400, 'Please select what your brand sells.');
}

// "Estimated monthly marketing budget" (optional). Same keys and labels as leadform.php.
$budgetOptions = ['50000' => '₹50,000', '1lakh' => '₹1 Lakh', 'above_1lakh' => 'Above ₹1 Lakh'];
$budget = $data['budget'] ?? '';
if (!is_string($budget) || !isset($budgetOptions[$budget])) {
    $budget = null;
}

if ($fullName === '') {
    pc_fail(400, 'Please enter your name.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    pc_fail(400, 'Please enter a valid email address.');
}
$phoneDigits = preg_replace('/\D/', '', $phone);
if (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
    pc_fail(400, 'Please enter a valid phone number.');
}

// Build the summary admins read in the lead's "Message".
$lines = [
    'Pricing calculator lead',
    'Brand sells: ' . $industryOptions[$industry],
    'Monthly marketing budget: ' . ($budget ? $budgetOptions[$budget] : 'Not given'),
    'Products: ' . ($product !== '' ? $product : '(not named)'),
];
// One entry per product variant, named "Product · Variant".
$variants = is_array($data['variants'] ?? null) ? array_slice($data['variants'], 0, 100) : [];
foreach ($variants as $v) {
    if (!is_array($v)) continue;
    $costs = [
        'Mfg ' . pc_money($v['mfg'] ?? 0),
        'Pack ' . pc_money($v['pack'] ?? 0),
        'Shipping ' . pc_money($v['ship'] ?? 0),
    ];
    foreach (array_slice(is_array($v['extras'] ?? null) ? $v['extras'] : [], 0, 10) as $x) {
        if (!is_array($x)) continue;
        $costs[] = pc_text($x['name'] ?? '', 40) . ' ' . pc_text($x['value'] ?? '', 20);
    }
    $lines[] = '';
    $lines[] = '• ' . pc_text($v['name'] ?? '', 160);
    $lines[] = '  Costs: ' . implode(', ', $costs) . ' (base ' . pc_money($v['base'] ?? 0) . ')';
    $lines[] = '  Selling price ' . pc_money($v['selling_price'] ?? 0)
        . ' · Breakeven ROAS ' . pc_text($v['breakeven_roas'] ?? '', 12)
        . ' · Compare-at ' . pc_money($v['compare_at'] ?? 0)
        . ' · GST ' . pc_text($v['gst'] ?? '', 6) . '%';
}
$message = implode("\n", $lines);

// Leads with the lowest budget are kept for admin but not pushed to the CRM
// (same rule as the main lead form's $crmSkipBudgets in leadform.php).
// A lead with no budget selected is still sent.
$crmSkipBudgets = ['50000'];
$crmSkipped = $budget !== null && in_array($budget, $crmSkipBudgets, true);

if (!PC_SAVE_LEADS) {
    // Testing mode: nothing is saved or sent. "crm" says what would happen.
    echo json_encode(['success' => true, 'saved' => false, 'crm' => $crmSkipped ? 'would_skip' : 'would_send']);
    exit;
}

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
$userAgent = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

try {
    $pdo = Database::getInstance();

    // Self-heal: older databases don't have crm_skipped yet (same check as leadform.php).
    try {
        if (!$pdo->query("SHOW COLUMNS FROM lead_form_submissions LIKE 'crm_skipped'")->fetch()) {
            $pdo->exec("ALTER TABLE lead_form_submissions ADD COLUMN crm_skipped TINYINT(1) NOT NULL DEFAULT 0 AFTER is_qualified");
        }
    } catch (Exception $e) {
        error_log('pricing_calculator_lead.php crm_skipped column check failed: ' . $e->getMessage());
    }

    $stmt = $pdo->prepare(
        "INSERT INTO lead_form_submissions (full_name, email, phone, business_type, industry, budget, crm_skipped, message, ip_address, user_agent)
         VALUES (?, ?, ?, 'pricing_calculator', ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$fullName, $email, $phone, $industry, $budget, $crmSkipped ? 1 : 0, $message, $ipAddress, $userAgent]);
    $leadId = (int) $pdo->lastInsertId();
} catch (Exception $e) {
    error_log('pricing_calculator_lead.php: ' . $e->getMessage());
    pc_fail(500, 'Something went wrong. Please try again.');
}

$recent[] = $now;
$_SESSION['pc_lead_times'] = array_values($recent);
session_write_close(); // don't hold the session lock while waiting on the CRM

// Send to the CRM webhook (same endpoint and field names as leadform.php).
// The lead is already saved, so a CRM failure is only logged; the visitor still gets their prices.
if ($crmSkipped) {
    error_log("pricing_calculator_lead.php: lead $leadId saved but not sent to CRM (budget=$budget)");
} else {
    $crmWebhookUrl = 'https://crm.zingbot.io/api/external/webhook_receiver_dynamic.php?org_id=10&conn_id=47674d8c390bb72696198f81797f238f&api_key=1e3d1ca5c5fe8226550f05824b3b9b83c152dafc4ccb665f30bb798693671fd4';
    $leadData = [
        'id'           => $leadId,
        'source'       => 'Pricing calculator',
        'full_name'    => $fullName,
        'email'        => $email,
        'phone'        => $phone,
        'industry'     => $industryOptions[$industry],
        'budget'       => $budget ? $budgetOptions[$budget] : '',
        'submitted_at' => date('Y-m-d H:i:s'),
    ];
    // Only contact + brand details go to the CRM. The visitor's products, costs
    // and prices ($message) stay in our admin only.

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

    error_log("CRM webhook sent from pricing_calculator_lead.php (lead $leadId): HTTP $httpCode - Response: "
        . substr((string) $response, 0, 500) . ($curlError ? " - cURL Error: $curlError" : ''));
}

echo json_encode(['success' => true]);
