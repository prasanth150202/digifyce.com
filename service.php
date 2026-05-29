<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'service');
$pageTitle = $_seo['meta_title'] ?: 'Digital Marketing Services – Growth & Performance Strategies';
$pageDescription = $_seo['meta_description'] ?: 'Explore Digifyce\'s expert digital marketing services including branding, paid ads, analytics, and customer acquisition solutions to grow your business.';
include __DIR__ . '/app/views/header.php';
?>
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
        switch ($row['setting_key']) {
            case 'service_cta_heading':      $serviceCtaHeading = $row['setting_value'] ?: $serviceCtaHeading; break;
            case 'service_cta_text':         $serviceCtaText = $row['setting_value'] ?: $serviceCtaText; break;
            case 'service_cta_primary_label':$serviceCtaPrimaryLabel = $row['setting_value'] ?: $serviceCtaPrimaryLabel; break;
            case 'service_cta_primary_url':  $serviceCtaPrimaryUrl = $row['setting_value'] ?: $serviceCtaPrimaryUrl; break;
            case 'service_cta_secondary_label': $serviceCtaSecondaryLabel = $row['setting_value'] ?: $serviceCtaSecondaryLabel; break;
            case 'service_cta_secondary_url': $serviceCtaSecondaryUrl = $row['setting_value'] ?: $serviceCtaSecondaryUrl; break;
        }
    }
} catch (Exception $e) {}

function buildServiceHref($url, $appUrl) {
    $href = trim($url);
    if ($href === '') return '#';
    if (strpos($href, 'http') === 0 || strpos($href, '//') === 0 || strpos($href, '#') === 0) return $href;
    return $appUrl . '/' . ltrim($href, '/');
}
function serviceHrefTarget($url) {
    $href = trim($url);
    return (strpos($href, 'http') === 0 || strpos($href, '//') === 0) ? ' target="_blank"' : '';
}

$serviceCtaPrimaryHref   = buildServiceHref($serviceCtaPrimaryUrl, $appUrl);
$serviceCtaSecondaryHref = buildServiceHref($serviceCtaSecondaryUrl, $appUrl);
$serviceCtaPrimaryTarget   = serviceHrefTarget($serviceCtaPrimaryUrl);
$serviceCtaSecondaryTarget = serviceHrefTarget($serviceCtaSecondaryUrl);
?>
<main>

<div id="service-hero-container">
  <div class="pt-40 pb-20 px-6 lg:px-12 border-b border-white/5 animate-pulse" style="background-color:var(--navy-black);">
    <div class="max-w-[1600px] mx-auto flex flex-col lg:flex-row justify-between items-end gap-12">
      <div class="max-w-3xl">
        <div class="h-3 bg-white/5 rounded w-64 mb-6"></div>
        <div class="h-20 bg-white/5 rounded w-2/3 mb-4"></div>
        <div class="h-5 bg-white/5 rounded w-1/2"></div>
      </div>
      <div class="h-24 w-48 bg-white/5 rounded"></div>
    </div>
  </div>
</div>

<div id="service-blocks-container">
  <section class="px-6 lg:px-12 py-24" style="background-color:rgba(255,255,255,0.02);">
    <div class="max-w-[1600px] mx-auto space-y-8">
      <div class="animate-pulse space-y-4">
        <div class="h-6 bg-white/5 rounded w-1/3"></div>
        <div class="h-4 bg-white/5 rounded w-2/3"></div>
        <div class="h-4 bg-white/5 rounded w-1/2"></div>
      </div>
    </div>
  </section>
</div>

<section class="max-w-[1600px] mx-auto px-6 lg:px-12 py-40">
<div class="relative bg-[var(--electric-blue)] border border-white/10 p-16 lg:p-24 overflow-hidden group">
<div class="absolute -bottom-20 -right-20 size-96 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000"></div>
<div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
<div>
<h2 class="text-4xl lg:text-6xl font-bold tracking-tight mb-8"><?= htmlspecialchars($serviceCtaHeading) ?></h2>
<p class="text-xl text-white/80 max-w-lg mb-0 leading-relaxed"><?= htmlspecialchars($serviceCtaText) ?></p>
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
(function () {
  const eb = 'var(--electric-blue)';

  function metricColor(c) {
    if (c === 'green') return { bar: 'bg-green-400', text: 'text-green-400' };
    if (c === 'blue')  return { bar: 'bg-[var(--electric-blue)]', text: '', style: 'color:var(--electric-blue)' };
    return { bar: 'bg-white/20', text: 'text-white' };
  }

  function renderMetricRow(m) {
    if (m.bar_pct !== undefined) {
      const col = metricColor(m.color);
      const valStyle = col.style ? ` style="${col.style}"` : '';
      const barStyle = m.color === 'blue' ? ' style="background-color:var(--electric-blue);box-shadow:0 0 10px rgba(0,102,255,0.5);"' : '';
      return `<div class="bg-[var(--navy-black)] p-4 border border-white/5">
        <div class="flex justify-between items-end mb-2">
          <span class="text-xs text-white/40">${m.label}</span>
          <span class="text-lg font-bold ${col.text}"${valStyle}>${m.value}</span>
        </div>
        <div class="w-full h-1 bg-white/5"><div class="h-full ${col.bar}" style="width:${m.bar_pct}%"${barStyle}></div></div>
      </div>`;
    } else {
      const noteColor = m.note_color === 'green' ? 'text-green-400' : m.note_color === 'blue' ? '' : 'text-white/20';
      const noteStyle = m.note_color === 'blue' ? ` style="color:var(--electric-blue);"` : '';
      return `<div class="bg-[var(--navy-black)] p-6 border border-white/5">
        <p class="text-[10px] uppercase text-white/40 mb-2">${m.label}</p>
        <p class="text-3xl font-bold">${m.value}</p>
        ${m.note ? `<p class="text-[10px] ${noteColor} mt-2 font-bold"${noteStyle}>${m.note}</p>` : ''}
      </div>`;
    }
  }

  function renderCaseStudyStats(stats) {
    if (!stats || !stats.length) return '';
    if (stats.length >= 2) {
      return `<div class="flex-shrink-0 grid grid-cols-${Math.min(stats.length,3)} gap-4">` +
        stats.map(s => {
          const cls = s.color === 'green' ? 'text-green-400' : s.color === 'blue' ? '' : '';
          const style = s.color === 'blue' ? ' style="color:var(--electric-blue);"' : '';
          return `<div class="text-center"><p class="text-2xl font-bold ${cls}"${style}>${s.value}</p><p class="text-[9px] uppercase tracking-tighter text-white/40">${s.label}</p></div>`;
        }).join('') + '</div>';
    } else {
      const s = stats[0];
      const sCls = s.color === 'blue' ? '' : '';
      const sStyle = s.color === 'blue' ? ' style="color:var(--electric-blue);"' : '';
      return `<div class="flex items-center">
        <div class="p-4 border" style="background-color:rgba(0,102,255,0.1);border-color:rgba(0,102,255,0.2);">
          <p class="text-[10px] uppercase font-bold mb-1" style="color:var(--electric-blue);">${s.label}</p>
          <p class="text-3xl font-bold"${sStyle}>${s.value}</p>
        </div>
      </div>`;
    }
  }

  function renderBlock(b, idx) {
    const badges = (b.tech_badges || []).map(t => `<span class="metric-badge bg-white/5 text-white/60">${t}</span>`).join('');
    const leftMetrics = (b.left_metrics_json || []).map(renderMetricRow).join('');
    const rightMetrics = (b.right_metrics_json || []).map(renderMetricRow).join('');
    const hasRightCol = b.right_metrics_json && b.right_metrics_json.length > 0;
    const isStatGrid = b.left_metrics_json && b.left_metrics_json.length > 0 && b.left_metrics_json[0].bar_pct === undefined;
    const statsHtml = renderCaseStudyStats(b.case_study_stats_json);

    let metricsHtml = '';
    if (isStatGrid) {
      metricsHtml = `<div class="space-y-6">
        <h4 class="text-[11px] uppercase tracking-widest font-bold" style="color:var(--electric-blue);">${b.left_col_heading}</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">${leftMetrics}</div>
      </div>`;
    } else if (hasRightCol) {
      metricsHtml = `<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-4">
          <h4 class="text-[11px] uppercase tracking-widest font-bold" style="color:var(--electric-blue);">${b.left_col_heading}</h4>
          <div class="space-y-4">${leftMetrics}</div>
        </div>
        <div class="space-y-4">
          <h4 class="text-[11px] uppercase tracking-widest font-bold" style="color:var(--electric-blue);">${b.right_col_heading}</h4>
          <div class="space-y-4">${rightMetrics}</div>
        </div>
      </div>`;
    }

    const bgImg = b.case_study_image_url ? `background-image:url('${b.case_study_image_url}')` : '';
    const caseStudyLayout = statsHtml.includes('grid-cols-') ? 'flex-col md:flex-row gap-8 items-start' : 'flex-col md:flex-row gap-8';

    return `<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
      <div class="lg:col-span-4 lg:sticky lg:top-32">
        <div class="flex items-center gap-4 mb-6">
          <span class="section-number text-3xl sm:text-4xl lg:text-5xl font-bold leading-none" style="color:rgba(0,102,255,0.5);text-shadow:0 0 20px rgba(0,102,255,0.4),0 0 40px rgba(0,102,255,0.2);transition:all 0.6s ease;">${b.section_number}</span>
          <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">${b.title_line1}${b.title_line2 ? '<br/>' + b.title_line2 : ''}</h3>
        </div>
        <p class="text-white/50 mb-8 leading-relaxed">${b.description || ''}</p>
        <div class="flex flex-wrap gap-2">${badges}</div>
      </div>
      <div class="lg:col-span-8 space-y-12 pl-12 border-l border-white/5">
        ${metricsHtml}
        <div class="p-8 bg-white/[0.02] border border-white/10 relative overflow-hidden group">
          ${bgImg ? `<div class="absolute inset-0 grayscale opacity-20 pointer-events-none bg-cover bg-center" style="${bgImg}"></div>` : ''}
          <div class="relative z-10 flex ${caseStudyLayout}">
            <div class="flex-1">
              <h5 class="text-xs uppercase tracking-widest font-bold text-white/40 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">${b.case_study_icon}</span> ${b.case_study_section_label}
              </h5>
              <h4 class="text-2xl font-bold mb-4 italic">${b.case_study_quote}</h4>
              <p class="text-white/60 text-sm leading-relaxed max-w-lg">${b.case_study_body}</p>
            </div>
            ${statsHtml}
          </div>
        </div>
      </div>
    </div>`;
  }

  function initSectionObserver() {
    const sectionNumbers = document.querySelectorAll('.section-number');
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.color = 'rgba(0,102,255,0.8)';
          e.target.style.textShadow = '0 0 30px rgba(0,102,255,0.8),0 0 60px rgba(0,102,255,0.4),0 0 90px rgba(0,102,255,0.2)';
          e.target.style.transform = 'scale(1.05)';
        } else {
          e.target.style.color = 'rgba(0,102,255,0.5)';
          e.target.style.textShadow = '0 0 20px rgba(0,102,255,0.4),0 0 40px rgba(0,102,255,0.2)';
          e.target.style.transform = 'scale(1)';
        }
      });
    }, { threshold: 0.3, rootMargin: '0px 0px -100px 0px' });
    sectionNumbers.forEach(n => obs.observe(n));
  }

  document.addEventListener('DOMContentLoaded', function () {
    fetch('<?= $appUrl ?>/app/api/service.php')
      .then(r => r.json())
      .then(res => {
        if (!res.success) return;
        const d = res.data;

        // Hero
        const h = d.hero;
        if (h) {
          document.getElementById('service-hero-container').innerHTML = `
            <section class="pt-40 pb-20 px-6 lg:px-12 border-b border-white/5" style="background-color:var(--navy-black);">
              <div class="max-w-[1600px] mx-auto">
                <div class="flex flex-col lg:flex-row justify-between items-end gap-12">
                  <div class="max-w-3xl">
                    <span class="font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block" style="color:var(--electric-blue);">${h.badge}</span>
                    <h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">${h.headline_line1}<br/>${h.headline_line2}</h1>
                    <p class="text-white/40 text-xl leading-relaxed max-w-xl">${h.description}</p>
                  </div>
                  <div class="flex flex-col gap-4 text-right">
                    <div class="p-6 border border-white/5" style="background-color:rgba(255,255,255,0.03);">
                      <p class="text-[10px] uppercase tracking-widest text-white/40 mb-2 font-bold">${h.stat_label}</p>
                      <p class="text-5xl font-bold" style="color:var(--electric-blue);">${h.stat_value} <span class="text-xs font-normal text-white/20 align-top uppercase">${h.stat_suffix}</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </section>`;
        }

        // Blocks
        const blocks = d.blocks || [];
        if (blocks.length) {
          document.getElementById('service-blocks-container').innerHTML = `
            <section class="px-6 lg:px-12 py-24" style="background-color:rgba(255,255,255,0.02);">
              <div class="max-w-[1600px] mx-auto space-y-40">
                ${blocks.map(renderBlock).join('')}
              </div>
            </section>`;
          initSectionObserver();
        }
      })
      .catch(console.error);
  });
})();
</script>

</main>
<?php include __DIR__ . '/app/views/footer.php'; ?>
