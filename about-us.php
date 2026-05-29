<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'about-us');
$pageTitle = $_seo['meta_title'] ?: 'About Us – Digifyce | Performance Marketing & Growth Agency';
$pageDescription = $_seo['meta_description'] ?: 'About Digifyce: a strategic branding and growth agency helping D2C brands, e-commerce businesses, startups, and growing companies scale with strategy, creativity, and performance.';
$bodyClass = 'about-creative';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo          = Database::getInstance();
$ab_hero       = $_pdo->query("SELECT * FROM about_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ab_stats      = $_pdo->query("SELECT * FROM about_hero_stats WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_sh         = [];
foreach ($_pdo->query("SELECT * FROM about_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $_r) { $ab_sh[$_r['slug']] = $_r; }
$ab_why_cards  = $_pdo->query("SELECT * FROM about_why_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_who_cards  = $_pdo->query("SELECT * FROM about_who_sub_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_what       = $_pdo->query("SELECT * FROM about_what_we_do WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_pillars    = $_pdo->query("SELECT * FROM about_approach_pillars WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_why_digi   = $_pdo->query("SELECT * FROM about_why_digi_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ab_mv         = $_pdo->query("SELECT * FROM about_mission_vision WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ab_cta        = $_pdo->query("SELECT * FROM about_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];

$extraHead = <<<'HTML'
<style>
	@keyframes slideInLeft {
		from { opacity: 0; transform: translateX(-60px); }
		to { opacity: 1; transform: translateX(0); }
	}
	@keyframes slideInRight {
		from { opacity: 0; transform: translateX(60px); }
		to { opacity: 1; transform: translateX(0); }
	}
	@keyframes slideInUp {
		from { opacity: 0; transform: translateY(40px); }
		to { opacity: 1; transform: translateY(0); }
	}
	@keyframes fadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	.sculpted-text {
		background: linear-gradient(135deg, #ffffff 0%, #0066ff 50%, #7dd3fc 100%);
		-webkit-background-clip: text;
		background-clip: text;
		-webkit-text-fill-color: transparent;
		font-weight: 900;
		letter-spacing: -0.02em;
	}

	.about-stat-box {
		border: 2px solid rgba(0,102,255,0.2);
		background: linear-gradient(135deg, rgba(0,102,255,0.06), rgba(255,255,255,0.03));
		backdrop-filter: blur(16px);
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.about-stat-box:hover {
		border-color: rgba(0,102,255,0.6);
		background: linear-gradient(135deg, rgba(0,102,255,0.12), rgba(0,102,255,0.05));
		transform: translateY(-6px);
	}

	.anim-stagger-1 { animation: slideInUp 0.8s ease-out 0.1s both; }
	.anim-stagger-2 { animation: slideInUp 0.8s ease-out 0.2s both; }
	.anim-stagger-3 { animation: slideInUp 0.8s ease-out 0.3s both; }
	.anim-stagger-4 { animation: slideInUp 0.8s ease-out 0.4s both; }
	.anim-stagger-5 { animation: slideInUp 0.8s ease-out 0.5s both; }
	.anim-stagger-6 { animation: slideInUp 0.8s ease-out 0.6s both; }

	.split-column {
		animation: slideInLeft 1.0s ease-out both;
	}
	.split-column:nth-child(2) {
		animation: slideInRight 1.0s ease-out both;
	}

	.counter-number { font-size: 3.5rem; font-weight: 700; line-height: 1; }

	.feature-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
		gap: 1.5rem;
	}

	@media (max-width: 768px) {
		.counter-number { font-size: 2.25rem; }
		.mobile-scroll-grid {
			display: flex !important;
			flex-wrap: nowrap !important;
			overflow-x: auto !important;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			margin: 0 -1.25rem;
			padding: 0 1.25rem 1.5rem;
			gap: 1rem;
		}
		.mobile-scroll-grid::-webkit-scrollbar {
			display: none;
		}
		.mobile-scroll-grid > * {
			flex: 0 0 85vw !important;
			scroll-snap-align: start;
		}
	}

	/* Mobile-first layout fixes for content density and touch scrolling */
	.about-hero {
		background-attachment: scroll;
	}

	@media (max-width: 1024px) {
		.about-hero {
			min-height: auto;
			padding-top: 7rem;
			padding-bottom: 3rem;
			background-attachment: scroll !important;
		}

		.about-hero h1 {
			font-size: clamp(2.2rem, 9.5vw, 3.6rem);
			line-height: 0.95;
			margin-bottom: 1.25rem;
		}

		.about-hero .split-column {
			animation: none;
		}
	}

	@media (max-width: 768px) {
		.about-section {
			padding-top: 4rem;
			padding-bottom: 4rem;
		}

		.about-hero {
			overflow: hidden;
		}

		.about-hero .max-w-7xl {
			padding-left: 0.1rem;
			padding-right: 0.1rem;
		}

		.about-hero .split-column {
			min-width: 0;
		}

		.about-hero h1 {
			font-size: clamp(2rem, 9.2vw, 2.85rem);
			line-height: 0.94;
			margin-bottom: 1rem;
		}

		.about-hero .anim-stagger-3 {
			font-size: 0.96rem;
			line-height: 1.65;
			margin-bottom: 1.5rem;
		}

		.about-hero .anim-stagger-4 {
			width: 100%;
		}

		.about-hero .anim-stagger-4 a {
			width: 100%;
		}

		.mobile-scroll-grid {
			scroll-padding-left: 1.25rem;
			scroll-padding-right: 1.25rem;
		}

		.mobile-scroll-grid > * {
			flex-basis: 88vw !important;
			min-width: 0;
		}

		.about-hero [data-mobile-slider-group] {
			min-width: 0;
			overflow: hidden;
		}
	}

	@media (max-width: 480px) {
		.mobile-scroll-grid > * {
			flex-basis: 90vw !important;
		}

		.about-hero {
			padding-top: 6.5rem;
		}

		.about-hero h1 {
			font-size: clamp(2rem, 10vw, 2.8rem);
		}
	}

	/* Mobile-only slider for Why Digifyce cards */
	.why-slider-controls {
		display: none;
	}

	.about-stats-controls {
		display: none;
	}

	@media (max-width: 768px) {
		.about-stats-slider {
			display: flex !important;
			flex-wrap: nowrap !important;
			overflow-x: auto !important;
			scroll-snap-type: x mandatory;
			-webkit-overflow-scrolling: touch;
			scroll-behavior: smooth;
			gap: 1rem;
			padding-bottom: 0.5rem;
			-ms-overflow-style: none;
			scrollbar-width: none;
			margin: 0;
			scroll-padding-left: 0;
			scroll-padding-right: 0;
			padding-left: 0;
			padding-right: 0;
			max-width: 100%;
		}

		.about-stats-slider::-webkit-scrollbar {
			display: none;
		}

		.about-stats-slider .about-stat-box {
			flex: 0 0 calc(100% - 1.25rem);
			scroll-snap-align: start;
			padding: 1.1rem;
		}

		.about-stats-slider .about-stat-box .text-sm {
			font-size: 0.82rem;
			line-height: 1.52;
		}

		.about-stats-controls {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 0.75rem;
			margin-top: 1rem;
		}

		.why-digifyce-slider {
			display: flex !important;
			flex-wrap: nowrap !important;
			overflow-x: auto !important;
			scroll-snap-type: x mandatory;
			-webkit-overflow-scrolling: touch;
			scroll-behavior: smooth;
			gap: 1rem;
			padding-bottom: 0.5rem;
			-ms-overflow-style: none;
			scrollbar-width: none;
		}

		.why-digifyce-slider::-webkit-scrollbar {
			display: none;
		}

		.why-digifyce-slider .about-card {
			flex: 0 0 88%;
			scroll-snap-align: start;
		}

		/* Avoid inherited negative margins from .mobile-scroll-grid that cause page overflow */
		#why-digifyce {
			overflow-x: clip;
		}

		#why-digifyce .why-digifyce-slider.mobile-scroll-grid {
			margin: 0;
			padding: 0 0 0.5rem;
			scroll-padding-left: 0;
			scroll-padding-right: 0;
			max-width: 100%;
		}

		#why-digifyce .why-digifyce-right {
			min-width: 0;
			overflow: hidden;
		}

		#why-digifyce .why-digifyce-slider .about-card {
			flex: 0 0 calc(100% - 2.25rem);
			min-width: 0;
		}

		.why-slider-controls {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 0.75rem;
			margin-top: 1rem;
		}

		.why-slider-nav {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 2.4rem;
			height: 2.4rem;
			border-radius: 9999px;
			border: 1px solid rgba(255,255,255,0.2);
			background: rgba(255,255,255,0.04);
			color: #fff;
			font-size: 1rem;
		}

		.why-slider-nav:disabled {
			opacity: 0.45;
		}

		.why-slider-dots {
			display: flex;
			align-items: center;
			gap: 0.4rem;
			flex-wrap: wrap;
		}

		.why-slider-dot {
			width: 0.45rem;
			height: 0.45rem;
			border-radius: 9999px;
			background: rgba(148,163,184,0.45);
			border: 0;
			padding: 0;
		}

		.why-slider-dot.is-active {
			background: var(--electric-blue);
			box-shadow: 0 0 0 4px rgba(0,102,255,0.18);
		}
	}
</style>
HTML;

include __DIR__ . '/app/views/header.php';
?>


<!-- HERO: BOLD & CREATIVE -->
<section class="about-hero relative min-h-screen flex items-center pt-20 px-4 sm:px-6 lg:px-8 overflow-hidden"
	style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('public/assets/img/group-img.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
	<div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/30 to-black opacity-80"></div>
	<div class="absolute inset-0"
		style="background: radial-gradient(circle at 10% 50%, rgba(0,102,255,0.22), transparent 40%), radial-gradient(circle at 90% 50%, rgba(125,211,252,0.14), transparent 45%);">
	</div>

	<div class="max-w-7xl mx-auto w-full relative z-10">
		<div class="grid md:grid-cols-12 gap-6 lg:gap-12 items-center">
			<div class="md:col-span-6 split-column">


				<h1
					class="anim-stagger-2 text-5xl sm:text-6xl lg:text-5xl xl:text-6xl font-black tracking-tighter leading-[0.88] mb-6 lg:mb-8">
					<span class="block text-white"><?= htmlspecialchars($ab_hero['h1_line1'] ?? 'Building Brands That') ?></span>
					<span class="sculpted-text"><?= htmlspecialchars($ab_hero['h1_line2_accent'] ?? 'Perform') ?></span>
					<span class="block text-white"><?= htmlspecialchars($ab_hero['h1_line3'] ?? 'Scale and Lead') ?></span>
				</h1>

				<p class="anim-stagger-3 max-w-xl text-base sm:text-lg text-slate-300 leading-relaxed mb-10">
					<?= htmlspecialchars($ab_hero['subtext'] ?? 'In today\'s fast-moving digital world, building a successful brand requires more than good design or marketing, it requires strategy, consistency, creativity and performance. At Digifyce, we help businesses create strong brand foundations and scalable growth systems that drive real business results.') ?>
				</p>

				<div class="anim-stagger-4 flex flex-col sm:flex-row gap-4">
					<a href="<?= htmlspecialchars($ab_hero['btn1_url'] ?? '#values') ?>"
						class="inline-flex items-center justify-center px-8 py-4 bg-[var(--electric-blue)] text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:shadow-lg hover:shadow-blue-600/50 transition-all group">
						<?= htmlspecialchars($ab_hero['btn1_label'] ?? 'Know More') ?>
						<span class="ml-2 group-hover:translate-x-1 transition-transform">→</span>
					</a>
					<a href="<?= htmlspecialchars($ab_hero['btn2_url'] ?? 'leadform.php') ?>"
						class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/20 text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:border-white/60 transition-colors">
						<?= htmlspecialchars($ab_hero['btn2_label'] ?? 'Connect Today') ?>
					</a>
				</div>
			</div>

			<div class="md:col-span-6 split-column mt-10 md:mt-0" data-mobile-slider-group>
				<?php
				$_stat_defaults = [
					['badge'=>'Founded','number'=>'2022','description'=>'Digifyce was built with one clear vision: to help modern brands grow with purpose, precision, and performance.'],
					['badge'=>'Brands','number'=>'120+','description'=>'From branding and creative development to performance marketing, e-commerce growth, and marketplace management, we provide complete solutions.'],
					['badge'=>'Focus','number'=>'360°','description'=>'Our focus is simple: Build brands that do not just look good but perform better.'],
					['badge'=>'Standard','number'=>'ROI','description'=>'We are not just a service provider, we are your growth partner.'],
				];
				$_stats = !empty($ab_stats) ? $ab_stats : $_stat_defaults;
				$_stat_stagger = ['anim-stagger-1','anim-stagger-2','anim-stagger-3','anim-stagger-4'];
				$_stat_num_colors = ['text-white','text-[var(--electric-blue)]','text-white','text-[var(--electric-blue)]'];
				?>
				<div class="grid grid-cols-2 gap-4 lg:gap-6 mobile-scroll-grid about-stats-slider" data-slider-track>
					<?php foreach ($_stats as $_si => $_stat): ?>
					<div class="<?= $_stat_stagger[$_si % 4] ?> about-stat-box rounded-2xl p-6 lg:p-8">
						<div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3"><?= htmlspecialchars($_stat['badge']) ?></div>
						<div class="counter-number <?= $_stat_num_colors[$_si % 4] ?>"><?= htmlspecialchars($_stat['number']) ?></div>
						<div class="text-slate-400 text-sm mt-3"><?= htmlspecialchars($_stat['description']) ?></div>
					</div>
					<?php endforeach; ?>
				</div>
				<div class="about-stats-controls" data-slider-controls>
					<button type="button" class="why-slider-nav" data-slider-prev
						aria-label="Previous slide">&#8592;</button>
					<div class="why-slider-dots" data-slider-dots aria-label="Slider pagination"></div>
					<button type="button" class="why-slider-nav" data-slider-next
						aria-label="Next slide">&#8594;</button>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="why-choose-digifyce" class="about-section py-20 lg:py-32 bg-[#030508] relative overflow-hidden">

	<style>
		/* ── Word reveal ── */
		.wcd-word {
			display: inline-block;
			overflow: hidden;
			vertical-align: bottom;
		}

		.wcd-word-inner {
			display: inline-block;
			transform: translateY(110%);
			opacity: 0;
		}

		/* ── Interactive Spotlight Cards ── */
		.wcd-interactive-card {
			position: relative;
			background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
			border: 1px solid rgba(255, 255, 255, 0.08);
			backdrop-filter: blur(16px);
			border-radius: 1.75rem;
			padding: 2.25rem;
			overflow: hidden;
			transition: border-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
				transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1),
				box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
		}

		.wcd-interactive-card:hover {
			border-color: rgba(0, 102, 255, 0.4);
			transform: translateY(-6px);
			box-shadow: 0 20px 40px rgba(0, 102, 255, 0.08);
		}

		.wcd-card-glow {
			position: absolute;
			inset: 0;
			pointer-events: none;
			opacity: 0;
			background: radial-gradient(350px circle at var(--x, 50%) var(--y, 50%), rgba(0, 102, 255, 0.12), transparent 80%);
			transition: opacity 0.3s ease;
		}

		.wcd-interactive-card:hover .wcd-card-glow {
			opacity: 1;
		}

		/* ── Core Highlight Pillars ── */
		.wcd-highlight {
			color: #ffffff;
			font-weight: 600;
			transition: all 0.3s ease;
			display: inline-block;
			position: relative;
		}

		.wcd-interactive-card:hover .wcd-highlight {
			color: #7dd3fc;
			text-shadow: 0 0 10px rgba(125, 211, 252, 0.55);
		}

		/* Mobile specific touch behavior & Slider support */
		@media (max-width: 768px) {
			#wcd-cards-container {
				display: flex !important;
				flex-wrap: nowrap !important;
				overflow-x: auto !important;
				scroll-snap-type: x mandatory;
				-ms-overflow-style: none;
				scrollbar-width: none;
				margin: 0 -1rem;
				padding: 0 1rem 1rem;
				gap: 1rem;
			}

			#wcd-cards-container::-webkit-scrollbar {
				display: none;
			}

			.wcd-interactive-card {
				padding: 1.75rem;
				flex: 0 0 calc(100% - 1.5rem) !important;
				scroll-snap-align: start;
				min-width: 0;
			}

			.wcd-card-glow {
				opacity: 0.7;
				background: radial-gradient(200px circle at 50% 50%, rgba(0, 102, 255, 0.08), transparent 80%);
			}

			.wcd-highlight {
				color: #7dd3fc;
			}

			.wcd-slider-dot {
				width: 6px;
				height: 6px;
				border-radius: 999px;
				background: rgba(255, 255, 255, 0.2);
				border: 0;
				padding: 0;
				transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
			}

			.wcd-slider-dot.active {
				background: #0066ff;
				width: 20px;
				box-shadow: 0 0 8px #0066ff;
			}
		}
	</style>

	<!-- ── Ambient Glows ── -->
	<div class="absolute inset-0 pointer-events-none overflow-hidden">
		<div
			style="position:absolute;top:20%;left:20%;width:600px;height:600px;background:#0066ff;opacity:0.04;border-radius:50%;filter:blur(130px);transform:translate(-50%,-50%);">
		</div>
		<div
			style="position:absolute;top:80%;right:15%;width:500px;height:500px;background:#0066ff;opacity:0.03;border-radius:50%;filter:blur(110px);">
		</div>
	</div>

	<!-- ── Top divider ── -->
	<div class="absolute top-0 left-0 w-full h-[1px]"
		style="background:linear-gradient(90deg,transparent,rgba(255,255,255,0.08),transparent);"></div>

	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

		<!-- Headline Section -->
		<div class="max-w-4xl mx-auto text-center mb-16 lg:mb-24">
			<div id="wcd-eyebrow" class="inline-flex items-center gap-3 mb-6"
				style="opacity:0;transform:translateY(15px);">
				<span
					style="width:1.5rem;height:1px;background:linear-gradient(to right,transparent,#0066ff);display:block;"></span>
				<span
					style="font-size:0.75rem;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;color:#0066ff;"><?= htmlspecialchars($ab_sh['why_brands']['eyebrow'] ?? 'Choose Digifyce') ?></span>
				<span
					style="width:1.5rem;height:1px;background:linear-gradient(to left,transparent,#0066ff);display:block;"></span>
			</div>

			<h2 id="wcd-headline" class="font-extrabold tracking-tighter leading-[1.08] text-white"
				style="font-size:clamp(2.4rem,6.5vw,5rem);">
				<div class="wcd-word" style="margin-bottom:0.05em;"><span class="wcd-word-inner">Why</span></div>
				<div class="wcd-word" style="margin-bottom:0.05em;"><span class="wcd-word-inner">Brands</span></div>
				<div class="wcd-word" style="margin-bottom:0.05em;">
					<span class="wcd-word-inner"
						style="background:linear-gradient(135deg,#ffffff 0%,#0066ff 60%,#7dd3fc 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;">Choose</span>
				</div>
				<div class="wcd-word">
					<span class="wcd-word-inner"
						style="background:linear-gradient(135deg,#ffffff 0%,#0066ff 60%,#7dd3fc 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;">Digifyce</span>
				</div>
			</h2>
		</div>

		<!-- Two-column narrative cards -->
		<div class="grid lg:grid-cols-2 gap-6 lg:gap-10 items-stretch max-w-6xl mx-auto" id="wcd-cards-container">

			<?php
			$_wcd_defaults = [
				['card_number'=>'SYSTEM / 01','body_text'=>'Brands choose Digifyce because modern business growth requires more than basic marketing. We combine strategy, creativity, performance, and technology-based solutions to help brands build strong digital foundations and achieve scalable growth. Our focus is not just on visibility, but on creating systems that improve conversions, strengthen brand identity, and drive long-term business success.'],
				['card_number'=>'SYSTEM / 02','body_text'=>'At Digifyce, we understand the challenges brands face in competitive digital markets, from poor ad performance to slow e-commerce growth and weak customer retention. By combining branding, content, paid marketing, design, and technology under one growth-driven approach, we create solutions that deliver measurable results, profitability, and sustainable brand growth.'],
			];
			$_wcd_items = !empty($ab_why_cards) ? $ab_why_cards : $_wcd_defaults;
			foreach ($_wcd_items as $_wc):
			?>
			<div class="wcd-interactive-card flex flex-col justify-between"
				style="opacity:0;transform:translateY(35px);">
				<div class="wcd-card-glow"></div>
				<div>
					<div class="flex items-center justify-between border-b border-white/5 pb-4 mb-6">
						<div class="text-[10px] font-mono text-[#0066ff] tracking-widest"><?= htmlspecialchars($_wc['card_number']) ?></div>
						<div class="w-1.5 h-1.5 rounded-full bg-[#0066ff]"></div>
					</div>
					<p class="text-slate-400 text-[1.05rem] sm:text-[1.12rem] leading-[1.85]">
						<?= htmlspecialchars($_wc['body_text']) ?>
					</p>
				</div>
			</div>
			<?php endforeach; ?>

		</div>

		<!-- Slider Pagination Dots (Mobile Only) -->
		<div class="flex md:hidden items-center justify-center gap-2 mt-4" id="wcd-slider-dots">
			<button type="button" class="wcd-slider-dot active" aria-label="Go to slide 1"></button>
			<button type="button" class="wcd-slider-dot" aria-label="Go to slide 2"></button>
		</div>

		<!-- CTA Button -->
		<div class="flex justify-center mt-12 md:mt-16" id="wcd-cta-container"
			style="opacity:0;transform:translateY(25px);">
			<a href="<?= htmlspecialchars($ab_sh['why_brands']['btn_url'] ?? 'leadform.php') ?>"
				class="inline-flex items-center justify-center px-8 py-4 bg-[var(--electric-blue)] text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:shadow-lg hover:shadow-blue-600/50 transition-all group">
				<?= htmlspecialchars($ab_sh['why_brands']['btn_label'] ?? 'Work With Us Today') ?>
				<span class="ml-2 group-hover:translate-x-1 transition-transform">→</span>
			</a>
		</div>

	</div>

	<!-- Bottom divider -->
	<div class="absolute bottom-0 left-0 w-full h-[1px]"
		style="background:linear-gradient(90deg,transparent,rgba(255,255,255,0.08),transparent);"></div>

	<!-- ── Load GSAP & ScrollTrigger dynamically if missing ── -->
	<script>
		(function () {
			// Failsafe: Make sure section is visible if script fails or GSAP takes too long
			const failsafeTimeout = setTimeout(function () {
				const section = document.getElementById('why-choose-digifyce');
				if (section) {
					// Fallback to instantly showing everything
					const hiddenElements = section.querySelectorAll('[style*="opacity:0"], [style*="opacity: 0"], .wcd-word-inner');
					hiddenElements.forEach(function (el) {
						el.style.opacity = '1';
						el.style.transform = 'none';
					});
				}
			}, 1500);

			function initSection() {
				clearTimeout(failsafeTimeout);
				const section = document.getElementById('why-choose-digifyce');
				if (!section) return;

				if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
					return; // Let the fallback handle it
				}

				gsap.registerPlugin(ScrollTrigger);

				// Eyebrow reveal
				gsap.to('#wcd-eyebrow', {
					opacity: 1, y: 0, duration: 0.8, ease: 'power3.out',
					scrollTrigger: { trigger: '#wcd-eyebrow', start: 'top 92%' }
				});

				// Word-by-word reveal
				const wordInners = section.querySelectorAll('.wcd-word-inner');
				gsap.to(wordInners, {
					y: 0, opacity: 1,
					duration: 0.9,
					ease: 'power4.out',
					stagger: 0.12,
					scrollTrigger: { trigger: '#wcd-headline', start: 'top 88%' }
				});

				// Staggered Cards reveal
				const cards = section.querySelectorAll('.wcd-interactive-card');
				gsap.to(cards, {
					opacity: 1, y: 0, duration: 1, ease: 'power3.out', stagger: 0.2,
					scrollTrigger: { trigger: '#wcd-cards-container', start: 'top 82%' }
				});

				// CTA button reveal
				const cta = section.querySelector('#wcd-cta-container');
				if (cta) {
					gsap.to(cta, {
						opacity: 1, y: 0, duration: 1, ease: 'power3.out',
						scrollTrigger: { trigger: '#wcd-cta-container', start: 'top 90%' }
					});
				}

				// Spotlight interactive tracking
				cards.forEach(function (card) {
					card.addEventListener('mousemove', function (e) {
						const rect = card.getBoundingClientRect();
						const x = e.clientX - rect.left;
						const y = e.clientY - rect.top;
						card.style.setProperty('--x', x + 'px');
						card.style.setProperty('--y', y + 'px');
					});
				});

				// Mobile slider dot synchronization
				const container = section.querySelector('#wcd-cards-container');
				const dots = section.querySelectorAll('.wcd-slider-dot');
				if (container && dots.length > 0) {
					container.addEventListener('scroll', function () {
						const width = container.clientWidth;
						const index = Math.round(container.scrollLeft / width);
						dots.forEach((dot, idx) => {
							dot.classList.toggle('active', idx === index);
						});
					}, { passive: true });

					dots.forEach((dot, idx) => {
						dot.addEventListener('click', function () {
							const width = container.clientWidth;
							container.scrollTo({
								left: idx * width,
								behavior: 'smooth'
							});
						});
					});
				}
			}

			// Load GSAP & ScrollTrigger dynamically if missing
			if (typeof gsap === 'undefined') {
				const scriptGsap = document.createElement('script');
				scriptGsap.src = "https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js";
				scriptGsap.onload = function () {
					const scriptST = document.createElement('script');
					scriptST.src = "https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js";
					scriptST.onload = initSection;
					document.head.appendChild(scriptST);
				};
				document.head.appendChild(scriptGsap);
			} else if (typeof ScrollTrigger === 'undefined') {
				const scriptST = document.createElement('script');
				scriptST.src = "https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js";
				scriptST.onload = initSection;
				document.head.appendChild(scriptST);
			} else {
				// Both are already loaded
				initSection();
			}
		})();
	</script>

</section>

<section id="who-we-are" class="about-section py-16 sm:py-20 lg:py-28 bg-[var(--navy-black)]">
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-start">
			<div class="lg:col-span-4 about-reveal">
				<div class="text-xl uppercase tracking-[0.4em] text-slate-500 mb-4 sm:mb-6"><?= htmlspecialchars($ab_sh['who_we_are']['eyebrow'] ?? 'Who We Are') ?></div>
				<h2 class="text-3xl sm:text-4xl lg:text-6xl font-bold tracking-tighter leading-tight"><?= htmlspecialchars($ab_sh['who_we_are']['heading'] ?? 'We are focused on clarity, execution, and growth.') ?></h2>
			</div>

			<div class="lg:col-span-8 about-reveal min-w-0">
				<div class="about-glass rounded-2xl p-6 sm:p-8 lg:p-10 min-w-0">
					<p class="text-base sm:text-lg lg:text-xl text-slate-400 leading-relaxed max-w-4xl">
						<?= nl2br(htmlspecialchars($ab_sh['who_we_are']['sub_text'] ?? "We believe branding is not only about visuals and performance marketing is not only about running ads. Real growth happens when strategy, creativity, customer psychology, and data work together.\nThat is where we come in.")) ?>
					</p>
					<div class="mt-6 sm:mt-8 grid mobile-scroll-grid md:grid-cols-3 gap-4 sm:gap-5">
						<?php
						$_who_defaults = [
							['badge'=>'WHO WE ARE','text'=>'A strategic branding and growth agency built for businesses that want measurable success not just marketing activity.'],
							['badge'=>'OUR BELIEF','text'=>'Branding is not only about visuals and performance marketing is not only about running ads. Real growth happens when strategy, creativity, customer psychology, and data work together.'],
							['badge'=>'OUR FOCUS','text'=>'We help businesses identify growth opportunities, create strong brand positioning, and build systems that generate consistent revenue.'],
						];
						$_who_items = !empty($ab_who_cards) ? $ab_who_cards : $_who_defaults;
						foreach ($_who_items as $_wh):
						?>
						<div class="about-card rounded-xl p-5 border border-white/10 bg-white/5">
							<div class="text-[10px] uppercase tracking-[0.35em] text-slate-500 mb-3"><?= htmlspecialchars($_wh['badge']) ?></div>
							<p class="text-slate-300 leading-relaxed"><?= htmlspecialchars($_wh['text']) ?></p>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section id="what-we-do" class="about-section py-16 sm:py-20 lg:py-28 bg-[#030508]">
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
		<div class="about-reveal mb-8 sm:mb-10 lg:mb-14 text-center">
			<div class="text-xl uppercase tracking-[0.4em] text-slate-500 mb-4 sm:mb-6"><?= htmlspecialchars($ab_sh['what_we_do']['eyebrow'] ?? 'What We Do') ?></div>
			<h2 class="text-3xl sm:text-4xl lg:text-6xl font-bold tracking-tighter"><?= htmlspecialchars($ab_sh['what_we_do']['heading'] ?? 'Complete growth solutions for ambitious businesses.') ?></h2>
		</div>

		<div class="grid mobile-scroll-grid md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 lg:gap-6">
			<?php
			$_what_defaults = [
				['number'=>'01','title'=>'Strategy','description'=>'At Digifyce, we help brands build strong digital foundations through strategy, creativity, and performance-driven solutions.'],
				['number'=>'02','title'=>'Services','description'=>'Our services include D2C branding, commercial shoots, performance marketing, e-commerce development, marketplace management, content marketing, and creative development.'],
				['number'=>'03','title'=>'Building Brands','description'=>'We believe successful brands need more than marketing — they need the right strategy, clear positioning, and consistent execution.'],
				['number'=>'04','title'=>'E-commerce Development','description'=>'We work with D2C brands, e-commerce businesses, startups, and growing companies to create better customer experiences and scalable systems that support long-term business growth.'],
				['number'=>'05','title'=>'Marketplace Management','description'=>'Every service is designed to improve visibility, increase conversions, strengthen customer trust, and help businesses grow faster in today\'s competitive digital market.'],
				['number'=>'06','title'=>'Content Marketing','description'=>'At Digifyce, we focus on building brands that not only look good but also perform better, generate results, and create lasting value for long-term business growth.'],
			];
			$_what_items = !empty($ab_what) ? $ab_what : $_what_defaults;
			foreach ($_what_items as $_wi):
			?>
			<div class="about-card about-reveal rounded-2xl p-6 sm:p-7 border border-white/10 bg-white/[0.03]">
				<div class="text-sm uppercase tracking-[0.35em] text-[var(--electric-blue)] mb-4"><?= htmlspecialchars($_wi['number']) ?></div>
				<h3 class="text-2xl font-bold mb-3"><?= htmlspecialchars($_wi['title']) ?></h3>
				<p class="text-slate-400 leading-relaxed"><?= htmlspecialchars($_wi['description']) ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="about-reveal mt-8 sm:mt-10 text-center">
			<a href="<?= htmlspecialchars($ab_sh['what_we_do']['btn_url'] ?? '#our-approach') ?>"
				class="inline-flex items-center justify-center gap-3 px-8 sm:px-10 py-4 sm:py-5 bg-white text-[var(--navy-black)] font-bold uppercase tracking-[0.25em] text-xs sm:text-sm rounded-sm hover:bg-[var(--electric-blue)] hover:text-white transition-all">
				<?= htmlspecialchars($ab_sh['what_we_do']['btn_label'] ?? 'Helping your business grow stronger and faster') ?>
				<span class="material-symbols-outlined text-base">arrow_forward</span>
			</a>
		</div>
	</div>
</section>

<section id="our-approach" class="about-section py-20 lg:py-32 bg-[var(--navy-black)] relative overflow-hidden">
	<div
		class="absolute inset-0 bg-gradient-to-b from-transparent via-[var(--electric-blue)]/5 to-transparent opacity-50">
	</div>
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
		<div class="text-center max-w-3xl mx-auto mb-16 lg:mb-24 about-reveal">
			<div class="text-xl uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-4"><?= htmlspecialchars($ab_sh['approach']['eyebrow'] ?? 'Our Approach') ?></div>
			<h2 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tighter leading-tight mb-6"><?= htmlspecialchars($ab_sh['approach']['heading'] ?? 'We believe growth should be strategic, not random.') ?></h2>
			<p class="text-slate-400 text-lg leading-relaxed"><?= nl2br(htmlspecialchars($ab_sh['approach']['sub_text'] ?? "That is why every project begins with understanding your business, your audience, and your long-term goals. We do not believe in one-size-fits-all solutions. Every strategy we create is customized to your brand's needs, market position, and growth stage.\nOur approach is built on three strong pillars:")) ?></p>
		</div>

		<?php
		$_pillar_icons = ['architecture', 'palette', 'monitoring'];
		$_pillar_defaults = [
			['number'=>'1','badge'=>'THE RIGHT ROADMAP','title'=>'Strategy','description'=>'We identify the right direction, positioning, and growth roadmap for your business.'],
			['number'=>'2','badge'=>'TRUSTWORTHY VISUALS','title'=>'Creativity','description'=>'We create visuals, messaging, and experiences that make your brand memorable and trustworthy.'],
			['number'=>'3','badge'=>'MEASURABLE OUTCOMES','title'=>'Performance','description'=>'We focus on measurable outcomes, better visibility, stronger conversions and sustainable growth.'],
		];
		$_pillars = !empty($ab_pillars) ? $ab_pillars : $_pillar_defaults;
		?>
		<div class="grid mobile-scroll-grid md:grid-cols-3 gap-6 lg:gap-10">
			<?php foreach ($_pillars as $_pi => $_pl): ?>
			<div class="about-reveal group relative p-8 lg:p-10 rounded-3xl bg-white/[0.02] border border-white/[0.05] hover:border-[var(--electric-blue)]/40 hover:bg-white/[0.04] transition-all duration-500 overflow-hidden"<?= $_pi > 0 ? ' style="transition-delay:' . ($_pi * 100) . 'ms;"' : '' ?>>
				<div class="absolute right-4 top-4 text-[140px] font-black text-white/[0.02] group-hover:text-[var(--electric-blue)]/10 transition-colors duration-500 pointer-events-none select-none leading-none"><?= htmlspecialchars($_pl['number']) ?></div>
				<div class="w-12 h-12 rounded-full bg-[var(--electric-blue)]/10 flex items-center justify-center mb-8 group-hover:scale-110 group-hover:bg-[var(--electric-blue)]/20 transition-all duration-500">
					<span class="material-symbols-outlined text-[var(--electric-blue)]"><?= htmlspecialchars($_pillar_icons[$_pi] ?? 'star') ?></span>
				</div>
				<div class="text-[10px] font-bold text-slate-500 mb-3 tracking-[0.3em] uppercase"><?= htmlspecialchars($_pl['badge']) ?></div>
				<h3 class="text-2xl lg:text-3xl font-bold mb-4 text-white"><?= htmlspecialchars($_pl['title']) ?></h3>
				<p class="text-slate-400 leading-relaxed"><?= htmlspecialchars($_pl['description']) ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<p class="text-slate-400 text-lg leading-relaxed text-center pt-10"><?= htmlspecialchars($ab_sh['approach']['extra_text'] ?? 'This balance helps brands move from confusion to clarity and from effort to results.') ?></p>
	</div>
</section>

<section id="why-digifyce" class="about-section py-16 sm:py-20 lg:pb-20 lg:pt-0 bg-[#030508]">
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

		<div class="about-reveal mb-8 sm:mb-10 lg:mb-14 text-center">
			<div class="text-xl uppercase tracking-[0.4em] text-[#0066ff] mb-4 sm:mb-6"><?= htmlspecialchars($ab_sh['why_digi']['eyebrow'] ?? 'Why Brands Choose Digifyce') ?></div>
			<h2 class="text-3xl sm:text-4xl lg:text-6xl font-bold tracking-tighter"><?= htmlspecialchars($ab_sh['why_digi']['heading'] ?? 'What makes us different') ?></h2>
		</div>

		<div class="why-digifyce-right" data-mobile-slider-group>

			<div class="grid mobile-scroll-grid why-digifyce-slider sm:grid-cols-2 gap-6 sm:gap-8" data-slider-track>
				<?php
				$_wdigi_defaults = [
					['badge'=>'D2C-FOCUSED EXPERTISE','title'=>'Built for D2C','description'=>'We understand how digital-first brands grow in highly competitive markets.'],
					['badge'=>'STRATEGY + CREATIVITY + PERFORMANCE','title'=>'One System','description'=>'We combine branding, design, marketing, and growth under one unified system.'],
					['badge'=>'ROI-DRIVEN EXECUTION','title'=>'Performance First','description'=>'Every decision is made with performance and profitability in mind.'],
					['badge'=>'END-TO-END SUPPORT','title'=>'Full Journey','description'=>'From launching your brand to scaling your business, we support you at every step.'],
				];
				$_wdigi_items = !empty($ab_why_digi) ? $ab_why_digi : $_wdigi_defaults;
				foreach ($_wdigi_items as $_wd):
				?>
				<div class="about-card about-reveal rounded-2xl p-8 lg:p-10 border border-white/10 bg-white/[0.03]">
					<div class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#0066ff] mb-3"><?= htmlspecialchars($_wd['badge']) ?></div>
					<h3 class="text-2xl lg:text-3xl font-bold mb-4 text-white"><?= htmlspecialchars($_wd['title']) ?></h3>
					<p class="text-slate-400 leading-relaxed"><?= htmlspecialchars($_wd['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="why-slider-controls" data-slider-controls>
				<button type="button" class="why-slider-nav" data-slider-prev
					aria-label="Previous slide">&#8592;</button>
				<div class="why-slider-dots" data-slider-dots aria-label="Slider pagination"></div>
				<button type="button" class="why-slider-nav" data-slider-next aria-label="Next slide">&#8594;</button>
			</div>
		</div>
	</div>

</section>

<section class="about-section py-16 sm:py-20 lg:py-14 bg-[var(--navy-black)]">
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid mobile-scroll-grid lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-10">
			<div class="about-reveal about-glass rounded-2xl p-6 sm:p-8 lg:p-10">
				<div class="text-xl uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-4 sm:mb-6"><?= htmlspecialchars($ab_mv['mission_badge'] ?? 'Our Mission') ?></div>
				<h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter mb-4 sm:mb-5"><?= htmlspecialchars($ab_mv['mission_title'] ?? 'Meaningful, profitable and sustainable growth.') ?></h2>
				<p class="text-slate-400 leading-relaxed"><?= nl2br(htmlspecialchars($ab_mv['mission_text'] ?? 'Our mission is to help brands create meaningful, profitable, and sustainable growth through strategy-driven digital solutions.')) ?></p>
			</div>

			<div class="about-reveal about-glass rounded-2xl p-6 sm:p-8 lg:p-10">
				<div class="text-xl uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-4 sm:mb-6"><?= htmlspecialchars($ab_mv['vision_badge'] ?? 'Our Vision') ?></div>
				<h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter mb-4 sm:mb-5"><?= htmlspecialchars($ab_mv['vision_title'] ?? 'Brands that grow with consistency.') ?></h2>
				<p class="text-slate-400 leading-relaxed"><?= nl2br(htmlspecialchars($ab_mv['vision_text'] ?? 'Our vision is to build a future where brands grow with clarity, confidence, and consistency through marketing & technology-based solutions.')) ?></p>
			</div>
		</div>
	</div>
</section>

<section class="about-section py-16 sm:py-20 lg:py-24 bg-[#030508]">
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
		<div class="about-reveal">
			<div class="text-xl uppercase tracking-[0.4em] text-slate-500 mb-4 sm:mb-6"><?= htmlspecialchars($ab_cta['badge'] ?? "Let's Build Something Bigger") ?></div>
			<h2 class="text-4xl sm:text-5xl lg:text-7xl font-bold tracking-tighter leading-tight max-w-5xl mx-auto"><?= htmlspecialchars($ab_cta['heading'] ?? "Let's build a brand that performs.") ?></h2>
			<p class="mt-6 sm:mt-8 max-w-3xl mx-auto text-base sm:text-lg lg:text-xl text-slate-400 leading-relaxed">
				<?= nl2br(htmlspecialchars($ab_cta['description'] ?? "Growth does not happen by chance, it happens through the right strategy, strong execution, and consistent improvement.\n\nAt Digifyce, we help brands move forward with confidence by creating systems that support visibility, trust, conversions, and long-term success.\n\nWhether you are starting your journey or scaling to the next level, we are here to help you grow.")) ?>
			</p>
			<div class="mt-8 sm:mt-10">
				<a href="<?= htmlspecialchars($ab_cta['btn_url'] ?? 'leadform.php') ?>"
					class="inline-flex items-center justify-center gap-3 px-8 sm:px-12 py-4 sm:py-5 bg-[var(--electric-blue)] text-white font-bold uppercase tracking-[0.25em] text-xs sm:text-sm rounded-sm hover:opacity-90 transition-all">
					<?= htmlspecialchars($ab_cta['btn_label'] ?? 'Connect with Digifyce Today') ?>
					<span class="material-symbols-outlined text-base">arrow_forward</span>
				</a>
			</div>
		</div>
	</div>
</section>

<script>
	document.addEventListener('DOMContentLoaded', () => {
		const reveals = document.querySelectorAll('.about-reveal');

		if (window.gsap) {
			gsap.registerPlugin(ScrollTrigger);

			gsap.to(reveals, {
				opacity: 1,
				y: 0,
				duration: 0.9,
				stagger: 0.08,
				ease: 'power3.out',
				delay: 0.15
			});

			document.querySelectorAll('.about-section').forEach(section => {
				const items = section.querySelectorAll('.about-card, .about-pill, .about-glass');
				if (!items.length) return;

				gsap.fromTo(items,
					{ opacity: 0, y: 30 },
					{
						opacity: 1,
						y: 0,
						duration: 0.8,
						stagger: 0.06,
						ease: 'power3.out',
						scrollTrigger: {
							trigger: section,
							start: 'top 78%'
						}
					}
				);
			});
		} else {
			const observer = new IntersectionObserver(entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateY(0)';
					}
				});
			}, { threshold: 0.15 });

			reveals.forEach(el => {
				observer.observe(el);
				el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
			});
		}

		const initMobileSliders = () => {
			const groups = document.querySelectorAll('[data-mobile-slider-group]');
			if (!groups.length) return;

			const isMobile = () => window.innerWidth <= 768;

			groups.forEach((group) => {
				const slider = group.querySelector('[data-slider-track]');
				const controls = group.querySelector('[data-slider-controls]');
				if (!slider || !controls) return;

				const prevBtn = controls.querySelector('[data-slider-prev]');
				const nextBtn = controls.querySelector('[data-slider-next]');
				const dotsWrap = controls.querySelector('[data-slider-dots]');
				const slides = Array.from(slider.children);
				if (!prevBtn || !nextBtn || !dotsWrap || !slides.length) return;

				let dots = [];

				const currentIndex = () => {
					const left = slider.scrollLeft;
					let idx = 0;
					let minDiff = Number.POSITIVE_INFINITY;
					slides.forEach((slide, i) => {
						const diff = Math.abs(slide.offsetLeft - left);
						if (diff < minDiff) {
							minDiff = diff;
							idx = i;
						}
					});
					return idx;
				};

				const updateUI = () => {
					if (!isMobile()) {
						controls.style.display = 'none';
						return;
					}
					controls.style.display = 'flex';
					const idx = currentIndex();
					prevBtn.disabled = idx <= 0;
					nextBtn.disabled = idx >= slides.length - 1;
					dots.forEach((dot, i) => dot.classList.toggle('is-active', i === idx));
				};

				const goTo = (idx) => {
					const clamped = Math.max(0, Math.min(idx, slides.length - 1));
					slider.scrollTo({ left: slides[clamped].offsetLeft, behavior: 'smooth' });
				};

				dotsWrap.innerHTML = '';
				dots = slides.map((_, i) => {
					const dot = document.createElement('button');
					dot.type = 'button';
					dot.className = 'why-slider-dot';
					dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
					dot.addEventListener('click', () => goTo(i));
					dotsWrap.appendChild(dot);
					return dot;
				});

				prevBtn.addEventListener('click', () => goTo(currentIndex() - 1));
				nextBtn.addEventListener('click', () => goTo(currentIndex() + 1));
				slider.addEventListener('scroll', () => requestAnimationFrame(updateUI), { passive: true });
				window.addEventListener('resize', updateUI);
				updateUI();
			});
		};

		initMobileSliders();
	});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>