<?php
require_once __DIR__ . '/../../app/helpers/seo.php';
$_seo = load_page_seo($pdo, 'blog');
$pageTitle = $_seo['meta_title'] ?: 'Digifyce Insights Index';
$pageDescription = $_seo['meta_description'] ?: 'Explore insights, guides, and strategies from the Digifyce team on digital marketing, branding, and growth.';
$bodyClass = 'bg-background-light dark:bg-background-dark font-display text-white selection:bg-primary/30';
$tailwindConfig = <<<HTML
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#0d69f2",
                    "background-light": "#f5f7f8",
                    "background-dark": "#05070a",
                },
                fontFamily: {
                    "display": ["Space Grotesk", "sans-serif"]
                },
                borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
        },
    }
</script>
HTML;
$extraHead = <<<HTML
<style type="text/tailwindcss">
    @layer utilities {
        .grainy-overlay {
            position: relative;
        }
        .grainy-overlay::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.15;
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuCR4e1vb2zWPMiZ9LeZTf2gzowyGBkrRUxBbPkUYdOwSSGqG42pL6sQYmAypr7rZ9ttLmTiMKG2zlTvnVUF4_E5t46k2wpduVM1bGhJCgVWYURAPo30ocoqND-bMzSHoCkNb5Lkca_p-nb5Fn9IB2r5wQS6mp5QVE0r0qDqkeK-FeNFL1GLykzfdg2ewQDxxZ1msDX3m9pNHJR19LXS238ALbKcDGTNm7k768xgjH35n5FqAbUZWjncHP6shs6W7zp8m9IuhLvuAA);
            mix-blend-mode: overlay;
        }
        .list-item-hover:hover .thumbnail-glow {
            box-shadow: 0 0 30px rgba(13, 105, 242, 0.4);
            filter: grayscale(0%);
        }
        .list-item-hover:hover .post-number {
            color: #0d69f2;
            transform: translateX(4px);
        }
        .thumbnail-img {
            filter: grayscale(100%);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .post-number {
            transition: all 0.3s ease;
        }
    }
</style>
HTML;
include __DIR__ . '/header.php';
?>
    <!-- Hero Section -->
    <section class="px-6 md:px-20 lg:px-40 py-20 flex flex-col items-center text-center">
        <div class="max-w-[1200px] w-full border-b border-white/5 ">
            <h1 class="text-white text-6xl md:text-8xl lg:text-9xl font-black leading-[0.9] tracking-tighter uppercase mb-8">
                Insights <br class="hidden md:block"/> &amp; Intel
            </h1>
            <p class="text-slate-400 text-lg md:text-xl font-normal max-w-2xl mx-auto leading-relaxed">
                Data-driven performance strategies for the modern enterprise. Navigate the complexities of high-scale marketing with our technical index.
            </p>
        </div>
    </section>

    <!-- Filters / Chips or Category Title -->
    <?php if (!empty($currentCategory)) : ?>
        <?php
            // Find the category name for the selected slug
            $categoryTitle = null;
            foreach ($categories as $cat) {
                if ($cat['slug'] === $currentCategory) {
                    $categoryTitle = $cat['name'];
                    break;
                }
            }
        ?>
        <div class="px-6 md:px-20 lg:px-40 mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-white py-12 text-center"><?= htmlspecialchars($categoryTitle) ?></h1>
        </div>
    <?php else: ?>
        <div class="px-6 md:px-20 lg:px-40 mb-12">
            <div class="mb-8">
                <h3 class="text-xs font-bold tracking-widest uppercase text-white/60 mb-4">Categories</h3>
                <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide">
                    <a href="<?= $appUrl ?>/blog_list.php" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full <?= empty($currentTag) && empty($currentCategory) ? 'bg-primary' : 'bg-white/5 border border-white/10 hover:bg-white/10' ?> px-6 transition-all cursor-pointer relative z-10">
                        <p class="<?= empty($currentTag) && empty($currentCategory) ? 'text-white' : 'text-white/60' ?> text-xs font-bold tracking-widest uppercase">ALL INTEL</p>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= $appUrl ?>/blog_list.php?category=<?= htmlspecialchars($cat['slug']) ?>" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full <?= $currentCategory === $cat['slug'] ? 'bg-primary' : 'bg-white/5 border border-white/10 hover:bg-white/10' ?> px-6 transition-all cursor-pointer relative z-10">
                            <p class="<?= $currentCategory === $cat['slug'] ? 'text-white' : 'text-white/60' ?> text-xs font-bold tracking-widest uppercase"><?= htmlspecialchars($cat['name']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($tags)): ?>
            <div>
                <h3 class="text-xs font-bold tracking-widest uppercase text-white/60 mb-4">Tags</h3>
                <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?= $appUrl ?>/blog_list.php?tag=<?= htmlspecialchars($tag['slug']) ?>" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full <?= $currentTag === $tag['slug'] ? 'bg-primary' : 'bg-white/5 border border-white/10 hover:bg-white/10' ?> px-6 transition-all cursor-pointer relative z-10">
                            <p class="<?= $currentTag === $tag['slug'] ? 'text-white' : 'text-white/60' ?> text-xs font-bold tracking-widest uppercase"><?= htmlspecialchars($tag['name']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Insights List -->
    <main class="px-6 md:px-20 lg:px-40 pb-40">
        <div class="flex flex-col">
            <?php if (empty($blogs)): ?>
                <div class="text-center py-20">
                    <p class="text-slate-400 text-lg">No insights found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($blogs as $index => $blog): ?>
                    <div class="list-item-hover group relative flex flex-col md:flex-row gap-8 py-16 border-b border-white/10 cursor-pointer transition-colors duration-500 hover:bg-white/[0.02]">
                        <a href="<?= $appUrl ?>/blog.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="absolute inset-0 z-20"></a>
                        <div class="flex items-start gap-8 flex-1 relative z-10 pointer-events-none">
                            <span class="post-number text-white/20 text-4xl md:text-6xl font-black italic"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-4">
                                    <span class="text-primary text-[10px] font-bold tracking-[0.2em] uppercase border border-primary/40 px-3 py-1 rounded"><?= htmlspecialchars($blog['category_name'] ?? 'INSIGHTS') ?></span>
                                    <span class="text-slate-500 text-[10px] font-medium tracking-widest uppercase"><?= $blog['estimatedRead'] ?> MIN READ</span>
                                </div>
                                <h3 class="text-white text-3xl md:text-4xl lg:text-5xl font-bold leading-tight max-w-2xl hover:text-primary transition-colors">
                                    <?= htmlspecialchars($blog['title']) ?>
                                </h3>
                                <p class="text-slate-400 text-base md:text-lg max-w-xl leading-relaxed">
                                    <?= htmlspecialchars($blog['excerpt']) ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($blog['featured_image']): ?>
                            <div class="relative w-full md:w-80 lg:w-96 overflow-hidden rounded-lg grainy-overlay thumbnail-glow transition-all duration-500">
                                <div class="aspect-video w-full bg-cover bg-center thumbnail-img" style="background-image: url('<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($blog['featured_image']) ?>');"></div>
                            </div>
                        <?php endif; ?>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden lg:block z-10">
                            <span class="material-symbols-outlined text-primary text-4xl">arrow_right_alt</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-24 flex justify-center gap-4">
                <?php if ($page > 1): ?>
                    <a href="<?= $appUrl ?>/blog_list.php?page=<?= $page - 1 ?><?= !empty($_GET['tag']) ? '&tag=' . htmlspecialchars($_GET['tag']) : '' ?><?= !empty($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '' ?>" class="flex items-center gap-4 px-10 py-5 rounded-full border border-white/10 hover:border-primary/50 transition-all text-white font-bold group">
                        <span class="material-symbols-outlined group-hover:-translate-y-1 transition-transform">expand_less</span>
                        PREVIOUS
                    </a>
                <?php endif; ?>
                
                <div class="flex items-center gap-2">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= $appUrl ?>/blog_list.php?page=<?= $i ?><?= !empty($_GET['tag']) ? '&tag=' . htmlspecialchars($_GET['tag']) : '' ?><?= !empty($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '' ?>" class="w-10 h-10 flex items-center justify-center rounded-full <?= $page === $i ? 'bg-primary text-white' : 'border border-white/10 text-white hover:border-primary/50' ?> transition-all">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $appUrl ?>/blog_list.php?page=<?= $page + 1 ?><?= !empty($_GET['tag']) ? '&tag=' . htmlspecialchars($_GET['tag']) : '' ?><?= !empty($_GET['category']) ? '&category=' . htmlspecialchars($_GET['category']) : '' ?>" class="flex items-center gap-4 px-10 py-5 rounded-full border border-white/10 hover:border-primary/50 transition-all text-white font-bold group">
                        LOAD MORE INTEL
                        <span class="material-symbols-outlined group-hover:translate-y-1 transition-transform">expand_more</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
