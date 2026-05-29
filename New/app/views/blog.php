<?php
$pageTitle = ($blog['title'] ?? 'Blog') . ' | Digifyce Intel';
$bodyClass = 'bg-background-dark font-display text-white selection:bg-primary/30';
$excerpt = $blog['excerpt'] ?? '';
$categoryName = $blog['category_name'] ?? 'INSIGHTS';
$categorySlug = $blog['category_slug'] ?? '';
$featuredImage = $blog['featured_image'] ?? '';
$authorName = $blog['author_name'] ?? '';
$authorAvatar = $blog['author_avatar'] ?? '';
$authorBio = $blog['author_bio'] ?? '';
$shareTitle = htmlspecialchars($blog['title'] ?? 'Digifyce Intel');

$tailwindConfig = '<script id="tailwind-config">
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
</script>';

$extraHead = '<style type="text/tailwindcss">
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
        .glow-text {
            text-shadow: 0 0 20px rgba(13, 105, 242, 0.4);
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .progress-bar {
            transform-origin: left;
            animation: scroll-progress auto linear;
            animation-timeline: scroll();
        }
        @keyframes scroll-progress {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
    }
</style>';

include __DIR__ . '/header.php';
?>



    <section class="relative h-screen flex items-end grainy-overlay overflow-hidden">
        <?php if (!empty($featuredImage)): ?>
            <div class="absolute inset-0 z-0 scale-110 grayscale" style="background-image: url('<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($featuredImage) ?>'); background-size: cover; background-position: center; filter: contrast(1.2) brightness(0.4);"></div>
        <?php else: ?>
            <div class="absolute inset-0 z-0 bg-gradient-to-br from-background-dark via-black to-slate-900"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent z-1"></div>
        <div class="relative z-10 w-full px-6 md:px-20 lg:px-40 pb-20">
            <div class="flex items-center gap-4 mb-8">
                <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase border border-primary/40 px-4 py-1.5 rounded-full"><?= htmlspecialchars($categoryName) ?></span>
                <span class="text-slate-400 text-xs font-medium tracking-widest"><?= htmlspecialchars($publishedDate) ?> // <?= $estimatedRead ?> MIN READ</span>
            </div>
            <h1 class="text-white text-6xl md:text-8xl lg:text-9xl font-black leading-[0.85] tracking-tighter uppercase max-w-6xl">
                <?= htmlspecialchars($blog['title']) ?>
            </h1>
        </div>
    </section>

    <main class="relative px-6 md:px-20 lg:px-40 py-24 flex flex-col lg:flex-row gap-20">
        <aside class="hidden lg:block w-72 shrink-0">
            <div class="sticky top-24 space-y-12">
                <?php if (!empty($blog['tags'])): ?>
                    <div>
                        <h4 class="text-primary text-[10px] font-bold tracking-[0.3em] uppercase mb-6">Key Takeaways</h4>
                        <ul class="space-y-4">
                            <?php foreach ($blog['tags'] as $tag): ?>
                                <li class="text-slate-400 text-sm leading-relaxed border-l-2 border-primary/20 pl-4 py-1 hover:border-primary transition-colors">
                                    <a href="<?= $appUrl ?>/blog_list.php?tag=<?= htmlspecialchars($tag['slug']) ?>" class="hover:text-white">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($authorName)): ?>
                    <div>
                        <h4 class="text-primary text-[10px] font-bold tracking-[0.3em] uppercase mb-6">Author Bio</h4>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="size-10 rounded-full bg-slate-800 flex items-center justify-center border border-white/10 overflow-hidden">
                                <?php if (!empty($authorAvatar)): ?>
                                    <img src="<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($authorAvatar) ?>" alt="<?= htmlspecialchars($authorName) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-slate-400">person</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-white text-sm font-bold"><?= htmlspecialchars($authorName) ?></p>
                                <p class="text-primary text-[10px] font-bold tracking-widest uppercase">Contributor</p>
                            </div>
                        </div>
                        <?php if (!empty($authorBio)): ?>
                            <p class="text-slate-400 text-xs leading-relaxed mb-4"><?= htmlspecialchars($authorBio) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="flex gap-4">
                    <button onclick="navigator.share({title: '<?= $shareTitle ?>', url: window.location.href})" class="size-10 rounded-full border border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary transition-all group">
                        <span class="material-symbols-outlined text-sm text-slate-400 group-hover:text-white">share</span>
                    </button>
                    <button onclick="navigator.clipboard.writeText(window.location.href)" class="size-10 rounded-full border border-white/10 flex items-center justify-center hover:bg-primary hover:border-primary transition-all group">
                        <span class="material-symbols-outlined text-sm text-slate-400 group-hover:text-white">content_copy</span>
                    </button>
                </div>
            </div>
        </aside>

        <article class="flex-1 max-w-3xl">
            <div class="prose prose-invert prose-lg">
                <?php if (!empty($excerpt)): ?>
                    <p class="text-slate-300 text-xl leading-relaxed mb-8 font-light italic">
                        <?= htmlspecialchars($excerpt) ?>
                    </p>
                <?php endif; ?>
                <div class="text-slate-300 leading-relaxed space-y-6">
                    <?php echo $blog['content']; ?>
                </div>
            </div>

            <div class="mt-24 pt-12 border-t border-white/10 flex items-center justify-between flex-wrap gap-4">
                <div class="flex gap-4">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest">Filed under:</span>
                    <?php if (!empty($categoryName)): ?>
                        <a class="text-primary text-xs font-bold uppercase tracking-widest hover:underline" href="<?= $appUrl ?>/blog_list.php?category=<?= htmlspecialchars($categorySlug) ?>">
                            <?= htmlspecialchars($categoryName) ?>
                        </a>
                    <?php endif; ?>
                    <?php foreach ($blog['tags'] as $tag): ?>
                        <a class="text-primary text-xs font-bold uppercase tracking-widest hover:underline" href="<?= $appUrl ?>/blog_list.php?tag=<?= htmlspecialchars($tag['slug']) ?>">
                            <?= htmlspecialchars($tag['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>
    </main>

    <?php if ($nextBlog): ?>
        <section class="mt-20 border-t border-white/10">
            <div class="grid md:grid-cols-2 h-[600px]">
                <div class="p-12 md:p-24 flex flex-col justify-center gap-6 border-r border-white/10 bg-black">
                    <p class="text-primary text-xs font-bold tracking-[0.3em] uppercase">Up Next</p>
                    <h2 class="text-white text-4xl md:text-6xl font-bold leading-tight tracking-tight uppercase">
                        <?= htmlspecialchars($nextBlog['title']) ?>
                    </h2>
                    <a class="flex items-center gap-4 text-white font-bold group mt-4" href="<?= $appUrl ?>/blog.php?slug=<?= htmlspecialchars($nextBlog['slug']) ?>">
                        READ NEXT INTEL
                        <span class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_right_alt</span>
                    </a>
                </div>
                <div class="relative grainy-overlay grayscale hover:grayscale-0 transition-all duration-700 cursor-pointer hidden md:block">
                    <?php if (!empty($nextBlog['featured_image'])): ?>
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($nextBlog['featured_image']) ?>');"></div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-primary/20 mix-blend-overlay"></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
