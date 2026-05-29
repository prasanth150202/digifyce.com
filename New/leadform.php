

<?php include __DIR__ . '/app/views/header.php'; ?>
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
        $budget = isset($_POST['budget']) ? trim($_POST['budget']) : null;
        $website = isset($_POST['website']) ? trim($_POST['website']) : null;
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        if (empty($fullName) || empty($email) || empty($message)) {
            $errorMessage = 'Please fill in all required fields.';
        } else {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $errorMessage = 'Please enter a valid email address.';
            } else {
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
                
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("INSERT INTO lead_form_submissions (full_name, email, phone, company, budget, website, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fullName, $email, $phone, $company, $budget, $website, $message, $ipAddress, $userAgent]);
                
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
			<div class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-3">Lead Form</div>
			<h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter">Tell Us About Your Project</h1>
			<p class="mt-4 text-slate-500 text-base sm:text-lg">Share a few details and we’ll get back with a growth plan.</p>
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
				<label class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Monthly Budget</label>
				<select name="budget"
    class="mt-3 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-[var(--electric-blue)] focus:outline-none">
    
    <option value="" class="bg-[#030508]">Select budget</option>
    <option value="4l-8l" class="bg-[#030508]">₹4L - ₹8L</option>
    <option value="8l-20l" class="bg-[#030508]">₹8L - ₹20L</option>
    <option value="20l-40l" class="bg-[#030508]">₹20L - ₹40L</option>
    <option value="40l+" class="bg-[#030508]">₹40L+</option>

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
