<?php
$pageTitle = 'About Us – Digifyce | Performance Marketing & Growth Agency';
$pageDescription = 'About Digifyce: a strategic branding and growth agency helping D2C brands, e-commerce businesses, startups, and growing companies scale with strategy, creativity, and performance.';
$bodyClass = 'about-creative';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');

$extraHead = <<<'HTML'
<style>
	@keyframes slideInLeft {
		from { opacity: 0; transform: translateX(-60px); }
		to { opacity: 1; transform: translateX(0); }
	}
	@keyframes slideInRight {
		from { opacity: 0; transform: translateX(60px); }
		to { opacity: 1; transform: translateX(0); }
	}
	@keyframes slideInUp {
		from { opacity: 0; transform: translateY(40px); }
		to { opacity: 1; transform: translateY(0); }
	}
	@keyframes fadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	.sculpted-text {
		background: linear-gradient(135deg, #ffffff 0%, #0066ff 50%, #7dd3fc 100%);
		-webkit-background-clip: text;
		background-clip: text;
		-webkit-text-fill-color: transparent;
		font-weight: 900;
		letter-spacing: -0.02em;
	}

	.about-stat-box {
		border: 2px solid rgba(0,102,255,0.2);
		background: linear-gradient(135deg, rgba(0,102,255,0.06), rgba(255,255,255,0.03));
		backdrop-filter: blur(16px);
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.about-stat-box:hover {
		border-color: rgba(0,102,255,0.6);
		background: linear-gradient(135deg, rgba(0,102,255,0.12), rgba(0,102,255,0.05));
		transform: translateY(-6px);
	}

	.anim-stagger-1 { animation: slideInUp 0.8s ease-out 0.1s both; }
	.anim-stagger-2 { animation: slideInUp 0.8s ease-out 0.2s both; }
	.anim-stagger-3 { animation: slideInUp 0.8s ease-out 0.3s both; }
	.anim-stagger-4 { animation: slideInUp 0.8s ease-out 0.4s both; }
	.anim-stagger-5 { animation: slideInUp 0.8s ease-out 0.5s both; }
	.anim-stagger-6 { animation: slideInUp 0.8s ease-out 0.6s both; }

	.split-column {
		animation: slideInLeft 1.0s ease-out both;
	}
	.split-column:nth-child(2) {
		animation: slideInRight 1.0s ease-out both;
	}

	.counter-number { font-size: 3.5rem; font-weight: 700; line-height: 1; }

	.feature-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
		gap: 1.5rem;
	}

	@media (max-width: 768px) {
		.counter-number { font-size: 2.25rem; }
	}
</style>
HTML;

include __DIR__ . '/app/views/header.php';
?>

<!-- HERO: BOLD & CREATIVE -->
<section class="relative min-h-screen flex items-center pt-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
	<!-- Background gradients -->
	<div class="absolute inset-0 -z-20">
		<div class="absolute inset-0" style="background: radial-gradient(circle at 10% 50%, rgba(0,102,255,0.12), transparent 40%), radial-gradient(circle at 90% 50%, rgba(125,211,252,0.08), transparent 45%);"></div>
	</div>
	<div class="absolute inset-0 -z-10" style="background: linear-gradient(135deg, #05070a 0%, #0a0f1a 50%, #05070a 100%);"></div>

	<div class="max-w-7xl mx-auto w-full relative z-10">
		<div class="grid md:grid-cols-12 gap-6 lg:gap-12 items-center">
			<!-- LEFT COLUMN -->
			<div class="md:col-span-6 split-column">
				<div class="anim-stagger-1 text-[10px] sm:text-xs uppercase tracking-[0.45em] text-slate-500 mb-8">
					<span class="inline-block px-4 py-2 rounded-full border border-white/10 bg-white/5">Who We Are</span>
				</div>

				<h1 class="anim-stagger-2 text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black tracking-tighter leading-[0.88] mb-6 lg:mb-8">
					<span class="block text-white">We Build</span>
					<span class="sculpted-text">Growth</span>
					<span class="block text-white">Systems</span>
				</h1>

				<p class="anim-stagger-3 max-w-xl text-base sm:text-lg text-slate-400 leading-relaxed mb-10">
					Not just marketing. Not just design. We architect complete systems that help brands win customer trust, drive conversions, and scale revenue with purpose.
				</p>

				<div class="anim-stagger-4 flex flex-col sm:flex-row gap-4">
					<a href="#values" class="inline-flex items-center justify-center px-8 py-4 bg-[var(--electric-blue)] text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:shadow-lg hover:shadow-blue-600/50 transition-all group">
						Our Story
						<span class="ml-2 group-hover:translate-x-1 transition-transform">→</span>
					</a>
					<a href="leadform.php" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/20 text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:border-white/60 transition-colors">
						Start Project
					</a>
				</div>
			</div>

			<!-- RIGHT COLUMN: STATS GRID -->
			<div class="md:col-span-6 split-column">
				<div class="grid grid-cols-2 gap-4 lg:gap-6">
					<div class="anim-stagger-1 about-stat-box rounded-2xl p-6 lg:p-8">
						<div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">Founded</div>
						<div class="counter-number text-white">2021</div>
						<div class="text-slate-400 text-sm mt-3">Launched with vision</div>
					</div>
					<div class="anim-stagger-2 about-stat-box rounded-2xl p-6 lg:p-8">
						<div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">Brands</div>
						<div class="counter-number text-[var(--electric-blue)]">120+</div>
						<div class="text-slate-400 text-sm mt-3">Growing faster</div>
					</div>
					<div class="anim-stagger-3 about-stat-box rounded-2xl p-6 lg:p-8">
						<div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">Focus</div>
						<div class="counter-number text-white">360°</div>
						<div class="text-slate-400 text-sm mt-3">Complete solutions</div>
					</div>
					<div class="anim-stagger-4 about-stat-box rounded-2xl p-6 lg:p-8">
						<div class="text-slate-500 text-xs uppercase tracking-[0.3em] mb-3">Standard</div>
						<div class="counter-number text-[var(--electric-blue)]">ROI</div>
						<div class="text-slate-400 text-sm mt-3">Driven always</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- VALUES SECTION -->
<section id="values" class="relative py-20 sm:py-28 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#030508] to-[#0a0f1a]">
	<div class="max-w-7xl mx-auto">
		<div class="mb-16">
			<h2 class="anim-stagger-1 text-sm uppercase tracking-[0.35em] text-slate-500 mb-4">Foundation</h2>
			<h3 class="anim-stagger-2 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">What We Believe In</h3>
		</div>

		<div class="grid md:grid-cols-3 gap-6 lg:gap-8">
			<div class="anim-stagger-1 rounded-3xl p-8 lg:p-10 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/40 hover:bg-[rgba(0,102,255,0.06)] transition-all duration-500">
				<div class="w-14 h-14 rounded-xl bg-[var(--electric-blue)] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
					<span class="text-2xl font-bold text-white">→</span>
				</div>
				<h4 class="text-2xl font-bold mb-4">Strategy First</h4>
				<p class="text-slate-400 leading-relaxed">Every design, every campaign, every copy is rooted in data and strategic clarity—not just pretty ideas.</p>
			</div>

			<div class="anim-stagger-2 rounded-3xl p-8 lg:p-10 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/40 hover:bg-[rgba(0,102,255,0.06)] transition-all duration-500">
				<div class="w-14 h-14 rounded-xl bg-[var(--electric-blue)] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
					<span class="text-2xl font-bold text-white">◯</span>
				</div>
				<h4 class="text-2xl font-bold mb-4">Performance Always</h4>
				<p class="text-slate-400 leading-relaxed">We measure, optimize, and iterate. Your growth is our only metric that matters. Real results, real ROI.</p>
			</div>

			<div class="anim-stagger-3 rounded-3xl p-8 lg:p-10 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/40 hover:bg-[rgba(0,102,255,0.06)] transition-all duration-500">
				<div class="w-14 h-14 rounded-xl bg-[var(--electric-blue)] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
					<span class="text-2xl font-bold text-white">✦</span>
				</div>
				<h4 class="text-2xl font-bold mb-4">Clarity Over Noise</h4>
				<p class="text-slate-400 leading-relaxed">In a crowded world, strong positioning and consistent messaging win. We cut through the noise and build trust.</p>
			</div>
		</div>
	</div>
</section>

<!-- THE JOURNEY SECTION -->
<section class="relative py-20 sm:py-28 px-4 sm:px-6 lg:px-8 bg-[#05070a]">
	<div class="max-w-6xl mx-auto">
		<div class="mb-16 text-center">
			<h2 class="anim-stagger-1 text-sm uppercase tracking-[0.35em] text-slate-500 mb-4">Process</h2>
			<h3 class="anim-stagger-2 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Our Growth Framework</h3>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-5 gap-0 lg:gap-4">
			<!-- Step 1 -->
			<div class="anim-stagger-1 relative">
				<div class="rounded-2xl p-6 lg:p-8 bg-gradient-to-br from-[rgba(0,102,255,0.12)] to-transparent border border-[rgba(0,102,255,0.25)] min-h-[280px] flex flex-col justify-between">
					<div>
						<div class="inline-block px-3 py-1 rounded-full bg-[var(--electric-blue)] text-white text-xs font-bold mb-4">01</div>
						<h4 class="text-xl font-bold mb-3">Discover</h4>
						<p class="text-slate-400 text-sm">Deep dive into your business, audience, and true competitive advantage.</p>
					</div>
					<span class="text-[var(--electric-blue)] text-xs uppercase tracking-[0.2em] font-semibold">2 weeks</span>
				</div>
				<div class="hidden lg:block absolute left-full top-1/2 -translate-y-1/2 w-4 h-0.5 bg-gradient-to-r from-[var(--electric-blue)] to-transparent"></div>
			</div>

			<!-- Step 2 -->
			<div class="anim-stagger-2 relative">
				<div class="rounded-2xl p-6 lg:p-8 bg-gradient-to-br from-[rgba(0,102,255,0.12)] to-transparent border border-[rgba(0,102,255,0.25)] min-h-[280px] flex flex-col justify-between">
					<div>
						<div class="inline-block px-3 py-1 rounded-full bg-[var(--electric-blue)] text-white text-xs font-bold mb-4">02</div>
						<h4 class="text-xl font-bold mb-3">Strategy</h4>
						<p class="text-slate-400 text-sm">Build a custom growth roadmap with clear positioning and tactical milestones.</p>
					</div>
					<span class="text-[var(--electric-blue)] text-xs uppercase tracking-[0.2em] font-semibold">3 weeks</span>
				</div>
				<div class="hidden lg:block absolute left-full top-1/2 -translate-y-1/2 w-4 h-0.5 bg-gradient-to-r from-[var(--electric-blue)] to-transparent"></div>
			</div>

			<!-- Step 3 -->
			<div class="anim-stagger-3 relative">
				<div class="rounded-2xl p-6 lg:p-8 bg-gradient-to-br from-[rgba(0,102,255,0.12)] to-transparent border border-[rgba(0,102,255,0.25)] min-h-[280px] flex flex-col justify-between">
					<div>
						<div class="inline-block px-3 py-1 rounded-full bg-[var(--electric-blue)] text-white text-xs font-bold mb-4">03</div>
						<h4 class="text-xl font-bold mb-3">Build</h4>
						<p class="text-slate-400 text-sm">Execute branding, creative, tech and marketing across all channels simultaneously.</p>
					</div>
					<span class="text-[var(--electric-blue)] text-xs uppercase tracking-[0.2em] font-semibold">4-8 weeks</span>
				</div>
				<div class="hidden lg:block absolute left-full top-1/2 -translate-y-1/2 w-4 h-0.5 bg-gradient-to-r from-[var(--electric-blue)] to-transparent"></div>
			</div>

			<!-- Step 4 -->
			<div class="anim-stagger-4 relative">
				<div class="rounded-2xl p-6 lg:p-8 bg-gradient-to-br from-[rgba(0,102,255,0.12)] to-transparent border border-[rgba(0,102,255,0.25)] min-h-[280px] flex flex-col justify-between">
					<div>
						<div class="inline-block px-3 py-1 rounded-full bg-[var(--electric-blue)] text-white text-xs font-bold mb-4">04</div>
						<h4 class="text-xl font-bold mb-3">Launch</h4>
						<p class="text-slate-400 text-sm">Go live with full orchestration across all touchpoints and measure initial traction.</p>
					</div>
					<span class="text-[var(--electric-blue)] text-xs uppercase tracking-[0.2em] font-semibold">1 week</span>
				</div>
				<div class="hidden lg:block absolute left-full top-1/2 -translate-y-1/2 w-4 h-0.5 bg-gradient-to-r from-[var(--electric-blue)] to-transparent"></div>
			</div>

			<!-- Step 5 -->
			<div class="anim-stagger-5 relative">
				<div class="rounded-2xl p-6 lg:p-8 bg-gradient-to-br from-[rgba(0,102,255,0.12)] to-transparent border border-[rgba(0,102,255,0.25)] min-h-[280px] flex flex-col justify-between">
					<div>
						<div class="inline-block px-3 py-1 rounded-full bg-[var(--electric-blue)] text-white text-xs font-bold mb-4">05</div>
						<h4 class="text-xl font-bold mb-3">Scale</h4>
						<p class="text-slate-400 text-sm">Continuously optimize, test, and scale what works based on live performance data.</p>
					</div>
					<span class="text-[var(--electric-blue)] text-xs uppercase tracking-[0.2em] font-semibold">Ongoing</span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- WHAT WE DO SECTION -->
<section class="relative py-20 sm:py-28 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#0a0f1a] to-[#05070a]">
	<div class="max-w-7xl mx-auto">
		<div class="mb-16">
			<h2 class="anim-stagger-1 text-sm uppercase tracking-[0.35em] text-slate-500 mb-4">Services</h2>
			<h3 class="anim-stagger-2 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">Complete Growth Solutions</h3>
		</div>

		<div class="feature-grid">
			<div class="anim-stagger-1 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">⬡</div>
				<h4 class="text-xl font-bold mb-3">D2C Branding</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Position, identity, messaging and systems that make your brand unforgettable in a crowded market.</p>
			</div>

			<div class="anim-stagger-2 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">◆</div>
				<h4 class="text-xl font-bold mb-3">Performance Marketing</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Paid media, funnel optimization, creative testing and audience scaling that drives real ROI.</p>
			</div>

			<div class="anim-stagger-3 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">◆</div>
				<h4 class="text-xl font-bold mb-3">Commercial Content</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Photo, video and visual storytelling that powers ads, social, and marketplace presence.</p>
			</div>

			<div class="anim-stagger-4 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">◆</div>
				<h4 class="text-xl font-bold mb-3">Web & E-commerce</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Storefronts, integrations and tech infrastructure built for conversion and scale.</p>
			</div>

			<div class="anim-stagger-5 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">◆</div>
				<h4 class="text-xl font-bold mb-3">Marketplace Ops</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Listings, SEO, reviews and channels management that improves discoverability and sales.</p>
			</div>

			<div class="anim-stagger-6 rounded-2xl p-8 border-2 border-white/10 bg-white/5 group hover:border-[var(--electric-blue)]/50 hover:bg-[rgba(0,102,255,0.08)] transition-all">
				<div class="text-[var(--electric-blue)] font-black text-4xl mb-4">◆</div>
				<h4 class="text-xl font-bold mb-3">Content Marketing</h4>
				<p class="text-slate-400 text-sm leading-relaxed">Strategy, production and distribution of content that builds authority and long-term demand.</p>
			</div>
		</div>
	</div>
</section>

<!-- CALL TO ACTION -->
<section class="relative py-20 sm:py-32 px-4 sm:px-6 lg:px-8 bg-[#05070a]">
	<div class="absolute inset-0 pointer-events-none">
		<div style="background: radial-gradient(circle at 50% 50%, rgba(0,102,255,0.1), transparent 70%); position: absolute; inset: 0;"></div>
	</div>
	
	<div class="max-w-4xl mx-auto text-center relative z-10">
		<h2 class="anim-stagger-1 text-5xl sm:text-6xl lg:text-7xl font-black tracking-tight mb-8">
			Ready to <span class="sculpted-text">scale</span>?
		</h2>
		<p class="anim-stagger-2 text-lg sm:text-xl text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
			Whether you're launching a new brand or scaling an existing one, we're here to help you build systems that drive measurable growth.
		</p>
		
		<div class="anim-stagger-3 flex flex-col sm:flex-row gap-4 justify-center">
			<a href="leadform.php" class="inline-flex items-center justify-center px-10 py-5 bg-[var(--electric-blue)] text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:shadow-2xl hover:shadow-blue-600/50 transition-all group">
				Start Your Project
				<span class="ml-2 group-hover:translate-x-1 transition-transform">→</span>
			</a>
			<a href="#values" class="inline-flex items-center justify-center px-10 py-5 border-2 border-white/30 text-white font-bold uppercase tracking-[0.2em] text-sm rounded-lg hover:border-white/60 hover:bg-white/5 transition-all">
				Learn More
			</a>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Scroll reveal animations
	const revealElements = document.querySelectorAll('[class*="reveal-"], [class*="anim-stagger-"], .split-column');
	
	const observerOptions = {
		threshold: 0.1,
		rootMargin: '0px 0px -50px 0px'
	};

	const observer = new IntersectionObserver(function(entries) {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.style.opacity = '1';
				entry.target.style.transform = 'translate(0, 0)';
				observer.unobserve(entry.target);
			}
		});
	}, observerOptions);

	// Animate on load for hero
	const heroAnimated = document.querySelectorAll('.anim-stagger-1, .anim-stagger-2, .anim-stagger-3, .anim-stagger-4, .split-column');
	heroAnimated.forEach(el => {
		// They already have animations via CSS
	});

	// Stat box counter animation
	const statBoxes = document.querySelectorAll('.about-stat-box');
	statBoxes.forEach(box => observer.observe(box));
});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
