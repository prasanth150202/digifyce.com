<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'e-com-marketing');
$pageTitle = $_seo['meta_title'] ?: 'E-Commerce Marketing Services in India – Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'Build High-Performing Online Stores That Convert Visitors into Customers with Digifyce. Shopify, WooCommerce, and custom e-commerce development.';
$bodyClass = 'ecom-unique-page';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo       = Database::getInstance();
$challenges = $_pdo->query("SELECT * FROM ecom_challenges WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$approaches = $_pdo->query("SELECT * FROM ecom_approaches WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps      = $_pdo->query("SELECT * FROM ecom_steps WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ec_hero    = $_pdo->query("SELECT * FROM ecom_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ec_sols    = $_pdo->query("SELECT * FROM ecom_solutions WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ec_why     = $_pdo->query("SELECT * FROM ecom_why_points WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ec_cta     = $_pdo->query("SELECT * FROM ecom_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ecom_sh    = [];
foreach ($_pdo->query("SELECT * FROM ecom_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $_r) { $ecom_sh[$_r['slug']] = $_r; }
include __DIR__ . '/app/views/header.php';
?>

<style>
    /* Custom Scrollbar for a seamless experience */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #030508;
    }

    ::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--electric-blue);
    }

    body {
        background-color: #030508;
        overflow-x: hidden;
    }

    /* Hero Text */

    /* Horizontal Scroll Cards */
    .challenge-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 2rem;
        transition: border-color 0.3s ease;
    }

    .challenge-card:hover {
        border-color: var(--electric-blue);
    }

    /* Sticky Stacking Cards */
    .stack-card {
        position: sticky;
        top: 120px;
        box-shadow: 0 -20px 40px rgba(0, 0, 0, 0.5);
    }

    /* Flex Hover Panels */
    .sol-panel {
        transition: flex 0.7s cubic-bezier(0.25, 1, 0.5, 1), background 0.5s ease;
        overflow: hidden;
    }

    .sol-panel:hover {
        background: rgba(0, 0, 0, 0.4);
    }

    .sol-content {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1) 0.1s;
        max-height: 0;
        margin-top: 0;
    }

    .sol-panel:hover .sol-content {
        opacity: 1;
        transform: translateY(0);
        max-height: 500px;
        margin-top: 1.5rem;
    }

    .sol-title {
        transition: color 0.5s ease;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    @keyframes subtle-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 102, 255, 0.4);
            transform: scale(1);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(0, 102, 255, 0);
            transform: scale(1.05);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 102, 255, 0);
            transform: scale(1);
        }
    }

    .sol-icon {
        transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: subtle-pulse 2s infinite;
    }

    .sol-panel:hover .sol-icon {
        transform: rotate(45deg);
        background-color: var(--electric-blue);
        border-color: var(--electric-blue);
        color: white;
        animation: none;
        box-shadow: 0 0 15px rgba(0, 102, 255, 0.5);
    }

    .sol-panel:hover .sol-title {
        color: var(--electric-blue);
    }

    /* CTA styles adapted from d2c-branding.php */
    #cta-final {
        background: #0066ff;
        /* keep dark background */
        padding: 7rem 0;
        position: relative;
        overflow: hidden;
        border-top: 1px solid rgba(255, 255, 255, .05);
    }

    @media (max-width: 768px) {
        #cta-final {
            padding: 4.8rem 0;
        }
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

    .cta-inner {
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .cta-inner h2 {
        font-size: clamp(2.4rem, 5vw, 4rem);
        font-weight: 900;
        line-height: 1.05;
        color: #fff;
        margin-bottom: 1.2rem;
    }

    .cta-inner p {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, .85);
        max-width: 64ch;
        margin: 0 auto 1.8rem;
        line-height: 1.7;
    }

    .btn-white {
        background: #fff;
        color: var(--electric-blue);
        font-weight: 800;
        padding: 14px 40px;
        border-radius: .6rem;
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        text-decoration: none;
        box-shadow: 0 10px 36px rgba(0, 0, 0, .16);
    }

    .btn-white:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 48px rgba(0, 0, 0, .28);
    }

    .btn-outline {
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .18);
        padding: 12px 30px;
        border-radius: .6rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }

    .btn-outline:hover {
        border-color: rgba(255, 255, 255, .36);
    }

    .sol-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0.12;
        transition: transform .7s cubic-bezier(.4, 0, .2, 1), opacity .7s;
        z-index: 0;
        pointer-events: none;
    }

    .sol-panel:hover .sol-bg {
        transform: scale(1.1);
        opacity: 0.25;
    }

    .hero-line-dim {
        color: var(--g400);
        display: block;
    }

    .hero-line-accent {
        color: var(--electric-blue);
        display: block;
    }

    @media (min-width: 1024px) {

        .btn-white .material-symbols-outlined,
        .btn-outline .material-symbols-outlined,
        .hero-btn .material-symbols-outlined {
            display: inline-block;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }

        .btn-white:hover .material-symbols-outlined,
        .btn-outline:hover .material-symbols-outlined,
        .hero-btn:hover .material-symbols-outlined {
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

        .btn-white .material-symbols-outlined,
        .btn-outline .material-symbols-outlined,
        .hero-btn .material-symbols-outlined {
            opacity: 1;
            transform: none;
        }
    }

    @media (max-width: 768px) {

        #hero-section {
            height: auto;
            min-height: auto;
            padding-top: 4.5rem;
            padding-bottom: 2.5rem;
        }

        #hero-section .hero-content {
            padding-bottom: .25rem;
        }

        #hero-section .hero-text {
            margin-bottom: 1rem;
        }

        #hero-section .hero-sub {
            margin-bottom: 1.4rem;
        }

        .pin-container {
            height: auto;
            min-height: auto;
            padding: 0.5rem 0 1rem;
        }

        .horizontal-scroll-track {
            display: flex;
            width: 100% !important;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            gap: 1rem;
            padding-left: 1rem;
            padding-right: 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .horizontal-scroll-track::-webkit-scrollbar {
            display: none;
        }

        .challenge-card {
            padding: 1.5rem;
        }

        .challenge-card .w-20 {
            width: 4rem;
            height: 4rem;
            margin-bottom: 1.25rem;
        }

        .challenge-card h4 {
            font-size: 1.6rem;
            margin-bottom: .75rem;
        }

        .challenge-card p {
            font-size: 1rem;
        }

        .section-approach,
        .section-services,
        .section-process,
        .section-impact,
        #cta-final {
            padding-top: 3.25rem !important;
            padding-bottom: 3.25rem !important;
        }

        #approachSection>.max-w-[1440px]>.text-center,
        .section-process>.max-w-[1440px]>.text-center,
        .section-impact>.max-w-[1440px]>.text-center,
        .section-services>.max-w-[1440px]>.pm-wrap>.pm-kicker {
            margin-bottom: 2rem !important;
        }

        .section-services .service-ribbons {
            margin-top: 1.5rem;
            gap: .85rem;
        }

        .section-process .process-step {
            margin-bottom: 1.2rem;
            opacity: 1 !important;
            transform: none !important;
            padding-left: 0 !important;
        }

        .section-process .process-step .step-circle {
            position: static !important;
            margin-bottom: 1rem;
        }

        .section-process .process-step .flex-1 {
            width: 100%;
        }

        .section-impact .impact-grid {
            gap: .9rem;
        }

        #cta-final .cta-inner h2 {
            margin-bottom: .9rem;
        }

        #cta-final .cta-inner p {
            margin-bottom: 1.3rem;
        }
    }

    /* ─── Approach GSAP Sticky Scroll ─────────────────────────── */
    .ap-sticky-grid {
        display: grid;
        grid-template-columns: 36% 1fr;
        gap: 0;
        align-items: start;
    }

    @media(max-width:860px) {
        .ap-sticky-grid {
            grid-template-columns: 1fr;
        }

        .ap-sticky-left {
            position: relative !important;
            top: auto !important;
            padding-bottom: 1.25rem;
            padding-right: 0;
        }

        .ap-counter-num {
            font-size: 6rem !important;
        }

        .ap-right-col {
            display: grid !important;
            grid-auto-flow: column !important;
            grid-auto-columns: 84vw !important;
            gap: 1rem !important;
            overflow-x: auto !important;
            scroll-snap-type: x mandatory !important;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding-left: 0 !important;
            padding-right: 1rem !important;
            border-left: 0 !important;
        }

        .ap-right-col::-webkit-scrollbar {
            display: none;
        }

        .ap-step {
            width: 84vw !important;
            padding: 1.5rem 1.25rem !important;
            border-radius: 1rem !important;
            background: rgba(255, 255, 255, .03) !important;
            border: 1px solid rgba(255, 255, 255, .07) !important;
            min-height: 12rem !important;
            opacity: 1 !important;
            transform: none !important;
            scroll-snap-align: start !important;
        }

        .ap-step:first-child {
            padding-top: 1.5rem !important;
        }

        .ap-step-line {
            width: 56px !important;
            margin-bottom: 1.25rem !important;
        }

        .ap-step-title {
            transform: none !important;
            display: block;
            color: #fff;
        }

        .ap-step-desc {
            max-width: none !important;
            font-size: 1rem !important;
            line-height: 1.7 !important;
            transform: none !important;
            opacity: 1 !important;
            color: #cbd5e1 !important;
        }

        .ap-step-pill {
            opacity: 1 !important;
            transform: none !important;
        }
    }

    .ap-sticky-left {
        position: sticky;
        top: 25vh;
        padding: 0 3rem 0 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .ap-counter-eyebrow {
        font-size: .65rem;
        font-weight: 900;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .2);
        margin-bottom: 1rem;
    }

    #apCounterNum {
        font-size: clamp(8rem, 17vw, 15rem);
        font-weight: 900;
        line-height: .85;
        color: transparent;
        -webkit-text-stroke: 1px rgb(0, 102, 255);
        letter-spacing: -.08em;
        user-select: none;
        margin-bottom: 2rem;
        display: block;
    }

    .ap-dots {
        display: flex;
        flex-direction: column;
        gap: .6rem;
    }

    .ap-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .12);
        transition: background .3s, transform .3s, box-shadow .3s;
    }

    .ap-dot.on {
        background: var(--electric-blue);
        transform: scale(1.6);
        box-shadow: 0 0 8px rgba(0, 102, 255, .6);
    }

    .ap-right-col {
        border-left: 1px solid rgba(255, 255, 255, .07);
        padding-left: 4rem;
    }

    .ap-step {
        padding: 4.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, .05);
    }

    .ap-step:first-child {
        padding-top: 0;
    }

    .ap-step-tag {
        font-size: .62rem;
        font-weight: 900;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--electric-blue);
        margin-bottom: 1rem;
        display: block;
    }

    .ap-step-line {
        width: 0;
        height: 2px;
        background: var(--electric-blue);
        margin-bottom: 2rem;
        box-shadow: 0 0 8px rgba(0, 102, 255, .5);
    }

    .ap-step-title-wrap {
        overflow: hidden;
        margin-bottom: 1.2rem;
    }

    .ap-step-title {
        font-size: clamp(2rem, 3.8vw, 3.2rem);
        font-weight: 900;
        color: #fff;
        letter-spacing: -.04em;
        line-height: 1.05;
        transform: translateY(110%);
        display: block;
    }

    .ap-step-desc {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #475569;
        max-width: 500px;
        margin-bottom: 1.5rem;
        transform: translateY(18px);
        opacity: 0;
    }

    .ap-step-pill {
        display: inline-block;
        padding: .38rem 1rem;
        border-radius: 9999px;
        border: 1px solid rgba(0, 102, 255, .25);
        background: rgba(0, 102, 255, .07);
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: var(--electric-blue);
        opacity: 0;
        transform: translateY(8px);
    }
</style>

<main class="text-white selection:bg-[var(--electric-blue)] selection:text-white">

    <!-- 1. Hero Section (Scale & Parallax) -->
    <section class="relative h-screen flex flex-col items-center justify-center overflow-hidden px-4 mt-[60px]"
        id="hero-section">
        <!-- Abstract Background -->
        <div class="absolute inset-0 z-0 opacity-20">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60vw] h-[60vw] bg-[var(--electric-blue)] rounded-full blur-[150px] hero-glow"></div>
        </div>

        <div class="relative z-10 text-center max-w-[1440px] mx-auto w-full hero-content">
            <div class="inline-block mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm text-xs sm:text-sm font-bold tracking-[0.2em] uppercase text-white overflow-hidden">
                <span class="inline-block hero-badge"><?= htmlspecialchars($ec_hero['badge'] ?? 'Scale Your Online Store') ?></span>
            </div>

            <h1 class="text-[3rem] md:text-[3.5rem] font-black leading-[0.9] tracking-tighter uppercase mb-10 hero-text overflow-hidden">
                <div class="hero-line hero-line-dim pb-2"><?= htmlspecialchars($ec_hero['h1_line1'] ?? 'E-Commerce Marketing') ?></div>
                <div class="hero-line hero-line-accent pb-2"><?= htmlspecialchars($ec_hero['h1_line2_accent'] ?? 'Services in India') ?></div>
            </h1>

            <p class="text-xl md:text-3xl text-slate-400 max-w-4xl mx-auto font-light leading-relaxed hero-sub mb-12">
                <?= htmlspecialchars($ec_hero['hero_sub'] ?? 'We build high-performing, conversion-focused e-commerce systems that turn traffic into revenue.') ?>
            </p>

            <a href="<?= htmlspecialchars($ec_hero['btn_url'] ?? 'leadform.php') ?>"
                class="hero-btn relative z-50 inline-flex items-center gap-4 px-10 py-5 bg-white text-black font-bold text-lg uppercase tracking-widest rounded-full hover:scale-105 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer">
                <?= htmlspecialchars($ec_hero['btn_label'] ?? 'Get Ready For Growth') ?>
                <span class="material-symbols-outlined bg-black text-white rounded-full p-2 px-4 text-sm">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- 2. Core Problem (GSAP Horizontal Scroll) -->
    <section
        class="pin-container h-screen bg-[#05070a] flex flex-col justify-center border-t border-white/5 relative z-20 overflow-hidden">
        <div class="horizontal-scroll-track flex items-center gap-8 md:gap-16 px-8 md:px-[10vw] w-max">

            <!-- Intro Panel -->
            <div class="w-[85vw] md:w-[45vw] flex-shrink-0 pr-8 md:pr-20">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($ecom_sh['challenges']['eyebrow'] ?? 'The Friction') ?></h2>
                <h3 class="text-5xl md:text-7xl font-black tracking-tighter mb-8 leading-[1.1]">
                    <?= nl2br(htmlspecialchars($ecom_sh['challenges']['heading'] ?? "Why Many Stores \nFail to Scale.")) ?>
                </h3>
                <p class="text-xl md:text-2xl text-slate-400 leading-relaxed">
                    <?= htmlspecialchars($ecom_sh['challenges']['sub_text'] ?? 'Many businesses invest in products and advertising but still struggle with poor online sales because their platform is not built for performance. A weak website creates friction in the customer journey.') ?>
                </p>
            </div>

            <!-- Challenge Cards -->
            <?php
            foreach ($challenges as $ch) {
                echo '<div class="challenge-card w-[85vw] md:w-[30vw] flex-shrink-0 p-8 md:p-14 h-auto md:h-[50vh] flex flex-col justify-center">';
                echo '<div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-8 border border-white/10">';
                echo '<span class="material-symbols-outlined text-[var(--electric-blue)] text-4xl">' . htmlspecialchars($ch['icon']) . '</span>';
                echo '</div>';
                echo '<h4 class="text-3xl md:text-4xl font-bold mb-4">' . htmlspecialchars($ch['title']) . '</h4>';
                echo '<p class="text-lg md:text-xl text-slate-400">' . htmlspecialchars($ch['description']) . '</p>';
                echo '</div>';
            }
            ?>

            <!-- Outro Panel -->
            <div class="w-[85vw] md:w-[40vw] flex-shrink-0 pl-8 md:pl-20">
                <h3 class="text-4xl md:text-6xl font-black tracking-tighter mb-8 leading-[1.1]">
                    <?= htmlspecialchars($ecom_sh['challenges']['extra_text'] ?? 'Without the right foundation, marketing produces weak results.') ?>
                </h3>
            </div>

        </div>
    </section>

    <!-- 4. Solutions (Flex Hover Accordion Panels) -->
    <section class="bg-[#05070a] border-t border-white/5 relative z-40">
        <div class="pt-32 px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto text-center mb-16">
            <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($ecom_sh['solutions']['eyebrow'] ?? 'Platforms') ?></h2>
            <h3 class="text-5xl md:text-7xl font-black tracking-tighter"><?= htmlspecialchars($ecom_sh['solutions']['heading'] ?? 'Complete Solutions') ?></h3>
            <p class="text-xl text-slate-400 mt-6 max-w-3xl mx-auto"><?= htmlspecialchars($ecom_sh['solutions']['sub_text'] ?? 'End-to-end development tailored to your business model, customer journey, and growth goals.') ?></p>
        </div>

        <?php
        $tagColorMap = ['blue' => 'var(--electric-blue)', 'purple' => '#a855f7', 'pink' => '#ec4899'];
        $bgColorMap  = ['blue' => '#020617', 'purple' => '#05070a', 'pink' => '#020617'];
        ?>
        <div class="flex flex-col lg:flex-row w-full h-auto lg:h-[80vh] border-y border-white/5 overflow-hidden">
            <?php foreach ($ec_sols as $idx => $sol):
                $bullets = json_decode($sol['bullets_json'] ?? '[]', true) ?: [];
                $tc = $tagColorMap[$sol['tag_color']] ?? 'var(--electric-blue)';
                $bg = $bgColorMap[$sol['tag_color']] ?? '#020617';
                $border = $idx < count($ec_sols) - 1 ? 'border-b lg:border-b-0 lg:border-r' : '';
            ?>
            <div class="sol-panel flex-1 <?= $border ?> border-white/5 p-8 md:p-16 flex flex-col justify-end relative cursor-pointer group min-h-[40vh] lg:min-h-0" style="background-color:<?= $bg ?>;">
                <div class="sol-bg" style="background-image: url('<?= htmlspecialchars($appUrl . '/' . $sol['bg_image']) ?>');" ></div>
                <div class="relative z-20 w-full">
                    <div class="mb-6">
                        <span class="px-4 py-1.5 border rounded-full text-sm font-bold tracking-widest uppercase" style="border-color:<?= $tc ?>;color:<?= $tc ?>;"><?= htmlspecialchars($sol['tag_label']) ?></span>
                    </div>
                    <h4 class="sol-title text-4xl md:text-5xl font-black tracking-tighter"><?= htmlspecialchars($sol['title']) ?> <span class="material-symbols-outlined sol-icon text-2xl">add</span></h4>
                    <div class="sol-content w-full lg:w-[400px]">
                        <p class="text-lg md:text-xl text-white mb-6"><?= htmlspecialchars($sol['description']) ?></p>
                        <ul class="space-y-3 text-white">
                            <?php foreach ($bullets as $b): ?>
                            <li class="flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full" style="background:<?= $tc ?>;"></span> <?= htmlspecialchars($b) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- 3. Our Approach (GSAP Sticky Counter + Scroll Reveal) -->
    <section class="py-20 md:py-32 bg-[#020617] relative z-30" id="approachSection">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 md:mb-24 max-w-4xl mx-auto">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($ecom_sh['approaches']['eyebrow'] ?? 'Strategic Focus') ?></h2>
                <h3 class="text-5xl md:text-7xl font-black tracking-tighter"><?= htmlspecialchars($ecom_sh['approaches']['heading'] ?? 'Our Approach to E-Commerce Growth') ?></h3>
                <p class="text-xl text-slate-400 mt-8"><?= htmlspecialchars($ecom_sh['approaches']['sub_text'] ?? 'At Digifyce, we do not just build websites, we create revenue-generating e-commerce systems.') ?></p>
            </div>

            <div class="ap-sticky-grid">
                <!-- Sticky left: live number + dots -->
                <div class="ap-sticky-left">
                    <span class="ap-counter-eyebrow">Currently viewing</span>
                    <span id="apCounterNum">01</span>
                    <div class="ap-dots">
                        <div class="ap-dot on"></div>
                        <div class="ap-dot"></div>
                        <div class="ap-dot"></div>
                        <div class="ap-dot"></div>
                    </div>
                </div>

                <!-- Scrollable right: approach steps -->
                <div class="ap-right-col">
                    <?php
                    $totalApproaches = count($approaches);
                    foreach ($approaches as $idx => $ap) {
                        $total = str_pad($totalApproaches, 2, '0', STR_PAD_LEFT);
                        echo '<div class="ap-step" data-step="' . $idx . '">';
                        echo '<span class="ap-step-tag">' . htmlspecialchars($ap['tag']) . '</span>';
                        echo '<div class="ap-step-line"></div>';
                        echo '<div class="ap-step-title-wrap"><span class="ap-step-title">' . htmlspecialchars($ap['title']) . '</span></div>';
                        echo '<p class="ap-step-desc">' . htmlspecialchars($ap['description']) . '</p>';
                        echo '<span class="ap-step-pill">' . htmlspecialchars($ap['number']) . ' / ' . $total . '</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>



    <!-- 5. Process (Interactive Reveal Steps) -->
    <section class="py-20 md:py-32 bg-[#020617] relative z-50">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 md:mb-24">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($ecom_sh['process']['eyebrow'] ?? 'Execution') ?></h2>
                <h3 class="text-5xl md:text-7xl font-black tracking-tighter"><?= htmlspecialchars($ecom_sh['process']['heading'] ?? 'Our Development Process') ?></h3>
            </div>

            <div class="max-w-4xl mx-auto relative">
                <!-- Vertical animated line -->
                <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-px bg-white/10 hidden sm:block">
                    <div class="process-progress w-full bg-[var(--electric-blue)] h-0"></div>
                </div>

                <?php
                foreach ($steps as $idx => $step) {
                    $isEven = $idx % 2 !== 0;
                    $flexDir = $isEven ? 'md:flex-row-reverse text-left md:text-right' : 'md:flex-row text-left';
                    $align = $isEven ? 'md:items-end' : 'md:items-start';

                    echo '<div class="process-step flex flex-col ' . $flexDir . ' items-start md:items-center gap-8 md:gap-20 mb-16 md:mb-24 relative opacity-0 translate-y-10 pl-20 sm:pl-0">';
                    echo '<div class="absolute sm:static left-0 sm:left-auto top-0 sm:top-auto w-16 h-16 rounded-full bg-[#05070a] border-2 border-white/20 text-white font-bold text-2xl flex items-center justify-center z-10 shrink-0 step-circle transition-colors duration-500">';
                    echo htmlspecialchars($step['step_number']);
                    echo '</div>';
                    echo '<div class="flex-1 flex flex-col ' . $align . '">';
                    echo '<h4 class="text-3xl md:text-4xl font-bold text-white mb-4 tracking-tight">' . htmlspecialchars($step['title']) . '</h4>';
                    echo '<p class="text-lg text-slate-400 leading-relaxed max-w-md">' . htmlspecialchars($step['description']) . '</p>';
                    echo '</div>';
                    echo '<div class="hidden md:block flex-1"></div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- 6. Why Choose Digifyce -->
    <section class="py-20 md:py-32 bg-[#05070a] relative z-50 border-t border-white/5">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-24">

                <!-- Left: Sticky Intro -->
                <div class="lg:w-1/3 lg:sticky lg:top-32 h-fit">
                    <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($ecom_sh['why']['eyebrow'] ?? 'Our Advantage') ?></h2>
                    <h3 class="text-5xl md:text-6xl font-black tracking-tighter mb-6"><?= htmlspecialchars($ecom_sh['why']['heading'] ?? 'Why Choose Digifyce') ?></h3>
                    <p class="text-lg text-slate-400 leading-relaxed mb-6">
                        <?= htmlspecialchars($ecom_sh['why']['sub_text'] ?? 'At Digifyce, we value our clients by combining strategy, design, and performance to create e-commerce platforms that truly support business growth and long-term scalability.') ?>
                    </p>
                    <p class="text-lg text-slate-300 font-bold border-l-2 border-[var(--electric-blue)] pl-4 mb-8">
                        <?= htmlspecialchars($ecom_sh['why']['extra_text'] ?? 'We do not just build stores, we build sales engines.') ?>
                    </p>
                    <div class="mt-8">
                        <a href="<?= htmlspecialchars($ecom_sh['why']['btn_url'] ?? 'leadform.php') ?>" class="btn-white">
                            <?= htmlspecialchars($ecom_sh['why']['btn_label'] ?? 'Build Your Sales Engine') ?>
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Points Grid (Mobile Slider) -->
                <div class="lg:w-2/3">
                    <h4 class="text-2xl font-bold mb-8 text-white">What makes us different:</h4>

                    <div
                        class="flex md:grid md:grid-cols-2 gap-6 overflow-x-auto md:overflow-visible snap-x snap-mandatory touch-pan-x pb-8 md:pb-0 -mx-4 px-4 md:mx-0 md:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                        <?php foreach ($ec_why as $wp): ?>
                        <div class="w-[85vw] md:w-auto flex-shrink-0 snap-start bg-white/5 p-8 rounded-3xl border border-white/10 hover:border-[var(--electric-blue)]/50 transition-colors">
                            <span class="material-symbols-outlined text-[var(--electric-blue)] text-3xl mb-4"><?= htmlspecialchars($wp['icon']) ?></span>
                            <h5 class="text-xl font-bold mb-2 text-white"><?= htmlspecialchars($wp['title']) ?></h5>
                            <p class="text-slate-400 text-sm leading-relaxed"><?= htmlspecialchars($wp['description']) ?></p>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 7. Impact & CTA (converted to d2c-style full-bleed CTA) -->
    <section id="cta-final" class="py-20 md:py-32 bg-[#05070a] relative z-50 overflow-hidden">
        <!-- Abstract gradient -->
        <div
            class="absolute top-0 right-0 w-[80vw] h-[80vw] bg-[var(--electric-blue)] rounded-full blur-[200px] opacity-10 pointer-events-none translate-x-1/3 -translate-y-1/3">
        </div>

        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="cta-inner">
                <div class="cta-bg-text"><?= htmlspecialchars($ec_cta['bg_text'] ?? 'GROW') ?></div>
                <h2 class="text-5xl md:text-6xl font-black tracking-tighter mb-6"><?= htmlspecialchars($ec_cta['heading'] ?? "Let's Build a Store That Sells More.") ?></h2>
                <p class="text-xl text-slate-400 mb-8"><?= htmlspecialchars($ec_cta['description'] ?? 'Your online store should be your strongest sales channel—not your biggest limitation.') ?></p>

                <div class="max-w-[640px] mx-auto" style="margin-top:1.25rem;">
                    <a href="<?= htmlspecialchars($ec_cta['btn_url'] ?? 'leadform.php') ?>"
                        class="btn-white w-full inline-flex items-center justify-center gap-4 px-10 py-5 text-lg uppercase tracking-widest rounded-xl">
                        <?= htmlspecialchars($ec_cta['btn_label'] ?? 'Build Your Store Today') ?>
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <p class="mt-6 text-sm text-slate-500 font-bold uppercase tracking-widest"><?= htmlspecialchars($ecom_sh['cta']['sub_text'] ?? 'We build sales engines.') ?></p>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    window.addEventListener("load", (event) => {
        // Delay initialization to wait for the preloader to finish fading out
        setTimeout(() => {
            // Register ScrollTrigger
            gsap.registerPlugin(ScrollTrigger);

            // 1. Hero Animation
            const tlHero = gsap.timeline({ defaults: { ease: "power4.out" } });
            tlHero.from(".hero-badge", { y: 50, opacity: 0, duration: 1 })
                .from(".hero-line", { y: 100, opacity: 0, duration: 1.2, stagger: 0.2 }, "-=0.6")
                .from(".hero-sub", { y: 30, opacity: 0, duration: 1 }, "-=0.8")
                .from(".hero-btn", { scale: 0.9, opacity: 0, duration: 0.8 }, "-=0.6")
                .from(".hero-glow", { scale: 0, opacity: 0, duration: 2 }, "-=1.5");

            // 2. Horizontal Scroll for Challenges
            let track = document.querySelector(".horizontal-scroll-track");
            if (track) {
                if (window.innerWidth <= 860) {
                    track.style.transform = 'none';
                } else {
                    let trackWidth = track.scrollWidth;
                    let windowWidth = window.innerWidth;

                    gsap.to(track, {
                        x: () => -(trackWidth - windowWidth) + "px",
                        ease: "none",
                        scrollTrigger: {
                            trigger: ".pin-container",
                            pin: true,
                            scrub: 1,
                            // The end distance controls the scroll speed
                            end: () => "+=" + (trackWidth)
                        }
                    });
                }
            }

            // 3. Process Steps Fade In & Line Draw
            // Animate the vertical line
            gsap.to(".process-progress", {
                height: "100%",
                ease: "none",
                scrollTrigger: {
                    trigger: ".process-progress",
                    start: "top 50%",
                    end: "bottom 50%",
                    endTrigger: ".process-step:last-child",
                    scrub: true
                }
            });

            // Animate each step
            gsap.utils.toArray('.process-step').forEach((step, i) => {
                gsap.to(step, {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    scrollTrigger: {
                        trigger: step,
                        start: "top 80%",
                        onEnter: () => {
                            // Highlight the circle when scrolled into view
                            const circle = step.querySelector('.step-circle');
                            if (circle) {
                                circle.style.borderColor = 'var(--electric-blue)';
                                circle.style.backgroundColor = 'rgba(0,102,255,0.1)';
                                circle.style.color = 'var(--electric-blue)';
                            }
                        }
                    }
                });
            });

            // 4. Fade up impact section
            gsap.from("#impact-cta", {
                y: 100,
                opacity: 0,
                duration: 1.2,
                scrollTrigger: {
                    trigger: "#impact-cta",
                    start: "top 85%"
                }
            });
        }, 500); // 500ms delay to ensure preloader is hidden
    });
</script>

<script>
    /* ── Approach GSAP Sticky Counter ────────────────────────── */
    window.addEventListener('load', () => {
        setTimeout(() => {
            const counterEl = document.getElementById('apCounterNum');
            const dots = document.querySelectorAll('.ap-dot');
            const steps = Array.from(document.querySelectorAll('.ap-step'));
            const nums = ['01', '02', '03', '04'];
            const isMobileApproach = window.matchMedia('(max-width: 860px)').matches;
            let current = -1;

            function flipCounter(idx) {
                if (idx === current) return;
                current = idx;
                gsap.to(counterEl, {
                    scaleY: 0, opacity: 0, duration: .18, ease: 'power2.in',
                    onComplete() {
                        counterEl.textContent = nums[idx];
                        gsap.to(counterEl, { scaleY: 1, opacity: 1, duration: .32, ease: 'back.out(1.5)' });
                    }
                });
                dots.forEach((d, i) => d.classList.toggle('on', i === idx));
            }

            if (isMobileApproach) {
                const scroller = document.querySelector('.ap-right-col');

                if (scroller) {
                    const syncMobileCounter = () => {
                        const containerRect = scroller.getBoundingClientRect();
                        const containerCenter = containerRect.left + (containerRect.width / 2);
                        let activeIdx = 0;
                        let bestDistance = Number.POSITIVE_INFINITY;

                        steps.forEach((step, idx) => {
                            const rect = step.getBoundingClientRect();
                            const stepCenter = rect.left + (rect.width / 2);
                            const distance = Math.abs(stepCenter - containerCenter);

                            if (distance < bestDistance) {
                                bestDistance = distance;
                                activeIdx = idx;
                            }
                        });

                        flipCounter(activeIdx);
                    };

                    let ticking = false;

                    scroller.addEventListener('scroll', () => {
                        if (ticking) return;
                        ticking = true;
                        requestAnimationFrame(() => {
                            syncMobileCounter();
                            ticking = false;
                        });
                    }, { passive: true });

                    window.addEventListener('resize', syncMobileCounter);
                    syncMobileCounter();
                }
            } else {
                steps.forEach((step, idx) => {
                    const line = step.querySelector('.ap-step-line');
                    const title = step.querySelector('.ap-step-title');
                    const desc = step.querySelector('.ap-step-desc');
                    const pill = step.querySelector('.ap-step-pill');

                    ScrollTrigger.create({
                        trigger: step,
                        start: 'top 62%',
                        onEnter() {
                            flipCounter(idx);
                            gsap.to(line, { width: 56, duration: .55, ease: 'power2.out' });
                            gsap.to(title, { y: 0, duration: .65, ease: 'power3.out', delay: .08 });
                            gsap.to(desc, { y: 0, opacity: 1, duration: .55, ease: 'power2.out', delay: .22 });
                            gsap.to(pill, { y: 0, opacity: 1, duration: .45, ease: 'power2.out', delay: .36 });
                        },
                        onEnterBack() {
                            flipCounter(idx);
                        }
                    });
                });

                // trigger first step immediately
                flipCounter(0);
            }
        }, 520);
    });
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>