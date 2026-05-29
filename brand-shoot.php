<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'brand-shoot');
$pageTitle = $_seo['meta_title'] ?: 'Commercial Shoot Services – Digifyce | Product Photography, Ad Films & Brand Storytelling';
$pageDescription = $_seo['meta_description'] ?: 'Commercial shoot services by Digifyce: product photography, ad films, brand storytelling, social reels, and performance-ready visual content built to drive trust and conversions.';
$bodyClass = 'brand-shoot-page';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo        = Database::getInstance();
$cs_chals    = $_pdo->query("SELECT * FROM commercial_shoot_challenges WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_services = $_pdo->query("SELECT * FROM commercial_shoot_services WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_steps    = $_pdo->query("SELECT * FROM commercial_shoot_steps WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_impacts  = $_pdo->query("SELECT * FROM commercial_shoot_impacts WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_hero     = $_pdo->query("SELECT * FROM cs_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cs_hero_features = $_pdo->query("SELECT * FROM cs_hero_features WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_sh       = [];
foreach ($_pdo->query("SELECT * FROM cs_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $row) { $cs_sh[$row['slug']] = $row; }
$cs_approach = $_pdo->query("SELECT * FROM cs_approach_panels WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_why_bullets = $_pdo->query("SELECT * FROM cs_why_bullets WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cs_cta      = $_pdo->query("SELECT * FROM cs_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];

$extraHead = <<<'HTML'
<style>
	:root {
		--brand-bg: #05070a;
		--brand-bg-2: #030508;
		--brand-accent: #0066ff;
		--brand-accent-2: #0080ff;
		--brand-text: #e5eefc;
		--brand-muted: #94a3b8;
	}

	html { scroll-behavior: smooth; }

	.page-shell {
		background:
			radial-gradient(circle at top right, rgba(0, 102, 255, 0.12), transparent 30%),
			radial-gradient(circle at bottom left, rgba(0, 102, 255, 0.09), transparent 28%),
			linear-gradient(180deg, var(--brand-bg) 0%, var(--brand-bg-2) 100%);
	}

	.section-wrap {
		
		margin: 0 auto;
	}

	.eyebrow {
		display: inline-flex;
		align-items: center;
		gap: .6rem;
		padding: .45rem .9rem;
		border-radius: 9999px;
		border: 1px solid rgba(255,255,255,.08);
		background: rgba(255,255,255,.03);
		color: #9fb0c8;
		font-size: .65rem;
		font-weight: 800;
		letter-spacing: .28em;
		text-transform: uppercase;
	}

	.eyebrow .dot {
		width: .5rem;
		height: .5rem;
		border-radius: 50%;
		background: var(--brand-accent);
		box-shadow: 0 0 12px rgba(0,102,255,.45);
	}

	.hero-title {
		font-size: clamp(3rem, 6vw, 3.5rem);
		line-height: 1.02;
		font-weight: 900;
		letter-spacing: -0.04em;
		color: #fff;
	}

	.hero-title .accent {
		background: linear-gradient(90deg, #0080ff 0%, #0066ff 55%, #0052cc 100%);
		-webkit-background-clip: text;
		background-clip: text;
		-webkit-text-fill-color: transparent;
	}

	.hero-copy {
		color: #cbd5e1;
		line-height: 1.7;
		
		font-size: clamp(1rem, 2vw, 1.15rem);
	}

	.cta-row {
		display: flex;
		flex-wrap: wrap;
		gap: .9rem;
		margin-top: 1.75rem;
	}

	.btn-modern {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: .65rem;
		padding: .95rem 1.35rem;
		border-radius: .9rem;
		font-weight: 800;
		font-size: .78rem;
		letter-spacing: .24em;
		text-transform: uppercase;
		transition: transform .28s cubic-bezier(.22,.95,.3,1), box-shadow .28s cubic-bezier(.22,.95,.3,1), background .28s;
	}

	.btn-primary {
		background: linear-gradient(135deg, var(--brand-accent), #0052cc);
		color: #fff;
		box-shadow: 0 14px 30px rgba(0,102,255,.16);
	}

	.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(0,102,255,.26); }

	.btn-secondary {
		background: rgba(255,255,255,.03);
		border: 1px solid rgba(255,255,255,.12);
		color: #fff;
	}

	.btn-secondary:hover { transform: translateY(-2px); background: rgba(255,255,255,.06); }

	@media (min-width: 1024px) {

		.btn-modern span.material-symbols-outlined,
		.btn-modern svg,
		.btn-white span.material-symbols-outlined,
		.btn-white svg,
		.btn-ghost span.material-symbols-outlined,
		.btn-ghost svg {
			display: inline-block;
			opacity: 0;
			transform: translateY(20px);
			pointer-events: none;
		}

		.btn-modern:hover span.material-symbols-outlined,
		.btn-modern:hover svg,
		.btn-white:hover span.material-symbols-outlined,
		.btn-white:hover svg,
		.btn-ghost:hover span.material-symbols-outlined,
		.btn-ghost:hover svg {
			opacity: 1;
			animation: iconJump 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
		}

		@keyframes iconJump {
			0% {
				transform: translateY(100%);
				opacity: 0;
			}

			100% {
				transform: translateY(0);
				opacity: 1;
			}
		}
	}

	@media (max-width: 1023px) {

		.btn-modern span.material-symbols-outlined,
		.btn-modern svg,
		.btn-white span.material-symbols-outlined,
		.btn-white svg,
		.btn-ghost span.material-symbols-outlined,
		.btn-ghost svg {
			opacity: 1;
			transform: none;
		}
	}

	.hero-metrics {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: .9rem;
		margin-top: 1.9rem;
	}

	@media (max-width: 640px) { .hero-metrics { grid-template-columns: 1fr; } }

	.metric-card,
	.soft-card,
	.service-card,
	.process-card,
	.impact-card,
	.reason-card {
		background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.015));
		border: 1px solid rgba(255,255,255,.07);
		backdrop-filter: blur(16px);
		box-shadow: 0 18px 50px rgba(0,0,0,.18);
	}

	.metric-card {
		padding: 1rem 1.1rem;
		border-radius: 1rem;
	}

	.metric-value {
		display: block;
		font-size: clamp(1.7rem, 3vw, 2.4rem);
		font-weight: 900;
		color: #fff;
		line-height: 1;
	}

	.metric-value .accent { color: var(--brand-accent); }

	.metric-label {
		margin-top: .5rem;
		color: #9fb0c8;
		font-size: .82rem;
		line-height: 1.5;
	}

	.hero-layout {
		display: flex;
		flex-direction: column;
		gap: 2.1rem;
	}

	

	.hero-stage {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 1rem;
		align-items: stretch;
	}

	@media (max-width: 1024px) {
		.hero-stage { 
			grid-template-columns: repeat(3, minmax(240px, 1fr));
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding-bottom: 0.5rem;
		}
		.hero-stage::-webkit-scrollbar {
			display: none;
		}
		.hero-feature {
			scroll-snap-align: start;
			min-height: 180px;
		}
		.visual-title {
			font-size: 1.3rem;
		}
		.visual-copy {
			font-size: 0.85rem;
		}
	}

	.hero-feature {
		min-height: 220px;
	}

	.visual-panel {
		position: relative;
		min-height: auto;
		display: grid;
		grid-template-columns: 1fr;
		gap: .9rem;
	}

	@media (max-width: 768px) {
		.visual-panel { min-height: auto; grid-template-columns: 1fr; }
	}

	.visual-orb {
		position: absolute;
		border-radius: 50%;
		filter: blur(38px);
		pointer-events: none;
		z-index: 0;
	}

	.orb-a {
		top: -8%; right: -10%; width: 260px; height: 260px;
		background: radial-gradient(circle, rgba(0,102,255,.18), transparent 65%);
	}

	.orb-b {
		bottom: -6%; left: -10%; width: 220px; height: 220px;
		background: radial-gradient(circle, rgba(0,102,255,.14), transparent 65%);
	}

	.visual-card {
		position: relative;
		z-index: 1;
		border-radius: 1.25rem;
		padding: 1.25rem;
		min-height: 160px;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		transition: transform .3s cubic-bezier(.22,.95,.3,1), border-color .3s, background .3s;
	}

	.visual-card.tall {
		min-height: 190px;
	}

	.visual-card:hover {
		transform: translateY(-5px);
		border-color: rgba(0,102,255,.32);
		background: linear-gradient(180deg, rgba(0,102,255,.11), rgba(255,255,255,.02));
	}

	.visual-label {
		font-size: .68rem;
		text-transform: uppercase;
		letter-spacing: .28em;
		color: #7c8aa3;
		font-weight: 700;
	}

	.visual-title {
		font-weight: 900;
		font-size: 1.7rem;
		color: #fff;
		margin: .3rem 0;
	}

	.visual-copy {
		color: #9fb0c8;
		font-size: .92rem;
		line-height: 1.6;
	}

	.section-heading {
		font-size: clamp(1.9rem, 4vw, 3.8rem);
		line-height: 1.08;
		font-weight: 900;
		letter-spacing: -.04em;
		color: #fff;
	}

	.section-copy {
		color: #b9c6d8;
		line-height: 1.75;
		max-width: 70ch;
	}

	.panel-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 1rem;
	}

	@media (max-width: 768px) {
		.panel-grid { 
			grid-template-columns: repeat(4, minmax(260px, 1fr));
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding-bottom: 0.5rem;
		}
		.panel-grid::-webkit-scrollbar {
			display: none;
		}
		.soft-card {
			scroll-snap-align: start;
		}
		.soft-card.md\:col-span-2 {
			grid-column: span 1 / span 1 !important;
		}
	}

	.reveal {
		opacity: 0;
		transform: translateY(18px);
	}

	.reveal.visible {
		animation: reveal-up .7s cubic-bezier(.22,.95,.3,1) forwards;
	}

	.reveal-left { opacity: 0; transform: translateX(-16px); }
	.reveal-left.visible { animation: reveal-left .7s cubic-bezier(.22,.95,.3,1) forwards; }

	.reveal-right { opacity: 0; transform: translateX(16px); }
	.reveal-right.visible { animation: reveal-right .7s cubic-bezier(.22,.95,.3,1) forwards; }

	@keyframes reveal-up { to { opacity: 1; transform: none; } }
	@keyframes reveal-left { to { opacity: 1; transform: none; } }
	@keyframes reveal-right { to { opacity: 1; transform: none; } }

	.section-block {
		padding: 6.75rem 1rem;
	}

	.section-block.alt {
		background: rgba(255,255,255,.01);
	}

	.split-layout {
		display: grid;
		grid-template-columns: 50% minmax(0, 1fr);
		gap: 2.5rem;
		align-items: start;
	}

	/* Pin the visible side panel until its section completes scrolling */
	.split-layout > .reveal-left.visible,
	.split-layout > .reveal-right.visible {
		position: sticky;
		top: 7rem;
		align-self: start;
		height: max-content;
	}

	@media (max-width: 1024px) {
		.split-layout { grid-template-columns: 1fr; }
		.split-layout > .reveal-left.visible,
		.split-layout > .reveal-right.visible {
			position: static;
		}
	}

	.sticky-note {
		position: sticky;
		top: 7rem;
	}

	@media (max-width: 1024px) { .sticky-note { position: static; } }

	.soft-card {
		border-radius: 1.1rem;
		padding: 1.5rem;
	}

	.soft-list {
		display: grid;
		gap: .8rem;
	}

	@media (max-width: 768px) {
		.soft-list {
			display: flex;
			flex-wrap: nowrap;
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding: 0 2rem 0.5rem 0;
			margin-right: -1rem;
		}
		.soft-list::-webkit-scrollbar {
			display: none;
		}
		.soft-pill {
			flex: 0 0 72vw;
			min-width: 230px;
			scroll-snap-align: start;
			height: 100%;
		}
	}

	.soft-pill {
		display: flex;
		gap: .75rem;
		align-items: flex-start;
		border: 1px solid rgba(255,255,255,.08);
		background: rgba(255,255,255,.03);
		border-radius: .95rem;
		padding: .9rem 1rem;
		color: #d4deec;
	}

	.soft-pill .num {
		flex: 0 0 auto;
		width: 2rem; height: 2rem;
		border-radius: 9999px;
		display: grid; place-items: center;
		background: rgba(0,102,255,.12);
		color: var(--brand-accent);
		font-weight: 900;
		font-size: .8rem;
	}

	.service-card,
	.process-card,
	.impact-card,
	.reason-card {
		border-radius: 1.2rem;
		padding: 1.5rem;
		transition: transform .3s cubic-bezier(.22,.95,.3,1), border-color .3s;
	}

	.service-stack {
		display: grid;
		gap: 1rem;
	}

	@media (max-width: 768px) {
		.service-stack.mobile-scroll-grid {
			display: flex;
			flex-wrap: nowrap;
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding: 0 1.5rem 1rem 0;
			margin-right: -1.5rem;
		}
		.service-stack.mobile-scroll-grid::-webkit-scrollbar {
			display: none;
		}
		.service-stack.mobile-scroll-grid .service-band {
			flex: 0 0 75vw;
			scroll-snap-align: start;
		}
	}

	.service-band {
		display: grid;
		grid-template-columns: 210px minmax(0, 1fr);
		gap: 1rem;
		align-items: start;
		border-radius: 1.25rem;
		padding: 1.4rem;
		border: 1px solid rgba(255,255,255,.07);
		background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.015));
		box-shadow: 0 18px 50px rgba(0,0,0,.18);
	}

	@media (max-width: 768px) {
		.service-band { 
			grid-template-columns: 1fr; 
			height: 100%;
			border-bottom: 1px solid rgba(255,255,255,.07) !important;
		}
	}

	.service-band .stripe {
		width: 100%;
		height: 100%;
		min-height: 140px;
		border-radius: 1rem;
		overflow: hidden;
		position: relative;
		border: 1px solid rgba(0,102,255,.14);
	}
	.service-band .stripe img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		object-position: center;
		display: block;
		border-radius: 1rem;
		transition: transform .45s cubic-bezier(.22,.95,.3,1);
	}
	.service-band:hover .stripe img {
		transform: scale(1.05);
	}

	.service-band .chips {
		display: flex;
		flex-wrap: wrap;
		gap: .55rem;
		margin-top: .95rem;
	}

	.service-band .chip {
		padding: .48rem .72rem;
		border-radius: 9999px;
		border: 1px solid rgba(255,255,255,.08);
		background: rgba(255,255,255,.03);
		font-size: .72rem;
		letter-spacing: .12em;
		text-transform: uppercase;
		color: #d7e4f3;
	}

	.service-card:hover,
	.process-card:hover,
	.impact-card:hover,
	.reason-card:hover {
		transform: translateY(-4px);
		border-color: rgba(0,102,255,.22);
	}

	.timeline {
		position: relative;
		padding-left: 2.2rem;
		counter-reset: processStep;
	}

	.timeline::before {
		content: '';
		position: absolute;
		left: .85rem;
		top: .3rem;
		bottom: .3rem;
		width: 1px;
		background: linear-gradient(to bottom, transparent, rgba(0,102,255,.7), transparent);
	}

	.timeline .process-card {
		position: relative;
		counter-increment: processStep;
	}

	.timeline .process-card::before {
		content: '';
		position: absolute;
		left: -1.78rem;
		top: 1.05rem;
		width: 1.35rem;
		height: 1.35rem;
		border-radius: 50%;
		background: radial-gradient(circle at 35% 35%, #0080ff 0%, #0066ff 55%, #0052cc 100%);
		box-shadow: 0 0 0 4px rgba(0,102,255,.12), 0 0 18px rgba(0,102,255,.5);
		border: 1px solid rgba(255,255,255,.18);
		z-index: 1;
	}

	.timeline .process-card::after {
		content: counter(processStep, decimal-leading-zero);
		position: absolute;
		left: -1.78rem;
		top: 1.05rem;
		width: 1.35rem;
		height: 1.35rem;
		display: grid;
		place-items: center;
		font-size: .55rem;
		font-weight: 900;
		color: #fff;
		letter-spacing: .08em;
		z-index: 2;
	}

	.counter {
		font-variant-numeric: tabular-nums;
		font-feature-settings: 'tnum';
	}

	.counter-value {
		font-size: clamp(1.6rem, 3vw, 2.5rem);
		font-weight: 900;
		color: #fff;
	}

	.counter-note {
		color: #9fb0c8;
		font-size: .85rem;
		margin-top: .35rem;
		line-height: 1.5;
	}

	.wide-cta {
		text-align: center;
		border-radius: 1.5rem;
		padding: 2.2rem 1.2rem;
		border: 1px solid rgba(255,255,255,.07);
		background: radial-gradient(circle at top, rgba(0,102,255,.12), rgba(255,255,255,.02));
	}

	.parallax {
		will-change: transform;
	}

	@media (max-width: 768px) {
		.hero-layout,
		.split-layout { gap: .75rem; }
		.hero-title { font-size: clamp(2rem, 10vw, 3rem); }
		.btn-modern { width: 100%; }
		.cta-row { flex-direction: column; }
		.section-block { padding: 3rem 1rem; }
	}

	@media (prefers-reduced-motion: reduce) {
		*, *::before, *::after {
			animation-duration: .01ms !important;
			animation-iteration-count: 1 !important;
			transition-duration: .01ms !important;
			scroll-behavior: auto !important;
		}
	}

	/* ─── MOBILE SCROLL GRID ────────────── */
	@media (max-width: 768px) {
		.mobile-scroll-grid {
			display: flex !important;
			flex-wrap: nowrap !important;
			overflow-x: auto !important;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding-bottom: 1.5rem;
			margin: 0 -1rem;
			padding-left: 1rem;
		}

		.mobile-scroll-grid::-webkit-scrollbar {
			display: none;
		}

		.mobile-scroll-grid > * {
			flex: 0 0 75vw !important;
			scroll-snap-align: start;
			margin-right: 1rem;
			min-width: 260px;
		}
	}

/* CTA styles (adapted from d2c-branding) */
#cta-final {
	background: var(--brand-accent);
	padding: 6.5rem 0;
	position: relative;
	overflow: hidden;
}

@media (max-width: 768px) {
	#cta-final { padding: 4.2rem 0; }
}

.cta-bg-text {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	font-size: clamp(5rem, 16vw, 14rem);
	font-weight: 900;
	letter-spacing: -.04em;
	color: rgba(0,0,0,.12);
	white-space: nowrap;
	pointer-events: none;
	user-select: none;
}

.cta-inner { text-align: center; position: relative; z-index: 1; }
.cta-inner h2 { font-size: clamp(2rem, 4.5vw, 3.2rem); font-weight: 900; color: #fff; margin-bottom: 1rem; }
.cta-inner p { color: rgba(255,255,255,.85); max-width: 64ch; margin: 0 auto 1.8rem; line-height: 1.7; }

.btn-white {
	background: #ffffff;
	color: var(--brand-accent);
	font-weight: 800;
	padding: 14px 40px;
	border-radius: .6rem;
	display: inline-flex; align-items:center; gap:.6rem; text-decoration:none;
	box-shadow: 0 10px 36px rgba(0,0,0,.16);
}
.btn-white:hover { transform: translateY(-3px); box-shadow: 0 18px 48px rgba(0,0,0,.28); }
.btn-ghost { color: #fff; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.02); padding: 12px 30px; border-radius:.6rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; }

</style>
HTML;

include __DIR__ . '/app/views/header.php';
?>

<main class="page-shell text-white overflow-x-clip">
	<!-- HERO -->
	<section class="section-block pt-28 sm:pt-32 pb-16 sm:pb-20 relative overflow-hidden">
		<div class="section-wrap px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="hero-layout">
				<div class="hero-intro reveal-left">
					<div class="eyebrow mb-5"><span class="dot"></span><?= htmlspecialchars($cs_hero['eyebrow'] ?? 'High-Impact Visual Content That Builds Trust and Drives Conversions') ?></div>
					<h1 class="hero-title">
						<span><?= htmlspecialchars($cs_hero['h1_line1'] ?? 'Commercial Shoot') ?> </span>
						<br />
						<span class="accent"><?= htmlspecialchars($cs_hero['h1_line2_accent'] ?? 'Services India') ?></span>
					</h1>
					<p class="hero-copy mt-5"><?= htmlspecialchars($cs_hero['hero_copy'] ?? '') ?></p>
					<div class="cta-row">
						<a href="<?= htmlspecialchars($cs_hero['btn1_url'] ?? 'leadform.php') ?>"
							class="btn-modern btn-primary relative z-50 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer">
							<?= htmlspecialchars($cs_hero['btn1_label'] ?? "Let's Shoot Content That Sells") ?>
							<span class="material-symbols-outlined text-base">arrow_forward</span>
						</a>
						<a href="<?= htmlspecialchars($cs_hero['btn2_url'] ?? '#services') ?>" class="btn-modern btn-secondary"><?= htmlspecialchars($cs_hero['btn2_label'] ?? 'Explore Services') ?></a>
					</div>
				</div>

				<div class="hero-stage reveal-right relative">
					<div class="visual-orb orb-a parallax" data-parallax="0.04"></div>
					<div class="visual-orb orb-b parallax" data-parallax="-0.03"></div>
					<?php foreach ($cs_hero_features as $feat):
						$chips = json_decode($feat['chips_json'] ?? '[]', true) ?: [];
					?>
					<div class="visual-card service-card hero-feature">
						<div>
							<div class="visual-label"><?= htmlspecialchars($feat['label']) ?></div>
							<div class="visual-title"><?= htmlspecialchars($feat['title']) ?></div>
							<div class="visual-copy"><?= htmlspecialchars($feat['copy']) ?></div>
						</div>
						<?php if (!empty($chips)): ?>
						<div class="flex flex-wrap gap-2 mt-4">
							<?php foreach ($chips as $chip): ?>
							<span class="px-3 py-1 rounded-full border border-white/10 bg-white/5 text-[10px] uppercase tracking-[0.2em] text-slate-300"><?= htmlspecialchars($chip) ?></span>
							<?php endforeach; ?>
						</div>
						<?php elseif (!empty($feat['footer_text'])): ?>
						<div class="text-xs uppercase tracking-[0.28em] text-slate-500"><?= htmlspecialchars($feat['footer_text']) ?></div>
						<?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>


	<!-- Scrolling photo shoot -->
	<div class="mb-4 sm:mb-8 lg:mb-12 text-center mt-10 sm:mt-20">
		<h2
			class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
			Brand Studio</h2>
		<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Creative Story telling
		</div>
	</div>
	<section class="brand-story-scroll-container relative" id="brand-stories">
		<div class="brand-story-sticky">
			<div class="brand-story-item visible" id="story-apparel">
				<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
					<source src="public/assets/videos/Lushra.mp4" type="video/mp4" />
				</video>
				<div class="brand-video-overlay"></div>
				<div class="absolute bottom-12 left-12">
					<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
					<div class="text-3xl font-bold tracking-tighter text-white">Lushra</div>
				</div>
			</div>
			<div class="brand-story-item" id="story-jewelry">
				<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
					<source src="public/assets/videos/blackape.mp4" type="video/mp4" />
				</video>
				<div class="brand-video-overlay"></div>
				<div class="absolute bottom-12 left-12">
					<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
					<div class="text-3xl font-bold tracking-tighter text-white">Black Ape</div>
				</div>
			</div>
			<div class="brand-story-item" id="story-wellness">
				<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
					<source src="public/assets/videos/elther.mp4" type="video/mp4" />
				</video>
				<div class="brand-video-overlay"></div>
				<div class="absolute bottom-12 left-12">
					<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
					<div class="text-3xl font-bold tracking-tighter text-white">Elther</div>
				</div>
			</div>
			<!-- <div class="brand-story-item" id="story-aishwaryam">
				<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
					<source src="public/assets/videos/Sriaishwaryam.mp4" type="video/mp4" />
				</video>
				<div class="brand-video-overlay"></div>
				<div class="absolute bottom-12 left-12">
					<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
					<div class="text-3xl font-bold tracking-tighter text-white">Sri Aishwaryam</div>
				</div>
			</div> -->
			<div class="absolute right-12 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20">
				<div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-0"></div>
				<div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-1"></div>
				<div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-2"></div>

			</div>
		</div>
	</section>

	<!-- WHY IT MATTERS -->
	<section class="section-block alt">
		<div class="section-wrap px-4 sm:px-6 lg:px-8 split-layout">
			<?php $sh_why = $cs_sh['why'] ?? []; ?>
			<div class="sticky-note reveal-left">
				<div class="eyebrow mb-4"><span class="dot"></span> <?= htmlspecialchars($sh_why['eyebrow'] ?? 'Why it matters') ?></div>
				<h2 class="section-heading"><?= htmlspecialchars($sh_why['heading'] ?? 'Why Commercial Shoots Matter for Modern Brands') ?></h2>
				<p class="section-copy mt-5"><?= htmlspecialchars($sh_why['sub_text'] ?? '') ?></p>
			</div>

			<div class="space-y-4 sm:space-y-5 overflow-x-scroll" style="scrollbar-width: none; ">
				<div class="soft-card reveal">
					<h3 class="text-xl font-bold mb-4"><?= htmlspecialchars(($cs_sh['why_challenges']['heading'] ?? 'Common challenges include')) ?></h3>
					<div class="soft-list">
						<?php foreach ($cs_chals as $i => $ch): ?>
						<div class="soft-pill"><span class="num"><?= $i + 1 ?></span><span><?= htmlspecialchars($ch['text']) ?></span></div>
						<?php endforeach; ?>
					</div>
					<p class="mt-5 text-slate-300 leading-relaxed"><?= htmlspecialchars($cs_sh['why_challenges']['sub_text'] ?? 'Without strong visual content, even the best products struggle to perform.') ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- APPROACH -->
	<section class="section-block">
		<div class="section-wrap px-4 sm:px-6 lg:px-8 split-layout">
			<?php $sh_ap = $cs_sh['approach'] ?? []; ?>
			<div class="reveal-left">
				<div class="eyebrow mb-4"><span class="dot"></span> <?= htmlspecialchars($sh_ap['eyebrow'] ?? 'Our approach') ?></div>
				<h2 class="section-heading"><?= htmlspecialchars($sh_ap['heading'] ?? 'We treat every shoot like a conversion strategy.') ?></h2>
				<p class="section-copy mt-5"><?= htmlspecialchars($sh_ap['sub_text'] ?? '') ?></p>
				<?php if (!empty($sh_ap['extra_text'])): ?>
				<p class="section-copy mt-4"><?= htmlspecialchars($sh_ap['extra_text']) ?></p>
				<?php endif; ?>
			</div>

			<div class="panel-grid reveal-right">
				<?php foreach ($cs_approach as $panel): ?>
				<div class="soft-card<?= $panel['is_full_width'] ? ' md:col-span-2' : '' ?>">
					<div class="text-[10px] uppercase tracking-[0.3em] text-slate-500 mb-2"><?= htmlspecialchars($panel['step_label']) ?></div>
					<h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($panel['heading']) ?></h3>
					<p class="text-slate-300 leading-relaxed"><?= htmlspecialchars($panel['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- SERVICES -->
	<section id="services" class="section-block alt">
		<div class="section-wrap px-4 sm:px-6 lg:px-8">
			<?php $sh_svc = $cs_sh['services'] ?? []; ?>
			<div class="max-w-3xl mb-10 sm:mb-12">
				<div class="eyebrow mb-4"><span class="dot"></span> <?= htmlspecialchars($sh_svc['eyebrow'] ?? 'Our commercial shoot services') ?></div>
				<h2 class="section-heading"><?= htmlspecialchars($sh_svc['heading'] ?? 'Built for performance, not just aesthetics.') ?></h2>
				<p class="section-copy mt-5"><?= htmlspecialchars($sh_svc['sub_text'] ?? '') ?></p>
			</div>

			<div class="service-stack mobile-scroll-grid">
				<?php foreach ($cs_services as $i => $svc):
					$chips = json_decode($svc['chips_json'] ?? '[]', true) ?: [];
					$delay = $i > 0 ? ' style="animation-delay:' . ($i * 0.06) . 's;"' : '';
				?>
				<article class="service-band reveal"<?= $delay ?>>
					<div class="stripe">
						<img src="<?= $appUrl ?>/<?= htmlspecialchars($svc['img_src']) ?>"
							alt="<?= htmlspecialchars($svc['eyebrow']) ?>" loading="lazy">
					</div>
					<div>
						<div class="text-[10px] uppercase tracking-[0.3em] text-slate-500 mb-3"><?= htmlspecialchars($svc['eyebrow']) ?></div>
						<h3 class="text-2xl font-bold mb-3"><?= htmlspecialchars($svc['heading']) ?></h3>
						<p class="text-slate-300 leading-relaxed"><?= htmlspecialchars($svc['description']) ?></p>
						<div class="chips">
							<?php foreach ($chips as $chip): ?>
							<span class="chip"><?= htmlspecialchars($chip) ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- PROCESS -->
	<section class="section-block">
		<div class="section-wrap px-4 sm:px-6 lg:px-8">
			<?php $sh_proc = $cs_sh['process'] ?? []; ?>
			<div class="max-w-3xl mb-10 sm:mb-12">
				<div class="eyebrow mb-4"><span class="dot"></span> <?= htmlspecialchars($sh_proc['eyebrow'] ?? 'Process') ?></div>
				<h2 class="section-heading"><?= htmlspecialchars($sh_proc['heading'] ?? 'Structured from discovery to delivery.') ?></h2>
				<p class="section-copy mt-5"><?= htmlspecialchars($sh_proc['sub_text'] ?? '') ?></p>
			</div>

			<div class="timeline space-y-4 sm:space-y-5">
				<?php foreach ($cs_steps as $i => $step):
					$delay = $i > 0 ? ' style="animation-delay:' . ($i * 0.06) . 's;"' : '';
				?>
				<div class="process-card reveal"<?= $delay ?>>
					<div class="text-[10px] uppercase tracking-[0.3em] text-slate-500 mb-2">Step <?= htmlspecialchars($step['step_number']) ?></div>
					<h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($step['title']) ?></h3>
					<p class="text-slate-300 leading-relaxed"><?= htmlspecialchars($step['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- IMPACT + WHY US -->
	<section class="section-block alt">
		<div class="section-wrap px-4 sm:px-6 lg:px-8 split-layout">
			<?php $sh_wc = $cs_sh['why_choose'] ?? []; ?>
			<div class="sticky-note reveal-left">
				<div class="eyebrow mb-4"><span class="dot"></span> <?= htmlspecialchars($sh_wc['eyebrow'] ?? 'Our Advantage') ?></div>
				<h2 class="section-heading"><?= htmlspecialchars($sh_wc['heading'] ?? 'Why Choose Digifyce') ?></h2>
				<p class="section-copy mt-5"><?= htmlspecialchars($sh_wc['sub_text'] ?? '') ?></p>
				<?php if (!empty($cs_why_bullets)): ?>
				<div class="reason-card mt-6">
					<ul class="space-y-3 text-slate-300 leading-relaxed">
						<?php foreach ($cs_why_bullets as $b): ?>
						<li>• <?= htmlspecialchars($b['text']) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
				<div class="mt-8">
					<a href="<?= htmlspecialchars($cs_sh['why_choose']['btn_url'] ?? $cs_hero['btn1_url'] ?? 'leadform.php') ?>"
						class="btn-modern btn-primary relative z-50 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer">
						<?= htmlspecialchars($cs_sh['why_choose']['btn_label'] ?? 'Start Your Brand Shoot') ?>
						<span class="material-symbols-outlined text-base">arrow_forward</span>
					</a>
				</div>
			</div>

			<div class="panel-grid reveal-right">
				<?php foreach ($cs_impacts as $imp): ?>
				<div class="impact-card">
					<div class="text-[10px] uppercase tracking-[0.3em] text-slate-500 mb-2">Impact</div>
					<h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($imp['title']) ?></h3>
					<p class="text-slate-300 leading-relaxed"><?= htmlspecialchars($imp['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- CTA (updated to match d2c-branding) -->
	<section id="cta-final">
		<div class="cta-bg-text"><?= htmlspecialchars($cs_cta['bg_text'] ?? 'SHOOT') ?></div>
		<div class="section-wrap px-4 sm:px-6 lg:px-8">
			<div class="cta-inner reveal">
				<h2><?= htmlspecialchars($cs_cta['heading'] ?? 'Book Your Brand Shoot Today') ?></h2>
				<p class="section-copy mt-4 mx-auto"><?= htmlspecialchars($cs_cta['description'] ?? '') ?></p>
				<div style="margin-top:1.4rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
					<a href="<?= htmlspecialchars($cs_cta['btn1_url'] ?? 'leadform.php') ?>" class="btn-white">
						<?= htmlspecialchars($cs_cta['btn1_label'] ?? 'Book Your Brand Shoot Today') ?>
						<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
						</svg>
					</a>
					<a href="<?= htmlspecialchars($cs_cta['btn2_url'] ?? '#services') ?>" class="btn-ghost"><?= htmlspecialchars($cs_cta['btn2_label'] ?? 'View Services') ?></a>
				</div>
			</div>
		</div>
	</section>
</main>

<script>
	document.addEventListener('DOMContentLoaded', () => {
		const revealTargets = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) entry.target.classList.add('visible');
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

		revealTargets.forEach((el) => observer.observe(el));

		const parallaxEls = document.querySelectorAll('.parallax');
		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const isMobile = window.innerWidth <= 768;

		if (!reduceMotion && !isMobile && parallaxEls.length) {
			let ticking = false;

			const update = () => {
				const scrollY = window.scrollY;
				parallaxEls.forEach((el) => {
					const factor = parseFloat(el.dataset.parallax || '0.04');
					el.style.transform = `translate3d(0, ${scrollY * factor}px, 0)`;
				});
				ticking = false;
			};

			window.addEventListener('scroll', () => {
				if (!ticking) {
					requestAnimationFrame(update);
					ticking = true;
				}
			}, { passive: true });
		}

		const counters = document.querySelectorAll('.count');
		const counterObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting || entry.target.dataset.counted) return;

				const el = entry.target;
				const raw = el.dataset.target || el.textContent.trim();
				const numeric = parseFloat(raw.replace(/[^0-9.]/g, '')) || 0;
				const suffix = raw.replace(/[0-9.]/g, '') || '';
				const floatValue = raw.includes('x');
				const start = performance.now();
				const duration = 850;

				const step = (now) => {
					const progress = Math.min((now - start) / duration, 1);
					const eased = 1 - Math.pow(1 - progress, 3);
					const value = numeric * eased;
					el.textContent = floatValue ? `${value.toFixed(1)}${suffix}` : `${Math.floor(value).toLocaleString()}${suffix}`;

					if (progress < 1) {
						requestAnimationFrame(step);
					} else {
						el.textContent = raw;
						el.dataset.counted = '1';
					}
				};

				requestAnimationFrame(step);
				counterObserver.unobserve(el);
			});
		}, { threshold: 0.5 });

		counters.forEach((el) => counterObserver.observe(el));

		// Brand Story Scroll Logic
		window.addEventListener('scroll', () => {
			const container = document.getElementById('brand-stories');
			if (!container) return;
			const items = document.querySelectorAll('.brand-story-item');
			const dots = [document.getElementById('dot-0'), document.getElementById('dot-1'), document.getElementById('dot-2'), document.getElementById('dot-3')];
			const scrollPos = window.scrollY - container.offsetTop;
			const sectionHeight = container.offsetHeight / 5;
			let activeIndex = 0;

			if (scrollPos > sectionHeight * 0.5 && scrollPos < sectionHeight * 1.5) activeIndex = 0;
			else if (scrollPos >= sectionHeight * 1.5 && scrollPos < sectionHeight * 2.5) activeIndex = 1;
			else if (scrollPos >= sectionHeight * 2.5 && scrollPos < sectionHeight * 3.5) activeIndex = 2;
			else if (scrollPos >= sectionHeight * 3.5) activeIndex = 3;

			items.forEach((item, idx) => {
				if (idx === activeIndex) {
					item.classList.add('visible');
					dots[idx].classList.remove('bg-white/20');
					dots[idx].classList.add('bg-[var(--electric-blue)]', 'scale-150');
				} else {
					item.classList.remove('visible');
					dots[idx].classList.add('bg-white/20');
					dots[idx].classList.remove('bg-[var(--electric-blue)]', 'scale-150');
				}
			});
		});
	});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>