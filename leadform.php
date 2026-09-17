<?php
$pageTitle = 'Contact Digifyce – Request a Consultation';
$pageDescription = 'Get in touch with Digifyce for a consultation, growth strategy session, or marketing inquiry. Let\'s accelerate your business success.';
include __DIR__ . '/app/views/header.php';
?>
<?php
require_once __DIR__ . '/config/database.php';
$selectedServicesText = '';

if (!empty($_GET['services'])) {
    $services = $_GET['services'];
    $selectedServicesText = "I am interested in: " . implode(', ', $services) . ".";
}

$submitted = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        $company = isset($_POST['company']) ? trim($_POST['company']) : null;
        $mainObjective = isset($_POST['main_objective']) ? trim($_POST['main_objective']) : '';
        $businessType = isset($_POST['business_type']) ? trim($_POST['business_type']) : '';
        $industryType = isset($_POST['industry_type']) ? trim($_POST['industry_type']) : '';
        $budget = isset($_POST['budget']) ? trim($_POST['budget']) : '';
        $website = isset($_POST['website']) ? trim($_POST['website']) : null;
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        $objectiveOptions = ['leads' => 'Leads', 'sales_website' => 'Sales (Website)'];
        $businessTypeOptions = ['own_manufacturer' => 'Own Manufacturer', 'reseller_retailer' => 'Reseller/Retailer (Outsource Products)', 'wholesale' => 'Wholesale'];
        $industryOptions = ['fmcg' => 'FMCG', 'textile' => 'Textile', 'cosmetic' => 'Cosmetic', 'others' => 'Others'];
        $budgetOptions = ['50000' => '₹50,000', '1lakh' => '₹1 Lakh', 'above_1lakh' => 'Above ₹1 Lakh'];

        if (empty($fullName) || empty($email) || empty($message) || empty($mainObjective) || empty($businessType) || empty($industryType) || empty($budget)) {
            $errorMessage = 'Please fill in all required fields.';
        } else {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $errorMessage = 'Please enter a valid email address.';
            } else {
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

                // Qualification: Sales(website) objective + Own Manufacturer/Wholesale + budget of 1Lakh or above
                $isQualified = ($mainObjective === 'sales_website')
                    && in_array($businessType, ['own_manufacturer', 'wholesale'], true)
                    && in_array($budget, ['1lakh', 'above_1lakh'], true);

                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("INSERT INTO lead_form_submissions (full_name, email, phone, company, main_objective, business_type, industry, budget, is_qualified, website, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fullName, $email, $phone, $company, $mainObjective, $businessType, $industryType, $budget, $isQualified ? 1 : 0, $website, $message, $ipAddress, $userAgent]);

                $leadId = $pdo->lastInsertId();

                // Send lead to CRM webhook
                $crmWebhookUrl = 'https://crm.zingbot.io/api/external/webhook_receiver_dynamic.php?org_id=10&conn_id=47674d8c390bb72696198f81797f238f&api_key=1e3d1ca5c5fe8226550f05824b3b9b83c152dafc4ccb665f30bb798693671fd4';

                $leadData = [
                    'id' => $leadId,
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'company' => $company,
                    'main_objective' => $objectiveOptions[$mainObjective] ?? $mainObjective,
                    'business_type' => $businessTypeOptions[$businessType] ?? $businessType,
                    'industry_type' => $industryOptions[$industryType] ?? $industryType,
                    'budget' => $budgetOptions[$budget] ?? $budget,
                    'is_qualified' => $isQualified,
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

                $submitted = true;
            }
        }
    } catch (Exception $e) {
        $errorMessage = 'An error occurred. Please try again later.';
    }
}
?>

<section class="py-16 sm:py-20 lg:py-24 bg-[#030508]">
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 sm:mb-12 text-center">

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter">Tell Us About Your Project</h1>
            <p class="mt-4 text-slate-500 text-base sm:text-lg">Share a few details and we’ll get back with a growth
                plan.</p>
        </div>

        <?php if ($submitted): ?>
            <div class="mb-8 sm:mb-10 rounded-lg border border-green-500/30 bg-green-500/10 p-5 text-center text-green-300">
                ✓ Thank you! Your details were submitted successfully. We will get back to you within 24 hours.
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="mb-8 sm:mb-10 rounded-lg border border-red-500/30 bg-red-500/10 p-5 text-center text-red-300">
                ⚠ <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Full Name</label>
                <input name="full_name" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="Your name" />
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Email</label>
                <input type="email" name="email" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="you@company.com" />
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Phone</label>
                <input type="tel" name="phone"
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="+1 (555) 000-0000" />
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Company</label>
                <input name="company"
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="Company name" />
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">What is your main objective?</label>
                <select name="main_objective" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                    <option value="" class="bg-[#030508]">Select objective</option>
                    <option value="leads" class="bg-[#030508]">Leads</option>
                    <option value="sales_website" class="bg-[#030508]">Sales (Website)</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Does your business have its own manufacturing facility?</label>
                <select name="business_type" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                    <option value="" class="bg-[#030508]">Select an option</option>
                    <option value="own_manufacturer" class="bg-[#030508]">Own Manufacturer</option>
                    <option value="reseller_retailer" class="bg-[#030508]">Reseller/Retailer (Outsource Products)</option>
                    <option value="wholesale" class="bg-[#030508]">Wholesale</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">What type of industry is your business?</label>
                <select name="industry_type" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                    <option value="" class="bg-[#030508]">Select industry</option>
                    <option value="fmcg" class="bg-[#030508]">FMCG</option>
                    <option value="textile" class="bg-[#030508]">Textile</option>
                    <option value="cosmetic" class="bg-[#030508]">Cosmetic</option>
                    <option value="others" class="bg-[#030508]">Others</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Estimated monthly marketing budget</label>
                <select name="budget" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
                    <option value="" class="bg-[#030508]">Select budget</option>
                    <option value="50000" class="bg-[#030508]">₹50,000</option>
                    <option value="1lakh" class="bg-[#030508]">₹1 Lakh</option>
                    <option value="above_1lakh" class="bg-[#030508]">Above ₹1 Lakh</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Website</label>
                <input name="website"
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="https://" />
            </div>
            <div class="md:col-span-2">
                <label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Project Details</label>
                <textarea name="message" rows="5" required
                    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-600 focus:border-[var(--electric-blue)] focus:outline-none"
                    placeholder="Tell us about your goals..."><?= htmlspecialchars($selectedServicesText) ?></textarea>
            </div>
            <div class="md:col-span-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <p class="text-[10px] uppercase tracking-[0.3em] text-slate-600">We respond within 24 hours</p>
                <button type="submit"
                    class="bg-[var(--electric-blue)] text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[var(--navy-black)] transition-all">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/app/views/footer.php'; ?>