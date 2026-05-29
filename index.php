<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'home');
$pageTitle = $_seo['meta_title'] ?: 'Digifyce – Performance Marketing & Growth Agency';
$pageDescription = $_seo['meta_description'] ?: 'Grow your business with Digifyce, a top digital marketing agency in Coimbatore offering performance marketing, D2C growth, e-commerce marketing, and lead generation solutions.';
include __DIR__ . '/app/views/header.php';
?>
<?php
require_once __DIR__ . '/db.php';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
$homeContent = [];
$contentQuery = $mysqli->query("SELECT section_key, content FROM page_content WHERE page_slug = 'home'");
if ($contentQuery) {
    while ($row = $contentQuery->fetch_assoc()) {
        $homeContent[$row['section_key']] = $row['content'];
    }
}

$hero = [
    'headline' => $homeContent['hero_title'] ?? 'TRANSFORM <br/><span class="text-white/20">DIGITAL PRESENCE</span> <br/>INTO REVENUE.',
    'subtext' => $homeContent['hero_subtext'] ?? 'We scale high-growth brands through hyper-precision data and minimalist strategy. No noise, just performance.',
    'cta_label' => $homeContent['hero_cta_text'] ?? 'Get Free Audit',
    'cta_url' => $homeContent['hero_cta_url'] ?? '#'
];

$homeCtaLabel = $homeContent['last_cta_text'] ?? 'Let\'s work together';
$homeCtaUrl = $homeContent['last_cta_url'] ?? '#';
$homeCtaNote = $homeContent['last_subtext'] ?? 'We\'ll respond within 24 hours';
$homeCtaHref = trim($homeCtaUrl);
$homeCtaTarget = '';
if ($homeCtaHref !== '') {
    $isExternal = strpos($homeCtaHref, 'http') === 0 || strpos($homeCtaHref, '//') === 0;
    $isAnchor = strpos($homeCtaHref, '#') === 0;
    if ($isExternal) {
        $homeCtaTarget = ' target="_blank"';
    } elseif (!$isAnchor) {
        if (strpos($homeCtaHref, '/') === 0) {
            $homeCtaHref = $appUrl . $homeCtaHref;
        } else {
            $homeCtaHref = $appUrl . '/' . ltrim($homeCtaHref, '/');
        }
    }
} else {
    $homeCtaHref = '#';
}
?>
<section class="relative min-h-[100vh] sm:min-h-screen flex items-center pt-12 sm:pt-16 px-4 sm:px-6 lg:px-8"
    style="background: transparent;">
    <div class="absolute inset-0 w-full h-full -z-20 pointer-events-none select-none">
        <img src="public/assets/img/map.png" alt="Map Background"
            class="w-full h-full object-cover opacity-90 object-[65%_center] sm:object-center" loading="eager">
    </div>
    <div class="absolute inset-0 w-full h-full -z-10 pointer-events-none select-none"
        style="background: linear-gradient(120deg, rgba(3,5,8,0.92) 0%, rgba(3,5,8,0.7) 60%, rgba(3,5,8,0.92) 100%);">
    </div>
    <div class="max-w-[1440px] mx-auto w-full relative z-10 py-12">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-2 items-start">
            <!-- Left Column: Badge + Title + CTA + Subtext -->
            <div class="lg:w-[56%] w-full flex flex-col ">
                <div class="inline-block mb-8 sm:mb-12 text-medium font-bold tracking-[0.3em] uppercase text-[var(--electric-blue)]"
                    style="text-size:15px;">
                    <?= $homeContent['hero_subtitle'] ?? 'Our Vision' ?>
                </div>
                <h1 class="text-5xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-[3.7rem] font-bold mb-10 sm:mb-14">
                    <?= $hero['headline'] ?>
                </h1>
                <div class="flex flex-col xl:flex-row gap-6 xl:gap-8 items-start xl:items-center">
                    <a href="<?= htmlspecialchars($hero['cta_url']) ?>" class="shrink-0">
                        <button
                            class="group relative px-8 sm:px-12 py-4 sm:py-6 bg-[var(--electric-blue)] text-white font-bold text-base sm:text-lg overflow-hidden transition-all hover:pr-12 sm:hover:pr-16 w-full md:w-auto">
                            <span class="relative z-10 uppercase tracking-widest text-sm">
                                <?= htmlspecialchars($hero['cta_label']) ?>
                            </span>
                            <span
                                class="material-symbols-outlined absolute right-6 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all">arrow_forward</span>
                        </button>
                    </a>
                    <p
                        class="max-w-md text-slate-500 text-base sm:text-lg leading-relaxed xl:border-l border-white/10 xl:pl-8">
                        <?= nl2br(htmlspecialchars($hero['subtext'])) ?>
                    </p>
                </div>
            </div>
            <!-- Right Column: Service Selection Form -->
            <div class="lg:w-[44%] w-full max-w-3xl lg:mt-0 mt-10 sm:mt-12">
                <form action="leadform.php" method="GET"
                    class="backdrop-blur-md bg-white/5 border border-white/10 rounded-2xl p-6 sm:p-4 shadow-[0_24px_80px_rgba(0,0,0,0.4)]">
                    <h3
                        class="text-xl sm:text-2xl mb-6 font-bold text-white text-left tracking-tight border-b border-white/5 pb-4">
                        <?= $homeContent['hero_services_title'] ?? 'Select the services you\'re interested in' ?>
                    </h3>
                    <div class="services-grid">
                        <?php for ($i = 1; $i <= 10; $i++):
                            $defaultValues = [
                                1 => 'Brand Kit',
                                2 => 'Package Design',
                                3 => 'E-Commerce Sales',
                                4 => 'E-com / Web Development',
                                5 => 'Market Places(Amazon, Flipkart)',
                                6 => 'End to End Digital Transformation',
                                7 => 'Content Production',
                                8 => 'CRM',
                                9 => 'AI Chatbots',
                                10 => 'Sales Audit & Scaling'
                            ];
                            $chkValue = $homeContent['hero_chk_' . $i] ?? $defaultValues[$i];
                            if (!empty(trim($chkValue))):
                                ?>
                                <label class="cdf-service-item">
                                    <input type="checkbox" class="cdf-service-checkbox" name="services[]"
                                        value="<?= htmlspecialchars(trim($chkValue)) ?>">
                                    <span class="custom-chk"></span>
                                    <span class="service-label"><?= htmlspecialchars(trim($chkValue)) ?></span>
                                </label>
                            <?php endif; endfor; ?>
                    </div>
                    <div style="display: flex; justify-content: flex-end; width: 100%; margin-top: 1.5rem;">
                        <button id="cdfSubmitBtn" type="submit" disabled class="px-6 py-4 rounded-xl font-bold transition-all duration-300 flex items-center gap-2
                            bg-white/5 text-white/40 cursor-not-allowed border border-white/5">
                            <?= $homeContent['hero_services_cta_text'] ?? 'Get Your Brand Audit' ?>
                            <span class="material-symbols-outlined text-lg align-middle">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <style>
            .services-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                margin-bottom: 1rem;
            }

            .cdf-service-item {
                position: relative;
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.02);
                padding: 0.85rem 8px;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                transition: all 0.25s cubic-bezier(0.25, 1, 0.5, 1);
                cursor: pointer;
                font-size: 13px;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.6);
                width: 100%;
                min-width: 0;
                box-sizing: border-box;
                user-select: none;
            }

            .cdf-service-item:hover {
                border-color: rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.04);
                color: #ffffff;
                transform: translateY(-2px);
            }

            /* Custom Checkbox Design */
            .cdf-service-checkbox {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
            }

            .custom-chk {
                position: relative;
                flex-shrink: 0;
                width: 18px;
                height: 18px;
                border: 1.5px solid rgba(255, 255, 255, 0.25);
                border-radius: 5px;
                transition: all 0.25s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, 0.2);
            }

            .custom-chk::after {
                content: "";
                position: absolute;
                width: 5px;
                height: 9px;
                border: solid #ffffff;
                border-width: 0 2px 2px 0;
                transform: rotate(45deg) scale(0);
                transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                top: 2px;
            }

            /* Checked States */
            .cdf-service-checkbox:checked+.custom-chk {
                background: var(--electric-blue, #0066ff);
                border-color: var(--electric-blue, #0066ff);
                box-shadow: 0 0 12px rgba(0, 102, 255, 0.5);
            }

            .cdf-service-checkbox:checked+.custom-chk::after {
                transform: rotate(45deg) scale(1);
            }

            .cdf-service-item:has(.cdf-service-checkbox:checked) {
                border-color: var(--electric-blue, #0066ff);
                background: rgba(0, 102, 255, 0.06);
                color: #ffffff;
                box-shadow: 0 4px 20px rgba(0, 102, 255, 0.12);
            }

            @media (max-width: 640px) {
                .services-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.5rem;
                }

                .cdf-service-item {
                    font-size: 11px;
                    padding: 0.75rem 0.6rem;
                    gap: 0.5rem;
                    border-radius: 8px;
                }

                .custom-chk {
                    width: 16px;
                    height: 16px;
                }

                .custom-chk::after {
                    width: 4px;
                    height: 7px;
                    top: 2px;
                }
            }
        </style>
    </div>
</section>

<section class="py-4 sm:py-8 bg-[#030508] relative flex flex-col justify-center min-h-screen" id="about-partner">
    <!-- Immersive Background -->
    <div
        class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PGRlZnM+PHBhdHRlcm4gaWQ9ImciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBMMDAgNDBMMDAgMEwwIDBaIiBmaWxsPSJub25lIiBzdHJva2U9InJnYmEoMjU1LDI1NSwyNTUsMC4wMykiIHN0cm9rZS13aWR0aD0iMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNnKSIvPjwvc3ZnPg==')] opacity-20 pointer-events-none">
    </div>
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80vw]  h-[80vw] max-h-[1000px] bg-[var(--electric-blue)] opacity-[0.04] rounded-full blur-[120px] pointer-events-none">
    </div>

    <div class=" mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col items-center text-center">

        <div class="inline-flex items-center gap-4 mb-16 fade-in-up opacity-0">
            <span class="w-12 h-[1px] bg-gradient-to-r from-transparent to-[var(--electric-blue)]"></span>
            <span
                class="text-xs font-bold tracking-[0.4em] uppercase text-[var(--electric-blue)]"><?= $homeContent['who_we_are_subtitle'] ?? 'Who We Are' ?></span>
            <span class="w-12 h-[1px] bg-gradient-to-l from-transparent to-[var(--electric-blue)]"></span>
        </div>

        <h2 class="text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold tracking-tighter leading-[1.1] mb-16 text-mask-reveal"
            style="--reveal-color: #ffffff;">
            <?= $homeContent['who_title'] ?? 'Your Trusted Partner for <span class="italic font-light">Your Startup Business.</span>' ?>
        </h2>

        <div class="w-full max-w-5xl mx-auto mt-12 flex flex-col gap-10 sm:gap-14 text-left">

            <!-- Sentence 1 -->
            <div class="cinematic-sentence relative origin-left">
                <!-- Accent Line -->
                <div
                    class="accent-line absolute -left-6 sm:-left-10 top-2 bottom-2 w-1 bg-[var(--electric-blue)] shadow-[0_0_15px_rgba(0,102,255,0.6)] rounded-full">
                </div>

                <h3 class="text-lg sm:text-2xl lg:text-3xl xl:text-4xl font-light leading-[1.4] tracking-tight text-mask-reveal"
                    style="--reveal-color: #ffffff;">
                    <?= $homeContent['who_sub_1'] ?? 'Digifyce is more than a marketing agency, we are a strategic growth partner for modern businesses.' ?>
                </h3>
            </div>

            <!-- Sentence 2 -->
            <div class="cinematic-sentence relative origin-left">
                <!-- Accent Line -->
                <div
                    class="accent-line absolute -left-6 sm:-left-10 top-2 bottom-2 w-1 bg-[var(--electric-blue)] shadow-[0_0_15px_rgba(0,102,255,0.6)] rounded-full">
                </div>

                <h3 class="text-lg sm:text-2xl lg:text-3xl xl:text-4xl font-light leading-[1.4] tracking-tight text-mask-reveal"
                    style="--reveal-color: #ffffff;">
                    <?= $homeContent['who_sub_2'] ?? 'Our expertise in D2C branding helps brands create strong customer connections, improve product perception, and build memorable brand identities.' ?>
                </h3>
            </div>

            <!-- Sentence 3 -->
            <div class="cinematic-sentence relative origin-left">
                <!-- Accent Line -->
                <div
                    class="accent-line absolute -left-6 sm:-left-10 top-2 bottom-2 w-1 bg-[var(--electric-blue)] shadow-[0_0_15px_rgba(0,102,255,0.6)] rounded-full">
                </div>

                <h3 class="text-lg sm:text-2xl lg:text-3xl xl:text-4xl font-light leading-[1.4] tracking-tight text-mask-reveal"
                    style="--reveal-color: #ffffff;">
                    <?= $homeContent['who_sub_3'] ?? 'We work with startups, e-commerce businesses, and growing brands to create performance-driven growth strategies that support long-term success.' ?>
                </h3>
            </div>

        </div>

        <div class="mt-24 partner-cta opacity-0 translate-y-8 flex justify-center">
            <a href="<?= htmlspecialchars($homeContent['who_cta_url'] ?? 'about-us.php') ?>" class="shrink-0">
                <button
                    class="group relative px-8 sm:px-12 py-4 sm:py-6 bg-[var(--electric-blue)] text-white font-bold text-base sm:text-lg overflow-hidden transition-all hover:pr-12 sm:hover:pr-16 w-full md:w-auto">
                    <span
                        class="relative z-10 uppercase tracking-widest text-sm"><?= $homeContent['who_cta_text'] ?? 'Know More About Us' ?></span>
                    <span
                        class="material-symbols-outlined absolute right-6 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all">arrow_forward</span>
                </button>
            </a>
        </div>

    </div>

    <style>
        .cinematic-sentence {
            filter: blur(8px);
            opacity: 0.2;
            transform: scale(1) translateY(0);
            transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .cinematic-sentence.is-focused {
            filter: blur(0px);
            opacity: 1;
            transform: scale(1.03) translateY(-4px);
        }

        .cinematic-sentence .accent-line {
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .cinematic-sentence.is-focused .accent-line {
            opacity: 1;
        }

        .text-mask-reveal {
            background: linear-gradient(to bottom, var(--reveal-color) 45%, rgba(255, 255, 255, 0.1) 55%);
            background-size: 100% 200%;
            background-position: 0% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
            /* fallback */
        }
    </style>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {

            gsap.to(".fade-in-up", {
                opacity: 1,
                y: -10,
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: "#about-partner",
                    start: "top 80%",
                }
            });

            // Text Scrub Effect
            const revealTexts = document.querySelectorAll('.text-mask-reveal');

            revealTexts.forEach((text) => {
                gsap.to(text, {
                    backgroundPosition: "0% 0%",
                    ease: "none",
                    scrollTrigger: {
                        trigger: text,
                        start: "top 85%",
                        end: "bottom 50%",
                        scrub: 1.5,
                    }
                });
            });

            // Fade in CTA at the end
            gsap.to(".partner-cta", {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: ".partner-cta",
                    start: "top 95%",
                }
            });

            // Scroll-Driven Focus/Blur Effect
            const sentences = document.querySelectorAll('.cinematic-sentence');
            sentences.forEach((sentence) => {
                ScrollTrigger.create({
                    trigger: sentence,
                    start: "top 75%",
                    end: "bottom 25%",
                    toggleClass: { targets: sentence, className: "is-focused" }
                });
            });
        }
    });
</script>

<section id="metrics-section" class="py-4 sm: bg-[#030508]">
    <div class="mb-6 sm:mb-8 lg:mb-12 text-center block sm:hidden">
        <h2
            class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
            <?= $homeContent['metrics_subtitle'] ?? 'Strong Retention & Expanding Base' ?>
        </h2>
        <div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">
            <?= $homeContent['metrics_title'] ?? 'Revenue Hypergrowth' ?>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8 sm:gap-12 items-end text-center">

            <!-- 2025 Revenue -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-4xl font-light text-slate-400 mb-2">
                    <span class="counter" data-target="<?= $homeContent['rev1_title'] ?? '12M' ?>">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-500">
                    <?= $homeContent['rev1_sub'] ?? 'Revenue — FY2024' ?>
                </div>
            </div>

            <!-- 2026 Revenue -->
            <div class="flex flex-col justify-end items-center">
                <div class="text-green-400 text-xs mb-2">
                    <?= $homeContent['rev2_sub'] ?? '↑ +148M vs last year' ?>
                </div>

                <div class="text-4xl sm:text-6xl font-semibold text-white mb-2">
                    <span class="counter" data-target="<?= $homeContent['rev2_title'] ?? '160M' ?>">0</span>
                </div>

                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400">
                    <?= $homeContent['rev2_sub2'] ?? 'Revenue — FY2025' ?>
                </div>
            </div>

            <!-- Growth -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-5xl font-light text-white mb-2">
                    <span class="counter" data-target="<?= $homeContent['rev3_title'] ?? '13.3X' ?>">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    <?= $homeContent['rev3_sub'] ?? 'Growth' ?>
                </div>
            </div>

            <!-- Retention -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-5xl font-light text-white mb-2">
                    <span class="counter" data-target="<?= $homeContent['rev4_title'] ?? '82%' ?>">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    <?= $homeContent['rev4_sub'] ?? 'Retention Rate' ?>
                </div>
            </div>

            <!-- Brands Served -->
            <div class="flex flex-col justify-end col-span-2 lg:col-span-1 justify-self-center">
                <div class="text-3xl sm:text-5xl font-light text-white mb-2">
                    <span class="counter" data-target="<?= $homeContent['rev5_title'] ?? '120+' ?>">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    <?= $homeContent['rev5_sub'] ?? 'Brands Served' ?>
                </div>
            </div>

        </div>
    </div>
</section>


<script>
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');
        const duration = 2000;

        counters.forEach(counter => {
            const target = counter.getAttribute('data-target');
            let numericValue = parseFloat(target.replace(/[^0-9.]/g, ''));
            let suffix = '';

            if (target.includes('M')) suffix = 'M';
            else if (target.includes('X')) suffix = 'X';
            else if (target.includes('%')) suffix = '%';

            let startValue = 0;
            const increment = numericValue / (duration / 16);

            function updateCounter() {
                startValue += increment;

                if (startValue < numericValue) {
                    let displayValue;

                    if (suffix === 'X') {
                        displayValue = startValue.toFixed(1);
                    } else {
                        displayValue = Math.floor(startValue);
                    }

                    counter.textContent = displayValue + suffix;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            }

            updateCounter();
        });
    }

    const section = document.getElementById('metrics-section');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(section);
</script>
<section class="py-4 bg-[var(--navy-black)]">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <style>
            .why-slider-controls {
                display: none;
            }

            .about-stats-controls {
                display: none;
            }

            .counter-number {
                font-size: 2.5rem;
                line-height: 1.1;
                font-weight: 700;
            }

            .about-stat-box {
                background: rgba(255, 255, 255, 0.04);
                border: 1.5px solid rgba(255, 255, 255, 0.2);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .about-stat-box:hover {
                border-color: rgba(0, 102, 255, 0.6);
                background: linear-gradient(135deg, rgba(0, 102, 255, 0.12), rgba(0, 102, 255, 0.05));
                transform: translateY(-6px);
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
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    background: rgba(255, 255, 255, 0.04);
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
                    background: rgba(148, 163, 184, 0.45);
                    border: 0;
                    padding: 0;
                }

                .why-slider-dot.is-active {
                    background: var(--electric-blue);
                    box-shadow: 0 0 0 4px rgba(0, 102, 255, 0.18);
                }
            }
        </style>

        <div class="md:col-span-6 spl   it-column mt-10 md:mt-0 md:mb-10" data-mobile-slider-group>
            <div class="grid grid-cols-2 gap-4 lg:gap-6 mobile-scroll-grid about-stats-slider" data-slider-track>
                <div class="anim-stagger-1 about-stat-box rounded-2xl p-6 lg:p-8">
                    <div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">
                        <?= $homeContent['grid1_sub'] ?? 'Founded' ?>
                    </div>
                    <div class="counter-number text-white"><?= $homeContent['grid1_title'] ?? '2022' ?></div>
                    <div class="text-slate-400 text-sm mt-3">
                        <?= $homeContent['grid1_text'] ?? 'Digifyce was built with one clear vision: to help modern brands grow with purpose, precision, and performance. We work with D2C brands, e-commerce businesses, startups, and growing companies that want to create stronger customer connections, improve conversions, and build long-term brand value.' ?>
                    </div>
                </div>
                <div class="anim-stagger-2 about-stat-box rounded-2xl p-6 lg:p-8">
                    <div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">
                        <?= $homeContent['grid2_sub'] ?? 'Brands' ?>
                    </div>
                    <div class="counter-number text-[var(--electric-blue)]">
                        <?= $homeContent['grid2_title'] ?? '120+' ?>
                    </div>
                    <div class="text-slate-400 text-sm mt-3">
                        <?= $homeContent['grid2_text'] ?? 'From branding and creative development to performance marketing, e-commerce growth, commercial shoots, content marketing, and marketplace management, we provide complete solutions that help businesses launch, scale, and dominate their digital presence.' ?>
                    </div>
                </div>
                <div class="anim-stagger-3 about-stat-box rounded-2xl p-6 lg:p-8">
                    <div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">
                        <?= $homeContent['grid3_sub'] ?? 'Focus' ?>
                    </div>
                    <div class="counter-number text-white"><?= $homeContent['grid3_title'] ?? '360°' ?></div>
                    <div class="text-slate-400 text-sm mt-3">
                        <?= $homeContent['grid3_text'] ?? 'Our focus is simple: Build brands that do not just look good but perform better.' ?>
                    </div>
                </div>
                <div class="anim-stagger-4 about-stat-box rounded-2xl p-6 lg:p-8">
                    <div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">
                        <?= $homeContent['grid4_sub'] ?? 'Standard' ?>
                    </div>
                    <div class="counter-number text-[var(--electric-blue)]">
                        <?= $homeContent['grid4_title'] ?? 'ROI' ?>
                    </div>
                    <div class="text-slate-400 text-sm mt-3">
                        <?= $homeContent['grid4_text'] ?? 'We are not just a service provider, we are your growth partner.' ?>
                    </div>
                </div>
            </div>
            <div class="about-stats-controls" data-slider-controls>
                <button type="button" class="why-slider-nav" data-slider-prev
                    aria-label="Previous slide">&#8592;</button>
                <div class="why-slider-dots" data-slider-dots aria-label="Slider pagination"></div>
                <button type="button" class="why-slider-nav" data-slider-next aria-label="Next slide">&#8594;</button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
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


        <section class="py-16 sm:py-20 lg:py-24 bg-[var(--navy-black)] relative overflow-hidden" id="strategy-matrix">
            <div class="mb-6 sm:mb-8 lg:mb-12 text-center">
                <h2
                    class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
                    <?= $homeContent['matrix_subtitle'] ?? 'Methodology' ?>
                </h2>
                <div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">
                    <?= $homeContent['matrix_main_title'] ?? 'Our Core Methodology' ?>
                </div>
            </div>

            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

                <div class="grid lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">
                    <div class="relative group order-2 lg:order-1">
                        <div
                            class="absolute -top-8 sm:-top-12 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex flex-col items-center">
                            <span class="mb-2">Motivation</span>
                            <span class="material-symbols-outlined text-xs">arrow_upward</span>
                        </div>
                        <div
                            class="absolute top-1/2 -right-8 sm:-right-16 -translate-y-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex items-center gap-2 rotate-90 origin-center">
                            <span class="hidden sm:inline">Purchase Difficulty</span>
                            <span class="sm:hidden">Difficulty</span>
                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </div>
                        <div
                            class="grid grid-cols-2 aspect-square w-full max-w-[600px] mx-auto border border-white/5 relative bg-[#0a0f1a]/40">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-full h-px bg-white/10"></div>
                                <div class="h-full w-px bg-white/10"></div>
                            </div>
                            <div class="absolute w-6 h-6 sm:w-8 sm:h-8 matrix-orb z-20 pointer-events-none pos-a"
                                id="orb">
                                <div
                                    class="w-full h-full rounded-full bg-[var(--electric-blue)] shadow-[0_0_20px_rgba(0,102,255,0.8)] flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full animate-ping"></div>
                                </div>
                            </div>

                            <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q"
                                id="quad-a" onclick="setMatrix('a')">
                                <span
                                    class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                                    A</span>
                                <div class="mt-auto">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">
                                        <?= $homeContent['matrix_qB_title'] ?? 'High Intent' ?>
                                    </div>
                                    <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">
                                        <?= $homeContent['matrix_qB_sub'] ?? 'Low CTR / High Conv.' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="matrix-quadrant active flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q"
                                id="quad-b" onclick="setMatrix('b')">
                                <span
                                    class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                                    B</span>
                                <div class="mt-auto">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">
                                        <?= $homeContent['matrix_qA_title'] ?? 'Impulse Zone' ?>
                                    </div>
                                    <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">
                                        <?= $homeContent['matrix_qA_sub'] ?? 'High CTR / High Conv.' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q"
                                id="quad-d" onclick="setMatrix('d')">
                                <span
                                    class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                                    D</span>
                                <div class="mt-auto">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">
                                        <?= $homeContent['matrix_qD_title'] ?? 'Click Magnet' ?>
                                    </div>
                                    <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">
                                        <?= $homeContent['matrix_qD_sub'] ?? 'High CTR / Low Conv.' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q"
                                id="quad-c" onclick="setMatrix('c')">
                                <span
                                    class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                                    C</span>
                                <div class="mt-auto">
                                    <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">
                                        <?= $homeContent['matrix_qC_title'] ?? 'Dead Space' ?>
                                    </div>
                                    <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">
                                        <?= $homeContent['matrix_qC_sub'] ?? 'Low CTR / Low Conv.' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between mt-4 px-2 text-[10px] uppercase tracking-[0.2em] text-slate-600">
                            <span>Difficult</span>
                            <span>Easy</span>
                        </div>
                        <div
                            class="absolute -left-8 sm:-left-12 top-0 h-full flex flex-col justify-between py-2 text-[10px] uppercase tracking-[0.2em] text-slate-600 [writing-mode:vertical-lr] rotate-180">
                            <span class="hidden sm:inline">High Motivation</span>
                            <span class="hidden sm:inline">Low Motivation</span>
                            <span class="sm:hidden">High</span>
                            <span class="sm:hidden">Low</span>
                        </div>
                    </div>
                    <div class="glass-panel p-6 sm:p-8 lg:p-12 relative overflow-hidden order-1 lg:order-2">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-[var(--electric-blue)]/10 blur-3xl -mr-16 -mt-16">
                        </div>
                        <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-8 sm:mb-12">Dynamic
                            Insights</h2>
                        <div class="space-y-8 sm:space-y-12" id="insight-content">
                            <div>
                                <div id="diagnosis-header"
                                    class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">
                                    <?= $homeContent['matrix_qA_side_sub1'] ?? 'DIAGNOSIS' ?>
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4" id="diagnosis-title">Maximum
                                    Efficiency
                                    Zone</h3>
                                <p class="text-slate-400 text-base sm:text-lg leading-relaxed" id="diagnosis-text">Your
                                    creative
                                    resonance is perfectly aligned with user intent. Every dollar spent is currently at
                                    peak
                                    velocity.</p>
                            </div>
                            <div>
                                <div id="strategy-header"
                                    class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">
                                    <?= $homeContent['matrix_qA_side_sub2'] ?? 'OPTIMIZATION STRATEGY' ?>
                                </div>
                                <ul class="space-y-3 sm:space-y-4" id="strategy-list">
                                    <li class="flex items-start gap-3 sm:gap-4">
                                        <span
                                            class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
                                        <span class="text-slate-300 text-sm sm:text-base">Increase budget horizontally
                                            across
                                            lookalike segments.</span>
                                    </li>
                                    <li class="flex items-start gap-3 sm:gap-4">
                                        <span
                                            class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
                                        <span class="text-slate-300 text-sm sm:text-base">Test iterative variations of
                                            winning
                                            'hooks' only.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div
                            class="mt-8 sm:mt-12 pt-8 sm:pt-12 border-t border-white/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest text-white/20"
                                id="auto-play-indicator">
                                <div class="w-1.5 h-1.5 rounded-full bg-[var(--electric-blue)] animate-pulse"></div>
                                Auto-Touring Matrix
                            </div>
                            <button
                                class="text-[10px] uppercase tracking-widest text-slate-500 hover:text-white transition-colors flex items-center gap-2"
                                onclick="toggleAutoTour()">
                                <span class="material-symbols-outlined text-xs" id="play-pause-icon">pause</span>
                                <span id="play-pause-text">Pause Tour</span>
                            </button>
                        </div>
                        <div class="mt-6 text-center">
                            <p class="text-slate-500 text-sm mb-4">Enter your email to get detailed PDF</p>
                            <form id="pdfEmailForm" class="flex flex-col sm:flex-row gap-3 max-w-sm mx-auto">
                                <input type="email" name="email" placeholder="Enter your email" required
                                    class="flex-1 px-4 py-3 bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:border-[var(--electric-blue)] transition-colors text-sm">
                                <button type="submit"
                                    class="bg-[var(--electric-blue)] text-white px-6 py-3 font-bold uppercase tracking-wider hover:bg-blue-600 transition-colors text-sm">Get
                                    PDF</button>
                            </form>
                            <div id="pdfEmailMessage" class="mt-3 text-sm hidden"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>




        <!-- Tools We Use Section with Animated Sliders -->
        <div class="py-4  border-t border-white/10">
            <div class="mb-6 sm:mb-8 lg:mb-12 text-center">
                <h2
                    class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
                    <?= $homeContent['stack_subtitle'] ?? 'Our Stack' ?>
                </h2>
                <div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">
                    <?= $homeContent['stack_title'] ?? 'Tools We Use' ?>
                </div>
            </div>

            <style>
                @keyframes scrollRight {
                    0% {
                        transform: translateX(0);
                    }

                    100% {
                        transform: translateX(-50%);
                    }
                }

                @keyframes scrollLeft {
                    0% {
                        transform: translateX(-50%);
                    }

                    100% {
                        transform: translateX(0);
                    }
                }

                .scroll-right {
                    animation: scrollRight 40s linear infinite;
                }

                .scroll-left {
                    animation: scrollLeft 40s linear infinite;
                }

                .logo-filtered {
                    filter: brightness(1.08) invert(0.97) saturate(0);
                    transition: all 0.4s ease;
                }

                .group:hover .logo-filtered {
                    filter: none;
                }

                @media (max-width: 640px) {
                    .scroll-right {
                        animation: scrollRight 30s linear infinite;
                    }

                    .scroll-left {
                        animation: scrollLeft 30s linear infinite;
                    }
                }
            </style>

            <div class="space-y-4 sm:space-y-6">
                <?php
                $toolLogos = [];
                try {
                    $toolPath = __DIR__ . '/public/assets/toolslogo/';
                    if (is_dir($toolPath)) {
                        $files = array_filter(scandir($toolPath), function ($file) use ($toolPath) {
                            $fullPath = $toolPath . $file;
                            return !in_array($file, ['.', '..', 'ads']) && is_file($fullPath);
                        });
                        foreach ($files as $file) {
                            $toolLogos[] = [
                                'name' => pathinfo($file, PATHINFO_FILENAME),
                                'path' => 'public/assets/toolslogo/' . $file
                            ];
                        }
                    }
                } catch (Exception $e) {
                }

                // Split tools into 3 rows - Mix logos across rows for variety
                $allTools = $toolLogos;
                shuffle($allTools); // Randomize logo order
                $row1 = [];
                $row2 = [];
                $row3 = [];

                // Distribute logos across rows in a mixed pattern
                for ($i = 0; $i < count($allTools); $i++) {
                    if ($i % 3 === 0) {
                        $row1[] = $allTools[$i];
                    } elseif ($i % 3 === 1) {
                        $row2[] = $allTools[$i];
                    } else {
                        $row3[] = $allTools[$i];
                    }
                }

                // Fill with placeholders if needed
                while (count($row1) < 6)
                    $row1[] = ['name' => 'Tool ' . (count($row1) + 1), 'path' => null];
                while (count($row2) < 6)
                    $row2[] = ['name' => 'Tool ' . (count($row2) + 7), 'path' => null];
                while (count($row3) < 6)
                    $row3[] = ['name' => 'Tool ' . (count($row3) + 13), 'path' => null];

                // Row 1 - Moving Right
                echo '<div class="relative overflow-hidden h-20 sm:h-24 bg-gradient-to-r from-[var(--navy-black)] via-transparent to-[var(--navy-black)] rounded-lg border border-white/10">';
                echo '<div class="flex gap-4 sm:gap-6 lg:gap-8 scroll-right absolute h-20 sm:h-24 items-center">';
                foreach (array_merge($row1, $row1, $row1, $row1) as $tool) {
                    echo '<div class="h-16 sm:h-20 min-w-[110px] sm:min-w-[120px] lg:min-w-[140px] w-[110px] sm:w-[120px] lg:w-[140px] flex items-center justify-center border border-white/10 bg-white/5 rounded px-2 sm:px-3 lg:px-4 group hover:border-white/30 hover:bg-white transition-all flex-shrink-0">';
                    if ($tool['path']) {
                        echo '<img src="' . htmlspecialchars($appUrl . '/' . $tool['path']) . '" alt="' . htmlspecialchars($tool['name']) . '" class="max-h-full max-w-full object-contain opacity-70 group-hover:opacity-100 transition-opacity logo-filtered">';
                    } else {
                        echo '<span class="text-slate-600 text-[10px] sm:text-xs text-center">' . htmlspecialchars($tool['name']) . '</span>';
                    }
                    echo '</div>';
                }
                echo '</div></div>';

                // Row 2 - Moving Left
                echo '<div class="relative overflow-hidden h-20 sm:h-24 bg-gradient-to-r from-[var(--navy-black)] via-transparent to-[var(--navy-black)] rounded-lg border border-white/10">';
                echo '<div class="flex gap-4 sm:gap-6 lg:gap-8 scroll-left absolute h-20 sm:h-24 items-center">';
                foreach (array_merge($row2, $row2, $row2, $row2) as $tool) {
                    echo '<div class="h-16 sm:h-20 min-w-[110px] sm:min-w-[120px] lg:min-w-[140px] w-[110px] sm:w-[120px] lg:w-[140px] flex items-center justify-center border border-white/10 bg-white/5 rounded px-2 sm:px-3 lg:px-4 group hover:border-white/30 hover:bg-white transition-all flex-shrink-0">';
                    if ($tool['path']) {
                        echo '<img src="' . htmlspecialchars($appUrl . '/' . $tool['path']) . '" alt="' . htmlspecialchars($tool['name']) . '" class="max-h-full max-w-full object-contain opacity-70 group-hover:opacity-100 transition-opacity logo-filtered">';
                    } else {
                        echo '<span class="text-slate-600 text-[10px] sm:text-xs text-center">' . htmlspecialchars($tool['name']) . '</span>';
                    }
                    echo '</div>';
                }
                echo '</div></div>';

                // Row 3 - Moving Right
                echo '<div class="relative overflow-hidden h-20 sm:h-24 bg-gradient-to-r from-[var(--navy-black)] via-transparent to-[var(--navy-black)] rounded-lg border border-white/10">';
                echo '<div class="flex gap-4 sm:gap-6 lg:gap-8 scroll-right absolute h-20 sm:h-24 items-center">';
                foreach (array_merge($row3, $row3, $row3, $row3) as $tool) {
                    echo '<div class="h-16 sm:h-20 min-w-[110px] sm:min-w-[120px] lg:min-w-[140px] w-[110px] sm:w-[120px] lg:w-[140px] flex items-center justify-center border border-white/10 bg-white/5 rounded px-2 sm:px-3 lg:px-4 group hover:border-white/30 hover:bg-white transition-all flex-shrink-0">';
                    if ($tool['path']) {
                        echo '<img src="' . htmlspecialchars($appUrl . '/' . $tool['path']) . '" alt="' . htmlspecialchars($tool['name']) . '" class="max-h-full max-w-full object-contain opacity-70 group-hover:opacity-100 transition-opacity logo-filtered">';
                    } else {
                        echo '<span class="text-slate-600 text-[10px] sm:text-xs text-center">' . htmlspecialchars($tool['name']) . '</span>';
                    }
                    echo '</div>';
                }
                echo '</div></div>';
                ?>
            </div>
        </div>
    </div>
</section>
<script>
    const cdfCheckboxes = document.querySelectorAll('.cdf-service-checkbox');
    const cdfSubmitBtn = document.getElementById('cdfSubmitBtn');

    function cdfCheckServices() {
        const checked = document.querySelectorAll('.cdf-service-checkbox:checked');

        if (checked.length > 0) {
            cdfSubmitBtn.disabled = false;
            cdfSubmitBtn.classList.remove('bg-gray-600', 'text-gray-300', 'cursor-not-allowed');
            cdfSubmitBtn.classList.add('bg-[#0066ff]', 'text-white', 'cursor-pointer', 'hover:opacity-90');
        } else {
            cdfSubmitBtn.disabled = true;
            cdfSubmitBtn.classList.add('bg-gray-600', 'text-gray-300', 'cursor-not-allowed');
            cdfSubmitBtn.classList.remove('bg-[#0066ff]', 'text-white', 'cursor-pointer', 'hover:opacity-90');
        }
    }

    cdfCheckboxes.forEach(cb => {
        cb.addEventListener('change', cdfCheckServices);
    });

</script>


<section class="py-4 sm:py-12 lg:py-16 bg-[var(--navy-black)]" id="services">

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h2 class="text-xs uppercase tracking-[0.4em] text-slate-500 mb-6 sm:mb-8">
                <?= $homeContent['serv_sub'] ?? 'Our services are built to support every stage of your brand journey.' ?>
            </h2>
            <div class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter">
                <?= $homeContent['serv_title'] ?? 'Complete Startup Growth and Martech Solutions' ?>
            </div>
        </div>
        <div class="flex flex-col" id="services-list">

            <style>
                .reveal-content {
                    height: 0;
                    overflow: hidden;
                    opacity: 0;
                    transition: height 0.45s ease, opacity 0.35s ease;
                }

                .service-head-row {
                    display: grid;
                    grid-template-columns: auto 1fr auto;
                    align-items: center;
                    gap: 1rem;
                }

                .service-number {
                    min-width: 3rem;
                    padding: 0.5rem 0.75rem;
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 9999px;
                    color: rgba(255, 255, 255, 0.38);
                    font-weight: 700;
                    font-size: 0.75rem;
                    letter-spacing: 0.25em;
                    text-align: center;
                    transition: color 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
                }

                .service-item:hover .service-number,
                .service-item.is-active .service-number {
                    color: white;
                    border-color: rgba(0, 102, 255, 0.35);
                    background: rgba(0, 102, 255, 0.08);
                }

                .service-item.is-active .reveal-content {
                    opacity: 1;
                }

                .service-item:hover .title-text,
                .service-item.is-active .title-text {
                    color: white !important;
                }

                .service-item:hover .arrow-icon,
                .service-item.is-active .arrow-icon {
                    color: var(--electric-blue);
                    transform: rotate(45deg);
                }

                .arrow-icon {
                    transition: transform 0.3s ease, color 0.3s ease;
                }

                @media (max-width: 640px) {
                    .service-head-row {
                        gap: 0.75rem;
                    }

                    .service-number {
                        min-width: 2.4rem;
                        padding: 0.4rem 0.5rem;
                        font-size: 0.65rem;
                    }
                }
            </style>

            <!-- 01 -->
            <!-- 01 -->
            <div class="big-list-item py-4 sm:py-6 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">01
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv1_title'] ?? 'D2C Branding Services' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv1_text'] ?? 'Build strong brand identity, packaging design, and strategic positioning that improves trust and customer recall.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv1_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv1_cta'] ?? 'Build Your Brand' ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 02 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">02
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv2_title'] ?? 'Commercial Shoot Services' ?>
                    </h2>
                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv2_text'] ?? 'Professional product photography, ad films, and visual storytelling that strengthen your <b>D2C branding</b> and improve conversions.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv2_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv2_cta'] ?? 'Make Your Brand Stand Out' ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 03 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">03
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv3_title'] ?? 'Performance Marketing Services' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv3_text'] ?? 'Meta Ads, Google Ads, SEO, PPC, and lead generation strategies focused on ROI and business growth.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv3_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv3_cta'] ?? 'Scale Your Business' ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 04 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">04
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv4_title'] ?? 'E-Commerce Development' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv4_text'] ?? 'Shopify, WooCommerce, and custom websites designed for seamless shopping experiences and higher conversions.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv4_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv4_cta'] ?? 'Build a Store That Sells' ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 05 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">05
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv5_title'] ?? 'Marketplace Management' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv5_text'] ?? 'Amazon and Flipkart listing optimization, account management, and marketplace ads for stronger product visibility and sales.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv5_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv5_cta'] ?? 'Grow Your Marketplace Sales' ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 06 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">06
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv6_title'] ?? 'Content Marketing Services' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv6_text'] ?? 'SEO-friendly content, blogs, and website content that improve visibility and strengthen your brand authority.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv6_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv6_cta'] ?? 'Create Content That Converts' ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 07 -->
            <div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item">
                <div class="service-head-row mb-4">
                    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">07
                        /</div>
                    <h2 class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
                        <?= $homeContent['serv7_title'] ?? 'Creative Development Services' ?>
                    </h2>

                    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
                        arrow_outward
                    </span>
                </div>

                <div class="reveal-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
                        <p class="text-lg sm:text-xl text-slate-400">
                            <?= $homeContent['serv7_text'] ?? 'Graphic design, ad creatives, video editing, and branding visuals that support stronger <b> D2C branding </b> and customer engagement.' ?>
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">

                            <a href="<?= htmlspecialchars($homeContent['serv7_url'] ?? 'leadform.php') ?>"
                                class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center"
                                style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;"><?= $homeContent['serv7_cta'] ?? 'Create High-Impact Visuals' ?></a>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const items = document.querySelectorAll('.service-item');
                const isTouchDevice =
                    'ontouchstart' in window || navigator.maxTouchPoints > 0;

                function openItem(item) {
                    const content = item.querySelector('.reveal-content');
                    content.style.height = content.scrollHeight + "px";
                    item.classList.add('is-active');
                }

                function closeItem(item) {
                    const content = item.querySelector('.reveal-content');
                    content.style.height = 0;
                    item.classList.remove('is-active');
                }

                items.forEach(item => {

                    // DESKTOP → HOVER ONLY
                    if (!isTouchDevice) {
                        item.addEventListener('mouseenter', () => {
                            items.forEach(other => {
                                if (other !== item) closeItem(other);
                            });
                            openItem(item);
                        });

                        item.addEventListener('mouseleave', () => {
                            closeItem(item);
                        });
                    }

                    // MOBILE → CLICK ONLY
                    item.addEventListener('click', () => {
                        if (item.classList.contains('is-active')) {
                            closeItem(item);
                        } else {
                            items.forEach(other => closeItem(other));
                            openItem(item);
                        }
                    });

                });

            });
        </script>

    </div>
</section>
<!-- <div class="mb-6 sm:mb-8 lg:mb-12 text-center">
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
        <div class="brand-story-item" id="story-aishwaryam">
            <video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
                <source src="public/assets/videos/Sriaishwaryam.mp4" type="video/mp4" />
            </video>
            <div class="brand-video-overlay"></div>
            <div class="absolute bottom-12 left-12">
                <span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
                <div class="text-3xl font-bold tracking-tighter text-white">Sri Aishwaryam</div>
            </div>
        </div>
        <div class="absolute right-12 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20">
            <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-0"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-1"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-2"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-500" id="dot-3"></div>
        </div>
    </div>
</section> -->
<section class="py-16 sm:py-20 lg:py-24 bg-[#030508]" id="methodology">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-12 sm:gap-16 lg:gap-20">
        <div class="lg:w-1/3 lg:sticky lg:top-32 h-fit">
            <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6 sm:mb-8">Process</h2>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter mb-6 sm:mb-8 leading-tight">
                <?= $homeContent['proc_title'] ?? 'Our 5-Step <br />Architecture.' ?>
            </h2>
            <p class="text-slate-500 max-w-xs text-sm sm:text-base">
                <?= $homeContent['proc_sub'] ?? 'As a top digital marketing agency in India, we follow a structured yet flexible growth model designed for scalable success.' ?>
            </p>
        </div>
        <div
            class="lg:w-2/3 space-y-12 sm:space-y-16 lg:space-y-20 pl-0 lg:pl-20 relative overflow-hidden lg:overflow-visible">
            <div class="absolute left-0 top-0 bottom-0 vertical-line opacity-20 hidden lg:block"></div>
            <div class="relative methodology-step">
                <div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
                <div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">
                    <?= $homeContent['proc1_sub'] ?? '01. DISCOVERY' ?>
                </div>
                <div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">
                    <?= $homeContent['proc1_title'] ?? 'Uncovering Hidden Data' ?>
                </div>
                <p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">
                    <?= $homeContent['proc1_text'] ?? 'We analyze real business data beyond vanity metrics to identify profitability drivers, customer acquisition costs, and growth opportunities. This data-first approach makes us a reliable performance marketing agency focused on real results.' ?>
                </p>
            </div>
            <div class="relative methodology-step">
                <div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
                <div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">
                    <?= $homeContent['proc2_sub'] ?? '02. STRATEGY' ?>
                </div>
                <div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">
                    <?= $homeContent['proc2_title'] ?? 'The Growth Roadmap' ?>
                </div>
                <p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">
                    <?= $homeContent['proc2_text'] ?? 'We create a customized 90-day roadmap tailored to your business goals. Our strategy combines digital marketing services, D2C growth, and performance marketing to ensure both quick wins and long-term scalability.' ?>
                </p>
            </div>
            <div class="relative methodology-step">
                <div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
                <div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">
                    <?= $homeContent['proc3_sub'] ?? '03. EXECUTION' ?>
                </div>
                <div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">
                    <?= $homeContent['proc3_title'] ?? 'Agile Implementation' ?>
                </div>
                <p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">
                    <?= $homeContent['proc3_text'] ?? 'Our team executes high-performance campaigns across all digital platforms. As an experienced online marketing agency, we focus on rapid testing, optimization, and scaling across paid channels and digital touchpoints.' ?>
                </p>
            </div>
            <div class="relative methodology-step">
                <div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
                <div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">
                    <?= $homeContent['proc4_sub'] ?? '04. OPTIMIZATION' ?>
                </div>
                <div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">
                    <?= $homeContent['proc4_title'] ?? 'Iterative Precision' ?>
                </div>
                <p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">
                    <?= $homeContent['proc4_text'] ?? 'We continuously monitor and improve campaigns. Every campaign is optimized based on real-time data to maximize ROI. This makes Digifyce a trusted performance marketing agency in Coimbatore for businesses focused on growth.' ?>
                </p>
            </div>
            <div class="relative methodology-step">
                <div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
                <div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">
                    <?= $homeContent['proc5_sub'] ?? '05. REPORTING' ?>
                </div>
                <div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">
                    <?= $homeContent['proc5_title'] ?? 'Full Transparency' ?>
                </div>
                <p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">
                    <?= $homeContent['proc5_text'] ?? 'We provide complete transparency through live dashboards and detailed reports. Track every rupee spent and every result generated with our data-driven approach as a leading marketing agency in India.' ?>
                </p>
            </div>
        </div>
    </div>
</section>


<!-- We Got Recognized In Section -->
<div class="mb-8 sm:mb-10 lg:mb-12">
    <div class="mb-6 sm:mb-8 lg:mb-12 text-center">
        <h2
            class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
            Press & Recognition</h2>
        <div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">

            <?= htmlspecialchars($homeContent['press_title'] ?? 'We Got Recognized In') ?>
        </div>
    </div>
    <div class="relative overflow-hidden bg-white/[0.02] rounded-lg border border-white/10 p-4 sm:p-6 lg:p-8">
        <div class="flex gap-4 sm:gap-6 lg:gap-8 xl:gap-12 items-center justify-center flex-wrap">
            <?php
            $recognitionLogos = [];
            try {
                $recognitionPath = __DIR__ . '/public/assets/toolslogo/ads/';
                if (is_dir($recognitionPath)) {
                    $files = array_filter(scandir($recognitionPath), function ($file) use ($recognitionPath) {
                        $fullPath = $recognitionPath . $file;
                        return !in_array($file, ['.', '..']) && is_file($fullPath);
                    });
                    foreach ($files as $file) {
                        $recognitionLogos[] = [
                            'name' => pathinfo($file, PATHINFO_FILENAME),
                            'path' => 'public/assets/toolslogo/ads/' . $file
                        ];
                    }
                }
            } catch (Exception $e) {
            }

            if (empty($recognitionLogos)) {
                for ($i = 1; $i <= 5; $i++) {
                    echo '<div class="w-24 sm:w-32 h-16 sm:h-20 flex items-center justify-center border border-white/10 bg-white/5 rounded text-slate-600 text-xs sm:text-sm">Logo ' . $i . '</div>';
                }
            } else {
                foreach ($recognitionLogos as $logo) {
                    echo '<div class="h-16 sm:h-20 w-32 sm:w-40 flex items-center justify-center border border-white/10 bg-white/5 rounded px-3 sm:px-4 lg:px-6 group hover:border-white/30 hover:bg-white transition-all">';
                    echo '<img src="' . htmlspecialchars($appUrl . '/' . $logo['path']) . '" alt="' . htmlspecialchars($logo['name']) . '" class="max-h-full max-w-[100px] sm:max-w-[120px] lg:max-w-[150px] object-contain opacity-70 group-hover:opacity-100 transition-opacity logo-filtered">';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- 
<section class="py-16 sm:py-20 lg:py-24 bg-[var(--navy-black)] relative overflow-hidden" id="strategy-matrix">
    <div class="mb-6 sm:mb-8 lg:mb-12 text-center">
        <h2
            class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">
            Methodology</h2>
        <div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Our Core Methodology
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">
            <div class="relative group order-2 lg:order-1">
                <div
                    class="absolute -top-8 sm:-top-12 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex flex-col items-center">
                    <span class="mb-2">Motivation</span>
                    <span class="material-symbols-outlined text-xs">arrow_upward</span>
                </div>
                <div
                    class="absolute top-1/2 -right-8 sm:-right-16 -translate-y-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex items-center gap-2 rotate-90 origin-center">
                    <span class="hidden sm:inline">Purchase Difficulty</span>
                    <span class="sm:hidden">Difficulty</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </div>
                <div
                    class="grid grid-cols-2 aspect-square w-full max-w-[600px] mx-auto border border-white/5 relative bg-[#0a0f1a]/40">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-full h-px bg-white/10"></div>
                        <div class="h-full w-px bg-white/10"></div>
                    </div>
                    <div class="absolute w-6 h-6 sm:w-8 sm:h-8 matrix-orb z-20 pointer-events-none pos-a" id="orb">
                        <div
                            class="w-full h-full rounded-full bg-[var(--electric-blue)] shadow-[0_0_20px_rgba(0,102,255,0.8)] flex items-center justify-center">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full animate-ping"></div>
                        </div>
                    </div>
                    <div class="matrix-quadrant active flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q"
                        id="quad-a" onclick="setMatrix('a')">
                        <span
                            class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                            A</span>
                        <div class="mt-auto">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Impulse Zone</div>
                            <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">High CTR /
                                High Conv.</div>
                        </div>
                    </div>
                    <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-b"
                        onclick="setMatrix('b')">
                        <span
                            class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                            B</span>
                        <div class="mt-auto">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">High Intent</div>
                            <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">Low CTR /
                                High Conv.</div>
                        </div>
                    </div>
                    <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-d"
                        onclick="setMatrix('d')">
                        <span
                            class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                            D</span>
                        <div class="mt-auto">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Click Magnet</div>
                            <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">High CTR /
                                Low Conv.</div>
                        </div>
                    </div>
                    <div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-c"
                        onclick="setMatrix('c')">
                        <span
                            class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT
                            C</span>
                        <div class="mt-auto">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Dead Space</div>
                            <div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">Low CTR /
                                Low Conv.</div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-4 px-2 text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    <span>Difficult</span>
                    <span>Easy</span>
                </div>
                <div
                    class="absolute -left-8 sm:-left-12 top-0 h-full flex flex-col justify-between py-2 text-[10px] uppercase tracking-[0.2em] text-slate-600 [writing-mode:vertical-lr] rotate-180">
                    <span class="hidden sm:inline">High Motivation</span>
                    <span class="hidden sm:inline">Low Motivation</span>
                    <span class="sm:hidden">High</span>
                    <span class="sm:hidden">Low</span>
                </div>
            </div>
            <div class="glass-panel p-6 sm:p-8 lg:p-12 relative overflow-hidden order-1 lg:order-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[var(--electric-blue)]/10 blur-3xl -mr-16 -mt-16"></div>
                <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-8 sm:mb-12">Dynamic
                    Insights</h2>
                <div class="space-y-8 sm:space-y-12" id="insight-content">
                    <div>
                        <div class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">DIAGNOSIS</div>
                        <h3 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4" id="diagnosis-title">Maximum Efficiency
                            Zone</h3>
                        <p class="text-slate-400 text-base sm:text-lg leading-relaxed" id="diagnosis-text">Your creative
                            resonance is perfectly aligned with user intent. Every dollar spent is currently at peak
                            velocity.</p>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">OPTIMIZATION
                            STRATEGY</div>
                        <ul class="space-y-3 sm:space-y-4" id="strategy-list">
                            <li class="flex items-start gap-3 sm:gap-4">
                                <span
                                    class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
                                <span class="text-slate-300 text-sm sm:text-base">Increase budget horizontally across
                                    lookalike segments.</span>
                            </li>
                            <li class="flex items-start gap-3 sm:gap-4">
                                <span
                                    class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
                                <span class="text-slate-300 text-sm sm:text-base">Test iterative variations of winning
                                    'hooks' only.</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div
                    class="mt-8 sm:mt-12 pt-8 sm:pt-12 border-t border-white/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest text-white/20"
                        id="auto-play-indicator">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--electric-blue)] animate-pulse"></div>
                        Auto-Touring Matrix
                    </div>
                    <button
                        class="text-[10px] uppercase tracking-widest text-slate-500 hover:text-white transition-colors flex items-center gap-2"
                        onclick="toggleAutoTour()">
                        <span class="material-symbols-outlined text-xs" id="play-pause-icon">pause</span>
                        <span id="play-pause-text">Pause Tour</span>
                    </button>
                </div>
                <div class="mt-6 text-center">
                    <p class="text-slate-500 text-sm mb-4">Enter your email to get detailed PDF</p>
                    <form id="pdfEmailForm" class="flex flex-col sm:flex-row gap-3 max-w-sm mx-auto">
                        <input type="email" name="email" placeholder="Enter your email" required
                            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:border-[var(--electric-blue)] transition-colors text-sm">
                        <button type="submit"
                            class="bg-[var(--electric-blue)] text-white px-6 py-3 font-bold uppercase tracking-wider hover:bg-blue-600 transition-colors text-sm">Get
                            PDF</button>
                    </form>
                    <div id="pdfEmailMessage" class="mt-3 text-sm hidden"></div>
                </div>
            </div>
        </div>
    </div>
</section> -->


<section class="py-16 sm:py-20 lg:py-24 bg-[var(--navy-black)] overflow-hidden" id="work">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">
            <div class="order-2 lg:order-1">
                <h2 class="text-xs uppercase tracking-[0.4em] text-slate-500 mb-6 sm:mb-8">Selected Case</h2>
                <div class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter mb-6 sm:mb-8">
                    <?= $homeContent['case_title'] ?? 'Scaling Wellness to 3.5X ROAS.' ?>
                </div>
                <p class="text-lg sm:text-xl text-slate-400 leading-relaxed mb-6 sm:mb-8">
                    <?= $homeContent['case_sub'] ?? 'As a results-driven e-commerce marketing agency in Coimbatore, Digifyce transformed a brand’s digital ecosystem using advanced performance marketing strategies.' ?>
                </p>
                <div class="flex flex-wrap gap-12 sm:gap-20">
                    <div>
                        <div class="text-3xl sm:text-4xl font-bold text-white mb-2">
                            <?= $homeContent['case_rev1_val'] ?? '+340%' ?>
                        </div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-600">
                            <?= $homeContent['case_rev1_sub'] ?? 'Revenue' ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl sm:text-4xl font-bold text-white mb-2">
                            <?= $homeContent['case_rev2_val'] ?? '-38%' ?>
                        </div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-600">
                            <?= $homeContent['case_rev2_sub'] ?? 'CPA Reduction' ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative group order-1 lg:order-2">
                <div
                    class="absolute inset-0 bg-[var(--electric-blue)]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                </div>
                <div class=" bg-white/5 overflow-hidden">
                    <img alt="Data Visualization" class=""
                        src="<?= htmlspecialchars($homeContent['case_img_path'] ?? 'public/assets/img/graph.png') ?>" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Email Capture Section -->
<!-- <section class="py-16 bg-gradient-to-b from-[var(--navy-black)] to-slate-900">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-2xl sm:text-3xl font-bold text-white mb-4">
            Get the Complete <span class="text-[var(--electric-blue)]">Strategy Guide</span>
        </h3>
        <p class="text-slate-400 mb-8">
            Enter your email to receive the detailed PDF with advanced optimization frameworks
        </p>
        <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input type="email" placeholder="Enter your email" required 
                   class="flex-1 px-6 py-4 bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:border-[var(--electric-blue)] transition-colors">
            <button type="submit" 
                    class="bg-[var(--electric-blue)] text-white px-8 py-4 font-bold uppercase tracking-wider hover:bg-blue-600 transition-colors">
                Download PDF
            </button>
        </form>
    </div>
</section> -->

<!-- Why Brands Choose Digifyce Section - Ultra Interactive -->
<section
    class="py-16 sm:py-24 lg:py-32 bg-gradient-to-b from-[#030508] via-[#0a0f1a] to-[#030508] relative overflow-hidden"
    id="why-choose-us">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
            class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-[var(--electric-blue)] opacity-[0.08] blur-[140px] rounded-full animate-blob">
        </div>
        <div
            class="absolute bottom-0 left-1/3 w-[500px] h-[500px] bg-[var(--electric-blue)] opacity-[0.05] blur-[120px] rounded-full animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute top-1/2 right-0 w-[400px] h-[400px] bg-[var(--electric-blue)] opacity-[0.03] blur-[100px] rounded-full animate-blob animation-delay-4000">
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header with GSAP Animation -->
        <div class="mb-12 sm:mb-16 lg:mb-20">
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center gap-3 mb-6 sm:mb-8 px-5 py-2.5 rounded-full border border-[var(--electric-blue)]/40 bg-gradient-to-r from-[var(--electric-blue)]/10 to-[var(--electric-blue)]/5 backdrop-blur-lg hover-glow-badge transition-all duration-300 cursor-pointer group">
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-[var(--electric-blue)] animate-pulse group-hover:scale-150 transition-transform">
                    </div>
                    <span
                        class="text-xs sm:text-sm font-bold tracking-[0.35em] uppercase text-[var(--electric-blue)] group-hover:tracking-[0.45em] transition-all">Why
                        choose digifyce</span>
                </div>

                <h2 id="why-title"
                    class="text-3xl sm:text-5xl lg:text-7xl font-bold tracking-tighter mb-4 sm:mb-8 text-white leading-tight">
                    <?= $homeContent['why_title'] ?? 'Why Brands <br class="hidden sm:block" /> <span class="text-[var(--electric-blue)] inline-block">Choose Digifyce</span>' ?>
                </h2>

                <p id="why-subtitle"
                    class="text-base sm:text-lg lg:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed font-light">
                    <?= $homeContent['why_sub'] ?? 'At Digifyce, we believe successful D2C branding requires more than attractive visuals. It requires strategy, consistency, and a deep understanding of customer behavior.' ?>
                </p>
            </div>

            <!-- Interactive Stats Row -->

        </div>

        <!-- Dynamic Filter Tabs -->
        <div class="flex flex-wrap gap-2 sm:gap-3 justify-center mb-8 sm:mb-12 filter-tabs">
            <button
                class="advantage-tab active relative px-5 sm:px-7 py-2.5 sm:py-3 rounded-lg text-xs sm:text-sm font-bold uppercase tracking-wider transition-all duration-300 group"
                data-tab="all">
                <span class="relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    All Features
                </span>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-[var(--electric-blue)] to-[var(--electric-blue)]/80 rounded-lg opacity-0 group-[.active]:opacity-100 transition-opacity duration-300 -z-10">
                </div>
            </button>
            <button
                class="advantage-tab relative px-5 sm:px-7 py-2.5 sm:py-3 rounded-lg text-xs sm:text-sm font-bold uppercase tracking-wider transition-all duration-300 border border-white/20 text-white hover:border-[var(--electric-blue)]/50 hover:bg-[var(--electric-blue)]/5 group"
                data-tab="strategy">
                <span class="relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">architecture</span>
                    Strategy
                </span>
            </button>
            <button
                class="advantage-tab relative px-5 sm:px-7 py-2.5 sm:py-3 rounded-lg text-xs sm:text-sm font-bold uppercase tracking-wider transition-all duration-300 border border-white/20 text-white hover:border-[var(--electric-blue)]/50 hover:bg-[var(--electric-blue)]/5 group"
                data-tab="execution">
                <span class="relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    Execution
                </span>
            </button>
            <button
                class="advantage-tab relative px-5 sm:px-7 py-2.5 sm:py-3 rounded-lg text-xs sm:text-sm font-bold uppercase tracking-wider transition-all duration-300 border border-white/20 text-white hover:border-[var(--electric-blue)]/50 hover:bg-[var(--electric-blue)]/5 group"
                data-tab="support">
                <span class="relative z-10 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                    Support
                </span>
            </button>
        </div>

        <!-- Premium Cards Grid / Slider -->
        <div class="cards-container grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-5 sm:grid-auto-flow-row sm:overflow-auto sm:scroll-smooth"
            id="cardsContainer">
            <!-- Card 1 -->
            <div class="advantage-card group h-full shrink-0 sm:shrink" data-category="strategy">
                <div
                    class="relative h-full p-6 sm:p-8 rounded-2xl backdrop-blur-xl bg-gradient-to-br from-white/[0.12] via-white/[0.05] to-white/[0.02] border border-white/15 hover:border-[var(--electric-blue)]/60 transition-all duration-500 hover:shadow-[0_20px_60px_rgba(0,102,255,0.25)] hover:-translate-y-3 overflow-hidden cursor-pointer card-inner">
                    <!-- Gradient Orb Background -->
                    <div
                        class="absolute -top-20 -right-20 w-40 h-40 bg-[var(--electric-blue)] opacity-0 blur-3xl rounded-full group-hover:opacity-20 transition-opacity duration-500">
                    </div>

                    <!-- Icon -->
                    <div class="relative mb-6 inline-block">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var(--electric-blue)]/30 to-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/50 flex items-center justify-center group-hover:from-[var(--electric-blue)]/50 group-hover:to-[var(--electric-blue)]/30 transition-all duration-500 group-hover:scale-125 group-hover:shadow-[0_0_30px_rgba(0,102,255,0.4)]">
                            <span
                                class="material-symbols-outlined text-2xl text-[var(--electric-blue)]">architecture</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <h3
                        class="text-lg sm:text-xl font-bold text-white mb-3 relative z-10 group-hover:text-[var(--electric-blue)] transition-colors duration-300">
                        <?= $homeContent['why1_title'] ?? 'Brand-First Growth Strategy' ?>
                    </h3>
                    <p class="text-sm sm:text-base text-slate-400 leading-relaxed mb-5 relative z-10">
                        <?= $homeContent['why1_text'] ?? 'We build brands with clear positioning, strong identity, and customer-focused messaging.' ?>
                    </p>

                    <!-- Hover Detail -->
                    <div
                        class="relative z-10 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[var(--electric-blue)] opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <span>Explore</span>
                        <span
                            class="material-symbols-outlined text-sm group-hover:translate-x-2 transition-transform duration-300">arrow_forward</span>
                    </div>

                    <!-- Number Badge -->
                    <div
                        class="absolute top-6 right-6 w-10 h-10 rounded-full bg-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/30 flex items-center justify-center text-xs font-bold text-[var(--electric-blue)] group-hover:bg-[var(--electric-blue)]/20 transition-all duration-300 relative z-10">
                        01
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="advantage-card group h-full shrink-0 sm:shrink" data-category="execution">
                <div
                    class="relative h-full p-6 sm:p-8 rounded-2xl backdrop-blur-xl bg-gradient-to-br from-white/[0.12] via-white/[0.05] to-white/[0.02] border border-white/15 hover:border-[var(--electric-blue)]/60 transition-all duration-500 hover:shadow-[0_20px_60px_rgba(0,102,255,0.25)] hover:-translate-y-3 overflow-hidden cursor-pointer card-inner">
                    <div
                        class="absolute -top-20 -right-20 w-40 h-40 bg-[var(--electric-blue)] opacity-0 blur-3xl rounded-full group-hover:opacity-20 transition-opacity duration-500">
                    </div>
                    <div class="relative mb-6 inline-block">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var(--electric-blue)]/30 to-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/50 flex items-center justify-center group-hover:from-[var(--electric-blue)]/50 group-hover:to-[var(--electric-blue)]/30 transition-all duration-500 group-hover:scale-125 group-hover:shadow-[0_0_30px_rgba(0,102,255,0.4)]">
                            <span
                                class="material-symbols-outlined text-2xl text-[var(--electric-blue)]">trending_up</span>
                        </div>
                    </div>
                    <h3
                        class="text-lg sm:text-xl font-bold text-white mb-3 relative z-10 group-hover:text-[var(--electric-blue)] transition-colors duration-300">
                        <?= $homeContent['why2_title'] ?? 'Performance-Driven Execution' ?>
                    </h3>
                    <p class="text-sm sm:text-base text-slate-400 leading-relaxed mb-5 relative z-10">
                        <?= $homeContent['why2_text'] ?? 'Every branding and marketing decision is designed to improve visibility, conversions, and business growth.' ?>
                    </p>
                    <div
                        class="relative z-10 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[var(--electric-blue)] opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <span>Explore</span>
                        <span
                            class="material-symbols-outlined text-sm group-hover:translate-x-2 transition-transform duration-300">arrow_forward</span>
                    </div>
                    <div
                        class="absolute top-6 right-6 w-10 h-10 rounded-full bg-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/30 flex items-center justify-center text-xs font-bold text-[var(--electric-blue)] group-hover:bg-[var(--electric-blue)]/20 transition-all duration-300 relative z-10">
                        02
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="advantage-card group h-full shrink-0 sm:shrink" data-category="support">
                <div
                    class="relative h-full p-6 sm:p-8 rounded-2xl backdrop-blur-xl bg-gradient-to-br from-white/[0.12] via-white/[0.05] to-white/[0.02] border border-white/15 hover:border-[var(--electric-blue)]/60 transition-all duration-500 hover:shadow-[0_20px_60px_rgba(0,102,255,0.25)] hover:-translate-y-3 overflow-hidden cursor-pointer card-inner">
                    <div
                        class="absolute -top-20 -right-20 w-40 h-40 bg-[var(--electric-blue)] opacity-0 blur-3xl rounded-full group-hover:opacity-20 transition-opacity duration-500">
                    </div>
                    <div class="relative mb-6 inline-block">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var(--electric-blue)]/30 to-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/50 flex items-center justify-center group-hover:from-[var(--electric-blue)]/50 group-hover:to-[var(--electric-blue)]/30 transition-all duration-500 group-hover:scale-125 group-hover:shadow-[0_0_30px_rgba(0,102,255,0.4)]">
                            <span
                                class="material-symbols-outlined text-2xl text-[var(--electric-blue)]">all_inclusive</span>
                        </div>
                    </div>
                    <h3
                        class="text-lg sm:text-xl font-bold text-white mb-3 relative z-10 group-hover:text-[var(--electric-blue)] transition-colors duration-300">
                        <?= $homeContent['why3_title'] ?? 'Complete Growth Support' ?>
                    </h3>
                    <p class="text-sm sm:text-base text-slate-400 leading-relaxed mb-5 relative z-10">
                        <?= $homeContent['why3_text'] ?? 'From brand launch to scaling revenue, we manage the full growth journey under one system.' ?>
                    </p>
                    <div
                        class="relative z-10 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[var(--electric-blue)] opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <span>Explore</span>
                        <span
                            class="material-symbols-outlined text-sm group-hover:translate-x-2 transition-transform duration-300">arrow_forward</span>
                    </div>
                    <div
                        class="absolute top-6 right-6 w-10 h-10 rounded-full bg-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/30 flex items-center justify-center text-xs font-bold text-[var(--electric-blue)] group-hover:bg-[var(--electric-blue)]/20 transition-all duration-300 relative z-10">
                        03
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="advantage-card group h-full shrink-0 sm:shrink" data-category="execution">
                <div
                    class="relative h-full p-6 sm:p-8 rounded-2xl backdrop-blur-xl bg-gradient-to-br from-white/[0.12] via-white/[0.05] to-white/[0.02] border border-white/15 hover:border-[var(--electric-blue)]/60 transition-all duration-500 hover:shadow-[0_20px_60px_rgba(0,102,255,0.25)] hover:-translate-y-3 overflow-hidden cursor-pointer card-inner">
                    <div
                        class="absolute -top-20 -right-20 w-40 h-40 bg-[var(--electric-blue)] opacity-0 blur-3xl rounded-full group-hover:opacity-20 transition-opacity duration-500">
                    </div>
                    <div class="relative mb-6 inline-block">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var(--electric-blue)]/30 to-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/50 flex items-center justify-center group-hover:from-[var(--electric-blue)]/50 group-hover:to-[var(--electric-blue)]/30 transition-all duration-500 group-hover:scale-125 group-hover:shadow-[0_0_30px_rgba(0,102,255,0.4)]">
                            <span class="material-symbols-outlined text-2xl text-[var(--electric-blue)]">domain</span>
                        </div>
                    </div>
                    <h3
                        class="text-lg sm:text-xl font-bold text-white mb-3 relative z-10 group-hover:text-[var(--electric-blue)] transition-colors duration-300">
                        <?= $homeContent['why4_title'] ?? 'Industry-Focused Expertise' ?>
                    </h3>
                    <p class="text-sm sm:text-base text-slate-400 leading-relaxed mb-5 relative z-10">
                        <?= $homeContent['why4_text'] ?? 'We understand the challenges faced by D2C brands, e-commerce businesses, and digital-first startups.' ?>
                    </p>
                    <div
                        class="relative z-10 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[var(--electric-blue)] opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <span>Explore</span>
                        <span
                            class="material-symbols-outlined text-sm group-hover:translate-x-2 transition-transform duration-300">arrow_forward</span>
                    </div>
                    <div
                        class="absolute top-6 right-6 w-10 h-10 rounded-full bg-[var(--electric-blue)]/10 border border-[var(--electric-blue)]/30 flex items-center justify-center text-xs font-bold text-[var(--electric-blue)] group-hover:bg-[var(--electric-blue)]/20 transition-all duration-300 relative z-10">
                        04
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        @keyframes glow-pulse {

            0%,
            100% {
                box-shadow: 0 0 30px rgba(0, 102, 255, 0.3);
            }

            50% {
                box-shadow: 0 0 60px rgba(0, 102, 255, 0.5);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .hover-glow-badge:hover {
            box-shadow: 0 0 30px rgba(0, 102, 255, 0.3);
        }

        .advantage-card {
            animation: slideInUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
        }

        .advantage-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .advantage-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .advantage-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .advantage-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .advantage-card.filtered-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0.2;
                transform: scale(0.95);
                pointer-events: none;
            }
        }

        .advantage-tab.active {
            color: white !important;
        }

        /* Mobile Slider Styles */
        @media (max-width: 640px) {
            .cards-container {
                display: flex !important;
                gap: 1rem;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scroll-behavior: smooth;
                scroll-snap-type: x mandatory;
                padding-bottom: 0.5rem;
            }

            .cards-container::-webkit-scrollbar {
                height: 4px;
            }

            .cards-container::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 10px;
            }

            .cards-container::-webkit-scrollbar-thumb {
                background: rgba(0, 102, 255, 0.3);
                border-radius: 10px;
            }

            .cards-container::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 102, 255, 0.5);
            }

            .advantage-card {
                animation-delay: 0 !important;
                min-width: 80%;
                scroll-snap-align: start;
                scroll-snap-stop: always;
                flex-shrink: unset;
            }

            .filter-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                gap: 0.5rem !important;
            }
        }
    </style>

    <script>
        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);

            document.addEventListener('DOMContentLoaded', function () {
                // Header animations
                gsap.fromTo('#why-title',
                    { opacity: 0, y: 30 },
                    { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out', delay: 0.2 }
                );

                gsap.fromTo('#why-subtitle',
                    { opacity: 0, y: 20 },
                    { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out', delay: 0.4 }
                );

                // Stat cards stagger
                gsap.fromTo('.stat-card',
                    { opacity: 0, y: 20 },
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: 0.15,
                        ease: 'back.out(1.7)',
                        delay: 0.6
                    }
                );

                // Tab filtering
                const tabs = document.querySelectorAll('.advantage-tab');
                const cards = document.querySelectorAll('.advantage-card');

                // Card click redirect to lead form
                cards.forEach(card => {
                    card.addEventListener('click', function (e) {
                        // Only redirect if clicking on the card itself, not on filtered-out cards
                        if (!this.classList.contains('filtered-out')) {
                            window.location.href = 'leadform.php';
                        }
                    });
                });

                tabs.forEach(tab => {
                    tab.addEventListener('click', function () {
                        const selectedTab = this.getAttribute('data-tab');

                        // Update tab styles
                        tabs.forEach(t => {
                            t.classList.remove('active');
                        });
                        this.classList.add('active');

                        // GSAP filter animation
                        cards.forEach((card, index) => {
                            const cardCategory = card.getAttribute('data-category');
                            const shouldShow = selectedTab === 'all' || cardCategory === selectedTab;

                            if (shouldShow) {
                                card.classList.remove('filtered-out');
                                gsap.to(card, {
                                    opacity: 1,
                                    scale: 1,
                                    duration: 0.5,
                                    ease: 'power2.out',
                                    delay: index * 0.05
                                });
                            } else {
                                gsap.to(card, {
                                    opacity: 0.2,
                                    scale: 0.95,
                                    duration: 0.3,
                                    ease: 'power2.in'
                                });
                                card.classList.add('filtered-out');
                            }
                        });
                    });
                });

                // Scroll-triggered card appearances
                cards.forEach((card, index) => {
                    gsap.fromTo(card,
                        { opacity: 0, y: 40 },
                        {
                            opacity: 1,
                            y: 0,
                            duration: 0.7,
                            ease: 'power3.out',
                            delay: index * 0.1,
                            scrollTrigger: {
                                trigger: card,
                                start: 'top 80%',
                                toggleActions: 'play none none none'
                            }
                        }
                    );

                    // Hover effect with GSAP
                    card.addEventListener('mouseenter', function () {
                        gsap.to(this, {
                            duration: 0.4,
                            ease: 'power2.out'
                        });
                    });
                });
            });
        } else {
            // Fallback for when GSAP is not available
            document.addEventListener('DOMContentLoaded', function () {
                const tabs = document.querySelectorAll('.advantage-tab');
                const cards = document.querySelectorAll('.advantage-card');

                // Card click redirect to lead form
                cards.forEach(card => {
                    card.addEventListener('click', function (e) {
                        if (!this.classList.contains('filtered-out')) {
                            window.location.href = 'leadform.php';
                        }
                    });
                });

                tabs.forEach(tab => {
                    tab.addEventListener('click', function () {
                        const selectedTab = this.getAttribute('data-tab');
                        tabs.forEach(t => t.classList.remove('active'));
                        this.classList.add('active');

                        cards.forEach(card => {
                            if (selectedTab === 'all' || card.getAttribute('data-category') === selectedTab) {
                                card.classList.remove('filtered-out');
                            } else {
                                card.classList.add('filtered-out');
                            }
                        });
                    });
                });
            });
        }
    </script>
</section>


<section class="py-20 sm:py-24 lg:py-32 bg-[var(--navy-black)] relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-20 sm:h-32 vertical-line opacity-30"></div>
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-huge font-bold mb-8 sm:mb-12 text-white">
            <?= $homeContent['last_title'] ?? 'READY TO <br /><span class="text-white/20">SCALE?</span>' ?>
        </h2>
        <div class="flex flex-col items-center gap-6 sm:gap-8"> <a href="<?= htmlspecialchars($homeCtaHref) ?>"
                <?= $homeCtaTarget ?> class="bg-white
                text-[var(--navy-black)] px-10 sm:px-16 py-6 sm:py-4 font-bold text-lg sm:text-xl uppercase
                tracking-widest hover:bg-[var(--electric-blue)] hover:text-white transition-all duration-300 w-full
                sm:w-auto text-center">
                <?= htmlspecialchars($homeCtaLabel) ?>
            </a>
            <div class="text-slate-600 uppercase tracking-[0.2em] text-xs">
                <?= htmlspecialchars($homeCtaNote) ?>
            </div>
        </div>
    </div>
</section>

<script>
    // PDF Email Form Handler
    document.getElementById('pdfEmailForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('pdfEmailMessage');
        const emailInput = form.querySelector('input[name="email"]');

        // Disable button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        messageDiv.classList.add('hidden');

        try {
            const response = await fetch('<?= $appUrl ?>/app/api/pdf_email_lead.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: emailInput.value
                })
            });

            const data = await response.json();

            if (data.success) {
                messageDiv.textContent = data.message;
                messageDiv.className = 'mt-3 text-sm text-green-400';
                messageDiv.classList.remove('hidden');
                form.reset();
            } else {
                messageDiv.textContent = data.message || 'An error occurred';
                messageDiv.className = 'mt-3 text-sm text-red-400';
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            messageDiv.textContent = 'Network error. Please try again.';
            messageDiv.className = 'mt-3 text-sm text-red-400';
            messageDiv.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Get PDF';
        }
    });
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
<script>
    const strategies = {
        a: {
            title: <?= json_encode($homeContent['matrix_qA_side_title'] ?? "Maximum Efficiency Zone") ?>,
            diagnosis: <?= json_encode($homeContent['matrix_qA_side_text'] ?? "Your creative resonance is perfectly aligned with user intent. Every dollar spent is currently at peak velocity.") ?>,
            sub1: <?= json_encode($homeContent['matrix_qA_side_sub1'] ?? "DIAGNOSIS") ?>,
            sub2: <?= json_encode($homeContent['matrix_qA_side_sub2'] ?? "OPTIMIZATION STRATEGY") ?>,
            steps: [
                <?= json_encode($homeContent['matrix_qA_pt1'] ?? "Increase budget horizontally across lookalike segments.") ?>,
                <?= json_encode($homeContent['matrix_qA_pt2'] ?? "Test iterative variations of winning 'hooks' only.") ?>
            ]
        },
        b: {
            title: <?= json_encode($homeContent['matrix_qB_side_title'] ?? "Trust Barrier Wall") ?>,
            diagnosis: <?= json_encode($homeContent['matrix_qB_side_text'] ?? "Users want the product but face Friction. High conversion rates suggest strong value, but low CTR means the ad 'hook' is weak.") ?>,
            sub1: <?= json_encode($homeContent['matrix_qB_side_sub1'] ?? "DIAGNOSIS") ?>,
            sub2: <?= json_encode($homeContent['matrix_qB_side_sub2'] ?? "OPTIMIZATION STRATEGY") ?>,
            steps: [
                <?= json_encode($homeContent['matrix_qB_pt1'] ?? "Revamp creative thumbnails and first 3 seconds.") ?>,
                <?= json_encode($homeContent['matrix_qB_pt2'] ?? "A/B test authority-based social proof in ad copy.") ?>
            ]
        },
        c: {
            title: <?= json_encode($homeContent['matrix_qC_side_title'] ?? "Strategic Exit Point") ?>,
            diagnosis: <?= json_encode($homeContent['matrix_qC_side_text'] ?? "Market mismatch. Neither the message nor the offer is sticking. High friction and low desire leads to wasted spend.") ?>,
            sub1: <?= json_encode($homeContent['matrix_qC_side_sub1'] ?? "DIAGNOSIS") ?>,
            sub2: <?= json_encode($homeContent['matrix_qC_side_sub2'] ?? "ACTION PLAN") ?>,
            steps: [
                <?= json_encode($homeContent['matrix_qC_pt1'] ?? "Pause all active sets immediately.") ?>,
                <?= json_encode($homeContent['matrix_qC_pt2'] ?? "Re-evaluate product-market fit and landing page UX.") ?>
            ]
        },
        d: {
            title: <?= json_encode($homeContent['matrix_qD_side_title'] ?? "Engagement Trap") ?>,
            diagnosis: <?= json_encode($homeContent['matrix_qD_side_text'] ?? "Clickbait or high curiosity but low intent. People are clicking, but the landing page isn't closing the deal.") ?>,
            sub1: <?= json_encode($homeContent['matrix_qD_side_sub1'] ?? "DIAGNOSIS") ?>,
            sub2: <?= json_encode($homeContent['matrix_qD_side_sub2'] ?? "OPTIMIZATION STRATEGY") ?>,
            steps: [
                <?= json_encode($homeContent['matrix_qD_pt1'] ?? "Align ad creative closer to the actual offer.") ?>,
                <?= json_encode($homeContent['matrix_qD_pt2'] ?? "Implement post-click educational funnels to build intent.") ?>
            ]
        }
    };
    let currentQuad = 'a';
    let isAutoPlaying = true;
    const tourOrder = ['a', 'b', 'c', 'd'];
    let tourIndex = 0;
    let tourInterval;
    function setMatrix(quad) {
        currentQuad = quad;
        document.querySelectorAll('.matrix-quadrant').forEach(q => q.classList.remove('active'));
        document.getElementById(`quad-${quad}`).classList.add('active');
        const orb = document.getElementById('orb');
        orb.className = `absolute w-8 h-8 -ml-4 -mt-4 matrix-orb z-20 pointer-events-none pos-${quad}`;
        const data = strategies[quad];
        document.getElementById('diagnosis-header').innerText = data.sub1;
        document.getElementById('strategy-header').innerText = data.sub2;
        document.getElementById('diagnosis-title').innerText = data.title;
        document.getElementById('diagnosis-text').innerText = data.diagnosis;
        const list = document.getElementById('strategy-list');
        list.innerHTML = data.steps.map(step => `
            <li class="flex items-start gap-4">
                <span class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1">check_circle</span>
                <span class="text-slate-300">${step}</span>
            </li>
        `).join('');
        if (event && event.type === 'click') {
            stopAutoTour();
        }
    }
    function startAutoTour() {
        tourInterval = setInterval(() => {
            tourIndex = (tourIndex + 1) % tourOrder.length;
            setMatrix(tourOrder[tourIndex]);
        }, 3500);
    }
    function stopAutoTour() {
        isAutoPlaying = false;
        clearInterval(tourInterval);
        document.getElementById('auto-play-indicator').classList.add('opacity-0');
        document.getElementById('play-pause-icon').innerText = 'play_arrow';
        document.getElementById('play-pause-text').innerText = 'Resume Tour';
    }
    function toggleAutoTour() {
        if (isAutoPlaying) {
            stopAutoTour();
        } else {
            isAutoPlaying = true;
            document.getElementById('auto-play-indicator').classList.remove('opacity-0');
            document.getElementById('play-pause-icon').innerText = 'pause';
            document.getElementById('play-pause-text').innerText = 'Pause Tour';
            startAutoTour();
        }
    }
    // Brand Story Scroll Logic
    window.addEventListener('scroll', () => {
        const container = document.getElementById('brand-stories');
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
    // Methodology Steps Animation - Desktop only
    const isMobile = window.innerWidth < 768;

    if (!isMobile) {
        gsap.registerPlugin(ScrollTrigger);
        const steps = gsap.utils.toArray('.methodology-step');
        steps.forEach((step, index) => {
            const isEven = index % 2 === 0;
            gsap.fromTo(step,
                {
                    opacity: 0,
                    x: window.innerWidth < 1024 ? (isEven ? -60 : 60) : 0,
                    y: window.innerWidth < 1024 ? 0 : 50,
                    scale: 0.95
                },
                {
                    opacity: 1,
                    x: 0,
                    y: 0,
                    scale: 1,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: step,
                        start: 'top 90%',
                        toggleActions: 'play none none none'
                    }
                }
            );
        });
    } else {
        // Simple fade-in for mobile using CSS
        const steps = document.querySelectorAll('.methodology-step');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.2 });

        steps.forEach(step => {
            step.style.opacity = '0';
            step.style.transform = 'translateY(20px)';
            step.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(step);
        });
    }

    // Initialize
    startAutoTour();
</script>
</body>

</html>