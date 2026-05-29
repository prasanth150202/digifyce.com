<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'market-manage');
$pageTitle = $_seo['meta_title'] ?: 'Marketplace Management Services in India – Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'Scale Your Brand on Amazon, Flipkart, and Leading Online Marketplaces. Complete marketplace management, SEO, and ads.';
$bodyClass = 'market-manage-page';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo        = Database::getInstance();
$challenges  = $_pdo->query("SELECT * FROM mktplace_challenges WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps       = $_pdo->query("SELECT * FROM mktplace_steps WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_hero     = $_pdo->query("SELECT * FROM mktplace_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$mk_approach = $_pdo->query("SELECT * FROM mktplace_approach_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_impacts  = $_pdo->query("SELECT * FROM mktplace_impacts WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_why      = $_pdo->query("SELECT * FROM mktplace_why_bullets WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_cta      = $_pdo->query("SELECT * FROM mktplace_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$mk_sh       = [];
foreach ($_pdo->query("SELECT * FROM mktplace_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $_r) { $mk_sh[$_r['slug']] = $_r; }
$mk_hero_icons = $_pdo->query("SELECT * FROM mktplace_hero_icons WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_svc_blocks = $_pdo->query("SELECT * FROM mktplace_service_blocks WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_svc_cards_raw = $_pdo->query("SELECT * FROM mktplace_service_block_cards WHERE is_active=1 ORDER BY service_block_id, sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mk_svc_cards = [];
foreach ($mk_svc_cards_raw as $_c) { $mk_svc_cards[$_c['service_block_id']][] = $_c; }
include __DIR__ . '/app/views/header.php';
?>

<style>
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

    /* Floating Animation for Hero */
    @keyframes float {
        0% {
            transform: translateY(0px) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(5deg);
        }

        100% {
            transform: translateY(0px) rotate(0deg);
        }
    }

    .float-element {
        animation: float 6s ease-in-out infinite;
    }

    .float-delayed {
        animation: float 8s ease-in-out infinite 1s;
    }

    /* Spotlight Grid Styles */
    .spotlight-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: inherit;
        padding: 2px;
        background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .spotlight-overlay {
        background: radial-gradient(800px circle at var(--mouse-x, 0px) var(--mouse-y, 0px), rgba(0, 102, 255, 0.15), transparent 40%);
    }

    /* Approach Accordion */
    .approach-accordion {
        transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        cursor: pointer;
        overflow: hidden;
    }

    .approach-accordion .app-content {
        max-height: 0;
        opacity: 0;
        transition: all 0.5s ease;
    }

    .approach-accordion.active {
        flex: 3;
        background: rgba(0, 102, 255, 0.05);
        border-color: var(--electric-blue);
    }

    .approach-accordion.active .app-content {
        max-height: 300px;
        opacity: 1;
        margin-top: 1rem;
    }

    /* ─── MOBILE SCROLL GRID ────────────── */
    @media (max-width: 768px) {
        #hero-section {
            padding-top: 4rem;
            padding-bottom: 2.25rem;
            min-height: auto;
        }

        .text-center.mb-16.md\:mb-24,
        .mb-16.md\:mb-24 {
            margin-bottom: 1.5rem !important;
        }

        .py-20.md\:py-32,
        .py-20.md\:py-32.bg-\[\#020617\],
        .py-20.md\:py-32.bg-\[\#05070a\] {
            padding-top: 2.75rem !important;
            padding-bottom: 2.75rem !important;
        }

        .service-sticky-container {
            padding-left: 0 !important;
        }

        .service-block {
            margin-bottom: 1.5rem;
        }

        #process-section {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .h-step {
            height: auto;
            min-height: 88vh;
        }

        .h-step-content {
            padding: 0 1rem;
        }

        #cta-final {
            margin-top: 1rem;
        }

        #cta-final .p-8.md\:p-24 {
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

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

        .mobile-scroll-grid>* {
            flex: 0 0 85vw !important;
            scroll-snap-align: start;
            margin-right: 1rem;
            min-width: 260px;
        }
    }

    .approach-accordion.active .app-icon {
        background-color: var(--electric-blue);
        color: white;
    }

    @media (max-width: 767px) {
        .approach-accordion {
            flex: 0 0 66vw;
            min-width: 75vw;
            min-height: 280px;
        }

        .approach-accordion.active {
            flex: 0 0 66vw;
        }

        .approach-accordion .app-content {
            max-height: none;
            opacity: 1;
            margin-top: 0.75rem;
        }

        .approach-accordion:not(.active) {
            opacity: 0.85;
        }

        .approach-accordion.active {
            opacity: 1;
            background: rgba(0, 102, 255, 0.08);
        }

        .approach-accordion.active .app-content {
            max-height: none;
            opacity: 1;
        }
    }

    /* Service Sticky Sections */
    .service-sticky-container {
        border-left: 1px solid rgba(255, 255, 255, 0.1);
    }

    .service-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #1e293b;
        position: absolute;
        left: -6px;
        top: 32px;
        transition: background 0.3s ease;
    }

    .service-block:hover .service-dot {
        background: var(--electric-blue);
        box-shadow: 0 0 10px var(--electric-blue);
    }

    /* Mobile horizontal scrolling layouts */
    .mobile-scroll-x {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #1e293b #030508;
    }

    .mobile-scroll-x::-webkit-scrollbar {
        height: 6px;
    }

    .mobile-scroll-x::-webkit-scrollbar-track {
        background: #030508;
    }

    .mobile-scroll-x::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 999px;
    }

    .mobile-scroll-x::-webkit-scrollbar-thumb:hover {
        background: var(--electric-blue);
    }

    .mobile-snap {
        scroll-snap-type: x mandatory;
    }

    .mobile-snap>* {
        scroll-snap-align: start;
    }

    .touch-pan-x {
        touch-action: pan-x;
    }

    .mobile-card-rail {
        align-items: stretch;
    }

    .mobile-card-rail>* {
        height: 100%;
    }

    /* CTA styles (copied/adapted from d2c-branding.php) */
    .btn-white {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.9rem 1.5rem;
        background: linear-gradient(90deg, #06f, #00b4ff);
        color: #041025;
        font-weight: 800;
        border-radius: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        box-shadow: 0 8px 30px rgba(3, 102, 255, 0.18);
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .btn-white:hover {
        transform: translateY(-3px);
        filter: saturate(1.05);
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        padding: 0.9rem 1.25rem;
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-weight: 700;
        border-radius: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.04);
        transform: translateY(-2px);
    }

    /* CTA layout tweaks */
    #cta-final {
        min-height: 360px;
        display: flex;
        align-items: center;
    }

    #cta-final .cta-bg-text {
        /* center the large background word */
        left: 50%;
        top: 50%;
        right: auto;
        transform: translate(-50%, -50%);
        color: rgba(148, 163, 184, 0.20);
        /* subtle gray */
        pointer-events: none;
        -webkit-font-smoothing: antialiased;
    }

    @media (max-width: 1024px) {
        #cta-final {
            min-height: auto;
        }

        #cta-final .cta-bg-text {
            font-size: 6rem;
            right: auto;
            top: 80px;
        }
    }

    /* Horizontal Scroll Process */
    .h-scroll-container {
        display: flex;
        flex-wrap: nowrap;
        width: 600%;
        /* 6 steps */
    }

    .h-step {
        width: 100vw;
        height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .h-step-content {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 10%;
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

    /* Glassmorphic Brand Cards */
    .glass-logo-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .glass-logo-card:hover {
        background: rgba(0, 102, 255, 0.1);
        border-color: rgba(0, 102, 255, 0.5);
        transform: scale(1.15) translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 102, 255, 0.25),
            0 0 30px rgba(0, 102, 255, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .glass-logo-card svg {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Connecting Neon Lines Style */
    .neon-line-pulse {
        stroke-linecap: round;
        filter: drop-shadow(0 0 4px var(--electric-blue));
        opacity: 0.55;
        stroke-dasharray: 8 8;
        animation: energy-flow 1.5s linear infinite;
        transition: stroke-width 0.3s ease, opacity 0.3s ease, filter 0.3s ease;
    }

    .float-logo-wrapper:hover .neon-line-pulse {
        stroke-dasharray: 6 6;
        stroke-width: 3.5px;
        opacity: 0.95;
        filter: drop-shadow(0 0 8px #00e5ff);
    }

    @keyframes energy-flow {
        to {
            stroke-dashoffset: -16;
        }
    }

    /* Floating Path animations for each brand card to look natural & organic */
    @keyframes float-p1 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        33% {
            transform: translate(12px, -20px) rotate(4deg);
        }

        66% {
            transform: translate(-8px, -12px) rotate(-3deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    @keyframes float-p2 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        50% {
            transform: translate(-15px, -25px) rotate(-6deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    @keyframes float-p3 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        33% {
            transform: translate(-12px, 15px) rotate(-4deg);
        }

        66% {
            transform: translate(10px, -15px) rotate(5deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    @keyframes float-p4 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        50% {
            transform: translate(18px, -12px) rotate(8deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    @keyframes float-p5 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        33% {
            transform: translate(-8px, -20px) rotate(-5deg);
        }

        66% {
            transform: translate(12px, 12px) rotate(3deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    @keyframes float-p6 {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        50% {
            transform: translate(15px, 20px) rotate(6deg);
        }

        100% {
            transform: translate(0, 0) rotate(0deg);
        }
    }

    .float-logo-1 {
        animation: float-p1 9s ease-in-out infinite;
    }

    .float-logo-2 {
        animation: float-p2 11s ease-in-out infinite;
    }

    .float-logo-3 {
        animation: float-p3 10s ease-in-out infinite;
    }

    .float-logo-4 {
        animation: float-p4 8s ease-in-out infinite;
    }

    .float-logo-5 {
        animation: float-p5 12s ease-in-out infinite;
    }

    .float-logo-6 {
        animation: float-p6 13s ease-in-out infinite;
    }

    /* Custom styles for the 3D isometric cyber box */
    .cyber-box-container {
        position: relative;
        width: 100%;
        max-width: 450px;
        height: 450px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 1023px) {
        .cyber-box-container {
            max-width: 320px;
            height: 320px;
            margin-top: 2rem;
        }

        .glass-logo-card {
            width: 56px;
            height: 56px;
            padding: 10px;
            border-radius: 14px;
        }
    }
</style>

<main class="text-white selection:bg-[var(--electric-blue)] selection:text-white">

    <!-- 1. Hero Section -->
    <section
        class="relative min-h-screen flex flex-col justify-center overflow-hidden px-4 pt-32 pb-20 md:py-0 mt-[30px]"
        id="hero-section">
        <!-- Abstract Background -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div
                class="absolute top-1/4 right-1/4 w-[40vw] h-[40vw] bg-[var(--electric-blue)] rounded-full blur-[150px] hero-glow">
            </div>
            <div
                class="absolute bottom-1/4 left-1/4 w-[30vw] h-[30vw] bg-[var(--electric-blue)]/25 rounded-full blur-[150px] hero-glow">
            </div>
        </div>
        <div class="relative z-10 max-w-[1440px] mx-auto w-full hero-content grid lg:grid-cols-12 gap-12 items-center">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 flex flex-col items-start text-left z-10">
                <div
                    class="inline-block mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm text-xs sm:text-sm font-bold tracking-[0.2em] uppercase text-white overflow-hidden">
                    <span class="inline-block hero-badge text-[var(--electric-blue)]"><?= htmlspecialchars($mk_hero['badge'] ?? 'Scale Your Brand On Amazon & Flipkart') ?></span>
                </div>

                <h1
                    class="text-[3rem] md:text-[3.5rem] font-black leading-[1] tracking-tighter uppercase mb-8 text-white max-w-6xl">
                    <div class="hero-line pb-2 text-[var(--electric-blue)]/70"><?= htmlspecialchars($mk_hero['h1_line1'] ?? 'Marketplace Management') ?></div>
                    <div class="hero-line pb-2 text-[var(--electric-blue)]"><?= htmlspecialchars($mk_hero['h1_line2_accent'] ?? 'Services in India') ?></div>
                </h1>

                <p class="text-lg md:text-2xl text-slate-400 max-w-3xl font-light leading-relaxed hero-sub mb-12">
                    <?= htmlspecialchars($mk_hero['hero_sub'] ?? 'Selling requires more than just product uploads. We provide complete listing optimization, SEO, account management and advertising to achieve profitable long-term growth.') ?>
                </p>

                <a href="<?= htmlspecialchars($mk_hero['btn_url'] ?? 'leadform.php') ?>"
                    class="hero-btn relative z-50 inline-flex items-center gap-4 px-8 py-4 bg-[var(--electric-blue)] text-white font-bold text-lg uppercase tracking-widest rounded-full hover:bg-blue-600 transition-[transform,background-color,box-shadow] hover:scale-105 hover:shadow-[0_0_30px_rgba(0,102,255,0.4)] cursor-pointer">
                    <?= htmlspecialchars($mk_hero['btn_label'] ?? "Let's Grow Your Sales") ?>
                    <span
                        class="material-symbols-outlined bg-white text-[var(--electric-blue)] rounded-full p-2 px-4 text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Right Interactive Graphic -->
            <div class="lg:col-span-5 flex items-center justify-center w-full relative">
                <div class="cyber-box-container">

                    <!-- Open Box Isometric Shape -->
                    <svg viewBox="0 0 400 400"
                        class="w-full h-full relative z-20 drop-shadow-[0_0_50px_rgba(0,102,255,0.3)]">
                        <!-- Bottom Shadow / Glow -->
                        <ellipse cx="200" cy="320" rx="100" ry="20" fill="url(#box-glow-grad)" opacity="0.6" />

                        <g transform="translate(0, 20)">
                            <!-- Inside Dark Glow -->
                            <polygon points="200,220 100,170 200,120 300,170" fill="url(#box-inside-glow)" />

                            <!-- Box Back Left Lid -->
                            <polygon points="100,170 40,110 140,80 200,120" fill="#111827"
                                stroke="rgba(0, 102, 255, 0.4)" stroke-width="2" />

                            <!-- Box Back Right Lid -->
                            <polygon points="300,170 360,110 260,80 200,120" fill="#111827"
                                stroke="rgba(0, 102, 255, 0.4)" stroke-width="2" />

                            <!-- Box Left Side Panel -->
                            <polygon points="200,300 100,250 100,170 200,220" fill="url(#box-left-grad)"
                                stroke="#0066FF" stroke-width="2" />

                            <!-- Box Right Side Panel -->
                            <polygon points="200,300 300,250 300,170 200,220" fill="url(#box-right-grad)"
                                stroke="#0066FF" stroke-width="2" />

                            <!-- Front Left Lid (folded down) -->
                            <polygon points="100,250 40,290 140,325 200,300" fill="#0b0f19"
                                stroke="rgba(0, 102, 255, 0.4)" stroke-width="1.5" />

                            <!-- Front Right Lid (folded down) -->
                            <polygon points="300,250 360,290 260,325 200,300" fill="#0b0f19"
                                stroke="rgba(0, 102, 255, 0.4)" stroke-width="1.5" />

                            <!-- Glowing laser core shooting out of the box -->
                            <line x1="200" y1="180" x2="200" y2="0" stroke="url(#laser-grad)" stroke-width="10"
                                stroke-linecap="round" />
                            <circle cx="200" cy="170" r="10" fill="#00e5ff" filter="blur(3px)" />
                        </g>

                        <!-- Gradients -->
                        <defs>
                            <radialGradient id="box-glow-grad" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#0066FF" stop-opacity="1" />
                                <stop offset="100%" stop-color="#030508" stop-opacity="0" />
                            </radialGradient>
                            <linearGradient id="box-left-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0b1329" />
                                <stop offset="100%" stop-color="#1e293b" />
                            </linearGradient>
                            <linearGradient id="box-right-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#1e293b" />
                                <stop offset="100%" stop-color="#0b1329" />
                            </linearGradient>
                            <radialGradient id="box-inside-glow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#00e5ff" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                            </radialGradient>
                            <linearGradient id="laser-grad" x1="0%" y1="100%" x2="0%" y2="0%">
                                <stop offset="0%" stop-color="#00e5ff" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#0066FF" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                    </svg>

                    <!-- Floating Brand Cards Container -->
                    <div class="absolute inset-0 w-full h-full z-30 pointer-events-none">

                        <!-- Shared SVG Gradients for Connecting Energy Lines -->
                        <svg class="absolute w-0 h-0 pointer-events-none" style="visibility: hidden;">
                            <defs>
                                <linearGradient id="grad-line-1" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#FF9900" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                                <linearGradient id="grad-line-2" x1="100%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#FFE500" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                                <linearGradient id="grad-line-3" x1="0%" y1="50%" x2="100%" y2="50%">
                                    <stop offset="0%" stop-color="#E11B74" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                                <linearGradient id="grad-line-4" x1="100%" y1="50%" x2="0%" y2="50%">
                                    <stop offset="0%" stop-color="#96BF48" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                                <linearGradient id="grad-line-5" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#96588A" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                                <linearGradient id="grad-line-6" x1="100%" y1="100%" x2="0%" y2="0%">
                                    <stop offset="0%" stop-color="#FC2779" />
                                    <stop offset="100%" stop-color="#0066FF" stop-opacity="0.2" />
                                </linearGradient>
                            </defs>
                        </svg>

                        <?php
                        $_icon_positions = [
                            1 => ['style' => 'top: 10%; left: 8%',  'path' => 'M 0 0 Q 30 70, 132 114'],
                            2 => ['style' => 'top: 15%; right: 5%', 'path' => 'M 0 0 Q -40 60, -144 94'],
                            3 => ['style' => 'top: 42%; left: -3%', 'path' => 'M 0 0 Q 80 -10, 176 -14'],
                            4 => ['style' => 'top: 48%; right: -5%','path' => 'M 0 0 Q -80 -20, -184 -38'],
                        ];
                        $_icon_defaults = [
                            ['title'=>'Amazon',  'svg_file'=>'amazon-color-svgrepo-com.svg'],
                            ['title'=>'Flipkart','svg_file'=>'brand-flipkart-svgrepo-com.svg'],
                            ['title'=>'Myntra',  'svg_file'=>'myntra-svgrepo-com.svg'],
                            ['title'=>'Meesho',  'svg_file'=>'meesho-seeklogo.svg'],
                        ];
                        $_icons = !empty($mk_hero_icons) ? array_slice($mk_hero_icons, 0, 4) : $_icon_defaults;
                        foreach ($_icons as $_pi => $_icon):
                            $_pos = $_icon_positions[$_pi + 1] ?? $_icon_positions[1];
                        ?>
                        <div class="float-logo-wrapper absolute float-logo-<?= $_pi + 1 ?> pointer-events-none"
                            style="<?= $_pos['style'] ?>; width: 72px; height: 72px;">
                            <svg class="absolute overflow-visible pointer-events-none"
                                style="top: 50%; left: 50%; z-index: -1;">
                                <path d="<?= $_pos['path'] ?>" fill="none" stroke="url(#grad-line-<?= $_pi + 1 ?>)"
                                    stroke-width="2" class="neon-line-pulse" />
                            </svg>
                            <div class="glass-logo-card pointer-events-auto select-none" title="<?= htmlspecialchars($_icon['title']) ?>">
                                <?php
                                $_svg_path = __DIR__ . '/public/assets/svg-logo/' . basename($_icon['svg_file']);
                                echo file_exists($_svg_path) ? file_get_contents($_svg_path) : '';
                                ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- WooCommerce Card (Bottom Left) -->
                        <!-- <div class="float-logo-wrapper absolute float-logo-5 pointer-events-none"
                            style="bottom: 8%; left: 12%; width: 72px; height: 72px;">
                            <svg class="absolute overflow-visible pointer-events-none"
                                style="top: 50%; left: 50%; z-index: -1;">
                                <path d="M 0 0 Q 40 -80, 116 -142" fill="none" stroke="url(#grad-line-5)"
                                    stroke-width="2" class="neon-line-pulse" />
                            </svg>
                            <div class="glass-logo-card pointer-events-auto select-none" title="WooCommerce">
                               
                            </div>
                        </div> -->

                        <!-- Shopping Card (Bottom Right) -->
                        <!-- <div class="float-logo-wrapper absolute float-logo-6 pointer-events-none"
                            style="bottom: 12%; right: 10%; width: 72px; height: 72px;">
                            <svg class="absolute overflow-visible pointer-events-none"
                                style="top: 50%; left: 50%; z-index: -1;">
                                <path d="M 0 0 Q -40 -80, -124 -126" fill="none" stroke="url(#grad-line-6)"
                                    stroke-width="2" class="neon-line-pulse" />
                            </svg>
                            <div class="glass-logo-card pointer-events-auto select-none" title="E-Commerce Stores">
                                
                            </div>
                        </div> -->

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Core Problem (Interactive Spotlight Grid) -->
    <section class="py-20 md:py-32 bg-[#05070a] border-y border-white/5 relative z-20" id="friction-section">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 md:mb-24 max-w-3xl mx-auto">
                <h2
                    class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6 flex items-center justify-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-[var(--electric-blue)] animate-pulse"></span> <?= htmlspecialchars($mk_sh['challenges']['eyebrow'] ?? 'The Struggle') ?>
                </h2>
                <h3 class="text-4xl md:text-6xl font-black tracking-tighter mb-6 leading-[1.1]">
                    <?= nl2br(htmlspecialchars($mk_sh['challenges']['heading'] ?? "Why Many Brands\nRemain Invisible.")) ?>
                </h3>
                <p class="text-xl text-slate-400 leading-relaxed">
                    <?= htmlspecialchars($mk_sh['challenges']['sub_text'] ?? 'Without the right strategy, products get lost among thousands of competitors. Poor management causes brands to lose both sales and profitability.') ?>
                </p>
            </div>

            <div class="grid grid-flow-col auto-cols-[82%] sm:auto-cols-[60%] md:grid-flow-row md:auto-cols-auto md:grid-cols-2 lg:grid-cols-3 gap-6 spotlight-group mobile-scroll-x mobile-snap pb-4 md:pb-0 touch-pan-x md:overflow-visible md:touch-auto"
                id="spotlight-grid">
                <?php
                foreach ($challenges as $idx => $ch) {
                    echo '<div class="spotlight-card relative rounded-3xl overflow-hidden bg-white/5 border border-white/10 p-8 group min-w-0">';
                    echo '<div class="spotlight-overlay absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>';
                    echo '<div class="relative z-10">';
                    echo '<div class="w-14 h-14 bg-[var(--electric-blue)]/10 rounded-xl flex items-center justify-center mb-6 border border-[var(--electric-blue)]/20 group-hover:scale-110 transition-transform duration-300">';
                    echo '<span class="material-symbols-outlined text-[var(--electric-blue)] text-2xl">' . htmlspecialchars($ch['icon']) . '</span>';
                    echo '</div>';
                    echo '<h4 class="text-2xl font-bold mb-3 text-white group-hover:text-[var(--electric-blue)] transition-colors">' . htmlspecialchars($ch['title']) . '</h4>';
                    echo '<p class="text-slate-400 leading-relaxed text-sm">' . htmlspecialchars($ch['description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- 3. Our Approach (Interactive Vertical Accordion) -->
    <section class="py-20 md:py-32 bg-[#020617] relative z-30">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 md:mb-24 max-w-3xl mx-auto">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($mk_sh['approach']['eyebrow'] ?? 'Strategy') ?></h2>
                <h3 class="text-4xl md:text-6xl font-black tracking-tighter mb-6"><?= htmlspecialchars($mk_sh['approach']['heading'] ?? 'Our Approach to Growth') ?></h3>
                <p class="text-xl text-slate-400"><?= htmlspecialchars($mk_sh['approach']['sub_text'] ?? 'At Digifyce, we do not just manage marketplace accounts — we build marketplace growth systems focusing on three critical areas.') ?></p>
            </div>

            <div
                class="flex flex-nowrap md:flex-row gap-6 max-w-6xl mx-auto h-auto md:h-[500px] mobile-scroll-x mobile-snap pb-4 md:pb-0 touch-pan-x mobile-card-rail md:overflow-visible md:touch-auto">

                <?php foreach ($mk_approach as $idx => $ap): ?>
                <div class="approach-accordion flex-[0_0_82%] sm:flex-[0_0_60%] md:flex-1 border border-white/10 bg-white/5 rounded-3xl p-8 flex flex-col justify-between <?= $idx === 0 ? 'active' : '' ?> group cursor-pointer select-none"
                    role="button" tabindex="0" aria-label="<?= htmlspecialchars($ap['title']) ?> strategy card">
                    <div>
                        <div
                            class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mb-8 app-icon transition-colors">
                            <span class="text-2xl font-black"><?= htmlspecialchars($ap['number_label']) ?></span>
                        </div>
                        <h4 class="text-3xl font-bold mb-2"><?= htmlspecialchars($ap['title']) ?></h4>
                        <div class="app-content">
                            <p class="text-slate-300 text-lg leading-relaxed"><?= htmlspecialchars($ap['description']) ?></p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-white/20 text-5xl self-end group-[.active]:text-[var(--electric-blue)] transition-colors"><?= htmlspecialchars($ap['icon']) ?></span>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- 4. Services (Sticky Sidebar Layout) -->
    <section class="py-20 md:py-32 bg-[#05070a] relative z-40 border-y border-white/5">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 md:mb-24">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6"><?= htmlspecialchars($mk_sh['services']['eyebrow'] ?? 'Complete Solutions') ?></h2>
                <h3 class="text-4xl md:text-6xl font-black tracking-tighter max-w-3xl"><?= htmlspecialchars($mk_sh['services']['heading'] ?? 'End-to-End Marketplace Management Services') ?></h3>
            </div>

            <div class="grid lg:grid-cols-[1fr_2fr] gap-12 lg:gap-24 items-start">

                <!-- Sticky Left Sidebar -->
                <div class="lg:sticky lg:top-32 hidden lg:block">
                    <div
                        class="p-8 border border-[var(--electric-blue)]/30 bg-[var(--electric-blue)]/5 rounded-3xl backdrop-blur-sm">
                        <h4 class="text-2xl font-bold text-white mb-4"><?= htmlspecialchars($mk_sh['services_sidebar']['eyebrow'] ?? 'Your Digital Salesperson') ?></h4>
                        <p class="text-slate-400 mb-8"><?= htmlspecialchars($mk_sh['services_sidebar']['sub_text'] ?? 'Every solution is designed to improve marketplace visibility, customer trust, and business profitability.') ?></p>
                        <ul class="space-y-4 text-sm font-bold tracking-widest uppercase text-slate-500">
                            <?php foreach ($mk_svc_blocks as $_si => $_sb): ?>
                            <li class="sidebar-link <?= $_si === 0 ? 'text-[var(--electric-blue)]' : '' ?> transition-colors duration-300"><?= str_pad($_si + 1, 2, '0', STR_PAD_LEFT) ?>. <?= htmlspecialchars($_sb['title']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Scrolling Right Content -->
                <div class="service-sticky-container relative pl-8 md:pl-12 space-y-24">

                    <?php foreach ($mk_svc_blocks as $_sb):
                        $_cards = $mk_svc_cards[$_sb['id']] ?? [];
                    ?>
                    <div class="service-block relative">
                        <div class="service-dot"></div>
                        <h3 class="text-3xl md:text-4xl font-black mb-6 text-white"><?= htmlspecialchars($_sb['title']) ?></h3>
                        <p class="text-xl text-slate-400 mb-10"><?= htmlspecialchars($_sb['description']) ?></p>

                        <div class="grid grid-flow-col auto-cols-[82%] sm:auto-cols-[60%] md:grid-flow-row md:auto-cols-auto md:grid-cols-2 gap-8 mobile-scroll-x mobile-snap pb-4 md:pb-0 touch-pan-x md:overflow-visible md:touch-auto">
                            <?php foreach ($_cards as $_card):
                                $_bullets = json_decode($_card['bullets_json'] ?? '[]', true) ?: [];
                                $_wide = !empty($_card['is_wide']);
                            ?>
                            <div class="bg-white/5 p-8 rounded-2xl border border-white/5 hover:border-[var(--electric-blue)]/50 transition-colors min-w-0<?= $_wide ? ' md:col-span-2 flex flex-col md:flex-row gap-6 items-center' : '' ?>">
                                <span class="material-symbols-outlined text-[var(--electric-blue)]<?= $_wide ? ' text-5xl' : ' text-3xl mb-4' ?>"><?= htmlspecialchars($_card['icon']) ?></span>
                                <?php if ($_wide): ?>
                                <div>
                                    <h4 class="text-xl font-bold mb-2"><?= htmlspecialchars($_card['title']) ?></h4>
                                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($_card['description']) ?></p>
                                </div>
                                <?php else: ?>
                                <h4 class="text-xl font-bold mb-3"><?= htmlspecialchars($_card['title']) ?></h4>
                                <ul class="space-y-2 text-slate-400 text-sm">
                                    <?php foreach ($_bullets as $_b): ?>
                                    <li>• <?= htmlspecialchars($_b) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </section>

    <!-- 5. Process (Horizontal Scroll Methodology) -->
    <section class="bg-[#020617] overflow-hidden relative z-50" id="process-section">
        <div class="h-scroll-container">
            <?php
            foreach ($steps as $idx => $step) {
                echo '<div class="h-step">';
                echo '<div class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none select-none">';
                echo '<span class="text-[16vw] font-black text-white/[0.02] leading-none">' . htmlspecialchars($step['step_number']) . '</span>';
                echo '</div>';
                echo '<div class="h-step-content relative z-10">';
                echo '<div class="max-w-[1440px] mx-auto w-full grid lg:grid-cols-2 gap-12 lg:gap-24 items-center">';
                echo '<div class="flex justify-center lg:justify-end">';
                echo '<div class="w-40 h-40 bg-white/5 rounded-2xl border border-white/10 flex items-center justify-center relative group">';
                echo '<div class="absolute inset-0 bg-[var(--electric-blue)]/10 rounded-2xl blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>';
                echo '<span class="material-symbols-outlined text-4xl md:text-5xl text-[var(--electric-blue)] relative z-10 transition-transform duration-700 group-hover:scale-110">' . htmlspecialchars($step['icon']) . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="text-center lg:text-left">';
                echo '<h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6">Step ' . htmlspecialchars($step['step_number']) . '</h2>';
                echo '<h3 class="text-3xl md:text-5xl font-black tracking-tighter mb-8">' . htmlspecialchars($step['title']) . '</h3>';
                echo '<p class="text-lg md:text-xl text-slate-400 leading-relaxed max-w-xl mx-auto lg:mx-0">' . htmlspecialchars($step['description']) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Progress Overlay -->
        <div class="absolute bottom-20 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 z-[60]">
            <div class="flex gap-4 items-center">
                <span class="text-[var(--electric-blue)] font-bold tracking-widest text-sm" id="p-current">01</span>
                <div class="w-48 md:w-80 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--electric-blue)] w-0" id="p-bar"></div>
                </div>
                <span class="text-slate-500 font-bold tracking-widest text-sm">06</span>
            </div>
            <div class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Scroll to explore</div>
        </div>
    </section>

    <!-- 6. Impact & Why Choose Us -->
    <section class="py-20 md:py-32 bg-[#05070a] border-t border-white/5 relative z-50 overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-4"><?= htmlspecialchars($mk_sh['impacts']['eyebrow'] ?? 'The Results') ?></h2>
                    <h3 class="text-4xl md:text-5xl font-black tracking-tighter mb-6"><?= htmlspecialchars($mk_sh['impacts']['heading'] ?? 'Business Impact of Strong Management') ?></h3>
                    <p class="text-slate-400 text-lg"><?= htmlspecialchars($mk_sh['impacts']['sub_text'] ?? 'Marketplace management is not just account handling, it is revenue optimization.') ?></p>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-12 mobile-scroll-grid">
                    <?php foreach ($mk_impacts as $imp): ?>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/5">
                        <span class="material-symbols-outlined text-[var(--electric-blue)] text-3xl mb-3"><?= htmlspecialchars($imp['icon']) ?></span>
                        <h4 class="font-bold text-lg mb-2"><?= htmlspecialchars($imp['title']) ?></h4>
                        <p class="text-sm text-slate-400"><?= htmlspecialchars($imp['description']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
        </div>

        <div id="cta-final" class="relative overflow-hidden w-full mt-12 md:mt-20">
            <div class="absolute inset-0 bg-[#020617] opacity-95"></div>
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-[var(--electric-blue)] opacity-10 blur-[100px] rounded-full">
            </div>
            <div
                class="cta-bg-text absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-[15rem] md:text-[25rem] font-black text-white/[0.03] select-none pointer-events-none uppercase">
                <?= htmlspecialchars($mk_cta['bg_text'] ?? 'Scale') ?></div>

            <div class="relative z-10 p-8 md:p-24 text-center w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-4xl md:text-6xl font-black tracking-tighter mb-6"><?= htmlspecialchars($mk_cta['heading'] ?? 'Why Choose Digifyce?') ?></h3>
                <p class="text-xl text-slate-400 mb-12 max-w-3xl mx-auto"><?= htmlspecialchars($mk_cta['description'] ?? '') ?></p>

                <ul class="text-left space-y-4 mb-12 mx-auto max-w-[36rem] text-slate-200">
                    <?php foreach ($mk_why as $wb): ?>
                    <li class="flex items-center gap-4 text-lg">
                        <span class="material-symbols-outlined text-[var(--electric-blue)] text-2xl">check_circle</span>
                        <?= htmlspecialchars($wb['text']) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="flex flex-col sm:flex-row gap-6 justify-center max-w-[45rem] mx-auto">
                    <a href="<?= htmlspecialchars($mk_cta['btn1_url'] ?? 'leadform.php') ?>" class="btn-white w-full sm:w-auto text-center px-10 py-4 text-lg">
                        <?= htmlspecialchars($mk_cta['btn1_label'] ?? 'Scale Your Business Now') ?>
                        <span class="material-symbols-outlined">rocket_launch</span>
                    </a>
                    <a href="<?= htmlspecialchars($mk_cta['btn2_url'] ?? 'contact.php') ?>" class="btn-outline w-full sm:w-auto text-center px-10 py-4 text-lg">
                        <?= htmlspecialchars($mk_cta['btn2_label'] ?? 'Talk to an Expert') ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    window.addEventListener("load", (event) => {
        setTimeout(() => {
            gsap.registerPlugin(ScrollTrigger);

            // 1. Hero Animation
            const tlHero = gsap.timeline({ defaults: { ease: "power4.out" } });
            tlHero.from(".hero-badge", { y: 30, opacity: 0, duration: 0.8 })
                .from(".hero-line", { y: 60, opacity: 0, duration: 1, stagger: 0.2 }, "-=0.4")
                .from(".hero-sub", { y: 20, opacity: 0, duration: 0.8 }, "-=0.6")
                .from(".hero-btn", { scale: 0.9, opacity: 0, duration: 0.6 }, "-=0.4")
                .from(".hero-glow", { scale: 0, opacity: 0, duration: 2 }, "-=1.5");

            // 2. Spotlight Grid Mouse Tracking
            const grid = document.getElementById("spotlight-grid");
            if (grid) {
                grid.addEventListener("mousemove", (e) => {
                    for (const card of document.querySelectorAll(".spotlight-card")) {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        card.style.setProperty("--mouse-x", `${x}px`);
                        card.style.setProperty("--mouse-y", `${y}px`);
                    }
                });
            }

            // 3. Accordion Interaction
            const accordions = document.querySelectorAll('.approach-accordion');
            const isTouchDevice = window.matchMedia('(hover: none), (pointer: coarse)').matches;

            function setActiveAccordion(activeAccordion) {
                accordions.forEach(a => a.classList.remove('active'));
                activeAccordion.classList.add('active');
            }

            accordions.forEach(acc => {
                if (!isTouchDevice) {
                    acc.addEventListener('mouseenter', () => setActiveAccordion(acc));
                }
                acc.addEventListener('click', () => setActiveAccordion(acc));
                acc.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        setActiveAccordion(acc);
                    }
                });
            });

            if (isTouchDevice && accordions.length > 0) {
                setActiveAccordion(accordions[0]);
            }

            // 4. Horizontal Scroll Process
            const sections = gsap.utils.toArray(".h-step");
            const container = document.querySelector(".h-scroll-container");

            if (sections.length > 0) {
                gsap.to(sections, {
                    xPercent: -100 * (sections.length - 1),
                    ease: "none",
                    scrollTrigger: {
                        trigger: "#process-section",
                        pin: true,
                        scrub: 1,
                        snap: 1 / (sections.length - 1),
                        // base the end on the actual container width
                        end: () => "+=" + container.offsetWidth
                    }
                });

                // Progress Bar and Counter
                const pBar = document.getElementById("p-bar");
                const pCurrent = document.getElementById("p-current");

                ScrollTrigger.create({
                    trigger: "#process-section",
                    start: "top top",
                    end: () => "+=" + container.offsetWidth,
                    scrub: 0.3,
                    onUpdate: (self) => {
                        const progress = self.progress;
                        gsap.set(pBar, { width: (progress * 100) + "%" });

                        const stepNum = Math.min(Math.floor(progress * 6) + 1, 6);
                        pCurrent.innerText = "0" + stepNum;
                    }
                });
            }

            // 5. Sidebar Syncing
            const serviceBlocks = gsap.utils.toArray('.service-block');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            if (sidebarLinks.length > 0) {
                serviceBlocks.forEach((block, i) => {
                    ScrollTrigger.create({
                        trigger: block,
                        start: "top 60%",
                        end: "bottom 40%",
                        onEnter: () => activateSidebarLink(i),
                        onEnterBack: () => activateSidebarLink(i),
                    });
                });

                function activateSidebarLink(index) {
                    sidebarLinks.forEach((link, i) => {
                        if (i === index) {
                            link.classList.add('text-[var(--electric-blue)]');
                        } else {
                            link.classList.remove('text-[var(--electric-blue)]');
                        }
                    });
                }
            }

            // 6. Spotlight overlay support for touch devices
            if (grid) {
                const updateSpotlight = (event) => {
                    const point = event.touches && event.touches[0] ? event.touches[0] : event;
                    document.querySelectorAll('.spotlight-card').forEach((card) => {
                        const rect = card.getBoundingClientRect();
                        const x = point.clientX - rect.left;
                        const y = point.clientY - rect.top;
                        card.style.setProperty('--mouse-x', `${x}px`);
                        card.style.setProperty('--mouse-y', `${y}px`);
                    });
                };

                grid.addEventListener('mousemove', updateSpotlight);
                grid.addEventListener('pointermove', updateSpotlight);
                grid.addEventListener('touchmove', updateSpotlight, { passive: true });
            }

        }, 500); // Wait for preloader
    });
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>