<?php
$pageTitle = 'Our Products | Digifyce Growth Systems';
$bodyClass = 'bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 selection:bg-primary selection:text-white';





include __DIR__ . '/app/views/header.php';
?>

<style>
    /* =========================
   CTA SECTION STYLING
========================= */
/* =========================
   CTA SECTION STYLING
========================= */

.cta-section {
    position: relative;
    background: radial-gradient(circle at 20% 20%, rgba(0,102,255,0.25), transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(0,102,255,0.15), transparent 40%),
                #000;
}

/* Animated gradient overlay */
.animate-gradient-move {
    background-size: 200% 200%;
    animation: gradientMove 8s ease infinite;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* =========================
   PRIMARY BUTTON
========================= */

.btn-primary {
    background: linear-gradient(135deg, #0066ff, #004bcc);
    color: #fff;
    font-weight: 600;
    padding: 16px 34px;
    border-radius: 999px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,102,255,0.35);
}

.btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 50px rgba(0,102,255,0.55);
}

/* Shine animation */
.btn-primary::after {
    content: '';
    position: absolute;
    top: 0;
    left: -75%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        rgba(255,255,255,0.2),
        rgba(255,255,255,0.6),
        rgba(255,255,255,0.2)
    );
    transform: skewX(-20deg);
    transition: 0.6s;
}

.btn-primary:hover::after {
    left: 130%;
}

/* =========================
   SECONDARY BUTTON
========================= */

.btn-secondary {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    font-weight: 500;
    padding: 16px 34px;
    border-radius: 999px;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: rgba(0,102,255,0.15);
    border-color: #0066ff;
    transform: translateY(-3px);
}</style>
<!-- Font Awesome CDN (if not already included in header) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<main class="bg-background-dark">

<!-- HERO -->
<section class="relative min-h-screen flex items-center justify-center text-center px-6 bg-black hero-section">
	<div class="max-w-5xl">
		<h1 class="text-5xl md:text-7xl font-black text-white leading-tight uppercase tracking-tight hero-title">
			Our Products For <span class="text-primary">Your Growth</span>
		</h1>
		<p class="text-slate-400 mt-6 text-lg md:text-xl max-w-2xl mx-auto">
			We build structured revenue systems. Capture leads. Automate engagement. Close more deals.
		</p>
		<div class="mt-10 flex justify-center gap-6">
			<button type="button" class="btn-primary flex items-center gap-2" onclick="window.location.href='#crm'">
			<i class="fa-solid fa-chart-line"></i> Explore CRM
			</button>
			<button type="button" class="btn-secondary flex items-center gap-2" onclick="window.location.href='#zingbot'">
			<i class="fa-solid fa-robot"></i> Explore Zingbot
			</button>
		</div>
	</div>
</section>

<!-- CRM SECTION -->
<section id="crm" class="py-24 border-t border-white/10 product-section">
	<div class="max-w-7xl mx-auto px-6">
		<div class="text-center mb-16">
			<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">CRM</h2>
			<h3 class="text-white text-5xl font-black uppercase tracking-tight">
				Smart CRM System
			</h3>
			<p class="text-slate-400 mt-6 max-w-2xl mx-auto">
				Organize leads. Track deals. Increase conversions.
			</p>
		</div>

		<div class="grid md:grid-cols-3 gap-8">
						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-solid fa-address-book"></i> Lead Management</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Centralized lead capture and tracking. No more spreadsheets. No more lost prospects.
							</p>
						</div>

						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-solid fa-diagram-project"></i> Pipeline Control</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Visual deal stages. Sales tracking. Clear forecasting and revenue visibility.
							</p>
						</div>

						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-solid fa-chart-pie"></i> Performance Analytics</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Data-driven dashboards to monitor team performance and optimize conversions.
							</p>
						</div>
		</div>

		<div class="text-center mt-12 flex items-center justify-center">
			<button type="button" class="btn-primary flex items-center gap-2 justify-center" onclick="window.location.href='leadform.php'"><i class="fa-solid fa-paper-plane"></i> Request CRM Demo</button>
		</div>
	</div>
</section>

<!-- ZINGBOT SECTION -->
<section id="zingbot" class="py-24 border-t border-white/10 bg-black product-section">
	<div class="max-w-7xl mx-auto px-6">
		<div class="text-center mb-16">
			<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">Automation</h2>
			<h3 class="text-white text-5xl font-black uppercase tracking-tight">
				Zingbot Automation
			</h3>
			<p class="text-slate-400 mt-6 max-w-2xl mx-auto">
				Capture. Engage. Convert. Automatically.
			</p>
		</div>

		<div class="grid md:grid-cols-3 gap-8">
						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-solid fa-comments"></i> Website Chatbot</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Instant replies. Smart qualification. 24/7 lead capture without manual effort.
							</p>
						</div>

						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-brands fa-whatsapp"></i> WhatsApp Automation</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Broadcast campaigns, drip sequences, and automated responses that scale engagement.
							</p>
						</div>

						<div class="glow-card bg-white/[0.02] p-8 product-card">
							<h4 class="text-white text-xl font-bold mb-4 uppercase flex items-center gap-2"><i class="fa-solid fa-link"></i> CRM Integration</h4>
							<p class="text-slate-400 text-sm leading-relaxed">
								Automatically push qualified leads into your CRM pipeline for seamless follow-up.
							</p>
						</div>
		</div>

		<div class="text-center mt-12 flex items-center justify-center">
			<button type="button" class="btn-primary flex items-center gap-2 justify-center" onclick="window.location.href='leadform.php'"><i class="fa-solid fa-paper-plane"></i> Request Zingbot Demo</button>
		</div>
	</div>
</section>

<!-- CTA -->
<section class="py-24 border-t border-white/10 text-center cta-section relative overflow-hidden">
	<div class="absolute inset-0 w-full h-full bg-gradient-to-br from-primary/30 via-purple-700/20 to-black/80 pointer-events-none animate-gradient-move"></div>
	<div class="relative z-10 max-w-2xl mx-auto">
		<h3 class="cta-heading text-white text-4xl md:text-5xl font-black uppercase tracking-tight drop-shadow-lg flex items-center justify-center gap-3">
			<i class="fa-solid fa-rocket text-primary"></i> Build Your Growth Engine
		</h3>
		<p class="cta-desc text-slate-300 mt-6 max-w-xl mx-auto text-lg md:text-xl drop-shadow">
			Stop managing leads manually. Start scaling with structured automation.
		</p>
		<div class="mt-10 flex justify-center">
			<button type="button" class="btn-primary flex items-center gap-2 justify-center shadow-xl text-lg px-12 py-5" onclick="window.location.href='leadform.php'"><i class="fa-solid fa-phone-volume"></i> Book Free Strategy Call</button>
		</div>
	</div>
</section>

</main>


<!-- GSAP Animation Script -->
<script>
gsap.registerPlugin(ScrollTrigger);

// Animate hero title
gsap.from('.hero-title', {
	opacity: 0,
	y: 60,
	duration: 1.2,
	ease: 'power3.out',
});

// Animate hero buttons
gsap.from('.hero-section .cta-btn', {
	opacity: 0,
	y: 40,
	duration: 1,
	stagger: 0.2,
	delay: 0.5,
	ease: 'power3.out',
});

// Animate product cards on scroll
gsap.utils.toArray('.product-card').forEach((card, i) => {
	gsap.from(card, {
		opacity: 0,
		y: 60,
		duration: 1,
		ease: 'power3.out',
		scrollTrigger: {
			trigger: card,
			start: 'top 85%',
			toggleActions: 'play none none none'
		},
		delay: i * 0.1
	});
});

// Animate CTA section
gsap.from('.cta-section', {
	opacity: 0,
	y: 80,
	duration: 1.2,
	scrollTrigger: {
		trigger: '.cta-section',
		start: 'top 85%',
		toggleActions: 'play none none none'
	}
});
gsap.from('.cta-heading', {
	opacity: 0,
	y: 40,
	duration: 1,
	delay: 0.2,
	scrollTrigger: {
		trigger: '.cta-section',
		start: 'top 90%',
		toggleActions: 'play none none none'
	}
});
gsap.from('.cta-desc', {
	opacity: 0,
	y: 30,
	duration: 1,
	delay: 0.4,
	scrollTrigger: {
		trigger: '.cta-section',
		start: 'top 90%',
		toggleActions: 'play none none none'
	}
});
gsap.from('.cta-section .cta-btn', {
	opacity: 0,
	scale: 0.95,
	duration: 1,
	delay: 0.6,
	scrollTrigger: {
		trigger: '.cta-section',
		start: 'top 90%',
		toggleActions: 'play none none none'
	}
});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>