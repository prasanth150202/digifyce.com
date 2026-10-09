
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'testimonial');
$pageTitle = $_seo['meta_title'] ?: 'Client Testimonials – Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'See what our clients say about Digifyce\'s digital marketing and growth services.';
$_testimonials = $_seoPdo->query("SELECT * FROM testimonial_items WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/app/views/header.php'; ?>

<style>
    .testimonial-innovative video { max-height: 340px; object-fit: cover; width: 100%; background: #000; display: block; }
    .testimonial-innovative { background: linear-gradient(120deg, #0a0f1a 60%, #1a2744 100%); position: relative; overflow: hidden; }
    .testimonial-innovative::before { content: ''; position: absolute; top: -100px; left: -100px; width: 400px; height: 400px; background: radial-gradient(circle, #00d9ff33 0%, transparent 80%); z-index: 0; filter: blur(10px); }
    .testimonial-innovative::after { content: ''; position: absolute; bottom: -120px; right: -120px; width: 500px; height: 500px; background: radial-gradient(circle, #8b5cf633 0%, transparent 80%); z-index: 0; filter: blur(12px); }
    .testimonial-fade { opacity: 0; transform: translateY(40px) scale(0.98); transition: all 1s cubic-bezier(.23,1,.32,1); }
    .testimonial-fade.visible { opacity: 1; transform: translateY(0) scale(1); }
    .testimonial-quote { font-size: 3rem; color: #00d9ff; opacity: 0.15; position: absolute; top: -30px; left: -20px; z-index: 1; pointer-events: none; }
    .testimonial-avatar { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 3px solid #00d9ff; box-shadow: 0 4px 24px #00d9ff33; margin-bottom: 1rem; background: #fff; position: relative; z-index: 2; animation: floatAvatar 4s ease-in-out infinite alternate; }
    @keyframes floatAvatar { 0% { transform: translateY(0); } 100% { transform: translateY(-12px); } }
</style>

<section class="testimonial-innovative py-20">
    <div class="max-w-5xl mx-auto px-4 relative z-10">
        <h2 class="text-3xl sm:text-4xl font-bold text-center text-white mb-16 tracking-tight">Client Video Testimonials</h2>
        <div class="space-y-24" id="testimonials-container">
            <?php $_borderColors = ['border-[#00d9ff33]', 'border-[#8b5cf633]']; ?>
            <?php foreach ($_testimonials as $i => $t):
                $isReverse = $i % 2 === 1;
                $flexDir = $isReverse ? 'md:flex-row-reverse' : 'md:flex-row';
                $border = $_borderColors[$i % 2];
            ?>
            <div class="flex flex-col <?= $flexDir ?> items-center md:items-stretch gap-10 testimonial-fade">
                <div class="md:w-1/2 w-full relative">
                    <video class="rounded-2xl shadow-2xl w-full h-auto border-4 <?= $border ?>" controls poster="/<?= htmlspecialchars($t['thumbnail_path']) ?>" preload="none">
                        <source src="/<?= htmlspecialchars($t['video_path']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="md:w-1/2 w-full flex items-center relative">
                    <div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
                        <span class="testimonial-quote">"</span>
                        <img src="/<?= htmlspecialchars($t['logo_path']) ?>" alt="<?= htmlspecialchars($t['client_name']) ?>" class="testimonial-avatar mx-auto md:mx-0" width="64" height="64" loading="lazy">
                        <h3 class="text-2xl font-bold mb-2 tracking-tight"><?= htmlspecialchars($t['client_name']) ?></h3>
                        <p class="text-lg mb-4 italic relative z-2"><?= htmlspecialchars($t['quote']) ?></p>
                        <div class="text-sm text-blue-300 font-semibold">- <?= htmlspecialchars($t['story_label']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    revealTestimonials();
});

function revealTestimonials() {
    const els = document.querySelectorAll('.testimonial-fade');
    const wh = window.innerHeight;
    els.forEach((el, i) => { if (el.getBoundingClientRect().top < wh - 100) setTimeout(() => el.classList.add('visible'), i * 200); });
}
window.addEventListener('scroll', revealTestimonials);
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
