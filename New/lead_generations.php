<?php 
$pageTitle = 'Lead Generation | Digifyce';
$bodyClass = 'bg-[#05070a] text-white';
include __DIR__ . '/app/views/header.php'; 
?>



<main class="min-h-screen bg-[var(--navy-black)] pt-32 pb-20">
	<section class="max-w-[1440px] mx-auto px-4 mb-16">
		  <span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block">
            Lead Generation
        </span>
		<h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">Digifyce Lead Generation</h1>
		<p class="text-white/60 text-lg mb-4">Digifyce delivers hyper-precision lead generation for both <span class="text-white">B2B and B2C</span> brands—including real estate, retail, and enterprise—focusing on <span class="text-white">intent-based lead quality</span> and measurable business outcomes.</p>
		<div class="flex flex-wrap gap-2 mb-6">
			<span class="metric-badge bg-white/5 text-white/60">ABM Logic</span>
			<span class="metric-badge bg-white/5 text-white/60">Lead Scoring</span>
			<span class="metric-badge bg-white/5 text-white/60">Predictive Modeling</span>
		</div>
		<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
			<div class="bg-[var(--navy-black)] p-6 border border-white/5 rounded-xl">
				<p class="text-[10px] uppercase text-white/40 mb-2">MQL to SQL Conv.</p>
				<p class="text-3xl font-bold">38%</p>
				<p class="text-[10px] text-green-400 mt-2 font-bold">+18% vs avg.</p>
			</div>
			<div class="bg-[var(--navy-black)] p-6 border border-white/5 rounded-xl">
				<p class="text-[10px] uppercase text-white/40 mb-2">CPL Efficiency</p>
				<p class="text-3xl font-bold">$42.50</p>
				<p class="text-[10px] text-[var(--electric-blue)] mt-2 font-bold">-22% vs benchmark</p>
			</div>
			<div class="bg-[var(--navy-black)] p-6 border border-white/5 rounded-xl">
				<p class="text-[10px] uppercase text-white/40 mb-2">Lead Decay Rate</p>
				<p class="text-3xl font-bold">0.8%</p>
				<p class="text-[10px] text-white/20 mt-2 font-bold">Ultra-low</p>
			</div>
		</div>

			<!-- New Industry Performance Section: Card Style -->
			<section class="mb-10">
				<span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-4 block">Industry Impact Snapshots</span>
				<h2 class="text-2xl font-bold tracking-tight mb-8">Performance Across Verticals</h2>
				   <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
					   <!-- Card 1: Real Estate -->
					   <div class="bg-gradient-to-br from-[#0a0f1a] to-[#1a2a3a] border border-white/10 rounded-2xl p-8 shadow-xl flex flex-col gap-4">
						   <div class="flex items-center gap-3 mb-2">
							   <span class="material-symbols-outlined text-3xl text-[var(--electric-blue)]">home</span>
							   <span class="font-bold text-lg">Real Estate</span>
						   </div>
						   <div class="flex flex-col gap-2">
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Site-to-Visit Rate</span><span class="font-mono text-white">7% → <span class="text-green-400 font-bold">19%</span></span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Walk-in Conversions</span><span class="font-mono text-white">2.2% → <span class="text-green-400 font-bold">6.5%</span></span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Lead-to-Booking</span><span class="font-mono text-white">1.1% → <span class="text-green-400 font-bold">3.8%</span></span>
							   </div>
						   </div>
						   <div class="mt-4 text-xs text-white/40 italic">“Hyperlocal targeting and nurturing increased site visits and bookings.”</div>
					   </div>
					   <!-- Card 2: Store Walk-ins -->
					   <div class="bg-gradient-to-br from-[#0a0f1a] to-[#0e2239] border border-white/10 rounded-2xl p-8 shadow-xl flex flex-col gap-4">
						   <div class="flex items-center gap-3 mb-2">
							   <span class="material-symbols-outlined text-3xl text-[var(--electric-blue)]">storefront</span>
							   <span class="font-bold text-lg">Store Walk-ins</span>
						   </div>
						   <div class="flex flex-col gap-2">
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Footfall Growth</span><span class="font-mono text-white">+62%</span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Repeat Visits</span><span class="font-mono text-white">1.3x → <span class="text-green-400 font-bold">2.7x</span></span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>In-store Conversions</span><span class="font-mono text-white">3.5% → <span class="text-green-400 font-bold">7.2%</span></span>
							   </div>
						   </div>
						   <div class="mt-4 text-xs text-white/40 italic">“Omnichannel campaigns drove measurable in-store results.”</div>
					   </div>
					   <!-- Card 3: B2B Clients -->
					   <div class="bg-gradient-to-br from-[#0a0f1a] to-[#223a2a] border border-white/10 rounded-2xl p-8 shadow-xl flex flex-col gap-4">
						   <div class="flex items-center gap-3 mb-2">
							   <span class="material-symbols-outlined text-3xl text-[var(--electric-blue)]">business_center</span>
							   <span class="font-bold text-lg">B2B Clients</span>
						   </div>
						   <div class="flex flex-col gap-2">
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Pipeline Value</span><span class="font-mono text-white">+$4M</span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>MQL to SQL Rate</span><span class="font-mono text-white">22% → <span class="text-green-400 font-bold">38%</span></span>
							   </div>
							   <div class="flex justify-between text-white/60 text-xs">
								   <span>Sales Cycle</span><span class="font-mono text-white">-28% duration</span>
							   </div>
						   </div>
						   <div class="mt-4 text-xs text-white/40 italic">“Intent-based targeting shortened sales cycles and boosted pipeline.”</div>
					   </div>
				   </div>
			</section>
		</section>

<script>

gsap.registerPlugin(ScrollTrigger);

/* DESKTOP ONLY */
let mm = gsap.matchMedia();

mm.add("(min-width: 1025px)", () => {

    const track = document.querySelector(".horizontal-track");
    const panels = gsap.utils.toArray(".panel");

    gsap.to(track, {
        xPercent: -100 * (panels.length - 1),
        ease: "none",
        scrollTrigger: {
            trigger: "#tech-stack-section",
            pin: true,
            scrub: 1,
            end: () => "+=" + track.offsetWidth
        }
    });

});

/* IMAGE SLIDERS */
gsap.utils.toArray(".image-slider").forEach(slider => {

    const images = slider.querySelectorAll(".slider-img");

    gsap.to(slider, {
        xPercent: -100 * (images.length - 1),
        duration: 12,
        ease: "none",
        repeat: -1
    });

});

</script>


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
       data-video="public/assets/testimonials/videos/1.mp4"
       poster="public/assets/testimonials/thumbnails/1.png"
       preload="none">
</video>
                <div class="testimonial-content text-white">
             
                    <p>They brought huge crowds to our expo, and Digifyce delivers real results.</p>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/2.mp4"
       poster="public/assets/testimonials/thumbnails/2.png"
       preload="none">
</video>
                <div class="testimonial-content text-white">
                    
                    <p>Really grateful to Digifyce.</p>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="testimonial-card">
                <video class="testimonial-video"
       data-video="public/assets/testimonials/videos/Sweet Smith.mp4"
       poster="public/assets/testimonials/thumbnails/Sweet Smith.jpg"
       preload="none">
                </video>
                <div class="testimonial-content text-white">
                    
                    <p>Digifyce transformed our Diwali sale into 10× growth.</p>
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

		</main>
		<?php include __DIR__ . '/app/views/footer.php'; ?>
