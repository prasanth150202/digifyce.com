<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
require_once __DIR__ . '/app/helpers/shopify_showcase.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'shopify-development');
$pageTitle = ($_seo['meta_title'] ?? '') ?: 'Shopify Development Company in India | Digifyce';
$pageDescription = ($_seo['meta_description'] ?? '') ?: 'Shopify development for scaling D2C brands in India: custom store builds, Shopify Plus development, store migrations, theme customization, and app integration.';
$extraHead = ($extraHead ?? '') . service_schema($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', 'shopify-development', 'Shopify Development', $pageTitle, $pageDescription);
$bodyClass = 'shopify-dev-page';
require_once __DIR__ . '/app/utilities/AppUrl.php';
$appUrl = AppUrl::resolve();
$leadUrl = $appUrl . '/leadform';
$shopifyLogo = $appUrl . '/public/assets/svg-logo/shopify-color-svgrepo-com.svg';
$wooLogo = $appUrl . '/public/assets/svg-logo/woocommerce-icon-svgrepo-com.svg';
// Tech-stack logos live in their own folder: index.php lists every file in
// public/assets/toolslogo/ on the home page, so they must not go there.
$techLogos = $appUrl . '/public/assets/tech-logos/';

// ─── Page copy ──────────────────────────────────────────────────────────────
$sdHero = [
    'eyebrow' => 'Shopify development company in India for D2C Brands',
    'sub'     => 'We build, migrate, and scale Shopify stores for funded D2C brands, taking you from a first custom build to a full Shopify Plus migration.',
    'cta'     => 'Build Your Website with Us',
];

// Colour presets the hero's theme-editor preview can switch between.
$sdThemes = [
    ['name' => 'Blue',     'accent' => '#0066ff', 'banner' => '#e6efff', 'ink' => '#0b1a3a', 'soft' => '#cddcfb'],
    ['name' => 'Green',    'accent' => '#008060', 'banner' => '#e3f1e8', 'ink' => '#0f3d2e', 'soft' => '#cfe6d8'],
    ['name' => 'Coral',    'accent' => '#d4543f', 'banner' => '#fbe8e3', 'ink' => '#4a1d16', 'soft' => '#f2d0c8'],
    ['name' => 'Charcoal', 'accent' => '#1f2328', 'banner' => '#efeee8', 'ink' => '#1f2328', 'soft' => '#dcdad1'],
];

$sdWho = [
    'heading' => 'Who This Is For',
    'text'    => 'As a Shopify development company for D2C brands, we work best with funded, scaling e-commerce businesses that are already doing real revenue. Most of our clients are looking to either launch a proper Shopify store for the first time, move off a platform that has started holding them back, or bring their store up to Shopify Plus. If that is roughly where your brand is, the process below is built around exactly that kind of project.',
    // The three situations named in the paragraph: clickable there, each one plays as a path.
    'fits'    => [
        ['phrase' => 'launch a proper Shopify store for the first time',       'tab' => 'add',      'from' => ['icon' => 'add',        'label' => 'Starting fresh'],   'to' => 'Shopify'],
        ['phrase' => 'move off a platform that has started holding them back', 'tab' => 'swap_horiz', 'from' => ['icon' => 'storefront', 'label' => 'Current platform'], 'to' => 'Shopify'],
        ['phrase' => 'bring their store up to Shopify Plus',                   'tab' => 'upgrade',  'from' => ['logo' => true,         'label' => 'Shopify'],          'to' => 'Shopify Plus'],
    ],
    'traits'  => ['Funded', 'Scaling', 'Doing real revenue'],
];

$sdHire = [
    'heading' => 'Hire Shopify Developers in India',
    'text'    => 'Digifyce is a Shopify development agency in India built around funded D2C brands, not one-off freelance projects. When you hire our Shopify developers, you get a dedicated team that handles architecture, integrations, and post-launch support under one roof, rather than juggling separate freelancers for design, development, and maintenance. Every engagement is scoped around your catalog, customer journey, and growth stage, so the store you get matches where your brand is actually headed.',
];

// The hero phone plays these four moments of a purchase.
$sdFlow = ['Browse', 'Add to cart', 'Checkout', 'Order placed'];
// Real screens from stores we built. The extra captures (phone views, a product page, the cart
// with one item) live in public/assets/img/shopify-dev/; the rest are the showcase screenshots.
// The hero plays one store's journey: home page, product page, cart, then "Order placed".
$sdHeroStore = [
    'domain' => 'hapliearth.com',
    'paths'  => [1 => '/products/buy-1-get-1-peanut-butter-muesli-250g', 2 => '/cart'],
    'desk'   => ['public/assets/img/shopify-showcase/hapli-earth.webp', 'public/assets/img/shopify-dev/hapli-earth-product.webp', 'public/assets/img/shopify-dev/hapli-earth-cart.webp'],
    'phone'  => ['public/assets/img/shopify-dev/hapli-earth-m-home.webp', 'public/assets/img/shopify-dev/hapli-earth-m-product.webp', 'public/assets/img/shopify-dev/hapli-earth-m-cart.webp'],
    'taps'   => [1 => [64.7, 91.9], 2 => [77.4, 71.9]],   // where "Add to cart" and "Check out" sit on the desktop screens, in %
];
$sdStoryShots = [
    'build' => ['domain' => 'coreeat.in', 'img' => 'public/assets/img/shopify-showcase/coreeat.webp'],
    'plus'  => ['l' => 'public/assets/img/shopify-showcase/seedtot.webp', 'c' => 'public/assets/img/shopify-showcase/mooie.webp', 'r' => 'public/assets/img/shopify-showcase/tirls.webp'],
];
$sdCardShots = [
    'store' => ['desk' => 'public/assets/img/shopify-showcase/bawse-baby.webp', 'phone' => 'public/assets/img/shopify-dev/bawse-baby-m-home.webp'],
    'plus'  => ['public/assets/img/shopify-showcase/aadhya.webp', 'public/assets/img/shopify-showcase/house-of-ko.webp', 'public/assets/img/shopify-showcase/ulag-natural.webp'],
];

// "Who this is for", act 2: the things a migration carries across (from the Store Migration copy), and their rows.
$sdMigRows = [28, 44, 60, 76];
$sdMigChips = [
    ['icon' => 'inventory_2', 'label' => 'Products'],
    ['icon' => 'receipt_long', 'label' => 'Order history'],
    ['icon' => 'group', 'label' => 'Customer data'],
    ['icon' => 'search', 'label' => 'SEO rankings'],
];
// act 3: what Shopify Plus adds (from the Shopify Plus Development copy)
$sdPlusPills = [
    ['icon' => 'storefront', 'label' => 'Multi-store', 'x' => 4, 'y' => 80],
    ['icon' => 'business_center', 'label' => 'B2B', 'x' => 25, 'y' => 80],
    ['icon' => 'inventory_2', 'label' => 'Wholesale', 'x' => 37, 'y' => 80],
    ['icon' => 'shopping_cart_checkout', 'label' => 'Checkout Extensibility', 'x' => 55, 'y' => 80],
];

// The Hire paragraph is told one sentence at a time; the text itself is untouched.
$sdHireBeats = preg_split('/(?<=[.!?])\s+/', $sdHire['text'], -1, PREG_SPLIT_NO_EMPTY);
// Diagram roles on a 100 x 86 grid: f = [left, top, width] as a freelancer, t = the same inside the team.
$sdHireRoles = [
    ['free' => 'Design',      'team' => 'Architecture',        'f' => [4, 6, 38],  't' => [8, 16, 50], 'wf' => [3, 4, 28],  'wt' => [4, 9.5, 30]],
    ['free' => 'Development', 'team' => 'Integrations',        'f' => [22, 19, 40], 't' => [8, 26, 50], 'wf' => [18, 13, 28], 'wt' => [4, 16.5, 30]],
    ['free' => 'Maintenance', 'team' => 'Post-launch support', 'f' => [6, 32, 38], 't' => [8, 36, 50], 'wf' => [6, 22, 28],  'wt' => [4, 23.5, 30]],
];
// kind: bars = a column chart by year; lift = a before-to-after curve up to the value.
// PROVISIONAL: the 2022-2025 figures in 'series' are placeholders that ramp up to the real totals (the last entry). Replace them with the real yearly numbers.
$sdYears = [2022, 2023, 2024, 2025, 2026];
$sdMetrics = [
    ['value' => 400, 'unit' => '+', 'label' => 'No of Website',             'kind' => 'bars', 'series' => [40, 110, 200, 300, 400]],
    ['value' => 100, 'unit' => '+', 'label' => 'No of Brands Handled',      'kind' => 'bars', 'series' => [10, 30, 55, 80, 100]],
    ['value' => 5,   'unit' => 'X', 'label' => 'Conversion Rate Increased', 'kind' => 'lift'],
    ['value' => 3,   'unit' => 'X', 'label' => 'AOV Increased',             'kind' => 'lift'],
];
$sdServicesHeading = 'What We Build: Shopify Development Services';
$sdServices = [
    ['art' => 'store',   'w' => 7, 'title' => 'Custom Shopify Store Development', 'text' => 'Ground-up store builds on the standard Shopify plan, covering theme, product architecture, checkout, and the right app stack for your brand. We start from your catalog and customer journey, not a generic template.'],
    ['art' => 'plus',    'w' => 5, 'title' => 'Shopify Plus Development',         'text' => 'Checkout Extensibility, custom scripts, B2B and wholesale setup, and multi-store configuration for brands operating at Plus scale. Useful once you are past the volume or complexity a standard plan can handle cleanly.'],
    ['art' => 'migrate', 'w' => 5, 'title' => 'Store Migration',                  'text' => 'Move from WooCommerce, Magento, or a custom platform to Shopify without losing SEO rankings, order history, or customer data. Redirect mapping and a monitored post-launch window are a standard part of the process.'],
    ['art' => 'theme',   'w' => 7, 'title' => 'Theme Customization',              'text' => 'Custom Liquid theme development and modification that goes beyond picking a template. We build the exact storefront your brand needs, down to its layout, interactions, and performance.'],
    ['art' => 'api',     'w' => 7, 'title' => 'App & API Integration',            'text' => 'Connect Shopify to your ERP, CRM, inventory, or marketplace tools through the Shopify API and app ecosystem, so your store stays connected to the rest of your operations rather than becoming a data island.'],
    ['art' => 'support', 'w' => 5, 'title' => 'Post-Launch Support',              'text' => 'Ongoing maintenance, app updates, and performance monitoring once your store goes live. We keep paying attention to the store after launch day, not just up to it.'],
];

$sdProcessHeading = 'Our Process: From Discovery to Launch';
$sdSteps = [
    ['num' => '01', 'icon' => 'travel_explore',  'title' => 'Discovery & Scoping',    'text' => 'We map your current store, or your requirements if starting fresh, along with your integrations and growth plans, to scope the build accurately. The estimate you get reflects the actual work, not a generic package price.'],
    ['num' => '02', 'icon' => 'design_services', 'title' => 'Design & Architecture',  'text' => 'Theme direction, product and collection structure, and app selection are all planned before a line of code is written, so the build phase does not stall on decisions that should have been made earlier.'],
    ['num' => '03', 'icon' => 'code',            'title' => 'Development',            'text' => 'Custom themes build or migration execution takes place in staging environments, so you can review progress throughout the project rather than seeing the store for the first time at launch.'],
    ['num' => '04', 'icon' => 'verified',        'title' => 'QA & Launch',            'text' => 'Cross-device testing, checkout verification, redirect mapping for migrations, and a monitored go-live, with someone actively watching the store through the first hours after launch.'],
    ['num' => '05', 'icon' => 'support_agent',   'title' => 'Post-Launch Support',    'text' => 'We stay on for fixes, app updates, and performance checks after launch. This is not a one-time handoff where the relationship ends the day the store goes live.'],
];

$sdTechHeading = 'Tech Stack';
// Shopify sits at the centre of the orbit; these circle around it. Shopify's
// own products (Plus, APIs, CLI, Storefront API) carry the Shopify logo.
$sdTechInner = [
    ['logos' => [$shopifyLogo],              'name' => 'Shopify Plus'],
    ['logos' => [$techLogos . 'liquid.png'], 'name' => 'Liquid'],
    ['logos' => [$shopifyLogo],              'name' => 'Shopify APIs'],
    ['logos' => [$shopifyLogo],              'name' => 'Shopify CLI'],
];
$sdTechOuter = [
    ['logos' => [$shopifyLogo],                'name' => 'Storefront API'],
    ['logos' => [$techLogos . 'razorpay.png', $techLogos . 'payu.png', $techLogos . 'cashfree.png'], 'name' => 'Razorpay / PayU / Cashfree'],
    ['logos' => [$techLogos . 'klaviyo.png'],  'name' => 'Klaviyo'],
    ['logos' => [$techLogos . 'judgeme.png'],  'name' => 'Judge.me'],
];

// Stores come from app/webContent/shopify-showcase.php; screenshots from
// tools/capture_showcase.php. A store without a screenshot yet is left out, and
// the whole section is hidden until at least one is ready.
$sdWorkHeading = "Shopify Stores We've Built";
$sdWorkText    = 'A few of the Shopify stores we have built and migrated for D2C brands. Scroll through a preview, or open the live store.';
$sdWork = array_values(array_filter(
    shopify_showcase_items(__DIR__),
    fn($store) => is_file(__DIR__ . '/' . $store['image'])
));

$sdFaqHeading = 'Frequently Asked Question';
$sdFaqs = [
    ['q' => 'How much does Shopify development cost in India?',                         'a' => 'Indian market rates for Shopify development typically range from around ₹20,000 for a basic store setup to ₹3,00,000 or more for a custom, feature-rich build, depending on theme complexity, integrations, and migration scope. The exact number depends on your specific requirements, so get a custom quote for an accurate figure.'],
    ['q' => 'Can you migrate our existing store to Shopify without losing SEO rankings?', 'a' => 'Yes. Migrations include 301 redirect mapping from your old URLs, metadata and structured data carryover, and a post-launch monitoring window to catch any indexing issues early.'],
    ['q' => 'Do we need Shopify Plus, or is standard Shopify enough?',                  'a' => 'Standard Shopify covers most growing D2C brands. Shopify Plus tends to become worth it once you are approaching $1M or more in annual revenue, or earlier if you need B2B or wholesale checkout, multiple expansion stores, or custom checkout scripting. We can help assess which fits your stage.'],
    ['q' => 'How long does a typical Shopify project take?',                            'a' => 'A standard custom store build launches in 7 days from discovery to go live. Migrations and Shopify Plus builds take longer depending on data volume and integration complexity. We scope a firm timeline during discovery.'],
    ['q' => 'Do you offer support after the store launches?',                           'a' => 'Yes. Post-launch support covering app updates, bug fixes, and performance checks is part of our process, not a separate add-on you have to negotiate for later.'],
];

$sdCta = [
    'heading' => "Let's Build Your Shopify Store.",
    'text'    => 'Tell us about your brand and what you are looking to build or migrate, and we will follow up with a scoped plan.',
    'btn'     => 'Get a Custom Quote for Your Website',
];

function sd_e($s): string {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Escapes $text and turns each of the given phrases into a clickable span that selects its story path. */
function sd_phrase_spans(string $text, array $phrases): string {
    $html = sd_e($text);
    foreach (array_values($phrases) as $i => $phrase) {
        $html = str_replace(sd_e($phrase), '<span class="sd-phrase" role="button" tabindex="0" aria-pressed="false" data-sd-path="' . $i . '">' . sd_e($phrase) . '</span>', $html);
    }
    return $html;
}

/** Section numbers ("01", "02", ...) in page order, so hiding a section never leaves a gap. */
function sd_next_index(): string {
    static $n = 0;
    return sprintf('%02d', ++$n);
}

// FAQPage structured data built from the same visible Q&A text.
$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(fn($f) => [
        '@type'          => 'Question',
        'name'           => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ], $sdFaqs),
];
$extraHead .= '<script type="application/ld+json">' . json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . '</script>';

$extraHead .= <<<'HTML'
<style>
	/* Colours match the other service pages (header.php, d2c-branding, brand-shoot):
	   navy-black background, #0d0f18 / #1a1d27 panels, electric-blue accent. */
	.sd-page {
		--ease: cubic-bezier(.16,1,.3,1);
		--mono: ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace;

		--bg: #05070a;
		--surface: #0d0f18;
		--surface-2: #1a1d27;
		--line: rgba(255,255,255,.08);
		--line-2: rgba(255,255,255,.14);
		--text: #ffffff;
		--body: #b9c6d8;
		--muted: #94a3b8;
		--accent: #0066ff;
		--accent-dark: #0052cc;
		--accent-text: #0080ff;   /* a touch lighter for small text on dark */
		--accent-soft: rgba(0,102,255,.12);

		/* Illustration details (service cards, store frames, orbit) */
		--art-panel: #1a1d27;
		--art-bar: rgba(255,255,255,.16);
		--art-bar-strong: rgba(255,255,255,.62);
		--art-line: rgba(255,255,255,.3);
		--art-grid: rgba(255,255,255,.035);

		background: var(--bg);
		color: var(--text);
		overflow-x: clip;
	}
	/* Every other section sits on a slightly deeper shade for rhythm */
	.sd-alt { --bg: #020617; }

	.sd-page .material-symbols-outlined {
		display: inline-block;
		width: 1em;
		height: 1em;
		line-height: 1;
		overflow: hidden;
		flex: none;
	}

	.sd-wrap { max-width: 1440px; margin: 0 auto; padding: 0 16px; position: relative; }
	@media (min-width: 640px) { .sd-wrap { padding: 0 24px; } }
	@media (min-width: 1024px) { .sd-wrap { padding: 0 32px; } }

	.sd-section { position: relative; padding: 7rem 0; background: var(--bg); color: var(--text); }
	.sd-hero + .sd-section, .sd-section + .sd-section { border-top: 1px solid rgba(255,255,255,.05); }
	@media (max-width: 768px) { .sd-section { padding: 4.25rem 0; } }

	/* ─── Shared type ─────────────────────────────── */
	.sd-label {
		margin: 0;
		line-height: 1.4;
		display: inline-flex;
		align-items: center;
		gap: .6rem;
		font-size: .72rem;
		font-weight: 700;
		letter-spacing: .18em;
		text-transform: uppercase;
		color: var(--accent-text);
	}
	.sd-label img { width: 20px; height: 22px; }
	.sd-index {
		display: flex;
		align-items: center;
		gap: .9rem;
		margin-bottom: 1.3rem;
		font-family: var(--mono);
		font-size: .72rem;
		letter-spacing: .24em;
		color: var(--accent-text);
	}
	.sd-index::after { content: ''; width: 3rem; height: 1px; background: var(--line-2); }
	.sd-h2 { font-size: clamp(2rem, 4.2vw, 3.4rem); line-height: 1.06; font-weight: 700; letter-spacing: -.04em; color: var(--text); }
	.sd-copy { color: var(--body); font-size: clamp(1rem, 1.3vw, 1.1rem); line-height: 1.8; }
	.sd-hl { color: var(--accent-text); font-weight: 600; }
	.sd-sec-head { max-width: 52rem; margin-bottom: 3.5rem; }
	@media (max-width: 720px) { .sd-sec-head { margin-bottom: 2.5rem; } }

	/* ─── Buttons (square corners like the other service pages) ─── */
	.sd-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: .65rem;
		min-height: 48px;
		padding: 14px 30px;
		border-radius: 4px;
		font-weight: 700;
		font-size: .92rem;
		letter-spacing: .02em;
		text-decoration: none;
		transition: transform .25s ease, background-color .25s ease;
	}
	.sd-btn svg { flex: none; transition: transform .25s ease; }
	.sd-btn:hover svg { transform: translateX(4px); }
	.sd-btn-primary { background: var(--accent); color: #fff; }
	.sd-btn-primary:hover { background: var(--accent-dark); transform: translateY(-2px); }
	.sd-btn-white { background: #fff; color: var(--accent); }
	.sd-btn-white:hover { background: #eef7f1; transform: translateY(-2px); }
	@media (max-width: 640px) { .sd-btn { width: 100%; } }

	/* ─── Reveal ──────────────────────────────────── */
	.sd-js [data-sd-reveal] {
		opacity: 0;
		transform: translateY(24px);
		transition: opacity .75s var(--ease), transform .75s var(--ease);
		transition-delay: var(--d, 0s);
	}
	.sd-js [data-sd-reveal].is-in { opacity: 1; transform: none; }

	/* ═══ THE STORY: hero, a sideways film, a sticky story, a chart ═══ */

	/* ── Hero: headline on top, a phone that plays a purchase, steps you can click ── */
	.sd-hero {
		position: relative;
		overflow: hidden;
		padding: 8.5rem 0 4.5rem;
		background:
			radial-gradient(55% 60% at 82% 16%, rgba(0,102,255,.32), transparent 72%),
			radial-gradient(50% 55% at 10% 88%, rgba(0,82,204,.26), transparent 72%),
			linear-gradient(180deg, #05070a 0%, #041029 100%);
		color: var(--text);
	}
	.sd-hero::before {
		content: '';
		position: absolute;
		inset: 0;
		background-image:
			linear-gradient(rgba(0,102,255,.04) 1px, transparent 1px),
			linear-gradient(90deg, rgba(0,102,255,.04) 1px, transparent 1px);
		background-size: 72px 72px;
		pointer-events: none;
	}
	.sd-hero-bg { position: absolute; left: 50%; bottom: -.06em; z-index: 0; transform: translateX(-50%); font-size: clamp(8rem, 27vw, 28rem); font-weight: 900; line-height: .8; letter-spacing: -.05em; color: rgba(255,255,255,.035); white-space: nowrap; pointer-events: none; user-select: none; }
	.sd-hero-top { position: relative; z-index: 1; max-width: 66rem; margin: 0 auto; text-align: center; }
	.sd-hero-top .sd-label { justify-content: center; }
	.sd-bag { filter: brightness(0) invert(1); }   /* the Shopify bag in plain white: no brand green in the hero */
	.sd-h1 { margin-top: 1.3rem; margin-bottom: 0; font-size: clamp(2.6rem, 6.6vw, 6.2rem); line-height: 1; font-weight: 800; letter-spacing: -.048em; color: var(--text); }
	.sd-h1-line { display: block; text-wrap: balance; }
	.sd-h1 .sd-accent { color: var(--accent); }
	.sd-hero-stage { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1fr) clamp(360px, 38vw, 560px) minmax(0, 1fr); gap: 2.4rem; align-items: center; margin-top: .5rem; }
	.sd-lead { max-width: 26rem; color: #b9c6d8; font-size: clamp(1rem, 1.3vw, 1.15rem); line-height: 1.75; }
	.sd-hero-cta { display: flex; flex-wrap: wrap; gap: .8rem; margin-top: 1.6rem; }
	.sd-btn-ghost { border: 1px solid var(--line-2); background: rgba(255,255,255,.03); color: var(--text); }
	.sd-btn-ghost:hover { border-color: var(--accent); background: var(--accent-soft); transform: translateY(-2px); }
	.sd-btn-ghost svg { transition: transform .25s ease; }
	.sd-btn-ghost:hover svg { transform: translateY(3px); }

	.sd-phone-wrap { position: relative; display: grid; place-items: center; perspective: 1400px; }
	/* a soft blue glow behind the computer and phone */
	.sd-phone-wrap::before { content: ''; position: absolute; left: 50%; top: 50%; z-index: -1; width: min(120%, 760px); aspect-ratio: 16 / 10; transform: translate(-50%, -50%); background: radial-gradient(closest-side, rgba(0,102,255,.30), rgba(0,82,204,.10) 55%, transparent); pointer-events: none; }
	/* A computer and a phone showing the same store, side by side (design size 560 x 400, scaled with zoom) */
	.sd-devs { position: relative; width: 560px; height: 400px; zoom: var(--pz, 1); }
	.sd-desk { position: absolute; left: 0; top: 0; z-index: 1; isolation: isolate; width: 470px; height: 326px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--line-2); border-radius: 12px; background: #0a0e17; box-shadow: 0 60px 90px -50px rgba(0,0,0,.95); }
	.sd-desk-bar { display: flex; align-items: center; gap: .4rem; flex: none; padding: .55rem .8rem; border-bottom: 1px solid var(--line); }
	.sd-desk-bar > i { width: .55rem; height: .55rem; border-radius: 50%; background: var(--art-bar); }
	.sd-desk-url { display: flex; align-items: center; gap: .35rem; flex: 1; min-width: 0; margin-left: .6rem; padding: .22rem .7rem; overflow: hidden; border-radius: 4px; background: rgba(255,255,255,.05); color: var(--muted); font-family: var(--mono); font-size: 10.5px; white-space: nowrap; }
	.sd-desk-url .material-symbols-outlined { font-size: 12px; }
	.sd-url-t { overflow: hidden; text-overflow: ellipsis; }
	.sd-url-p { display: none; color: #6b7a92; }
	.sd-desk[data-step="1"] .sd-url-p--1,
	.sd-desk[data-step="2"] .sd-url-p--2,
	.sd-desk[data-step="3"] .sd-url-p--2 { display: inline; }
	.sd-desk-body { position: relative; flex: 1; overflow: hidden; background: #fff; color: #0b1a3a; }
	.sd-devs .sd-phone { position: absolute; right: 0; bottom: 0; z-index: 2; zoom: .68; }
	.sd-phone {
		--rx: 0deg;
		--ry: 0deg;
		position: relative;
		width: 252px;
		aspect-ratio: 9 / 18.2;
		padding: 10px;
		border: 1px solid var(--line-2);
		border-radius: 42px;
		background: #0a0e17;
		box-shadow: 0 70px 110px -55px rgba(0,0,0,.95), inset 0 0 0 1px rgba(255,255,255,.04);
		transform: rotateY(var(--ry)) rotateX(var(--rx));
		transition: transform .45s var(--ease);
	}
	.sd-phone-notch { position: absolute; top: 10px; left: 50%; z-index: 6; width: 88px; height: 22px; margin-left: -44px; border-radius: 0 0 14px 14px; background: #0a0e17; }
	.sd-ps { position: relative; height: 100%; overflow: hidden; border-radius: 32px; background: #000; color: #0b1a3a; }
	.sd-rail button:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 2px; }

	/* Real screens from a store we built, one per step. They cross-fade, and the home page
	   scrolls while you "browse". (Screens: public/assets/img/shopify-dev/) */
	.sd-shot { position: absolute; left: 0; right: 0; top: 0; opacity: 0; transition: opacity .45s ease; }
	.sd-phone .sd-shot { top: 22px; }   /* below the status bar */
	.sd-shot img { display: block; width: 100%; max-width: none; height: auto; }
	.sd-shot--0 img { transform: translateY(var(--browse, -16%)); }
	.sd-phone .sd-shot--0 { --browse: -22%; }
	[data-step="0"] .sd-shot--0,
	[data-step="1"] .sd-shot--1,
	[data-step="2"] .sd-shot--2,
	[data-step="3"] .sd-shot--2 { opacity: 1; }
	[data-step="0"] .sd-shot--0 img { animation: sdBrowse 2.2s ease-in-out .45s both; }
	@keyframes sdBrowse { from { transform: none; } }
	/* a press on the real "Add to cart" / "Check out" button */
	.sd-tap { position: absolute; width: 36px; height: 36px; margin: -18px 0 0 -18px; border: 2px solid var(--accent); border-radius: 50%; background: rgba(0,102,255,.2); opacity: 0; pointer-events: none; }
	[data-step="1"] .sd-shot--1 .sd-tap,
	[data-step="2"] .sd-shot--2 .sd-tap { animation: sdTap 1.1s ease-out .3s both; }
	@keyframes sdTap { 0% { opacity: 0; transform: scale(.4); } 30% { opacity: 1; transform: scale(1); } 100% { opacity: 0; transform: scale(1.8); } }
	.sd-toast { position: absolute; z-index: 3; top: 18%; right: 4%; display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .85rem .5rem .6rem; border-radius: 8px; background: #0b1a3a; color: #fff; font-size: 12px; font-weight: 700; white-space: nowrap; box-shadow: 0 14px 30px -12px rgba(0,0,0,.55); opacity: 0; transform: translateY(-8px); transition: opacity .3s ease, transform .4s var(--ease); pointer-events: none; }
	.sd-toast .material-symbols-outlined { font-size: 16px; color: #5ea1ff; }
	[data-step="1"] .sd-toast { opacity: 1; transform: none; transition-delay: .75s; }
	.sd-desk .sd-toast { right: auto; left: 30%; }   /* clear of the phone, which covers the right edge */
	.sd-phone .sd-toast { top: 12%; left: 50%; right: auto; font-size: 13px; transform: translate(-50%, -8px); }
	.sd-phone[data-step="1"] .sd-toast { transform: translate(-50%, 0); }
	.sd-done { position: absolute; inset: 0; z-index: 4; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .8rem; background: rgba(245,247,251,.9); opacity: 0; transition: opacity .45s ease; pointer-events: none; }
	[data-step="3"] .sd-done { opacity: 1; }
	.sd-ps-ok { position: relative; display: grid; place-items: center; width: 84px; height: 84px; border-radius: 50%; background: var(--accent); color: #fff; transform: scale(.6); transition: transform .5s var(--ease); }
	[data-step="3"] .sd-ps-ok { transform: none; }
	.sd-desk .sd-ps-ok { width: 62px; height: 62px; }
	.sd-ps-ok .material-symbols-outlined { font-size: 2.6rem; }
	.sd-desk .sd-ps-ok .material-symbols-outlined { font-size: 2rem; }
	.sd-ps-done { font-size: 1.05rem; font-weight: 800; color: #0b1a3a; }
	.sd-next { position: absolute; inset: 0; z-index: 5; width: 100%; height: 100%; padding: 0; border: 0; background: transparent; cursor: pointer; }
	.sd-next:focus-visible { outline: 2px solid var(--accent-text); outline-offset: -3px; border-radius: inherit; }
	.sd-conf { position: absolute; left: 50%; top: 44%; width: 8px; height: 8px; margin: -4px; border-radius: 2px; background: var(--accent); opacity: 0; }
	.sd-phone[data-step="3"] .sd-conf { animation: sdConf 1.4s var(--ease) both; animation-delay: calc(var(--i) * 40ms); }
	@keyframes sdConf { 0% { opacity: 1; transform: translate(0, 0) rotate(0); } 100% { opacity: 0; transform: translate(var(--cx), var(--cy)) rotate(260deg); } }

	.sd-rail { display: grid; gap: .5rem; list-style: none; margin: 0; padding: 0; }
	.sd-rail button { display: flex; align-items: center; gap: 1rem; width: 100%; min-height: 54px; padding: .8rem 1rem; border: 1px solid var(--line); border-radius: 12px; background: transparent; color: var(--muted); font-size: .95rem; font-weight: 600; text-align: left; cursor: pointer; transition: color .3s, border-color .3s, background-color .3s; }
	.sd-rail button:hover { color: var(--text); }
	.sd-rail-n { font-family: var(--mono); font-size: .72rem; letter-spacing: .2em; }
	.sd-rail button[aria-pressed="true"] { border-color: var(--accent); background: var(--accent-soft); color: var(--text); }
	.sd-rail button[aria-pressed="true"] .sd-rail-n { color: var(--accent-text); }

	@media (max-width: 1100px) {
		.sd-hero-stage { grid-template-columns: minmax(0, 1fr); gap: 2rem; }
		.sd-phone-wrap { min-width: 0; }
		.sd-hero-side--l { text-align: center; }
		.sd-lead { margin: 0 auto; }
		.sd-hero-cta { justify-content: center; }
		.sd-rail { grid-template-columns: repeat(4, minmax(0, 1fr)); }
		.sd-rail button { flex-direction: column; align-items: flex-start; gap: .4rem; }
	}
	@media (max-width: 760px) {
		/* a touch quicker transitions between the steps on phones */
		.sd-shot, .sd-done { transition-duration: .32s; }
		[data-step="0"] .sd-shot--0 img { animation-duration: 1.5s; animation-delay: .3s; }
		/* Phones: the whole hero fits one screen. The computer and the phone are both shown, scaled to the space left. */
		.sd-hero { display: flex; height: 100vh; height: 100svh; min-height: 560px; padding: 5.7rem 0 1rem; box-sizing: border-box; }
		.sd-hero > .sd-wrap { display: flex; flex-direction: column; width: 100%; min-height: 0; }
		.sd-h1 { margin-top: .6rem; font-size: clamp(1.8rem, min(8.6vw, 5.4svh), 2.6rem); }
		.sd-hero-stage { flex: 1; min-height: 0; grid-template-rows: auto minmax(0, 1fr); gap: .8rem; margin-top: .6rem; }
		.sd-lead { font-size: .95rem; line-height: 1.55; }
		.sd-hero-cta { flex-direction: column; gap: .5rem; margin-top: .9rem; }
		.sd-hero-cta .sd-btn { width: 100%; min-height: 46px; padding: 11px 22px; }
		.sd-phone-wrap { order: 2; height: 100%; min-height: 0; }
		.sd-hero-side--r { display: none; }
	}

	/* Desktop: the whole hero fits one screen. The headline scales with the window height,
	   and the phone takes what is left (JS sets --pz so it shrinks on short windows). */
	@media (min-width: 1101px) and (min-height: 560px) {
		.sd-hero { display: flex; height: 100vh; height: 100svh; min-height: 560px; padding: 6.6rem 0 1.4rem; box-sizing: border-box; }
		.sd-hero > .sd-wrap { display: flex; flex-direction: column; width: 100%; min-height: 0; }
		.sd-hero .sd-h1 { margin-top: .7rem; font-size: clamp(2.2rem, min(5.4vw, 9svh), 5rem); }
		.sd-hero-stage { flex: 1; min-height: 0; margin-top: .4rem; grid-template-rows: minmax(0, 1fr); }
		.sd-phone-wrap { height: 100%; }
		.sd-lead { font-size: clamp(.95rem, min(1.3vw, 2.1svh), 1.15rem); }
		.sd-rail button { min-height: clamp(44px, 6.4svh, 54px); }
	}

	/* ── Chapter 1 · Who this is for: a film that scrolls sideways ── */
	.sd-film { position: relative; background: var(--bg); color: var(--text); border-top: 1px solid rgba(255,255,255,.05); }
	.sd-film + .sd-hire { border-top: 1px solid rgba(255,255,255,.05); }
	.sd-film-pin { position: relative; }
	.sd-film-track { display: block; }
	.sd-panel { display: flex; align-items: center; padding: 5rem 0; }
	.sd-panel + .sd-panel { border-top: 1px solid var(--line); }
	.sd-panel > .sd-wrap { width: 100%; }
	.sd-film-bar { display: none; }

	.sd-intro { max-width: 60rem; }
	.sd-intro .sd-h2 { font-size: clamp(2.6rem, 6.4vw, 6rem); letter-spacing: -.05em; }
	.sd-intro .sd-copy { margin-top: 1.8rem; font-size: clamp(1.05rem, 1.7vw, 1.4rem); line-height: 1.75; }
	.sd-hint { display: inline-flex; align-items: center; gap: .6rem; margin-top: 2rem; font-size: .72rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
	.sd-hint .material-symbols-outlined { font-size: 1.1rem; color: var(--accent-text); animation: sdNudge 1.6s ease-in-out infinite; }
	@keyframes sdNudge { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(6px); } }
	.sd-fit-cap { margin-top: 2rem; font-size: .7rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); }
	.sd-fit-list { display: flex; flex-wrap: wrap; gap: .6rem 1.5rem; margin-top: .7rem; }
	.sd-fit-list li { display: flex; align-items: center; gap: .45rem; font-size: .95rem; font-weight: 600; color: var(--text); }
	.sd-fit-list .material-symbols-outlined { font-size: 1.15rem; color: var(--accent); }

	.sd-act-grid { display: grid; grid-template-columns: minmax(0, 4fr) minmax(0, 8fr); gap: 3.5rem; align-items: center; }
	.sd-act-n { font-size: clamp(6rem, 13vw, 12rem); font-weight: 800; line-height: .82; letter-spacing: -.06em; color: transparent; -webkit-text-stroke: 1.5px rgba(255,255,255,.28); }
	.sd-act-t { margin-top: 1.4rem; font-size: clamp(1.6rem, 2.8vw, 2.5rem); font-weight: 700; line-height: 1.12; letter-spacing: -.03em; color: var(--text); }
	.sd-act-t::before { content: ''; display: block; width: 3rem; height: 3px; margin-bottom: 1.2rem; background: var(--accent); }

	@media (min-width: 1025px) and (min-height: 720px) and (prefers-reduced-motion: no-preference) {
		.sd-js .sd-film { height: calc(100vh + 400vh); height: calc(100svh + 400svh); }
		.sd-js .sd-film-pin { position: sticky; top: 0; overflow: hidden; height: 100vh; height: 100svh; padding-top: 6rem; box-sizing: border-box; }
		.sd-js .sd-film-track { display: flex; height: 100%; transform: translateX(calc(var(--s, 0) * -100%)); will-change: transform; }
		.sd-js .sd-panel { flex: 0 0 100%; padding: 0 0 4.5rem; border-top: 0 !important; }
		.sd-js .sd-film-bar { position: absolute; left: 0; right: 0; bottom: 1.3rem; z-index: 3; display: block; }
		.sd-film-bar .sd-wrap { display: flex; align-items: center; gap: 1.2rem; }
		.sd-film-prog { flex: 1; height: 2px; background: var(--line-2); }
		.sd-film-prog i { display: block; height: 100%; width: 0; background: var(--accent); }
		.sd-film-pips { display: flex; gap: .4rem; }
		.sd-pip { min-width: 46px; min-height: 36px; padding: 0 .6rem; border: 1px solid var(--line-2); border-radius: 6px; background: transparent; color: var(--muted); font-family: var(--mono); font-size: .72rem; letter-spacing: .14em; cursor: pointer; transition: color .3s, background-color .3s, border-color .3s; }
		.sd-pip:hover { color: var(--text); }
		.sd-pip[aria-pressed="true"] { border-color: var(--accent); background: var(--accent); color: #fff; }
		.sd-pip:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 2px; }
	}
	@media (max-width: 1024px) {
		.sd-act-grid { grid-template-columns: 1fr; gap: 1.8rem; }
		.sd-act-n { font-size: 5.5rem; }
		.sd-panel { padding: 4rem 0; }
	}

	/* Not pinned (phones, tablets, short windows): the acts become a swipeable slider */
	.sd-acts { display: contents; }
	.sd-acts-dots { display: none; }
	@media not all and (min-width: 1025px) and (min-height: 720px) and (prefers-reduced-motion: no-preference) {
		.sd-acts { position: relative; display: flex; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; scrollbar-width: none; padding-bottom: 2px; }
		.sd-acts::-webkit-scrollbar { display: none; }
		.sd-acts .sd-panel { flex: 0 0 88%; scroll-snap-align: center; padding: 1.5rem 0 2rem; border-top: 0 !important; }
		.sd-acts .sd-panel .sd-wrap { padding: 0 .5rem; }
		.sd-acts .sd-act-n { font-size: 4.2rem; }
		.sd-acts .sd-act-t { margin-top: .8rem; font-size: clamp(1.3rem, 5vw, 1.8rem); }
		.sd-acts-dots { display: flex; justify-content: center; gap: .5rem; padding: 0 0 3rem; }
	}

	/* Scenes are driven by --a (0 to 1): scroll fills it, and every part fades or slides in by its own slice of it */
	.sd-scn {
		--a: 0;
		container-type: inline-size;
		position: relative;
		width: 100%;
		aspect-ratio: 16 / 10;
		overflow: hidden;
		border: 1px solid var(--line);
		border-radius: 16px;
		background:
			radial-gradient(75% 85% at 88% 6%, rgba(0,102,255,.28), transparent 62%),
			radial-gradient(60% 70% at 6% 100%, rgba(0,82,204,.18), transparent 70%),
			linear-gradient(var(--art-grid) 1px, transparent 1px) 0 0 / 30px 30px, linear-gradient(90deg, var(--art-grid) 1px, transparent 1px) 0 0 / 30px 30px, var(--surface);
	}
	.sd-k { --k: clamp(0, calc((var(--a) - var(--ks)) / var(--kl)), 1); }

	/* Act 1 · a store builds itself */
	.sd-b1 { position: absolute; left: 8%; right: 8%; top: 9%; bottom: 9%; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--line-2); border-radius: 12px; background: #0b111d; }
	.sd-b1-bar { display: flex; align-items: center; gap: .4rem; flex: none; padding: .55rem .8rem; border-bottom: 1px solid var(--line); }
	.sd-b1-bar > i { width: .5rem; height: .5rem; border-radius: 50%; background: var(--art-bar); }
	.sd-b1-url { flex: 1; margin-left: .5rem; padding: .22rem .6rem; border-radius: 4px; background: rgba(255,255,255,.05); color: var(--muted); font-family: var(--mono); font-size: clamp(8px, 1.5cqw, 12px); white-space: nowrap; overflow: hidden; }
	.sd-b1-body { position: relative; flex: 1; min-height: 0; overflow: hidden; }
	/* five horizontal slices of one real home page; each slides in on its own slice of the scroll */
	.sd-band { position: absolute; left: 0; right: 0; top: calc(var(--b) * 20%); height: 20%; overflow: hidden; }
	.sd-band::before { content: ''; position: absolute; inset: 7% 3%; border: 1.5px dashed var(--line-2); border-radius: 8px; opacity: calc(1 - var(--k)); }
	.sd-band img { position: absolute; left: 0; top: calc(var(--b) * -100%); width: 100%; max-width: none; height: auto; opacity: var(--k); transform: translateY(calc((1 - var(--k)) * 16px)); }
	.sd-live { position: absolute; right: 4%; top: 5%; z-index: 3; display: inline-flex; align-items: center; gap: .45rem; padding: .35rem .8rem; border-radius: 999px; background: var(--accent); color: #fff; font-size: clamp(9px, 1.7cqw, 13px); font-weight: 700; opacity: var(--k); transform: scale(calc(.7 + .3 * var(--k))); }
	.sd-live i { width: .45rem; height: .45rem; border-radius: 50%; background: #fff; }

	/* Act 2 · everything moves across */
	.sd-mbox { position: absolute; top: 14%; bottom: 10%; width: 30%; border: 1px solid var(--line-2); border-radius: 14px; background: rgba(255,255,255,.02); }
	.sd-mbox--old { left: 4%; opacity: calc(1 - .55 * var(--k)); filter: grayscale(calc(var(--k))); }
	.sd-mtag { position: absolute; left: 0; right: 0; top: 4%; display: flex; align-items: center; justify-content: center; gap: .5rem; font-size: clamp(9px, 1.7cqw, 13px); font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
	/* the destination: a real product page on the new Shopify store. It starts grey and faded,
	   and fills with colour as the data arrives */
	.sd-mtag--shop { left: 40%; right: 4%; top: 9%; color: var(--accent-text); }
	.sd-mweb { position: absolute; left: 40%; right: 4%; top: 18%; display: flex; flex-direction: column; overflow: hidden; border: 1px solid rgba(0,102,255,.6); border-radius: 12px; background: #0b111d; box-shadow: 0 28px 50px -26px rgba(0,0,0,.8); }
	.sd-mweb img { display: block; width: 100%; max-width: none; height: auto; filter: grayscale(calc(1 - var(--k))) blur(calc((1 - var(--k)) * 2px)); opacity: calc(.3 + .7 * var(--k)); }
	/* small scenes (phones): a slimmer address bar, so the page and the redirects badge both fit */
	@container (max-width: 520px) {
		.sd-mtag--shop { top: 3%; left: 41%; right: 6%; }
		.sd-mweb { top: 11%; left: 41%; right: 6%; border-radius: 8px; }
		.sd-mweb .sd-b1-bar { padding: .3rem .45rem; gap: .25rem; }
		.sd-mweb .sd-b1-bar > i { width: .35rem; height: .35rem; }
		.sd-mpill { left: 67.5%; bottom: 2%; padding: .3rem .65rem; line-height: 1.1; }
	}
	.sd-mtag img { width: 1.4em; height: 1.6em; }
	.sd-slot { position: absolute; left: 10%; right: 10%; height: 12.5%; border: 1.5px dashed var(--line-2); border-radius: 9px; }
	.sd-mchip { position: absolute; z-index: 2; left: calc(4% + 2.4cqw); width: calc(30% - 4.8cqw); height: 12.5%; display: flex; align-items: center; gap: .5em; padding: 0 .8em; border: 1px solid var(--line-2); border-radius: 9px; background: var(--surface-2); color: var(--text); font-size: clamp(8px, 1.6cqw, 13px); font-weight: 700; white-space: nowrap; transform: translateX(calc(var(--k) * 40cqw)) scale(calc(1 - .25 * var(--k))); opacity: clamp(0, calc((1 - var(--k)) * 5), 1); }   /* each one is absorbed into the page as it arrives */
	.sd-mchip .material-symbols-outlined { font-size: 1.3em; color: var(--accent-text); }
	.sd-mchip.is-landed { border-color: var(--accent); }
	.sd-mpill { position: absolute; left: 68%; bottom: 5%; z-index: 3; display: inline-flex; align-items: center; gap: .4rem; padding: .4rem .9rem; border: 1px solid var(--accent); border-radius: 999px; background: var(--bg); color: var(--accent-text); font-size: clamp(9px, 1.7cqw, 13px); font-weight: 700; white-space: nowrap; opacity: var(--k); transform: translate(-50%, calc((1 - var(--k)) * 10px)); }
	.sd-mpill .material-symbols-outlined { font-size: 1.2em; }
	.sd-marrow { position: absolute; left: 35.5%; right: 61.5%; top: 49%; height: 2px; background: repeating-linear-gradient(90deg, var(--art-line) 0 6px, transparent 6px 12px); }
	.sd-marrow::after { content: ''; position: absolute; right: -2px; top: -5px; border-left: 8px solid var(--art-line); border-top: 6px solid transparent; border-bottom: 6px solid transparent; }

	/* Act 3 · one store becomes many */
	.sd-plabel { position: absolute; left: 5%; top: 6%; z-index: 4; font-size: clamp(11px, 2.2cqw, 18px); font-weight: 800; letter-spacing: -.01em; color: var(--text); }
	.sd-plabel span { position: absolute; left: 0; top: 0; white-space: nowrap; }
	.sd-plabel .sd-pl-a { opacity: calc(1 - var(--k)); }
	.sd-plabel .sd-pl-b { display: inline-flex; align-items: center; gap: .5rem; opacity: var(--k); }
	.sd-plabel .sd-pl-b b { display: grid; place-items: center; width: 1.5em; height: 1.5em; border-radius: 6px; background: var(--accent); color: #fff; font-size: .9em; line-height: 1; }
	.sd-pst { position: absolute; left: 33%; top: 24%; width: 34%; aspect-ratio: 4 / 3; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--line-2); border-radius: 10px; background: #fff; box-shadow: 0 24px 40px -22px rgba(0,0,0,.7); }
	.sd-pst--c { z-index: 3; }
	.sd-pst--l { z-index: 2; opacity: var(--k); transform: translateX(calc(var(--k) * -30cqw)) rotate(calc(var(--k) * -5deg)); }
	.sd-pst--r { z-index: 2; opacity: var(--k); transform: translateX(calc(var(--k) * 30cqw)) rotate(calc(var(--k) * 5deg)); }
	.sd-pst img { flex: 1; min-height: 0; width: 100%; max-width: none; object-fit: cover; object-position: top; }
	/* a slim browser bar on top of each real screen */
	.sd-win-bar { display: flex; flex: none; gap: 4px; padding: 5px 7px; background: #0a0e17; }
	.sd-win-bar i { width: 6px; height: 6px; border-radius: 50%; background: var(--art-bar); }
	.sd-ppill { position: absolute; display: inline-flex; align-items: center; gap: .4em; padding: .4em .85em; border: 1px solid var(--line-2); border-radius: 999px; background: var(--surface-2); color: var(--text); font-size: clamp(9px, 1.75cqw, 14px); font-weight: 700; white-space: nowrap; opacity: var(--k); transform: translateY(calc((1 - var(--k)) * 14px)); }
	.sd-ppill .material-symbols-outlined { font-size: 1.25em; color: var(--accent-text); }

	/* ── Chapter 2 · Hire: a diagram that changes as each sentence scrolls past ── */
	.sd-hire { position: relative; background: var(--bg); color: var(--text); padding: 6.5rem 0 4rem; }
	.sd-hire-grid { display: grid; grid-template-columns: minmax(0, 7fr) minmax(0, 5fr); gap: 4rem; align-items: start; }
	.sd-hstage { position: sticky; top: 7rem; z-index: 2; background: var(--bg); }
	.sd-hbox { --m: 0; --n: 0; container-type: inline-size; position: relative; width: 100%; aspect-ratio: 100 / 86; overflow: hidden; border: 1px solid var(--line); border-radius: 16px; background: linear-gradient(var(--art-grid) 1px, transparent 1px) 0 0 / 30px 30px, linear-gradient(90deg, var(--art-grid) 1px, transparent 1px) 0 0 / 30px 30px, var(--surface); }
	.sd-hsvg { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
	.sd-hl-loose { fill: none; stroke: var(--muted); stroke-width: 1.4; stroke-dasharray: 5 5; vector-effect: non-scaling-stroke; opacity: clamp(0, calc(1 - var(--m) * 2.2), 1); }
	.sd-hl-team { fill: none; stroke: var(--accent-text); stroke-width: 2.6; vector-effect: non-scaling-stroke; opacity: clamp(0, calc((var(--m) - .6) * 2.5), 1); }
	.sd-hl-arrow-m { fill: var(--muted); }
	.sd-hl-arrow { fill: var(--accent-text); }
	.sd-hpanel { position: absolute; left: 3%; top: calc(5 / 86 * 100%); width: 62%; height: calc(42 / 86 * 100%); border: 1.5px solid var(--accent); border-radius: 14px; background: rgba(0,102,255,.07); opacity: var(--m); transform: scale(calc(.94 + .06 * var(--m))); transform-origin: 30% 50%; }
	.sd-hcap { position: absolute; left: 6%; top: calc(9.5 / 86 * 100%); font-size: clamp(8px, 1.6cqw, 12px); font-weight: 700; letter-spacing: .16em; color: var(--accent-text); opacity: var(--m); }
	.sd-hrole {
		position: absolute;
		left: calc((var(--fx) * (1 - var(--m)) + var(--tx) * var(--m)) * 1%);
		top: calc((var(--fy) * (1 - var(--m)) + var(--ty) * var(--m)) / 86 * 100%);
		width: calc((var(--fw) * (1 - var(--m)) + var(--tw) * var(--m)) * 1%);
		height: calc(9 / 86 * 100%);
		display: grid;
		border: 1px solid var(--line-2);
		border-radius: 10px;
		background: var(--surface-2);
		font-size: clamp(10px, 2.5cqw, 15px);
		font-weight: 700;
		color: var(--text);
	}
	.sd-hlab { grid-area: 1 / 1; display: flex; align-items: center; gap: .6em; min-width: 0; padding: 0 .8em; white-space: nowrap; }
	.sd-hlab-f { color: #8a97ad; opacity: clamp(0, calc(1 - var(--m) * 1.7), 1); }
	.sd-hlab-t { opacity: clamp(0, calc((var(--m) - .4) * 1.8), 1); }
	.sd-hico { display: grid; place-items: center; flex: none; width: 1.8em; height: 1.8em; border-radius: 50%; }
	.sd-hlab-f .sd-hico { border: 1px solid var(--line-2); background: var(--bg); color: var(--muted); }
	.sd-hlab-t .sd-hico { background: var(--accent); color: #fff; }
	.sd-hico .material-symbols-outlined { font-size: 1.15em; }
	.sd-hstore { position: absolute; left: 72%; top: calc(10 / 86 * 100%); width: 24%; height: calc(32 / 86 * 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8%; border: 1px solid var(--line-2); border-radius: 14px; background: var(--art-panel); font-size: clamp(9px, 2.1cqw, 14px); font-weight: 700; text-align: center; }
	.sd-hstore img { width: 36%; height: auto; }
	.sd-hsvg--w { display: none; }
	.sd-hdots { display: flex; gap: .5rem; justify-content: center; margin-top: 1rem; }
	.sd-hdot { position: relative; width: 44px; height: 32px; padding: 0; border: 0; background: none; cursor: pointer; transition: width .3s; }
	.sd-hdot::before { content: ''; position: absolute; left: 0; right: 0; top: 50%; height: 8px; border-radius: 4px; background: var(--line-2); transform: translateY(-50%); transition: background-color .3s; }
	.sd-hdot.is-on { width: 64px; }
	.sd-hdot.is-on::before { background: var(--accent); }
	.sd-hdot:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 3px; }

	.sd-hire-text { padding-bottom: 12svh; }
	.sd-hire-head { padding-bottom: 3rem; }
	.sd-beat { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; gap: .9rem; min-height: 58svh; font-size: clamp(1.25rem, 2vw, 1.85rem); font-weight: 500; line-height: 1.5; letter-spacing: -.01em; color: var(--muted); transition: color .5s ease; }
	.sd-beat-n { flex: none; font-family: var(--mono); font-size: .72rem; letter-spacing: .2em; color: var(--muted); transition: color .5s; }
	.sd-beat.is-active { color: var(--text); }
	.sd-beat.is-active .sd-beat-n { color: var(--accent-text); }
	.sd-beat-copy { cursor: pointer; }

	@media (max-width: 1024px) {
		.sd-hire { padding: 4.25rem 0 2rem; }
		.sd-hire-grid { grid-template-columns: 1fr; gap: 0; }
		.sd-hire-text { display: contents; }
		.sd-hire-head { order: -2; padding-bottom: 1.5rem; }
		.sd-hstage { order: -1; top: 4.6rem; margin: 0 -16px; padding: .6rem 16px .6rem; border-bottom: 1px solid var(--line); }
		.sd-hbox { width: min(100%, calc(42svh * 100 / 86)); margin: 0 auto; }
		.sd-hdots { margin-top: .6rem; }
		.sd-beat { min-height: 44svh; font-size: clamp(1.15rem, 4.4vw, 1.5rem); }
	}

	/* Large screens: the section pins. Heading and one sentence at a time across the top,
	   a wide diagram below that reads left to right: people, then what they scope, then your store. */
	@media (min-width: 1025px) and (min-height: 720px) and (prefers-reduced-motion: no-preference) {
		.sd-js .sd-hire { height: calc(100vh + 300vh); height: calc(100svh + 300svh); padding: 0; }
		.sd-js .sd-hire-grid {
			position: sticky;
			top: 0;
			box-sizing: border-box;
			height: 100vh;
			height: 100svh;
			padding-top: 6.4rem;
			padding-bottom: 1.2rem;
			grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
			grid-template-rows: auto minmax(0, 1fr);
			grid-template-areas: "head beats" "stage stage";
			gap: 1.2rem 4rem;
			align-items: end;
		}
		.sd-js .sd-hire-text { display: contents; }
		.sd-js .sd-hire-head { grid-area: head; padding: 0; }
		.sd-js .sd-hire-head .sd-h2 { font-size: clamp(2rem, min(4vw, 6.4svh), 3.4rem); }
		.sd-js .sd-beat { grid-area: beats; align-self: end; min-height: 0; padding-bottom: .2rem; font-size: clamp(1.05rem, min(1.55vw, 2.5svh), 1.5rem); opacity: 0; transform: translateY(10px); transition: opacity .5s ease, transform .6s var(--ease), color .5s; }
		.sd-js .sd-beat.is-active { opacity: 1; transform: none; }
		.sd-js .sd-hstage { grid-area: stage; position: static; align-self: center; width: 100%; padding: 0; background: transparent; }
		.sd-js .sd-hbox { aspect-ratio: 100 / 32; width: min(100%, calc((100svh - 19.5rem) * 100 / 32)); margin: 0 auto; }
		.sd-js .sd-hsvg--w { display: block; }
		.sd-js .sd-hsvg--n { display: none; }
		.sd-js .sd-hpanel { left: 2%; top: calc(2 / 32 * 100%); width: 34%; height: calc(28 / 32 * 100%); }
		.sd-js .sd-hcap { left: 4%; top: calc(4.2 / 32 * 100%); font-size: clamp(9px, .9cqw, 12px); }
		.sd-js .sd-hrole {
			left: calc((var(--wfx) * (1 - var(--m)) + var(--wtx) * var(--m)) * 1%);
			top: calc((var(--wfy) * (1 - var(--m)) + var(--wty) * var(--m)) / 32 * 100%);
			width: calc((var(--wfw) * (1 - var(--m)) + var(--wtw) * var(--m)) * 1%);
			height: calc(5.6 / 32 * 100%);
			font-size: clamp(11px, 1.15cqw, 16px);
		}
		.sd-js .sd-hstore { left: 82%; top: calc(8 / 32 * 100%); width: 16%; height: calc(16 / 32 * 100%); font-size: clamp(10px, 1.1cqw, 15px); }
		.sd-js .sd-hdots { margin-top: .8rem; }
	}

	/* Third sentence: the store lights up and work flows to it */
	.sd-hstore { box-shadow: 0 0 0 calc(var(--n) * 3px) rgba(0,102,255,.55), 0 0 calc(var(--n) * 40px) rgba(0,102,255,.18); }
	.sd-hflow { display: none; }
	@media (min-width: 1025px) and (min-height: 720px) and (prefers-reduced-motion: no-preference) {
		.sd-js .sd-hflow { display: block; position: absolute; left: 40%; width: 38%; top: calc(16 / 32 * 100% - 4px); height: 8px; opacity: var(--n); pointer-events: none; }
		.sd-js .sd-hflow i { position: absolute; top: 0; left: 0; width: 8px; height: 8px; border-radius: 2px; background: var(--accent); animation: sdFlowPk 2.6s linear infinite; animation-delay: calc(var(--i) * -.65s); }
	}
	@keyframes sdFlowPk { 0% { left: 0; opacity: 0; } 12% { opacity: 1; } 88% { opacity: 1; } 100% { left: calc(100% - 8px); opacity: 0; } }

	/* ── The numbers: an interactive dashboard (pick a result, explore it) ── */
	.sd-dash { display: grid; grid-template-columns: minmax(0, 4fr) minmax(0, 8fr); gap: 1rem; }
	.sd-dtabs { display: grid; grid-auto-rows: 1fr; gap: .7rem; }
	.sd-dtab { position: relative; display: flex; flex-direction: column; justify-content: center; gap: .55rem; padding: 1.2rem 1.5rem; overflow: hidden; border: 1px solid var(--line); border-radius: 14px; background: var(--surface); color: var(--text); text-align: left; cursor: pointer; transition: border-color .3s, background-color .3s; }
	.sd-dtab:hover { border-color: var(--line-2); }
	.sd-dtab:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 2px; }
	.sd-dtab[aria-selected="true"] { border-color: var(--accent); background: var(--surface-2); }
	.sd-dtab-n { font-size: clamp(2rem, 3.2vw, 3.1rem); font-weight: 800; line-height: 1; letter-spacing: -.045em; font-variant-numeric: tabular-nums; color: var(--muted); transition: color .3s; }
	.sd-dtab[aria-selected="true"] .sd-dtab-n { color: var(--text); }
	.sd-dtab-unit { color: var(--accent); }
	.sd-dtab-l { font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
	.sd-dtab-fill { position: absolute; left: 0; bottom: 0; width: 0; height: 3px; background: var(--accent); }
	.sd-dtab[aria-selected="true"] .sd-dtab-fill { width: 100%; }
	.sd-dash.is-auto .sd-dtab[aria-selected="true"] .sd-dtab-fill { width: 0; animation: sdFill 6.5s linear forwards; }
	.sd-dash.is-paused .sd-dtab-fill { animation-play-state: paused; }

	.sd-dstage { position: relative; min-height: 460px; overflow: hidden; border: 1px solid var(--line); border-radius: 14px; background: linear-gradient(var(--art-grid) 1px, transparent 1px) 0 0 / 32px 32px, linear-gradient(90deg, var(--art-grid) 1px, transparent 1px) 0 0 / 32px 32px, var(--surface); }
	.sd-pane { position: absolute; inset: 0; display: flex; flex-direction: column; padding: 1.8rem 2rem 1.5rem; opacity: 0; visibility: hidden; transform: translateY(14px); transition: opacity .5s ease, transform .6s var(--ease), visibility 0s .5s; }
	.sd-pane.is-on { opacity: 1; visibility: visible; transform: none; transition-delay: 0s; }
	.sd-pane-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: .5rem 1.5rem; }
	.sd-pane-num { font-size: clamp(3.4rem, 6.4vw, 6.2rem); font-weight: 800; line-height: .9; letter-spacing: -.05em; font-variant-numeric: tabular-nums; }
	.sd-pane-unit { color: var(--accent); }
	.sd-pane-label { font-size: .78rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); }
	.sd-pane-body { position: relative; display: flex; flex: 1; flex-direction: column; min-height: 0; margin-top: 1rem; }

	/* Column charts by year (websites, brands) */
	.sd-cols { position: relative; flex: 1; min-height: 0; margin: 1.6rem 0 2.2rem 2.6rem; }
	.sd-gl { position: absolute; left: 0; right: 0; bottom: calc(var(--y) * 100%); border-top: 1px solid var(--line); pointer-events: none; }
	.sd-gl:first-of-type { border-top-color: var(--line-2); }
	.sd-gl span { position: absolute; right: calc(100% + .7rem); top: -.5em; font-family: var(--mono); font-size: .68rem; color: var(--muted); }
	.sd-cols-plot { position: absolute; inset: 0; display: flex; align-items: stretch; justify-content: space-around; }
	.sd-col { position: relative; flex: 1; display: flex; align-items: flex-end; justify-content: center; outline: none; cursor: pointer; }
	.sd-col::before { content: ; position: absolute; inset: 0 8%; border-radius: 10px; background: var(--accent-soft); opacity: 0; transition: opacity .25s; }
	.sd-col:hover::before, .sd-col:focus-visible::before { opacity: 1; }
	.sd-col-bar { position: relative; width: 52%; max-width: 92px; height: calc(var(--v) * 100%); min-height: 4px; border-radius: 8px 8px 0 0; background: var(--art-bar-strong); transform: scaleY(0); transform-origin: bottom; transition: transform 1.1s var(--ease), background-color .25s; transition-delay: calc(var(--i) * .1s + .15s), 0s; }
	.sd-col.is-last .sd-col-bar { background: var(--accent); }
	.sd-pane.is-on .sd-col-bar { transform: scaleY(1); }
	.sd-col:hover .sd-col-bar, .sd-col:focus-visible .sd-col-bar { background: var(--accent-text); transition-delay: 0s, 0s; }
	.sd-col-val { position: absolute; left: 50%; bottom: calc(var(--v) * 100%); margin-bottom: .55rem; font-size: .9rem; font-weight: 800; color: var(--text); white-space: nowrap; opacity: 0; transform: translate(-50%, 6px); transition: opacity .4s ease, transform .4s var(--ease); transition-delay: calc(var(--i) * .1s + 1s); }
	.sd-pane.is-on .sd-col-val { opacity: 1; transform: translate(-50%, 0); }
	.sd-col:not(.is-last) .sd-col-val { color: var(--body); font-weight: 700; }
	.sd-col:hover .sd-col-val, .sd-col:focus-visible .sd-col-val { color: #fff; transform: translate(-50%, -4px) scale(1.15); transition-delay: 0s; }
	.sd-col-yr { position: absolute; top: calc(100% + .7rem); left: 50%; transform: translateX(-50%); font-family: var(--mono); font-size: .8rem; letter-spacing: .08em; color: var(--muted); transition: color .25s; }
	.sd-col.is-last .sd-col-yr, .sd-col:hover .sd-col-yr { color: var(--text); }
	@media (max-width: 640px) {
		.sd-cols { margin-left: 2.2rem; }
		.sd-col-bar { width: 62%; }
		.sd-col-val { font-size: .78rem; }
	}

	/* Lift curves: drag the handle to scrub from before to after */
	.sd-lift { flex: 1; min-height: 0; width: 100%; height: 100%; overflow: visible; cursor: ew-resize; touch-action: pan-y; }
	.sd-lg { stroke: var(--line); stroke-width: 1; }
	.sd-lg--base { stroke: var(--line-2); stroke-dasharray: 4 5; }
	.sd-lt { fill: var(--muted); font-family: var(--mono); font-size: 10px; }
	.sd-lline { fill: none; stroke: var(--accent); stroke-width: 3; stroke-linecap: round; }
	.sd-larea { fill: url(#sdLiftFill); }
	.sd-lmark line { stroke: var(--line-2); stroke-dasharray: 3 4; }
	.sd-lmark circle { fill: #fff; stroke: var(--accent); stroke-width: 3; }
	.sd-lbub rect { fill: var(--accent); }
	.sd-lbub text { fill: #fff; font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 800; text-anchor: middle; }
	.sd-lend { fill: var(--accent-text); font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; text-anchor: end; }
	.sd-lctl { display: flex; align-items: center; gap: 1rem; margin-top: .8rem; color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
	.sd-lctl input { flex: 1; min-width: 0; height: 44px; margin: 0; background: transparent; cursor: pointer; -webkit-appearance: none; appearance: none; }
	.sd-lctl input::-webkit-slider-runnable-track { height: 6px; border-radius: 3px; background: linear-gradient(90deg, var(--accent) calc(var(--p, 100) * 1%), var(--line-2) 0); }
	.sd-lctl input::-moz-range-track { height: 6px; border-radius: 3px; background: var(--line-2); }
	.sd-lctl input::-moz-range-progress { height: 6px; border-radius: 3px; background: var(--accent); }
	.sd-lctl input::-webkit-slider-thumb { -webkit-appearance: none; width: 24px; height: 24px; margin-top: -9px; border: 3px solid var(--accent); border-radius: 50%; background: #fff; }
	.sd-lctl input::-moz-range-thumb { width: 18px; height: 18px; border: 3px solid var(--accent); border-radius: 50%; background: #fff; }
	.sd-lctl input:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 4px; border-radius: 4px; }

	@media (max-width: 900px) {
		.sd-dash { grid-template-columns: 1fr; }
		.sd-dtabs { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: auto; gap: .6rem; }
		.sd-dtab { padding: 1rem 1rem .95rem; }
		.sd-dstage { min-height: 430px; }
		.sd-pane { padding: 1.3rem 1.1rem 1.1rem; }
	}

	/* ═══ SERVICES BENTO ═══════════════════════════════ */
	.sd-bento { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 1rem; }
	.sd-card {
		grid-column: span 6;
		display: flex;
		flex-direction: column;
		overflow: hidden;
		border: 1px solid var(--line);
		border-radius: 12px;
		background: var(--surface);
	}
	.sd-card--w7 { grid-column: span 7; }
	.sd-card--w5 { grid-column: span 5; }
	.sd-card:hover { border-color: var(--accent); }
	.sd-js .sd-card[data-sd-reveal] { transition: opacity .75s var(--ease) var(--d, 0s), transform .75s var(--ease) var(--d, 0s), border-color .3s; }
	.sd-js .sd-card[data-sd-reveal].is-in:hover { transform: translateY(-3px); }
	.sd-card-art {
		position: relative;
		height: 210px;
		overflow: hidden;
		border-bottom: 1px solid var(--line);
		background:
			radial-gradient(70% 95% at 86% 0%, rgba(0,102,255,.28), transparent 64%),
			radial-gradient(55% 80% at 0% 100%, rgba(0,82,204,.16), transparent 70%),
			linear-gradient(var(--art-grid) 1px, transparent 1px) 0 0 / 28px 28px,
			linear-gradient(90deg, var(--art-grid) 1px, transparent 1px) 0 0 / 28px 28px,
			var(--surface-2);
	}
	.sd-card-art svg text { font-family: 'Space Grotesk', sans-serif; }
	.sd-card-body { padding: 1.6rem 1.7rem 1.9rem; }
	.sd-card-num { font-family: var(--mono); font-size: .7rem; letter-spacing: .24em; color: var(--accent-text); }
	.sd-card h4 { margin: .65rem 0 .75rem; font-size: clamp(1.3rem, 1.8vw, 1.55rem); font-weight: 700; letter-spacing: -.02em; line-height: 1.2; color: var(--text); }
	.sd-card p { color: var(--body); line-height: 1.75; font-size: .98rem; }

	/* Art: custom store (a real store on a computer and a phone; hovering scrolls both) */
	.sd-art-store { height: 100%; display: flex; align-items: center; justify-content: center; padding: 0 1rem; }
	.sd-mini { width: 62%; max-width: 380px; overflow: hidden; border: 1px solid var(--line-2); border-radius: 8px; background: #fff; box-shadow: 0 24px 40px -22px rgba(0,0,0,.7); }
	.sd-mini-view { height: 136px; overflow: hidden; }
	.sd-mini-phone { position: relative; z-index: 2; flex: none; width: 76px; height: 152px; margin: 2.6rem 0 0 -2.6rem; overflow: hidden; border: 4px solid #0a0e17; border-radius: 14px; background: #fff; box-shadow: 0 20px 34px -16px rgba(0,0,0,.75); }
	.sd-mini-view img, .sd-mini-phone img { display: block; width: 100%; max-width: none; height: auto; transition: transform 2.8s ease-in-out; }
	.sd-card:hover .sd-mini-view img { transform: translateY(-34%); }
	.sd-card:hover .sd-mini-phone img { transform: translateY(-40%); }
	@keyframes sdDrop { to { opacity: 1; transform: none; } }

	/* Art: Shopify Plus (several real stores, one account) */
	.sd-art-plus { height: 100%; display: flex; align-items: center; justify-content: center; gap: 2rem; }
	.sd-plus-stack { position: relative; width: 230px; height: 150px; }
	.sd-plus-win { position: absolute; left: 0; top: 0; width: 176px; height: 110px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--line-2); border-radius: 8px; background: #fff; box-shadow: 0 18px 30px -18px rgba(0,0,0,.8); transition: transform .5s var(--ease); }
	.sd-plus-win img { flex: 1; min-height: 0; width: 100%; max-width: none; object-fit: cover; object-position: top; }
	.sd-plus-win--1 { transform: translate(48px, 40px); z-index: 3; border-color: var(--accent); }
	.sd-plus-win--2 { transform: translate(24px, 20px); z-index: 2; }
	.sd-plus-win--3 { transform: translate(0, 0); z-index: 1; }
	.sd-card:hover .sd-plus-win--1 { transform: translate(60px, 46px); }
	.sd-card:hover .sd-plus-win--3 { transform: translate(-10px, -6px); }
	.sd-plus-badge { width: 64px; height: 64px; display: grid; place-items: center; border-radius: 12px; background: var(--accent); color: #fff; font-size: 2.2rem; font-weight: 300; }
	/* Art: migration + API diagrams (static svg) */
	.sd-art-svg { height: 100%; display: flex; align-items: center; justify-content: center; padding: .75rem 1rem; }
	.sd-art-svg svg { width: 100%; height: 100%; max-width: 440px; overflow: visible; }
	.sd-chip-rect { fill: var(--art-panel); stroke: var(--line-2); }
	.sd-chip-text { fill: var(--text); font-size: 12px; font-weight: 600; }
	.sd-path { fill: none; stroke: var(--art-line); stroke-width: 1.4; stroke-dasharray: 4 5; }
	.sd-arrow-head { fill: var(--art-bar-strong); }
	.sd-shop-tile { fill: var(--art-panel); stroke: var(--line-2); }
	.sd-shop-text { fill: var(--text); font-size: 12px; font-weight: 700; }

	/* Art: Liquid code editor (editors are dark, even on a light page) */
	.sd-art-code { height: 100%; display: flex; align-items: center; justify-content: center; padding: 1.1rem; }
	.sd-code { width: 100%; max-width: 520px; overflow: hidden; border-radius: 8px; background: #11161b; font-family: var(--mono); font-size: clamp(.62rem, 1.05vw, .78rem); }
	.sd-code-tabs { display: flex; border-bottom: 1px solid rgba(255,255,255,.08); }
	.sd-code-tabs span { padding: .5rem .9rem; color: #7c8aa3; border-right: 1px solid rgba(255,255,255,.08); }
	.sd-code-tabs span.is-on { color: #fff; box-shadow: inset 0 -2px 0 var(--accent); }
	.sd-code-body { padding: .75rem 0; }
	.sd-code-line { display: block; white-space: pre; line-height: 1.75; color: #d6e2f5; }
	.sd-code-line .ln { display: inline-block; width: 2.4rem; padding-right: .9rem; text-align: right; color: #3f4b58; user-select: none; }
	.sd-code .t { color: #7fb0ff; }
	.sd-code .k { color: #c3a6ff; }
	.sd-code .s { color: #a6d17a; }
	.sd-code .h { color: #f0a47f; }
	.sd-js .sd-card .sd-code-line { opacity: 0; transform: translateX(-8px); }
	.sd-js .sd-card.is-in .sd-code-line { animation: sdDrop .5s var(--ease) forwards; animation-delay: calc(var(--i, 0) * 120ms + .3s); }

	/* Art: post-launch monitoring */
	.sd-art-support { height: 100%; display: flex; flex-direction: column; justify-content: center; gap: 1.4rem; padding: 1.25rem 1.6rem; }
	.sd-ecg { width: 100%; height: 64px; overflow: visible; }
	.sd-ecg path { fill: none; stroke: var(--accent); stroke-width: 2; stroke-linejoin: round; stroke-linecap: round; vector-effect: non-scaling-stroke; }
	.sd-checks { display: flex; flex-wrap: wrap; gap: .6rem 1.4rem; }
	.sd-checks li { display: flex; align-items: center; gap: .45rem; font-size: .82rem; font-weight: 600; color: var(--text); }
	.sd-checks .material-symbols-outlined { font-size: 1.05rem; color: var(--accent); }

	@media (max-width: 1024px) {
		.sd-card, .sd-card--w7, .sd-card--w5 { grid-column: span 6; }
	}
	@media (max-width: 720px) {
		/* phones: the six service cards become a swipeable row */
		.sd-bento { display: flex; gap: .9rem; margin: 0 -16px; padding: 0 16px 8px; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; scroll-padding: 0 16px; scrollbar-width: none; }
		.sd-bento::-webkit-scrollbar { display: none; }
		.sd-card, .sd-card--w7, .sd-card--w5 { flex: 0 0 84%; grid-column: auto; scroll-snap-align: start; }
		.sd-card-art { height: 180px; }
		.sd-card-body { padding: 1.4rem 1.3rem 1.6rem; }
	}

	/* ═══ STORES WE'VE BUILT (carousel) ═════════════════ */
	.sd-work-top { display: flex; align-items: flex-end; justify-content: space-between; gap: 2rem; margin-bottom: 3rem; }
	.sd-work-top .sd-sec-head { margin-bottom: 0; }
	.sd-work-head .sd-copy { margin-top: 1.25rem; max-width: 44rem; }
	.sd-work-nav { display: flex; gap: .5rem; flex: none; }
	.sd-work-nav[hidden] { display: none; }
	.sd-work-nav button {
		width: 48px; height: 48px;
		display: grid;
		place-items: center;
		border-radius: 4px;
		border: 1px solid var(--line-2);
		color: var(--text);
		cursor: pointer;
		transition: border-color .3s, background-color .3s, opacity .3s;
	}
	.sd-work-nav button:hover:not(:disabled) { border-color: var(--accent); background: var(--accent-soft); }
	.sd-work-nav button:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
	.sd-work-nav button:disabled { opacity: .35; cursor: default; }
	/* About 2.4 stores in view so the next one peeks in */
	.sd-work-track {
		display: grid;
		grid-auto-flow: column;
		grid-auto-columns: calc((100% - 3rem) / 2.4);
		gap: 1.5rem;
		overflow-x: auto;
		overscroll-behavior-x: contain;
		scroll-snap-type: x mandatory;
		scrollbar-width: none;
		padding: 6px 0 4px;   /* room for the hover lift, which overflow would clip */
		margin-top: -6px;
	}
	.sd-work-track::-webkit-scrollbar { display: none; }
	.sd-work-track:focus-visible { outline: 2px solid var(--accent); outline-offset: 4px; }
	.sd-work { display: flex; flex-direction: column; gap: 1.25rem; min-width: 0; scroll-snap-align: start; }
	.sd-work-frame { display: block; overflow: hidden; border: 1px solid var(--line-2); border-radius: 12px; background: var(--surface); transition: border-color .3s, transform .45s var(--ease); }
	.sd-work-frame:hover { border-color: var(--accent); transform: translateY(-3px); }
	.sd-window-bar { display: flex; align-items: center; gap: .45rem; padding: .7rem 1rem; border-bottom: 1px solid var(--line); }
	.sd-window-bar > i { width: .6rem; height: .6rem; border-radius: 50%; background: var(--art-bar); }
	.sd-url { flex: 1; display: flex; align-items: center; gap: .4rem; height: 1.75rem; margin-left: .6rem; padding: 0 .75rem; border-radius: 4px; background: var(--surface-2); color: var(--muted); font-family: var(--mono); font-size: .72rem; white-space: nowrap; overflow: hidden; }
	.sd-url .material-symbols-outlined { font-size: .85rem; }
	.sd-work-view { position: relative; aspect-ratio: 16 / 10; overflow: hidden; background: var(--surface-2); }
	.sd-work-view img { display: block; width: 100%; height: auto; transform: translateY(0); transition: transform 1.1s var(--ease); will-change: transform; }
	.sd-work.is-scrolling .sd-work-view img { transform: translateY(calc(var(--dist, 0px) * -1)); transition: transform var(--dur, 8s) cubic-bezier(.45, 0, .55, 1); }
	.sd-work-meta { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
	.sd-work-meta h3 { font-size: clamp(1.2rem, 1.8vw, 1.45rem); font-weight: 700; letter-spacing: -.02em; color: var(--text); }
	.sd-work-tags { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .65rem; }
	.sd-work-tags span { padding: .3rem .7rem; border-radius: 999px; border: 1px solid var(--line-2); color: var(--body); font-size: .72rem; font-weight: 600; }
	.sd-work-link { display: inline-flex; align-items: center; gap: .45rem; min-height: 44px; padding: .65rem 1.05rem; border: 1px solid var(--line-2); border-radius: 4px; color: var(--text); font-size: .85rem; font-weight: 700; text-decoration: none; white-space: nowrap; transition: border-color .3s, color .3s; }
	.sd-work-link .material-symbols-outlined { font-size: 1rem; transition: transform .3s var(--ease); }
	.sd-work-link:hover { border-color: var(--accent); color: var(--accent-text); }
	.sd-work-link:hover .material-symbols-outlined { transform: translate(2px, -2px); }
	@media (max-width: 1024px) { .sd-work-track { grid-auto-columns: 62%; } }
	@media (max-width: 640px) {
		.sd-work-top { flex-direction: column; align-items: flex-start; gap: 1.5rem; margin-bottom: 2rem; }
		.sd-work-track { grid-auto-columns: 82%; gap: 1rem; }
	}

	/* ═══ PROCESS: CHECKOUT-STYLE STEPPER ══════════════ */
	.sd-pdots { display: none; }
	.sd-steps-tabs { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .5rem; margin-bottom: 1rem; }
	.sd-tab {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		gap: .35rem;
		padding: 1rem 1.1rem 1.15rem;
		overflow: hidden;
		border: 1px solid var(--line);
		border-radius: 10px;
		background: var(--surface);
		color: var(--muted);
		text-align: left;
		cursor: pointer;
		transition: border-color .3s, color .3s;
	}
	.sd-tab:hover { border-color: var(--line-2); color: var(--text); }
	.sd-tab:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 2px; }
	.sd-tab-top { display: flex; align-items: center; justify-content: space-between; width: 100%; font-family: var(--mono); font-size: .7rem; letter-spacing: .2em; }
	.sd-tab-check { font-size: 1rem; color: var(--accent-text); opacity: 0; transition: opacity .3s; }
	.sd-tab-name { font-size: .95rem; font-weight: 600; line-height: 1.3; }
	.sd-tab-track { position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: var(--line); }
	.sd-tab-fill { display: block; width: 0; height: 100%; background: var(--accent-text); }
	.sd-tab.is-active { border-color: var(--accent-text); color: var(--text); }
	.sd-tab.is-active .sd-tab-top { color: var(--accent-text); }
	.sd-tab.is-done { color: var(--body); }
	.sd-tab.is-done .sd-tab-check { opacity: 1; }
	.sd-tab.is-done .sd-tab-fill { width: 100%; }
	.sd-tab.is-active .sd-tab-fill { animation: sdFill 7s linear forwards; }
	.sd-stepper.is-paused .sd-tab-fill,
	.sd-steps-tabs:hover .sd-tab-fill { animation-play-state: paused; }
	.sd-stepper.is-manual .sd-tab.is-active .sd-tab-fill { animation: none; width: 100%; }
	@keyframes sdFill { from { width: 0; } to { width: 100%; } }
	.sd-step-panel { display: grid; grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); gap: 2.5rem; align-items: center; padding: clamp(2rem, 4vw, 3.5rem); border: 1px solid var(--line); border-radius: 12px; background: var(--surface); }
	.sd-step-panel + .sd-step-panel { margin-top: 1rem; }
	.sd-step-visual { display: flex; align-items: center; gap: 1.5rem; }
	.sd-step-big { font-size: clamp(6rem, 12vw, 11rem); font-weight: 700; line-height: .85; letter-spacing: -.06em; color: transparent; -webkit-text-stroke: 1.5px rgba(255,255,255,.28); }
	.sd-step-icon { width: 4rem; height: 4rem; display: grid; place-items: center; border-radius: 10px; background: var(--accent); color: #fff; }
	.sd-step-icon .material-symbols-outlined { font-size: 1.8rem; }
	.sd-step-panel h3 { margin-bottom: 1rem; font-size: clamp(1.6rem, 2.8vw, 2.4rem); font-weight: 700; letter-spacing: -.03em; line-height: 1.12; color: var(--text); }
	.sd-step-panel p { max-width: 40rem; color: var(--body); font-size: clamp(1rem, 1.3vw, 1.12rem); line-height: 1.8; }
	.sd-step-count { display: block; margin-bottom: .9rem; font-family: var(--mono); font-size: .72rem; letter-spacing: .22em; color: var(--muted); }
	@media (min-width: 901px) {
		.sd-stepper.is-enhanced .sd-step-panel { display: none; }
		.sd-stepper.is-enhanced .sd-step-panel.is-active { display: grid; animation: sdPanelIn .5s var(--ease); }
		.sd-stepper.is-enhanced .sd-step-panel + .sd-step-panel { margin-top: 0; }
	}
	@keyframes sdPanelIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
	@media (max-width: 900px) {
		.sd-steps-tabs { display: none; }
		/* phones and tablets: the five steps are a swipeable row */
		.sd-stepper { position: relative; display: flex; gap: .9rem; margin: 0 -16px; padding: 0 16px 6px; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; scroll-padding: 0 16px; scrollbar-width: none; }
		.sd-stepper::-webkit-scrollbar { display: none; }
		.sd-step-panel, .sd-step-panel + .sd-step-panel { flex: 0 0 86%; scroll-snap-align: start; margin-top: 0; grid-template-columns: 1fr; gap: 1.25rem; padding: 1.6rem 1.3rem; }
		.sd-pdots { display: flex; justify-content: center; gap: .5rem; padding-top: .8rem; }
		.sd-step-big { font-size: 4.5rem; }
		.sd-step-icon { width: 3.25rem; height: 3.25rem; }
		.sd-step-icon .material-symbols-outlined { font-size: 1.5rem; }
	}

	/* ═══ TECH STACK ORBIT ═════════════════════════════ */
	.sd-tech-head { margin: 0 auto 1rem; text-align: center; }
	.sd-tech-head .sd-index { justify-content: center; }
	.sd-tech-head .sd-index::before { content: ''; width: 3rem; height: 1px; background: var(--line-2); }
	.sd-orbit { --os: 640px; position: relative; width: var(--os); height: var(--os); margin: 0 auto; }
	.sd-orbit-ring { position: absolute; left: 50%; top: 50%; border-radius: 50%; transform: translate(-50%, -50%); border: 1px dashed rgba(255,255,255,.12); }
	.sd-orbit-ring--1 { width: 50%; height: 50%; }
	.sd-orbit-ring--2 { width: 96%; height: 96%; border-style: solid; border-color: rgba(255,255,255,.06); }
	.sd-orbit-core {
		position: absolute;
		left: 50%; top: 50%;
		z-index: 2;
		display: grid;
		place-items: center;
		width: calc(var(--os) * .22); height: calc(var(--os) * .22);
		transform: translate(-50%, -50%);
		border: 1px solid var(--line-2);
		border-radius: 50%;
		background: var(--surface);
	}
	.sd-orbit-core img { width: 40%; height: auto; }
	.sd-orbit-spin { position: absolute; inset: 0; z-index: 3; animation: sdSpin 90s linear infinite; }
	@keyframes sdSpin { to { transform: rotate(360deg); } }
	.sd-ring { position: absolute; inset: 0; margin: 0; padding: 0; list-style: none; }
	.sd-ring li { position: absolute; left: 50%; top: 50%; width: 0; height: 0; transform: rotate(var(--a)) translateX(var(--r)) rotate(calc(-1 * var(--a))); }
	.sd-ring--inner li { --r: calc(var(--os) * .25); }
	.sd-ring--outer li { --r: calc(var(--os) * .48); }
	.sd-orbit-pos { position: absolute; left: 0; top: 0; width: max-content; transform: translate(-50%, -50%); }
	/* Icon-only chips. The name shows only as a small tooltip on hover. */
	.sd-orbit-chip {
		position: relative;
		display: inline-flex;
		align-items: center;
		gap: .3rem;
		padding: .4rem;
		border: 1px solid var(--line-2);
		border-radius: 14px;
		background: var(--surface);
		animation: sdSpin 90s linear infinite reverse;
		transition: border-color .3s;
	}
	.sd-orbit-chip::after { content: attr(data-name); position: absolute; left: 50%; top: calc(100% + 8px); padding: .3rem .65rem; border-radius: 6px; background: var(--accent); color: #fff; font-size: .72rem; font-weight: 700; white-space: nowrap; opacity: 0; transform: translate(-50%, -4px); pointer-events: none; transition: opacity .2s, transform .2s; }
	.sd-orbit-chip:hover::after { opacity: 1; transform: translate(-50%, 0); }
	/* Brand logos on small tiles so each keeps its own colours;
	   max-width:none because the global img { max-width:100% } lets a shrink-to-fit chip count them as zero width */
	.sd-orbit-logos { display: inline-flex; gap: .3rem; flex: none; }
	.sd-orbit-logos img { display: block; flex: none; width: 36px; height: 36px; max-width: none; padding: 5px; border-radius: 9px; background: #fff; object-fit: contain; }
	.sd-orbit-chip:hover { border-color: var(--accent); }
	.sd-orbit:hover .sd-orbit-spin,
	.sd-orbit:hover .sd-orbit-chip { animation-play-state: paused; }
	@media (max-width: 1024px) { .sd-orbit { --os: 540px; } }
	/* Phones keep the same orbit, just smaller */
	@media (max-width: 640px) {
		.sd-orbit { --os: min(calc(100vw - 32px), 420px); margin-top: 1rem; }
		.sd-orbit-chip { gap: .22rem; padding: .28rem; border-radius: 10px; }
		.sd-orbit-chip::after { display: none; }
		.sd-orbit-logos { gap: .22rem; }
		.sd-orbit-logos img { width: 26px; height: 26px; padding: 3px; border-radius: 6px; }
	}
	/* ═══ FAQ ══════════════════════════════════════════ */
	.sd-faq-grid { display: grid; grid-template-columns: minmax(0, 4fr) minmax(0, 8fr); gap: 3.5rem; align-items: start; }
	.sd-faq-side { position: sticky; top: 8rem; }
	.sd-faq-glyph { margin-top: 1rem; font-size: clamp(8rem, 16vw, 15rem); line-height: .8; font-weight: 700; color: transparent; -webkit-text-stroke: 1.5px rgba(255,255,255,.14); user-select: none; }
	.sd-faq { border-bottom: 1px solid var(--line); transition: border-color .3s; }
	.sd-faq:first-child { border-top: 1px solid var(--line); }
	.sd-faq summary { display: flex; align-items: center; gap: 1.25rem; padding: 1.6rem 0; cursor: pointer; list-style: none; }
	.sd-faq summary::-webkit-details-marker { display: none; }
	.sd-faq summary:focus-visible { outline: 2px solid var(--accent); outline-offset: 4px; border-radius: 4px; }
	.sd-faq-num { min-width: 2.2rem; font-family: var(--mono); font-size: .72rem; letter-spacing: .18em; color: var(--muted); transition: color .3s; }
	.sd-faq summary h3 { flex: 1; font-size: clamp(1.05rem, 1.6vw, 1.3rem); font-weight: 600; line-height: 1.4; color: var(--text); }
	.sd-faq summary:hover h3 { color: var(--accent-text); }
	.sd-faq-icon { position: relative; flex: none; width: 2.6rem; height: 2.6rem; border: 1px solid var(--line-2); border-radius: 50%; transition: transform .4s var(--ease), background-color .3s, border-color .3s; }
	.sd-faq-icon::before,
	.sd-faq-icon::after { content: ''; position: absolute; left: 50%; top: 50%; width: 12px; height: 1.5px; background: var(--text); transform: translate(-50%, -50%); }
	.sd-faq-icon::after { transform: translate(-50%, -50%) rotate(90deg); }
	.sd-faq[open] { border-bottom-color: var(--accent); }
	.sd-faq[open] .sd-faq-num { color: var(--accent-text); }
	.sd-faq[open] .sd-faq-icon { transform: rotate(45deg); background: var(--accent); border-color: var(--accent); }
	.sd-faq[open] .sd-faq-icon::before,
	.sd-faq[open] .sd-faq-icon::after { background: #fff; }
	.sd-faq-body { padding: 0 3.75rem 1.9rem 3.45rem; }
	.sd-faq-body p { color: var(--body); font-size: 1.02rem; line-height: 1.85; }
	.sd-faq[open] .sd-faq-body { animation: sdFaqIn .4s var(--ease); }
	@keyframes sdFaqIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
	@media (max-width: 1024px) {
		.sd-faq-grid { grid-template-columns: 1fr; gap: 2rem; }
		.sd-faq-side { position: static; }
		.sd-faq-glyph { display: none; }
	}
	@media (max-width: 640px) {
		.sd-faq summary { gap: .9rem; padding: 1.3rem 0; }
		.sd-faq-num { min-width: 1.6rem; }
		.sd-faq-body { padding: 0 0 1.5rem 2.5rem; }
	}

	/* ═══ FINAL CTA ════════════════════════════════════ */
	.sd-cta { padding: 2rem 0 7rem; }
	.sd-section + .sd-cta { border-top: 0; }
	.sd-cta-card { position: relative; overflow: hidden; padding: clamp(3.25rem, 7vw, 6rem) clamp(1.25rem, 5vw, 5rem); border-radius: 16px; background: var(--accent); text-align: center; }
	.sd-cta-card::before {
		content: '';
		position: absolute;
		inset: 0;
		background-image:
			linear-gradient(rgba(255,255,255,.08) 1px, transparent 1px),
			linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px);
		background-size: 56px 56px;
		pointer-events: none;
	}
	.sd-cta-card > * { position: relative; }
	.sd-cta-icon { width: 4.25rem; height: 4.25rem; display: grid; place-items: center; margin: 0 auto 1.75rem; border-radius: 12px; background: #fff; }
	.sd-cta-icon img { width: 38px; height: 38px; }
	.sd-cta h2 { font-size: clamp(2.1rem, 5.5vw, 4.2rem); font-weight: 700; letter-spacing: -.045em; line-height: 1.04; color: #fff; }
	.sd-cta p { max-width: 40rem; margin: 1.4rem auto 2.4rem; color: rgba(255,255,255,.9); font-size: clamp(1rem, 1.4vw, 1.15rem); line-height: 1.75; }
	@media (max-width: 768px) { .sd-cta { padding: 1rem 0 4.5rem; } .sd-cta-card { border-radius: 12px; } }

	/* ─── Phones: every timed animation runs about 30% quicker ─── */
	@media (max-width: 760px) {
		.sd-js [data-sd-reveal] { transition-duration: .5s; transition-delay: calc(var(--d, 0s) * .6); }
		.sd-js .sd-card[data-sd-reveal] { transition: opacity .5s var(--ease) calc(var(--d, 0s) * .6), transform .5s var(--ease) calc(var(--d, 0s) * .6), border-color .3s; }
		.sd-orbit-spin, .sd-orbit-chip { animation-duration: 55s; }
		.sd-phone .sd-conf { animation-duration: 1s; }
		.sd-pane { transition-duration: .35s, .45s, 0s; transition-delay: 0s, 0s, .35s; }
		.sd-pane.is-on { transition-delay: 0s; }
		.sd-dash.is-auto .sd-dtab[aria-selected="true"] .sd-dtab-fill { animation-duration: 4.5s; }
		.sd-col-bar { transition-duration: .75s, .25s; transition-delay: calc(var(--i) * .07s + .1s), 0s; }
		.sd-col-val { transition-delay: calc(var(--i) * .07s + .7s); }
		.sd-col:hover .sd-col-val, .sd-col:focus-visible .sd-col-val { transition-delay: 0s; }
		.sd-work-view img { transition-duration: .75s; }
		.sd-js .sd-card.is-in .sd-code-line { animation-duration: .35s; animation-delay: calc(var(--i, 0) * 80ms + .2s); }
		.sd-faq[open] .sd-faq-body { animation-duration: .28s; }
		.sd-faq-icon { transition-duration: .28s, .2s, .2s; }
	}
	/* ─── Reduced motion ─────────────────────────── */
	@media (prefers-reduced-motion: reduce) {
		.sd-page *, .sd-page *::before, .sd-page *::after { animation: none !important; transition: none !important; }
		.sd-js [data-sd-reveal] { opacity: 1; transform: none; }
		.sd-js .sd-card .sd-code-line { opacity: 1; transform: none; }
		.sd-mini-view img, .sd-mini-phone img { transition: none; }
		.sd-tab.is-active .sd-tab-fill { width: 100%; }
	}
</style>
HTML;

include __DIR__ . '/app/views/header.php';
?>

<main class="sd-page">
	<script>document.currentScript.parentNode.classList.add('sd-js');</script>

	<!-- ══ HERO: A SHOPIFY STORE, PLAYED START TO FINISH ═══ -->
	<section class="sd-hero">
		<div class="sd-hero-bg" aria-hidden="true">SHOPIFY</div>
		<div class="sd-wrap">
			<div class="sd-hero-top">
				<h1 class="sd-label" data-sd-reveal><img class="sd-bag" src="<?= sd_e($shopifyLogo) ?>" alt="" width="20" height="22"><?= sd_e($sdHero['eyebrow']) ?></h1>
				<p class="sd-h1" data-sd-reveal style="--d:.08s">
					<span class="sd-h1-line">Your D2C Store,</span>
					<span class="sd-h1-line sd-accent">Ready to Sell in 7 Days</span>
				</p>
			</div>

			<div class="sd-hero-stage" data-sd-hero>
				<div class="sd-hero-side sd-hero-side--l" data-sd-reveal style="--d:.16s">
					<p class="sd-lead"><?= sd_e($sdHero['sub']) ?></p>
					<div class="sd-hero-cta">
						<a href="<?= sd_e($leadUrl) ?>" class="sd-btn sd-btn-primary">
							<?= sd_e($sdHero['cta']) ?>
							<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
						</a>
						<?php if ($sdWork): ?>
						<a href="#our-work" class="sd-btn sd-btn-ghost" data-sd-jump>
							See Stores We've Built
							<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
						</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="sd-phone-wrap" data-sd-reveal style="--d:.24s">
					<div class="sd-devs" data-sd-devs>
					<div class="sd-desk" data-sd-desk data-step="0">
						<div class="sd-desk-bar" aria-hidden="true"><i></i><i></i><i></i><span class="sd-desk-url"><span class="material-symbols-outlined">lock</span><span class="sd-url-t"><?= sd_e($sdHeroStore['domain']) ?><?php foreach ($sdHeroStore['paths'] as $p => $path): ?><span class="sd-url-p sd-url-p--<?= $p ?>"><?= sd_e($path) ?></span><?php endforeach; ?></span></span></div>
						<div class="sd-desk-body">
							<?php foreach ($sdHeroStore['desk'] as $p => $src): ?>
							<div class="sd-shot sd-shot--<?= $p ?>"><img src="<?= sd_e($appUrl . '/' . $src) ?>" alt="" decoding="async"<?= image_dims_attr(__DIR__, $src) ?>><?php if (isset($sdHeroStore['taps'][$p])): ?><i class="sd-tap" style="left:<?= $sdHeroStore['taps'][$p][0] ?>%;top:<?= $sdHeroStore['taps'][$p][1] ?>%"></i><?php endif; ?></div>
							<?php endforeach; ?>
							<span class="sd-toast" aria-hidden="true"><span class="material-symbols-outlined">check_circle</span>Added to cart</span>
							<div class="sd-done" aria-hidden="true"><span class="sd-ps-ok"><span class="material-symbols-outlined">check</span></span><span class="sd-ps-done">Order placed</span></div>
							<button type="button" class="sd-next" data-sd-next aria-label="Show the next step of the store journey"></button>
						</div>
					</div>
					<div class="sd-phone" data-sd-phone data-step="0">
						<span class="sd-phone-notch"></span>
						<div class="sd-ps">
							<?php foreach ($sdHeroStore['phone'] as $p => $src): ?>
							<div class="sd-shot sd-shot--<?= $p ?>"><img src="<?= sd_e($appUrl . '/' . $src) ?>" alt="" decoding="async"<?= image_dims_attr(__DIR__, $src) ?>></div>
							<?php endforeach; ?>
							<span class="sd-toast" aria-hidden="true"><span class="material-symbols-outlined">check_circle</span>Added to cart</span>
							<div class="sd-done" aria-hidden="true">
								<span class="sd-ps-ok"><span class="material-symbols-outlined">check</span></span>
								<span class="sd-ps-done">Order placed</span>
								<?php foreach ([[-90, -120], [-60, -150], [-20, -170], [30, -160], [70, -130], [95, -100], [-100, -60], [100, -50], [-40, -110], [50, -100]] as $c => [$cx, $cy]): ?>
								<i class="sd-conf" style="--i:<?= $c ?>;--cx:<?= $cx ?>px;--cy:<?= $cy ?>px"></i>
								<?php endforeach; ?>
							</div>
							<button type="button" class="sd-next" data-sd-next aria-label="Show the next step of the store journey"></button>
						</div>
					</div>
					</div>
				</div>

				<div class="sd-hero-side sd-hero-side--r" data-sd-reveal style="--d:.32s">
					<ol class="sd-rail" role="group" aria-label="Store journey">
						<?php foreach ($sdFlow as $i => $name): ?>
						<li><button type="button" data-sd-rail="<?= $i ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><span class="sd-rail-n"><?= sprintf('%02d', $i + 1) ?></span><?= sd_e($name) ?></button></li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ WHO THIS IS FOR: A FILM THAT SCROLLS SIDEWAYS ═══ -->
	<section class="sd-film sd-alt" id="who-its-for" data-sd-film>
		<div class="sd-film-pin">
			<div class="sd-film-track">
				<article class="sd-panel">
					<div class="sd-wrap">
						<div class="sd-intro" data-sd-reveal>
							<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
							<h2 class="sd-h2"><?= sd_e($sdWho['heading']) ?></h2>
							<p class="sd-copy"><?= sd_e($sdWho['text']) ?></p>
							<div aria-hidden="true">
								<div class="sd-fit-cap">We work best with</div>
								<ul class="sd-fit-list">
									<?php foreach ($sdWho['traits'] as $trait): ?>
									<li><span class="material-symbols-outlined">check_circle</span><?= sd_e($trait) ?></li>
									<?php endforeach; ?>
								</ul>
								<span class="sd-hint"><span class="material-symbols-outlined">arrow_forward</span>Keep scrolling</span>
							</div>
						</div>
					</div>
				</article>

				<div class="sd-acts" data-sd-acts>
				<?php foreach ($sdWho['fits'] as $i => $fit): ?>
				<article class="sd-panel" data-sd-act="<?= $i ?>">
					<div class="sd-wrap sd-act-grid">
						<div>
							<div class="sd-act-n" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></div>
							<h3 class="sd-act-t"><?= sd_e(ucfirst($fit['phrase'])) ?></h3>
						</div>

						<?php if ($i === 0): ?>
						<div class="sd-scn" aria-hidden="true">
							<div class="sd-b1">
								<div class="sd-b1-bar"><i></i><i></i><i></i><span class="sd-b1-url"><?= sd_e($sdStoryShots['build']['domain']) ?></span></div>
								<div class="sd-b1-body">
									<?php for ($b = 0; $b < 5; $b++): ?>
									<div class="sd-band sd-k" style="--b:<?= $b ?>;--ks:<?= round(.04 + $b * .15, 2) ?>;--kl:.16"><img src="<?= sd_e($appUrl . '/' . $sdStoryShots['build']['img']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdStoryShots['build']['img']) ?>></div>
									<?php endfor; ?>
								</div>
							</div>
							<span class="sd-live sd-k" style="--ks:.86;--kl:.12"><i></i>Live</span>
						</div>

						<?php elseif ($i === 1): ?>
						<div class="sd-scn" aria-hidden="true">
							<div class="sd-mbox sd-mbox--old sd-k" style="--ks:.2;--kl:.7">
								<span class="sd-mtag"><?= sd_e($fit['from']['label']) ?></span>
								<?php foreach ($sdMigRows as $r): ?><span class="sd-slot" style="top:<?= round(($r - 14) / 76 * 100, 1) ?>%;height:16.4%"></span><?php endforeach; ?>
							</div>
							<span class="sd-marrow"></span>
							<span class="sd-mtag sd-mtag--shop"><img src="<?= sd_e($shopifyLogo) ?>" alt="" width="14" height="16">Shopify</span>
							<div class="sd-mweb sd-k" style="--ks:.22;--kl:.66">
								<div class="sd-b1-bar"><i></i><i></i><i></i><span class="sd-b1-url"><?= sd_e($sdHeroStore['domain'] . $sdHeroStore['paths'][1]) ?></span></div>
								<img src="<?= sd_e($appUrl . '/' . $sdHeroStore['desk'][1]) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdHeroStore['desk'][1]) ?>>
							</div>
							<?php foreach ($sdMigChips as $c => $chip): ?>
							<div class="sd-mchip sd-k" data-sd-chip style="--ks:<?= .1 + $c * .15 ?>;--kl:.32;top:<?= $sdMigRows[$c] ?>%"><span class="material-symbols-outlined"><?= sd_e($chip['icon']) ?></span><?= sd_e($chip['label']) ?></div>
							<?php endforeach; ?>
							<span class="sd-mpill sd-k" style="--ks:.86;--kl:.12"><span class="material-symbols-outlined">alt_route</span>301 redirects</span>
						</div>

						<?php else: ?>
						<div class="sd-scn" aria-hidden="true">
							<div class="sd-plabel sd-k" style="--ks:.14;--kl:.2"><span class="sd-pl-a"><?= sd_e($fit['from']['label']) ?></span><span class="sd-pl-b"><b>+</b>Shopify Plus</span></div>
							<div class="sd-pst sd-pst--l sd-k" style="--ks:.2;--kl:.3"><span class="sd-win-bar"><i></i><i></i><i></i></span><img src="<?= sd_e($appUrl . '/' . $sdStoryShots['plus']['l']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdStoryShots['plus']['l']) ?>></div>
							<div class="sd-pst sd-pst--r sd-k" style="--ks:.2;--kl:.3"><span class="sd-win-bar"><i></i><i></i><i></i></span><img src="<?= sd_e($appUrl . '/' . $sdStoryShots['plus']['r']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdStoryShots['plus']['r']) ?>></div>
							<div class="sd-pst sd-pst--c"><span class="sd-win-bar"><i></i><i></i><i></i></span><img src="<?= sd_e($appUrl . '/' . $sdStoryShots['plus']['c']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdStoryShots['plus']['c']) ?>></div>
							<?php foreach ($sdPlusPills as $p => $pill): ?>
							<span class="sd-ppill sd-k" style="--ks:<?= .5 + $p * .11 ?>;--kl:.18;left:<?= $pill['x'] ?>%;top:<?= $pill['y'] ?>%"><span class="material-symbols-outlined"><?= sd_e($pill['icon']) ?></span><?= sd_e($pill['label']) ?></span>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</article>
				<?php endforeach; ?>
				</div>
			</div>

			<div class="sd-acts-dots" role="group" aria-label="<?= sd_e($sdWho['heading']) ?>">
				<?php foreach ($sdWho['fits'] as $i => $fit): ?><button type="button" class="sd-hdot<?= $i === 0 ? ' is-on' : '' ?>" data-sd-adot="<?= $i ?>" aria-label="<?= sd_e(ucfirst($fit['phrase'])) ?>"></button><?php endforeach; ?>
			</div>

			<div class="sd-film-bar">
				<div class="sd-wrap">
					<div class="sd-film-prog" aria-hidden="true"><i></i></div>
					<div class="sd-film-pips" role="group" aria-label="<?= sd_e($sdWho['heading']) ?>">
						<button type="button" class="sd-pip" data-sd-pip="0" aria-pressed="true" aria-label="<?= sd_e($sdWho['heading']) ?>">00</button>
						<?php foreach ($sdWho['fits'] as $i => $fit): ?>
						<button type="button" class="sd-pip" data-sd-pip="<?= $i + 1 ?>" aria-pressed="false" aria-label="<?= sd_e(ucfirst($fit['phrase'])) ?>"><?= sprintf('%02d', $i + 1) ?></button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ HIRE: A DIAGRAM THAT CHANGES AS EACH SENTENCE SCROLLS BY ═══ -->
	<section class="sd-hire" id="hire-shopify-developers" data-sd-hire>
		<div class="sd-wrap sd-hire-grid">
			<div class="sd-hstage" data-sd-hstage>
				<div class="sd-hbox" data-sd-hbox aria-hidden="true">
					<svg class="sd-hsvg sd-hsvg--w" viewBox="0 0 100 32" preserveAspectRatio="none">
						<defs>
							<marker id="sdHAw" markerUnits="userSpaceOnUse" viewBox="0 0 3 3" refX="2.7" refY="1.5" markerWidth="1.7" markerHeight="1.7" orient="auto"><path class="sd-hl-arrow" d="M0 0 L3 1.5 L0 3 z" /></marker>
							<marker id="sdHAmw" markerUnits="userSpaceOnUse" viewBox="0 0 3 3" refX="2.7" refY="1.5" markerWidth="1.4" markerHeight="1.4" orient="auto"><path class="sd-hl-arrow-m" d="M0 0 L3 1.5 L0 3 z" /></marker>
						</defs>
						<path class="sd-hl-loose" marker-end="url(#sdHAmw)" d="M31 6.8 C 56 6.8, 60 20, 81.6 20" />
						<path class="sd-hl-loose" marker-end="url(#sdHAmw)" d="M46 15.8 C 62 15.8, 66 16, 81.6 16" />
						<path class="sd-hl-loose" marker-end="url(#sdHAmw)" d="M34 24.8 C 58 24.8, 62 12, 81.6 12" />
						<path class="sd-hl-team" marker-end="url(#sdHAw)" d="M36 16 L81.6 16" />
					</svg>
					<svg class="sd-hsvg sd-hsvg--n" viewBox="0 0 100 86" preserveAspectRatio="none">
						<defs>
							<marker id="sdHA" markerUnits="userSpaceOnUse" viewBox="0 0 3 3" refX="2.7" refY="1.5" markerWidth="3.2" markerHeight="3.2" orient="auto"><path class="sd-hl-arrow" d="M0 0 L3 1.5 L0 3 z" /></marker>
							<marker id="sdHAm" markerUnits="userSpaceOnUse" viewBox="0 0 3 3" refX="2.7" refY="1.5" markerWidth="2.6" markerHeight="2.6" orient="auto"><path class="sd-hl-arrow-m" d="M0 0 L3 1.5 L0 3 z" /></marker>
						</defs>
						<path class="sd-hl-loose" marker-end="url(#sdHAm)" d="M42 10.5 C 58 10.5, 60 22, 71.5 20" />
						<path class="sd-hl-loose" marker-end="url(#sdHAm)" d="M62 23.5 C 66 23.5, 68 26, 71.5 26" />
						<path class="sd-hl-loose" marker-end="url(#sdHAm)" d="M44 36.5 C 58 36.5, 60 30, 71.5 32" />
						<path class="sd-hl-team" marker-end="url(#sdHA)" d="M65 26 L71.5 26" />
					</svg>
					<div class="sd-hpanel"></div>
					<span class="sd-hcap">DEDICATED TEAM</span>
					<?php foreach ($sdHireRoles as $role): ?>
					<div class="sd-hrole" style="--fx:<?= $role['f'][0] ?>;--fy:<?= $role['f'][1] ?>;--fw:<?= $role['f'][2] ?>;--tx:<?= $role['t'][0] ?>;--ty:<?= $role['t'][1] ?>;--tw:<?= $role['t'][2] ?>;--wfx:<?= $role['wf'][0] ?>;--wfy:<?= $role['wf'][1] ?>;--wfw:<?= $role['wf'][2] ?>;--wtx:<?= $role['wt'][0] ?>;--wty:<?= $role['wt'][1] ?>;--wtw:<?= $role['wt'][2] ?>">
						<span class="sd-hlab sd-hlab-f"><span class="sd-hico"><span class="material-symbols-outlined">person</span></span><?= sd_e($role['free']) ?></span>
						<span class="sd-hlab sd-hlab-t"><span class="sd-hico"><span class="material-symbols-outlined">check</span></span><?= sd_e($role['team']) ?></span>
					</div>
					<?php endforeach; ?>
					<div class="sd-hflow"><i style="--i:0"></i><i style="--i:1"></i><i style="--i:2"></i><i style="--i:3"></i></div>
					<div class="sd-hstore"><img src="<?= sd_e($shopifyLogo) ?>" alt="" width="48" height="54">Your store</div>
				</div>
				<div class="sd-hdots" role="group" aria-label="Story steps">
					<?php foreach ($sdHireBeats as $b => $beat): ?><button type="button" class="sd-hdot<?= $b === 0 ? ' is-on' : '' ?>" data-sd-hdot="<?= $b ?>" aria-label="Step <?= $b + 1 ?>"></button><?php endforeach; ?>
				</div>
			</div>

			<div class="sd-hire-text">
				<div class="sd-hire-head" data-sd-reveal>
					<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
					<h2 class="sd-h2"><?= sd_e($sdHire['heading']) ?></h2>
				</div>
				<?php foreach ($sdHireBeats as $b => $beat): ?>
				<p class="sd-beat<?= $b === 0 ? ' is-active' : '' ?>" data-sd-beat="<?= $b ?>"><span class="sd-beat-n" aria-hidden="true"><?= sprintf('%02d', $b + 1) ?></span><span class="sd-beat-copy"><?= sd_e($beat) ?></span></p>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ══ THE NUMBERS: AN INTERACTIVE DASHBOARD ═══════════ -->
	<section class="sd-section sd-alt" id="numbers">
		<div class="sd-wrap">
			<div class="sd-dash" data-sd-dash data-sd-reveal>
				<div class="sd-dtabs" role="tablist" aria-label="Our results">
					<?php foreach ($sdMetrics as $k => $m): ?>
					<button type="button" class="sd-dtab" role="tab" id="sd-dtab-<?= $k ?>" aria-controls="sd-pane-<?= $k ?>" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" tabindex="<?= $k === 0 ? '0' : '-1' ?>">
						<span class="sd-dtab-n"><span><?= (int) $m['value'] ?></span><span class="sd-dtab-unit"><?= sd_e($m['unit']) ?></span></span>
						<span class="sd-dtab-l"><?= sd_e($m['label']) ?></span>
						<i class="sd-dtab-fill" aria-hidden="true"></i>
					</button>
					<?php endforeach; ?>
				</div>

				<div class="sd-dstage">
					<?php foreach ($sdMetrics as $k => $m): ?>
					<div class="sd-pane<?= $k === 0 ? ' is-on' : '' ?>" role="tabpanel" id="sd-pane-<?= $k ?>" aria-labelledby="sd-dtab-<?= $k ?>" data-kind="<?= sd_e($m['kind']) ?>" data-value="<?= (int) $m['value'] ?>">
						<div class="sd-pane-head">
							<div class="sd-pane-num"><span data-count><?= (int) $m['value'] ?></span><span class="sd-pane-unit"><?= sd_e($m['unit']) ?></span></div>
							<div class="sd-pane-label"><?= sd_e($m['label']) ?></div>
						</div>
						<div class="sd-pane-body" aria-hidden="true">
							<?php if ($m['kind'] === 'bars'): $cmax = (int) $m['value']; ?>
							<div class="sd-cols">
								<?php for ($g = 0; $g <= 4; $g++): ?>
								<div class="sd-gl" style="--y:<?= $g / 4 ?>"><span><?= (int) round($cmax * $g / 4) ?></span></div>
								<?php endfor; ?>
								<div class="sd-cols-plot">
									<?php foreach ($m['series'] as $ci => $cv): $last = $ci === count($m['series']) - 1; ?>
									<div class="sd-col<?= $last ? ' is-last' : '' ?>" style="--v:<?= round($cv / $cmax, 3) ?>;--i:<?= $ci ?>" tabindex="0" aria-label="<?= (int) $sdYears[$ci] ?>: <?= (int) $cv . ($last ? sd_e($m['unit']) : '') ?>">
										<span class="sd-col-val"><?= (int) $cv . ($last ? sd_e($m['unit']) : '') ?></span>
										<i class="sd-col-bar"></i>
										<span class="sd-col-yr"><?= (int) $sdYears[$ci] ?></span>
									</div>
									<?php endforeach; ?>
								</div>
							</div>
							<?php else:
								$max = (int) $m['value'];
								$pts = [];
								for ($i = 0; $i <= 40; $i++) {
									$t = $i / 40;
									$e = $t * $t * (3 - 2 * $t);
									$pts[] = [44 + $t * 336, 180 - $e * 140];
								}
								$line = 'M' . implode(' L', array_map(fn($p) => round($p[0], 1) . ' ' . round($p[1], 1), $pts));
								$area = $line . ' L380 180 L44 180 Z';
							?>
							<svg class="sd-lift" data-max="<?= $max ?>" viewBox="0 0 400 220" preserveAspectRatio="xMidYMid meet">
								<defs>
									<linearGradient id="sdLiftFill<?= $k ?>" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0066ff" stop-opacity=".38" /><stop offset="1" stop-color="#0066ff" stop-opacity="0" /></linearGradient>
									<clipPath id="sdLiftClip<?= $k ?>"><rect class="sd-lclip" x="0" y="0" width="380" height="220" /></clipPath>
								</defs>
								<?php for ($g = 1; $g <= $max; $g++): $gy = round(180 - ($g - 1) / ($max - 1) * 140, 1); ?>
								<line class="sd-lg<?= $g === 1 ? ' sd-lg--base' : '' ?>" x1="44" y1="<?= $gy ?>" x2="380" y2="<?= $gy ?>" />
								<text class="sd-lt" x="34" y="<?= $gy + 3.5 ?>" text-anchor="end"><?= $g ?>X</text>
								<?php endfor; ?>
								<g clip-path="url(#sdLiftClip<?= $k ?>)">
									<path class="sd-larea" style="fill:url(#sdLiftFill<?= $k ?>)" d="<?= $area ?>" />
									<path class="sd-lline" d="<?= $line ?>" />
								</g>
								<text class="sd-lend" x="380" y="<?= round(180 - 140 - 12, 1) ?>"><?= $max ?>X</text>
								<g class="sd-lmark" data-mark>
									<line x1="0" y1="20" x2="0" y2="180" />
									<circle r="7" cx="0" cy="180" data-dot />
									<g class="sd-lbub" data-bub><rect x="-25" y="-30" width="50" height="24" rx="6" /><text x="0" y="-13">1X</text></g>
								</g>
							</svg>
							<label class="sd-lctl"><span>Before</span><input type="range" min="0" max="100" value="100" step="1" aria-label="<?= sd_e($m['label']) ?>: drag from before to after"><span>After</span></label>
							<?php endif; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ SERVICES BENTO ═══════════════════════════════ -->
	<section class="sd-section" id="services">
		<div class="sd-wrap">
			<div class="sd-sec-head" data-sd-reveal>
				<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
				<h2 class="sd-h2"><?= sd_e($sdServicesHeading) ?></h2>
			</div>

			<div class="sd-bento">
				<?php foreach ($sdServices as $i => $svc): ?>
				<article class="sd-card sd-card--w<?= (int) $svc['w'] ?>" data-sd-reveal style="--d:<?= ($i % 2) * 0.08 ?>s">
					<div class="sd-card-art" aria-hidden="true">
						<?php if ($svc['art'] === 'store'): ?>
						<div class="sd-art-store">
							<div class="sd-mini"><span class="sd-win-bar"><i></i><i></i><i></i></span><div class="sd-mini-view"><img src="<?= sd_e($appUrl . '/' . $sdCardShots['store']['desk']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdCardShots['store']['desk']) ?>></div></div>
							<div class="sd-mini-phone"><img src="<?= sd_e($appUrl . '/' . $sdCardShots['store']['phone']) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdCardShots['store']['phone']) ?>></div>
						</div>

						<?php elseif ($svc['art'] === 'plus'): ?>
						<div class="sd-art-plus">
							<div class="sd-plus-stack">
								<?php foreach ([3, 2, 1] as $w => $n): ?>
								<div class="sd-plus-win sd-plus-win--<?= $n ?>"><span class="sd-win-bar"><i></i><i></i><i></i></span><img src="<?= sd_e($appUrl . '/' . $sdCardShots['plus'][$w]) ?>" alt="" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $sdCardShots['plus'][$w]) ?>></div>
								<?php endforeach; ?>
							</div>
							<div class="sd-plus-badge">+</div>
						</div>

						<?php elseif ($svc['art'] === 'migrate'): ?>
						<div class="sd-art-svg">
							<svg viewBox="0 0 400 190">
								<defs>
									<marker id="sdArrowMig" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto">
										<path class="sd-arrow-head" d="M0 0 L10 5 L0 10 z" />
									</marker>
								</defs>
								<path class="sd-path" marker-end="url(#sdArrowMig)" d="M142 42 C 220 42, 215 95, 282 95" />
								<path class="sd-path" marker-end="url(#sdArrowMig)" d="M142 95 L 282 95" />
								<path class="sd-path" marker-end="url(#sdArrowMig)" d="M142 148 C 220 148, 215 95, 282 95" />

								<rect class="sd-chip-rect" x="16" y="25" width="126" height="34" rx="6" />
								<image href="<?= sd_e($wooLogo) ?>" x="26" y="33" width="26" height="18" />
								<text class="sd-chip-text" x="58" y="46.5">WooCommerce</text>
								<rect class="sd-chip-rect" x="16" y="78" width="126" height="34" rx="6" />
								<text class="sd-chip-text" x="30" y="99.5">Magento</text>
								<rect class="sd-chip-rect" x="16" y="131" width="126" height="34" rx="6" />
								<text class="sd-chip-text" x="30" y="152.5">Custom platform</text>

								<rect class="sd-shop-tile" x="288" y="50" width="96" height="90" rx="10" />
								<image href="<?= sd_e($shopifyLogo) ?>" x="316" y="60" width="40" height="44" />
								<text class="sd-shop-text" x="336" y="125" text-anchor="middle">Shopify</text>
							</svg>
						</div>

						<?php elseif ($svc['art'] === 'theme'): ?>
						<div class="sd-art-code">
							<div class="sd-code">
								<div class="sd-code-tabs"><span class="is-on">product-card.liquid</span><span>theme.liquid</span></div>
								<div class="sd-code-body">
									<div class="sd-code-line" style="--i:0"><span class="ln">1</span><span class="t">{%</span> <span class="k">for</span> product <span class="k">in</span> collection.products <span class="t">%}</span></div>
									<div class="sd-code-line" style="--i:1"><span class="ln">2</span>  <span class="h">&lt;div</span> class=<span class="s">"card card--{{ section.settings.style }}"</span><span class="h">&gt;</span></div>
									<div class="sd-code-line" style="--i:2"><span class="ln">3</span>    <span class="t">{%</span> <span class="k">render</span> <span class="s">'price'</span>, product: product <span class="t">%}</span></div>
									<div class="sd-code-line" style="--i:3"><span class="ln">4</span>  <span class="h">&lt;/div&gt;</span></div>
									<div class="sd-code-line" style="--i:4"><span class="ln">5</span><span class="t">{%</span> <span class="k">endfor</span> <span class="t">%}</span></div>
								</div>
							</div>
						</div>

						<?php elseif ($svc['art'] === 'api'): ?>
						<div class="sd-art-svg">
							<svg viewBox="0 0 400 200">
								<defs>
									<marker id="sdArrowApi" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
										<path class="sd-arrow-head" d="M0 0 L10 5 L0 10 z" />
									</marker>
								</defs>
								<path class="sd-path" marker-start="url(#sdArrowApi)" marker-end="url(#sdArrowApi)" d="M166 86 L 130 62" />
								<path class="sd-path" marker-start="url(#sdArrowApi)" marker-end="url(#sdArrowApi)" d="M234 86 L 270 62" />
								<path class="sd-path" marker-start="url(#sdArrowApi)" marker-end="url(#sdArrowApi)" d="M166 114 L 130 138" />
								<path class="sd-path" marker-start="url(#sdArrowApi)" marker-end="url(#sdArrowApi)" d="M234 114 L 270 138" />

								<circle class="sd-shop-tile" cx="200" cy="100" r="34" />
								<image href="<?= sd_e($shopifyLogo) ?>" x="183" y="81" width="34" height="38" />

								<rect class="sd-chip-rect" x="30" y="25" width="96" height="34" rx="6" />
								<text class="sd-chip-text" x="78" y="46.5" text-anchor="middle">ERP</text>
								<rect class="sd-chip-rect" x="274" y="25" width="96" height="34" rx="6" />
								<text class="sd-chip-text" x="322" y="46.5" text-anchor="middle">CRM</text>
								<rect class="sd-chip-rect" x="30" y="141" width="96" height="34" rx="6" />
								<text class="sd-chip-text" x="78" y="162.5" text-anchor="middle">Inventory</text>
								<rect class="sd-chip-rect" x="274" y="141" width="96" height="34" rx="6" />
								<text class="sd-chip-text" x="322" y="162.5" text-anchor="middle">Marketplace</text>
							</svg>
						</div>

						<?php elseif ($svc['art'] === 'support'): ?>
						<div class="sd-art-support">
							<svg class="sd-ecg" viewBox="0 0 400 64" preserveAspectRatio="none">
								<path d="M0 34 H110 L122 34 L132 10 L144 56 L156 22 L166 34 H250 L262 34 L272 12 L284 54 L295 26 L305 34 H400" />
							</svg>
							<ul class="sd-checks">
								<li><span class="material-symbols-outlined">check_circle</span>Maintenance</li>
								<li><span class="material-symbols-outlined">check_circle</span>App updates</li>
								<li><span class="material-symbols-outlined">check_circle</span>Performance</li>
							</ul>
						</div>
						<?php endif; ?>
					</div>
					<div class="sd-card-body">
						<div class="sd-card-num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></div>
						<h4><?= sd_e($svc['title']) ?></h4>
						<p><?= sd_e($svc['text']) ?></p>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ($sdWork): ?>
	<!-- ══ STORES WE'VE BUILT ═══════════════════════════ -->
	<section class="sd-section sd-alt" id="our-work">
		<div class="sd-wrap">
			<div class="sd-work-top" data-sd-reveal>
				<div class="sd-sec-head sd-work-head">
					<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
					<h2 class="sd-h2"><?= sd_e($sdWorkHeading) ?></h2>
					<p class="sd-copy"><?= sd_e($sdWorkText) ?></p>
				</div>
				<div class="sd-work-nav" data-sd-work-nav>
					<button type="button" data-sd-work-prev aria-label="Previous stores">
						<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
					</button>
					<button type="button" data-sd-work-next aria-label="Next stores">
						<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
					</button>
				</div>
			</div>

			<div class="sd-work-track" data-sd-work-track tabindex="0" role="region" aria-label="<?= sd_e($sdWorkHeading) ?>">
				<?php foreach ($sdWork as $i => $store): ?>
				<article class="sd-work" data-sd-work>
					<a class="sd-work-frame" href="<?= sd_e($store['url']) ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true">
						<div class="sd-window-bar">
							<i></i><i></i><i></i>
							<div class="sd-url"><span class="material-symbols-outlined">lock</span><?= sd_e($store['domain']) ?></div>
						</div>
						<div class="sd-work-view">
							<img src="<?= sd_e($appUrl . '/' . $store['image']) ?>" alt="<?= sd_e($store['name']) ?> Shopify store homepage" loading="lazy" decoding="async"<?= image_dims_attr(__DIR__, $store['image']) ?>>
						</div>
					</a>
					<div class="sd-work-meta">
						<div>
							<h3><?= sd_e($store['name']) ?></h3>
							<?php if ($store['tags']): ?>
							<div class="sd-work-tags">
								<?php foreach ($store['tags'] as $tag): ?><span><?= sd_e($tag) ?></span><?php endforeach; ?>
							</div>
							<?php endif; ?>
						</div>
						<a class="sd-work-link" href="<?= sd_e($store['url']) ?>" target="_blank" rel="noopener" aria-label="Visit the <?= sd_e($store['name']) ?> live store (opens in a new tab)">
							Visit live store <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
						</a>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ══ PROCESS: CHECKOUT-STYLE STEPPER ══════════════ -->
	<section class="sd-section" id="process">
		<div class="sd-wrap">
			<div class="sd-sec-head" data-sd-reveal>
				<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
				<h2 class="sd-h2"><?= sd_e($sdProcessHeading) ?></h2>
			</div>

			<div class="sd-stepper" data-sd-stepper data-sd-reveal>
				<div class="sd-steps-tabs" role="tablist" aria-label="<?= sd_e($sdProcessHeading) ?>">
					<?php foreach ($sdSteps as $i => $step): ?>
					<button type="button" class="sd-tab" role="tab" id="sd-tab-<?= $i ?>" aria-controls="sd-panel-<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" tabindex="<?= $i === 0 ? '0' : '-1' ?>">
						<span class="sd-tab-top"><span><?= sd_e($step['num']) ?></span><span class="sd-tab-check material-symbols-outlined" aria-hidden="true">check_circle</span></span>
						<span class="sd-tab-name"><?= sd_e($step['title']) ?></span>
						<span class="sd-tab-track" aria-hidden="true"><span class="sd-tab-fill"></span></span>
					</button>
					<?php endforeach; ?>
				</div>

				<?php foreach ($sdSteps as $i => $step): ?>
				<div class="sd-step-panel<?= $i === 0 ? ' is-active' : '' ?>" role="tabpanel" id="sd-panel-<?= $i ?>" aria-labelledby="sd-tab-<?= $i ?>">
					<div class="sd-step-visual" aria-hidden="true">
						<span class="sd-step-big"><?= sd_e($step['num']) ?></span>
						<span class="sd-step-icon"><span class="material-symbols-outlined"><?= sd_e($step['icon']) ?></span></span>
					</div>
					<div>
						<span class="sd-step-count" aria-hidden="true"><?= sd_e($step['num']) ?> / <?= sprintf('%02d', count($sdSteps)) ?></span>
						<h3><?= sd_e($step['num']) ?>. <?= sd_e($step['title']) ?></h3>
						<p><?= sd_e($step['text']) ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="sd-pdots" role="group" aria-label="<?= sd_e($sdProcessHeading) ?>">
				<?php foreach ($sdSteps as $i => $step): ?><button type="button" class="sd-hdot<?= $i === 0 ? ' is-on' : '' ?>" data-sd-pdot="<?= $i ?>" aria-label="<?= sd_e($step['num'] . '. ' . $step['title']) ?>"></button><?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ══ TECH STACK ORBIT ═════════════════════════════ -->
	<section class="sd-section sd-alt" id="tech-stack">
		<div class="sd-wrap">
			<div class="sd-tech-head" data-sd-reveal>
				<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
				<h2 class="sd-h2"><?= sd_e($sdTechHeading) ?></h2>
			</div>

			<div class="sd-orbit" data-sd-reveal style="--d:.1s">
				<div class="sd-orbit-ring sd-orbit-ring--1" aria-hidden="true"></div>
				<div class="sd-orbit-ring sd-orbit-ring--2" aria-hidden="true"></div>
				<div class="sd-orbit-core">
					<img src="<?= sd_e($shopifyLogo) ?>" alt="Shopify" width="52" height="52">
				</div>
				<div class="sd-orbit-spin">
					<ul class="sd-ring sd-ring--inner">
						<?php foreach ($sdTechInner as $i => $t): ?>
						<li style="--a:<?= -90 + $i * 90 ?>deg"><span class="sd-orbit-pos"><span class="sd-orbit-chip" data-name="<?= sd_e($t['name']) ?>" role="img" aria-label="<?= sd_e($t['name']) ?>"><span class="sd-orbit-logos" aria-hidden="true"><?php foreach ($t['logos'] as $logo): ?><img src="<?= sd_e($logo) ?>" alt="" width="28" height="28" loading="lazy"><?php endforeach; ?></span></span></span></li>
						<?php endforeach; ?>
					</ul>
					<ul class="sd-ring sd-ring--outer">
						<?php foreach ($sdTechOuter as $i => $t): ?>
						<li style="--a:<?= -45 + $i * 90 ?>deg"><span class="sd-orbit-pos"><span class="sd-orbit-chip" data-name="<?= sd_e($t['name']) ?>" role="img" aria-label="<?= sd_e($t['name']) ?>"><span class="sd-orbit-logos" aria-hidden="true"><?php foreach ($t['logos'] as $logo): ?><img src="<?= sd_e($logo) ?>" alt="" width="28" height="28" loading="lazy"><?php endforeach; ?></span></span></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ FAQ ══════════════════════════════════════════ -->
	<section class="sd-section" id="faq">
		<div class="sd-wrap sd-faq-grid">
			<div class="sd-faq-side" data-sd-reveal>
				<div class="sd-index" aria-hidden="true"><?= sd_next_index() ?></div>
				<h2 class="sd-h2"><?= sd_e($sdFaqHeading) ?></h2>
				<div class="sd-faq-glyph" aria-hidden="true">?</div>
			</div>
			<div data-sd-reveal style="--d:.1s">
				<?php foreach ($sdFaqs as $i => $faq): ?>
				<details class="sd-faq" name="sd-faq"<?= $i === 0 ? ' open' : '' ?>>
					<summary>
						<span class="sd-faq-num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
						<h3><?= sd_e($faq['q']) ?></h3>
						<span class="sd-faq-icon" aria-hidden="true"></span>
					</summary>
					<div class="sd-faq-body"><p><?= sd_e($faq['a']) ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ══ FINAL CTA ════════════════════════════════════ -->
	<section class="sd-section sd-cta sd-alt" id="cta-final">
		<div class="sd-wrap">
			<div class="sd-cta-card" data-sd-reveal>
				<div class="sd-cta-icon" aria-hidden="true"><img src="<?= sd_e($shopifyLogo) ?>" alt="" width="38" height="38"></div>
				<h2><?= sd_e($sdCta['heading']) ?></h2>
				<p><?= sd_e($sdCta['text']) ?></p>
				<a href="<?= sd_e($leadUrl) ?>" class="sd-btn sd-btn-white">
					<?= sd_e($sdCta['btn']) ?>
					<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
				</a>
			</div>
		</div>
	</section>
</main>

<script>
	document.addEventListener('DOMContentLoaded', () => {
		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		// phones get every timed animation about 30% quicker
		const onPhone = window.matchMedia('(max-width: 760px)').matches;
		const spd = (ms) => (onPhone ? Math.round(ms * 0.7) : ms);

		/* ── Scroll reveals ─────────────────────────── */
		const revealEls = document.querySelectorAll('[data-sd-reveal]');
		if ('IntersectionObserver' in window) {
			const io = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) return;
					entry.target.classList.add('is-in');
					io.unobserve(entry.target);
				});
			}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
			revealEls.forEach((el) => io.observe(el));
		} else {
			revealEls.forEach((el) => el.classList.add('is-in'));
		}

		/* ── Hero: a phone plays a purchase; the steps beside it jump to any moment ── */
		const heroStage = document.querySelector('[data-sd-hero]');
		if (heroStage) {
			const phone = heroStage.querySelector('[data-sd-phone]');
			const rail = Array.from(heroStage.querySelectorAll('[data-sd-rail]'));
			let step = 0;
			let timer = null;
			let userTookOver = false;

			const desk = heroStage.querySelector('[data-sd-desk]');
			const devs = heroStage.querySelector('[data-sd-devs]');
			const setStep = (i) => {
				step = i % rail.length;
				phone.dataset.step = String(step);
				if (desk) desk.dataset.step = String(step);
				rail.forEach((b, k) => b.setAttribute('aria-pressed', k === step ? 'true' : 'false'));
			};
			const stop = () => { userTookOver = true; clearInterval(timer); timer = null; };
			rail.forEach((b, k) => b.addEventListener('click', () => { stop(); setStep(k); }));
			heroStage.querySelectorAll('[data-sd-next]').forEach((b) => b.addEventListener('click', () => { stop(); setStep(step + 1); }));

			if (!reduceMotion && 'IntersectionObserver' in window) {
				new IntersectionObserver(([entry]) => {
					if (entry.isIntersecting && !userTookOver && !timer) timer = setInterval(() => setStep(step + 1), window.matchMedia('(max-width: 760px)').matches ? 2000 : 2800);
					if (!entry.isIntersecting && timer) { clearInterval(timer); timer = null; }
				}, { threshold: 0.35 }).observe(heroStage);
			}
			// Scale the computer + phone to the space they get: the column width, and on desktop
			// (where the hero is exactly one screen tall) its height too.
			const fitPhone = () => {
				const wrap = heroStage.querySelector('.sd-phone-wrap');
				const tall = window.matchMedia('(min-width: 1101px) and (min-height: 560px)').matches;
				const small = window.matchMedia('(max-width: 760px)').matches;
				let z = Math.min(1, wrap.clientWidth / 560);
				if (tall || small) z = Math.min(z, (wrap.clientHeight - 6) / 400);
				devs.style.setProperty('--pz', Math.max(0.3, z).toFixed(3));
			};
			fitPhone();
			window.addEventListener('resize', fitPhone);
			// The phone leans toward the pointer
			if (!reduceMotion && window.matchMedia('(pointer: fine) and (min-width: 1101px)').matches) {
				const wrap = heroStage.querySelector('.sd-phone-wrap');
				wrap.addEventListener('pointermove', (e) => {
					const r = wrap.getBoundingClientRect();
					phone.style.setProperty('--ry', `${(((e.clientX - r.left) / r.width) - 0.5) * 16}deg`);
					phone.style.setProperty('--rx', `${(0.5 - ((e.clientY - r.top) / r.height)) * 12}deg`);
				});
				wrap.addEventListener('pointerleave', () => { phone.style.setProperty('--ry', '0deg'); phone.style.setProperty('--rx', '0deg'); });
			}
		}

		/* ── Shared helpers for the two scroll stories ── */
		const pinnedMQ = window.matchMedia('(min-width: 1025px) and (min-height: 720px) and (prefers-reduced-motion: no-preference)');
		const clamp01 = (v) => Math.min(1, Math.max(0, v));
		const pressKey = (el, fn) => el.addEventListener('keydown', (e) => {
			if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fn(); }
		});
		const scrollBehavior = reduceMotion ? 'auto' : 'smooth';
		const renders = [];

		/* ── Who this is for: a film. On large screens the section pins and the scroll pulls the film sideways,
		      playing each act as it arrives; on smaller screens the acts stack and play as you reach them ── */
		const film = document.querySelector('[data-sd-film]');
		if (film) {
			const track = film.querySelector('.sd-film-track');
			const acts = Array.from(film.querySelectorAll('[data-sd-act]'));
			const scenes = acts.map((a) => a.querySelector('.sd-scn'));
			const pips = Array.from(film.querySelectorAll('[data-sd-pip]'));
			const phrases = Array.from(film.querySelectorAll('[data-sd-path]'));
			const prog = film.querySelector('.sd-film-prog i');
			const chips = Array.from(film.querySelectorAll('[data-sd-chip]'));
			// hold, then (move, play) for each act, measured in "beats" of scroll
			const segs = [['h', 0.4], ['m', 0.6], ['p', 1], ['m', 0.6], ['p', 1], ['m', 0.6], ['p', 1]];
			const total = segs.reduce((n, s) => n + s[1], 0);
			const ease = (t) => t * t * (3 - 2 * t);
			let current = -1;
			let wasSlider = false;

			const stateAt = (u) => {
				let s = 0, ai = -1;
				const a = [0, 0, 0];
				segs.forEach(([type, len]) => {
					const frac = clamp01(u / len);
					u -= len;
					if (type === 'm') s += ease(frac);
					else if (type === 'p') a[++ai] = frac;
				});
				return { s, a };
			};
			// scroll position (in beats) at the start of act k's playing time
			const uOfAct = (k) => (k === 0 ? 0 : 1.0 + (k - 1) * 1.6);
			const scrollToU = (u) => {
				const span = film.offsetHeight - window.innerHeight;
				window.scrollTo({ top: window.scrollY + film.getBoundingClientRect().top + (u / total) * span, behavior: scrollBehavior });
			};
			const slider = film.querySelector('[data-sd-acts]');
			const adots = Array.from(film.querySelectorAll('[data-sd-adot]'));
			let slideNow = -1;
			const slideTo = (i) => slider.scrollTo({ left: acts[i].offsetLeft - (slider.clientWidth - acts[i].offsetWidth) / 2, behavior: scrollBehavior });
			const goTo = (k) => {
				if (pinnedMQ.matches) { scrollToU(uOfAct(k) + (k ? 0.3 : 0)); return; }
				if (k === 0) { film.scrollIntoView({ behavior: scrollBehavior, block: 'start' }); return; }
				slider.scrollIntoView({ behavior: scrollBehavior, block: 'center' });
				slideTo(k - 1);
			};
			// swiped-to acts play once on arrival (0 to 1 over 2.6s)
			let tween = 0;
			const playAct = (i) => {
				cancelAnimationFrame(tween);
				if (reduceMotion) { scenes[i].style.setProperty('--a', '1'); syncChips(); return; }
				const t0 = performance.now();
				const step = (t) => {
					const k = Math.min(1, (t - t0) / spd(2600));
					scenes[i].style.setProperty('--a', k.toFixed(3));
					syncChips();
					if (k < 1) tween = requestAnimationFrame(step);
				};
				scenes[i].style.setProperty('--a', '0');
				tween = requestAnimationFrame(step);
			};
			const syncChips = () => chips.forEach((c) => c.classList.toggle('is-landed', (parseFloat(getComputedStyle(c).getPropertyValue('--k')) || 0) > 0.98));
			const onSlide = (announce) => {
				if (pinnedMQ.matches) return;
				const mid = slider.scrollLeft + slider.clientWidth / 2;
				let best = 0, bd = Infinity;
				acts.forEach((a, i) => { const d = Math.abs(a.offsetLeft + a.offsetWidth / 2 - mid); if (d < bd) { bd = d; best = i; } });
				if (best === slideNow && !announce) return;
				const first = slideNow === -1;
				slideNow = best;
				adots.forEach((d, i) => d.classList.toggle('is-on', i === best));
				highlight(best + 1);
				if (!first || announce) playAct(best);
			};
			slider.addEventListener('scroll', () => onSlide(false), { passive: true });
			adots.forEach((d, i) => d.addEventListener('click', () => slideTo(i)));
			if ('IntersectionObserver' in window) {
				new IntersectionObserver(([e], obs) => {
					if (!e.isIntersecting || pinnedMQ.matches) return;
					obs.disconnect();
					onSlide(true);
				}, { threshold: 0.4 }).observe(slider);
			}
			const highlight = (k) => {
				if (k === current) return;
				current = k;
				pips.forEach((p, i) => p.setAttribute('aria-pressed', i === k ? 'true' : 'false'));
				phrases.forEach((p, i) => p.setAttribute('aria-pressed', i === k - 1 ? 'true' : 'false'));
			};
			pips.forEach((p, i) => p.addEventListener('click', () => goTo(i)));
			phrases.forEach((p) => {
				const k = Number(p.dataset.sdPath) + 1;
				p.addEventListener('click', () => goTo(k));
				pressKey(p, () => goTo(k));
			});

			renders.push(() => {
				let a;
				if (pinnedMQ.matches) {
					const span = film.offsetHeight - window.innerHeight;
					const f = span > 0 ? clamp01(-film.getBoundingClientRect().top / span) : 0;
					const st = stateAt(f * total);
					a = st.a;
					film.style.setProperty('--s', st.s.toFixed(4));
					if (prog) prog.style.width = `${(f * 100).toFixed(2)}%`;
					highlight(Math.round(st.s));
				} else {
					// slider mode: acts finished by default; the one swiped to replays (see playAct)
					if (!wasSlider) { wasSlider = true; slideNow = -1; film.style.setProperty('--s', '0'); scenes.forEach((el) => el.style.setProperty('--a', '1')); syncChips(); onSlide(false); }
					return;
				}
				wasSlider = false;
				scenes.forEach((el, i) => el.style.setProperty('--a', reduceMotion ? '1' : a[i].toFixed(3)));
				syncChips();
			});
		}

		/* ── Hire: three sentences, three states of one diagram ── */
		const hire = document.querySelector('[data-sd-hire]');
		if (hire) {
			const box = hire.querySelector('[data-sd-hbox]');
			const stage = hire.querySelector('[data-sd-hstage]');
			const beats = Array.from(hire.querySelectorAll('[data-sd-beat]'));
			const dots = Array.from(hire.querySelectorAll('[data-sd-hdot]'));
			let active = -1;

			const center = (i) => {
				const r = beats[i].getBoundingClientRect();
				return r.top + r.height / 2;
			};
			// Large screens: the section pins and scroll progress drives the diagram. The three
			// sentences sit at these points of the pin's scroll; in between, the diagram morphs.
			const stops = [0, 0.1, 0.4, 0.52, 0.82, 1];
			const gAt = [0, 0, 1, 1, 2, 2];
			const gOfProgress = (p) => {
				for (let i = 0; i < stops.length - 1; i++) {
					if (p <= stops[i + 1]) {
						const t = (p - stops[i]) / (stops[i + 1] - stops[i] || 1);
						const e = t * t * (3 - 2 * t);
						return gAt[i] + (gAt[i + 1] - gAt[i]) * e;
					}
				}
				return 2;
			};
			const goBeat = (i) => {
				if (pinnedMQ.matches) {
					const span = hire.offsetHeight - window.innerHeight;
					window.scrollTo({ top: window.scrollY + hire.getBoundingClientRect().top + span * [0.03, 0.46, 0.92][i], behavior: scrollBehavior });
					return;
				}
				// aim the sentence at the middle of the space that isn't covered by the diagram
				const ref = refLine();
				window.scrollTo({ top: window.scrollY + center(i) - ref, behavior: scrollBehavior });
			};
			const refLine = () => {
				const vh = window.innerHeight;
				if (window.innerWidth > 1024) return vh * 0.5;
				const bottom = stage.getBoundingClientRect().bottom;
				return bottom + (vh - bottom) * 0.42;
			};
			dots.forEach((d, i) => d.addEventListener('click', () => goBeat(i)));
			beats.forEach((b, i) => b.querySelector('.sd-beat-copy').addEventListener('click', () => goBeat(i)));

			renders.push(() => {
				if (reduceMotion) { box.style.setProperty('--m', '1'); box.style.setProperty('--n', '1'); return; }
				let g = 0, near = 0;
				if (pinnedMQ.matches) {
					const span = hire.offsetHeight - window.innerHeight;
					g = gOfProgress(span > 0 ? clamp01(-hire.getBoundingClientRect().top / span) : 0);
					near = Math.round(g);
				} else {
					const ref = refLine();
					const c = beats.map((_, i) => center(i) - ref);   // > 0: still below the reference line
					// g runs 0..2, blending between the two sentences either side of the line
					if (c[0] <= 0) {
						g = beats.length - 1;
						for (let i = 0; i < beats.length - 1; i++) {
							if (c[i] <= 0 && c[i + 1] > 0) { g = i + (0 - c[i]) / (c[i + 1] - c[i]); break; }
						}
					}
					let nd = Infinity;
					c.forEach((v, i) => { if (Math.abs(v) < nd) { nd = Math.abs(v); near = i; } });
				}
				box.style.setProperty('--m', clamp01(g).toFixed(3));
				box.style.setProperty('--n', clamp01(g - 1).toFixed(3));
				if (near !== active) {
					active = near;
					beats.forEach((b, i) => b.classList.toggle('is-active', i === near));
					dots.forEach((d, i) => d.classList.toggle('is-on', i === near));
				}
			});
		}

		if (renders.length) {
			let ticking = false;
			const run = () => { ticking = false; renders.forEach((render) => render()); };
			const onScroll = () => { if (!ticking) { ticking = true; requestAnimationFrame(run); } };
			window.addEventListener('scroll', onScroll, { passive: true });
			window.addEventListener('resize', onScroll);
			pinnedMQ.addEventListener('change', run);
			run();
		}

		/* ── The numbers: pick a result on the left, explore it on the right ── */
		const dash = document.querySelector('[data-sd-dash]');
		if (dash) {
			const tabs = Array.from(dash.querySelectorAll('[role="tab"]'));
			const panes = tabs.map((t) => document.getElementById(t.getAttribute('aria-controls')));
			const NS = 'http://www.w3.org/2000/svg';
			const ease = (t) => t * t * (3 - 2 * t);
			let cur = -1;
			let manual = false;
			const timers = new Map();
			const tweens = new Map();

			const tween = (key, ms, apply) => {
				cancelAnimationFrame(tweens.get(key));
				if (reduceMotion) { apply(1); return; }
				const t0 = performance.now();
				const step = (t) => {
					const k = Math.min(1, (t - t0) / ms);
					apply(k);
					if (k < 1) tweens.set(key, requestAnimationFrame(step));
				};
				tweens.set(key, requestAnimationFrame(step));
			};

			/* lift curve */
			const buildLift = (pane) => {
				const svg = pane.querySelector('.sd-lift');
				if (!svg || svg.dataset.ready) return;
				svg.dataset.ready = '1';
				const max = Number(svg.dataset.max);
				const clip = svg.querySelector('.sd-lclip');
				const mark = svg.querySelector('[data-mark]');
				const dot = svg.querySelector('[data-dot]');
				const bub = svg.querySelector('[data-bub]');
				const label = bub.querySelector('text');
				const input = pane.querySelector('input[type="range"]');
				const at = (t) => {
					const e = ease(t);
					return { x: 44 + t * 336, y: 180 - e * 140, v: 1 + (max - 1) * e };
				};
				const setT = (t) => {
					t = Math.min(1, Math.max(0, t));
					const p = at(t);
					clip.setAttribute('width', String(p.x));
					mark.setAttribute('transform', `translate(${p.x.toFixed(1)} 0)`);
					dot.setAttribute('cy', p.y.toFixed(1));
					bub.setAttribute('transform', `translate(0 ${(p.y - 6).toFixed(1)})`);
					label.textContent = (t >= 0.995 ? max : p.v.toFixed(1)) + 'X';
					input.value = String(Math.round(t * 100));
					input.style.setProperty('--p', String(Math.round(t * 100)));
				};
				pane._setT = setT;
				pane._play = () => { tween(`lift${pane.id}`, spd(1700), (k) => setT(ease(k))); };
				const fromPointer = (e) => {
					cancelAnimationFrame(tweens.get(`lift${pane.id}`));
					const r = svg.getBoundingClientRect();
					const scale = Math.min(r.width / 400, r.height / 220);
					const ox = r.left + (r.width - 400 * scale) / 2;
					setT(((e.clientX - ox) / scale - 44) / 336);
				};
				svg.addEventListener('pointermove', fromPointer);
				input.addEventListener('input', () => { cancelAnimationFrame(tweens.get(`lift${pane.id}`)); setT(Number(input.value) / 100); });
				setT(reduceMotion ? 1 : 0);
			};

			const countUp = (pane) => {
				const el = pane.querySelector('[data-count]');
				const target = Number(pane.dataset.value);
				tween(`count${pane.id}`, spd(1400), (k) => { el.textContent = String(Math.round(target * (1 - Math.pow(1 - k, 3)))); });
			};

			const show = (i) => {
				if (i === cur) return;
				cur = i;
				tabs.forEach((t, k) => {
					const on = k === i;
					t.setAttribute('aria-selected', on ? 'true' : 'false');
					t.tabIndex = on ? 0 : -1;
				});
				panes.forEach((p, k) => {
					const on = k === i;
					p.classList.toggle('is-on', on);
					if (!on) { p.classList.remove('is-ready'); clearTimeout(timers.get(p.id)); }
				});
				const pane = panes[i];
				buildLift(pane);
				countUp(pane);
				if (pane._play) pane._play();
				clearTimeout(timers.get(pane.id));
				timers.set(pane.id, setTimeout(() => pane.classList.add('is-ready'), reduceMotion ? 0 : spd(1500)));
				// restart the auto-advance bar
				const fill = tabs[i].querySelector('.sd-dtab-fill');
				fill.style.animation = 'none';
				void fill.offsetWidth;
				fill.style.animation = '';
			};

			const choose = (i, focus) => {
				manual = true;
				dash.classList.remove('is-auto');
				show(i);
				if (focus) tabs[i].focus();
			};
			tabs.forEach((t, i) => {
				t.addEventListener('click', () => choose(i));
				t.addEventListener('keydown', (e) => {
					const keys = { ArrowRight: cur + 1, ArrowDown: cur + 1, ArrowLeft: cur - 1, ArrowUp: cur - 1, Home: 0, End: tabs.length - 1 };
					if (!(e.key in keys)) return;
					e.preventDefault();
					choose((keys[e.key] + tabs.length) % tabs.length, true);
				});
				t.querySelector('.sd-dtab-fill').addEventListener('animationend', () => {
					if (!manual && i === cur) show((cur + 1) % tabs.length);
				});
			});
			// touching the stage means someone is exploring: stop auto-advancing
			dash.querySelector('.sd-dstage').addEventListener('pointerdown', () => { manual = true; dash.classList.remove('is-auto'); });
			dash.addEventListener('pointerenter', () => dash.classList.add('is-paused'));
			dash.addEventListener('pointerleave', () => dash.classList.remove('is-paused'));

			show(0);
			if (!reduceMotion && 'IntersectionObserver' in window) {
				// nothing plays until the section is on screen; then it walks through the four results
				panes[0].classList.remove('is-on');
				cur = -1;
				new IntersectionObserver(([e], obs) => {
					if (!e.isIntersecting) return;
					obs.disconnect();
					dash.classList.add('is-auto');
					show(0);
				}, { threshold: 0.35 }).observe(dash);
			}
		}

		/* ── The hero button that jumps to the stores ── */
		document.querySelectorAll('[data-sd-jump]').forEach((a) => a.addEventListener('click', (e) => {
			const target = document.querySelector(a.getAttribute('href'));
			if (!target) return;
			e.preventDefault();
			target.scrollIntoView({ behavior: scrollBehavior, block: 'start' });
		}));

		/* ── Store previews: each screenshot in view scrolls itself down, pauses, and comes back up ── */
		const workCards = document.querySelectorAll('[data-sd-work]');
		if (workCards.length && !reduceMotion) {
			workCards.forEach((card) => {
				const view = card.querySelector('.sd-work-view');
				const img = view.querySelector('img');
				let inView = false;
				let timer = null;

				const scrollDown = () => {
					const dist = img.offsetHeight - view.clientHeight;
					if (dist <= 0) return false;
					card.style.setProperty('--dist', `${dist}px`);
					card.style.setProperty('--dur', `${(onPhone ? Math.max(2.8, dist / 460) : Math.max(4, dist / 320)).toFixed(1)}s`);
					card.classList.add('is-scrolling');
					return true;
				};
				const scrollUp = () => {
					clearTimeout(timer);
					card.classList.remove('is-scrolling');
				};

				// While the card is on screen: scroll down, pause, return to top, repeat.
				img.addEventListener('transitionend', (e) => {
					if (e.propertyName !== 'transform' || !inView) return;
					clearTimeout(timer);
					timer = setTimeout(() => {
						if (!inView) return;
						card.classList.contains('is-scrolling') ? scrollUp() : scrollDown();
					}, spd(1500));
				});
				new IntersectionObserver(([entry]) => {
					inView = entry.isIntersecting;
					if (!inView) { scrollUp(); return; }
					if (!card.classList.contains('is-scrolling')) {
						timer = setTimeout(() => { if (inView) scrollDown(); }, spd(600));
					}
				}, { threshold: 0.6 }).observe(card);
			});
		}

		/* ── Store carousel arrows ─────────────────── */
		const workTrack = document.querySelector('[data-sd-work-track]');
		if (workTrack) {
			const nav = document.querySelector('[data-sd-work-nav]');
			const prev = nav.querySelector('[data-sd-work-prev]');
			const next = nav.querySelector('[data-sd-work-next]');
			const step = () => {
				const card = workTrack.querySelector('.sd-work');
				return card ? card.getBoundingClientRect().width + parseFloat(getComputedStyle(workTrack).columnGap || 0) : workTrack.clientWidth;
			};
			const update = () => {
				nav.hidden = workTrack.scrollWidth <= workTrack.clientWidth + 4;
				prev.disabled = workTrack.scrollLeft <= 4;
				next.disabled = workTrack.scrollLeft + workTrack.clientWidth >= workTrack.scrollWidth - 4;
			};
			const behavior = reduceMotion ? 'auto' : 'smooth';
			prev.addEventListener('click', () => workTrack.scrollBy({ left: -step(), behavior }));
			next.addEventListener('click', () => workTrack.scrollBy({ left: step(), behavior }));
			// Auto-slide: move on every few seconds, wrap around at the end, and hold still while
			// someone is pointing at, focusing or touching the row (or after they swipe it themselves).
			if (!reduceMotion && 'IntersectionObserver' in window) {
				let timer = null;
				let held = false;
				let visible = false;
				let quietUntil = 0;
				const tick = () => {
					if (held || !visible || Date.now() < quietUntil || nav.hidden) return;
					if (workTrack.scrollLeft + workTrack.clientWidth >= workTrack.scrollWidth - 4) workTrack.scrollTo({ left: 0, behavior });
					else workTrack.scrollBy({ left: step(), behavior });
				};
				const hold = (v) => () => { held = v; };
				workTrack.addEventListener('pointerenter', hold(true));
				workTrack.addEventListener('pointerleave', hold(false));
				workTrack.addEventListener('focusin', hold(true));
				workTrack.addEventListener('focusout', hold(false));
				['touchstart', 'wheel'].forEach((t) => workTrack.addEventListener(t, () => { quietUntil = Date.now() + 9000; }, { passive: true }));
				nav.addEventListener('click', () => { quietUntil = Date.now() + 9000; });
				new IntersectionObserver(([e]) => {
					visible = e.isIntersecting;
					if (visible && !timer) timer = setInterval(tick, spd(4800));
					if (!visible && timer) { clearInterval(timer); timer = null; }
				}, { threshold: 0.4 }).observe(workTrack);
			}
			workTrack.addEventListener('scroll', update, { passive: true });
			workTrack.addEventListener('scrollend', update);   // after the snap to a card settles
			window.addEventListener('resize', update);
			update();
		}

		/* ── Process stepper (auto-advances until clicked) ── */
		const stepper = document.querySelector('[data-sd-stepper]');
		if (stepper) {
			const tabs = Array.from(stepper.querySelectorAll('[role="tab"]'));
			const panels = tabs.map((t) => document.getElementById(t.getAttribute('aria-controls')));
			let current = 0;

			const activate = (index, { focus = false, manual = false } = {}) => {
				current = (index + tabs.length) % tabs.length;
				if (manual) stepper.classList.add('is-manual');
				tabs.forEach((tab, i) => {
					const on = i === current;
					tab.setAttribute('aria-selected', on ? 'true' : 'false');
					tab.tabIndex = on ? 0 : -1;
					tab.classList.toggle('is-active', on);
					tab.classList.toggle('is-done', i < current);
					panels[i].classList.toggle('is-active', on);
				});
				const fill = tabs[current].querySelector('.sd-tab-fill');
				fill.style.animation = 'none';
				void fill.offsetWidth;
				fill.style.animation = '';
				if (focus) tabs[current].focus();
			};

			tabs.forEach((tab, i) => {
				tab.addEventListener('click', () => activate(i, { manual: true }));
				tab.addEventListener('keydown', (e) => {
					const keys = { ArrowRight: current + 1, ArrowLeft: current - 1, Home: 0, End: tabs.length - 1 };
					if (!(e.key in keys)) return;
					e.preventDefault();
					activate(keys[e.key], { focus: true, manual: true });
				});
				tab.querySelector('.sd-tab-fill').addEventListener('animationend', () => {
					if (!stepper.classList.contains('is-manual') && i === current) activate(current + 1);
				});
			});

			if (reduceMotion) stepper.classList.add('is-manual');
			stepper.classList.add('is-enhanced', 'is-paused');
			if ('IntersectionObserver' in window) {
				new IntersectionObserver(([entry]) => {
					stepper.classList.toggle('is-paused', !entry.isIntersecting);
				}, { threshold: 0.35 }).observe(stepper);
			}
			// Phones and tablets show the steps as a swipeable row; keep the dots in step with it
			const pdots = Array.from(document.querySelectorAll('[data-sd-pdot]'));
			const rowMQ = window.matchMedia('(max-width: 900px)');
			const rowTo = (i) => stepper.scrollTo({ left: panels[i].offsetLeft - 16, behavior: reduceMotion ? 'auto' : 'smooth' });
			pdots.forEach((d, i) => d.addEventListener('click', () => rowTo(i)));
			stepper.addEventListener('scroll', () => {
				if (!rowMQ.matches) return;
				let best = 0, bd = Infinity;
				panels.forEach((p, i) => { const d = Math.abs(p.offsetLeft - 16 - stepper.scrollLeft); if (d < bd) { bd = d; best = i; } });
				pdots.forEach((d, i) => d.classList.toggle('is-on', i === best));
			}, { passive: true });
			activate(0);
		}
	});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
