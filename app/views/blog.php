<?php
$pageTitle = ($blog['title'] ?? 'Blog') . ' | Digifyce';
$bodyClass = 'bg-background-dark font-display text-white selection:bg-primary/30';
$excerpt = $blog['excerpt'] ?? '';
$categoryName = $blog['category_name'] ?? 'INSIGHTS';
$categorySlug = $blog['category_slug'] ?? '';
$featuredImage = $blog['featured_image'] ?? '';
$authorName = $blog['author_name'] ?? '';
$authorAvatar = $blog['author_avatar'] ?? '';
$authorBio = $blog['author_bio'] ?? '';
$shareTitle = htmlspecialchars($blog['title'] ?? 'Digifyce');

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
</style>
<style>

.blog-content h2 {
    font-size: 1.6rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.blog-content h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.blog-content p {
    margin-bottom: 1rem;
    line-height: 1.8;
}

.blog-content ul,
.blog-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.blog-content li {
    margin-bottom: 0.4rem;
    line-height: 1.7;
}

.blog-content a {
    color: #0d6efd;
    text-decoration: underline;
}

.blog-content strong {
    font-weight: 700;
}
    /* Preserve TinyMCE content formatting and alignment */
    .blog-content p[style*="text-align: left"] {
        text-align: left;
    }
    .blog-content p[style*="text-align: center"],
    .blog-content p[style*="text-align:center"] {
        text-align: center;
    }
    .blog-content p[style*="text-align: right"],
    .blog-content p[style*="text-align:right"] {
        text-align: right;
    }
    .blog-content p[style*="text-align: justify"],
    .blog-content p[style*="text-align:justify"] {
        text-align: justify;
    }
    .blog-content h1[style*="text-align"],
    .blog-content h2[style*="text-align"],
    .blog-content h3[style*="text-align"],
    .blog-content h4[style*="text-align"],
    .blog-content h5[style*="text-align"],
    .blog-content h6[style*="text-align"],
    .blog-content div[style*="text-align"] {
        text-align: inherit;
    }
    /* Remove Word/clipboard paste artifacts */
    .blog-content span[class*="SCXW"],
    .blog-content span[class*="BCX"],
    .blog-content span[class*="NormalTextRun"],
    .blog-content span[class*="TextRun"] {
        all: unset !important;
        display: inline !important;
        font: inherit !important;
        color: inherit !important;
    }
    .blog-content img {
        max-width: 100%;
        height: auto;
    }
    .blog-content img[style*="float: left"] {
        float: left;
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
    .blog-content img[style*="float: right"] {
        float: right;
        margin-left: 1rem;
        margin-bottom: 0.5rem;
    }
    .blog-content ul, .blog-content ol {
        margin-left: 1.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .blog-content ul {
        list-style-type: disc;
    }
    .blog-content ol {
        list-style-type: decimal;
    }
    .blog-content li {
        margin-bottom: 0.25rem;
    }
    .blog-content blockquote {
        border-left: 4px solid #0d69f2;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        color: #94a3b8;
    }
    .blog-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }
    .blog-content table td, .blog-content table th {
        border: 1px solid #334155;
        padding: 0.5rem;
    }
    .blog-content table th {
        background-color: #1e293b;
        font-weight: bold;
    }
    .sidebar-scroll {
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
        transition: scrollbar-color 0.2s;
    }
    .sidebar-scroll:hover {
        scrollbar-color: rgba(255,255,255,0.15) transparent;
    }
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: transparent;
        border-radius: 2px;
        transition: background 0.2s;
    }
    .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
</style>';

include __DIR__ . '/header.php';
?>



    <section class="relative min-h-[40vh] md:min-h-screen flex items-end grainy-overlay overflow-hidden">
        <?php if (!empty($featuredImage)): ?>
            <div class="absolute inset-0 z-0 grayscale bg-[center_top] bg-no-repeat mt-[100px]"
                style="background-image: url('<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($featuredImage) ?>'); filter: contrast(1.2) brightness(0.4); background-size : 80%"></div>
        <?php else: ?>
            <div class="absolute inset-0 z-0 bg-gradient-to-br from-background-dark via-black to-slate-900"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent z-[1]"></div>
        <div class="relative z-10 w-full px-6 md:px-20 lg:px-40 pb-20 sm:pb-24 lg:pb-28">
            <div class="flex items-center gap-4 mb-8">
                <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase border border-primary/40 px-4 py-1.5 rounded-full"><?= htmlspecialchars($categoryName) ?></span>
                <span class="text-slate-400 text-xs font-medium tracking-widest"><?= htmlspecialchars($publishedDate) ?> // <?= $estimatedRead ?> MIN READ</span>
            </div>
            <h1 class="text-white text-4xl md:text-4xl lg:text-6xl font-black leading-[0.85] tracking-tighter uppercase max-w-6xl">
                <?= htmlspecialchars($blog['title']) ?>
            </h1>
        </div>
    </section>

    <main class="relative px-6 md:px-20 lg:px-40 py-12 flex flex-col lg:flex-row gap-20">
        <aside class="hidden lg:block w-72 shrink-0">
            <!-- Sticky: tags + author + share -->
            <div class="sticky top-24 space-y-12 sidebar-scroll overflow-y-auto max-h-[calc(100vh-7rem)] pr-2">
                <?php if (!empty($blog['tags'])): ?>
                    <div>
                        <h4 class="text-primary text-[10px] font-bold tracking-[0.3em] uppercase mb-6">Key Takeaways</h4>
                        <ul class="space-y-4">
                            <?php foreach ($blog['tags'] as $tag): ?>
                                <li class="text-slate-400 text-sm leading-relaxed border-l-2 border-primary/20 pl-4 py-1 hover:border-primary transition-colors">
                                    <a href="<?= $appUrl ?>/blog_list/tag/<?= htmlspecialchars($tag['slug']) ?>" class="hover:text-white">
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

                <!-- Lead form below author -->
                <div class="border-t border-white/10 pt-10">
                    <h4 class="text-primary text-[10px] font-bold tracking-[0.3em] uppercase mb-6">Get In Touch</h4>
                    <p class="text-slate-500 text-xs mb-6 leading-relaxed">Share a few details and we'll get back with a growth plan.</p>

                    <div id="blog-lead-msg" class="hidden mb-5 rounded-lg p-4 text-xs font-medium text-center"></div>

                    <form id="blog-lead-form" class="flex flex-col gap-4">
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Full Name *</label>
                            <input name="full_name" required
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="Your name" />
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Email *</label>
                            <input type="email" name="email" required
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="you@company.com" />
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Phone</label>
                            <input type="tel" name="phone"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="+91 00000 00000" />
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Company</label>
                            <input name="company"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="Company name" />
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Monthly Budget</label>
                            <select name="budget"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-[#05070a] px-3 py-2.5 text-sm text-white focus:border-primary focus:outline-none">
                                <option value="">Select budget</option>
                                <option value="50k-1l">₹50k - ₹1L</option>
                                <option value="1l-1.5l">₹1L - ₹1.5L</option>
                                <option value="1.5l-2l">₹1.5L - ₹2L</option>
                                <option value="2l-3l">₹2L - ₹3L</option>
                                <option value="3l+">₹3L+</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Website</label>
                            <input name="website"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="https://" />
                        </div>
                        <div>
                            <label class="text-[9px] uppercase tracking-[0.3em] text-slate-500">Project Details *</label>
                            <textarea name="message" rows="4" required
                                class="mt-2 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-primary focus:outline-none"
                                placeholder="Tell us about your goals..."></textarea>
                        </div>
                        <button type="submit" id="blog-lead-btn"
                            class="w-full bg-primary text-white py-3 font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[#05070a] transition-all rounded-lg">
                            Submit Request
                        </button>
                        <p class="text-[9px] uppercase tracking-[0.2em] text-slate-600 text-center">We respond within 24 hours</p>
                    </form>

                    <script>
                    (function () {
                        var form = document.getElementById('blog-lead-form');
                        var msg  = document.getElementById('blog-lead-msg');
                        var btn  = document.getElementById('blog-lead-btn');
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            btn.disabled = true;
                            btn.textContent = 'Sending…';
                            var data = {
                                full_name: form.full_name.value.trim(),
                                email:     form.email.value.trim(),
                                phone:     form.phone.value.trim(),
                                company:   form.company.value.trim(),
                                budget:    form.budget.value,
                                website:   form.website.value.trim(),
                                message:   form.message.value.trim(),
                            };
                            fetch('<?= $appUrl ?>/app/api/lead_form_submit.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify(data),
                            })
                            .then(function(r){ return r.json(); })
                            .then(function(res) {
                                msg.className = 'mb-5 rounded-lg p-4 text-xs font-medium text-center border';
                                if (res.success) {
                                    msg.classList.add('border-green-500/30','bg-green-500/10','text-green-300');
                                    msg.textContent = '✓ ' + res.message;
                                    form.reset();
                                } else {
                                    msg.classList.add('border-red-500/30','bg-red-500/10','text-red-300');
                                    msg.textContent = '⚠ ' + res.message;
                                }
                                btn.disabled = false;
                                btn.textContent = 'Submit Request';
                            })
                            .catch(function() {
                                msg.className = 'mb-5 rounded-lg p-4 text-xs font-medium text-center border border-red-500/30 bg-red-500/10 text-red-300';
                                msg.textContent = '⚠ Something went wrong. Please try again.';
                                btn.disabled = false;
                                btn.textContent = 'Submit Request';
                            });
                        });
                    })();
                    </script>
                </div>
            </div>
        </aside>

        <article class="flex-1 max-w-3xl">
            <?php if (!empty($excerpt)): ?>
                <p class="text-slate-300 text-xl leading-relaxed mb-8 font-light italic">
                    <?= htmlspecialchars($excerpt) ?>
                </p>
            <?php endif; ?>
            <div class="blog-content text-slate-300 leading-relaxed space-y-6">
                <?php echo $blog['content']; ?>
            </div>

            <div class="mt-24 pt-12 border-t border-white/10 flex items-center justify-between flex-wrap gap-4">
                <div class="flex gap-4">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest">Filed under:</span>
                    <?php if (!empty($categoryName)): ?>
                        <a class="text-primary text-xs font-bold uppercase tracking-widest hover:underline" href="<?= $appUrl ?>/blog_list/category/<?= htmlspecialchars($categorySlug) ?>">
                            <?= htmlspecialchars($categoryName) ?>
                        </a>
                    <?php endif; ?>
                    <?php foreach ($blog['tags'] as $tag): ?>
                        <a class="text-primary text-xs font-bold uppercase tracking-widest hover:underline" href="<?= $appUrl ?>/blog_list/tag/<?= htmlspecialchars($tag['slug']) ?>">
                            <?= htmlspecialchars($tag['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>
    </main>

    <?php if (!empty($relatedBlogs)): ?>
        <section class="mt-20 border-t border-white/10">
            <div class="px-6 md:px-20 lg:px-40 py-16">
                <p class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-10">You Might Also Like</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($relatedBlogs as $rel): ?>
                    <a href="<?= $appUrl ?>/blog/<?= htmlspecialchars($rel['slug']) ?>"
                       class="group block border border-white/10 hover:border-primary/40 transition-colors overflow-hidden">
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <?php if (!empty($rel['featured_image'])): ?>
                                <div class="absolute inset-0 bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-700"
                                     style="background-image:url('<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($rel['featured_image']) ?>')"></div>
                            <?php else: ?>
                                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800"></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <div class="p-6">
                            <?php if (!empty($rel['category_name'])): ?>
                                <span class="text-primary text-[10px] font-bold tracking-[0.2em] uppercase mb-3 block">
                                    <?= htmlspecialchars($rel['category_name']) ?>
                                </span>
                            <?php endif; ?>
                            <h3 class="text-white font-bold text-base leading-snug tracking-tight uppercase mb-5 line-clamp-3">
                                <?= htmlspecialchars($rel['title']) ?>
                            </h3>
                            <span class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-widest group-hover:text-primary group-hover:gap-4 transition-all duration-200">
                                Read More
                                <span class="material-symbols-outlined text-sm">arrow_right_alt</span>
                            </span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

<!-- PDF Lead Capture Modal -->
<div id="pdfLeadModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);align-items:center;justify-content:center;padding:1rem">
    <div class="bg-[#0d1117] border border-white/10 rounded-xl w-full max-w-md p-7 relative shadow-2xl" style="max-height:90vh;overflow-y:auto">
        <button id="pdfLeadClose" type="button" style="position:absolute;top:1rem;right:1rem;background:none;border:none;color:#94a3b8;font-size:1.25rem;cursor:pointer;line-height:1" aria-label="Close">&times;</button>
        <h2 id="pdfLeadTitle" class="text-white font-bold text-xl mb-1 tracking-tight">Download PDF</h2>
        <p class="text-slate-400 text-sm mb-5">Enter your details to access the PDF.</p>
        <form id="pdfLeadForm" novalidate>
            <div id="pdfLeadFields"></div>
            <p id="pdfLeadError" class="text-red-400 text-sm mb-3" style="display:none"></p>
            <button type="submit" id="pdfLeadSubmit"
                class="w-full bg-primary text-white font-bold py-3 rounded text-sm tracking-widest uppercase transition-opacity hover:opacity-90">
                Download PDF
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    var apiUrl  = <?= json_encode(rtrim($appUrl ?? '', '/') . '/app/api/blog_pdf_lead.php') ?>;
    var blogCtx = {
        id:    <?= intval($blog['id'] ?? 0) ?>,
        slug:  <?= json_encode($blog['slug'] ?? '') ?>,
        title: <?= json_encode($blog['title'] ?? '') ?>
    };

    var modal    = document.getElementById('pdfLeadModal');
    var form     = document.getElementById('pdfLeadForm');
    var fieldsEl = document.getElementById('pdfLeadFields');
    var errorEl  = document.getElementById('pdfLeadError');
    var submitEl = document.getElementById('pdfLeadSubmit');
    var currentBtn = null;

    function openModal(btn) {
        currentBtn = btn;
        fieldsEl.innerHTML = '';
        errorEl.style.display = 'none';
        form.reset();

        var emailMode  = btn.dataset.leadEmail  || 'off';
        var phoneMode  = btn.dataset.leadPhone  || 'off';
        var cfConfig   = [];
        try { cfConfig = JSON.parse(btn.dataset.leadFields || '[]'); } catch (e) {}

        if (emailMode !== 'off') addField('lf_email', 'Email Address', 'email', emailMode === 'required');
        if (phoneMode !== 'off') addField('lf_phone', 'Phone Number', 'tel', phoneMode === 'required');
        cfConfig.forEach(function (f, i) {
            addField('lf_cf_' + i, f.label, 'text', f.required);
        });

        document.getElementById('pdfLeadTitle').textContent = btn.dataset.pdfLabel || 'Download PDF';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function addField(name, label, type, required) {
        var wrap  = document.createElement('div');
        wrap.className = 'mb-4';
        var lbl   = document.createElement('label');
        lbl.setAttribute('for', name);
        lbl.className = 'block text-sm text-slate-300 mb-1.5';
        lbl.innerHTML = label + (required ? ' <span style="color:#f87171">*</span>' : '');
        var inp   = document.createElement('input');
        inp.type  = type;
        inp.name  = name;
        inp.id    = name;
        inp.autocomplete = type === 'email' ? 'email' : type === 'tel' ? 'tel' : 'off';
        if (required) inp.required = true;
        inp.style.cssText = 'width:100%;background:#1e293b;border:1px solid rgba(255,255,255,0.15);color:#fff;border-radius:6px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box';
        inp.addEventListener('focus',  function(){ this.style.borderColor='#0d69f2'; });
        inp.addEventListener('blur',   function(){ this.style.borderColor='rgba(255,255,255,0.15)'; });
        wrap.appendChild(lbl);
        wrap.appendChild(inp);
        fieldsEl.appendChild(wrap);
    }

    document.getElementById('pdfLeadClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.style.display !== 'none') closeModal(); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorEl.style.display = 'none';

        if (!form.checkValidity()) { form.reportValidity(); return; }

        var emailMode  = currentBtn.dataset.leadEmail  || 'off';
        var phoneMode  = currentBtn.dataset.leadPhone  || 'off';
        var cfConfig   = [];
        try { cfConfig = JSON.parse(currentBtn.dataset.leadFields || '[]'); } catch (ex) {}

        var email = emailMode !== 'off' ? (form.lf_email ? form.lf_email.value.trim() : '') : '';
        var phone = phoneMode !== 'off' ? (form.lf_phone ? form.lf_phone.value.trim() : '') : '';
        var customFields = {};
        cfConfig.forEach(function (f, i) {
            var el = form['lf_cf_' + i];
            if (el) customFields[f.label] = el.value.trim();
        });

        // Read filename from data attribute (most reliable), fall back to URL parsing
        var pdfFile = currentBtn.dataset.pdfFile ? decodeURIComponent(currentBtn.dataset.pdfFile) : '';
        if (!pdfFile) {
            try { pdfFile = new URL(currentBtn.getAttribute('href'), window.location.href).searchParams.get('file') || ''; } catch (ex) {}
        }

        submitEl.disabled     = true;
        submitEl.textContent  = 'Processing…';

        fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email:        email,
                phone:        phone,
                custom_fields: customFields,
                pdf_filename: pdfFile,
                pdf_label:    currentBtn.dataset.pdfLabel || '',
                blog_id:      blogCtx.id,
                blog_slug:    blogCtx.slug,
                blog_title:   blogCtx.title
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                closeModal();
                // Navigate to the download URL — pdf-download.php sends
                // Content-Disposition: attachment so the page stays put
                window.location.href = currentBtn.getAttribute('href');
            } else {
                errorEl.textContent = res.message || 'An error occurred. Please try again.';
                errorEl.style.display = 'block';
            }
        })
        .catch(function () {
            errorEl.textContent = 'Network error. Please try again.';
            errorEl.style.display = 'block';
        })
        .finally(function () {
            submitEl.disabled    = false;
            submitEl.textContent = 'Download PDF';
        });
    });

    // Intercept clicks on lead-capture PDF buttons
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('a.digifyce-pdf-cta[data-lead-capture="1"]');
        if (!btn) return;
        e.preventDefault();
        openModal(btn);
    });
})();
</script>

<?php include __DIR__ . '/footer.php'; ?>
