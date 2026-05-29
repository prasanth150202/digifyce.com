<?php include __DIR__ . '/app/views/header.php'; ?>
<?php
require_once __DIR__ . '/db.php';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
$hero = $mysqli->query("SELECT * FROM hero_section WHERE id = 1 LIMIT 1");
$hero = $hero ? $hero->fetch_assoc() : [
    'headline' => 'TRANSFORM <br/><span class="text-white/20">DIGITAL PRESENCE</span> <br/>INTO REVENUE.',
    'subtext' => 'We scale high-growth brands through hyper-precision data and minimalist strategy. No noise, just performance.',
    'cta_label' => 'Get Free Audit',
    'cta_url' => '#'
];
$metrics = $mysqli->query("SELECT * FROM metrics ORDER BY id ASC");
$metrics = $metrics ? $metrics->fetch_all(MYSQLI_ASSOC) : [];
$homeCtaLabel = 'Book Your Audit';
$homeCtaUrl = '#';
$homeCtaNote = 'Only 3 slots remaining for Q4';
$ctaSettings = $mysqli->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('home_cta_label','home_cta_url','home_cta_note')");
if ($ctaSettings) {
    while ($row = $ctaSettings->fetch_assoc()) {
        if ($row['setting_key'] === 'home_cta_label') {
            $homeCtaLabel = $row['setting_value'] ?: $homeCtaLabel;
        } elseif ($row['setting_key'] === 'home_cta_url') {
            $homeCtaUrl = $row['setting_value'] ?: $homeCtaUrl;
        } elseif ($row['setting_key'] === 'home_cta_note') {
            $homeCtaNote = $row['setting_value'] ?: $homeCtaNote;
        }
    }
}

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
<section class="relative min-h-screen flex items-center pt-12 sm:pt-16 px-4 sm:px-6 lg:px-8 overflow-hidden" style="background: transparent;">
    <div class="absolute inset-0 w-full h-full -z-20 pointer-events-none select-none">
        <img src="public/assets/img/map.png" alt="Map Background" class="w-full h-full object-cover opacity-90" style="object-position:center;" loading="eager">
    </div>
    <div class="absolute inset-0 w-full h-full -z-10 pointer-events-none select-none" style="background: linear-gradient(120deg, rgba(3,5,8,0.92) 0%, rgba(3,5,8,0.7) 60%, rgba(3,5,8,0.92) 100%);"></div>
    <div class="max-w-[1440px] mx-auto w-full relative z-10">
        <div class="grid items-center">
            <div>
                <div class="inline-block mb-8 sm:mb-12 text-medium font-bold tracking-[0.3em] uppercase text-[var(--electric-blue)]" style="text-size:15px;">
                    Our Vision
                </div>
     <h1 class="text-5xl sm:text-5xl md:text-6xl lg:text-8xl font-bold mb-12 sm:mb-16">
                    <?= $hero['headline'] ?>
                </h1>
                <div class="flex flex-col md:flex-row gap-8 sm:gap-12 items-start md:items-end">
                    <a href="<?= htmlspecialchars($hero['cta_url']) ?>">
                    <button class="group relative px-8 sm:px-12 py-4 sm:py-6 bg-[var(--electric-blue)] text-white font-bold text-base sm:text-lg overflow-hidden transition-all hover:pr-12 sm:hover:pr-16 w-full md:w-auto">
                        <span class="relative z-10 uppercase tracking-widest text-sm"><?= htmlspecialchars($hero['cta_label']) ?></span>
                        <span class="material-symbols-outlined absolute right-6 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all">arrow_forward</span>
                    </button>
                    </a>
                    <p class="max-w-md text-slate-500 text-base sm:text-lg leading-relaxed md:border-l border-white/10 md:pl-8">
                        <?= nl2br(htmlspecialchars($hero['subtext'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="metrics-section" class="py-4 sm: bg-[#030508]">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8 sm:gap-12 items-end text-center">

            <!-- 2025 Revenue -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-4xl font-light text-slate-400 mb-2">
                    <span class="counter" data-target="12M">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-500">
                    Revenue — FY2024
                </div>
            </div>

            <!-- 2026 Revenue -->
            <div class="flex flex-col justify-end items-center">
                <div class="text-green-400 text-xs mb-2">
                    ↑ +148M vs last year
                </div>

                <div class="text-4xl sm:text-6xl font-semibold text-white mb-2">
                    <span class="counter" data-target="160M">0</span>
                </div>

                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400">
                    Revenue — FY2025
                </div>
            </div>

            <!-- Growth -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-5xl font-light text-white mb-2">
                    <span class="counter" data-target="13.3X">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    Growth
                </div>
            </div>

            <!-- Retention -->
            <div class="flex flex-col justify-end">
                <div class="text-3xl sm:text-5xl font-light text-white mb-2">
                    <span class="counter" data-target="82%">0</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
                    Retention Rate
                </div>
            </div>

            <!-- Brands Served -->
            <!-- Brands Served -->
<div class="flex flex-col justify-end col-span-2 lg:col-span-1 justify-self-center">
    <div class="text-3xl sm:text-5xl font-light text-white mb-2">
        <span class="counter" data-target="120+">0</span>
    </div>
    <div class="text-[10px] uppercase tracking-[0.2em] text-slate-600">
        Brands Served
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
<!-- We Got Recognized In Section -->
<div class="mb-8 sm:mb-10 lg:mb-12">
<div class="mb-6 sm:mb-8 lg:mb-12 text-center">
<h2 class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">Press & Recognition</h2>
<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">We Got Recognized In</div>
</div>
<div class="relative overflow-hidden bg-white/[0.02] rounded-lg border border-white/10 p-4 sm:p-6 lg:p-8">
<div class="flex gap-4 sm:gap-6 lg:gap-8 xl:gap-12 items-center justify-center flex-wrap">
<?php
$recognitionLogos = [];
try {
    $recognitionPath = __DIR__ . '/public/assets/toolslogo/ads/';
    if (is_dir($recognitionPath)) {
        $files = array_filter(scandir($recognitionPath), function($file) use ($recognitionPath) {
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
} catch (Exception $e) {}

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

<!-- Tools We Use Section with Animated Sliders -->
<div class="py-4  border-t border-white/10">
<div class="mb-6 sm:mb-8 lg:mb-12 text-center">
<h2 class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">Our Stack</h2>
<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Tools We Use</div>
</div>

<style>
@keyframes scrollRight {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes scrollLeft {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}
.scroll-right { animation: scrollRight 40s linear infinite; }
.scroll-left { animation: scrollLeft 40s linear infinite; }
.logo-filtered { filter: brightness(1.08) invert(0.97) saturate(0); transition: all 0.4s ease; }
.group:hover .logo-filtered { filter: none; }
@media (max-width: 640px) {
    .scroll-right { animation: scrollRight 30s linear infinite; }
    .scroll-left { animation: scrollLeft 30s linear infinite; }
}
</style>

<div class="space-y-4 sm:space-y-6">
<?php
$toolLogos = [];
try {
    $toolPath = __DIR__ . '/public/assets/toolslogo/';
    if (is_dir($toolPath)) {
        $files = array_filter(scandir($toolPath), function($file) use ($toolPath) {
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
} catch (Exception $e) {}

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
while (count($row1) < 6) $row1[] = ['name' => 'Tool ' . (count($row1) + 1), 'path' => null];
while (count($row2) < 6) $row2[] = ['name' => 'Tool ' . (count($row2) + 7), 'path' => null];
while (count($row3) < 6) $row3[] = ['name' => 'Tool ' . (count($row3) + 13), 'path' => null];

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
<!-- Clients Slider Section -->
<section class="cdf-section py-4 bg-[#030508]">
    <div class="py-12 sm:py-16 lg:py-20 border-t border-white/10">
<div class="mb-6 sm:mb-8 lg:mb-12 text-center">
<h2 class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">Our Clients</h2>
<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Trusted by Industry Leaders</div>
</div>
<div class="max-w-[1400px] mx-auto px-4">

<style>
@keyframes cdfScrollLeft {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes cdfScrollRight {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}

.cdf-scroll-left {
    animation: cdfScrollLeft 28s linear infinite;
}

.cdf-scroll-right {
    animation: cdfScrollRight 30s linear infinite;
}


.cdf-client-logos {
    overflow: hidden;
    position: relative;
}


/* Client logo: white bg, circle, no shadow, bigger */
.cdf-client-img {
    filter: none !important;
    background: white;
    border-radius: 9999px;
    border: 2px solid #fff;
    width: 90px;
    height: 90px;
    object-fit: contain;
    padding: 10px;
    box-sizing: border-box;
    display: block;
    margin: 0 auto;
}

.cdf-service-item w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.75rem)] {
    display:flex;
    align-items:center;
    gap:10px;
    border:1px solid rgba(255,255,255,0.08);
    padding:12px;
    border-radius:8px;
    cursor:pointer;
    transition:.25s ease;
}
.cdf-service-item w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.75rem)]:hover {
    border-color:var(--electric-blue);
    background:rgba(255,255,255,0.03);
}
</style>

<div class="grid lg:grid-cols-2 gap-12 items-start">



<!-- LEFT : CLIENT LOGOS (2-PAGE SLIDER) -->
<div class="cdf-client-logos py-3">
    <div class="relative">
        <button id="clientPrev" type="button" aria-label="Previous" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center shadow transition-all" style="display:none;">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button id="clientNext" type="button" aria-label="Next" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center shadow transition-all">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
        <div id="clientSliderPages">
            <?php
            $clientLogos = [];
            $stmt = $mysqli->prepare("SELECT name, logo_url FROM trusted_brands ORDER BY position ASC");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                    if (!empty($row['logo_url'])) {
                            $clientLogos[] = $row;
                    }
            }
            $stmt->close();
            $logosPerPage = 12; // 3 rows x 4 columns
            $totalPages = max(1, ceil(count($clientLogos) / $logosPerPage));
            if (empty($clientLogos)) {
                echo '<div class="client-slider-page grid grid-cols-4 grid-rows-3 gap-2 items-center justify-center" style="min-height: 270px;">';
                for ($i = 1; $i <= 12; $i++) {
                    echo '<div class="flex items-center justify-center p-1"><div class="w-[90px] h-[90px] flex items-center justify-center bg-white border border-white rounded-full"><span class="text-slate-400 text-xs">Logo ' . $i . '</span></div></div>';
                }
                echo '</div>';
            } else {
                for ($p = 0; $p < $totalPages; $p++) {
                    echo '<div class="client-slider-page grid grid-cols-4 grid-rows-3 gap-2 items-center justify-center" style="display:' . ($p === 0 ? 'grid' : 'none') . '; min-height: 270px;">';
                    for ($i = $p * $logosPerPage; $i < min(($p + 1) * $logosPerPage, count($clientLogos)); $i++) {
                        $client = $clientLogos[$i];
                        $logoPath = $appUrl.'/'.ltrim($client['logo_url'],'/');
                        echo '<div class="flex items-center justify-center p-1"><div class="w-[90px] h-[90px] flex items-center justify-center bg-white border border-white rounded-full"><img src="'.htmlspecialchars($logoPath).'" class="cdf-client-img" loading="lazy" alt="'.htmlspecialchars($client['name']).'"></div></div>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>
        <div class="flex justify-center mt-4 gap-2" id="clientSliderDots">
            <?php
            for ($d = 0; $d < $totalPages; $d++) {
                echo '<button type="button" class="client-slider-dot w-2 h-2 rounded-full bg-white/20" data-page="'.$d.'"></button>';
            }
            ?>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pages = document.querySelectorAll('.client-slider-page');
    const prevBtn = document.getElementById('clientPrev');
    const nextBtn = document.getElementById('clientNext');
    const dots = document.querySelectorAll('.client-slider-dot');
    let currentPage = 0;
    let autoSlideInterval = null;

    function showPage(idx) {
        pages.forEach((p, i) => p.style.display = i === idx ? 'grid' : 'none');
        dots.forEach((d, i) => d.classList.toggle('bg-[var(--electric-blue)]', i === idx));
        prevBtn.style.display = idx === 0 ? 'none' : '';
        nextBtn.style.display = idx === pages.length - 1 ? 'none' : '';
    }

    function nextPage() {
        if (currentPage < pages.length - 1) {
            currentPage++;
        } else {
            currentPage = 0;
        }
        showPage(currentPage);
    }

    function prevPage() {
        if (currentPage > 0) {
            currentPage--;
        } else {
            currentPage = pages.length - 1;
        }
        showPage(currentPage);
    }

    function startAutoSlide() {
        if (autoSlideInterval) clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextPage, 3000);
    }

    if (pages.length > 1) {
        prevBtn.addEventListener('click', () => { prevPage(); startAutoSlide(); });
        nextBtn.addEventListener('click', () => { nextPage(); startAutoSlide(); });
        dots.forEach((d, i) => d.addEventListener('click', () => { currentPage = i; showPage(currentPage); startAutoSlide(); }));
        startAutoSlide();
    } else {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        document.getElementById('clientSliderDots').style.display = 'none';
    }
    showPage(currentPage);
});
</script>

<!-- RIGHT : SERVICES -->
 <form action="leadform.php" method="GET">
<div class="bg-white/5 border border-white/10 rounded-xl p-4">

<h3 class="text-2xl mb-2 font-bold text-white text-center">Select the service you're interested in</h3>

<style>
    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        border: 1.5px solid #1e293b;
        border-radius: 12px;
        padding: 1.25rem 0.5rem;
        background: rgba(255,255,255,0.02);
        margin-bottom: 1rem;
    }
    .cdf-service-item {
        border: 1.5px solid #334155;
        border-radius: 8px;
        background: rgba(255,255,255,0.04);
        padding: 0.6rem 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
        font-size: 13px ;
        width: 100%;
        min-width: 0;
        justify-content: flex-start;
        box-sizing: border-box;
    }
    .cdf-service-item:hover, .cdf-service-item:focus-within {
        border-color: var(--electric-blue, #0066ff);
        background: rgba(0,102,255,0.07);
    }
    .cdf-service-item input[type="checkbox"] {
        accent-color: var(--electric-blue, #0066ff);
        width: 1.1em;
        height: 1.1em;
        margin-right: 0.3em;
    }
    @media (max-width: 640px) {
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            padding: 1rem 0.25rem;
        }
        .cdf-service-item {
            font-size: 11px;
            padding: 0.5rem 0.3rem;
            min-width: 0;
            width: 100%;
            gap: 0.3rem;
        }
    }
</style>
<div class="services-grid">
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Digital Marketing"> Digital Marketing
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Social Media"> Social Media
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="SEO/AEO/GEO"> SEO/AEO/GEO
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="ECom Development"> ECom Development
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Web Development"> Web Development
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="CRM"> CRM
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Chatbots"> Chatbots
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Lead Generation"> Lead Generation
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="D2C Sale"> D2C Sale
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="AI Automation"> AI Automation
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Branding"> Branding
    </label>
    <label class="cdf-service-item">
        <input type="checkbox" class="cdf-service-checkbox" name="services[]" value="Content Development"> Content Development
    </label>
</div>
<div style="display: flex; justify-content: flex-end; width: 100%;">
    <button id="cdfSubmitBtn" type="submit" disabled
            class="px-3 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2
            bg-gray-600 text-gray-300 cursor-not-allowed">
                    Let’s Go
                    <span class="material-symbols-outlined text-lg align-middle">arrow_forward</span>
    </button>
</div>
</div>
</form>
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
        cdfSubmitBtn.classList.remove('bg-gray-600','text-gray-300','cursor-not-allowed');
        cdfSubmitBtn.classList.add('bg-[#0066ff]','text-white','cursor-pointer','hover:opacity-90');
    } else {
        cdfSubmitBtn.disabled = true;
        cdfSubmitBtn.classList.add('bg-gray-600','text-gray-300','cursor-not-allowed');
        cdfSubmitBtn.classList.remove('bg-[#0066ff]','text-white','cursor-pointer','hover:opacity-90');
    }
}

cdfCheckboxes.forEach(cb => {
    cb.addEventListener('change', cdfCheckServices);
});

</script>


<section class="py-4 sm:py-12 lg:py-16 bg-[var(--navy-black)]" id="services">

<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
<div class="mb-6 sm:mb-8">
<h2 class="text-xs uppercase tracking-[0.4em] text-slate-500 mb-6 sm:mb-8">Our Ecosystem</h2>
<div class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter">Core Services</div>
</div>
<div class="flex flex-col" id="services-list">

  <style>
    .reveal-content {
      height: 0;
      overflow: hidden;
      opacity: 0;
      transition: height 0.45s ease, opacity 0.35s ease;
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
  </style>

  <!-- 01 -->
  <!-- 01 -->
<div class="big-list-item py-4 sm:py-6 cursor-pointer group service-item border-b border-white/5">
  <div class="flex items-center justify-between gap-4 mb-4">
   <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
  01 / Branding & Go-To-Market
</div>

    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
      arrow_outward
    </span>
  </div>

  <div class="reveal-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
     <p class="text-lg sm:text-xl text-slate-400">
  Brand foundation and launch strategy covering positioning, identity, messaging, and market entry planning for scalable growth.
</p>

      <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Brand Positioning</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Identity Design</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Brand Guidelines</span>
                <a href="leadform.php" class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center" style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;">Get Branding Proposal</a>
      </div>
    </div>
  </div>
</div>

<!-- 02 -->
<div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
  <div class="flex items-center justify-between gap-4 mb-4">
    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
  02 / Commercials & Content Production
</div>
    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
      arrow_outward
    </span>
  </div>

  <div class="reveal-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
      <p class="text-lg sm:text-xl text-slate-400">
  Product and campaign-focused photo and video production designed for performance creatives, ads, and marketplace content.
</p>

      <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Paid Media</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Go-To-Market Strategy</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Creative Testing</span>
                <a href="leadform.php" class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center" style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;">Request Shoot Details</a>
      </div>
    </div>
  </div>
</div>

<!-- 03 -->
<div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
  <div class="flex items-center justify-between gap-4 mb-4">
    <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
  03 / Technology Development
</div>

    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
      arrow_outward
    </span>
  </div>

  <div class="reveal-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
     <p class="text-lg sm:text-xl text-slate-400">
  Custom technology solutions including eCommerce development, integrations, automation systems, and scalable digital infrastructure.
</p>

      <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Listing Optimization</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Marketplace Ads</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Catalog Management</span>
                <a href="leadform.php" class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center" style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;">Enquire Tech Solutions</a>
      </div>
    </div>
  </div>
</div>

<!-- 04 -->
<div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
  <div class="flex items-center justify-between gap-4 mb-4">
   <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
  04 / Performance Marketing
</div>

    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
      arrow_outward
    </span>
  </div>

  <div class="reveal-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
      <p class="text-lg sm:text-xl text-slate-400">
  Data-driven acquisition through paid media, creative testing, funnel optimization, and performance scaling strategies.
</p>

      <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Email & WhatsApp Flows</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Customer Lifecycle</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">LTV Optimization</span>
                <a href="leadform.php" class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center" style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;">Get Performance Plan</a>
      </div>
    </div>
  </div>
</div>
<!-- 05 -->
<div class="big-list-item py-4 sm:py-12 cursor-pointer group service-item border-b border-white/5">
  <div class="flex items-center justify-between gap-4 mb-4">
   <div class="title-text text-xl sm:text-3xl lg:text-5xl font-bold text-white/40 transition-colors">
   05 / Retention & Lifecycle
</div>

    <span class="material-symbols-outlined arrow-icon text-2xl sm:text-4xl text-white/10">
      arrow_outward
    </span>
  </div>

  <div class="reveal-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 pt-4">
      <p class="text-lg sm:text-xl text-slate-400">
  Retention-focused growth using CRM automation, lifecycle communication, and repeat purchase optimization.
</p>

      <div class="flex flex-wrap gap-3 sm:gap-4 justify-end">
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Email & WhatsApp Flows</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">Customer Lifecycle</span>
        <span class="px-4 py-1  text-[10px] uppercase tracking-widest rounded-full text-white">LTV Optimization</span>
                <a href="leadform.php" class="ml-2 mt-2 inline-block bg-[var(--electric-blue)] text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-600 transition-all text-center" style="height:44px; line-height:40px; min-width:180px; display:flex; align-items:center; justify-content:center;">Boost Retention Now</a>
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
<div class="mb-6 sm:mb-8 lg:mb-12 text-center">
<h2 class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">Brand Studio</h2>
<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Creative Story telling</div>
</div>
<section class="brand-story-scroll-container relative" id="brand-stories">

<div class="brand-story-sticky">
<div class="brand-story-item visible" id="story-apparel">
<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
<source src="public/assets/videos/Lushra.mp4" type="video/mp4"/>
</video>
<div class="brand-video-overlay"></div>
<div class="absolute bottom-12 left-12">
<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
<div class="text-3xl font-bold tracking-tighter text-white">Lushra</div>
</div>
</div>
<div class="brand-story-item" id="story-jewelry">
<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
<source src="public/assets/videos/blackape.mp4" type="video/mp4"/>
</video>
<div class="brand-video-overlay"></div>
<div class="absolute bottom-12 left-12">
<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
<div class="text-3xl font-bold tracking-tighter text-white">Black Ape</div>
</div>
</div>
<div class="brand-story-item" id="story-wellness">
<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
<source src="public/assets/videos/elther.mp4" type="video/mp4"/>
</video>
<div class="brand-video-overlay"></div>
<div class="absolute bottom-12 left-12">
<span class="text-xs font-bold tracking-[0.5em] text-white/40 mb-2 block"></span>
<div class="text-3xl font-bold tracking-tighter text-white">Elther</div>
</div>
</div>
<div class="brand-story-item" id="story-aishwaryam">
<video autoplay="" class="w-full h-full object-cover" loop="" muted="" playsinline="">
<source src="public/assets/videos/Sriaishwaryam.mp4" type="video/mp4"/>
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
</section>
<section class="py-16 sm:py-20 lg:py-24 bg-[#030508]" id="methodology">
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-12 sm:gap-16 lg:gap-20">
<div class="lg:w-1/3 lg:sticky lg:top-32 h-fit">
<h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-6 sm:mb-8">Process</h2>
<div class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter mb-6 sm:mb-8 leading-tight">Our 5-Step <br/>Architecture.</div>
<p class="text-slate-500 max-w-xs text-sm sm:text-base">We follow a rigorous, non-linear path to exponential growth.</p>
</div>
<div class="lg:w-2/3 space-y-12 sm:space-y-16 lg:space-y-20 pl-0 lg:pl-20 relative overflow-hidden lg:overflow-visible">
<div class="absolute left-0 top-0 bottom-0 vertical-line opacity-20 hidden lg:block"></div>
<div class="relative methodology-step">
<div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
<div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">01. DISCOVERY</div>
<div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Uncovering Hidden Data</div>
<p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">We strip away vanity metrics to find the core levers of your business profitability and customer acquisition costs.</p>
</div>
<div class="relative methodology-step">
<div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
<div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">02. STRATEGY</div>
<div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">The Growth Roadmap</div>
<p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">A bespoke, 90-day execution plan focused on quick wins and long-term ecosystem stability.</p>
</div>
<div class="relative methodology-step">
<div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
<div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">03. EXECUTION</div>
<div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Agile Implementation</div>
<p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">High-velocity testing cycles across all paid channels and digital touchpoints.</p>
</div>
<div class="relative methodology-step">
<div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
<div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">04. OPTIMIZATION</div>
<div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Iterative Precision</div>
<p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">We don't set and forget. Every dollar is tracked, analyzed, and re-allocated for maximum efficiency.</p>
</div>
<div class="relative methodology-step">
<div class="absolute -left-[84px] top-0 w-2 h-2 bg-[var(--electric-blue)] hidden lg:block"></div>
<div class="text-sm font-bold text-slate-700 mb-3 sm:mb-4 tracking-[0.3em]">05. REPORTING</div>
<div class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Full Transparency</div>
<p class="text-slate-400 max-w-xl text-base sm:text-lg leading-relaxed">Live dashboards that show you exactly how much revenue every cent of ad spend is generating.</p>
</div>
</div>
</div>
</section>


<section class="py-16 sm:py-20 lg:py-24 bg-[var(--navy-black)] relative overflow-hidden" id="strategy-matrix">
<div class="mb-6 sm:mb-8 lg:mb-12 text-center">
<h2 class="text-[10px] sm:text-xs uppercase tracking-[0.3em] sm:tracking-[0.4em] text-slate-500 mb-4 sm:mb-6 lg:mb-8">Methodology</h2>
<div class="text-2xl sm:text-3xl lg:text-4xl xl:text-6xl font-bold tracking-tighter px-4">Our Core Methodology</div>
</div>

<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
  
<div class="grid lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">
<div class="relative group order-2 lg:order-1">
<div class="absolute -top-8 sm:-top-12 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex flex-col items-center">
<span class="mb-2">Motivation</span>
<span class="material-symbols-outlined text-xs">arrow_upward</span>
</div>
<div class="absolute top-1/2 -right-8 sm:-right-16 -translate-y-1/2 text-[10px] uppercase tracking-[0.3em] text-slate-500 flex items-center gap-2 rotate-90 origin-center">
<span class="hidden sm:inline">Purchase Difficulty</span>
<span class="sm:hidden">Difficulty</span>
<span class="material-symbols-outlined text-xs">arrow_forward</span>
</div>
<div class="grid grid-cols-2 aspect-square w-full max-w-[600px] mx-auto border border-white/5 relative bg-[#0a0f1a]/40">
<div class="absolute inset-0 flex items-center justify-center pointer-events-none">
<div class="w-full h-px bg-white/10"></div>
<div class="h-full w-px bg-white/10"></div>
</div>
<div class="absolute w-6 h-6 sm:w-8 sm:h-8 matrix-orb z-20 pointer-events-none pos-a" id="orb">
<div class="w-full h-full rounded-full bg-[var(--electric-blue)] shadow-[0_0_20px_rgba(0,102,255,0.8)] flex items-center justify-center">
<div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full animate-ping"></div>
</div>
</div>
<div class="matrix-quadrant active flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-a" onclick="setMatrix('a')">
<span class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT A</span>
<div class="mt-auto">
<div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Impulse Zone</div>
<div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">High CTR / High Conv.</div>
</div>
</div>
<div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-b" onclick="setMatrix('b')">
<span class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT B</span>
<div class="mt-auto">
<div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">High Intent</div>
<div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">Low CTR / High Conv.</div>
</div>
</div>
<div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-d" onclick="setMatrix('d')">
<span class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT D</span>
<div class="mt-auto">
<div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Click Magnet</div>
<div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">High CTR / Low Conv.</div>
</div>
</div>
<div class="matrix-quadrant flex flex-col p-4 sm:p-6 lg:p-8 cursor-pointer group/q" id="quad-c" onclick="setMatrix('c')">
<span class="text-[10px] sm:text-xs font-bold text-white/30 group-hover/q:text-[var(--electric-blue)] transition-colors">QUADRANT C</span>
<div class="mt-auto">
<div class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1">Dead Space</div>
<div class="text-[9px] sm:text-[10px] uppercase tracking-widest text-slate-600">Low CTR / Low Conv.</div>
</div>
</div>
</div>
<div class="flex justify-between mt-4 px-2 text-[10px] uppercase tracking-[0.2em] text-slate-600">
<span>Difficult</span>
<span>Easy</span>
</div>
<div class="absolute -left-8 sm:-left-12 top-0 h-full flex flex-col justify-between py-2 text-[10px] uppercase tracking-[0.2em] text-slate-600 [writing-mode:vertical-lr] rotate-180">
<span class="hidden sm:inline">High Motivation</span>
<span class="hidden sm:inline">Low Motivation</span>
<span class="sm:hidden">High</span>
<span class="sm:hidden">Low</span>
</div>
</div>
<div class="glass-panel p-6 sm:p-8 lg:p-12 relative overflow-hidden order-1 lg:order-2">
<div class="absolute top-0 right-0 w-32 h-32 bg-[var(--electric-blue)]/10 blur-3xl -mr-16 -mt-16"></div>
<h2 class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-8 sm:mb-12">Dynamic Insights</h2>
<div class="space-y-8 sm:space-y-12" id="insight-content">
<div>
<div class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">DIAGNOSIS</div>
<h3 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4" id="diagnosis-title">Maximum Efficiency Zone</h3>
<p class="text-slate-400 text-base sm:text-lg leading-relaxed" id="diagnosis-text">Your creative resonance is perfectly aligned with user intent. Every dollar spent is currently at peak velocity.</p>
</div>
<div>
<div class="text-sm font-bold text-slate-500 mb-3 sm:mb-4 tracking-[0.3em]">OPTIMIZATION STRATEGY</div>
<ul class="space-y-3 sm:space-y-4" id="strategy-list">
<li class="flex items-start gap-3 sm:gap-4">
<span class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
<span class="text-slate-300 text-sm sm:text-base">Increase budget horizontally across lookalike segments.</span>
</li>
<li class="flex items-start gap-3 sm:gap-4">
<span class="material-symbols-outlined text-[var(--electric-blue)] text-sm mt-1 flex-shrink-0">check_circle</span>
<span class="text-slate-300 text-sm sm:text-base">Test iterative variations of winning 'hooks' only.</span>
</li>
</ul>
</div>
</div>
<div class="mt-8 sm:mt-12 pt-8 sm:pt-12 border-t border-white/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
<div class="flex items-center gap-2 text-[10px] uppercase tracking-widest text-white/20" id="auto-play-indicator">
<div class="w-1.5 h-1.5 rounded-full bg-[var(--electric-blue)] animate-pulse"></div>
                        Auto-Touring Matrix
                    </div>
<button class="text-[10px] uppercase tracking-widest text-slate-500 hover:text-white transition-colors flex items-center gap-2" onclick="toggleAutoTour()">
<span class="material-symbols-outlined text-xs" id="play-pause-icon">pause</span>
<span id="play-pause-text">Pause Tour</span>
</button>
</div>
<div class="mt-6 text-center">
<p class="text-slate-500 text-sm mb-4">Enter your email to get detailed PDF</p>
<form id="pdfEmailForm" class="flex flex-col sm:flex-row gap-3 max-w-sm mx-auto">
<input type="email" name="email" placeholder="Enter your email" required class="flex-1 px-4 py-3 bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:border-[var(--electric-blue)] transition-colors text-sm">
<button type="submit" class="bg-[var(--electric-blue)] text-white px-6 py-3 font-bold uppercase tracking-wider hover:bg-blue-600 transition-colors text-sm">Get PDF</button>
</form>
<div id="pdfEmailMessage" class="mt-3 text-sm hidden"></div>
</div>
</div>
</div>
</div>
</section>


<section class="py-16 sm:py-20 lg:py-24 bg-[var(--navy-black)] overflow-hidden" id="work">
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">
<div class="order-2 lg:order-1">
<h2 class="text-xs uppercase tracking-[0.4em] text-slate-500 mb-6 sm:mb-8">Selected Case</h2>
<div class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter mb-6 sm:mb-8">Scaling Wellness to 3.5X ROAS.</div>
<p class="text-lg sm:text-xl text-slate-400 leading-relaxed mb-6 sm:mb-8">
                    A total overhaul of digital infrastructure. From fragmented tracking to a unified data ecosystem that fueled aggressive scaling.
                </p>
<div class="flex flex-wrap gap-12 sm:gap-20">
<div>
<div class="text-3xl sm:text-4xl font-bold text-white mb-2">+340%</div>
<div class="text-[10px] uppercase tracking-widest text-slate-600">Revenue</div>
</div>
<div>
<div class="text-3xl sm:text-4xl font-bold text-white mb-2">-38%</div>
<div class="text-[10px] uppercase tracking-widest text-slate-600">CPA Reduction</div>
</div>
</div>
</div>
<div class="relative group order-1 lg:order-2">
<div class="absolute inset-0 bg-[var(--electric-blue)]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
<div class=" bg-white/5 overflow-hidden">
<img alt="Data Visualization" class="" src="public/assets/img/graph.png"/>
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

<section class="py-20 sm:py-24 lg:py-32 bg-[var(--navy-black)] relative">
<div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-20 sm:h-32 vertical-line opacity-30"></div>
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
<h2 class="text-huge font-bold mb-8 sm:mb-12 text-white">READY TO <br/><span class="text-white/20">SCALE?</span></h2>
<div class="flex flex-col items-center gap-6 sm:gap-8">
<a href="<?= htmlspecialchars($homeCtaHref) ?>"<?= $homeCtaTarget ?> class="bg-white text-[var(--navy-black)] px-10 sm:px-16 py-6 sm:py-4 font-bold text-lg sm:text-xl uppercase tracking-widest hover:bg-[var(--electric-blue)] hover:text-white transition-all duration-300 w-full sm:w-auto text-center">
                <?= htmlspecialchars($homeCtaLabel) ?>
            </a>
<div class="text-slate-600 uppercase tracking-[0.2em] text-xs"><?= htmlspecialchars($homeCtaNote) ?></div>
</div>
</div>
</section>

<script>
// PDF Email Form Handler
document.getElementById('pdfEmailForm').addEventListener('submit', async function(e) {
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
            title: "Maximum Efficiency Zone",
            diagnosis: "Your creative resonance is perfectly aligned with user intent. Every dollar spent is currently at peak velocity.",
            steps: ["Increase budget horizontally across lookalike segments.", "Test iterative variations of winning 'hooks' only."]
        },
        b: {
            title: "Trust Barrier Wall",
            diagnosis: "Users want the product but face Friction. High conversion rates suggest strong value, but low CTR means the ad 'hook' is weak.",
            steps: ["Revamp creative thumbnails and first 3 seconds.", "A/B test authority-based social proof in ad copy."]
        },
        c: {
            title: "Strategic Exit Point",
            diagnosis: "Market mismatch. Neither the message nor the offer is sticking. High friction and low desire leads to wasted spend.",
            steps: ["Pause all active sets immediately.", "Re-evaluate product-market fit and landing page UX."]
        },
        d: {
            title: "Engagement Trap",
            diagnosis: "Clickbait or high curiosity but low intent. People are clicking, but the landing page isn't closing the deal.",
            steps: ["Align ad creative closer to the actual offer.", "Implement post-click educational funnels to build intent."]
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
</body></html>