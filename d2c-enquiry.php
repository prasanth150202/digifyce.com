<?php
// D2C brand enquiry lead page: a standalone landing page (no site header/footer,
// light theme, self-contained CSS). Each submission is appended to a Google
// Sheet via app/utilities/GoogleSheetLead.php (setup: tools/google-sheets/d2c-leads.gs).
require_once __DIR__ . '/app/utilities/AppUrl.php';
require_once __DIR__ . '/app/utilities/GoogleSheetLead.php';
$appUrl = AppUrl::resolve();

$pageTitle = 'Grow Your D2C Brand – Talk to Digifyce';
$pageDescription = 'Tell us about your D2C brand and our team will connect with you to discuss opportunities to improve your product sales.';

$categoryOptions = [
    'Clothing & Fashion',
    'FMCG',
    'Beauty & Cosmetics',
    'Health & Wellness',
    'Baby & Kids',
    'Home & Lifestyle',
    'Others',
];

$submitted = isset($_GET['success']);
$errorMessage = '';
$old = ['full_name' => '', 'contact_number' => '', 'brand_category' => '', 'location' => '', 'additional_details' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    }

    // Honeypot: real visitors never see or fill this field.
    $isBot = !empty($_POST['website_url']);
    $phoneDigits = preg_replace('/\D/', '', $old['contact_number']);

    if ($old['full_name'] === '' || $old['contact_number'] === '' || $old['brand_category'] === '' || $old['location'] === '') {
        $errorMessage = 'Please fill in all required fields.';
    } elseif (!preg_match('/^\+?[\d\s\-()]+$/', $old['contact_number']) || strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
        $errorMessage = 'Please enter a valid contact number.';
    } elseif (!in_array($old['brand_category'], $categoryOptions, true)) {
        $errorMessage = 'Please select a brand category.';
    } elseif (mb_strlen($old['full_name']) > 120 || mb_strlen($old['location']) > 120 || mb_strlen($old['additional_details']) > 2000) {
        $errorMessage = 'Some of your answers are too long. Please shorten them and try again.';
    } else {
        $saved = $isBot ? true : GoogleSheetLead::send([
            'submitted_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s'),
            'full_name' => $old['full_name'],
            'contact_number' => $old['contact_number'],
            'brand_category' => $old['brand_category'],
            'location' => $old['location'],
            'additional_details' => $old['additional_details'],
            'page_url' => $appUrl . '/d2c-enquiry',
        ]);

        if ($saved) {
            // Redirect so refreshing the thank-you view never re-submits the lead.
            header('Location: ' . $appUrl . '/d2c-enquiry?success=1');
            exit;
        }
        $errorMessage = 'We couldn\'t submit your details just now. Please try again in a moment.';
    }
}

$e = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($pageTitle) ?></title>
    <meta name="description" content="<?= $e($pageDescription) ?>">
    <link rel="canonical" href="<?= $e($appUrl . '/d2c-enquiry') ?>">
    <meta property="og:title" content="<?= $e($pageTitle) ?>">
    <meta property="og:description" content="<?= $e($pageDescription) ?>">
    <meta property="og:url" content="<?= $e($appUrl . '/d2c-enquiry') ?>">
    <meta name="theme-color" content="#f5f7fb">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Microsoft Clarity -->
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","w58ca53ero");</script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2D5QYRRXZG"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-2D5QYRRXZG');</script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-TDJJXH8X');</script>

    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --field: #f8fafc;
            --brand: #0066ff;
            --brand-dark: #0052cc;
            --brand-soft: #e8f0ff;
            --error: #dc2626;
            --error-soft: #fef2f2;
            --success: #16a34a;
            --success-soft: #ecfdf3;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background:
                radial-gradient(1200px 500px at 50% -10%, var(--brand-soft), transparent 70%),
                var(--bg);
            color: var(--ink);
            font-family: 'Space Grotesk', system-ui, -apple-system, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .topbar {
            padding: 24px 20px 0;
            text-align: center;
        }
        .logo { display: inline-block; }
        .logo img { display: block; height: 52px; width: auto; }
        main {
            flex: 1;
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            padding: 32px 16px 48px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 40px -12px rgba(15, 23, 42, 0.12);
            padding: 40px;
        }
        .intro { text-align: center; margin-bottom: 32px; }
        h1 {
            margin: 0 0 10px;
            font-size: clamp(26px, 4vw, 34px);
            line-height: 1.15;
            letter-spacing: -0.03em;
        }
        .lead {
            margin: 0 auto;
            max-width: 520px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
        }
        .alert {
            margin-bottom: 24px;
            padding: 12px 16px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            background: var(--error-soft);
            color: var(--error);
            font-size: 14px;
            text-align: center;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .full { grid-column: 1 / -1; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        label .req { color: var(--brand); }
        label .opt { color: var(--muted); font-weight: 400; }
        input, select, textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--field);
            color: var(--ink);
            font: inherit;
            font-size: 16px;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        input::placeholder, textarea::placeholder { color: #94a3b8; }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--brand);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 102, 255, 0.12);
        }
        select {
            appearance: none;
            padding-right: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            cursor: pointer;
        }
        select:invalid { color: #94a3b8; }
        select option { color: var(--ink); }
        textarea { resize: vertical; min-height: 120px; }
        .hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 4px;
            padding: 15px 24px;
            border: 0;
            border-radius: 12px;
            background: var(--brand);
            color: #fff;
            font: inherit;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background .15s, transform .05s;
        }
        .btn:hover { background: var(--brand-dark); }
        .btn:active { transform: translateY(1px); }
        .btn:disabled { opacity: .65; cursor: wait; }
        .note { margin: 4px 0 0; color: var(--muted); font-size: 13px; text-align: center; }
        .thanks { text-align: center; padding: 16px 0; }
        .check {
            width: 64px; height: 64px;
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            background: var(--success-soft);
            color: var(--success);
        }
        .thanks .btn { width: auto; margin-top: 28px; padding-left: 32px; padding-right: 32px; }
        footer {
            padding: 0 16px 24px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }
        @media (max-width: 640px) {
            .card { padding: 28px 20px; border-radius: 16px; }
            form { grid-template-columns: 1fr; }
            main { padding-top: 24px; }
        }
    </style>
</head>
<body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TDJJXH8X" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<header class="topbar">
    <a class="logo" href="<?= $e($appUrl) ?>">
        <img src="<?= $e($appUrl) ?>/public/assets/site/digifyce-logo-dark.png" alt="Digifyce" width="295" height="120">
    </a>
</header>

<main>
    <div class="card">
        <?php if ($submitted): ?>
        <div class="thanks">
            <div class="check" aria-hidden="true">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <h1>Thank you!</h1>
            <p class="lead">We've received your details. Our team will connect with you shortly to discuss how we can grow your brand's sales.</p>
            <a class="btn" href="<?= $e($appUrl) ?>">Visit Digifyce</a>
        </div>
        <?php else: ?>
        <div class="intro">
            <h1>Tell us a little about your D2C brand</h1>
            <p class="lead">Our team will connect with you to discuss potential opportunities to improve product sales.</p>
        </div>

        <?php if ($errorMessage): ?>
        <div class="alert" role="alert"><?= $e($errorMessage) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $e($appUrl) ?>/d2c-enquiry" id="d2cEnquiryForm">
            <div class="hp" aria-hidden="true">
                <label>Leave this empty <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
            </div>

            <div>
                <label for="full_name">Full Name <span class="req">*</span></label>
                <input id="full_name" name="full_name" required maxlength="120" autocomplete="name"
                    value="<?= $e($old['full_name']) ?>" placeholder="Your name">
            </div>

            <div>
                <label for="contact_number">Contact Number <span class="req">*</span></label>
                <input id="contact_number" type="tel" name="contact_number" required maxlength="20" autocomplete="tel"
                    pattern="\+?[\d\s\-()]{10,20}" title="Enter a valid phone number (at least 10 digits)"
                    value="<?= $e($old['contact_number']) ?>" placeholder="+91 98765 43210">
            </div>

            <div>
                <label for="brand_category">Brand Category <span class="req">*</span></label>
                <select id="brand_category" name="brand_category" required>
                    <option value="" disabled <?= $old['brand_category'] === '' ? 'selected' : '' ?>>Select a category</option>
                    <?php foreach ($categoryOptions as $category): ?>
                    <option value="<?= $e($category) ?>" <?= $old['brand_category'] === $category ? 'selected' : '' ?>><?= $e($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="location">Location <span class="req">*</span></label>
                <input id="location" name="location" required maxlength="120" autocomplete="address-level2"
                    value="<?= $e($old['location']) ?>" placeholder="City, State">
            </div>

            <div class="full">
                <label for="additional_details">Additional Details <span class="opt">(optional)</span></label>
                <textarea id="additional_details" name="additional_details" rows="5" maxlength="2000"
                    placeholder="Tell us about your products, current sales channels, or goals"><?= $e($old['additional_details']) ?></textarea>
            </div>

            <div class="full">
                <button type="submit" class="btn" id="d2cSubmit">Submit</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</main>

<footer>&copy; <?= date('Y') ?> Digifyce</footer>

<script>
// Prevent double submits while the sheet write is in progress.
(function () {
    var form = document.getElementById('d2cEnquiryForm');
    var btn = document.getElementById('d2cSubmit');
    if (!form || !btn) return;
    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.textContent = 'Submitting…';
    });
    // Re-enable it if the visitor comes back via the browser's back button.
    window.addEventListener('pageshow', function () {
        btn.disabled = false;
        btn.textContent = 'Submit';
    });
})();
</script>
</body>
</html>
