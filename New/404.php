<?php
http_response_code(404);
include __DIR__ . '/app/views/header.php';
?>

<section class="py-20 sm:py-24 lg:py-32 bg-[#030508]">
	<div class="max-w-[900px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
		<div class="text-xs uppercase tracking-[0.4em] text-[var(--electric-blue)] mb-3">Error 404</div>
		<h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter">Page Not Found</h1>
		<p class="mt-4 text-slate-500 text-base sm:text-lg">The page you’re looking for doesn’t exist or was moved.</p>

		<div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
			<a href="/digifyce2" class="bg-white text-[var(--navy-black)] px-8 py-3 font-bold uppercase tracking-widest text-xs hover:bg-[var(--electric-blue)] hover:text-white transition-all">
				Back to Home
			</a>
			<a href="/digifyce2/leadform.php" class="border border-white/20 text-white px-8 py-3 font-bold uppercase tracking-widest text-xs hover:border-[var(--electric-blue)] hover:text-[var(--electric-blue)] transition-all">
				Contact Us
			</a>
		</div>
	</div>
</section>

<?php include __DIR__ . '/app/views/footer.php'; ?>
