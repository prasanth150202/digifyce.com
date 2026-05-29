<?php include __DIR__ . '/app/views/header.php'; ?>

<section class="py-20 sm:py-24 lg:py-32 bg-[#030508]">
	<div class="max-w-[900px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
		<div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[var(--electric-blue)]/10 text-[var(--electric-blue)] mb-6">
			<span class="material-symbols-outlined">check_circle</span>
		</div>
		<div class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-3">Thank You</div>
		<h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter">We received your request.</h1>
		<p class="mt-4 text-slate-500 text-base sm:text-lg">Our team will reach out within 24 hours with next steps.</p>

		<div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
			<a href="/digifyce2" class="bg-white text-[var(--navy-black)] px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-[var(--electric-blue)] hover:text-white transition-all">
				Back to Home
			</a>
			<a href="/digifyce2/leadform.php" class="border border-white/20 text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:border-[var(--electric-blue)] hover:text-[var(--electric-blue)] transition-all">
				Submit Another
			</a>
		</div>
	</div>
</section>

<?php include __DIR__ . '/app/views/footer.php'; ?>
