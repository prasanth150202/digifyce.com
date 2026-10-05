<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'technology');
$pageTitle = $_seo['meta_title'] ?: 'Marketing Technology & Automation Solutions';
$pageDescription = $_seo['meta_description'] ?: 'Leverage advanced marketing technology, automation tools, and analytics solutions to improve performance tracking and business growth.';
$bodyClass = 'bg-[#05070a] text-white';
$_techHero = $_seoPdo->query("SELECT * FROM technology_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$_techPanels = $_seoPdo->query("SELECT * FROM technology_panels WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($_techPanels as &$_tp) {
    $_tp['bullets_json'] = $_tp['bullets_json'] ? json_decode($_tp['bullets_json'], true) : [];
    $_tp['image_paths']  = $_tp['image_paths']  ? array_map('trim', explode(',', $_tp['image_paths'])) : [];
}
unset($_tp);
include __DIR__ . '/app/views/header.php';
// GSAP + ScrollTrigger are already loaded (deferred) by header.php -- no need
// to load a second copy here; this page's usage below runs inside
// DOMContentLoaded, after header.php's deferred scripts have executed.
?>

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

<section class="py-24 lg:py-32 border-b border-white/5">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block"><?= htmlspecialchars($_techHero['badge'] ?? '') ?></span>
    <h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8"><?= htmlspecialchars($_techHero['headline'] ?? 'Marketing Technology & Automation') ?></h1>
    <p class="text-white/50 text-lg mt-6 max-w-2xl"><?= htmlspecialchars($_techHero['description'] ?? '') ?></p>
  </div>
</section>

<?php if ($_techPanels): ?>
<section class="py-24 border-t border-white/5 overflow-hidden" id="tech-stack-section">
  <div class="horizontal-wrapper relative">
    <div class="horizontal-track flex gap-10">
      <?php foreach ($_techPanels as $p):
        $hasBullets = !empty($p['bullets_json']);
        $align = $hasBullets ? 'start' : 'center';
      ?>
      <div class="panel min-w-[85vw] glass-card rounded-3xl p-12">
        <div class="grid lg:grid-cols-2 gap-12 items-<?= $align ?>">
          <div>
            <span class="text-xs font-mono text-[var(--electric-blue)]"><?= htmlspecialchars($p['panel_number']) ?>. <?= htmlspecialchars($p['category_label']) ?></span>
            <h3 class="text-4xl md:text-5xl font-semibold mt-4 mb-8"><?= htmlspecialchars($p['title']) ?></h3>
            <?php if ($hasBullets): ?>
            <div class="space-y-6">
              <?php foreach ($p['bullets_json'] as $b): ?>
              <div><h4 class="text-xl font-bold mb-2"><?= htmlspecialchars($b['h4']) ?></h4><p class="text-gray-400"><?= htmlspecialchars($b['p']) ?></p></div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-xl text-gray-300 leading-relaxed"><?= htmlspecialchars($p['description'] ?? '') ?></p>
            <?php endif; ?>
          </div>
          <div class="relative overflow-hidden rounded-2xl">
            <div class="image-slider flex">
              <?php foreach ($p['image_paths'] as $img): ?>
              <img src="/<?= htmlspecialchars($img) ?>" class="slider-img w-full shrink-0 object-cover" loading="lazy"<?= image_dims_attr(__DIR__, $img) ?>>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  initGsap();
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
