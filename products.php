<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'products');
$pageTitle = $_seo['meta_title'] ?: 'Growth Marketing Products & Digital Solutions';
$pageDescription = $_seo['meta_description'] ?: 'Discover Digifyce\'s innovative marketing products designed to automate growth, optimize conversions, and improve campaign performance.';
$bodyClass = 'bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 selection:bg-primary selection:text-white';
include __DIR__ . '/app/views/header.php';
?>

<style>
.cta-section { position: relative; background: radial-gradient(circle at 20% 20%, rgba(0,102,255,0.25), transparent 40%), radial-gradient(circle at 80% 80%, rgba(0,102,255,0.15), transparent 40%), #000; }
.animate-gradient-move { background-size: 200% 200%; animation: gradientMove 8s ease infinite; }
@keyframes gradientMove { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
.btn-primary { background: linear-gradient(135deg, #0066ff, #004bcc); color: #fff; font-weight: 600; padding: 16px 34px; border-radius: 999px; transition: all 0.3s ease; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,102,255,0.35); }
.btn-primary:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,102,255,0.55); }
.btn-secondary { background: transparent; border: 1px solid rgba(255,255,255,0.25); color: #fff; font-weight: 500; padding: 16px 34px; border-radius: 999px; transition: all 0.3s ease; }
.btn-secondary:hover { background: rgba(0,102,255,0.15); border-color: #0066ff; transform: translateY(-3px); }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<main class="bg-background-dark" id="products-main">
  <div class="animate-pulse p-24 text-center">
    <div class="h-12 bg-white/5 rounded w-1/2 mx-auto mb-4"></div>
    <div class="h-6 bg-white/5 rounded w-1/3 mx-auto"></div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  fetch('<?= $appUrl ?>/app/api/products.php')
    .then(r => r.json())
    .then(res => {
      if (!res.success) return;
      const d = res.data;
      const h = d.hero || {};
      const crm = d.crm || {};
      const zing = d.zingbot || {};
      const cta = d.cta || {};

      function featureCards(feats) {
        return (feats || []).map(f =>
          `<div class="glow-card bg-white/[0.02] p-8 product-card">
            <h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2">
              <i class="${f.icon_class}"></i> ${f.title}
            </h4>
            <p class="text-slate-400 text-sm leading-relaxed">${f.description}</p>
          </div>`
        ).join('');
      }

      document.getElementById('products-main').innerHTML = `
        <section class="relative min-h-screen flex items-center justify-center text-center px-6 bg-black hero-section">
          <div class="max-w-5xl">
            <h1 class="text-5xl md:text-7xl font-black text-white leading-tight uppercase tracking-tight hero-title">
              ${h.headline_main} <span class="text-primary">${h.headline_highlight}</span>
            </h1>
            <p class="text-slate-400 mt-6 text-lg md:text-xl max-w-2xl mx-auto">${h.description}</p>
            <div class="mt-10 flex justify-center gap-6">
              <button type="button" class="btn-primary flex items-center gap-2" onclick="window.location.href='#crm'">
                <i class="fa-solid fa-chart-line"></i> ${h.crm_btn_label}
              </button>
              <button type="button" class="btn-secondary flex items-center gap-2" onclick="window.location.href='#zingbot'">
                <i class="fa-solid fa-robot"></i> ${h.zingbot_btn_label}
              </button>
            </div>
          </div>
        </section>

        <section id="crm" class="py-24 border-t border-white/10 product-section">
          <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
              <h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">${crm.label}</h2>
              <h3 class="text-white text-5xl font-black uppercase tracking-tight">${crm.heading}</h3>
              <p class="text-slate-400 mt-6 max-w-2xl mx-auto">${crm.sub_desc}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">${featureCards(d.crm_features)}</div>
            <div class="text-center mt-12 flex items-center justify-center">
              <button type="button" class="btn-primary flex items-center gap-2 justify-center" onclick="window.location.href='${crm.cta_url}'">
                <i class="fa-solid fa-paper-plane"></i> ${crm.cta_label}
              </button>
            </div>
          </div>
        </section>

        <section id="zingbot" class="py-24 border-t border-white/10 bg-black product-section">
          <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
              <h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">${zing.label}</h2>
              <h3 class="text-white text-5xl font-black uppercase tracking-tight">${zing.heading}</h3>
              <p class="text-slate-400 mt-6 max-w-2xl mx-auto">${zing.sub_desc}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">${featureCards(d.zingbot_features)}</div>
            <div class="text-center mt-12 flex items-center justify-center">
              <button type="button" class="btn-primary flex items-center gap-2 justify-center" onclick="window.location.href='${zing.cta_url}'">
                <i class="fa-solid fa-paper-plane"></i> ${zing.cta_label}
              </button>
            </div>
          </div>
        </section>

        <section class="py-24 border-t border-white/10 text-center cta-section relative overflow-hidden">
          <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-primary/30 via-purple-700/20 to-black/80 pointer-events-none animate-gradient-move"></div>
          <div class="relative z-10 max-w-2xl mx-auto">
            <h3 class="cta-heading text-white text-4xl md:text-5xl font-black uppercase tracking-tight drop-shadow-lg flex items-center justify-center gap-3">
              <i class="fa-solid fa-rocket text-primary"></i> ${cta.heading}
            </h3>
            <p class="cta-desc text-slate-300 mt-6 max-w-xl mx-auto text-lg md:text-xl drop-shadow">${cta.description}</p>
            <div class="mt-10 flex justify-center">
              <button type="button" class="btn-primary flex items-center gap-2 justify-center shadow-xl text-lg px-12 py-5" onclick="window.location.href='${cta.btn_url}'">
                <i class="fa-solid fa-phone-volume"></i> ${cta.btn_label}
              </button>
            </div>
          </div>
        </section>`;

      if (typeof gsap !== 'undefined') {
        gsap.from('.hero-title', { opacity: 0, y: 60, duration: 1.2, ease: 'power3.out' });
        gsap.utils.toArray('.product-card').forEach((card, i) => {
          gsap.from(card, { opacity: 0, y: 60, duration: 1, ease: 'power3.out',
            scrollTrigger: { trigger: card, start: 'top 85%', toggleActions: 'play none none none' }, delay: i * 0.1 });
        });
      }
    })
    .catch(console.error);
});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
