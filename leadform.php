<?php
// Output buffering: header.php below emits a large amount of HTML before we
// know whether this request needs to redirect (see the POST handler further
// down). Without buffering, header('Location: ...') would fail with
// "headers already sent" once that HTML has been flushed to the client.
ob_start();
session_start();
$pageTitle = 'Contact Digifyce – Request a Consultation';
$pageDescription = 'Get in touch with Digifyce for a consultation, growth strategy session, or marketing inquiry. Let\'s accelerate your business success.';
include __DIR__ . '/app/views/header.php';
?>
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/utilities/AuditTrigger.php';
$selectedServicesText = '';

if (!empty($_GET['services'])) {
    $services = $_GET['services'];
    $selectedServicesText = "I am interested in: " . implode(', ', $services) . ".";
}

$businessTypeOptions = ['D2C Brand', 'E-commerce', 'Retail/Offline', 'Service Business', 'Startup', 'Others'];
$industryOptions = ['Clothing & Fashion', 'Food & Beverage', 'Beauty & Personal Care', 'Health & Wellness', 'Electronics & Gadgets', 'Home & Furniture', 'Jewelry & Accessories', 'Education', 'Real Estate', 'Professional Services', 'Others'];
$adSpendOptions = ['under-25k' => 'Under ₹25k', '25k-50k' => '₹25k - ₹50k', '50k-1l' => '₹50k - ₹1L', '1l-3l' => '₹1L - ₹3L', '3l-plus' => '₹3L+'];
$roasOptions = ['below-1x' => 'Below 1x', '1x-2x' => '1x - 2x', '2x-4x' => '2x - 4x', '4x-6x' => '4x - 6x', '6x-plus' => '6x+', 'not-sure' => 'Not sure'];

$submitted = false;
$errorMessage = '';
$auditToken = null;
$auditUnavailable = false;
$auditQuotaExceeded = false;

if (isset($_GET['success'])) {
    $submitted = true;
    if (isset($_GET['audit']) && preg_match('/^[a-f0-9]{32}$/', $_GET['audit'])) {
        $auditToken = $_GET['audit'];
    } elseif (isset($_GET['audit_quota_exceeded'])) {
        $auditQuotaExceeded = true;
    } elseif (isset($_GET['audit_unavailable'])) {
        $auditUnavailable = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        $company = isset($_POST['company']) ? trim($_POST['company']) : null;

        $businessType = isset($_POST['business_type']) ? trim($_POST['business_type']) : '';
        if ($businessType === 'Others') {
            $customType = isset($_POST['business_type_other']) ? trim($_POST['business_type_other']) : '';
            if ($customType !== '') {
                $businessType = $customType;
            }
        }

        $industry = isset($_POST['industry']) ? trim($_POST['industry']) : '';
        if ($industry === 'Others') {
            $customIndustry = isset($_POST['industry_other']) ? trim($_POST['industry_other']) : '';
            if ($customIndustry !== '') {
                $industry = $customIndustry;
            }
        }

        $budget = isset($_POST['budget']) ? trim($_POST['budget']) : null;

        $hasAds = isset($_POST['has_ads']) && in_array($_POST['has_ads'], ['yes', 'no'], true)
            ? $_POST['has_ads']
            : null;
        $adSpend = ($hasAds === 'yes' && isset($_POST['ad_spend'])) ? trim($_POST['ad_spend']) : null;
        $roas = ($hasAds === 'yes' && isset($_POST['roas'])) ? trim($_POST['roas']) : null;

        $hasWebsite = isset($_POST['has_website']) && in_array($_POST['has_website'], ['yes', 'no'], true)
            ? $_POST['has_website']
            : null;
        $website = ($hasWebsite === 'yes' && isset($_POST['website'])) ? trim($_POST['website']) : null;
        $wantAudit = isset($_POST['want_audit']) && in_array($_POST['want_audit'], ['yes', 'no'], true)
            ? $_POST['want_audit']
            : null;

        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        if (
            empty($fullName) || empty($email) || empty($message) ||
            $businessType === '' || $industry === '' || $hasAds === null ||
            ($hasAds === 'yes' && (empty($adSpend) || empty($roas))) ||
            $hasWebsite === null ||
            ($hasWebsite === 'yes' && empty($website))
        ) {
            $errorMessage = 'Please fill in all required fields.';
        } else {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $errorMessage = 'Please enter a valid email address.';
            } else {
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

                $pdo = Database::getInstance();

                // If step 2 (Website) already created a draft lead + started an
                // audit in the background, finish that same row instead of
                // inserting a second one. The draft id lives only in the
                // server-side session (never trusted from the client), so a
                // tampered/foreign id can't hijack someone else's draft.
                $draftLeadId = isset($_SESSION['draft_lead_id']) ? (int) $_SESSION['draft_lead_id'] : null;
                $draftExists = false;
                if ($draftLeadId) {
                    $draftStmt = $pdo->prepare("SELECT id FROM lead_form_submissions WHERE id = ? AND status = 'draft'");
                    $draftStmt->execute([$draftLeadId]);
                    $draftExists = (bool) $draftStmt->fetchColumn();
                }

                if ($draftExists) {
                    $stmt = $pdo->prepare(
                        "UPDATE lead_form_submissions SET
                            full_name = ?, email = ?, phone = ?, company = ?, business_type = ?, industry = ?,
                            budget = ?, has_ads = ?, ad_spend = ?, roas = ?, has_website = ?, website = ?,
                            message = ?, ip_address = ?, user_agent = ?, status = 'submitted'
                         WHERE id = ?"
                    );
                    $stmt->execute([$fullName, $email, $phone, $company, $businessType, $industry, $budget, $hasAds, $adSpend, $roas, $hasWebsite, $website, $message, $ipAddress, $userAgent, $draftLeadId]);
                    $leadId = $draftLeadId;

                    // The audit (if eligible) already started when the draft was
                    // created - never trigger a second one for the same lead.
                    $tokenStmt = $pdo->prepare("SELECT token FROM website_audits WHERE lead_id = ? ORDER BY id DESC LIMIT 1");
                    $tokenStmt->execute([$leadId]);
                    $newAuditToken = $tokenStmt->fetchColumn() ?: null;
                    $auditReason = $_SESSION['draft_audit_reason'] ?? null;

                    unset($_SESSION['draft_lead_id'], $_SESSION['draft_audit_reason']);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO lead_form_submissions (full_name, email, phone, company, business_type, industry, budget, has_ads, ad_spend, roas, has_website, website, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$fullName, $email, $phone, $company, $businessType, $industry, $budget, $hasAds, $adSpend, $roas, $hasWebsite, $website, $message, $ipAddress, $userAgent]);
                    $leadId = (int) $pdo->lastInsertId();

                    // Fallback path: the early draft-start request never fired or
                    // failed (JS disabled, network blip) - trigger the audit now,
                    // exactly as before this feature existed.
                    $auditResult = AuditTrigger::maybeStart($pdo, $leadId, $ipAddress, $hasWebsite, $wantAudit, $website);
                    $newAuditToken = $auditResult['token'];
                    $auditReason = $auditResult['reason'];
                }

                // Send lead to CRM webhook
                $crmWebhookUrl = 'https://crm.zingbot.io/api/external/webhook_receiver_dynamic.php?org_id=10&conn_id=47674d8c390bb72696198f81797f238f&api_key=1e3d1ca5c5fe8226550f05824b3b9b83c152dafc4ccb665f30bb798693671fd4';

                $leadData = [
                    'id' => $leadId,
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'company' => $company,
                    'business_type' => $businessType,
                    'industry' => $industry,
                    'budget' => $budget,
                    'has_ads' => $hasAds,
                    'ad_spend' => $adSpend,
                    'roas' => $roas,
                    'has_website' => $hasWebsite,
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
                error_log("CRM webhook sent from leadform.php: HTTP $httpCode - Response: " . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : '') . ($curlError ? " - cURL Error: $curlError" : ""));

                // Redirect (not re-render) so refreshing the thank-you page never
                // re-submits the lead, and the audit token gets a bookmarkable URL.
                $redirectUrl = $appUrl . '/leadform?success=1';
                if ($newAuditToken) {
                    $redirectUrl .= '&audit=' . $newAuditToken;
                } elseif ($hasWebsite === 'yes' && $wantAudit === 'yes') {
                    // An audit was requested but never got a token - say so
                    // instead of silently showing nothing, and distinguish
                    // "you've already used your free audits for this site"
                    // from every other skip reason (rate-limited, unsafe URL).
                    if (($auditReason ?? null) === 'quota_exceeded') {
                        $redirectUrl .= '&audit_quota_exceeded=1';
                    } else {
                        $redirectUrl .= '&audit_unavailable=1';
                    }
                }
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                header('Location: ' . $redirectUrl);
                exit;
            }
        }
    } catch (Exception $e) {
        $errorMessage = 'An error occurred. Please try again later.';
    }
}
?>

<?php if ($submitted): ?>
<section class="relative bg-[#030508] min-h-screen py-16 sm:py-20 lg:py-24">
    <div class="max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div id="submissionThankYou" class="text-center" style="margin-bottom: 2rem; margin-top: 2rem;">
            <div class="mx-auto mb-6 w-14 h-14 rounded-full bg-green-500/10 border border-green-500/30 flex items-center justify-center text-green-300 text-2xl">✓</div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tighter">Thank You!</h1>
            <p class="mt-3 text-slate-400 text-base sm:text-lg">Your details were submitted successfully. We will get back to you within 24 hours.</p>
        </div>

        <?php if ($auditToken): ?>
        <div id="auditStatusPanel" class="border-t border-white/10 pt-12">
            <div id="auditPending" class="text-center ">
                <div class="mx-auto mb-4 w-8 h-8 border-2 border-white/20 border-t-[var(--electric-blue)] rounded-full animate-spin"></div>
                <p class="text-base text-slate-300">Auditing your website<span id="auditDots">...</span></p>
                <p class="mt-2 text-sm text-slate-500">We're crawling every page of your site, so this can take a few minutes. You can leave this page open, or bookmark it to check back later.</p>
            </div>
            <div id="auditResult" class="hidden"></div>
            <div id="auditFailed" class="hidden text-center text-sm text-slate-400 py-10">
                We couldn't complete an automatic audit of your site. Our team will take a manual look and follow up with you.
            </div>
        </div>
        <?php elseif ($auditQuotaExceeded): ?>
        <div class="border-t border-white/10 pt-12 text-center text-sm text-slate-400">
            <p class="mb-1">You've already used your free automatic audits for this website.</p>
            <p>Our team will still take a manual look and include the results when we follow up.</p>
        </div>
        <?php elseif ($auditUnavailable): ?>
        <div class="border-t border-white/10 pt-12 text-center text-sm text-slate-400">
            We couldn't start an automatic audit of your website right now. Our team will review it manually and include the results when we follow up.
        </div>
        <?php endif; ?>

        <div class="text-center mt-12">
            <a id="reportCta" href="<?= htmlspecialchars($appUrl) ?>"
                class="inline-block bg-[var(--electric-blue)] text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[var(--navy-black)] transition-all">
                Back to Home
            </a>
        </div>
    </div>
</section>
<?php else: ?>
<section class="relative py-16 sm:py-20 lg:py-24 bg-[#030508]">
    <div class="relative w-[90%] max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative border border-white/10 rounded-xl p-6 sm:p-10 lg:p-14">

            <a href="<?= htmlspecialchars($appUrl) ?>" aria-label="Close"
                class="absolute top-5 right-5 sm:top-6 sm:right-6 w-9 h-9 flex items-center justify-center rounded-full border border-white/10 text-white/60 hover:text-white hover:border-white/30 transition-colors">
                &#10005;
            </a>

                <div class="mb-8 sm:mb-10 text-center">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tighter">Tell Us About Your Project</h1>
                    <p class="mt-3 text-slate-500 text-sm sm:text-base">Share a few details and we’ll get back with a growth plan.</p>
                </div>

                <?php if ($errorMessage): ?>
                <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-center text-red-300 text-sm">
                    ⚠ <?= htmlspecialchars($errorMessage) ?>
                </div>
                <?php endif; ?>

                <!-- Progress bar -->
                <div class="mb-8 sm:mb-10">
                    <div class="flex items-center justify-between mb-3">
                        <span id="stepLabel" class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Step 1 of 5 — About You</span>
                        <span id="stepPercent" class="text-[10px] uppercase tracking-[0.3em] text-[var(--electric-blue)]">20%</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                        <div id="progressBar" class="h-full bg-[var(--electric-blue)] rounded-full transition-all duration-500 ease-out" style="width: 20%;"></div>
                    </div>
                </div>

                <form method="post" id="leadForm" novalidate>
                    <input type="hidden" name="has_website" id="hasWebsiteInput" value="">
                    <input type="hidden" name="want_audit" id="wantAuditInput" value="">
                    <input type="hidden" name="has_ads" id="hasAdsInput" value="">

                    <!-- Step 1: About You -->
                    <div class="form-step grid grid-cols-1 md:grid-cols-2 gap-6" data-step="0">
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Full Name <span class="text-[var(--electric-blue)]">*</span></label>
                            <input name="full_name" required
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="Your name" />
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please enter your full name.</p>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Email <span class="text-[var(--electric-blue)]">*</span></label>
                            <input type="email" name="email" required
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="you@company.com" />
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please enter a valid email address.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Phone</label>
                            <input type="tel" name="phone"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="+1 (555) 000-0000" />
                        </div>
                    </div>

                    <!-- Step 2: Website -->
                    <div class="form-step grid grid-cols-1 md:grid-cols-2 gap-6 hidden" data-step="1">
                        <div class="md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500 block mb-3">Do you have a website? <span class="text-[var(--electric-blue)]">*</span></label>
                            <div class="grid grid-cols-2 gap-4" id="hasWebsiteGroup">
                                <button type="button" data-value="yes"
                                    class="has-website-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    Yes
                                </button>
                                <button type="button" data-value="no"
                                    class="has-website-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    No
                                </button>
                            </div>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please let us know if you have a website.</p>
                        </div>
                        <div id="websiteUrlWrap" class="hidden md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Website URL <span class="text-[var(--electric-blue)]">*</span></label>
                            <input name="website" id="websiteInput"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="https://" />
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please enter your website URL.</p>
                        </div>
                        <div id="wantAuditWrap" class="hidden md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500 block mb-3">Want a free audit of your website? <span class="text-[var(--electric-blue)]">*</span></label>
                            <div class="grid grid-cols-2 gap-4" id="wantAuditGroup">
                                <button type="button" data-value="yes"
                                    class="audit-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    Yes, audit it
                                </button>
                                <button type="button" data-value="no"
                                    class="audit-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    No thanks
                                </button>
                            </div>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please let us know if you'd like a free audit.</p>
                            <p id="auditTimeNote" class="hidden mt-3 text-xs text-slate-500">We'll start it right away and it'll run in the background while you finish the rest of this form — usually done in 1–3 minutes.</p>
                        </div>
                    </div>

                    <!-- Step 3: Your Business -->
                    <div class="form-step grid grid-cols-1 md:grid-cols-2 gap-6 hidden" data-step="2">
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Company</label>
                            <input name="company"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="Company name" />
                        </div>
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Business Type <span class="text-[var(--electric-blue)]">*</span></label>
                            <select name="business_type" id="businessTypeSelect" required
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                                <option value="" class="bg-[#030508]">Select business type</option>
                                <?php foreach ($businessTypeOptions as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" class="bg-[#030508]"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please select your business type.</p>
                        </div>
                        <div id="businessTypeOtherWrap" class="hidden md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Please Specify <span class="text-[var(--electric-blue)]">*</span></label>
                            <input name="business_type_other" id="businessTypeOtherInput"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="Tell us your business type" />
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please tell us your business type.</p>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Monthly Budget</label>
                            <select name="budget"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                                <option value="" class="bg-[#030508]">Select budget</option>
                                <option value="50k-1l" class="bg-[#030508]">₹50k - ₹1L</option>
                                <option value="1l-1.5l" class="bg-[#030508]">₹1L - ₹1.5L</option>
                                <option value="1.5l-2l" class="bg-[#030508]">₹1.5L - ₹2L</option>
                                <option value="2l-3l" class="bg-[#030508]">₹2L - ₹3L</option>
                                <option value="3l+" class="bg-[#030508]">₹3L+</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 4: Marketing -->
                    <div class="form-step grid grid-cols-1 md:grid-cols-2 gap-6 hidden" data-step="3">
                        <div class="md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Industry <span class="text-[var(--electric-blue)]">*</span></label>
                            <select name="industry" id="industrySelect" required
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                                <option value="" class="bg-[#030508]">Select industry</option>
                                <?php foreach ($industryOptions as $opt): ?>
                                <option value="<?= htmlspecialchars($opt) ?>" class="bg-[#030508]"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please select your industry.</p>
                        </div>
                        <div id="industryOtherWrap" class="hidden md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Please Specify <span class="text-[var(--electric-blue)]">*</span></label>
                            <input name="industry_other" id="industryOtherInput"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="Tell us your industry" />
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please tell us your industry.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500 block mb-3">Do you currently run paid ads? <span class="text-[var(--electric-blue)]">*</span></label>
                            <div class="grid grid-cols-2 gap-4" id="hasAdsGroup">
                                <button type="button" data-value="yes"
                                    class="has-ads-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    Yes
                                </button>
                                <button type="button" data-value="no"
                                    class="has-ads-option rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white hover:border-[var(--electric-blue)] transition-colors">
                                    No
                                </button>
                            </div>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please let us know if you currently run paid ads.</p>
                        </div>
                        <div id="adSpendWrap" class="hidden">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Monthly Ad Spend <span class="text-[var(--electric-blue)]">*</span></label>
                            <select name="ad_spend" id="adSpendSelect"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                                <option value="" class="bg-[#030508]">Select ad spend</option>
                                <?php foreach ($adSpendOptions as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>" class="bg-[#030508]"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please select your monthly ad spend.</p>
                        </div>
                        <div id="roasWrap" class="hidden">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Current ROAS <span class="text-[var(--electric-blue)]">*</span></label>
                            <select name="roas" id="roasSelect"
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                                <option value="" class="bg-[#030508]">Select ROAS</option>
                                <?php foreach ($roasOptions as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>" class="bg-[#030508]"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please select your current ROAS.</p>
                        </div>
                    </div>

                    <!-- Step 5: Project Details -->
                    <div class="form-step space-y-6 hidden" data-step="4">
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Project Details <span class="text-[var(--electric-blue)]">*</span></label>
                            <textarea name="message" rows="5" required
                                class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                                placeholder="Tell us about your goals..."><?= htmlspecialchars($selectedServicesText) ?></textarea>
                            <p class="field-error hidden mt-2 text-xs text-red-400">Please tell us about your project.</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-10 flex items-center justify-between gap-4">
                        <button type="button" id="backBtn"
                            class="invisible text-[10px] uppercase tracking-[0.3em] text-slate-500 hover:text-white transition-colors px-4 py-3">
                            &larr; Back
                        </button>
                        <button type="button" id="nextBtn"
                            class="bg-[var(--electric-blue)] text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[var(--navy-black)] transition-all">
                            Next
                        </button>
                        <button type="submit" id="submitBtn"
                            class="hidden bg-[var(--electric-blue)] text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[var(--navy-black)] transition-all">
                            Submit Request
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-[10px] uppercase tracking-[0.3em] text-slate-600">We respond within 24 hours</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!$submitted): ?>
<script>
(function () {
    var form = document.getElementById('leadForm');
    if (!form) return;

    var steps = Array.prototype.slice.call(form.querySelectorAll('.form-step'));
    var stepLabels = ['About You', 'Website', 'Your Business', 'Marketing', 'Project Details'];
    var backBtn = document.getElementById('backBtn');
    var nextBtn = document.getElementById('nextBtn');
    var submitBtn = document.getElementById('submitBtn');
    var progressBar = document.getElementById('progressBar');
    var stepLabelEl = document.getElementById('stepLabel');
    var stepPercentEl = document.getElementById('stepPercent');
    var current = 0;

    function showStep(index) {
        steps.forEach(function (step, i) {
            step.classList.toggle('hidden', i !== index);
        });
        var pct = Math.round(((index + 1) / steps.length) * 100);
        progressBar.style.width = pct + '%';
        stepLabelEl.textContent = 'Step ' + (index + 1) + ' of ' + steps.length + ' — ' + stepLabels[index];
        stepPercentEl.textContent = pct + '%';
        backBtn.classList.toggle('invisible', index === 0);
        nextBtn.classList.toggle('hidden', index === steps.length - 1);
        submitBtn.classList.toggle('hidden', index !== steps.length - 1);
    }

    function setFieldError(field, hasError) {
        var wrap = field.closest('div');
        var errorEl = wrap ? wrap.querySelector('.field-error') : null;
        field.classList.toggle('border-red-500', hasError);
        if (errorEl) errorEl.classList.toggle('hidden', !hasError);
    }

    function isVisible(el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    }

    var hasWebsiteInput = document.getElementById('hasWebsiteInput');
    var hasWebsiteGroup = document.getElementById('hasWebsiteGroup');
    var wantAuditInput = document.getElementById('wantAuditInput');
    var wantAuditGroup = document.getElementById('wantAuditGroup');
    var hasAdsInput = document.getElementById('hasAdsInput');
    var hasAdsGroup = document.getElementById('hasAdsGroup');

    function validateStep(index) {
        var valid = true;
        var step = steps[index];

        Array.prototype.forEach.call(step.querySelectorAll('input[required], select[required], textarea[required]'), function (field) {
            if (!isVisible(field)) return;
            var value = field.value.trim();
            var fieldValid = value.length > 0;
            if (fieldValid && field.type === 'email') {
                fieldValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            }
            if (!fieldValid) valid = false;
            setFieldError(field, !fieldValid);
        });

        if (hasWebsiteGroup && step.contains(hasWebsiteGroup)) {
            var groupError = hasWebsiteGroup.parentElement.querySelector('.field-error');
            if (!hasWebsiteInput.value) {
                valid = false;
                if (groupError) groupError.classList.remove('hidden');
            } else if (groupError) {
                groupError.classList.add('hidden');
            }

            if (hasWebsiteInput.value === 'yes' && wantAuditGroup) {
                var auditGroupError = wantAuditGroup.parentElement.querySelector('.field-error');
                if (!wantAuditInput.value) {
                    valid = false;
                    if (auditGroupError) auditGroupError.classList.remove('hidden');
                } else if (auditGroupError) {
                    auditGroupError.classList.add('hidden');
                }
            }
        }

        if (hasAdsGroup && step.contains(hasAdsGroup)) {
            var adsGroupError = hasAdsGroup.parentElement.querySelector('.field-error');
            if (!hasAdsInput.value) {
                valid = false;
                if (adsGroupError) adsGroupError.classList.remove('hidden');
            } else if (adsGroupError) {
                adsGroupError.classList.add('hidden');
            }
        }

        return valid;
    }

    // Fire-and-forget: as soon as the website/audit question is answered
    // (step index 1), start the audit in the background so it's already
    // running (maybe even done) by the time the rest of the form is filled
    // in. If this fails silently, final submit falls back to triggering the
    // audit itself - nothing else needs to happen here.
    var draftAuditStarted = false;
    function startDraftAudit() {
        draftAuditStarted = true;
        var fullNameInput = form.querySelector('[name="full_name"]');
        var emailInput = form.querySelector('[name="email"]');
        var phoneInput = form.querySelector('[name="phone"]');
        var body = new URLSearchParams();
        body.set('full_name', fullNameInput ? fullNameInput.value : '');
        body.set('email', emailInput ? emailInput.value : '');
        body.set('phone', phoneInput ? phoneInput.value : '');
        body.set('has_website', hasWebsiteInput.value);
        body.set('website', websiteInput ? websiteInput.value : '');
        body.set('want_audit', wantAuditInput.value);

        fetch(<?= json_encode(rtrim($appUrl, '/') . '/app/api/lead_draft_start.php') ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).catch(function () {});
    }

    nextBtn.addEventListener('click', function () {
        if (!validateStep(current)) return;
        if (current === 1 && !draftAuditStarted && hasWebsiteInput.value === 'yes' && wantAuditInput.value === 'yes') {
            startDraftAudit();
        }
        current = Math.min(current + 1, steps.length - 1);
        showStep(current);
    });

    backBtn.addEventListener('click', function () {
        current = Math.max(current - 1, 0);
        showStep(current);
    });

    form.addEventListener('submit', function (e) {
        if (!validateStep(current)) {
            e.preventDefault();
        }
    });

    // Business type "Others" reveal
    var businessTypeSelect = document.getElementById('businessTypeSelect');
    var businessTypeOtherWrap = document.getElementById('businessTypeOtherWrap');
    var businessTypeOtherInput = document.getElementById('businessTypeOtherInput');
    businessTypeSelect.addEventListener('change', function () {
        var isOthers = businessTypeSelect.value === 'Others';
        businessTypeOtherWrap.classList.toggle('hidden', !isOthers);
        if (isOthers) {
            businessTypeOtherInput.setAttribute('required', 'required');
        } else {
            businessTypeOtherInput.removeAttribute('required');
            businessTypeOtherInput.value = '';
            setFieldError(businessTypeOtherInput, false);
        }
    });

    // Industry "Others" reveal
    var industrySelect = document.getElementById('industrySelect');
    var industryOtherWrap = document.getElementById('industryOtherWrap');
    var industryOtherInput = document.getElementById('industryOtherInput');
    industrySelect.addEventListener('change', function () {
        var isOthers = industrySelect.value === 'Others';
        industryOtherWrap.classList.toggle('hidden', !isOthers);
        if (isOthers) {
            industryOtherInput.setAttribute('required', 'required');
        } else {
            industryOtherInput.removeAttribute('required');
            industryOtherInput.value = '';
            setFieldError(industryOtherInput, false);
        }
    });

    // Do-you-run-ads yes/no toggle
    var adSpendWrap = document.getElementById('adSpendWrap');
    var adSpendSelect = document.getElementById('adSpendSelect');
    var roasWrap = document.getElementById('roasWrap');
    var roasSelect = document.getElementById('roasSelect');
    Array.prototype.forEach.call(document.querySelectorAll('.has-ads-option'), function (btn) {
        btn.addEventListener('click', function () {
            var value = btn.getAttribute('data-value');
            hasAdsInput.value = value;
            Array.prototype.forEach.call(document.querySelectorAll('.has-ads-option'), function (b) {
                var active = b === btn;
                b.classList.toggle('border-[var(--electric-blue)]', active);
                b.classList.toggle('bg-[var(--electric-blue)]/10', active);
            });
            var groupError = hasAdsGroup.parentElement.querySelector('.field-error');
            if (groupError) groupError.classList.add('hidden');

            var showAdsDetails = value === 'yes';
            adSpendWrap.classList.toggle('hidden', !showAdsDetails);
            roasWrap.classList.toggle('hidden', !showAdsDetails);
            if (showAdsDetails) {
                adSpendSelect.setAttribute('required', 'required');
                roasSelect.setAttribute('required', 'required');
            } else {
                adSpendSelect.removeAttribute('required');
                roasSelect.removeAttribute('required');
                adSpendSelect.value = '';
                roasSelect.value = '';
                setFieldError(adSpendSelect, false);
                setFieldError(roasSelect, false);
            }
        });
    });

    // Has-website yes/no toggle
    var websiteUrlWrap = document.getElementById('websiteUrlWrap');
    var websiteInput = document.getElementById('websiteInput');
    var wantAuditWrap = document.getElementById('wantAuditWrap');
    var auditTimeNote = document.getElementById('auditTimeNote');

    function resetAuditChoice() {
        wantAuditInput.value = '';
        auditTimeNote.classList.add('hidden');
        Array.prototype.forEach.call(document.querySelectorAll('.audit-option'), function (b) {
            b.classList.remove('border-[var(--electric-blue)]', 'bg-[var(--electric-blue)]/10');
        });
        var auditGroupError = wantAuditGroup.parentElement.querySelector('.field-error');
        if (auditGroupError) auditGroupError.classList.add('hidden');
    }

    Array.prototype.forEach.call(document.querySelectorAll('.has-website-option'), function (btn) {
        btn.addEventListener('click', function () {
            var value = btn.getAttribute('data-value');
            hasWebsiteInput.value = value;
            Array.prototype.forEach.call(document.querySelectorAll('.has-website-option'), function (b) {
                var active = b === btn;
                b.classList.toggle('border-[var(--electric-blue)]', active);
                b.classList.toggle('bg-[var(--electric-blue)]/10', active);
            });
            var groupError = hasWebsiteGroup.parentElement.querySelector('.field-error');
            if (groupError) groupError.classList.add('hidden');

            var showUrl = value === 'yes';
            websiteUrlWrap.classList.toggle('hidden', !showUrl);
            wantAuditWrap.classList.toggle('hidden', !showUrl);
            if (showUrl) {
                websiteInput.setAttribute('required', 'required');
            } else {
                websiteInput.removeAttribute('required');
                websiteInput.value = '';
                setFieldError(websiteInput, false);
                resetAuditChoice();
            }
        });
    });

    // Want-a-free-audit yes/no toggle
    Array.prototype.forEach.call(document.querySelectorAll('.audit-option'), function (btn) {
        btn.addEventListener('click', function () {
            var value = btn.getAttribute('data-value');
            wantAuditInput.value = value;
            Array.prototype.forEach.call(document.querySelectorAll('.audit-option'), function (b) {
                var active = b === btn;
                b.classList.toggle('border-[var(--electric-blue)]', active);
                b.classList.toggle('bg-[var(--electric-blue)]/10', active);
            });
            var groupError = wantAuditGroup.parentElement.querySelector('.field-error');
            if (groupError) groupError.classList.add('hidden');
            auditTimeNote.classList.toggle('hidden', value !== 'yes');
        });
    });

    showStep(current);
})();
</script>
<?php endif; ?>

<?php if ($submitted && $auditToken): ?>
<style>
    /* Progressive reveal-as-you-scroll, driven by IntersectionObserver
       toggling .is-visible (see initScrollReveal()) rather than a scroll-
       linked animation library timeline - a plain CSS transition can never
       get "stuck" mid-animation the way a mistimed scroll-triggered tween
       can, so content can't end up permanently invisible. */
    #auditResult [data-section],
    #auditResult .reveal-item {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    #auditResult [data-section].is-visible,
    #auditResult .reveal-item.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Full-screen story takeover shown once when the audit completes.
       Slides are all present in the DOM at once (cheap - a handful of
       nodes) and cross-fade via opacity so a slide can never get stuck
       mid-transition the same way the reveal-on-scroll rule above avoids
       that for the dashboard. */
    #auditStory {
        position: fixed;
        inset: 0;
        z-index: 999;
        /* Solid black letterbox bars top/bottom - the moodier gradient lives
           one level down on .story-stage, so the strip behind the progress
           bar/skip link reads as a widescreen cinema frame, not just "the
           page background continues here." */
        background: #000;
        overflow: hidden;
    }
    #auditStory::before {
        /* A slow, ambient drift behind the content - the point isn't to be
           noticed, it's so the frame never reads as a paused/static image
           the way a plain flat background would. */
        content: '';
        position: absolute;
        inset: -10%;
        background: radial-gradient(60% 60% at 30% 20%, rgba(0,102,255,0.16) 0%, transparent 60%),
                    radial-gradient(50% 50% at 75% 85%, rgba(0,102,255,0.10) 0%, transparent 65%),
                    var(--navy-black);
        animation: storyAmbientDrift 18s ease-in-out infinite alternate;
        z-index: 0;
    }
    @keyframes storyAmbientDrift {
        0%   { transform: translate3d(0, 0, 0) scale(1); }
        100% { transform: translate3d(-2%, 2%, 0) scale(1.06); }
    }
    #auditStory .story-stage {
        position: absolute;
        top: 56px;
        bottom: 56px;
        left: 0;
        right: 0;
        z-index: 1;
    }
    #auditStory .story-slide {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px clamp(20px, 6vw, 64px);
        opacity: 0;
        transform: scale(1.045);
        pointer-events: none;
        transition: opacity 0.7s ease, transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
        overflow-y: auto;
    }
    #auditStory .story-slide.is-active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    #auditStory .story-tap {
        position: absolute;
        top: 56px;
        bottom: 56px;
        z-index: 2;
        cursor: pointer;
    }
    #auditStory .story-tap-prev { left: 0; width: 35%; }
    #auditStory .story-tap-next { right: 0; width: 65%; }
    #auditStory .story-cta,
    #auditStory .story-slide a {
        position: relative;
        z-index: 3;
    }
    #auditStory .story-seg-fill { transform: scaleX(0); transform-origin: left; }
    #auditStory .story-ring-svg { transform: rotate(-90deg); }
    #auditStory .story-score-ring {
        stroke-dasharray: 339;
        stroke-dashoffset: 339;
        transition: stroke-dashoffset 1.2s ease-out;
    }
    #auditStory .story-shot-wrap { overflow: hidden; }
    #auditStory .story-shot-wrap img { transform: scale(1); transform-origin: center top; will-change: transform; }
    /* "What We Can Do For Your Brand" slide motion: word-masked title,
       clip-path wipe on the body paragraph, color-shifting highlight
       phrases, and a low-opacity self-drawing growth line behind it all. */
    .story-impl-wrap { isolation: isolate; }
    .story-bg-growth { z-index: 0; }
    .story-bg-growth-dot { opacity: 0; }
    .story-impl-title, .story-impl-body { z-index: 1; }
    .story-mask-word { display: inline-block; overflow: hidden; vertical-align: top; }
    .story-mask-word-inner { display: inline-block; will-change: transform; }
    .story-highlight { color: #94a3b8; }
    .story-impl-body { clip-path: inset(0 0 100% 0); }

    /* Vignette: darkens the frame's edges so attention stays centered,
       the same trick a lens or a color grade uses - not decoration for its
       own sake, it's what stops the letterboxed frame from reading as "a
       page with black bars" instead of "a shot." */
    #auditStory .story-vignette {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background: radial-gradient(120% 100% at 50% 45%, transparent 50%, rgba(0,0,0,0.6) 100%);
    }

    /* Film grain: a small noise tile (generated once on open, see
       buildGrainDataUrl) stepped through a handful of offsets - cheap to
       run, and it's the single biggest cue that reads as "video" rather
       than "web page," because a real page never has this. */
    #auditStory .story-grain {
        position: absolute;
        inset: -10%;
        z-index: 4;
        pointer-events: none;
        opacity: 0.05;
        mix-blend-mode: overlay;
        background-repeat: repeat;
        background-size: 180px 180px;
        animation: storyGrainShift 0.7s steps(6) infinite;
    }
    @keyframes storyGrainShift {
        0%   { transform: translate(0%, 0%); }
        16%  { transform: translate(-3%, 2%); }
        32%  { transform: translate(2%, -4%); }
        48%  { transform: translate(-4%, -2%); }
        64%  { transform: translate(3%, 3%); }
        82%  { transform: translate(-2%, 4%); }
        100% { transform: translate(0%, 0%); }
    }

    /* Opening title card - plays once, before slide 0, while the stage's
       inset is still animating from full-bleed to its letterboxed resting
       position (see playStoryIntro/openStory). */
    #auditStory .story-intro {
        position: absolute;
        inset: 0;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
        opacity: 0;
        pointer-events: none;
    }
    #auditStory .story-intro-mark {
        font-size: clamp(28px, 5vw, 44px);
        font-weight: 700;
        letter-spacing: 0.35em;
        color: #fff;
        opacity: 0;
        transform: scale(0.92);
        padding-left: 0.35em; /* optically re-center the letter-spaced text */
    }

    @media (prefers-reduced-motion: reduce) {
        #auditStory .story-slide { transition: none; }
        #auditStory::before { animation: none; }
        #auditStory .story-grain { display: none; }
    }

    /* "Download PDF" is just the browser's native print-to-PDF, aimed at
       the dashboard (never the full-screen story overlay) - this strips
       site chrome and interactive-only controls, forces the scroll-reveal
       and accordion content fully visible regardless of what's actually
       been scrolled past or expanded yet, and keeps the report's real
       colors (backgrounds, badges, the score ring) intact in the saved
       file instead of the browser defaulting to a stripped-down grayscale
       printout. */
    @media print {
        nav.main-nav-uppercase, footer, #auditStory, #reportCta,
        #submissionThankYou, #sectionNav, #severityFilters {
            display: none !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        #auditResult [data-section],
        #auditResult .reveal-item {
            opacity: 1 !important;
            transform: none !important;
        }
        #auditResult .category-body {
            height: auto !important;
            overflow: visible !important;
        }
        #auditResult .category-group.hidden,
        #auditResult .finding-item.hidden {
            display: block !important;
        }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function () {
    var token = <?= json_encode($auditToken) ?>;
    var apiUrl = <?= json_encode(rtrim($appUrl, '/') . '/app/api/website_audit_status') ?>;
    var keepInTouchApiUrl = <?= json_encode(rtrim($appUrl, '/') . '/app/api/audit_keep_in_touch') ?>;
    var digifyceLogoUrl = <?= json_encode(rtrim($appUrl, '/') . '/public/assets/site/wlogo-1770095408.png') ?>;
    var pendingEl = document.getElementById('auditPending');
    var resultEl = document.getElementById('auditResult');
    var failedEl = document.getElementById('auditFailed');
    var dotsEl = document.getElementById('auditDots');
    var pollCount = 0;
    var maxPolls = 450; // ~450 * 3s = 22.5 minutes of active polling, matching config's max_running_seconds budget
    var lastAuditData = null; // the last data payload renderCompleted() received - the PDF is built from this, not from the DOM

    var severityDotColor = {
        critical: 'bg-red-500',
        high: 'bg-orange-500',
        medium: 'bg-yellow-500',
        low: 'bg-slate-500',
        info: 'bg-blue-400'
    };

    var severityBorderColor = {
        critical: 'border-red-500',
        high: 'border-orange-500',
        medium: 'border-yellow-500',
        low: 'border-white/20',
        info: 'border-blue-400'
    };

    var effortColor = {
        low: 'text-green-400 border-green-500/30 bg-green-500/10',
        medium: 'text-yellow-400 border-yellow-500/30 bg-yellow-500/10',
        high: 'text-orange-400 border-orange-500/30 bg-orange-500/10'
    };

    var categoryIcon = {
        'SEO': 'search',
        'Technical': 'settings',
        'Mobile': 'smartphone',
        'Content': 'article',
        'Social': 'share',
        'Layout & Design': 'palette',
        'Conversion': 'shopping_cart',
        'Terminal': 'info'
    };

    var effortIcon = { low: 'bolt', medium: 'schedule', high: 'fitness_center' };

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function icon(name, cls) {
        return '<span class="material-symbols-outlined align-middle' + (cls ? ' ' + cls : '') + '" aria-hidden="true">' + name + '</span>';
    }

    function scoreTier(score) {
        if (score >= 80) return { label: 'Performing Well', badge: 'text-green-400 border-green-500/30 bg-green-500/10', ring: '#22c55e', icon: 'check_circle' };
        if (score >= 50) return { label: 'Needs Improvement', badge: 'text-yellow-400 border-yellow-500/30 bg-yellow-500/10', ring: '#eab308', icon: 'warning' };
        return { label: 'Critical Issues Found', badge: 'text-red-400 border-red-500/30 bg-red-500/10', ring: '#ef4444', icon: 'error' };
    }

    function renderSidebarScore(score) {
        var tier = scoreTier(score);
        var radius = 44;
        var circumference = Math.round(2 * Math.PI * radius);
        var html = '<div class="mb-6 rounded-xl border border-white/10 bg-white/[0.03] p-6 text-center transition-all duration-300 hover:border-white/20">';
        html += '<div class="relative w-28 h-28 mx-auto">';
        html += '<svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">';
        html += '<circle cx="50" cy="50" r="' + radius + '" stroke="rgba(255,255,255,0.08)" stroke-width="8" fill="none"></circle>';
        html += '<circle id="scoreRing" cx="50" cy="50" r="' + radius + '" stroke="' + tier.ring + '" stroke-width="8" fill="none" stroke-linecap="round" ' +
            'stroke-dasharray="' + circumference + '" stroke-dashoffset="' + circumference + '"></circle>';
        html += '</svg>';
        html += '<div class="absolute inset-0 flex flex-col items-center justify-center">';
        html += '<span id="scoreNumber" class="text-3xl font-bold text-white">0</span>';
        html += '<span class="text-[10px] uppercase tracking-[0.2em] text-slate-500">/ 100</span>';
        html += '</div></div>';
        html += '<span class="mt-4 inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.2em] font-bold px-3 py-1.5 rounded-full border ' + tier.badge + '">' + icon(tier.icon, 'text-base') + escapeHtml(tier.label) + '</span>';
        html += '</div>';
        return html;
    }

    function renderPriorityActionsContent(actions) {
        var html = '<div class="space-y-2">';
        actions.forEach(function (a, i) {
            html += '<div class="reveal-item flex items-start gap-4 rounded-lg border border-white/10 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:border-white/20 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/20">';
            html += '<span class="shrink-0 w-8 h-8 rounded-full bg-[var(--electric-blue)]/15 text-[var(--electric-blue)] text-sm font-bold flex items-center justify-center mt-0.5">' + (i + 1) + '</span>';
            html += '<div class="flex-1 min-w-0">';
            html += '<div class="flex items-start justify-between gap-3">';
            html += '<p class="text-base sm:text-lg font-semibold text-white">' + escapeHtml(a.action) + '</p>';
            if (a.effort) {
                html += '<span class="shrink-0 inline-flex items-center gap-1 text-xs uppercase tracking-wider px-2.5 py-1 rounded-full border ' + (effortColor[a.effort] || effortColor.medium) + '">' + icon(effortIcon[a.effort] || 'schedule', 'text-sm') + escapeHtml(a.effort) + '</span>';
            }
            html += '</div>';
            html += '<p class="mt-1.5 text-sm sm:text-base text-slate-400">' + escapeHtml(a.why) + '</p>';
            html += '</div></div>';
        });
        html += '</div>';
        return html;
    }

    function renderFindingsContent(findings) {
        var categories = {};
        var order = [];
        findings.forEach(function (f) {
            if (!categories[f.category]) {
                categories[f.category] = [];
                order.push(f.category);
            }
            categories[f.category].push(f);
        });

        var html = '<div class="flex flex-wrap gap-2 mb-5" id="severityFilters">';
        ['all', 'critical', 'high', 'medium', 'low'].forEach(function (sev) {
            var active = sev === 'all';
            html += '<button type="button" data-filter="' + sev + '" class="severity-filter-btn text-xs uppercase tracking-wider px-3.5 py-2 rounded-full border transition-all ' +
                (active ? 'border-[var(--electric-blue)] bg-[var(--electric-blue)]/10 text-white' : 'border-white/10 text-slate-400') + '">' + sev + '</button>';
        });
        html += '</div>';

        html += '<div class="rounded-xl border border-white/10 divide-y divide-white/10 overflow-hidden">';
        order.forEach(function (category) {
            var items = categories[category];
            html += '<div class="category-group" data-open="false">';
            html += '<button type="button" class="category-toggle w-full text-left cursor-pointer select-none flex items-center justify-between px-5 py-4 bg-white/[0.02] hover:bg-white/[0.05] transition-colors">';
            html += '<span class="flex items-center gap-2.5 text-sm uppercase tracking-[0.2em] font-bold text-white">' + icon(categoryIcon[category] || 'info', 'text-lg text-[var(--electric-blue)]') + escapeHtml(category) + '</span>';
            html += '<span class="flex items-center gap-1.5">';
            html += '<span class="text-xs text-slate-500 mr-1">' + items.length + '</span>';
            items.forEach(function (f) {
                html += '<span class="w-1.5 h-1.5 rounded-full ' + (severityDotColor[f.severity] || severityDotColor.low) + '"></span>';
            });
            html += icon('expand_more', 'chevron-icon text-slate-500 ml-1 transition-transform duration-300');
            html += '</span></button>';
            html += '<div class="category-body overflow-hidden" style="height:0">';
            html += '<div class="px-5 py-4 space-y-2 bg-black/20">';
            items.forEach(function (f) {
                var borderColor = severityBorderColor[f.severity] || severityBorderColor.low;
                html += '<div class="finding-item border-l-2 ' + borderColor + ' pl-4 py-3 rounded-r-md transition-colors hover:bg-white/[0.03]" data-severity="' + f.severity + '">';
                html += '<p class="text-base font-semibold text-white">' + escapeHtml(f.title) + '</p>';
                html += '<p class="mt-1 text-sm text-slate-400">' + escapeHtml(f.description) + '</p>';
                if (f.page) {
                    html += '<span class="inline-block mt-2 text-xs uppercase tracking-wider text-slate-500 px-2 py-0.5 rounded bg-white/5">' + escapeHtml(f.page) + '</span>';
                }
                html += '</div>';
            });
            html += '</div></div></div>';
        });
        html += '</div>';
        return html;
    }

    // Picks an icon that actually matches what the recommendation is about
    // (a content play looks like a content play, an ad campaign looks like
    // an ad campaign) instead of every card wearing the same generic bolt.
    // Each rule pairs a matching pattern with an illustration key, a
    // category label, and its own accent color - the point is real visual
    // variety (an ads play looks orange and megaphone-shaped, a loyalty
    // play looks rose and heart-shaped) instead of every recommendation
    // wearing the same static glyph regardless of what it actually is.
    var IMPLEMENTATION_VISUAL_RULES = [
        [/loyalty|retention|repeat purchase|subscri|winback/, 'retention', 'Retention', '#f43f5e'],
        [/social media|instagram|facebook|influencer|ugc/, 'social', 'Social', '#ec4899'],
        [/content|blog|storytell|story\b/, 'content', 'Content', '#22c55e'],
        [/whatsapp|email|sms|newsletter|drip/, 'messaging', 'Messaging', '#8b5cf6'],
        [/\bads?\b|advertis|campaign|meta ads|google ads|paid media/, 'ads', 'Paid Ads', '#f97316'],
        [/video|creative|shoot|reel|photograph/, 'creative', 'Creative', '#eab308'],
        [/conversion|checkout|funnel|call-?to-?action|\bcta\b/, 'conversion', 'Conversion', '#0066ff'],
        [/website|landing page|product page/, 'website', 'Website', '#14b8a6'],
        [/analytic|data|track|dashboard/, 'analytics', 'Analytics', '#6366f1'],
        [/brand|position|identity|logo/, 'branding', 'Branding', '#f59e0b'],
        [/marketplace|amazon|flipkart|shopify/, 'marketplace', 'Marketplace', '#06b6d4']
    ];

    function pickImplementationVisual(item) {
        var text = ((item.title || '') + ' ' + (item.description || '')).toLowerCase();
        for (var i = 0; i < IMPLEMENTATION_VISUAL_RULES.length; i++) {
            var rule = IMPLEMENTATION_VISUAL_RULES[i];
            if (rule[0].test(text)) return { key: rule[1], label: rule[2], color: rule[3] };
        }
        return { key: 'growth', label: 'Growth', color: '#0066ff' };
    }

    function renderImplementationCard(item) {
        var visual = pickImplementationVisual(item);
        var html = '<div class="reveal-item mb-5 rounded-xl border border-[var(--electric-blue)]/30 bg-[var(--electric-blue)]/5 p-5 sm:p-6">';

        html += '<div class="mb-3">';
        html += '<span class="block text-[10px] uppercase tracking-wider font-bold mb-1" style="color:' + visual.color + ';">' + escapeHtml(visual.label) + '</span>';
        html += '<p class="text-base sm:text-lg font-semibold text-white leading-snug">' + escapeHtml(item.title) + '</p>';
        html += '</div>';

        if (item.description) {
            html += '<p class="text-sm sm:text-base text-slate-200 leading-relaxed">' + escapeHtml(item.description) + '</p>';
        }

        html += '</div>';
        return html;
    }

    function renderCaseStudyMatchesContent(items) {
        var html = '<p class="reveal-item mb-6 max-w-2xl text-sm sm:text-base text-slate-400 leading-relaxed">Grounded in Digifyce\'s own past work — real campaigns and proposals we\'ve delivered for other brands — not generic advice.</p>';
        items.forEach(function (item) {
            html += renderImplementationCard(item);
        });
        return html;
    }

    function pressFeedback(el) {
        if (!window.gsap) return;
        gsap.fromTo(el, { scale: 0.94 }, { scale: 1, duration: 0.3, ease: 'back.out(3)' });
    }

    function toggleCategory(groupEl) {
        var isOpen = groupEl.getAttribute('data-open') === 'true';
        var willOpen = !isOpen;
        var body = groupEl.querySelector('.category-body');
        var chevron = groupEl.querySelector('.chevron-icon');
        groupEl.setAttribute('data-open', willOpen ? 'true' : 'false');
        if (chevron) chevron.style.transform = willOpen ? 'rotate(180deg)' : 'rotate(0deg)';

        if (willOpen) {
            var target = body.scrollHeight;
            if (window.gsap) {
                gsap.fromTo(body, { height: 0 }, {
                    height: target, duration: 0.35, ease: 'power2.out',
                    onComplete: function () { body.style.height = 'auto'; }
                });
            } else {
                body.style.height = 'auto';
            }
        } else {
            var current = body.scrollHeight;
            body.style.height = current + 'px';
            requestAnimationFrame(function () {
                if (window.gsap) {
                    gsap.to(body, { height: 0, duration: 0.3, ease: 'power2.in' });
                } else {
                    body.style.height = '0px';
                }
            });
        }
    }

    function wireFindingsInteractivity() {
        var categoryGroups = resultEl.querySelectorAll('.category-group');
        Array.prototype.forEach.call(categoryGroups, function (group) {
            if (group.getAttribute('data-open') === 'true') {
                var chevron = group.querySelector('.chevron-icon');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
            var toggleBtn = group.querySelector('.category-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () { toggleCategory(group); });
            }
        });

        var filterBtns = resultEl.querySelectorAll('.severity-filter-btn');
        Array.prototype.forEach.call(filterBtns, function (btn) {
            btn.addEventListener('click', function () {
                pressFeedback(btn);
                var filter = btn.getAttribute('data-filter');
                Array.prototype.forEach.call(filterBtns, function (b) {
                    var active = b === btn;
                    b.classList.toggle('border-[var(--electric-blue)]', active);
                    b.classList.toggle('bg-[var(--electric-blue)]/10', active);
                    b.classList.toggle('text-white', active);
                    b.classList.toggle('border-white/10', !active);
                    b.classList.toggle('text-slate-400', !active);
                });
                var items = resultEl.querySelectorAll('.finding-item');
                Array.prototype.forEach.call(items, function (item) {
                    var show = filter === 'all' || item.getAttribute('data-severity') === filter;
                    item.classList.toggle('hidden', !show);
                });
                Array.prototype.forEach.call(categoryGroups, function (group) {
                    var visibleItems = group.querySelectorAll('.finding-item:not(.hidden)');
                    group.classList.toggle('hidden', visibleItems.length === 0);
                    // An open category's body has no fixed height (auto), so
                    // hiding/showing items reflows it for free. A currently
                    // animating/closed category doesn't need recalculation -
                    // toggleCategory() measures scrollHeight fresh on open.
                });
            });
        });
    }

    function renderDashboardLayout(sections, score) {
        // No items-start here deliberately: the sidebar column must stretch
        // to match the (much taller) main content column's height, or its
        // sticky child has almost no room to actually stick as you scroll -
        // it would just reach the bottom of its own short column immediately.
        var html = '<div class="lg:flex lg:gap-10">';

        html += '<div class="mb-8 lg:mb-0 lg:w-64 lg:shrink-0">';
        // top-28 (7rem), not top-8: the site's own nav bar is fixed at
        // 6rem tall (h-24) from this breakpoint up, so sticking any less
        // than that leaves this sidebar scrolling in underneath/behind it.
        html += '<div class="lg:sticky lg:top-28">';
        if (score !== null && score !== undefined) {
            html += renderSidebarScore(score);
        }
        html += '<nav class="flex lg:flex-col gap-1.5 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0" id="sectionNav">';
        sections.forEach(function (s, i) {
            html += '<a href="#section-' + s.id + '" data-nav-target="' + s.id + '" class="section-nav-link shrink-0 flex items-center gap-2.5 px-4 py-3 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors ' +
                (i === 0 ? 'bg-[var(--electric-blue)]/10 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5') + '">' + icon(s.icon, 'text-lg') + escapeHtml(s.label) + '</a>';
        });
        html += '</nav>';
        html += '</div></div>';

        html += '<div class="flex-1 min-w-0 space-y-14">';
        sections.forEach(function (s) {
            html += '<section id="section-' + s.id + '" data-section="' + s.id + '">';
            html += '<h2 class="flex items-center gap-2.5 text-xl sm:text-2xl font-bold text-white mb-5">' + icon(s.icon, 'text-2xl text-[var(--electric-blue)]') + escapeHtml(s.label) + '</h2>';
            html += s.html;
            html += '</section>';
        });
        html += '</div>';

        html += '</div>';
        return html;
    }

    function wireSectionNav() {
        var links = resultEl.querySelectorAll('.section-nav-link');
        if (!links.length) return;

        Array.prototype.forEach.call(links, function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                pressFeedback(link);
                var targetEl = document.getElementById('section-' + link.getAttribute('data-nav-target'));
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        var setActive = function (id) {
            Array.prototype.forEach.call(links, function (link) {
                var active = link.getAttribute('data-nav-target') === id;
                link.classList.toggle('bg-[var(--electric-blue)]/10', active);
                link.classList.toggle('text-white', active);
                link.classList.toggle('text-slate-400', !active);
            });
        };

        var sectionEls = resultEl.querySelectorAll('[data-section]');
        if ('IntersectionObserver' in window && sectionEls.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        setActive(entry.target.getAttribute('data-section'));
                    }
                });
            }, { rootMargin: '-15% 0px -70% 0px', threshold: 0 });
            Array.prototype.forEach.call(sectionEls, function (el) { observer.observe(el); });
        }
    }

    function loadImageAsDataUrl(url) {
        return new Promise(function (resolve) {
            if (!url) { resolve(null); return; }
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function () {
                try {
                    var canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    resolve({ dataUrl: canvas.toDataURL('image/jpeg', 0.9), width: img.naturalWidth, height: img.naturalHeight });
                } catch (e) {
                    resolve(null); // e.g. a canvas-tainting cross-origin issue - the doc just skips the image
                }
            };
            img.onerror = function () { resolve(null); };
            img.src = url;
        });
    }

    function hexToRgb(hex) {
        var n = parseInt(hex.replace('#', ''), 16);
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    }

    // Builds an actual paginated text document (headings, wrapped paragraphs,
    // a real page count) rather than a screenshot of the page sliced into
    // image "pages" - selectable, searchable, and small in file size.
    function buildReportDocument(doc, data, screenshotImg, logoImg) {
        var pageWidth = doc.internal.pageSize.getWidth();
        var pageHeight = doc.internal.pageSize.getHeight();
        var margin = 50;
        var contentWidth = pageWidth - margin * 2;
        var HEADER_BAND_HEIGHT = 84;
        var cursorY = margin;

        var INK = [15, 23, 42];
        var MUTED = [100, 116, 139];
        var ACCENT = [0, 102, 255];
        var LINE = [226, 232, 240];
        var SEVERITY_COLOR = { critical: [185, 28, 28], high: [194, 65, 12], medium: [161, 98, 7], low: [100, 116, 139], info: [29, 78, 216] };
        var EFFORT_COLOR = { low: [21, 128, 61], medium: [161, 98, 7], high: [194, 65, 12] };

        function ensureSpace(h) {
            if (cursorY + h > pageHeight - margin) {
                doc.addPage();
                cursorY = margin;
            }
        }

        function setStyle(size, style, color) {
            doc.setFont('helvetica', style || 'normal');
            doc.setFontSize(size);
            doc.setTextColor(color[0], color[1], color[2]);
        }

        function paragraph(text, opts) {
            opts = opts || {};
            var size = opts.size || 10;
            var lineHeight = opts.lineHeight || size * 1.5;
            var x = opts.x || margin;
            setStyle(size, opts.style || 'normal', opts.color || INK);
            var lines = doc.splitTextToSize(text, opts.maxWidth || (contentWidth - (x - margin)));
            lines.forEach(function (line) {
                ensureSpace(lineHeight);
                doc.text(line, x, cursorY);
                cursorY += lineHeight;
            });
        }

        function wrappedLineCount(text, maxWidth) {
            return text ? doc.splitTextToSize(text, maxWidth).length : 0;
        }

        function heading(text) {
            ensureSpace(40);
            cursorY += 16;
            setStyle(14, 'bold', INK);
            doc.text(text, margin, cursorY);
            cursorY += 8;
            doc.setDrawColor(LINE[0], LINE[1], LINE[2]);
            doc.setLineWidth(1);
            doc.line(margin, cursorY, pageWidth - margin, cursorY);
            cursorY += 16;
        }

        // ---- Cover ----
        // The real logo (public/assets/site/wlogo-*.png) is a light/white
        // wordmark meant for a dark background, so it gets its own dark
        // band across the top rather than sitting directly on the
        // document's white page - otherwise it would be all but invisible.
        doc.setFillColor(5, 7, 10);
        doc.rect(0, 0, pageWidth, HEADER_BAND_HEIGHT, 'F');
        if (logoImg) {
            var logoHeight = 38;
            var logoWidth = logoHeight * (logoImg.width / logoImg.height);
            doc.addImage(logoImg.dataUrl, 'PNG', margin, (HEADER_BAND_HEIGHT - logoHeight) / 2, logoWidth, logoHeight);
        } else {
            setStyle(11, 'bold', [255, 255, 255]);
            doc.text('DIGIFYCE', margin, HEADER_BAND_HEIGHT / 2 + 4);
        }
        cursorY = HEADER_BAND_HEIGHT + 34;
        setStyle(21, 'bold', INK);
        doc.text('Website Growth Report', margin, cursorY);
        cursorY += 18;
        setStyle(9.5, 'normal', MUTED);
        doc.text('Prepared ' + new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' }), margin, cursorY);
        cursorY += 30;

        if (data.score !== null && data.score !== undefined) {
            var tier = scoreTier(data.score);
            var tierRgb = hexToRgb(tier.ring);
            setStyle(28, 'bold', INK);
            doc.text(String(data.score) + ' / 100', margin, cursorY);
            cursorY += 16;
            var barWidth = 220, barHeight = 7;
            doc.setFillColor(LINE[0], LINE[1], LINE[2]);
            doc.roundedRect(margin, cursorY, barWidth, barHeight, 3.5, 3.5, 'F');
            doc.setFillColor(tierRgb[0], tierRgb[1], tierRgb[2]);
            doc.roundedRect(margin, cursorY, barWidth * Math.max(0.04, data.score / 100), barHeight, 3.5, 3.5, 'F');
            cursorY += barHeight + 12;
            setStyle(9.5, 'bold', tierRgb);
            doc.text(tier.label.toUpperCase(), margin, cursorY);
            cursorY += 24;
        }

        if (screenshotImg) {
            var maxImgWidth = contentWidth;
            var imgHeight = (screenshotImg.height * maxImgWidth) / screenshotImg.width;
            var cappedHeight = Math.min(imgHeight, 260);
            var cappedWidth = cappedHeight === imgHeight ? maxImgWidth : (screenshotImg.width * cappedHeight) / screenshotImg.height;
            ensureSpace(cappedHeight + 10);
            doc.addImage(screenshotImg.dataUrl, 'JPEG', margin, cursorY, cappedWidth, cappedHeight);
            cursorY += cappedHeight + 20;
        }

        // ---- Overview ----
        if (data.brand_brief || data.executive_summary) {
            heading('Overview');
            if (data.brand_brief) paragraph(data.brand_brief, { size: 10, color: MUTED });
            if (data.executive_summary) {
                cursorY += 6;
                paragraph(data.executive_summary, { size: 10.5, color: INK });
            }
        }

        // ---- Action Plan ----
        if (data.priority_actions && data.priority_actions.length) {
            heading('Action Plan');
            data.priority_actions.forEach(function (a, i) {
                // Reserve the whole item's height up front (title + why +
                // effort label) so a page break can only ever land between
                // items, never orphan a label or wrapped line from the rest
                // of its own item.
                setStyle(10.5, 'bold', INK);
                var titleLineCount = wrappedLineCount((i + 1) + '. ' + (a.action || ''), contentWidth);
                setStyle(9.5, 'normal', MUTED);
                var whyLineCount = wrappedLineCount(a.why, contentWidth - 14);
                var blockHeight = titleLineCount * 14 + whyLineCount * 13.5 + (a.effort ? 16 : 0) + 6;
                ensureSpace(blockHeight);

                paragraph((i + 1) + '. ' + (a.action || ''), { size: 10.5, style: 'bold', color: INK, lineHeight: 14 });
                if (a.why) paragraph(a.why, { size: 9.5, color: MUTED, x: margin + 14, lineHeight: 13.5 });
                if (a.effort) {
                    var ec = EFFORT_COLOR[a.effort] || MUTED;
                    setStyle(8, 'bold', ec);
                    doc.text((a.effort || '').toUpperCase() + ' EFFORT', margin + 14, cursorY);
                    cursorY += 16;
                }
                cursorY += 6;
            });
        }

        // ---- Findings ----
        if (data.findings && data.findings.length) {
            heading('Findings (' + data.findings.length + ')');
            var categories = {}, order = [];
            data.findings.forEach(function (f) {
                if (!categories[f.category]) { categories[f.category] = []; order.push(f.category); }
                categories[f.category].push(f);
            });
            order.forEach(function (cat) {
                ensureSpace(22);
                setStyle(11, 'bold', ACCENT);
                doc.text(cat.toUpperCase(), margin, cursorY);
                cursorY += 16;
                categories[cat].forEach(function (f) {
                    var sevLabel = '[' + (f.severity || '').toUpperCase() + '] ';
                    setStyle(9, 'bold', MUTED); // font metrics only, for the width measurement below
                    var sevWidth = doc.getTextWidth(sevLabel);
                    setStyle(10, 'bold', INK); // the title's own size/weight, so its wrap measurement is accurate
                    var titleLineCount = wrappedLineCount(f.title, contentWidth - sevWidth);
                    var descLineCount = wrappedLineCount(f.description, contentWidth - 14);
                    var blockHeight = titleLineCount * 13 + descLineCount * 12.5 + 6;
                    ensureSpace(blockHeight);

                    var sevColor = SEVERITY_COLOR[f.severity] || MUTED;
                    setStyle(9, 'bold', sevColor);
                    doc.text(sevLabel, margin, cursorY);
                    setStyle(10, 'bold', INK);
                    var titleLines = doc.splitTextToSize(f.title || '', contentWidth - sevWidth);
                    doc.text(titleLines[0] || '', margin + sevWidth, cursorY);
                    cursorY += 13;
                    for (var i = 1; i < titleLines.length; i++) {
                        doc.text(titleLines[i], margin + sevWidth, cursorY);
                        cursorY += 13;
                    }
                    if (f.description) paragraph(f.description, { size: 9, color: MUTED, x: margin + 14, lineHeight: 12.5 });
                    cursorY += 6;
                });
                cursorY += 6;
            });
        }

        // ---- What We Can Do For Your Brand ----
        if (data.case_study_matches && data.case_study_matches.length) {
            heading('What We Can Do For Your Brand');
            data.case_study_matches.forEach(function (item, i) {
                setStyle(10.5, 'bold', INK);
                var titleLineCount = wrappedLineCount((i + 1) + '. ' + (item.title || ''), contentWidth);
                setStyle(9.5, 'normal', MUTED);
                var descLineCount = wrappedLineCount(item.description, contentWidth - 14);
                ensureSpace(titleLineCount * 14 + descLineCount * 13.5 + 8);

                paragraph((i + 1) + '. ' + (item.title || ''), { size: 10.5, style: 'bold', color: INK, lineHeight: 14 });
                if (item.description) paragraph(item.description, { size: 9.5, color: MUTED, x: margin + 14, lineHeight: 13.5 });
                cursorY += 8;
            });
        }

        // ---- Footer: page numbers ----
        var pageCount = doc.internal.getNumberOfPages();
        for (var p = 1; p <= pageCount; p++) {
            doc.setPage(p);
            setStyle(8.5, 'normal', MUTED);
            doc.text('Digifyce · Website Growth Report', margin, pageHeight - 24);
            doc.text('Page ' + p + ' of ' + pageCount, pageWidth - margin - 60, pageHeight - 24);
        }
    }

    function downloadReportPdf(triggerEl) {
        if (typeof window.jspdf === 'undefined' || !lastAuditData) {
            window.print(); // CDN script failed to load, or somehow called before data exists
            return;
        }

        var originalLabel = triggerEl.textContent;
        triggerEl.textContent = 'Preparing PDF…';
        triggerEl.style.pointerEvents = 'none';

        Promise.all([
            loadImageAsDataUrl(lastAuditData.screenshot_url),
            loadImageAsDataUrl(digifyceLogoUrl)
        ])
            .then(function (results) {
                var jsPDF = window.jspdf.jsPDF;
                var doc = new jsPDF({ unit: 'pt', format: 'a4' });
                buildReportDocument(doc, lastAuditData, results[0], results[1]);
                doc.save('digifyce-website-growth-report.pdf');
            })
            .catch(function () {
                window.print();
            })
            .finally(function () {
                triggerEl.textContent = originalLabel;
                triggerEl.style.pointerEvents = '';
            });
    }

    function enableReportDownload() {
        var cta = document.getElementById('reportCta');
        if (!cta || cta.getAttribute('data-download-wired') === '1') return;
        cta.setAttribute('data-download-wired', '1');
        cta.textContent = 'Download PDF';
        cta.addEventListener('click', function (e) {
            e.preventDefault();
            downloadReportPdf(cta);
        });
    }

    function renderCompleted(data) {
        pendingEl.classList.add('hidden');
        resultEl.classList.remove('hidden');
        lastAuditData = data;
        var thankYouEl = document.getElementById('submissionThankYou');
        if (thankYouEl) thankYouEl.classList.add('hidden');
        enableReportDownload();

        var overviewHtml = '<div class="mb-6 rounded-xl overflow-hidden border border-white/10">';
        if (data.screenshot_url) {
            overviewHtml += '<img src="' + data.screenshot_url + '" alt="Homepage screenshot" class="w-full h-auto block" loading="lazy" />';
        } else {
            overviewHtml += '<div class="py-10 text-center text-slate-500 text-xs uppercase tracking-[0.2em]">Screenshot unavailable</div>';
        }
        overviewHtml += '</div>';
        if (data.brand_brief) {
            overviewHtml += '<p class="mb-4 max-w-3xl text-base text-slate-400 leading-relaxed">' + escapeHtml(data.brand_brief) + '</p>';
        }
        if (data.executive_summary) {
            overviewHtml += '<p class="max-w-3xl text-base sm:text-lg text-slate-300 leading-relaxed">' + escapeHtml(data.executive_summary) + '</p>';
        }

        var sections = [{ id: 'overview', icon: 'dashboard', label: 'Overview', html: overviewHtml }];
        if (data.priority_actions && data.priority_actions.length) {
            sections.push({ id: 'actions', icon: 'checklist', label: 'Action Plan', html: renderPriorityActionsContent(data.priority_actions) });
        }
        if (data.findings && data.findings.length) {
            sections.push({ id: 'findings', icon: 'fact_check', label: 'Findings (' + data.findings.length + ')', html: renderFindingsContent(data.findings) });
        }
        if (data.case_study_matches && data.case_study_matches.length) {
            sections.push({ id: 'implementations', icon: 'auto_awesome', label: 'What We Can Do For Your Brand', html: renderCaseStudyMatchesContent(data.case_study_matches) });
        }

        resultEl.innerHTML = renderDashboardLayout(sections, data.score);

        var ring = document.getElementById('scoreRing');
        var scoreNumberEl = document.getElementById('scoreNumber');
        if (ring && data.score !== null && data.score !== undefined) {
            var circumference = Math.round(2 * Math.PI * 44);
            var offset = Math.round(circumference * (1 - data.score / 100));
            if (window.gsap) {
                gsap.to(ring, { strokeDashoffset: offset, duration: 1.4, ease: 'power2.out', delay: 0.2 });
                if (scoreNumberEl) {
                    var counter = { val: 0 };
                    gsap.to(counter, {
                        val: data.score, duration: 1.4, ease: 'power2.out', delay: 0.2,
                        onUpdate: function () { scoreNumberEl.textContent = Math.round(counter.val); }
                    });
                }
            } else {
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        ring.style.transition = 'stroke-dashoffset 1.2s ease-out';
                        ring.style.strokeDashoffset = offset;
                        if (scoreNumberEl) scoreNumberEl.textContent = data.score;
                    });
                });
            }
        }

        wireSectionNav();
        wireFindingsInteractivity();
        initScrollReveal();
        openStory(data);
    }

    function initScrollReveal() {
        var targets = resultEl.querySelectorAll('[data-section], .reveal-item');
        if (!targets.length) return;

        var reveal = function (el) { el.classList.add('is-visible'); };

        // Safety net: no matter what happens with the observer below
        // (unsupported browser, an element that technically never
        // intersects for some layout reason, etc.), this is the actual
        // audit content the lead came here to read - it must never end up
        // permanently invisible. Force it visible after a generous delay.
        var safetyTimer = setTimeout(function () {
            Array.prototype.forEach.call(targets, reveal);
        }, 4000);

        if (!('IntersectionObserver' in window)) {
            Array.prototype.forEach.call(targets, reveal);
            clearTimeout(safetyTimer);
            return;
        }

        // IntersectionObserver (not a scroll-linked animation-library
        // timeline) drives this: it's a plain browser API that recalculates
        // automatically as layout shifts (e.g. the screenshot image loading
        // in), and each element is observed independently, so scrolling
        // down naturally reveals section-by-section and card-by-card in
        // order - no manual stagger timing needed, and nothing can get
        // stuck mid-animation the way a mistimed scroll-triggered tween can.
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });

        Array.prototype.forEach.call(targets, function (el) { observer.observe(el); });
    }

    // ---- Full-screen "story" takeover ---------------------------------
    // Shown once, automatically, the moment an audit completes: a
    // Stories-style walkthrough (score -> brand snapshot -> one slide per
    // case-study-backed recommendation -> closing CTA) that sits on top of
    // the dashboard built above. Closing it (X, "Skip to full report", or
    // the closing slide's CTA) never destroys anything - the full
    // Overview/Action Plan/Findings/"What We Can Do For Your Brand"
    // dashboard is still there underneath for anyone who wants the detail.
    var STORY_MIN_SECONDS = 4.5;
    var STORY_MAX_SECONDS = 11;
    var STORY_WORDS_PER_SECOND = 2.6; // a comfortable narrated-voiceover pace, not a speed-read
    var storyState = { slides: [], index: 0, timeline: null, ambientTween: null, started: false };
    var grainDataUrlCache = null;
    var storyWired = false;
    var prefersReducedMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

    // A video's slides don't all get equal airtime - a caption card and a
    // dense paragraph shouldn't hold the screen for the same six seconds.
    // Read the slide's own rendered text to decide how long it earns.
    function computeSlideDuration(slideEl) {
        var text = (slideEl && slideEl.textContent || '').trim();
        var wordCount = text ? text.split(/\s+/).length : 0;
        var seconds = wordCount / STORY_WORDS_PER_SECOND;
        return Math.min(STORY_MAX_SECONDS, Math.max(STORY_MIN_SECONDS, seconds));
    }

    function renderStoryScoreSlideHtml(data) {
        var html = '<div class="max-w-lg mx-auto text-center flex flex-col items-center">';
        html += '<p class="story-anim text-xs uppercase tracking-[0.3em] font-bold text-[var(--electric-blue)] mb-6">Your Growth Score</p>';
        if (data.score !== null && data.score !== undefined) {
            var tier = scoreTier(data.score);
            html += '<div class="story-anim story-emphasis relative w-40 h-40 mb-6">';
            html += '<svg viewBox="0 0 120 120" class="story-ring-svg w-full h-full">';
            html += '<circle cx="60" cy="60" r="54" stroke="rgba(255,255,255,0.12)" stroke-width="8" fill="none"></circle>';
            html += '<circle class="story-score-ring" cx="60" cy="60" r="54" stroke="' + tier.ring + '" stroke-width="8" fill="none" stroke-linecap="round" data-score="' + data.score + '"></circle>';
            html += '</svg>';
            html += '<div class="absolute inset-0 flex flex-col items-center justify-center">';
            html += '<span class="story-score-num text-4xl font-bold text-white">0</span>';
            html += '<span class="text-[10px] uppercase tracking-[0.2em] text-slate-500">/ 100</span>';
            html += '</div></div>';
            html += '<span class="story-anim inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.2em] font-bold px-3 py-1.5 rounded-full border mb-6 ' + tier.badge + '">' + icon(tier.icon, 'text-base') + escapeHtml(tier.label) + '</span>';
        }
        if (data.executive_summary) {
            html += '<p class="story-anim text-base sm:text-lg text-slate-300 leading-relaxed">' + escapeHtml(data.executive_summary) + '</p>';
        }
        html += '</div>';
        return html;
    }

    function renderStoryBrandSlideHtml(data) {
        var html = '<div class="max-w-lg mx-auto">';
        html += '<p class="story-anim text-xs uppercase tracking-[0.3em] font-bold text-[var(--electric-blue)] mb-5">What We Saw</p>';
        if (data.screenshot_url) {
            html += '<div class="story-anim story-shot-wrap mb-5 rounded-xl border border-white/10"><img src="' + data.screenshot_url + '" alt="Homepage screenshot" class="story-shot w-full h-auto block" /></div>';
        }
        if (data.brand_brief) {
            html += '<p class="story-anim text-base sm:text-lg text-slate-300 leading-relaxed">' + escapeHtml(data.brand_brief) + '</p>';
        }
        html += '</div>';
        return html;
    }

    function smoothPath(points) {
        var d = 'M ' + points[0][0] + ' ' + points[0][1];
        for (var i = 0; i < points.length - 1; i++) {
            var p0 = points[i], p1 = points[i + 1];
            var midX = (p0[0] + p1[0]) / 2;
            d += ' C ' + midX + ' ' + p0[1] + ', ' + midX + ' ' + p1[1] + ', ' + p1[0] + ' ' + p1[1];
        }
        return d;
    }

    // 4x+ is the widely-used "healthy" e-commerce/D2C ROAS benchmark once
    // margin and overhead are accounted for - below that (or no ads running
    // at all, or an unreported ROAS) reads as "room to grow" rather than
    // "protect what's working."
    function classifyRoasTier(currentAdSpend) {
        if (!currentAdSpend || !currentAdSpend.runs_ads) return 'bad';
        return (currentAdSpend.roas_key === '4x-6x' || currentAdSpend.roas_key === '6x-plus') ? 'good' : 'bad';
    }

    function renderStoryGrowthSlideHtml(data) {
        var currentAdSpend = data.current_ad_spend;
        var isGood = classifyRoasTier(currentAdSpend) === 'good';

        var copy = isGood ? {
            headline: 'You’re Already Doing This Well.',
            note: 'Your current ' + (currentAdSpend.roas ? currentAdSpend.roas + ' ROAS' : 'ad performance') + ' is a healthy base. Our job from here is protecting that efficiency while scaling spend - not gambling it away chasing volume.',
            usNote: 'Scaled, same discipline'
        } : {
            headline: 'There’s Real Room To Grow.',
            note: (currentAdSpend && currentAdSpend.runs_ads)
                ? 'At ' + (currentAdSpend.roas || 'your current ROAS') + ', your ad spend isn’t compounding the way it should yet. Tighter targeting and a stronger funnel change that curve.'
                : 'You’re not running paid media yet, so growth is capped at whatever comes organically. A structured channel strategy changes that curve.',
            usNote: 'Compounding growth'
        };

        var growthPotential = data.ad_strategy && data.ad_strategy.growth_potential;

        // Both curves start from the same point - only how they diverge
        // differs: a healthy-ROAS brand already trends up well, so "with us"
        // stays a disciplined notch above it; a low/no-ROAS brand plateaus,
        // so "with us" pulls sharply away.
        var nowPoints = isGood
            ? [[0, 170], [100, 148], [200, 128], [300, 112], [400, 100]]
            : [[0, 170], [100, 152], [200, 142], [300, 136], [400, 132]];
        var usPoints = isGood
            ? [[0, 170], [100, 142], [200, 115], [300, 92], [400, 75]]
            : [[0, 170], [100, 135], [200, 95], [300, 55], [400, 25]];

        var html = '<div class="max-w-xl mx-auto w-full">';
        html += '<p class="story-anim text-xs uppercase tracking-[0.3em] font-bold text-[var(--electric-blue)] mb-4">Where This Goes From Here</p>';
        html += '<h3 class="story-anim story-emphasis text-2xl sm:text-3xl font-bold text-white leading-snug mb-5">' + copy.headline + '</h3>';

        html += '<div class="story-anim rounded-xl border border-white/10 bg-white/[0.03] p-4 sm:p-5 mb-5">';
        html += '<svg viewBox="0 0 400 200" class="w-full h-auto block" preserveAspectRatio="none" style="overflow:visible">';
        html += '<line x1="0" y1="170" x2="400" y2="170" stroke="rgba(255,255,255,0.12)" stroke-width="1"></line>';
        html += '<path class="story-growth-now" d="' + smoothPath(nowPoints) + '" fill="none" stroke="rgba(148,163,184,0.9)" stroke-width="3" stroke-linecap="round"></path>';
        html += '<path class="story-growth-us" d="' + smoothPath(usPoints) + '" fill="none" stroke="var(--electric-blue)" stroke-width="4" stroke-linecap="round"></path>';
        html += '<circle class="story-growth-now-dot" cx="400" cy="' + nowPoints[4][1] + '" r="5" fill="rgba(148,163,184,0.9)" opacity="0"></circle>';
        html += '<circle class="story-growth-us-dot" cx="400" cy="' + usPoints[4][1] + '" r="5" fill="var(--electric-blue)" opacity="0"></circle>';
        html += '</svg>';
        html += '<div class="story-anim flex items-center justify-between mt-3 text-xs">';
        html += '<span class="flex items-center gap-1.5 text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-slate-400 inline-block"></span>Your Growth Now</span>';
        html += '<span class="flex items-center gap-1.5 text-[var(--electric-blue)] font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-[var(--electric-blue)] inline-block"></span>With Digifyce · ' + escapeHtml(copy.usNote) + '</span>';
        html += '</div>';
        html += '</div>';

        html += '<p class="story-anim text-base text-slate-300 leading-relaxed">' + escapeHtml(copy.note) + '</p>';
        if (growthPotential) {
            html += '<p class="story-anim mt-3 text-sm text-slate-400 leading-relaxed border-l-2 border-[var(--electric-blue)]/40 pl-3">' + escapeHtml(growthPotential) + '</p>';
        }
        html += '</div>';
        return html;
    }

    // Word-by-word "mask reveal": each word sits inside its own
    // overflow-hidden box so it can slide up from below an invisible
    // boundary, instead of the whole title just fading in as one block.
    function buildMaskedTitle(text) {
        var words = (text || '').split(/\s+/).filter(Boolean);
        return words.map(function (w) {
            return '<span class="story-mask-word"><span class="story-mask-word-inner">' + escapeHtml(w) + '</span></span>';
        }).join(' ');
    }

    // A curated marketing-vocabulary list so the body paragraph's own key
    // phrases (whatever they happen to be, since this text is AI-generated
    // per audit) can be picked out and color-shifted after the paragraph
    // settles in, instead of hardcoding specific words.
    var STORY_HIGHLIGHT_TERMS = [
        'organic traffic', 'organic cotton', 'organic search', 'brand awareness', 'social media',
        'content marketing', 'conversion rates', 'conversion rate', 'customer retention',
        'repeat purchase', 'loyalty program', 'paid advertising', 'search visibility',
        'drive traffic', 'drive sales', 'increase sales', 'user-generated content', 'retargeting',
        'email marketing', 'click-through rate', 'engagement', 'seo', 'roi', 'roas', 'cta',
        'call-to-action', 'influencer', 'whatsapp', 'analytics', 'branding', 'positioning',
        'sustainability', 'eco-conscious'
    ];
    var STORY_HIGHLIGHT_PATTERN = new RegExp(
        '(' + STORY_HIGHLIGHT_TERMS.slice().sort(function (a, b) { return b.length - a.length; })
            .map(function (t) { return t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }).join('|') + ')',
        'gi'
    );

    function buildHighlightedBody(text, categoryColor, maxHighlights) {
        text = text || '';
        STORY_HIGHLIGHT_PATTERN.lastIndex = 0;
        var segments = [];
        var lastIndex = 0;
        var count = 0;
        var m;
        while (count < maxHighlights && (m = STORY_HIGHLIGHT_PATTERN.exec(text))) {
            if (m.index > lastIndex) segments.push({ text: text.slice(lastIndex, m.index), hl: false });
            segments.push({ text: m[0], hl: true });
            lastIndex = m.index + m[0].length;
            count++;
        }
        if (lastIndex < text.length) segments.push({ text: text.slice(lastIndex), hl: false });

        var hlIndex = 0;
        return segments.map(function (seg) {
            if (!seg.hl) return escapeHtml(seg.text);
            var color = (hlIndex % 2 === 0) ? 'var(--electric-blue)' : categoryColor;
            hlIndex++;
            return '<span class="story-highlight" data-hl-color="' + color + '">' + escapeHtml(seg.text) + '</span>';
        }).join('');
    }

    function renderStoryImplementationSlideHtml(item, index, total) {
        var visual = pickImplementationVisual(item);
        var gradId = 'storyGrowthGrad' + index;
        var html = '<div class="max-w-lg mx-auto relative story-impl-wrap">';

        // A thin, glowing, self-drawing growth-chart line behind the text -
        // real volatility (up, down, up, down) but a clear net-positive
        // trajectory ending at the top-right, kept low-opacity so it reads
        // as ambient background rather than competing with the copy.
        var bgPoints = [
            [10, 300], [80, 268], [125, 282], [200, 220], [250, 236],
            [320, 150], [385, 75]
        ];
        var bgPeakIndices = [3, 6]; // just the mid and final "up" points
        html += '<svg class="story-bg-growth absolute pointer-events-none" viewBox="0 0 400 320" preserveAspectRatio="none" style="left:-40px;top:-30px;width:calc(100% + 80px);height:calc(100% + 60px);">';
        html += '<defs><linearGradient id="' + gradId + '" x1="0%" y1="100%" x2="100%" y2="0%">';
        html += '<stop offset="0%" stop-color="var(--electric-blue)"/><stop offset="100%" stop-color="' + visual.color + '"/>';
        html += '</linearGradient></defs>';
        html += '<path class="story-bg-growth-path" d="' + smoothPath(bgPoints) + '" stroke="url(#' + gradId + ')" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.16" style="filter: drop-shadow(0 0 3px ' + visual.color + ');"/>';
        bgPeakIndices.forEach(function (pi, di) {
            var pt = bgPoints[pi];
            html += '<circle class="story-bg-growth-dot" cx="' + pt[0] + '" cy="' + pt[1] + '" r="3" fill="' + (di % 2 === 0 ? 'var(--electric-blue)' : visual.color) + '"/>';
        });
        html += '</svg>';

        html += '<p class="story-anim relative flex items-center gap-2 text-xs uppercase tracking-[0.3em] font-bold text-[var(--electric-blue)] mb-6">' + icon('auto_awesome', 'text-base') + 'What We Can Do For Your Brand · ' + (index + 1) + '/' + total + '</p>';
        html += '<span class="story-anim relative block text-[11px] uppercase tracking-[0.2em] font-bold mb-3" style="color:' + visual.color + ';">' + escapeHtml(visual.label) + '</span>';
        html += '<h3 class="story-impl-title relative text-2xl sm:text-3xl font-bold text-white leading-snug mb-4">' + buildMaskedTitle(item.title || '') + '</h3>';
        if (item.description) {
            html += '<p class="story-impl-body relative text-base text-slate-300 leading-relaxed">' + buildHighlightedBody(item.description, visual.color, 3) + '</p>';
        }
        html += '</div>';
        return html;
    }

    function renderStoryClosingSlideHtml() {
        var html = '<div class="max-w-lg mx-auto text-center flex flex-col items-center">';
        html += '<p class="story-anim text-xs uppercase tracking-[0.3em] font-bold text-[var(--electric-blue)] mb-5">Ready When You Are</p>';
        html += '<h3 class="story-anim story-emphasis text-2xl sm:text-3xl font-bold text-white leading-snug mb-4">Let&rsquo;s build this for your brand.</h3>';
        html += '<p class="story-anim text-base text-slate-300 leading-relaxed mb-6">We&rsquo;d love to stay in touch and help you get there.</p>';
        html += '<button type="button" id="storyKeepInTouch" class="story-cta story-anim inline-flex items-center gap-2 bg-[var(--electric-blue)] text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[var(--navy-black)] transition-all">';
        html += icon('check', 'text-base') + '<span class="story-keep-in-touch-label">Keep In Touch</span>';
        html += '</button>';
        html += '<button type="button" id="storyViewReport" class="story-anim mt-5 text-xs uppercase tracking-[0.2em] text-slate-500 hover:text-white transition-colors underline underline-offset-4">View full report</button>';
        html += '</div>';
        return html;
    }

    function buildStorySlides(data) {
        var slides = [];
        slides.push({ kind: 'score', html: renderStoryScoreSlideHtml(data) });

        if (data.brand_brief || data.screenshot_url) {
            slides.push({ kind: 'brand', html: renderStoryBrandSlideHtml(data) });
        }

        slides.push({ kind: 'growth', html: renderStoryGrowthSlideHtml(data) });

        var implementations = data.case_study_matches || [];
        implementations.forEach(function (item, i) {
            slides.push({ kind: 'implementation', html: renderStoryImplementationSlideHtml(item, i, implementations.length) });
        });

        slides.push({ kind: 'closing', html: renderStoryClosingSlideHtml() });
        return slides;
    }

    function buildGrainDataUrl() {
        if (grainDataUrlCache) return grainDataUrlCache;
        var size = 180;
        var canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        var ctx = canvas.getContext('2d');
        var imageData = ctx.createImageData(size, size);
        for (var i = 0; i < imageData.data.length; i += 4) {
            var v = Math.floor(Math.random() * 255);
            imageData.data[i] = v;
            imageData.data[i + 1] = v;
            imageData.data[i + 2] = v;
            imageData.data[i + 3] = 255;
        }
        ctx.putImageData(imageData, 0, 0);
        grainDataUrlCache = canvas.toDataURL('image/png');
        return grainDataUrlCache;
    }

    // Plays once, before slide 0: a brief black title card (the wordmark
    // fades and settles in, holds, fades out) while the letterbox bars are
    // still fully open behind it - by the time it clears, the frame is
    // ready to narrow into its widescreen resting position.
    function playStoryIntro(onComplete) {
        var intro = document.getElementById('storyIntro');
        var mark = intro ? intro.querySelector('.story-intro-mark') : null;
        if (!intro || !mark || prefersReducedMotion || !window.gsap) {
            if (intro) intro.style.display = 'none';
            onComplete();
            return;
        }
        gsap.timeline({ onComplete: function () { intro.style.display = 'none'; onComplete(); } })
            .set(intro, { display: 'flex', opacity: 1 })
            .to(mark, { opacity: 1, scale: 1, duration: 0.7, ease: 'power2.out' })
            .to(mark, { opacity: 1, scale: 1, duration: 0.5 }) // pure hold - let it sit before the cut
            .to(intro, { opacity: 0, duration: 0.45, ease: 'power1.in' }, '+=0.05');
    }

    // The stage opens full-bleed and narrows down to its letterboxed resting
    // height right as the intro card clears - "curtains closing in," not a
    // static bar that was just always there.
    function openStoryLetterbox(callback) {
        var stage = document.getElementById('storyStage');
        var chromeEls = [document.getElementById('storyProgress'), document.getElementById('storyClose'), document.getElementById('storySkip')].filter(Boolean);
        if (prefersReducedMotion || !window.gsap) {
            stage.style.top = '56px';
            stage.style.bottom = '56px';
            chromeEls.forEach(function (el) { el.style.opacity = '1'; });
            callback();
            return;
        }
        gsap.set(stage, { top: 0, bottom: 0 });
        gsap.set(chromeEls, { opacity: 0 });
        gsap.timeline({ onComplete: callback })
            .to(stage, { top: 56, bottom: 56, duration: 0.9, ease: 'power3.out' })
            .to(chromeEls, { opacity: 1, duration: 0.4 }, '-=0.35');
    }

    function ensureStoryContainer() {
        var el = document.getElementById('auditStory');
        if (el) return el;
        el = document.createElement('div');
        el.id = 'auditStory';
        el.className = 'hidden';
        el.setAttribute('role', 'dialog');
        el.setAttribute('aria-modal', 'true');
        el.setAttribute('aria-label', 'Your growth story');
        el.innerHTML =
            '<div class="story-stage" id="storyStage"></div>' +
            '<div class="story-vignette"></div>' +
            '<div class="absolute top-0 left-0 right-0 z-10 flex gap-1.5 p-3" id="storyProgress"></div>' +
            '<button type="button" id="storyClose" aria-label="Close" class="absolute top-3 right-3 z-10 w-9 h-9 flex items-center justify-center rounded-full bg-black/30 text-white/80 hover:text-white hover:bg-black/50 transition-colors">' + icon('close') + '</button>' +
            '<div class="story-tap story-tap-prev" id="storyTapPrev" aria-hidden="true"></div>' +
            '<div class="story-tap story-tap-next" id="storyTapNext" aria-hidden="true"></div>' +
            '<button type="button" id="storySkip" class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1 text-xs uppercase tracking-[0.2em] text-slate-400 hover:text-white transition-colors">Skip to full report' + icon('expand_more', 'text-base') + '</button>' +
            '<div class="story-grain" id="storyGrain"></div>' +
            '<div class="story-intro" id="storyIntro"><span class="story-intro-mark">DIGIFYCE</span></div>';
        document.body.appendChild(el);

        var grain = el.querySelector('#storyGrain');
        if (grain) grain.style.backgroundImage = 'url(' + buildGrainDataUrl() + ')';

        return el;
    }

    function wireStoryEvents(el) {
        if (storyWired) return;
        storyWired = true;

        document.getElementById('storyClose').addEventListener('click', function () { closeStory(false); });
        document.getElementById('storySkip').addEventListener('click', function () { closeStory(true); });
        document.getElementById('storyTapPrev').addEventListener('click', function () { goToStorySlide(storyState.index - 1); });
        document.getElementById('storyTapNext').addEventListener('click', function () { goToStorySlide(storyState.index + 1); });

        var stage = document.getElementById('storyStage');
        stage.addEventListener('click', function (e) {
            if (e.target.closest('#storyViewReport')) { closeStory(true); return; }
            var kitBtn = e.target.closest('#storyKeepInTouch');
            if (kitBtn) confirmKeepInTouch(kitBtn);
        });

        var pause = function () { if (storyState.timeline) storyState.timeline.pause(); };
        var resume = function () { if (storyState.timeline) storyState.timeline.play(); };
        stage.addEventListener('pointerdown', pause);
        stage.addEventListener('pointerup', resume);
        stage.addEventListener('pointercancel', resume);

        var touchStartX = null;
        stage.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        stage.addEventListener('touchend', function (e) {
            if (touchStartX === null) return;
            var dx = e.changedTouches[0].clientX - touchStartX;
            touchStartX = null;
            if (Math.abs(dx) < 40) return;
            if (dx < 0) goToStorySlide(storyState.index + 1); else goToStorySlide(storyState.index - 1);
        }, { passive: true });

        document.addEventListener('keydown', function (e) {
            if (el.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeStory(false);
            else if (e.key === 'ArrowRight') goToStorySlide(storyState.index + 1);
            else if (e.key === 'ArrowLeft') goToStorySlide(storyState.index - 1);
        });
    }

    function animateStoryScore(slideEl) {
        var ring = slideEl.querySelector('.story-score-ring');
        var num = slideEl.querySelector('.story-score-num');
        if (!ring) return;
        var score = parseInt(ring.getAttribute('data-score'), 10);
        if (isNaN(score)) return;
        var circumference = Math.round(2 * Math.PI * 54);
        var offset = Math.round(circumference * (1 - score / 100));
        if (prefersReducedMotion || !window.gsap) {
            ring.style.strokeDashoffset = offset;
            if (num) num.textContent = score;
            return;
        }
        gsap.set(ring, { strokeDashoffset: circumference });
        gsap.to(ring, { strokeDashoffset: offset, duration: 1.2, ease: 'power2.out', delay: 0.15 });
        var counter = { val: 0 };
        gsap.to(counter, { val: score, duration: 1.2, ease: 'power2.out', delay: 0.15, onUpdate: function () { if (num) num.textContent = Math.round(counter.val); } });
    }

    function animateStoryGrowthChart(slideEl) {
        var nowPath = slideEl.querySelector('.story-growth-now');
        var usPath = slideEl.querySelector('.story-growth-us');
        var nowDot = slideEl.querySelector('.story-growth-now-dot');
        var usDot = slideEl.querySelector('.story-growth-us-dot');
        if (!nowPath || !usPath) return;

        [nowPath, usPath].forEach(function (path) {
            var len = path.getTotalLength();
            path.style.strokeDasharray = len;
            path.style.strokeDashoffset = len;
        });

        if (prefersReducedMotion || !window.gsap) {
            [nowPath, usPath].forEach(function (path) { path.style.strokeDashoffset = 0; });
            if (nowDot) nowDot.setAttribute('opacity', '1');
            if (usDot) usDot.setAttribute('opacity', '1');
            return;
        }

        // "Now" draws first (the baseline), then "With Digifyce" draws over
        // it and pulls away - the divergence itself is the point, so it
        // needs to be watched happening, not just shown as a static state.
        var tl = gsap.timeline({ delay: 0.3 });
        tl.to(nowPath, { strokeDashoffset: 0, duration: 1.1, ease: 'power2.out' })
          .to(nowDot, { opacity: 1, duration: 0.2 }, '-=0.1')
          .to(usPath, { strokeDashoffset: 0, duration: 1.3, ease: 'power2.out' }, '-=0.3')
          .to(usDot, { opacity: 1, duration: 0.2 }, '-=0.1');
    }

    // The bespoke "What We Can Do For Your Brand" reveal sequence: labels
    // fade up first, then the title's words slide up out of a mask, then
    // the body paragraph wipes in top-to-bottom, then its key phrases
    // shift color - while a low-opacity growth line quietly draws itself
    // in behind all of it, on its own slower clock.
    function animateStoryImplementation(slideEl) {
        var labels = slideEl.querySelectorAll('.story-anim');
        var maskWords = slideEl.querySelectorAll('.story-mask-word-inner');
        var body = slideEl.querySelector('.story-impl-body');
        var highlights = slideEl.querySelectorAll('.story-highlight');
        var bgPath = slideEl.querySelector('.story-bg-growth-path');
        var bgDots = slideEl.querySelectorAll('.story-bg-growth-dot');

        if (bgPath) {
            var len = bgPath.getTotalLength();
            bgPath.style.strokeDasharray = len;
            bgPath.style.strokeDashoffset = len;
        }

        if (prefersReducedMotion || !window.gsap) {
            Array.prototype.forEach.call(labels, function (el) { el.style.opacity = '1'; el.style.transform = 'none'; });
            Array.prototype.forEach.call(maskWords, function (el) { el.style.transform = 'none'; });
            if (body) body.style.clipPath = 'inset(0 0 0% 0)';
            Array.prototype.forEach.call(highlights, function (el) { el.style.color = el.getAttribute('data-hl-color'); });
            if (bgPath) bgPath.style.strokeDashoffset = 0;
            Array.prototype.forEach.call(bgDots, function (el) { el.style.opacity = '0.35'; });
            return;
        }

        if (bgPath) {
            gsap.to(bgPath, { strokeDashoffset: 0, duration: 3.2, ease: 'power2.out', delay: 0.2 });
        }
        if (bgDots.length) {
            // Each dot lights up roughly as the self-drawing line reaches
            // it, so the "peaks" of the up/down/up swing read as waypoints
            // on a chart, not just a decorative squiggle.
            gsap.to(bgDots, { opacity: 0.35, duration: 0.4, stagger: 0.32, delay: 0.5, ease: 'power1.out' });
        }

        var tl = gsap.timeline();
        tl.fromTo(labels, { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: 0.45, ease: 'power2.out', stagger: 0.08 });
        if (maskWords.length) {
            tl.fromTo(maskWords, { yPercent: 115 }, { yPercent: 0, duration: 0.55, ease: 'power3.out', stagger: 0.035 }, '-=0.15');
        }
        if (body) {
            tl.fromTo(body, { clipPath: 'inset(0 0 100% 0)' }, { clipPath: 'inset(0 0 0% 0)', duration: 0.9, ease: 'power2.inOut' }, '-=0.1');
        }
        if (highlights.length) {
            tl.to(highlights, {
                color: function (i, target) { return target.getAttribute('data-hl-color'); },
                duration: 0.6, stagger: 0.15, ease: 'power1.inOut'
            }, body ? '-=0.3' : '+=0');
        }
    }

    function animateStoryKenBurns(slideEl, duration) {
        var img = slideEl.querySelector('.story-shot');
        if (!img || prefersReducedMotion || !window.gsap) return;
        // A slow, continuous drift for exactly as long as this slide is on
        // screen - documentary b-roll, not a paused screenshot.
        gsap.fromTo(img, { scale: 1 }, { scale: 1.09, duration: duration, ease: 'none' });
    }

    function animateStoryAmbientBreathing(slideEl) {
        if (prefersReducedMotion || !window.gsap) return;
        var wrap = slideEl.firstElementChild;
        if (!wrap) return;
        // A slow, continuous handheld-camera-like drift on the whole slide,
        // independent of and on top of the entrance stagger and any
        // slide-specific animation (Ken Burns, the score ring, the growth
        // chart) - the thing that keeps even a text-only slide from ever
        // reading as a paused frame.
        storyState.ambientTween = gsap.to(wrap, {
            scale: 1.015, x: 4, y: -3, duration: 7, ease: 'sine.inOut', yoyo: true, repeat: -1
        });
    }

    function animateStorySlideIn(slideEl, duration) {
        if (!slideEl) return;
        var kind = slideEl.getAttribute('data-kind');

        // The implementation slide runs its own bespoke reveal (masked
        // title, wiped body, highlight color-shift, background growth
        // line) instead of the generic fade/zoom treatment below.
        if (kind === 'implementation') {
            animateStoryImplementation(slideEl);
            animateStoryAmbientBreathing(slideEl);
            return;
        }

        var normalItems = slideEl.querySelectorAll('.story-anim:not(.story-emphasis)');
        var emphasisItems = slideEl.querySelectorAll('.story-emphasis');
        if (prefersReducedMotion || !window.gsap) {
            Array.prototype.forEach.call(normalItems, function (elx) { elx.style.opacity = '1'; elx.style.transform = 'none'; });
            Array.prototype.forEach.call(emphasisItems, function (elx) { elx.style.opacity = '1'; elx.style.transform = 'none'; });
        } else {
            // The headline/hero elements of each slide (score, headline,
            // feature icon) punch in with a zoom - the kind of emphasis a
            // video editor would put on the one thing that matters on
            // screen - while everything else just settles in underneath it.
            gsap.fromTo(emphasisItems, { opacity: 0, scale: 0.72 }, { opacity: 1, scale: 1, duration: 0.65, ease: 'back.out(1.6)', stagger: 0.1 });
            gsap.fromTo(normalItems, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out', stagger: 0.08, delay: 0.12 });
        }
        if (kind === 'score') animateStoryScore(slideEl);
        else if (kind === 'growth') animateStoryGrowthChart(slideEl);
        else if (kind === 'brand') animateStoryKenBurns(slideEl, duration || STORY_MAX_SECONDS);
        animateStoryAmbientBreathing(slideEl);
    }

    function showStorySlide(i) {
        var slides = storyState.slides;
        var isLast = i === slides.length - 1;
        storyState.index = i;
        storyState.started = true;

        if (storyState.timeline) { storyState.timeline.kill(); storyState.timeline = null; }
        if (storyState.ambientTween) { storyState.ambientTween.kill(); storyState.ambientTween = null; }

        var slideEls = document.querySelectorAll('#storyStage .story-slide');
        Array.prototype.forEach.call(slideEls, function (slideEl, idx) {
            slideEl.classList.toggle('is-active', idx === i);
        });

        var fills = document.querySelectorAll('#storyProgress .story-seg-fill');
        Array.prototype.forEach.call(fills, function (fill, idx) {
            if (window.gsap) gsap.killTweensOf(fill);
            if (idx < i || (idx === i && isLast)) {
                fill.style.transform = 'scaleX(1)';
            } else if (idx > i) {
                fill.style.transform = 'scaleX(0)';
            }
        });

        var duration = computeSlideDuration(slideEls[i]);
        animateStorySlideIn(slideEls[i], duration);

        // The closing CTA slide never auto-advances (and thus never
        // auto-closes) - a lead reading the CTA shouldn't have the story
        // vanish out from under them on a fixed timer.
        if (isLast) return;

        var currentFill = fills[i];
        if (!currentFill) return;

        if (prefersReducedMotion) {
            currentFill.style.transform = 'scaleX(1)';
            return;
        }

        if (window.gsap) {
            gsap.set(currentFill, { scaleX: 0 });
            storyState.timeline = gsap.to(currentFill, {
                scaleX: 1, duration: duration, ease: 'none',
                onComplete: function () { goToStorySlide(i + 1); }
            });
        } else {
            currentFill.style.transition = 'none';
            currentFill.style.transform = 'scaleX(0)';
            requestAnimationFrame(function () {
                currentFill.style.transition = 'transform ' + duration + 's linear';
                currentFill.style.transform = 'scaleX(1)';
            });
        }
    }

    function goToStorySlide(i) {
        // Ignore taps/swipes/keys that land before the opening title
        // card + letterbox reveal finishes and calls showStorySlide(0) for
        // the first time - otherwise an impatient early tap advancing to
        // slide 1 gets silently stomped back to slide 0 once that intro
        // sequence's own showStorySlide(0) call finally lands.
        if (!storyState.started) return;
        if (i >= storyState.slides.length) { closeStory(true); return; }
        if (i < 0) i = 0;
        showStorySlide(i);
    }

    function confirmKeepInTouch(btn) {
        // A real button click, not a toggle - swap the label to a quick
        // confirmation and disable further clicks so it can't double-fire,
        // long enough to register as "yes, that worked" before moving on
        // to the report.
        if (btn) {
            var label = btn.querySelector('.story-keep-in-touch-label');
            if (label) label.textContent = 'Thanks!';
            btn.style.pointerEvents = 'none';
        }

        fetch(keepInTouchApiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'token=' + encodeURIComponent(token) + '&value=1'
        }).catch(function () {}); // best-effort - a network hiccup shouldn't block the lead from reaching their report

        setTimeout(function () { closeStory(true); }, 700);
    }

    function closeStory(scrollToReport) {
        var el = document.getElementById('auditStory');
        if (!el || el.classList.contains('hidden')) return;
        if (storyState.timeline) { storyState.timeline.kill(); storyState.timeline = null; }
        el.classList.add('hidden');
        document.body.style.overflow = '';
        if (scrollToReport && resultEl) {
            resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function openStory(data) {
        var slides = buildStorySlides(data);
        if (!slides.length) return;

        var el = ensureStoryContainer();
        wireStoryEvents(el);
        storyState.slides = slides;
        storyState.started = false;

        document.getElementById('storyStage').innerHTML = slides.map(function (s, i) {
            return '<div class="story-slide" data-index="' + i + '" data-kind="' + s.kind + '">' + s.html + '</div>';
        }).join('');
        document.getElementById('storyProgress').innerHTML = slides.map(function () {
            return '<div class="story-seg h-[3px] flex-1 rounded-full bg-white/25 overflow-hidden"><div class="story-seg-fill h-full w-full bg-[var(--electric-blue)]"></div></div>';
        }).join('');

        el.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        playStoryIntro(function () {
            openStoryLetterbox(function () {
                showStorySlide(0);
            });
        });
    }

    function poll() {
        pollCount++;
        fetch(apiUrl + '?token=' + encodeURIComponent(token))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) {
                    failedEl.classList.remove('hidden');
                    pendingEl.classList.add('hidden');
                    return;
                }
                if (data.status === 'completed') {
                    renderCompleted(data);
                    return;
                }
                if (data.status === 'failed') {
                    failedEl.classList.remove('hidden');
                    pendingEl.classList.add('hidden');
                    return;
                }
                if (pollCount >= maxPolls) {
                    var msg = pendingEl.querySelector('p.text-sm');
                    if (msg) msg.textContent = 'Still working on it — refresh this page in a bit to check again.';
                    return;
                }
                setTimeout(poll, 3000);
            })
            .catch(function () {
                if (pollCount < maxPolls) {
                    setTimeout(poll, 3000);
                }
            });
    }

    var dotStates = ['.', '..', '...'];
    var dotIndex = 0;
    setInterval(function () {
        if (dotsEl) {
            dotIndex = (dotIndex + 1) % dotStates.length;
            dotsEl.textContent = dotStates[dotIndex];
        }
    }, 500);

    poll();
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/app/views/footer.php'; ?>
