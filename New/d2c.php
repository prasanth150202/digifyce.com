<?php include __DIR__ . '/app/views/header.php'; ?>
<?php $pageTitle = 'Direct to Consumer | Digifyce'; ?>


<style>
		.testimonial-innovative video {
			max-height: 340px;
			object-fit: cover;
			width: 100%;
			background: #000;
			display: block;
		}
	.testimonial-innovative {
		
		position: relative;
		overflow: hidden;
	}
	.testimonial-innovative::before {
		content: '';
		position: absolute;
		top:100px; left: -100px;
		width: 400px; height: 400px;
		background: radial-gradient(circle, #00d9ff33 0%, transparent 80%);
		z-index: 0;
		filter: blur(10px);
	}
	.testimonial-innovative::after {
		content: '';
		position: absolute;
		bottom: -120px; right: -120px;
		width: 500px; height: 500px;
		background: radial-gradient(circle, #8b5cf633 0%, transparent 80%);
		z-index: 0;
		filter: blur(12px);
	}
	.testimonial-fade {
		opacity: 0;
		transform: translateY(40px) scale(0.98);
		transition: all 1s cubic-bezier(.23,1,.32,1);
	}
	.testimonial-fade.visible {
		opacity: 1;
		transform: translateY(0) scale(1);
	}
	.testimonial-quote {
		font-size: 3rem;
		color: #00d9ff;
		opacity: 0.15;
		position: absolute;
		top: -30px; left: -20px;
		z-index: 1;
		pointer-events: none;
	}
	.testimonial-avatar {
		width: 64px; height: 64px;
		border-radius: 50%;
		object-fit: cover;
		border: 3px solid #00d9ff;
		box-shadow: 0 4px 24px #00d9ff33;
		margin-bottom: 1rem;
		background: #fff;
		position: relative;
		z-index: 2;
		animation: floatAvatar 4s ease-in-out infinite alternate;
	}
	@keyframes floatAvatar {
		0% { transform: translateY(0); }
		100% { transform: translateY(-12px); }
	}
</style>

<section class="py-32 px-6 lg:px-12 bg-[var(--navy-black)]">
<div class="max-w-[1600px] mx-auto">
<div class="mb-20">
<span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-4 block">Quantifiable Comparison</span>
<h2 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">Industry Verticals &amp; Performance Benchmarks</h2>
<p class="text-white/40 max-w-2xl text-lg">A multi-level analysis comparing standard agency performance against Digifyce's engineered approach.</p>
</div>
<!-- Filter Tags -->
<div class="flex flex-wrap gap-4 mb-8">
    <button class="industry-filter-tag bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] font-bold px-4 py-2 rounded-full text-xs uppercase tracking-widest border border-[var(--electric-blue)] transition-all hover:bg-[var(--electric-blue)] hover:text-white active" data-industry="all">All</button>
    <button class="industry-filter-tag bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] font-bold px-4 py-2 rounded-full text-xs uppercase tracking-widest border border-[var(--electric-blue)] transition-all hover:bg-[var(--electric-blue)] hover:text-white" data-industry="apparel">Clothing & Apparel</button>
    <button class="industry-filter-tag bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] font-bold px-4 py-2 rounded-full text-xs uppercase tracking-widest border border-[var(--electric-blue)] transition-all hover:bg-[var(--electric-blue)] hover:text-white" data-industry="fmcg">FMCG & CPG</button>
    <button class="industry-filter-tag bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] font-bold px-4 py-2 rounded-full text-xs uppercase tracking-widest border border-[var(--electric-blue)] transition-all hover:bg-[var(--electric-blue)] hover:text-white" data-industry="b2b">B2B SaaS</button>
    <button class="industry-filter-tag bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] font-bold px-4 py-2 rounded-full text-xs uppercase tracking-widest border border-[var(--electric-blue)] transition-all hover:bg-[var(--electric-blue)] hover:text-white" data-industry="skincare">Skin Care & Beauty</button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b border-white/10">
<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Industry Segment</th>
<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Core Performance Metric</th>
<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Market Benchmark</th>
<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold text-white/40">Digifyce Avg.</th>
<th class="py-6 px-4 text-[11px] uppercase tracking-[0.2em] font-bold" style="color: var(--electric-blue);">Performance Lift</th>
</tr>
</thead>
<tbody class="divide-y divide-white/5">
<tr class="data-table-row">
<td class="py-10 px-4 align-top border-r border-white/5 industry-group" data-industry="apparel" rowspan="3">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined" style="color: var(--electric-blue);">apparel</span>
<span class="font-bold text-lg">Clothing &amp; Apparel</span>
</div>
</td>
<td class="py-6 px-4 text-sm text-white/60 italic">Blended ROAS</td>
<td class="py-6 px-4 font-mono text-sm">2.4x</td>
<td class="py-6 px-4 font-bold text-lg">5.8x</td>
<td class="py-6 px-4 text-green-400 font-bold">+141%</td>
</tr>
<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">LTV/CAC Ratio</td>
<td class="py-6 px-4 font-mono text-sm">2.1:1</td>
<td class="py-6 px-4 font-bold text-lg">4.2:1</td>
<td class="py-6 px-4 text-green-400 font-bold">+100%</td>
</tr>
<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Purchase Frequency</td>
<td class="py-6 px-4 font-mono text-sm">1.8 / yr</td>
<td class="py-6 px-4 font-bold text-lg">3.2 / yr</td>
<td class="py-6 px-4 text-green-400 font-bold">+77%</td>
</tr>
<tr class="data-table-row border-t border-white/10">
<td class="py-10 px-4 align-top border-r border-white/5 industry-group" data-industry="fmcg" rowspan="3">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined" style="color: var(--electric-blue);">fastfood</span>
<span class="font-bold text-lg">FMCG &amp; CPG</span>
</div>
</td>
<td class="py-6 px-4 text-sm text-white/60 italic">Market Share Velocity</td>
<td class="py-6 px-4 font-mono text-sm">Low-Mod</td>
<td class="py-6 px-4 font-bold text-lg">Hyper-Scale</td>
<td class="py-6 px-4 text-green-400 font-bold">+210%</td>
</tr>
<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Subscription MoM</td>
<td class="py-6 px-4 font-mono text-sm">+4.2%</td>
<td class="py-6 px-4 font-bold text-lg">+24.5%</td>
<td class="py-6 px-4 text-green-400 font-bold">+483%</td>
</tr>
<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Amazon ACR</td>
<td class="py-6 px-4 font-mono text-sm">12.1%</td>
<td class="py-6 px-4 font-bold text-lg">18.2%</td>
<td class="py-6 px-4 text-green-400 font-bold">+50%</td>
</tr>
<tr class="data-table-row border-t border-white/10">
<td class="py-10 px-4 align-top border-r border-white/5 industry-group" data-industry="b2b" rowspan="2">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined" style="color: var(--electric-blue);">business_center</span>
<span class="font-bold text-lg">B2B SaaS</span>
</div>
</td>
<td class="py-6 px-4 text-sm text-white/60 italic">Lead-to-Close Rate</td>
<td class="py-6 px-4 font-mono text-sm">2.5%</td>
<td class="py-6 px-4 font-bold text-lg">8.4%</td>
<td class="py-6 px-4 text-green-400 font-bold">+236%</td>
</tr>
<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Avg. Deal Cycle</td>
<td class="py-6 px-4 font-mono text-sm">120 days</td>
<td class="py-6 px-4 font-bold text-lg">74 days</td>
<td class="py-6 px-4 text-green-400 font-bold">-38% <span class="text-[10px]">(Time)</span></td>
</tr>
<tr class="data-table-row border-t border-white/10">
<td class="py-10 px-4 align-top border-r border-white/5 industry-group" data-industry="skincare" rowspan="3">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined" style="color: var(--electric-blue);">spa</span>
<span class="font-bold text-lg">Skin Care &amp; Beauty</span>
</div>
</td>
<td class="py-6 px-4 text-sm text-white/60 italic">Conversion Rate</td>
<td class="py-6 px-4 font-mono text-sm">1.9%</td>
<td class="py-6 px-4 font-bold text-lg">4.6%</td>
<td class="py-6 px-4 text-green-400 font-bold">+142%</td>
</tr>

<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Repeat Purchase Rate</td>
<td class="py-6 px-4 font-mono text-sm">22%</td>
<td class="py-6 px-4 font-bold text-lg">41%</td>
<td class="py-6 px-4 text-green-400 font-bold">+86%</td>
</tr>

<tr class="data-table-row">
<td class="py-6 px-4 text-sm text-white/60 italic">Customer Lifetime Value</td>
<td class="py-6 px-4 font-mono text-sm">₹4,200</td>
<td class="py-6 px-4 font-bold text-lg">₹8,900</td>
<td class="py-6 px-4 text-green-400 font-bold">+112%</td>
</tr>

</tbody>
</table>
</div>
<script>
// Industry Table Filter
document.querySelectorAll('.industry-filter-tag').forEach(tag => {
    tag.addEventListener('click', function() {
        document.querySelectorAll('.industry-filter-tag').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const industry = this.getAttribute('data-industry');
        document.querySelectorAll('.industry-group').forEach(cell => {
            const row = cell.parentElement;
            if (industry === 'all') {
                row.style.display = '';
                // Show all related rows
                let next = row.nextElementSibling;
                let count = cell.getAttribute('rowspan') ? parseInt(cell.getAttribute('rowspan')) : 1;
                for (let i = 1; i < count; i++) {
                    if (next) { next.style.display = ''; next = next.nextElementSibling; }
                }
            } else {
                if (cell.getAttribute('data-industry') === industry) {
                    row.style.display = '';
                    // Show all related rows
                    let next = row.nextElementSibling;
                    let count = cell.getAttribute('rowspan') ? parseInt(cell.getAttribute('rowspan')) : 1;
                    for (let i = 1; i < count; i++) {
                        if (next) { next.style.display = ''; next = next.nextElementSibling; }
                    }
                } else {
                    row.style.display = 'none';
                    // Hide all related rows
                    let next = row.nextElementSibling;
                    let count = cell.getAttribute('rowspan') ? parseInt(cell.getAttribute('rowspan')) : 1;
                    for (let i = 1; i < count; i++) {
                        if (next) { next.style.display = 'none'; next = next.nextElementSibling; }
                    }
                }
            }
        });
    });
});
</script>
</div>
</div>
</section>
<!-- ===============================
   TESTIMONIAL SECTION START
================================ -->
<section class="testimonial-section py-24 bg-[#05070a]">

<style>
/* ===== SECTION ===== */
.testimonial-section {
    position: relative;
    overflow: hidden;
}

/* ===== SMOKE EFFECT ===== */
.testimonial-section::before,
.testimonial-section::after {
    content: '';
    position: absolute;
    width: 650px;
    height: 650px;
    filter: blur(140px);
    opacity: 0.10;
    z-index: 0;
    animation: smokeMove 30s linear infinite alternate;
}

.testimonial-section::before {
    background: radial-gradient(circle, #00d9ff 0%, transparent 70%);
    top: -250px;
    left: -200px;
}

.testimonial-section::after {
    background: radial-gradient(circle, #8b5cf6 0%, transparent 70%);
    bottom: -250px;
    right: -200px;
}

@keyframes smokeMove {
    from { transform: translate(0,0); }
    to { transform: translate(140px,-80px); }
}

/* ===== TITLE ===== */
.testimonial-title {
    text-align: center;
    font-size: 40px;
    font-weight: 700;
    margin-bottom: 60px;
}

/* ===== CAROUSEL ===== */
.testimonial-wrapper {
    overflow: hidden;
    position: relative;
    z-index: 2;
}

.testimonial-track {
    display: flex;
    gap: 40px;
    width: max-content;
    will-change: transform;
}

/* ===== CARD ===== */
.testimonial-card {
    width: 420px;
    flex-shrink: 0;
}

.testimonial-card video {
    width: 100%;
    border-radius: 16px;
    border: 3px solid rgba(0,217,255,0.2);
    background: #000;
}

.testimonial-content {
    margin-top: 16px;
}

.testimonial-content h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 6px;
}

.testimonial-content p {
    font-size: 15px;
    color: rgba(255,255,255,0.75);
    line-height: 1.6;
}

/* MOBILE */
@media(max-width:768px){
    .testimonial-card {
        width: 320px;
    }
}
/* ===== VIDEO MODAL ===== */
.video-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999999;
}

.video-modal.active {
    display: flex;
}

.video-modal-content {
    width: 100%;
    max-width: 1080px;
    padding: 0 16px;
    position: relative;
}
.video-modal video {
    width: 100%;
    max-height: 90vh;
    object-fit: contain;
}


.video-close {
    position: absolute;
    top: -0px;
    right: 0;
    font-size: 32px;
    cursor: pointer;
    color: #fff;
}

</style>

<div class="relative z-10 max-w-[1600px] mx-auto px-6">

    <h2 class="testimonial-title text-white">
        Client Video Testimonials
    </h2>
<div id="videoModal" class="video-modal">
    <div class="video-modal-content">
        <span class="video-close">&times;</span>
        <video id="modalVideo" controls autoplay></video>
    </div>
</div>

    <div class="testimonial-wrapper">
        <div class="testimonial-track" id="testimonialTrack">

            <!-- CARD 1 -->
            <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/Aadhya.mp4"
       poster="public/assets/testimonials/thumbnails/Aadhya.jpg"
       preload="none">
</video>
                <div class="testimonial-content text-white">
                    <h3>AADHYA</h3>
                    <p>Digifyce helped us scale our herbal care brand online. Their performance strategy delivered consistent growth.</p>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/Bawse Baby.mp4"
       poster="public/assets/testimonials/thumbnails/Bawse Baby.jpg"
       preload="none">
</video>
                <div class="testimonial-content text-white">
                    <h3>BAWSE BABY</h3>
                    <p>The team is creative, responsive, and deeply invested in our growth. Execution quality is excellent.</p>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/Aishwaryam.mp4"
       poster="public/assets/testimonials/thumbnails/Aishwaryam.jpg"
       preload="none">
                </video>
                <div class="testimonial-content text-white">
                    <h3>AISHWARYAM</h3>
                    <p>From strategy to execution, Digifyce exceeded expectations and helped strengthen our online presence.</p>
                </div>
            </div>

              <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/lushra.mp4"
       poster="public/assets/testimonials/thumbnails/lushra.png"
       preload="none">
                </video>
                <div class="testimonial-content text-white">
                    <h3>LUSHRA</h3>
                    <p>They are a game-changer in digital marketing. Their strategic approach and execution are unmatched.</p>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
const track = document.getElementById('testimonialTrack');
track.innerHTML += track.innerHTML;

let position = 0;
let speed = 0.35;
let paused = false;

track.addEventListener('mouseenter', () => paused = true);
track.addEventListener('mouseleave', () => paused = false);

function animateTestimonials() {
    if (!paused) {
        position += speed;
        if (position >= track.scrollWidth / 2) {
            position = 0;
        }
        track.style.transform = `translateX(-${position}px)`;
    }
    requestAnimationFrame(animateTestimonials);
}

animateTestimonials();



const modal = document.getElementById('videoModal');
const modalVideo = document.getElementById('modalVideo');
const closeBtn = document.querySelector('.video-close');

document.querySelectorAll('.testimonial-video').forEach(video => {
    video.addEventListener('click', () => {
        const src = video.getAttribute('data-video');
        modalVideo.src = src;
        modal.classList.add('active');
        modalVideo.play();
    });
});

closeBtn.addEventListener('click', () => {
    modal.classList.remove('active');
    modalVideo.pause();
    modalVideo.src = '';
});

modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
        modalVideo.pause();
        modalVideo.src = '';
    }
});

</script>

</section>
<!-- ===============================
   TESTIMONIAL SECTION END
================================ -->


<?php include __DIR__ . '/app/views/footer.php'; ?>
