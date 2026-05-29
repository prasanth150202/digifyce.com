<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'technology');
$pageTitle = $_seo['meta_title'] ?: 'Marketing Technology & Automation Solutions';
$pageDescription = $_seo['meta_description'] ?: 'Leverage advanced marketing technology, automation tools, and analytics solutions to improve performance tracking and business growth.';
$bodyClass = 'bg-[#05070a] text-white';
include __DIR__ . '/app/views/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

<style>
@media (max-width: 1024px) {
    .horizontal-wrapper { overflow-x: hidden; }
    .horizontal-track { flex-direction: column !important; gap: 2rem !important; width: 100% !important; }
    .panel { min-width: 100% !important; width: 100%; padding: 1.5rem !important; border-radius: 1.5rem !important; }
    .image-slider { flex-direction: row !important; }
    .slider-img { min-width: 100% !important; max-width: 100% !important; height: 240px; object-fit: cover; }
    .py-24, .lg\:py-32 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
    .text-4xl, .lg\:text-6xl, .md\:text-5xl { font-size: 2rem !important; line-height: 2.5rem !important; }
    .text-xl { font-size: 1.1rem !important; }
    .p-12 { padding: 1.5rem !important; }
    .mb-20 { margin-bottom: 2rem !important; }
}
@media (max-width: 640px) {
    .panel { padding: 1rem !important; }
    .text-4xl, .lg\:text-6xl, .md\:text-5xl { font-size: 1.5rem !important; line-height: 2rem !important; }
    .text-xl { font-size: 1rem !important; }
    .p-12 { padding: 1rem !important; }
    .mb-20 { margin-bottom: 1.5rem !important; }
}
.panel { margin-left: calc((100vw - 85vw) / 2); margin-right: calc((100vw - 85vw) / 2); }
</style>

<main class="min-h-screen">

<div id="tech-hero-container">
  <section class="py-24 lg:py-32 border-b border-white/5 animate-pulse">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-3 bg-white/5 rounded w-32 mb-6"></div>
      <div class="h-16 bg-white/5 rounded w-2/3 mb-4"></div>
      <div class="h-5 bg-white/5 rounded w-1/2"></div>
    </div>
  </section>
</div>

<div id="tech-panels-container">
  <section class="py-24 border-t border-white/5 overflow-hidden" id="tech-stack-section">
    <div class="horizontal-wrapper relative">
      <div class="horizontal-track flex gap-10">
        <div class="panel min-w-[85vw] glass-card rounded-3xl p-12 animate-pulse">
          <div class="h-4 bg-white/5 rounded w-1/3 mb-4"></div>
          <div class="h-10 bg-white/5 rounded w-1/2 mb-8"></div>
          <div class="h-4 bg-white/5 rounded w-full mb-2"></div>
        </div>
      </div>
    </div>
  </section>
</div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  fetch('<?= $appUrl ?>/app/api/technology.php')
    .then(r => r.json())
    .then(res => {
      if (!res.success) return;
      const d = res.data;
      const h = d.hero;
      if (h) {
        document.getElementById('tech-hero-container').innerHTML = `
          <section class="py-24 lg:py-32 border-b border-white/5">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
              <span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block">${h.badge}</span>
              <h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">${h.headline}</h1>
              <p class="text-white/50 text-lg mt-6 max-w-2xl">${h.description}</p>
            </div>
          </section>`;
      }
      const panels = d.panels || [];
      if (panels.length) {
        const panelsHtml = panels.map(p => {
          const imgs = (p.image_paths || []).map(img => `<img src="/${img}" class="slider-img w-full shrink-0 object-cover">`).join('');
          let contentHtml = '';
          if (p.bullets_json && p.bullets_json.length) {
            contentHtml = `<div class="space-y-6">${p.bullets_json.map(b => `<div><h4 class="text-xl font-bold mb-2">${b.h4}</h4><p class="text-gray-400">${b.p}</p></div>`).join('')}</div>`;
          } else {
            contentHtml = `<p class="text-xl text-gray-300 leading-relaxed">${p.description || ''}</p>`;
          }
          const align = (p.bullets_json && p.bullets_json.length) ? 'start' : 'center';
          return `<div class="panel min-w-[85vw] glass-card rounded-3xl p-12">
            <div class="grid lg:grid-cols-2 gap-12 items-${align}">
              <div>
                <span class="text-xs font-mono text-[var(--electric-blue)]">${p.panel_number}. ${p.category_label}</span>
                <h3 class="text-4xl md:text-5xl font-semibold mt-4 mb-8">${p.title}</h3>
                ${contentHtml}
              </div>
              <div class="relative overflow-hidden rounded-2xl">
                <div class="image-slider flex">${imgs}</div>
              </div>
            </div>
          </div>`;
        }).join('');
        document.getElementById('tech-panels-container').innerHTML = `
          <section class="py-24 border-t border-white/5 overflow-hidden" id="tech-stack-section">
            <div class="horizontal-wrapper relative">
              <div class="horizontal-track flex gap-10">${panelsHtml}</div>
            </div>
          </section>`;
        initGsap();
      }
    })
    .catch(console.error);
});

function initGsap() {
  if (typeof gsap === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);
  let mm = gsap.matchMedia();
  mm.add('(min-width: 1025px)', () => {
    const section = document.querySelector('#tech-stack-section');
    const track = document.querySelector('.horizontal-track');
    if (!section || !track) return;
    gsap.to(track, {
      x: () => -(track.scrollWidth - window.innerWidth),
      ease: 'none',
      scrollTrigger: {
        trigger: section, start: 'top top',
        end: () => '+=' + (track.scrollWidth - window.innerWidth),
        scrub: 1, pin: true, invalidateOnRefresh: true
      }
    });
  });
  gsap.utils.toArray('.image-slider').forEach(slider => {
    const images = slider.querySelectorAll('.slider-img');
    gsap.to(slider, { xPercent: -100 * (images.length - 1), duration: 12, ease: 'none', repeat: -1 });
  });
}
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
