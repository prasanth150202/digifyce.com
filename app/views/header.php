<?php
$dotenv = __DIR__ . '/../../.env';
if (!isset($_ENV['APP_URL']) && file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
    }
}
$appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
$pageTitle = $pageTitle ?? 'Digifyce | Minimalist High-End Home';
$bodyClass = $bodyClass ?? '';
$tailwindConfig = $tailwindConfig ?? '';
$extraHead = $extraHead ?? '';
$siteLogo = '';
$siteFavicon = '';
$navCtaLabel = 'Audit';
$navCtaUrl = '#';
try {
    require_once __DIR__ . '/../../config/database.php';
    $pdo = Database::getInstance();
    $settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('site_logo','site_favicon','nav_cta_label','nav_cta_url')")->fetchAll();
    foreach ($settings as $row) {
        if ($row['setting_key'] === 'site_logo') {
            $siteLogo = $row['setting_value'];
        } elseif ($row['setting_key'] === 'site_favicon') {
            $siteFavicon = $row['setting_value'];
        } elseif ($row['setting_key'] === 'nav_cta_label') {
            $navCtaLabel = $row['setting_value'] ?: $navCtaLabel;
        } elseif ($row['setting_key'] === 'nav_cta_url') {
            $navCtaUrl = $row['setting_value'] ?: $navCtaUrl;
        }
    }
} catch (Exception $e) {
    $siteLogo = '';
    $siteFavicon = '';
}

$navCtaHref = trim($navCtaUrl);
$navCtaTarget = '';
if ($navCtaHref !== '') {
    $isExternal = strpos($navCtaHref, 'http') === 0 || strpos($navCtaHref, '//') === 0;
    $isAnchor = strpos($navCtaHref, '#') === 0;
    if ($isExternal) {
        $navCtaTarget = ' target="_blank"';
    } elseif (!$isAnchor) {
        if (strpos($navCtaHref, '/') === 0) {
            $navCtaHref = $appUrl . $navCtaHref;
        } else {
            $navCtaHref = $appUrl . '/' . ltrim($navCtaHref, '/');
        }
    }
} else {
    $navCtaHref = '#';
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>"/>
    <meta name="keywords" content="web design, development, digital marketing agency in coimbatore, digifyce"/>
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>"/>
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>"/>
    <meta property="og:site_name" content="Digifyce"/>
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>"/>
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>"/>
    <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" href="<?= htmlspecialchars($appUrl . '/' . ltrim($siteFavicon, '/')) ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?= $tailwindConfig ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script type="text/javascript">     (function(c,l,a,r,i,t,y){         c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};         t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;         y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);     })(window, document, "clarity", "script", "w58ca53ero"); </script>
    <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-2D5QYRRXZG"></script> <script>   window.dataLayer = window.dataLayer || [];   function gtag(){dataLayer.push(arguments);}   gtag('js', new Date());   gtag('config', 'G-2D5QYRRXZG'); </script>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TDJJXH8X');</script>
<!-- End Google Tag Manager -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style type="text/tailwindcss">
        :root {
            --navy-black: #05070a;
            --electric-blue: #0066ff;
            --accent-glow: rgba(0, 102, 255, 0.15);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: var(--navy-black);
            color: #f8fafc;
        }
        .hero-gradient {
            background: linear-gradient(-45deg, #05070a, #0a0f1a, #05070a, #020617);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .big-list-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .big-list-item:hover {
            padding-left: 1rem;
            border-bottom-color: var(--electric-blue);
        }
        @media (min-width: 768px) {
            .big-list-item:hover {
                padding-left: 2rem;
            }
        }
        .reveal-content {
            max-height: 0;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .big-list-item:hover .reveal-content {
            max-height: 300px;
            opacity: 1;
            margin-top: 1rem;
        }
        @media (min-width: 640px) {
            .big-list-item:hover .reveal-content {
                margin-top: 1.5rem;
            }
        }
        .vertical-line {
            width: 1px;
            background: linear-gradient(to bottom, var(--electric-blue), transparent);
        }
        .text-huge {
            font-size: clamp(2.5rem, 8vw, 9rem);
            line-height: 0.9;
            letter-spacing: -0.04em;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-scroll {
            animation: scroll 30s linear infinite;
        }
        .animate-scroll-fast {
            animation: scroll 20s linear infinite;
        }
        .fade-edges {
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }
        .matrix-quadrant {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid var(--glass-border);
        }
        .matrix-quadrant.active {
            border-color: var(--electric-blue);
            box-shadow: 0 0 30px rgba(0, 102, 255, 0.15);
            background: rgba(0, 102, 255, 0.05);
        }
        .matrix-orb {
            transition: all 0.8s cubic-bezier(0.65, 0, 0.35, 1);
        }
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
        }
        .pos-a {
            left: 25%;
            top: 25%;
            transform: translate(-50%, -50%);
        }
        .pos-b {
            left: 75%;
            top: 25%;
            transform: translate(-50%, -50%);
        }
        .pos-c {
            left: 75%;
            top: 75%;
            transform: translate(-50%, -50%);
        }
        .pos-d {
            left: 25%;
            top: 75%;
            transform: translate(-50%, -50%);
        }
        .methodology-step {
            opacity: 0;
            transform: translateY(50px);
        }
        .methodology-step.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .active-label {
            color: var(--electric-blue);
            text-shadow: 0 0 10px rgba(0, 102, 255, 0.5);
        }
        .brand-story-scroll-container {
            height: 500vh;
            background: #000;
        }
        .brand-story-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-story-item {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), transform 2s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1.1) translateZ(0);
            will-change: transform, opacity;
        }
        .brand-story-item.visible {
            opacity: 1;
            transform: scale(1) translateZ(0);
        }
        .brand-video-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 40%);
        }
    </style>
    <?= $extraHead ?>
    <style>
        /* Force uppercase for all menu links */
        .main-nav-uppercase, .main-nav-uppercase * {
            text-transform: uppercase !important;
        }
    </style>
    <style>
       /* Preloader Wrapper */
#preloader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.6s ease;
}

/* Blur Background */
.preloader-overlay {
    position: absolute;
    inset: 0;
    background: rgba(5, 7, 10, 0.6);
    backdrop-filter: blur(12px);.
    -webkit-backdrop-filter: blur(12px);
}

/* Loader */
.loader {
    position: relative;
    width: 120px;
    height: 90px;
    z-index: 2;
}

.loader:before {
    content: "";
    position: absolute;
    bottom: 0px;
    left: 45px;
    height: 30px;
    width: 30px;
    border-radius: 50%;
    background: #0066ff; /* electric blue */
    animation: loading-bounce 0.5s ease-in-out infinite alternate;
}

@keyframes loading-bounce {
    0% { transform: scale(1, 0.7); }
    40% { transform: scale(0.8, 1.2); }
    60% { transform: scale(1, 1); }
    100% { bottom: 60px; }
}

#preloader.hide {
    opacity: 0;
    pointer-events: none;
}
    </style>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TDJJXH8X"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <!-- Common Preloader -->
    <div id="preloader">
    <div class="preloader-overlay"></div>
    <div class="loader"></div>
</div>
    <script>
        // Hide preloader when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                var preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.classList.add('hide');
                    setTimeout(function() {
                        preloader.style.display = 'none';
                    }, 600);
                }
            }, 200); // slight delay for smoothness
        });
    </script>
    <nav class="fixed top-0 left-0 right-0 z-50 main-nav-uppercase" style="background: rgba(5, 7, 10, 0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-20 sm:h-24 flex items-center justify-between">
            <a class="flex items-center gap-2" href="<?= htmlspecialchars($appUrl) ?>">
                <?php if (!empty($siteLogo)): ?>
                    <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($siteLogo, '/')) ?>" alt="Logo" class="h-16 sm:h-16 md:h-20 w-auto">
                <?php else: ?>
                    <span class="text-xl sm:text-2xl font-bold tracking-tighter text-white">DIGIFYCE</span>
                <?php endif; ?>
            </a>
            <div class="hidden md:flex items-center gap-6 lg:gap-12 text-xs uppercase tracking-[0.2em] font-bold text-white/70">
                <?php 
                    $parents = [];
                    $children = [];
                    $buildUrl = function ($rawUrl) use ($appUrl) {
                        $url = trim($rawUrl);
                        $isExternal = strpos($url, 'http') === 0 || strpos($url, '//') === 0;
                        $isAnchor = strpos($url, '#') === 0;
                        if (!$isExternal && !$isAnchor) {
                            if (strpos($url, '/') === 0) {
                                $url = $appUrl . $url;
                            } else {
                                $url = $appUrl . '/' . ltrim($url, '/');
                            }
                        }
                        return [$url, $isExternal];
                    };

                    // Fetch dynamic navigation from database
                    try {
                        require_once __DIR__ . '/../../config/database.php';
                        $pdo = Database::getInstance();
                        $navItems = $pdo->query('SELECT id, label, url, parent_id FROM navigation WHERE is_footer = 0 ORDER BY position ASC')->fetchAll();

                        foreach ($navItems as $item) {
                            if (empty($item['parent_id'])) {
                                $parents[] = $item;
                            } else {
                                $children[$item['parent_id']][] = $item;
                            }
                        }

                        foreach ($parents as $item) {
                            $childItems = $children[$item['id']] ?? [];
                            [$url, $isExternal] = $buildUrl($item['url']);
                            $target = $isExternal ? ' target="_blank"' : '';

                            if (empty($childItems)) {
                                echo '<a class="hover:text-white transition-colors text-xs uppercase" href="' . htmlspecialchars($url) . '"' . $target . '>' . htmlspecialchars(strtoupper($item['label'])) . '</a>';
                            } else {
                                echo '<div class="relative group">';
                                echo '<a class="hover:text-white transition-colors flex items-center gap-2" href="' . htmlspecialchars($url) . '"' . $target . '>' . htmlspecialchars($item['label']) . '<span class="text-white/40">▾</span></a>';
                                echo '<div class="absolute left-0 top-full z-50 min-w-[220px] rounded-xl border border-white/10 bg-[#0a0f1a]/95 backdrop-blur hidden group-hover:block transition-all">';
                                echo '<div class="py-3">';
                                foreach ($childItems as $child) {
                                    [$childUrl, $childExternal] = $buildUrl($child['url']);
                                    $childTarget = $childExternal ? ' target="_blank"' : '';
                                    echo '<a class="block px-4 py-2 text-xs uppercase tracking-[0.2em] text-white/70 hover:text-white hover:bg-white/5 transition-colors" href="' . htmlspecialchars($childUrl) . '"' . $childTarget . '>' . htmlspecialchars(strtoupper($child['label'])) . '</a>';
                                }
                                echo '</div></div></div>';
                            }
                        }
                    } catch (Exception $e) {
                        // Fallback to hardcoded menu
                        echo '<a class="hover:text-white transition-colors" href="' . $appUrl . '/service.php">Services</a>';
                        echo '<a class="hover:text-white transition-colors" href="#methodology">Methodology</a>';
                        echo '<a class="hover:text-white transition-colors" href="#work">Work</a>';
                    }
                ?>
                <a class="px-4 lg:px-6 py-2 border border-white/20 hover:border-white transition-colors" href="<?= htmlspecialchars($navCtaHref) ?>"<?= $navCtaTarget ?>><?= htmlspecialchars($navCtaLabel) ?></a>
            </div>
            <button id="mobileMenuBtn" class="md:hidden text-white/80 text-sm uppercase tracking-[0.2em] font-bold">Menu</button>
        </div>
        <div id="mobileMenu" class="md:hidden hidden border-t border-white/10 bg-[#000000] mix-blend-normal opacity-100 backdrop-blur-none">
            <div class="max-w-[1440px] mx-auto px-6 py-6 flex flex-col gap-4 text-xs uppercase tracking-[0.2em] font-bold text-white">
                <?php 
                    try {
                        if (empty($parents)) {
                            throw new Exception('No nav items');
                        }
                        foreach ($parents as $item) {
                            $childItems = $children[$item['id']] ?? [];
                            [$url, $isExternal] = $buildUrl($item['url']);
                            $target = $isExternal ? ' target="_blank"' : '';
                            $hasChildren = !empty($childItems);
                            $parentId = 'mobile-submenu-' . $item['id'];
                            if ($hasChildren) {
                                echo '<div class="flex flex-col">';
                                echo '<button type="button" class="flex items-center justify-between hover:text-white transition-colors w-full submenu-toggle" data-target="' . $parentId . '">';
                                echo '<span>' . htmlspecialchars($item['label']) . '</span>';
                                echo '<span class="ml-2">▸</span>';
                                echo '</button>';
                                echo '<div id="' . $parentId . '" class="pl-6 py-2 flex flex-col gap-4 border-l border-white/10 mt-2 mb-2 bg-[#0a0f1a]/80 rounded-lg shadow-lg hidden">';
                                foreach ($childItems as $child) {
                                    [$childUrl, $childExternal] = $buildUrl($child['url']);
                                    $childTarget = $childExternal ? ' target="_blank"' : '';
                                    echo '<a class="text-xs text-white uppercase hover:text-white transition-colors" href="' . htmlspecialchars($childUrl) . '"' . $childTarget . '>' . htmlspecialchars(strtoupper($child['label'])) . '</a>';
                                }
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<a class="hover:text-white transition-colors text-xs uppercase" href="' . htmlspecialchars($url) . '"' . $target . '>' . htmlspecialchars(strtoupper($item['label'])) . '</a>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<a class="hover:text-white transition-colors" href="' . $appUrl . '/service.php">Services</a>';
                        echo '<a class="hover:text-white transition-colors" href="#methodology">Methodology</a>';
                        echo '<a class="hover:text-white transition-colors" href="#work">Work</a>';
                    }
                ?>
                <a class="px-4 py-2 border border-white/20 hover:border-white transition-colors inline-flex w-fit text-xs uppercase" href="<?= htmlspecialchars($navCtaHref) ?>"<?= $navCtaTarget ?>><?= htmlspecialchars(strtoupper($navCtaLabel)) ?></a>
            </div>
        </div>
    </nav>
    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        // Mobile submenu toggle
        document.querySelectorAll('.submenu-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const submenu = document.getElementById(targetId);
                if (submenu) {
                    submenu.classList.toggle('hidden');
                }
            });
        });
    </script>
