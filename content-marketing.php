<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'content-marketing');
$pageTitle = $_seo['meta_title'] ?: 'Content Marketing Services in India - Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'Strategic content marketing services including blog writing, website content, social media content, and SEO-focused writing that build trust and drive growth.';
$bodyClass = 'content-marketing';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo          = Database::getInstance();
$solutions     = $_pdo->query("SELECT * FROM content_solutions WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_hero       = $_pdo->query("SELECT * FROM content_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ct_stats      = $_pdo->query("SELECT * FROM content_hero_stats WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_challenges = $_pdo->query("SELECT * FROM content_challenges WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_pillars    = $_pdo->query("SELECT * FROM content_pillars WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_metrics    = $_pdo->query("SELECT * FROM content_metrics WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_why        = $_pdo->query("SELECT * FROM content_why_points WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$ct_cta        = $_pdo->query("SELECT * FROM content_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$ct_main_stats = array_values(array_filter($ct_stats, fn($s) => empty($s['description'])));
$ct_chip_stats = array_values(array_filter($ct_stats, fn($s) => !empty($s['description'])));
$ct_sh         = [];
foreach ($_pdo->query("SELECT * FROM content_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $_r) { $ct_sh[$_r['slug']] = $_r; }
$ct_signal_pts = $_pdo->query("SELECT * FROM content_signal_points WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/app/views/header.php';
?>

<style>
    /* CSS Variables & Core Setup */
    :root {
        --clr-bg: #020617;
        --clr-surface: rgba(30, 41, 59, 0.4);
        --clr-border: rgba(148, 163, 184, 0.1);
        --clr-primary: #0066ff;
        --clr-accent: #0066ff;
        --clr-grey-100: #e5e7eb;
        --clr-grey-300: #cbd5e1;
        --clr-grey-500: #94a3b8;
    }

    body {
        background-color: var(--clr-bg);
        -webkit-font-smoothing: antialiased;
        overflow-x: hidden;
    }

    /* Glows & Gradients */
    .glow-bg {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        z-index: -1;
        animation: floatGlow 10s ease-in-out infinite alternate;
    }

    @keyframes floatGlow {
        0% {
            transform: translate(0, 0) scale(1);
        }

        100% {
            transform: translate(30px, -30px) scale(1.1);
        }
    }

    .text-gradient {
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 30%, #0066ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Glassmorphism */
    .glass {
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* Interactive Components */
    .hover-lift {
        transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
    }

    .hover-lift:hover {
        transform: translateY(-6px);
        border-color: rgba(0, 102, 255, 0.4);
        box-shadow: 0 20px 45px rgba(0, 102, 255, 0.22);
    }

    .soft-glow {
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.35);
    }

    /* Unified brand palette */
    .content-marketing [class~="text-teal-200"],
    .content-marketing [class~="text-teal-300"],
    .content-marketing [class~="text-blue-200"],
    .content-marketing [class~="text-blue-300"],
    .content-marketing [class~="text-blue-400"] {
        color: var(--clr-primary) !important;
    }

    .content-marketing [class~="bg-blue-600"],
    .content-marketing [class~="bg-teal-500"] {
        background-color: var(--clr-primary) !important;
    }

    .content-marketing [class~="bg-blue-500/10"],
    .content-marketing [class~="bg-blue-700/20"],
    .content-marketing [class~="bg-blue-600/30"],
    .content-marketing [class~="bg-teal-500/20"],
    .content-marketing [class~="bg-teal-400/70"],
    .content-marketing [class~="bg-teal-300"] {
        background-color: rgba(0, 102, 255, 0.16) !important;
    }

    .content-marketing [class~="border-teal-400/30"],
    .content-marketing [class~="border-teal-500/20"],
    .content-marketing [class~="border-blue-500/20"] {
        border-color: rgba(0, 102, 255, 0.32) !important;
    }

    .content-marketing [class~="text-slate-500/30"] {
        color: rgba(203, 213, 225, 0.35) !important;
    }

    .content-marketing [class~="text-slate-400"] {
        color: var(--clr-grey-500) !important;
    }

    .content-marketing [class~="text-slate-300"] {
        color: var(--clr-grey-300) !important;
    }

    .content-marketing [class~="text-slate-200"] {
        color: var(--clr-grey-100) !important;
    }

    .content-marketing [class~="bg-gradient-to-r"] {
        background-image: linear-gradient(90deg, #ffffff 0%, #d1d5db 35%, #0066ff 100%) !important;
    }

    /* Interaction upgrades */
    .glass {
        transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease;
    }

    .glass:hover {
        border-color: rgba(0, 102, 255, 0.35);
        box-shadow: 0 20px 40px rgba(0, 102, 255, 0.12);
    }

    .tab-btn {
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow .3s ease;
    }

    .tab-btn:hover {
        border-color: rgba(0, 102, 255, 0.35);
        background: rgba(255, 255, 255, 0.04);
    }

    .tab-btn.active {
        border-left-color: var(--clr-primary);
        border-color: rgba(0, 102, 255, 0.4);
        box-shadow: 0 10px 28px rgba(0, 102, 255, 0.16);
    }

    .cta-primary,
    .cta-secondary {
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, background-color .25s ease;
    }

    .cta-primary:hover,
    .cta-secondary:hover {
        transform: translateY(-2px);
    }

    .cta-primary {
        background: var(--clr-primary) !important;
        box-shadow: 0 0 28px rgba(0, 102, 255, 0.35) !important;
    }

    .cta-primary:hover {
        background: #005ae0 !important;
        box-shadow: 0 0 36px rgba(0, 102, 255, 0.45) !important;
    }

    .cta-secondary:hover {
        border-color: rgba(0, 102, 255, 0.45) !important;
        color: #ffffff !important;
    }

    /* Button styles (copied from d2c-branding for consistent CTA look) */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 32px;
        border-radius: 4px;
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: .03em;
        text-decoration: none;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
    }

    .btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, .06);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .3s ease;
    }

    .btn:hover::after {
        transform: scaleX(1);
    }

    .btn-solid {
        background: var(--clr-primary);
        color: #ffffff;
        box-shadow: 0 0 0 0 rgba(0, 102, 255, 0.18);
    }

    .btn-solid:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0, 102, 255, 0.22);
    }

    .btn-outline {
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    .btn-outline:hover {
        border-color: var(--clr-primary);
        color: var(--clr-primary);
    }

    @media (min-width: 1024px) {

        .btn svg,
        .btn .material-symbols-outlined,
        .cta-primary svg,
        .cta-primary .material-symbols-outlined {
            display: inline-block;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }

        .btn:hover svg,
        .btn:hover .material-symbols-outlined,
        .cta-primary:hover svg,
        .cta-primary:hover .material-symbols-outlined {
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

    /* Fallback/Mobile: Keep icons visible and use a simple shift */
    @media (max-width: 1023px) {

        .btn svg,
        .btn .material-symbols-outlined,
        .cta-primary svg,
        .cta-primary .material-symbols-outlined {
            opacity: 1;
            transform: none;
        }

        .btn:hover svg,
        .btn:hover .material-symbols-outlined,
        .cta-primary:hover svg,
        .cta-primary:hover .material-symbols-outlined {
            transform: translateX(4px);
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

        .mobile-scroll-grid>* {
            flex: 0 0 85vw !important;
            scroll-snap-align: start;
            margin-right: 1rem;
            min-width: 260px;
        }
    }

    .counter {
        text-shadow: 0 0 20px rgba(0, 102, 255, 0.25);
    }

    /* Scroll Reveals */
    .reveal {
        opacity: 0;
        transform: translateY(40px) scale(0.98);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .reveal-scale {
        opacity: 0;
        transform: scale(0.9);
        transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal-scale.active {
        opacity: 1;
        transform: scale(1);
    }

    /* Marquee */
    .marquee {
        background: var(--clr-primary);
        padding: 1rem 0;
        overflow: hidden;
        white-space: nowrap;
    }

    .marquee-track {
        display: inline-block;
        animation: scroll-marquee 22s linear infinite;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .9);
    }

    @keyframes scroll-marquee {
        to {
            transform: translateX(-50%);
        }
    }

    .marquee .mq-sep {
        color: rgba(255, 255, 255, .35);
        margin: 0 1.5rem;
    }

    /* Custom Mobile Scroller */
    .snap-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(1, 1fr);
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .snap-grid {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 2rem;
            scrollbar-width: none;
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }

        .snap-grid::-webkit-scrollbar {
            display: none;
        }

        .snap-grid>* {
            flex: 0 0 85%;
            scroll-snap-align: center;
        }
    }

    @media (min-width: 768px) {
        .snap-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .snap-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Service Tabs */
    .tab-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 2px solid transparent;
    }

    .tab-btn.active {
        background: rgba(255, 255, 255, 0.05);
        border-left-color: #14b8a6;
        transform: translateX(8px);
    }

    .tab-content-panel {
        position: absolute;
        inset: 0;
        opacity: 0;
        pointer-events: none;
        transform: translateY(20px) scale(0.95);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tab-content-panel.active {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scale(1);
        position: relative;
    }

    .hide-scrollbar {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    @media (max-width: 1023px) {
        .pillars-tabs {
            gap: 0.75rem;
            padding-bottom: 0.75rem;
            margin-right: -0.25rem;
            padding-right: 0.25rem;
        }

        .pillars-tabs .tab-btn {
            min-width: 84%;
            padding: 1rem 1.1rem;
            border-radius: 1rem;
        }

        .pillars-tabs .tab-btn.active {
            transform: none;
        }

        .pillars-content {
            min-height: 0;
            margin-top: 0.4rem;
        }

        .pillars-content .tab-content-panel {
            position: relative;
            inset: auto;
            display: none;
            opacity: 1;
            pointer-events: auto;
            transform: none;
        }

        .pillars-content .tab-content-panel.active {
            display: block;
        }
    }

    @media (max-width: 640px) {
        .pillars-content .tab-content-panel {
            padding: 1.25rem;
            border-radius: 1rem;
        }

        .pillars-content .tab-content-panel h3 {
            font-size: 1.5rem;
            line-height: 1.3;
        }

        .pillars-content .tab-content-panel p {
            font-size: 1rem;
            line-height: 1.7;
        }

        .pillars-content .tab-content-panel ul {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
    }

    /* ── Final CTA (d2c-branding style) ── */
    #cta-final {
        background: #0066ff;
        padding: 8rem 0;
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        #cta-final {
            padding: 5rem 0;
        }
    }

    @media (max-width: 768px) {
        #hero-section {
            padding-top: 3.5rem;
            padding-bottom: 1.75rem;
        }

        #hero-section .hero-content {
            padding-bottom: 0.25rem;
        }

        #hero-section .hero-sub {
            margin-bottom: 1.1rem;
        }

        #hero-section .hero-glow {
            opacity: 0.08 !important;
            filter: blur(120px);
        }

        .marquee {
            padding: 0.7rem 0;
        }

        .marquee-track {
            font-size: .68rem;
        }

        section.py-24.lg\:py-32,
        section.py-20.md\:py-32,
        section.py-24.lg\:py-32.bg-white\/[0\.01],
        section.py-20.md\:py-32.bg-\[\#020617\] {
            padding-top: 2.2rem !important;
            padding-bottom: 2.2rem !important;
        }

        section.py-20.md\:py-32+section.py-24.lg\:py-32,
        section.py-24.lg\:py-32+section.py-24.lg\:py-32 {
            padding-top: 2rem !important;
        }

        .snap-grid {
            margin-top: 1.2rem;
            gap: 1rem;
        }

        .pillars-tabs {
            margin-bottom: 0.25rem;
        }

        .pillars-content {
            margin-top: 0.15rem;
        }

        .pillars-content .tab-content-panel {
            margin-top: 0;
        }

        .stack-card {
            margin-bottom: 1rem !important;
        }

        .stack-card.p-10,
        .stack-card.md\:p-16 {
            padding-top: 1.4rem !important;
            padding-bottom: 1.4rem !important;
        }

        .stack-card h4 {
            font-size: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .stack-card p {
            font-size: 1rem !important;
            line-height: 1.7 !important;
        }

        .glass.rounded-3xl.p-6.md\:p-8,
        .glass.rounded-2xl.p-5,
        .glass.rounded-2xl.p-4 {
            padding: 1rem !important;
        }

        #cta-final {
            padding: 3.5rem 0;
        }

        .cta-inner {
            padding: 0 1rem;
        }

        .cta-inner h2 {
            margin-bottom: 1rem;
        }

        .cta-inner p {
            margin-bottom: 1.5rem;
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
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 4rem;
    }

    @media (max-width: 1024px) {
        .cta-inner {
            padding: 0 2rem;
        }
    }

    @media (max-width: 640px) {
        .cta-inner {
            padding: 0 1rem;
        }
    }

    .cta-inner h2 {
        font-size: clamp(2.5rem, 5vw, 5rem);
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -.04em;
        color: #ffffff;
        margin-bottom: 1.5rem;
    }

    .cta-inner p {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, .75);
        max-width: 520px;
        margin: 0 auto 3rem;
        line-height: 1.7;
    }

    .btn-white {
        background: #ffffff;
        color: #0066ff;
        font-weight: 800;
        box-shadow: 0 8px 40px rgba(0, 0, 0, .2);
    }

    .btn-white:hover {
        background: #f0f2f5;
        transform: translateY(-3px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, .3);
    }

    .btn-white::after {
        background: rgba(0, 102, 255, .06);
    }

    /* ── Stacking Cards (e-com style) ── */
    .stack-card {
        position: sticky;
        top: 120px;
        box-shadow: 0 -20px 40px rgba(0, 0, 0, 0.5);
    }
</style>

<section class="relative pt-32 pb-20 lg:pt-44 lg:pb-28 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
    <!-- Ambient Glows -->
    <div class="glow-bg bg-[#0066ff7a] top-[10%] left-[8%] w-[320px] h-[320px]" data-parallax="-0.12"></div>
    <div class="glow-bg bg-[#0066ff7a] top-[18%] right-[6%] w-[420px] h-[420px] animation-delay-2000"
        data-parallax="-0.06"></div>
    <div class="glow-bg bg-blue-700/20 top-[65%] left-[35%] w-[520px] h-[520px] animation-delay-4000"
        data-parallax="-0.08"></div>

    <div class="grid lg:grid-cols-12 gap-12 items-center relative z-10">
        <div class="lg:col-span-7 reveal-scale animate-on-load">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border border-teal-400/30 text-teal-200 text-xs font-bold tracking-widest uppercase mb-6">
                <span class="relative flex h-2 w-2"><span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-300 opacity-70"></span><span
                        class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span></span>
                <?= htmlspecialchars($ct_hero['kicker'] ?? 'Strategic Content That Builds Trust & Visibility') ?>
            </div>
            <h1 class="text-[3rem] md:text-[3.5rem] font-extrabold tracking-tight mb-6 leading-tight text-white">
                <?= htmlspecialchars($ct_hero['h1_line1'] ?? 'Content Marketing') ?> <br class="hidden md:block" />
                <span class="text-gradient"><?= htmlspecialchars($ct_hero['h1_line2_gradient'] ?? 'Services in India') ?></span>
            </h1>
            <p class="text-lg md:text-2xl text-slate-400 mb-8 leading-relaxed font-light">
                <?= htmlspecialchars($ct_hero['hero_sub'] ?? 'We deliver semantic, SEO-focused, and engaging digital content that turns casual visitors into converted loyalists.') ?>
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="<?= htmlspecialchars($ct_hero['btn1_url'] ?? 'leadform.php') ?>"
                    class="cta-primary inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-bold text-lg transition-colors shadow-[0_0_30px_rgba(59,130,246,0.25)]">
                    <?= htmlspecialchars($ct_hero['btn1_label'] ?? "Let's Create Together") ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                <a href="<?= htmlspecialchars($ct_hero['btn2_url'] ?? 'blog_list.php') ?>"
                    class="cta-secondary inline-flex items-center gap-3 px-8 py-4 rounded-full border border-white/10 text-slate-200 hover:border-teal-400/40 hover:text-white transition-colors">
                    <?= htmlspecialchars($ct_hero['btn2_label'] ?? 'Explore Insights') ?>
                </a>
            </div>
            <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <?php foreach ($ct_main_stats as $ms): ?>
                <div class="glass rounded-2xl p-4 hover-lift">
                    <div class="text-xs uppercase tracking-widest text-teal-300"><?= htmlspecialchars($ms['label']) ?></div>
                    <div class="text-2xl font-bold text-white"><?= htmlspecialchars($ms['value']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="lg:col-span-5 space-y-6">
            <div class="glass rounded-3xl p-6 soft-glow hover-lift reveal">
                <div class="flex items-center justify-between mb-6">
                    <div class="text-xs uppercase tracking-widest text-teal-300"><?= htmlspecialchars($ct_sh['hero_signal']['eyebrow'] ?? 'Content Signal') ?></div>
                </div>
                <?php
                $_dot_colors = ['bg-teal-400', 'bg-blue-400', 'bg-blue-300'];
                $_sig_pts = !empty($ct_signal_pts) ? $ct_signal_pts : [
                    ['title'=>'Intent Mapping','description'=>'Match every page to the buying stage that drives action.'],
                    ['title'=>'Editorial Engine','description'=>'Consistent publishing that compounds authority.'],
                    ['title'=>'Conversion Copy','description'=>'UX-first storytelling designed for qualified leads.'],
                ];
                ?>
                <ul class="space-y-4">
                    <?php foreach ($_sig_pts as $_si => $_sp): ?>
                    <li class="flex items-start gap-3">
                        <span class="mt-2 h-2 w-2 rounded-full <?= $_dot_colors[$_si % 3] ?>"></span>
                        <div>
                            <div class="text-white font-semibold"><?= htmlspecialchars($_sp['title']) ?></div>
                            <p class="text-slate-400 text-sm"><?= htmlspecialchars($_sp['description']) ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php foreach ($ct_chip_stats as $cs): ?>
                <div class="glass rounded-2xl p-5 hover-lift reveal">
                    <div class="text-xs uppercase tracking-widest text-teal-300"><?= htmlspecialchars($cs['label']) ?></div>
                    <div class="text-2xl font-bold text-white"><?= htmlspecialchars($cs['value']) ?></div>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($cs['description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<div class="marquee reveal text-white">
    <div class="marquee-track uppercase whitespace-nowrap">
        BLOG WRITING <span class="mq-sep">◆</span> WEBSITE COPY <span class="mq-sep">◆</span> SOCIAL MEDIA <span
            class="mq-sep">◆</span> SEO CONTENT <span class="mq-sep">◆</span> BRAND MESSAGING <span
            class="mq-sep">◆</span> EMAIL MARKETING <span class="mq-sep">◆</span> BLOG WRITING <span
            class="mq-sep">◆</span> WEBSITE COPY <span class="mq-sep">◆</span> SOCIAL MEDIA <span
            class="mq-sep">◆</span> SEO CONTENT <span class="mq-sep">◆</span> BRAND MESSAGING <span
            class="mq-sep">◆</span> EMAIL MARKETING <span class="mq-sep">◆</span>&nbsp;
    </div>
</div>

<section class="py-24 lg:py-32 relative text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-start">
            <div class="lg:col-span-6 reveal">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass border border-teal-400/30 text-teal-200 text-xs font-bold tracking-widest uppercase mb-6">
                    <?= htmlspecialchars($ct_sh['challenges']['eyebrow'] ?? 'The Challenge') ?>
                </div>
                <h2 class="text-4xl md:text-6xl font-bold mb-6 leading-tight"><?= htmlspecialchars($ct_sh['challenges']['heading'] ?? 'Why Modern Brands Demand Strategic Content') ?></h2>
                <p class="text-slate-400 text-lg leading-relaxed mb-8"><?= htmlspecialchars($ct_sh['challenges']['sub_text'] ?? 'Paid campaigns fade, but high-quality content forms an eternal growth engine. Without a dedicated semantic strategy, you will continually struggle against algorithm updates.') ?></p>
                <?php
                $_ch_pts_default = [
                    ['dot'=>'bg-teal-400','text'=>'Algorithm volatility is rising'],
                    ['dot'=>'bg-blue-400','text'=>'Trust is earned through consistency'],
                    ['dot'=>'bg-blue-300','text'=>'Content is now a growth asset'],
                ];
                $_ch_extra = $ct_sh['challenges']['extra_text'] ?? '';
                $_ch_pts = $_ch_extra ? json_decode($_ch_extra, true) : null;
                if (!is_array($_ch_pts)) $_ch_pts = $_ch_pts_default;
                ?>
                <div class="space-y-3 text-slate-400 text-sm">
                    <?php foreach ($_ch_pts as $_pi => $_pt):
                        $_dot = is_array($_pt) ? ($_pt['dot'] ?? $_dot_colors[$_pi % 3]) : $_dot_colors[$_pi % 3];
                        $_txt = is_array($_pt) ? ($_pt['text'] ?? $_pt) : $_pt;
                    ?>
                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full <?= htmlspecialchars($_dot) ?>"></span><?= htmlspecialchars($_txt) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="lg:col-span-6 space-y-6 mobile-scroll-grid">
                <?php foreach ($ct_challenges as $i => $ch): ?>
                <div class="glass rounded-3xl p-8 hover-lift reveal-scale<?= $i > 0 ? ' delay-' . ($i * 100) : '' ?>">
                    <div class="flex items-start gap-5">
                        <div class="h-12 w-[100px] rounded-2xl bg-blue-500/10 text-blue-200 flex items-center justify-center font-bold">
                            <?= htmlspecialchars($ch['number_label']) ?></div>
                        <div>
                            <h3 class="text-2xl font-bold mb-3 text-white"><?= htmlspecialchars($ch['title']) ?></h3>
                            <p class="text-slate-400 text-lg"><?= htmlspecialchars($ch['description']) ?></p>
                        </div>
                    </div>
                    <div class="mt-6 h-1 rounded-full bg-blue-500/10">
                        <div class="h-1 rounded-full bg-teal-400/70 <?= htmlspecialchars($ch['progress_width']) ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32 bg-white/[0.01] border-y border-white/5 relative text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-end">
            <div class="lg:col-span-4 reveal">
                <h2 class="text-3xl md:text-5xl font-bold mb-6"><?= htmlspecialchars($ct_sh['pillars']['heading'] ?? 'Our 3-Pillar Approach') ?></h2>
                <p class="text-slate-400 mb-8"><?= htmlspecialchars($ct_sh['pillars']['sub_text'] ?? 'We treat content as a highly targeted business growth system focusing on search optimization and psychological triggers.') ?></p>

                <!-- Dynamic Interactive Tabs list -->
                <div class="pillars-tabs flex flex-row lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-4 snap-x hide-scrollbar"
                    style="scroll-snap-type: x mandatory;">
                    <?php foreach ($ct_pillars as $pi => $pl): ?>
                    <button class="tab-btn <?= $pi === 0 ? 'active' : '' ?> text-left px-6 py-5 rounded-2xl w-full min-w-[240px]"
                        style="scroll-snap-align: center;" data-target="panel-pillar-<?= $pi ?>">
                        <div class="text-teal-300 font-bold mb-1 uppercase tracking-widest text-xs">Pillar <?= htmlspecialchars($pl['number_label']) ?></div>
                        <div class="text-xl font-bold text-white"><?= htmlspecialchars($pl['name']) ?></div>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Dynamic Content Area -->
            <div class="pillars-content lg:col-span-8 relative min-h-[350px] reveal delay-100">
                <?php foreach ($ct_pillars as $pi => $pl):
                    $bullets = json_decode($pl['bullets_json'] ?? '[]', true) ?: [];
                ?>
                <div id="panel-pillar-<?= $pi ?>"
                    class="tab-content-panel <?= $pi === 0 ? 'active' : '' ?> glass rounded-3xl p-8 md:p-12 border border-blue-500/20">
                    <div class="w-16 h-16 bg-teal-500/20 rounded-2xl flex items-center justify-center mb-8 border border-teal-400/30">
                        <span class="text-teal-300 font-black text-2xl"><?= htmlspecialchars($pl['number_label']) ?></span>
                    </div>
                    <h3 class="text-3xl font-bold mb-4 text-white"><?= htmlspecialchars($pl['panel_title']) ?></h3>
                    <p class="text-slate-300 text-lg leading-relaxed mb-6"><?= htmlspecialchars($pl['panel_description']) ?></p>
                    <ul class="grid sm:grid-cols-2 gap-4 text-slate-400">
                        <?php foreach ($bullets as $b): ?>
                        <li class="flex items-center gap-2"><span class="text-teal-300 text-xl">•</span> <?= htmlspecialchars($b) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Solutions Grid -->
<section class=" md:py-16 bg-[#020617] relative z-30">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 md:mb-32 max-w-4xl mx-auto">
            <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--clr-primary)] mb-6"><?= htmlspecialchars($ct_sh['solutions']['eyebrow'] ?? 'Our Services') ?></h2>
            <h3 class="text-5xl md:text-7xl font-black tracking-tighter text-white"><?= htmlspecialchars($ct_sh['solutions']['heading'] ?? 'End-to-End Content Solutions') ?></h3>
            <p class="text-xl text-slate-400 mt-8"><?= htmlspecialchars($ct_sh['solutions']['sub_text'] ?? 'At Digifyce, we do not just write content, we build content systems engineered for search, trust, and conversion.') ?></p>
        </div>

        <div class="max-w-5xl mx-auto relative pb-16 md:pb-2">
            <?php
            foreach ($solutions as $idx => $sol) {
                $rotate = ($idx % 2 === 0) ? '-1deg' : '1deg';
                if ($idx === count($solutions) - 1)
                    $rotate = '0deg';
                $top = 120 + ($idx * 28);

                echo '<div class="stack-card p-10 md:p-16 rounded-[2rem] border border-white/10 mb-20 flex flex-col md:flex-row gap-10 items-center justify-between transition-all reveal" style="background-color: ' . htmlspecialchars($sol['bg_color']) . '; top: ' . $top . 'px; transform: rotate(' . $rotate . ');">';
                echo '<div class="md:w-1/2">';
                echo '<div class="font-black text-6xl md:text-8xl mb-6 opacity-50" style="color:var(--clr-primary)">' . htmlspecialchars($sol['number']) . '</div>';
                echo '<h4 class="text-4xl md:text-5xl font-bold text-white mb-6 tracking-tight">' . htmlspecialchars($sol['title']) . '</h4>';
                echo '</div>';
                echo '<div class="md:w-1/2">';
                echo '<p class="text-xl md:text-2xl text-slate-300 leading-relaxed">' . htmlspecialchars($sol['description']) . '</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Impact Metrics -->
<section class="pb-24 lg:py-16 relative overflow-hidden bg-white/[0.01] border-t border-white/5 text-white">
    <div class="glow-bg bg-blue-600/30 bottom-0 left-[20%] w-[500px] h-[300px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="reveal">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 text-white"><?= htmlspecialchars($ct_sh['metrics']['heading'] ?? 'Business Impact of Strong Content') ?></h2>
                <p class="text-slate-400 text-lg mb-8"><?= htmlspecialchars($ct_sh['metrics']['sub_text'] ?? 'Strategic content marketing creates massive business value. We do not just create content, we engineer structural growth through precise communication.') ?></p>
                <?php
                $_metrics_tags_default = ['Higher Traffic','Better Rankings','Lead Gen Acceleration','Reduced Ad Spends'];
                $_metrics_tags_json = $ct_sh['metrics']['extra_text'] ?? '';
                $_metrics_tags = ($_metrics_tags_json && ($_t = json_decode($_metrics_tags_json, true)) && is_array($_t)) ? $_t : $_metrics_tags_default;
                ?>
                <div class="flex flex-wrap gap-4">
                    <?php foreach ($_metrics_tags as $_tag): ?>
                    <span class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm font-medium"><?= htmlspecialchars($_tag) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Animated Counters -->
            <div class="grid grid-cols-2 gap-4 md:gap-6">
                <?php foreach ($ct_metrics as $mi => $mt): ?>
                <div class="glass rounded-3xl p-6 md:p-8 text-center reveal hover-lift<?= $mi % 2 === 1 ? ' delay-100' : '' ?>">
                    <div>
                        <div class="counter text-gradient text-4xl md:text-5xl font-black mb-2" data-target="<?= (int)$mt['target_num'] ?>">0</div>
                        <div class="text-slate-400 font-bold uppercase tracking-widest text-[10px] md:text-xs"><?= htmlspecialchars($mt['label']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Digifyce -->
<section class="py-20 md:py-32 bg-[#05070a] relative z-50 border-t border-white/5 text-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-24">

            <!-- Left: Sticky Intro -->
            <div class="lg:w-1/3 lg:sticky lg:top-32 h-fit">
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--clr-primary)] mb-6"><?= htmlspecialchars($ct_sh['why']['eyebrow'] ?? 'Our Advantage') ?></h2>
                <h3 class="text-5xl md:text-6xl font-black tracking-tighter mb-6 text-white"><?= htmlspecialchars($ct_sh['why']['heading'] ?? 'Why Choose Digifyce') ?></h3>
                <p class="text-lg text-slate-400 leading-relaxed mb-6">
                    <?= htmlspecialchars($ct_sh['why']['sub_text'] ?? 'At Digifyce, we value our clients by combining strategy, SEO understanding, and customer psychology to create content that builds visibility, engagement, and business growth.') ?>
                </p>
                <p class="text-lg text-slate-300 font-bold border-l-2 border-[var(--clr-primary)] pl-4 mb-8">
                    <?= htmlspecialchars($ct_sh['why']['extra_text'] ?? 'We do not just create content, we create growth through content.') ?>
                </p>
                <div class="mt-8">
                    <a href="<?= htmlspecialchars($ct_sh['why']['btn_url'] ?? 'leadform.php') ?>" class="btn btn-white">
                        <?= htmlspecialchars($ct_sh['why']['btn_label'] ?? 'Create Growth Today') ?>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- Right: Points Grid (Mobile Slider) -->
            <div class="lg:w-2/3">
                <h4 class="text-2xl font-bold mb-8 text-white"><?= htmlspecialchars($ct_sh['why_right_title']['heading'] ?? 'What makes us different:') ?></h4>

                <div
                    class="flex md:grid md:grid-cols-2 gap-6 overflow-x-auto md:overflow-visible snap-x snap-mandatory touch-pan-x pb-8 md:pb-0 -mx-4 px-4 md:mx-0 md:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                    <?php foreach ($ct_why as $wp): ?>
                    <div class="w-[85vw] md:w-auto flex-shrink-0 snap-start bg-white/5 p-8 rounded-3xl border border-white/10 hover:border-[var(--clr-primary)]/50 transition-colors">
                        <span class="material-symbols-outlined text-[var(--clr-primary)] text-3xl mb-4"><?= htmlspecialchars($wp['icon']) ?></span>
                        <h5 class="text-xl font-bold mb-2 text-white"><?= htmlspecialchars($wp['title']) ?></h5>
                        <p class="text-slate-400 text-sm leading-relaxed"><?= htmlspecialchars($wp['description']) ?></p>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Final CTA -->
<section id="cta-final">
    <div class="cta-bg-text"><?= htmlspecialchars($ct_cta['bg_text'] ?? 'GROW') ?></div>
    <div class="cta-inner" data-reveal>
        <h2><?= htmlspecialchars($ct_cta['heading'] ?? 'Ready to Build a Content System That Scales?') ?></h2>
        <p><?= htmlspecialchars($ct_cta['description'] ?? 'Your audience is already searching for solutions. We help ensure they find you first, trust you immediately, and convert flawlessly.') ?></p>
        <a href="<?= htmlspecialchars($ct_cta['btn_url'] ?? 'leadform.php') ?>" class="btn btn-white" style="font-size:1rem;padding:18px 44px;">
            <?= htmlspecialchars($ct_cta['btn_label'] ?? 'Start Your Content Strategy') ?>
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </a>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        // --- Scroll Parallax (Background Globs Only) ---
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            parallaxElements.forEach(el => {
                const speed = parseFloat(el.getAttribute('data-parallax'));
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        }, { passive: true });

        // --- 1. Reveal Animations ---
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');

                    // Fire Counter Animation if active
                    const counter = entry.target.querySelector('.counter');
                    if (counter && !counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        const target = +counter.getAttribute('data-target');
                        let count = 0;
                        const increment = target / 40;
                        const updateCount = () => {
                            count += increment;
                            if (count < target) {
                                counter.innerText = Math.ceil(count);
                                requestAnimationFrame(updateCount);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        updateCount();
                    }
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal, .reveal-scale').forEach(el => observer.observe(el));
        setTimeout(() => document.querySelectorAll('.animate-on-load').forEach(el => el.classList.add('active')), 100);

        // --- 2. Interactive Tabs ---
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-content-panel');
        const tabsRail = document.querySelector('.pillars-tabs');

        const activateTab = (btn) => {
            if (!btn) return;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const targetPanel = document.getElementById(btn.getAttribute('data-target'));
            if (targetPanel) targetPanel.classList.add('active');
        };

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                activateTab(btn);

                // Keep selected tab in view on mobile
                if (window.innerWidth < 1024) {
                    btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });
        });

        // On mobile, swiping the horizontal tab rail updates the content panel automatically.
        if (tabsRail) {
            let ticking = false;
            const syncTabFromScroll = () => {
                const isMobile = window.innerWidth < 1024;
                if (!isMobile) return;

                const railRect = tabsRail.getBoundingClientRect();
                const railCenter = railRect.left + (railRect.width / 2);
                let nearestBtn = null;
                let nearestDistance = Number.POSITIVE_INFINITY;

                tabBtns.forEach((btn) => {
                    const rect = btn.getBoundingClientRect();
                    const btnCenter = rect.left + (rect.width / 2);
                    const distance = Math.abs(btnCenter - railCenter);

                    if (distance < nearestDistance) {
                        nearestDistance = distance;
                        nearestBtn = btn;
                    }
                });

                activateTab(nearestBtn);
            };

            tabsRail.addEventListener('scroll', () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(() => {
                    syncTabFromScroll();
                    ticking = false;
                });
            }, { passive: true });

            window.addEventListener('resize', syncTabFromScroll);
            syncTabFromScroll();
        }
    });
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>