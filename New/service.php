<?php include __DIR__ . '/app/views/header.php'; ?>
<?php
require_once __DIR__ . '/config/database.php';
$pdo = Database::getInstance();
$serviceCtaHeading = 'Ready to Scale Your Digital Infrastructure?';
$serviceCtaText = "Request a technical audit. We'll analyze your stack, your CAC/LTV benchmarks, and your competitor's marketplace velocity.";
$serviceCtaPrimaryLabel = 'Request Audit';
$serviceCtaPrimaryUrl = '#';
$serviceCtaSecondaryLabel = 'View Methodology';
$serviceCtaSecondaryUrl = '#';

try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('service_cta_heading','service_cta_text','service_cta_primary_label','service_cta_primary_url','service_cta_secondary_label','service_cta_secondary_url')");
    $settings = $stmt ? $stmt->fetchAll() : [];
    foreach ($settings as $row) {
        if ($row['setting_key'] === 'service_cta_heading') {
            $serviceCtaHeading = $row['setting_value'] ?: $serviceCtaHeading;
        } elseif ($row['setting_key'] === 'service_cta_text') {
            $serviceCtaText = $row['setting_value'] ?: $serviceCtaText;
        } elseif ($row['setting_key'] === 'service_cta_primary_label') {
            $serviceCtaPrimaryLabel = $row['setting_value'] ?: $serviceCtaPrimaryLabel;
        } elseif ($row['setting_key'] === 'service_cta_primary_url') {
            $serviceCtaPrimaryUrl = $row['setting_value'] ?: $serviceCtaPrimaryUrl;
        } elseif ($row['setting_key'] === 'service_cta_secondary_label') {
            $serviceCtaSecondaryLabel = $row['setting_value'] ?: $serviceCtaSecondaryLabel;
        } elseif ($row['setting_key'] === 'service_cta_secondary_url') {
            $serviceCtaSecondaryUrl = $row['setting_value'] ?: $serviceCtaSecondaryUrl;
        }
    }
} catch (Exception $e) {
}

$serviceCtaPrimaryHref = trim($serviceCtaPrimaryUrl);
$serviceCtaPrimaryTarget = '';
if ($serviceCtaPrimaryHref !== '') {
    $isExternal = strpos($serviceCtaPrimaryHref, 'http') === 0 || strpos($serviceCtaPrimaryHref, '//') === 0;
    $isAnchor = strpos($serviceCtaPrimaryHref, '#') === 0;
    if ($isExternal) {
        $serviceCtaPrimaryTarget = ' target="_blank"';
    } elseif (!$isAnchor) {
        if (strpos($serviceCtaPrimaryHref, '/') === 0) {
            $serviceCtaPrimaryHref = $appUrl . $serviceCtaPrimaryHref;
        } else {
            $serviceCtaPrimaryHref = $appUrl . '/' . ltrim($serviceCtaPrimaryHref, '/');
        }
    }
} else {
    $serviceCtaPrimaryHref = '#';
}

$serviceCtaSecondaryHref = trim($serviceCtaSecondaryUrl);
$serviceCtaSecondaryTarget = '';
if ($serviceCtaSecondaryHref !== '') {
    $isExternal = strpos($serviceCtaSecondaryHref, 'http') === 0 || strpos($serviceCtaSecondaryHref, '//') === 0;
    $isAnchor = strpos($serviceCtaSecondaryHref, '#') === 0;
    if ($isExternal) {
        $serviceCtaSecondaryTarget = ' target="_blank"';
    } elseif (!$isAnchor) {
        if (strpos($serviceCtaSecondaryHref, '/') === 0) {
            $serviceCtaSecondaryHref = $appUrl . $serviceCtaSecondaryHref;
        } else {
            $serviceCtaSecondaryHref = $appUrl . '/' . ltrim($serviceCtaSecondaryHref, '/');
        }
    }
} else {
    $serviceCtaSecondaryHref = '#';
}
?>
<main>


<section class="pt-40 pb-20 px-6 lg:px-12 border-b border-white/5" style="background-color: var(--navy-black);">
<div class="max-w-[1600px] mx-auto">
<div class="flex flex-col lg:flex-row justify-between items-end gap-12">
<div class="max-w-3xl">
<span class="font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block" style="color: var(--electric-blue);">Strategic Engineering &amp; Performance Intelligence</span>
<h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">
                        The Architecture <br/>of High-Yield Growth
                    </h1>
<p class="text-white/40 text-xl leading-relaxed max-w-xl">
                        Deep-dive analysis of our core service ecosystems. Engineered for performance-focused stakeholders requiring granular data and comparative insight.
                    </p>
</div>
<div class="flex flex-col gap-4 text-right">
<div class="p-6 border border-white/5" style="background-color: rgba(255, 255, 255, 0.03);">
<p class="text-[10px] uppercase tracking-widest text-white/40 mb-2 font-bold">Aggregate Performance Lift</p>
<p class="text-5xl font-bold" style="color: var(--electric-blue);">+142% <span class="text-xs font-normal text-white/20 align-top uppercase">vs avg.</span></p>
</div>
</div>
</div>
</div>
</section>
<section class="px-6 lg:px-12 py-24" style="background-color: rgba(255, 255, 255, 0.02);">
<div class="max-w-[1600px] mx-auto space-y-40">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
<div class="lg:col-span-4 lg:sticky lg:top-32">
<div class="flex items-center gap-4 mb-6">
<span class="section-number text-3xl sm:text-4xl lg:text-5xl font-bold leading-none" style="color: rgba(0, 102, 255, 0.5); text-shadow: 0 0 20px rgba(0, 102, 255, 0.4), 0 0 40px rgba(0, 102, 255, 0.2); transition: all 0.6s ease;">01</span>
<h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">Website <br/>Ecosystems</h3>
</div>
<p class="text-white/50 mb-8 leading-relaxed">
                        Transitioning from legacy monolithic structures to <span class="text-white">Headless E-commerce</span>. We optimize for the technical metrics that drive transaction velocity.
                    </p>
<div class="flex flex-wrap gap-2">
<span class="metric-badge bg-white/5 text-white/60">Next.js</span>
<span class="metric-badge bg-white/5 text-white/60">Hydrogen</span>
<span class="metric-badge bg-white/5 text-white/60">Core Web Vitals</span>
</div>
</div>
<div class="lg:col-span-8 space-y-12 pl-12 border-l border-white/5">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="space-y-4">
<h4 class="text-[11px] uppercase tracking-widest font-bold" style="color: var(--electric-blue);">Infrastructure Metrics</h4>
<div class="space-y-4">
<div class="bg-[var(--navy-black)] p-4 border border-white/5">
<div class="flex justify-between items-end mb-2">
<span class="text-xs text-white/40">LCP (Largest Contentful Paint)</span>
<span class="text-lg font-bold text-green-400">0.8s</span>
</div>
<div class="w-full h-1 bg-white/5"><div class="w-[90%] h-full bg-green-400"></div></div>
</div>
<div class="bg-[var(--navy-black)] p-4 border border-white/5">
<div class="flex justify-between items-end mb-2">
<span class="text-xs text-white/40">TTFB (Time To First Byte)</span>
<span class="text-lg font-bold text-green-400">140ms</span>
</div>
<div class="w-full h-1 bg-white/5"><div class="w-[85%] h-full bg-green-400"></div></div>
</div>
</div>
</div>
<div class="space-y-4">
<h4 class="text-[11px] uppercase tracking-widest font-bold" style="color: var(--electric-blue);">Business Impact</h4>
<div class="space-y-4">
<div class="bg-[var(--navy-black)] p-4 border border-white/5">
<div class="flex justify-between items-end mb-2">
<span class="text-xs text-white/40">Conv. Rate Lift (Post-Migration)</span>
<span class="text-lg font-bold" style="color: var(--electric-blue);">+28.4%</span>
</div>
<div class="w-full h-1 bg-white/5"><div class="w-[70%] h-full" style="background-color: var(--electric-blue); box-shadow: 0 0 10px rgba(0, 102, 255, 0.5);"></div></div>
</div>
<div class="bg-[var(--navy-black)] p-4 border border-white/5">
<div class="flex justify-between items-end mb-2">
<span class="text-xs text-white/40">Avg. Page Load Reduction</span>
<span class="text-lg font-bold" style="color: var(--electric-blue);">-62%</span>
</div>
<div class="w-full h-1 bg-white/5"><div class="w-[82%] h-full" style="background-color: var(--electric-blue); box-shadow: 0 0 10px rgba(0, 102, 255, 0.5);"></div></div>
</div>
</div>
</div>
</div>
<div class="p-8 bg-white/[0.02] border border-white/10 relative overflow-hidden group">
<div class="absolute inset-0 grayscale opacity-20 pointer-events-none bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAsFjE5EphKE8sXsbSQ24bJmMPfI2KN2iTUYcHr1anS9YeP_0K4i0-aQbO_ZIygHRwJx7kDRTiBoXz4HMFwjEwGAVpdyOzSLLnTScrx_Kj7k7Vl3H5SMXF54ePa1Gz3Ay7-t0cI2ZAjhLgSXEy31f6gEblwe-fW67QQJyW4B3QidAizLdkq0jCgRY6TH2B30Cmz7Zk9ujjbXjHmtw0IcGwU9DzhdvpeNdzAVJGwOLGZuMtMqs1WbmXzLwA6ZJXdLUvjCotMsDjAqw')"></div>
<div class="relative z-10 flex flex-col md:flex-row gap-8 items-start">
<div class="flex-1">
<h5 class="text-xs uppercase tracking-widest font-bold text-white/40 mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">terminal</span> Analysis Snippet
                                </h5>
<h4 class="text-2xl font-bold mb-4 italic">"Solving the scale bottleneck."</h4>
<p class="text-white/60 text-sm leading-relaxed max-w-lg">
                                    Implemented a headless Shopify Hydrogen architecture for a high-volume retailer. By decoupling the frontend, we enabled dynamic personalization without sacrificing Core Web Vitals, resulting in a 14% increase in AOV.
                                </p>
</div>
<div class="flex-shrink-0 grid grid-cols-2 gap-4">
<div class="text-center">
<p class="text-2xl font-bold">4.2M</p>
<p class="text-[9px] uppercase tracking-tighter text-white/40">Annual UV</p>
</div>
<div class="text-center">
<p class="text-2xl font-bold text-green-400">+$2.1M</p>
<p class="text-[9px] uppercase tracking-tighter text-white/40">Delta Revenue</p>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
<div class="lg:col-span-4 lg:sticky lg:top-32">
<div class="flex items-center gap-4 mb-6">
<span class="section-number text-3xl sm:text-4xl lg:text-5xl font-bold leading-none" style="color: rgba(0, 102, 255, 0.5); text-shadow: 0 0 20px rgba(0, 102, 255, 0.4), 0 0 40px rgba(0, 102, 255, 0.2); transition: all 0.6s ease;">02</span>
<h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">Marketplace <br/>Dominance</h3>
</div>
<p class="text-white/50 mb-8 leading-relaxed">
                        Precision-engineered channel management across Amazon and Flipkart. Moving beyond basic PPC into full-funnel DSP and inventory forecasting.
                    </p>
<div class="flex flex-wrap gap-2">
<span class="metric-badge bg-white/5 text-white/60">Amazon DSP</span>
<span class="metric-badge bg-white/5 text-white/60">Flipkart</span>
<!-- <span class="metric-badge bg-white/5 text-white/60">ACOS Opt.</span> -->
</div>
</div>
<div class="lg:col-span-8 space-y-12 pl-12 border-l border-white/5">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="space-y-4">
<h4 class="text-[11px] uppercase tracking-widest text-[var(--electric-blue)] font-bold">Channel Efficiency</h4>
<div class="bg-[var(--navy-black)] p-6 border border-white/5">
<div class="space-y-6">
<div>
<div class="flex justify-between mb-1"><span class="text-[10px] uppercase text-white/40">Buy Box Share</span><span class="text-xs font-bold">94%</span></div>
<div class="w-full h-1 bg-white/5"><div class="w-[94%] h-full bg-[var(--electric-blue)]"></div></div>
</div>
<div>
<div class="flex justify-between mb-1"><span class="text-[10px] uppercase text-white/40">Ad Sales % of Total</span><span class="text-xs font-bold">18%</span></div>
<div class="w-full h-1 bg-white/5"><div class="w-[30%] h-full bg-[var(--electric-blue)]"></div></div>
</div>
</div>
</div>
</div>
<div class="space-y-4">
<h4 class="text-[11px] uppercase tracking-widest text-[var(--electric-blue)] font-bold">Inventory Velocity</h4>
<div class="bg-[var(--navy-black)] p-6 border border-white/5">
<div class="space-y-6">
<div>
<div class="flex justify-between mb-1"><span class="text-[10px] uppercase text-white/40">Stock-out Rate</span><span class="text-xs font-bold">&lt;1.5%</span></div>
<div class="w-full h-1 bg-white/5"><div class="w-[10%] h-full bg-green-400"></div></div>
</div>
<div>
<div class="flex justify-between mb-1"><span class="text-[10px] uppercase text-white/40">Forecast Accuracy</span><span class="text-xs font-bold">92%</span></div>
<div class="w-full h-1 bg-white/5"><div class="w-[92%] h-full bg-[var(--electric-blue)]"></div></div>
</div>
</div>
</div>
</div>
</div>
<div class="p-8 bg-white/[0.02] border border-white/10 relative overflow-hidden group">
<div class="absolute inset-0 grayscale opacity-20 pointer-events-none bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD9GRIg20tLm8_rtIrEIRZzPmJI9l4sMto2pUOSrMNciBm0yh4MM-wNQHjsusHeu-eFcN3ZU1LafE6IbYU_ItQOtY9UvWD2z4yF9FtDItqecgn-Lpm9pmOr9WwfAh9bynwbN7CBKBvXTFEOaNXsIaVIPifYzXehKNSyFV_nesJg6o0CG0V8oD2vfKv42ac_chEgYpli1_iNUfe9lA6VMoEtiTPf17mYNBetBv1MdgbJgMZlV2oAsuJSsU2glkRWyzKsHQZiLJEbzw')"></div>
<div class="relative z-10 flex flex-col md:flex-row gap-8">
<div class="flex-1">
<h5 class="text-xs uppercase tracking-widest font-bold text-white/40 mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">inventory_2</span> Efficiency Brief
                                </h5>
<h4 class="text-2xl font-bold mb-4 italic">"Reclaiming margins from Amazon fees."</h4>
<p class="text-white/60 text-sm leading-relaxed max-w-lg">
                                    Reduced TACoS from 24% to 12.8% within 90 days for an FMCG brand. This was achieved via predictive listing optimization and aggressive cross-channel remarketing through Amazon DSP.
                                </p>
</div>
<div class="grid grid-cols-1 gap-4">
<div class="p-4 border" style="background-color: rgba(0, 102, 255, 0.1); border-color: rgba(0, 102, 255, 0.2);">
<p class="text-[10px] uppercase font-bold mb-1" style="color: var(--electric-blue);">TACoS Reduction</p>
<p class="text-3xl font-bold">47%</p>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
<div class="lg:col-span-4 lg:sticky lg:top-32">
<div class="flex items-center gap-4 mb-6">
<span class="section-number text-3xl sm:text-4xl lg:text-5xl font-bold leading-none" style="color: rgba(0, 102, 255, 0.5); text-shadow: 0 0 20px rgba(0, 102, 255, 0.4), 0 0 40px rgba(0, 102, 255, 0.2); transition: all 0.6s ease;">03</span>
<h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">Hyper-Precision <br/>Lead Gen</h3>
</div>
<p class="text-white/50 mb-8 leading-relaxed">
                        Data-driven B2B and consumer acquisition. We focus on <span class="text-white">Intent-Based Lead Quality</span> rather than sheer volume.
                    </p>
<div class="flex flex-wrap gap-2">
<span class="metric-badge bg-white/5 text-white/60">ABM Logic</span>
<span class="metric-badge bg-white/5 text-white/60">Lead Scoring</span>
<span class="metric-badge bg-white/5 text-white/60">Predictive Modeling</span>
</div>
</div>
<div class="lg:col-span-8 space-y-12 pl-12 border-l border-white/5">
<div class="space-y-6">
<h4 class="text-[11px] uppercase tracking-widest text-[var(--electric-blue)] font-bold">Funnel Analytics</h4>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
<div class="bg-[var(--navy-black)] p-6 border border-white/5">
<p class="text-[10px] uppercase text-white/40 mb-2">MQL to SQL Conv.</p>
<p class="text-3xl font-bold">38%</p>
<p class="text-[10px] text-green-400 mt-2 font-bold">+18% vs avg.</p>
</div>
<div class="bg-[var(--navy-black)] p-6 border border-white/5">
<p class="text-[10px] uppercase text-white/40 mb-2">CPL Efficiency</p>
<p class="text-3xl font-bold">$42.50</p>
<p class="text-[10px] text-[var(--electric-blue)] mt-2 font-bold">-22% vs benchmark</p>
</div>
<div class="bg-[var(--navy-black)] p-6 border border-white/5">
<p class="text-[10px] uppercase text-white/40 mb-2">Lead Decay Rate</p>
<p class="text-3xl font-bold">0.8%</p>
<p class="text-[10px] text-white/20 mt-2 font-bold">Ultra-low</p>
</div>
</div>
</div>
<div class="p-8 bg-white/[0.02] border border-white/10 relative overflow-hidden group">
<div class="absolute inset-0 grayscale opacity-20 pointer-events-none bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAnEXNAHGsKR5a99soipforEDk8q5j1hK0tL9cDoBykUU9u6fUAfFxaO_gcCrWv6uh8O8UFMkFHtu8WhMHjbiSg3jkKXxMrM-TJfWjHJ0_vGUtKQ4weeRyYlmyefUMEdTvHjtTQ4RZt9OIj9bG2eQKBNcFYIwxwdDcgJ-Nk77O62EaD8bGRvwDEMlDX9taEjoPIegSS-5SDKWCkfsIX39j5cOIB9GL8F09jBd7DL8dMN9-B_8_HdLoajIGW-esXnj-poGIM7AlucA')"></div>
<div class="relative z-10 flex flex-col md:flex-row gap-8">
<div class="flex-1">
<h5 class="text-xs uppercase tracking-widest font-bold text-white/40 mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">target</span> Campaign Performance
                                </h5>
<h4 class="text-2xl font-bold mb-4 italic">"Eliminating waste in high-ticket B2B."</h4>
<p class="text-white/60 text-sm leading-relaxed max-w-lg">
                                    Built an Account-Based Marketing (ABM) engine for a SaaS provider. By integrating intent data from 3rd party signals, we targeted only accounts in an active buying window, increasing pipeline value by $4M in two quarters.
                                </p>
</div>
<div class="flex items-center">
<div class="bg-white/10 backdrop-blur px-6 py-4 border border-white/10">
<p class="text-[9px] uppercase tracking-widest font-bold text-white/40">Pipeline Lift</p>
<p class="text-4xl font-bold tracking-tighter"><span style="color: var(--electric-blue);">+$4.2M</span></p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>

<section class="max-w-[1600px] mx-auto px-6 lg:px-12 py-40">
<div class="relative bg-[var(--electric-blue)] border border-white/10 p-16 lg:p-24 overflow-hidden group">
<div class="absolute -bottom-20 -right-20 size-96 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000"></div>
<div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
<div>
<h2 class="text-4xl lg:text-6xl font-bold tracking-tight mb-8"><?= htmlspecialchars($serviceCtaHeading) ?></h2>
<p class="text-xl text-white/80 max-w-lg mb-0 leading-relaxed">
                        <?= htmlspecialchars($serviceCtaText) ?>
                    </p>
</div>
<div class="flex flex-col sm:flex-row gap-4 justify-end">
<a href="<?= htmlspecialchars($serviceCtaPrimaryHref) ?>"<?= $serviceCtaPrimaryTarget ?> class="bg-white text-[var(--electric-blue)] px-10 py-5 font-bold uppercase tracking-widest text-xs hover:shadow-[0_20px_40px_rgba(0,0,0,0.3)] transition-all text-center">
                        <?= htmlspecialchars($serviceCtaPrimaryLabel) ?>
                    </a>
<a href="<?= htmlspecialchars($serviceCtaSecondaryHref) ?>"<?= $serviceCtaSecondaryTarget ?> class="bg-black/20 backdrop-blur-md text-white border border-white/20 px-10 py-5 font-bold uppercase tracking-widest text-xs hover:bg-black/40 transition-all text-center">
                        <?= htmlspecialchars($serviceCtaSecondaryLabel) ?>
                    </a>
</div>
</div>
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for section numbers glow effect
    const sectionNumbers = document.querySelectorAll('.section-number');
    
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.color = 'rgba(0, 102, 255, 0.8)';
                entry.target.style.textShadow = '0 0 30px rgba(0, 102, 255, 0.8), 0 0 60px rgba(0, 102, 255, 0.4), 0 0 90px rgba(0, 102, 255, 0.2)';
                entry.target.style.transform = 'scale(1.05)';
            } else {
                entry.target.style.color = 'rgba(0, 102, 255, 0.5)';
                entry.target.style.textShadow = '0 0 20px rgba(0, 102, 255, 0.4), 0 0 40px rgba(0, 102, 255, 0.2)';
                entry.target.style.transform = 'scale(1)';
            }
        });
    }, observerOptions);
    
    sectionNumbers.forEach(number => {
        observer.observe(number);
    });
});
</script>
<?php include __DIR__ . '/app/views/footer.php'; ?>







