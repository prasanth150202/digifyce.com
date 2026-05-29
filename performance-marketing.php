<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'performance-marketing');
$pageTitle = $_seo['meta_title'] ?: 'Performance Marketing Services in India – Digifyce | SEO, PPC, Meta Ads & Growth';
$pageDescription = $_seo['meta_description'] ?: 'Data-driven performance marketing services in India by Digifyce. We build full-funnel growth systems across Meta Ads, Google Ads, SEO, PPC, CRO, retargeting, and automation.';
$bodyClass = 'performance-marketing-page';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');

require_once __DIR__ . '/config/database.php';
$_pdo            = Database::getInstance();
$pm_hero         = $_pdo->query("SELECT * FROM pm_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$pm_hero_metrics = $_pdo->query("SELECT * FROM pm_hero_metrics WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_benchmark    = $_pdo->query("SELECT * FROM pm_benchmark_groups WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$_sh_rows        = $_pdo->query("SELECT * FROM pm_section_headers")->fetchAll(PDO::FETCH_ASSOC);
$pmsh = [];
foreach ($_sh_rows as $_shr) { $pmsh[$_shr['slug']] = $_shr; }
unset($_sh_rows, $_shr);
$pm_challenges   = $_pdo->query("SELECT * FROM pm_challenges WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_approaches   = $_pdo->query("SELECT * FROM pm_approaches WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_services     = $_pdo->query("SELECT * FROM pm_services WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_leadgen_tabs = $_pdo->query("SELECT * FROM pm_leadgen_tabs WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_seo_panels   = $_pdo->query("SELECT * FROM pm_seo_panels WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_steps        = $_pdo->query("SELECT * FROM pm_steps WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);
$pm_impacts      = $_pdo->query("SELECT * FROM pm_impacts WHERE is_active=1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);

$extraHead = <<<'HTML'
<style>
	:root {
		--pm-bg: #030508;
		--pm-bg-alt: #030508;
		--pm-panel: rgba(255,255,255,0.02);
		--pm-line: rgba(255,255,255,0.1);
		--pm-text: #ffffff;
		--pm-muted: #94a3b8;
		--pm-accent: #0066ff;
		--pm-accent-2: #0080ff;
		--pm-success: #0066ff;
	}

	html { scroll-behavior: smooth; }

	.pm-shell {
		background: var(--pm-bg);
	}

	

	.pm-section { padding: 6.5rem 1rem; }
	.pm-section.tight { padding-top: 5rem; padding-bottom: 5rem; }
	.pm-section { position: relative; overflow: clip; }

	.pm-section::before {
		content: '';
		position: absolute;
		width: 440px;
		height: 440px;
		border-radius: 9999px;
		filter: blur(70px);
		opacity: .08;
		pointer-events: none;
		z-index: 0;
	}

	.pm-section > .pm-wrap { position: relative; z-index: 1; }
	.section-hero::before { right: -220px; top: -180px; background: #0066ff; }
	.section-problem::before { left: -220px; top: 30%; background: #0066ff; }
	.section-approach::before { right: -190px; bottom: -220px; background: #0066ff; }
	.section-services::before { left: -240px; bottom: -230px; background: #0066ff; }
	.section-process::before { right: -210px; top: 22%; background: #0066ff; }
	.section-impact::before { left: -180px; top: -180px; background: #0066ff; }
	.section-cta::before { right: -210px; bottom: -210px; background: #0066ff; }
	.section-problem,
	.section-approach,
	.section-process,
	.section-impact { overflow: visible; }

	/* Section background images removed */

	.hero-bg-graph {
		position: absolute;
		inset: 0;
		background: url('public/assets/img/graph_2x.png') no-repeat center ;
		background-size: 100% auto;
		opacity: 0.20;
		pointer-events: none;
		z-index: 0;
		mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
		-webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
	}

	.pm-kicker {
		display: inline-flex;
		align-items: center;
		gap: .55rem;
		border: 1px solid var(--pm-line);
		background: rgba(255,255,255,0.03);
		padding: .45rem .85rem;
		border-radius: 9999px;
		font-size: .62rem;
		letter-spacing: .24em;
		text-transform: uppercase;
		font-weight: 800;
		color: var(--pm-muted);
	}

	.pm-kicker i {
		width: .45rem;
		height: .45rem;
		border-radius: 9999px;
		background: var(--pm-accent);
		box-shadow: 0 0 14px rgba(0,163,255,.6);
	}

	.pm-title {
		margin-top: 1rem;
		color: #fff;
		font-size: clamp(3rem, 10vw, 3.5rem);
		line-height: 1.02;
		letter-spacing: -0.04em;
		font-weight: 900;
		width: 100%;
	}

	.pm-title .accent {
		color: #0066ff;
	}

	.pm-sub {
		color: #c7d3e3;
		max-width: 65ch;
		line-height: 1.75;
		font-size: clamp(1rem, 2vw, 1.12rem);
		margin-top: 1.2rem;
	}

	.pm-actions { margin-top: 1.8rem; display: flex; flex-wrap: wrap; gap: .8rem; }

	.pm-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: .55rem;
		padding: .9rem 1.3rem;
		border-radius: .85rem;
		text-transform: uppercase;
		letter-spacing: .2em;
		font-size: .72rem;
		font-weight: 800;
		transition: transform .28s cubic-bezier(.23,.95,.3,1), box-shadow .28s;
	}

	.pm-btn.primary {
		color: #fff;
		background: #0066ff;
		box-shadow: 0 14px 36px rgba(0,102,255,.2);
	}

	.pm-btn.ghost {
		color: #fff;
		border: 1px solid rgba(255,255,255,.16);
		background: rgba(255,255,255,.02);
	}

	.pm-btn:hover { transform: translateY(-2px); }

	@media (min-width: 1024px) {
		.pm-btn span.material-symbols-outlined,
		.pm-btn svg,
		.btn-white span.material-symbols-outlined,
		.btn-white svg,
		.btn-outline span.material-symbols-outlined,
		.btn-outline svg {
			display: inline-block;
			opacity: 0;
			transform: translateY(20px);
			pointer-events: none;
		}

		.pm-btn:hover span.material-symbols-outlined,
		.pm-btn:hover svg,
		.btn-white:hover span.material-symbols-outlined,
		.btn-white:hover svg,
		.btn-outline:hover span.material-symbols-outlined,
		.btn-outline:hover svg {
			opacity: 1;
			animation: iconJump 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
		}

		@keyframes iconJump {
			0% { transform: translateY(100%); opacity: 0; }
			100% { transform: translateY(0); opacity: 1; }
		}
	}

	@media (max-width: 1023px) {
		.pm-btn span.material-symbols-outlined,
		.pm-btn svg,
		.btn-white span.material-symbols-outlined,
		.btn-white svg,
		.btn-outline span.material-symbols-outlined,
		.btn-outline svg {
			opacity: 1;
			transform: none;
		}
	}

	.hero-grid {
		margin-top: 2.2rem;
		display: grid;
		grid-template-columns: 1.2fr .8fr;
		gap: 1rem;
	}

	.hero-card {
		border: 1px solid var(--pm-line);
		background: rgba(0,102,255,0.04);
		border-radius: 1rem;
		padding: 1.1rem;
		backdrop-filter: blur(14px);
		box-shadow: 0 20px 54px rgba(0,0,0,.24);
		transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
	}

	.hero-card:hover {
		transform: translateY(-4px);
		border-color: var(--pm-accent);
		box-shadow: 0 20px 40px rgba(0, 102, 255, 0.15);
	}

	.hero-card h3 { color: #fff; font-weight: 800; margin-bottom: .55rem; }
	.hero-card p { color: var(--pm-muted); line-height: 1.65; }

	.hero-metrics { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .7rem; }
	.metric {
		border: 1px solid rgba(255,255,255,.1);
		background: rgba(255,255,255,.02);
		border-radius: .9rem;
		padding: .95rem;
		transition: transform 0.3s ease, border-color 0.3s ease, background 0.3s ease;
	}

	.metric:hover {
		transform: translateY(-4px);
		border-color: rgba(0,102,255,0.4);
		background: rgba(0,102,255,0.05);
	}
	.metric .num { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 900; color: #fff; }
	.metric .txt { margin-top: .25rem; font-size: .8rem; color: var(--pm-muted); line-height: 1.5; }

	.split {
		display: grid;
		grid-template-columns: 40% minmax(0,1fr);
		gap: 2rem;
		align-items: start;
	}

	.split .sticky,
	.split .reveal-left {
		position: sticky;
		top: 7.5rem; /* Increased slightly for better clearance */
		align-self: start;
		z-index: 10;
	}

	.pm-img-compact {
		max-height: 180px;
		width: 100%;
		object-fit: cover;
		border-radius: 1rem;
		border: 1px solid rgba(255,255,255,0.1);
	}

	.panel {
		border: 1px solid var(--pm-line);
		background: var(--pm-panel);
		border-radius: 1.1rem;
		padding: 1.35rem;
		backdrop-filter: blur(14px);
		transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
	}

	.panel:hover {
		transform: translateY(-4px);
		border-color: var(--pm-accent);
		box-shadow: 0 15px 35px rgba(0, 102, 255, 0.15);
	}

	.panel h3, .panel h4 { color: #fff; font-weight: 800; }
	.panel p { color: #b8c7da; line-height: 1.72; }

	.challenge-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 1.25rem;
		text-align: left;
	}

	.challenge-card {
		border: 1px solid var(--pm-line);
		background: rgba(255, 255, 255, 0.02);
		border-radius: 1rem;
		padding: 1.5rem;
		display: flex;
		align-items: center;
		gap: 1rem;
		transition: transform 0.3s ease, border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
	}

	.challenge-card .icon {
		color: var(--pm-accent);
		font-size: 1.8rem;
		background: rgba(0,102,255,0.1);
		padding: 0.5rem;
		border-radius: 0.75rem;
		flex-shrink: 0;
	}

	.challenge-card p {
		color: #e2e8f0;
		font-size: 1.05rem;
		line-height: 1.4;
		margin: 0;
		font-weight: 500;
	}

	.challenge-card:hover {
		transform: translateY(-5px);
		border-color: var(--pm-accent);
		background: rgba(0, 102, 255, 0.05);
		box-shadow: 0 10px 30px rgba(0, 102, 255, 0.15);
	}

	.approach-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0,1fr));
		gap: 1rem;
	}

	.section-approach .approach-grid {
		grid-template-columns: repeat(12, minmax(0,1fr));
		gap: 1.1rem;
	}

	.approach-item {
		border: 1px solid var(--pm-line);
		background: rgba(0,102,255,0.04);
		border-radius: 1rem;
		padding: 1.1rem;
		position: relative;
		overflow: hidden;
		transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
	}

	.approach-item:hover {
		transform: translateY(-6px);
		border-color: var(--pm-accent);
		box-shadow: 0 15px 35px rgba(0,102,255,0.12);
	}

	.section-approach .approach-item { grid-column: span 6; }
	.section-approach .approach-item:nth-child(1),
	.section-approach .approach-item:nth-child(4) { grid-column: span 7; }
	.section-approach .approach-item:nth-child(2),
	.section-approach .approach-item:nth-child(3) { grid-column: span 5; }

	.section-approach .approach-item::after {
		content: '';
		position: absolute;
		inset: auto -40px -60px auto;
		width: 150px;
		height: 150px;
		border-radius: 9999px;
		background: radial-gradient(circle, rgba(0,102,255,.22), transparent 62%);
	}

	.section-approach .approach-item:nth-child(2n)::after {
		inset: -55px auto auto -45px;
	}

	.approach-item .idx {
		font-size: .68rem;
		letter-spacing: .22em;
		text-transform: uppercase;
		color: var(--pm-accent);
		font-weight: 800;
	}

	.approach-item h4 { margin-top: .35rem; margin-bottom: .4rem; }
	.approach-item p { color: var(--pm-muted); line-height: 1.65; }

	.service-ribbons {
		display: grid;
		grid-template-columns: repeat(6, minmax(0,1fr));
		gap: 1.05rem;
		perspective: 1200px;
	}

	.ribbon {
		display: flex;
		flex-direction: column;
		gap: .9rem;
		border: 1px solid var(--pm-line);
		background:
			linear-gradient(160deg, rgba(0,102,255,.08), rgba(255,255,255,.015) 55%),
			rgba(7,13,24,.78);
		border-radius: 1.1rem;
		padding: 1.15rem;
		position: relative;
		overflow: hidden;
		grid-column: span 3;
		isolation: isolate;
	}

	.section-services .ribbon {
		transition: transform .38s cubic-bezier(.2,.8,.2,1), border-color .32s, box-shadow .32s;
	}

	.section-services .ribbon:nth-child(1) {
		grid-column: span 6;
		padding: 1.35rem;
		background:
			linear-gradient(130deg, rgba(0,102,255,.2), rgba(255,255,255,.02) 52%),
			linear-gradient(165deg, rgba(9,16,30,.94), rgba(5,10,20,.9));
	}

	.section-services .ribbon:nth-child(2) {
		background:
			linear-gradient(140deg, rgba(0,102,255,.16), rgba(255,255,255,.015) 55%),
			rgba(6,13,24,.8);
	}

	.section-services .ribbon:nth-child(3) {
		background:
			linear-gradient(150deg, rgba(0,102,255,.2), rgba(255,255,255,.01) 58%),
			rgba(7,10,24,.82);
	}

	.section-services .ribbon:nth-child(4) {
		background:
			linear-gradient(140deg, rgba(0,102,255,.18), rgba(255,255,255,.012) 55%),
			rgba(6,11,22,.82);
	}

	.section-services .ribbon:nth-child(5) {
		background:
			linear-gradient(145deg, rgba(0,102,255,.16), rgba(255,255,255,.016) 56%),
			rgba(6,14,23,.82);
	}

	.section-services .ribbon:nth-child(6) {
		background:
			linear-gradient(145deg, rgba(0,102,255,.16), rgba(255,255,255,.014) 55%),
			rgba(7,12,22,.84);
	}

	.section-services .ribbon:nth-child(7) {
		background:
			linear-gradient(145deg, rgba(0,102,255,.18), rgba(255,255,255,.014) 55%),
			rgba(6,12,21,.84);
	}

	.section-services .ribbon:nth-child(2),
	.section-services .ribbon:nth-child(5) { grid-column: span 4; }

	.section-services .ribbon:nth-child(3),
	.section-services .ribbon:nth-child(4),
	.section-services .ribbon:nth-child(6),
	.section-services .ribbon:nth-child(7) { grid-column: span 2; }

	.section-services .ribbon:hover {
		transform: translateY(-6px) rotateX(2deg) rotateY(-1.4deg);
		border-color: rgba(0,102,255,.42);
		box-shadow: 0 26px 52px rgba(2,12,28,.55);
	}

	.section-services .ribbon::after {
		content: '';
		position: absolute;
		right: -90px;
		bottom: -112px;
		width: 180px;
		height: 180px;
		border-radius: 9999px;
		background: radial-gradient(circle, rgba(0,102,255,.22), transparent 62%);
		pointer-events: none;
	}

	.section-services .ribbon::before {
		content: '';
		position: absolute;
		left: 0;
		top: 0;
		height: 2px;
		width: 100%;
		background: linear-gradient(90deg, transparent 0%, rgba(0,102,255,.9) 45%, transparent 100%);
		transform: translateX(-100%);
		transition: transform .7s cubic-bezier(.19,.95,.29,1);
		opacity: .85;
	}

	.section-services .ribbon:hover::before { transform: translateX(100%); }

	.section-services .ribbon:nth-child(1)::after {
		width: 240px;
		height: 240px;
		right: -100px;
		bottom: -96px;
	}

	.ribbon .tag {
		width: fit-content;
		border-radius: .9rem;
		border: 1px solid rgba(0,102,255,.22);
		background: linear-gradient(135deg, rgba(0,102,255,.18), rgba(0,102,255,.08));
		display: inline-flex;
		align-items: center;
		justify-content: center;
		color: #dff4ff;
		font-weight: 800;
		text-transform: uppercase;
		letter-spacing: .15em;
		font-size: .74rem;
		padding: .5rem .72rem;
		box-shadow: 0 8px 18px rgba(0,102,255,.14);
	}

	.ribbon h4 { margin-bottom: .45rem; }
	.ribbon p { color: #b8c7da; line-height: 1.68; }

	.section-services .ribbon > div:last-child {
		position: relative;
		z-index: 1;
	}

	.section-services .ribbon:nth-child(1) h4 {
		font-size: clamp(1.45rem, 2.2vw, 2rem);
		line-height: 1.2;
		max-width: 24ch;
	}

	.section-services .ribbon:nth-child(3) h4,
	.section-services .ribbon:nth-child(4) h4,
	.section-services .ribbon:nth-child(6) h4,
	.section-services .ribbon:nth-child(7) h4 {
		font-size: 1.22rem;
		line-height: 1.32;
	}

	.ribbon .mini-chips {
		display: flex;
		flex-wrap: wrap;
		gap: .5rem;
		margin-top: .8rem;
	}

	.ribbon .mini-chips span {
		border: 1px solid rgba(255,255,255,.1);
		background: rgba(255,255,255,.03);
		border-radius: 9999px;
		padding: .4rem .65rem;
		font-size: .7rem;
		color: #d7e2f0;
		letter-spacing: .1em;
		text-transform: uppercase;
		transition: transform .22s ease, border-color .22s ease, background .22s ease;
	}

	.section-services .ribbon:hover .mini-chips span {
		border-color: rgba(0,102,255,.3);
		background: rgba(0,102,255,.08);
		transform: translateY(-1px);
	}

	.section-services .ribbon:nth-child(3) .mini-chips span,
	.section-services .ribbon:nth-child(4) .mini-chips span,
	.section-services .ribbon:nth-child(6) .mini-chips span,
	.section-services .ribbon:nth-child(7) .mini-chips span {
		font-size: .64rem;
		padding: .35rem .55rem;
	}

	.timeline {
		position: relative;
		padding-left: 0;
		counter-reset: pmStep;
		display: grid;
		gap: .9rem;
	}

	.timeline::before {
		content: '';
		position: absolute;
		left: 50%;
		top: .5rem;
		bottom: .5rem;
		width: 1px;
		background: linear-gradient(to bottom, transparent, rgba(0,52,204,.9), transparent);
	}

	.step {
		position: relative;
		border: 1px solid var(--pm-line);
		border-radius: 1rem;
		background: rgba(255,255,255,.03);
		padding: 1.05rem 1.05rem 1.05rem 1.2rem;
		counter-increment: pmStep;
		transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
	}

	.step:hover {
		transform: translateY(-4px);
		border-color: var(--pm-accent);
		box-shadow: 0 10px 25px rgba(0, 102, 255, 0.1);
	}

	.step::before {
		content: '';
		position: absolute;
		left: auto;
		right: -0.72rem;
		top: 1rem;
		width: 1.35rem;
		height: 1.35rem;
		border-radius: 9999px;
		background: radial-gradient(circle at 35% 35%, #0052cc 0%, #003d99 55%, #002966 100%);
		box-shadow: 0 0 0 4px rgba(0,52,204,.2), 0 0 18px rgba(0,52,204,.6);
		border: 1px solid rgba(255,255,255,.18);
		z-index: 1;
	}

	.step::after {
		content: counter(pmStep, decimal-leading-zero);
		position: absolute;
		left: auto;
		right: -0.72rem;
		top: 1rem;
		width: 1.35rem;
		height: 1.35rem;
		display: grid;
		place-items: center;
		color: #fff;
		font-size: .55rem;
		font-weight: 900;
		letter-spacing: .08em;
		z-index: 2;
	}

	.section-process .step {
		width: calc(50% - 1.1rem);
	}

	.section-process .step:nth-child(odd) { margin-right: auto; }
	.section-process .step:nth-child(even) { margin-left: auto; }

	.section-process .step:nth-child(even)::before,
	.section-process .step:nth-child(even)::after {
		right: auto;
		left: -0.72rem;
	}

	.impact-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0,1fr));
		gap: 1.25rem;
	}

	.impact {
		border: 1px solid var(--pm-line);
		background: rgba(255,255,255,0.02);
		border-radius: 1rem;
		padding: 1.5rem;
		position: relative;
		overflow: hidden;
		transition: transform .3s ease, border-color .3s ease, background .3s ease, box-shadow .3s ease;
	}

	.impact h4 { font-size: 1.15rem; margin-bottom: .5rem; font-weight: 700; color: #fff; }
	.impact p { color: var(--pm-muted); line-height: 1.6; }

	.section-impact .impact:hover {
		transform: translateY(-5px);
		border-color: var(--pm-accent);
		background: rgba(0, 102, 255, 0.05);
		box-shadow: 0 10px 30px rgba(0, 102, 255, 0.15);
	}

	.final-cta {
		border: 1px solid var(--pm-line);
		border-radius: 1.3rem;
		text-align: center;
		padding: 2.2rem 1.2rem;
		background: radial-gradient(circle at top center, rgba(0,163,255,.14), rgba(255,255,255,.02));
	}

	.section-cta .final-cta {
		background:
			radial-gradient(circle at 12% 12%, rgba(0,102,255,.2), transparent 30%),
			radial-gradient(circle at 88% 88%, rgba(0,102,255,.18), transparent 34%),
			linear-gradient(165deg, rgba(9,16,29,.9), rgba(5,10,18,.86));
		border-color: rgba(0,102,255,.25);
	}

	.reveal-up { opacity: 0; transform: translateY(18px); }
	.reveal-left { opacity: 0; transform: translateX(-18px); }
	.reveal-right { opacity: 0; transform: translateX(18px); }

	.reveal-up.visible,
	.reveal-left.visible,
	.reveal-right.visible {
		opacity: 1;
		transform: none;
		transition: opacity .72s cubic-bezier(.22,.95,.3,1), transform .72s cubic-bezier(.22,.95,.3,1);
	}

	@media (max-width: 1080px) {
		.hero-grid { grid-template-columns: 1fr; }
		.impact-grid { grid-template-columns: 1fr; }
		.challenge-grid { 
			grid-template-columns: repeat(3, minmax(280px, 1fr));
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding-bottom: 1rem;
		}
		.challenge-grid::-webkit-scrollbar {
			display: none;
		}
		.challenge-card {
			scroll-snap-align: start;
		}
		.service-ribbons { grid-template-columns: repeat(2, minmax(0,1fr)); }
		.section-services .ribbon,
		.section-services .ribbon:nth-child(2),
		.section-services .ribbon:nth-child(3),
		.section-services .ribbon:nth-child(4),
		.section-services .ribbon:nth-child(5),
		.section-services .ribbon:nth-child(6),
		.section-services .ribbon:nth-child(7) { grid-column: span 1; }
		.section-services .ribbon:nth-child(1) { grid-column: span 2; }
		.section-process .timeline::before { left: .72rem; }
		.section-process .step { width: 100%; margin: 0 !important; padding-left: 1.2rem; }
		.section-process .step::before,
		.section-process .step::after {
			left: -0.72rem !important;
			right: auto !important;
		}
	}

	@media (max-width: 860px) {
        .split { grid-template-columns: 1fr; } 
        .split .sticky { position: static; }
        .split .reveal-left { position: static; }
        
        /* Fixed: Higher specificity to override the 12-col desktop grid */
        .section-approach .approach-grid {
            grid-template-columns: none !important; 
            grid-auto-flow: column;
            grid-auto-columns: 85%;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding-bottom: 1.5rem;
            gap: 1rem; /* Ensures proper spacing between scroll items */
        }
        
        .section-approach .approach-grid::-webkit-scrollbar {
            display: none;
        }

        .section-approach .approach-item { 
            grid-column: auto !important; 
            scroll-snap-align: start;
        }

        /* Mobile Scrollable Ribbons Setup */
        .service-ribbons { 
            grid-template-columns: none !important;
            grid-auto-flow: column;
            grid-auto-columns: 85%;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding-top: 0.5rem;
            padding-bottom: 1.5rem;
        }
        
        .service-ribbons::-webkit-scrollbar {
            display: none; 
        }

        .section-services .ribbon { 
            grid-column: auto !important; /* Resets all n-th child spans */
            scroll-snap-align: start; 
        }
        
        /* Keep remaining mobile styles */
		.impact-grid { grid-template-columns: 1fr; }
		.pm-section { padding: 3.8rem 1rem; }
		.pm-section.tight { padding-top: 3.2rem; padding-bottom: 3.2rem; }
        .pm-btn { width: 100%; }

		/* -----------------------------------------
           Mobile Timeline & Animation Optimization
           ----------------------------------------- */
           
       /* -----------------------------------------
           Mobile Scrollable Timeline Setup
           ----------------------------------------- */
           
        /* 1. Transform the timeline container into a horizontal scroller */
        .section-process .timeline {
            display: grid;
            grid-template-columns: none !important;
            grid-auto-flow: column;
            grid-auto-columns: 85%;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding-bottom: 1.5rem;
            padding-left: 0; /* Resets any desktop padding */
            padding-top: 1rem; /* Space for the overlapping number badges */
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .section-process .timeline::-webkit-scrollbar {
            display: none;
        }

        /* 2. Hide the vertical gradient connecting line */
        .section-process .timeline::before {
            display: none; 
        }

        /* 3. Make steps fill the horizontal column and snap into place */
        .section-process .step {
			width: 100% !important; 
			margin: 0 !important; 
			padding-top: 2.85rem;
            scroll-snap-align: start;
        }

		.section-process .step h4 .material-symbols-outlined {
			display: none;
		}

        /* 4. Reposition the numbered circles to pop out of the top-left edge of the cards */
        .section-process .step::before,
        .section-process .step::after {
			top: 1rem !important; 
			left: 1rem !important; 
            right: auto !important;
        }

		/* Mobile Scrollable Impact Grid Setup */
        .impact-grid {
            grid-template-columns: none !important;
            grid-auto-flow: column;
            grid-auto-columns: 85%; /* Shows 85% of the card to encourage swiping */
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -ms-overflow-style: none; /* IE/Edge */
            scrollbar-width: none; /* Firefox */
            padding-bottom: 1.5rem; /* Prevents bottom shadow clipping */
            gap: 1rem;
        }
        
        .impact-grid::-webkit-scrollbar {
            display: none;
        }

        .impact-grid .impact {
            scroll-snap-align: start;
        }

		.section-services .mini-chips {
			flex-wrap: nowrap;
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			-ms-overflow-style: none;
			scrollbar-width: none;
			padding-bottom: 0.5rem;
		}

		.section-services .mini-chips::-webkit-scrollbar {
			display: none;
		}

		.section-services .mini-chips span {
			white-space: nowrap;
			scroll-snap-align: start;
		}

    }

	@media (prefers-reduced-motion: reduce) {
		*, *::before, *::after {
			animation-duration: .01ms !important;
			animation-iteration-count: 1 !important;
			transition-duration: .01ms !important;
			scroll-behavior: auto !important;
		}
	}

/* Full-bleed CTA (d2c-branding style adapted) */
#cta-final {
	background: var(--pm-accent);
	padding: 7rem 0;
	position: relative;
	overflow: hidden;
}

@media (max-width: 768px) {
	#cta-final { padding: 4.8rem 0; }
}

.cta-bg-text {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	font-size: clamp(6rem, 18vw, 18rem);
	font-weight: 900;
	letter-spacing: -.04em;
	color: rgba(0, 0, 0, .12);
	white-space: nowrap;
	pointer-events: none;
	user-select: none;
}

.cta-inner { text-align: center; position: relative; z-index: 1; }
.cta-inner h2 { font-size: clamp(2.4rem, 5vw, 4rem); font-weight: 900; line-height: 1.05; color: #fff; margin-bottom: 1.2rem; }
.cta-inner p { font-size: 1.05rem; color: rgba(255,255,255,.85); max-width: 64ch; margin: 0 auto 1.8rem; line-height: 1.7; }

.btn-white {
	background: #fff;
	color: var(--pm-accent);
	font-weight: 800;
	padding: 14px 40px;
	border-radius: .6rem;
	display: inline-flex; align-items:center; gap:.6rem; text-decoration:none;
	box-shadow: 0 10px 36px rgba(0,0,0,.16);
}
.btn-white:hover { transform: translateY(-3px); box-shadow: 0 18px 48px rgba(0,0,0,.28); }
.btn-white::after { background: rgba(0,163,255,.06); }

.btn-outline {
	color: #fff;
	border: 1px solid rgba(255,255,255,.18);
	padding: 12px 30px;
	border-radius: .6rem;
	text-decoration: none;
	display:inline-flex; align-items:center; gap:.5rem;
}
.btn-outline:hover { border-color: rgba(255,255,255,.36); }

</style>
HTML;

include __DIR__ . '/app/views/header.php';
?>

<main class="pm-shell text-white" style="overflow-x: clip;">
	<section class="pm-section section-hero pt-28 sm:pt-32 pb-12 sm:pb-16">
		<div class="hero-bg-graph"></div>
		<div class="pm-wrap px-4 sm:px-6 lg:px-8">
			<div class="pm-kicker reveal-up"><i></i><?= htmlspecialchars($pm_hero['kicker'] ?? 'Data-Driven Marketing That Generates Leads, Sales, and Scalable Growth') ?></div>
			<h1 class="pm-title reveal-up"><span><?= htmlspecialchars($pm_hero['headline_main'] ?? 'Performance Marketing ') ?></span><br /><span class="accent"><?= htmlspecialchars($pm_hero['headline_accent'] ?? 'Services in India') ?></span></h1>
			<p class="pm-sub reveal-up">
				<?= htmlspecialchars($pm_hero['sub_text'] ?? '') ?>
			</p>

			<div class="pm-actions reveal-up">
				<a href="<?= htmlspecialchars($pm_hero['btn1_url'] ?? 'leadform.php') ?>"
					class="pm-btn primary relative z-50 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer"><?= htmlspecialchars($pm_hero['btn1_label'] ?? 'Scale Your Business with Proven Strategies') ?> <span class="material-symbols-outlined text-base">arrow_forward</span></a>
				<a href="<?= htmlspecialchars($pm_hero['btn2_url'] ?? '#services') ?>" class="pm-btn ghost"><?= htmlspecialchars($pm_hero['btn2_label'] ?? 'Explore Services') ?></a>
			</div>

			<div class="hero-grid reveal-up">
				<div class="hero-card">
					<h3><?= htmlspecialchars($pm_hero['card_heading'] ?? '') ?></h3>
					<p><?= htmlspecialchars($pm_hero['card_body'] ?? '') ?></p>
					<p class="mt-3" style="color:#d6e2f2;"><?= htmlspecialchars($pm_hero['card_body2'] ?? '') ?></p>
				</div>

				<div class="hero-metrics">
					<?php foreach ($pm_hero_metrics as $m): ?>
					<div class="metric">
						<div class="num"><span class="count" data-target="<?= (int)$m['value'] ?>"><?= (int)$m['value'] ?></span></div>
						<div class="txt"><?= htmlspecialchars($m['text']) ?></div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<div class="overflow-x-auto">
		<table class="w-full text-left border-collapse">
			<thead>
				<tr class="border-b border-white/10">
					<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Industry
						Segment</th>
					<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Core
						Performance Metric</th>
					<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Market
						Benchmark</th>
					<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Digifyce Avg.
					</th>
					<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold"
						style="color: var(--electric-blue);">Performance Lift</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-white/5">
				<?php foreach ($pm_benchmark as $grpIdx => $grp):
					$grpRows = json_decode($grp['rows_json'] ?? '[]', true) ?: [];
					$rowCount = count($grpRows);
					foreach ($grpRows as $rIdx => $row):
				?>
				<tr class="data-table-row<?= $grpIdx > 0 && $rIdx === 0 ? ' border-t border-white/10' : '' ?>">
					<?php if ($rIdx === 0): ?>
					<td class="py-10 px-4 align-top border-r border-white/5 industry-group" rowspan="<?= $rowCount ?>">
						<div class="flex items-center gap-3">
							<span class="material-symbols-outlined" style="color: var(--electric-blue);"><?= htmlspecialchars($grp['industry_icon']) ?></span>
							<span class="font-bold text-lg"><?= htmlspecialchars($grp['industry_label']) ?></span>
						</div>
					</td>
					<?php endif; ?>
					<td class="py-6 px-4 text-sm text-white/60 italic"><?= htmlspecialchars($row['metric'] ?? '') ?></td>
					<td class="py-6 px-4 font-mono text-sm"><?= htmlspecialchars($row['benchmark'] ?? '') ?></td>
					<td class="py-6 px-4 font-bold text-lg"><?= htmlspecialchars($row['digifyce_avg'] ?? '') ?></td>
					<td class="py-6 px-4 text-green-400 font-bold"><?= htmlspecialchars($row['lift'] ?? '') ?></td>
				</tr>
				<?php endforeach; endforeach; ?>
			</tbody>
		</table>
	</div>


	<section class="pm-section section-problem tight text-center">
		<div class="pm-wrap px-4 sm:px-6 lg:px-8">
			<div class="pm-kicker reveal-up mx-auto mb-4"><i></i><?= htmlspecialchars($pmsh['problem']['kicker'] ?? 'Core problem') ?></div>
			<h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mt-4 leading-tight reveal-up mx-auto max-w-4xl">
				<?= htmlspecialchars($pmsh['problem']['heading'] ?? 'Why Most Businesses Struggle with Performance Marketing') ?>
			</h2>
			<p class="pm-sub mt-6 mx-auto max-w-3xl reveal-up text-lg">
				<?= htmlspecialchars($pmsh['problem']['sub_text'] ?? '') ?>
			</p>

			<h3 class="text-2xl mt-16 mb-8 reveal-up font-bold text-white">Common challenges include</h3>
			<div class="challenge-grid reveal-up">
				<?php foreach ($pm_challenges as $i => $ch): ?>
				<div class="challenge-card"<?= ($i === count($pm_challenges) - 1) ? ' style="grid-column: 1 / -1; justify-content: center;"' : '' ?>>
					<span class="material-symbols-outlined icon"><?= htmlspecialchars($ch['icon']) ?></span>
					<p><?= htmlspecialchars($ch['text']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="panel reveal-up mx-auto mt-12 max-w-3xl text-center"
				style="background: rgba(255,255,255,0.02); border-color: rgba(0, 102, 255, 0.3);">
				<p class="text-lg">Without a proper performance marketing system, businesses often keep spending without
					predictable growth.</p>
			</div>
		</div>
	</section>

	<section class="pm-section section-approach tight" id="approach">
		<div class="pm-wrap px-4 sm:px-6 lg:px-8 split">
			<div class="sticky reveal-left">
				<div class="pm-kicker"><i></i><?= htmlspecialchars($pmsh['approach']['kicker'] ?? 'Our approach') ?></div>
				<h2 class="text-4xl sm:text-5xl font-black tracking-tight mt-4 leading-tight"><?= htmlspecialchars($pmsh['approach']['heading'] ?? 'Our Approach to Performance Marketing') ?></h2>
				<p class="pm-sub mt-4">
					<?= htmlspecialchars($pmsh['approach']['sub_text'] ?? '') ?>
				</p>
			</div>

			<div class="approach-grid reveal-right">
				<?php foreach ($pm_approaches as $ap): ?>
				<div class="approach-item">
					<div class="idx"><?= htmlspecialchars($ap['step_label']) ?></div>
					<h4 class="text-xl"><?= htmlspecialchars($ap['heading']) ?></h4>
					<p><?= htmlspecialchars($ap['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>



	<section id="services" class="pm-section section-services">
		<div class="pm-wrap px-4 sm:px-6 lg:px-8">
			<div class="pm-kicker reveal-up"><i></i><?= htmlspecialchars($pmsh['services']['kicker'] ?? 'Paid + Organic + Automation Services') ?></div>
			<h2 class="text-4xl sm:text-6xl font-black tracking-tight mt-4 max-w-4xl reveal-up"><?= htmlspecialchars($pmsh['services']['heading'] ?? 'Performance Marketing Services Designed as a Scalable System') ?></h2>

			<div class="service-ribbons mt-10">
				<?php foreach ($pm_services as $svc):
					$chips = json_decode($svc['chips_json'] ?? '[]', true) ?: [];
				?>
				<article class="ribbon reveal-up">
					<div class="tag"><?= htmlspecialchars($svc['tag']) ?></div>
					<div>
						<h4 class="text-2xl font-bold"><?= htmlspecialchars($svc['heading']) ?></h4>
						<p><?= htmlspecialchars($svc['description']) ?></p>
						<div class="mini-chips">
							<?php foreach ($chips as $chip): ?><span><?= htmlspecialchars($chip) ?></span><?php endforeach; ?>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>




	<!-- LEAD GEN SECTION START -->
	<style>
		.lg-tab-btn {
			position: relative;
			padding: 1.5rem 2rem;
			border-radius: 1rem;
			background: transparent;
			border: 1px solid transparent;
			text-align: left;
			cursor: pointer;
			transition: all 0.4s ease;
			overflow: hidden;
			display: flex;
			align-items: center;
			gap: 1rem;
		}

		.lg-tab-btn::before {
			content: '';
			position: absolute;
			left: 0;
			top: 0;
			bottom: 0;
			width: 0%;
			background: linear-gradient(90deg, rgba(0, 102, 255, 0.15), transparent);
			z-index: 0;
			transition: width 0.4s ease;
		}

		.lg-tab-btn.active {
			background: rgba(255, 255, 255, 0.02);
			border-color: rgba(0, 102, 255, 0.3);
			box-shadow: inset 4px 0 0 var(--pm-accent);
		}

		.lg-tab-btn.active::before {
			width: 100%;
		}

		.lg-tab-btn:hover:not(.active) {
			background: rgba(255, 255, 255, 0.03);
			border-color: rgba(255, 255, 255, 0.1);
		}

		.lg-tab-btn .lg-tab-icon {
			font-size: 1.5rem;
			color: rgba(255, 255, 255, 0.4);
			transition: all 0.4s ease;
			position: relative;
			z-index: 1;
		}

		.lg-tab-btn.active .lg-tab-icon {
			color: var(--pm-accent);
			transform: scale(1.1);
			text-shadow: 0 0 15px rgba(0, 102, 255, 0.6);
		}

		.lg-tab-btn h3 {
			font-size: 1.25rem;
			font-weight: 700;
			color: rgba(255, 255, 255, 0.5);
			margin: 0;
			position: relative;
			z-index: 1;
			transition: all 0.4s ease;
		}

		.lg-tab-btn.active h3 {
			color: #fff;
		}

		.lg-progress-bar {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			height: 2px;
			background: rgba(255, 255, 255, 0.05);
			z-index: 2;
			opacity: 0;
		}

		.lg-tab-btn.active .lg-progress-bar {
			opacity: 1;
		}

		.lg-progress-fill {
			height: 100%;
			width: 0%;
			background: var(--pm-accent);
			box-shadow: 0 0 10px var(--pm-accent);
		}

		.lg-content-panel {
			background: rgba(7, 13, 24, 0.6);
			backdrop-filter: blur(24px);
			border: 1px solid rgba(255, 255, 255, 0.08);
			border-radius: 1.5rem;
			padding: 3rem;
			height: 100%;
			box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
			display: none;
			opacity: 0;
		}

		.lg-content-panel.active {
			display: flex;
			flex-direction: column;
		}

		@media (max-width: 1024px) {
			.lg-interactive-layout {
				flex-direction: column;
				gap: 1.5rem;
			}

			.lg-tabs-wrapper {
				flex-direction: row;
				overflow-x: auto;
				scroll-snap-type: x mandatory;
				padding-bottom: 0.5rem;
				scrollbar-width: none;
				/* Firefox */
			}

			.lg-tabs-wrapper::-webkit-scrollbar {
				display: none;
				/* Chrome, Safari */
			}

			.lg-tab-btn {
				flex: 0 0 auto;
				padding: 0.8rem 1.2rem;
				height: auto;
				border-radius: 9999px;
				border: 1px solid rgba(255, 255, 255, 0.1);
				scroll-snap-align: start;
			}

			.lg-tab-btn::before {
				display: none;
			}

			.lg-tab-btn.active {
				background: rgba(0, 102, 255, 0.15);
				border-color: rgba(0, 102, 255, 0.5);
				box-shadow: none;
			}

			.lg-tab-icon {
				display: none;
			}

			.lg-tab-btn h3 {
				font-size: 0.95rem;
				white-space: nowrap;
			}

			.lg-progress-bar {
				display: none;
			}

			.lg-content-panel {
				padding: 1.8rem;
				margin-top: 0;
			}
		}

		.lg-list {
			display: flex;
			flex-wrap: wrap;
			gap: 0.6rem;
			list-style: none;
			padding: 0;
			margin: 0;
		}

		.lg-list li {
			background: rgba(255, 255, 255, 0.03);
			border: 1px solid rgba(255, 255, 255, 0.06);
			border-radius: 9999px;
			padding: 0.5rem 1rem;
			font-size: 0.9rem;
			color: #d7e2f0;
			display: inline-flex;
			align-items: center;
			gap: 0.5rem;
		}

		.lg-list li::before {
			content: '';
			width: 5px;
			height: 5px;
			border-radius: 50%;
			background: var(--pm-accent);
			box-shadow: 0 0 8px var(--pm-accent);
		}

		.lg-conclusion {
			margin-top: 2rem;
			padding: 1.2rem;
			background: linear-gradient(90deg, rgba(0, 102, 255, 0.1), transparent);
			border-left: 3px solid var(--pm-accent);
			border-radius: 0 0.8rem 0.8rem 0;
			color: #fff;
			font-weight: 600;
			line-height: 1.6;
		}
	</style>

	<section class="pm-section section-leadgen tight overflow-visible relative" id="leadgen">
		<!-- Decorative background -->
		<div class="absolute inset-0 pointer-events-none z-0">
			<div
				class="absolute right-[5%] top-[10%] w-[600px] h-[600px] rounded-full bg-[#0066ff] opacity-10 blur-[120px]">
			</div>
			<div
				class="absolute left-[5%] bottom-[10%] w-[500px] h-[500px] rounded-full bg-[#00a3ff] opacity-5 blur-[100px]">
			</div>
		</div>

		<div class="pm-wrap px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="text-center mb-16 max-w-4xl mx-auto">
				<div class="pm-kicker reveal-up mx-auto mb-4"><i></i><?= htmlspecialchars($pmsh['leadgen']['kicker'] ?? 'Growth Engine') ?></div>
				<h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mt-4 leading-tight reveal-up text-white">
					<?= htmlspecialchars($pmsh['leadgen']['heading'] ?? 'Lead Generation Services') ?>
				</h2>
				<p class="pm-sub mt-6 mx-auto reveal-up text-lg">
					<?= htmlspecialchars($pmsh['leadgen']['sub_text'] ?? '') ?>
				</p>
			</div>

			<div class="lg-interactive-layout flex gap-8 lg:gap-16 max-w-7xl mx-auto mt-12">

				<!-- Tabs Nav -->
				<div class="lg-tabs-wrapper w-full lg:w-5/12 flex flex-col gap-3 reveal-left">

					<?php foreach ($pm_leadgen_tabs as $i => $lgt): ?>
					<button class="lg-tab-btn<?= $i === 0 ? ' active' : '' ?>" data-lg-target="<?= $i ?>">
						<span class="material-symbols-outlined lg-tab-icon"><?= htmlspecialchars($lgt['tab_icon']) ?></span>
						<h3><?= htmlspecialchars($lgt['title']) ?></h3>
						<div class="lg-progress-bar">
							<div class="lg-progress-fill" id="lg-fill-<?= $i ?>"></div>
						</div>
					</button>
					<?php endforeach; ?>

				</div>

				<!-- Content Panels -->
				<div class="lg-content-wrapper w-full lg:w-7/12 relative reveal-right min-h-[450px]">

					<?php foreach ($pm_leadgen_tabs as $i => $lgt):
						$lgt_bullets = json_decode($lgt['bullets_json'] ?? '[]', true) ?: [];
					?>
					<div class="lg-content-panel<?= $i === 0 ? ' active' : '' ?>" id="lg-panel-<?= $i ?>">
						<div class="flex items-center gap-4 mb-6">
							<span class="material-symbols-outlined text-4xl text-[var(--pm-accent)]"><?= htmlspecialchars($lgt['tab_icon']) ?></span>
							<h3 class="text-3xl font-bold text-white m-0"><?= htmlspecialchars($lgt['title']) ?></h3>
						</div>
						<p class="text-[var(--pm-muted)] text-lg mb-8 leading-relaxed">
							<?= htmlspecialchars($lgt['intro_text']) ?>
						</p>
						<ul class="lg-list mb-auto">
							<?php foreach ($lgt_bullets as $bullet): ?>
							<li><?= htmlspecialchars($bullet) ?></li>
							<?php endforeach; ?>
						</ul>
						<div class="lg-conclusion">
							<?= htmlspecialchars($lgt['conclusion']) ?>
						</div>
					</div>
					<?php endforeach; ?>


				</div>
			</div>
		</div>
	</section>

	<script>
		document.addEventListener('DOMContentLoaded', () => {

			function initLGInteractive() {
				const buttons = document.querySelectorAll('.lg-tab-btn');
				const panels = document.querySelectorAll('.lg-content-panel');
				let currentIndex = 0;
				let progressTween;
				let isAnimating = false;

				function switchTab(index, manual = false, direction = null) {
					if (index === currentIndex || isAnimating) return;

					isAnimating = true;
					if (progressTween) progressTween.kill();
					gsap.set('.lg-progress-fill', { width: "0%" });

					const prevPanel = panels[currentIndex];
					const newPanel = panels[index];

					buttons[currentIndex].classList.remove('active');
					buttons[index].classList.add('active');

					if (window.innerWidth <= 1024) {
						buttons[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
					}

					const slideDir = direction !== null ? direction : (index > currentIndex ? 1 : -1);
					const xOffset = 50 * slideDir;

					gsap.to(prevPanel, {
						opacity: 0,
						x: -xOffset,
						duration: 0.3,
						onComplete: () => {
							prevPanel.classList.remove('active');
							newPanel.classList.add('active');
							gsap.fromTo(newPanel,
								{ opacity: 0, x: xOffset },
								{ opacity: 1, x: 0, duration: 0.4, ease: "power2.out", onComplete: () => isAnimating = false }
							);
						}
					});

					currentIndex = index;

					if (window.innerWidth > 1024) {
						startProgress(index);
					}
				}

				function startProgress(index) {
					const fill = document.getElementById(`lg-fill-${index}`);
					progressTween = gsap.to(fill, {
						width: "100%",
						duration: 5,
						ease: "none",
						onComplete: () => {
							const nextIndex = (currentIndex + 1) % buttons.length;
							switchTab(nextIndex, false, 1);
						}
					});
				}

				buttons.forEach((btn, idx) => {
					btn.addEventListener('click', () => {
						switchTab(idx, true);
					});
				});

				const wrapper = document.querySelector('.lg-content-wrapper');
				let startX = 0;
				let endX = 0;

				wrapper.addEventListener('touchstart', e => {
					startX = e.changedTouches[0].screenX;
				}, { passive: true });

				wrapper.addEventListener('touchend', e => {
					endX = e.changedTouches[0].screenX;
					handleSwipe();
				}, { passive: true });

				function handleSwipe() {
					const threshold = 40;
					if (endX < startX - threshold) {
						switchTab((currentIndex + 1) % buttons.length, true, 1);
					} else if (endX > startX + threshold) {
						switchTab((currentIndex - 1 + buttons.length) % buttons.length, true, -1);
					}
				}

				if (window.innerWidth > 1024) {
					startProgress(0);
				}

				gsap.fromTo(panels[0], { opacity: 0, x: 50 }, { opacity: 1, x: 0, duration: 0.5, delay: 0.2 });
			}

			if (typeof gsap !== 'undefined') {
				initLGInteractive();
			} else {
				const gsapScript = document.createElement('script');
				gsapScript.src = "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js";
				document.head.appendChild(gsapScript);

				gsapScript.onload = () => {
					initLGInteractive();
				};
			}
		});
	</script>
	<!-- LEAD GEN SECTION END -->

	<!-- SEO SECTION START -->
	<style>
		.seo-flex-container {
			display: flex;
			height: 600px;
			gap: 1rem;
			width: 100%;
			max-width: 85rem;
			margin: 0 auto;
		}

		.seo-panel {
			position: relative;
			flex: 1;
			border-radius: 2rem;
			background: rgba(7, 13, 24, 0.4);
			border: 1px solid rgba(255, 255, 255, 0.05);
			overflow: hidden;
			cursor: pointer;
			transition: all 0.7s cubic-bezier(0.25, 1, 0.5, 1);
			display: flex;
			align-items: center;
			justify-content: center;
			backdrop-filter: blur(10px);
		}

		.seo-panel:hover {
			background: rgba(0, 102, 255, 0.1);
			border-color: rgba(0, 102, 255, 0.3);
		}

		.seo-panel.active {
			flex: 6;
			background: rgba(7, 13, 24, 0.8);
			border-color: rgba(0, 102, 255, 0.5);
			box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(0, 102, 255, 0.3);
			cursor: default;
		}

		/* Collapsed State Content */
		.seo-panel-collapsed {
			position: absolute;
			inset: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: flex-start;
			padding-top: 3rem;
			gap: 2rem;
			transition: opacity 0.4s ease;
		}

		.seo-panel.active .seo-panel-collapsed {
			opacity: 0;
			pointer-events: none;
		}

		.seo-collapsed-icon {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.05);
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--pm-accent);
		}

		.seo-collapsed-title {
			writing-mode: vertical-rl;
			text-orientation: mixed;
			font-size: 1.25rem;
			font-weight: 700;
			color: rgba(255, 255, 255, 0.4);
			letter-spacing: 2px;
			transform: rotate(180deg);
			white-space: nowrap;
		}

		/* Expanded State Content */
		.seo-panel-expanded {
			position: absolute;
			inset: 0;
			padding: 3rem;
			display: flex;
			flex-direction: column;
			opacity: 0;
			visibility: hidden;
			transform: translateX(20px);
			transition: all 0.5s ease;
			transition-delay: 0s;
			overflow-y: auto;
		}

		/* Hide scrollbar for aesthetics */
		.seo-panel-expanded::-webkit-scrollbar {
			display: none;
		}

		.seo-panel-expanded {
			-ms-overflow-style: none;
			scrollbar-width: none;
		}

		.seo-panel.active .seo-panel-expanded {
			opacity: 1;
			visibility: visible;
			transform: translateX(0);
			transition-delay: 0.3s;
		}

		.seo-exp-header {
			display: flex;
			align-items: center;
			gap: 1.2rem;
			margin-bottom: 1.5rem;
			flex-shrink: 0;
		}

		.seo-exp-header h3 {
			font-size: 2.2rem;
			font-weight: 800;
			color: #fff;
			margin: 0;
			line-height: 1.2;
		}

		.seo-exp-icon {
			width: 60px;
			height: 60px;
			border-radius: 1rem;
			background: linear-gradient(135deg, rgba(0, 102, 255, 0.2), rgba(0, 163, 255, 0.05));
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--pm-accent);
			border: 1px solid rgba(0, 102, 255, 0.3);
			flex-shrink: 0;
		}

		@media (max-width: 1024px) {
			.seo-flex-container {
				flex-direction: column;
				height: auto;
				gap: 1rem;
			}

			.seo-panel {
				flex: auto;
				width: 100%;
				height: 80px;
				justify-content: flex-start;
				align-items: center;
			}

			.seo-panel.active {
				height: auto;
				min-height: 400px;
			}

			.seo-panel-collapsed {
				flex-direction: row;
				padding: 0 2rem;
				align-items: center;
				gap: 1.5rem;
			}

			.seo-collapsed-title {
				writing-mode: horizontal-tb;
				transform: none;
			}

			.seo-panel-expanded {
				padding: 2rem;
				transform: translateY(20px);
				position: relative;
				inset: auto;
				display: none;
			}

			.seo-panel.active .seo-panel-expanded {
				display: flex;
			}

			.seo-panel.active .seo-panel-collapsed {
				display: none;
			}
		}
	</style>

	<section class="pm-section section-seo tight overflow-visible relative" id="seo-services">
		<!-- Decorative background -->
		<div class="absolute inset-0 pointer-events-none z-0">
			<div
				class="absolute left-[10%] top-[20%] w-[500px] h-[500px] rounded-full bg-[#0066ff] opacity-10 blur-[120px]">
			</div>
			<div
				class="absolute right-[10%] bottom-[10%] w-[400px] h-[400px] rounded-full bg-[#00a3ff] opacity-10 blur-[100px]">
			</div>
		</div>

		<div class="pm-wrap px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="text-center mb-16 max-w-4xl mx-auto">
				<div class="pm-kicker reveal-up mx-auto mb-4"><i></i><?= htmlspecialchars($pmsh['seo']['kicker'] ?? 'Organic Dominance') ?></div>
				<h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mt-4 leading-tight reveal-up text-white">
					<?= htmlspecialchars($pmsh['seo']['heading'] ?? 'SEO Services in India for Organic Growth') ?>
				</h2>
				<p class="pm-sub mt-6 mx-auto reveal-up text-lg">
					<?= htmlspecialchars($pmsh['seo']['sub_text'] ?? '') ?>
				</p>
			</div>

			<div class="seo-flex-container reveal-up">

				<?php foreach ($pm_seo_panels as $i => $sp):
					$sp_bullets = json_decode($sp['bullets_json'] ?? '[]', true) ?: [];
				?>
				<div class="seo-panel<?= $i === 0 ? ' active' : '' ?>">
					<div class="seo-panel-collapsed">
						<div class="seo-collapsed-icon"><span class="material-symbols-outlined"><?= htmlspecialchars($sp['panel_icon']) ?></span></div>
						<div class="seo-collapsed-title"><?= htmlspecialchars($sp['title']) ?></div>
					</div>
					<div class="seo-panel-expanded">
						<div class="seo-exp-header">
							<div class="seo-exp-icon"><span class="material-symbols-outlined text-3xl"><?= htmlspecialchars($sp['panel_icon']) ?></span></div>
							<h3><?= htmlspecialchars($sp['title']) ?></h3>
						</div>
						<p class="text-[var(--pm-muted)] text-lg mb-6 leading-relaxed">
							<?= htmlspecialchars($sp['intro_text']) ?>
						</p>
						<ul class="lg-list mb-auto">
							<?php foreach ($sp_bullets as $bullet): ?>
							<li><?= htmlspecialchars($bullet) ?></li>
							<?php endforeach; ?>
						</ul>
						<div class="lg-conclusion">
							<?= htmlspecialchars($sp['conclusion']) ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>

			</div>
		</div>
	</section>

	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const panels = document.querySelectorAll('.seo-panel');
			panels.forEach(panel => {
				panel.addEventListener('click', () => {
					if (panel.classList.contains('active')) return;

					panels.forEach(p => p.classList.remove('active'));
					panel.classList.add('active');

					if (window.innerWidth <= 1024) {
						setTimeout(() => {
							const y = panel.getBoundingClientRect().top + window.scrollY - 100;
							window.scrollTo({ top: y, behavior: 'smooth' });
						}, 300);
					}
				});
			});
		});
	</script>
	<!-- SEO SECTION END -->




	<section class="pm-section section-process tight">
		<div class="pm-wrap px-4 sm:px-6 lg:px-8 split">
			<div class="sticky reveal-left">
				<div class="pm-kicker"><i></i><?= htmlspecialchars($pmsh['process']['kicker'] ?? 'Execution system') ?></div>
				<h2 class="text-4xl sm:text-5xl font-black tracking-tight mt-4 leading-tight"><?= htmlspecialchars($pmsh['process']['heading'] ?? 'Our Performance Marketing Process') ?></h2>
				<p class="pm-sub mt-4"><?= htmlspecialchars($pmsh['process']['sub_text'] ?? '') ?></p>
			</div>

			<div class="timeline space-y-4 reveal-right">
				<?php foreach ($pm_steps as $st): ?>
				<div class="step">
					<h4 class="text-xl font-bold mb-2"><span class="material-symbols-outlined text-[var(--pm-accent)] align-middle mr-1"><?= htmlspecialchars($st['icon']) ?></span>
						<?= htmlspecialchars($st['heading']) ?></h4>
					<p><?= htmlspecialchars($st['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>






	<section class="pm-section section-impact tight">
		<div class="pm-wrap px-4 sm:px-6 lg:px-8 split">
			<div class="sticky reveal-left">
				<div class="pm-kicker"><i></i><?= htmlspecialchars($pmsh['impact']['kicker'] ?? 'Business impact') ?></div>
				<h2 class="text-4xl sm:text-5xl font-black tracking-tight mt-4 leading-tight"><?= htmlspecialchars($pmsh['impact']['heading'] ?? 'Why Choose Digifyce') ?></h2>
				<div class="panel mt-8">
					<p class="pm-sub mt-2 text-sm text-slate-300">
						<?= htmlspecialchars($pmsh['impact']['extra_text'] ?? '') ?>
					</p>
				</div>
				<?php if (!empty($pmsh['impact']['btn_label'])): ?>
				<div class="mt-8">
					<a href="<?= htmlspecialchars($pmsh['impact']['btn_url'] ?? 'leadform.php') ?>"
						class="pm-btn primary relative z-50 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer">
						<?= htmlspecialchars($pmsh['impact']['btn_label']) ?>
						<span class="material-symbols-outlined text-base">arrow_forward</span>
					</a>
				</div>
				<?php endif; ?>
			</div>

			<div class="impact-grid reveal-right">
				<?php foreach ($pm_impacts as $imp): ?>
				<div class="impact">
					<div class="flex items-center gap-4 mb-3">
						<span class="material-symbols-outlined <?= htmlspecialchars($imp['icon_color']) ?>"><?= htmlspecialchars($imp['icon']) ?></span>
						<h4 style="margin:0"><?= htmlspecialchars($imp['heading']) ?></h4>
					</div>
					<p><?= htmlspecialchars($imp['description']) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>


	<section id="cta-final" class="pm-section">
		<div class="cta-bg-text">GROW</div>
		<div class="pm-wrap px-4 sm:px-6 lg:px-8">
			<div class="cta-inner reveal-up">
				<h2><?= htmlspecialchars($pmsh['cta']['heading'] ?? 'Start Your Performance Marketing Today') ?></h2>
				<p class="pm-sub mx-auto"><?= htmlspecialchars($pmsh['cta']['sub_text'] ?? '') ?></p>
				<div style="margin-top:1.4rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
					<?php if (!empty($pmsh['cta']['btn_label'])): ?>
					<a href="<?= htmlspecialchars($pmsh['cta']['btn_url'] ?? 'leadform.php') ?>" class="btn-white"><?= htmlspecialchars($pmsh['cta']['btn_label']) ?>
						<span class="material-symbols-outlined">arrow_forward</span>
					</a>
					<?php endif; ?>
					<?php if (!empty($pmsh['cta']['btn2_label'])): ?>
					<a href="<?= htmlspecialchars($pmsh['cta']['btn2_url'] ?? '#approach') ?>" class="btn-outline"><?= htmlspecialchars($pmsh['cta']['btn2_label']) ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>

<script>
	document.addEventListener('DOMContentLoaded', () => {
		const items = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');
		const io = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) entry.target.classList.add('visible');
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
		items.forEach((el) => io.observe(el));

		const counters = document.querySelectorAll('.count');
		const counterObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting || entry.target.dataset.done) return;
				const el = entry.target;
				const target = parseFloat(el.dataset.target || '0');
				const start = performance.now();
				const duration = 850;
				const animate = (t) => {
					const p = Math.min((t - start) / duration, 1);
					const eased = 1 - Math.pow(1 - p, 3);
					const value = Math.max(0, Math.floor(target * eased));
					el.textContent = value.toLocaleString();
					if (p < 1) requestAnimationFrame(animate);
					else {
						el.textContent = target.toLocaleString();
						el.dataset.done = '1';
					}
				};
				requestAnimationFrame(animate);
				counterObserver.unobserve(el);
			});
		}, { threshold: 0.5 });
		counters.forEach((c) => counterObserver.observe(c));

		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const parallaxTargets = document.querySelectorAll('.pm-kicker.parallax');
		if (!reduceMotion && window.innerWidth > 900 && parallaxTargets.length) {
			let ticking = false;
			const applyParallax = () => {
				const y = window.scrollY;
				parallaxTargets.forEach((el, i) => {
					const factor = i % 2 === 0 ? 0.02 : -0.015;
					el.style.transform = `translate3d(0, ${y * factor}px, 0)`;
				});
				ticking = false;
			};
			window.addEventListener('scroll', () => {
				if (!ticking) {
					requestAnimationFrame(applyParallax);
					ticking = true;
				}
			}, { passive: true });
		}
	});
</script>



<?php include __DIR__ . '/app/views/footer.php'; ?>