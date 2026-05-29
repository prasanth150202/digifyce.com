

<?php include __DIR__ . '/app/views/header.php'; ?>

<style>
		.testimonial-innovative video {
			max-height: 340px;
			object-fit: cover;
			width: 100%;
			background: #000;
			display: block;
		}
	.testimonial-innovative {
		background: linear-gradient(120deg, #0a0f1a 60%, #1a2744 100%);
		position: relative;
		overflow: hidden;
	}
	.testimonial-innovative::before {
		content: '';
		position: absolute;
		top: -100px; left: -100px;
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

<section class="testimonial-innovative py-20">
	<div class="max-w-5xl mx-auto px-4 relative z-10">
		<h2 class="text-3xl sm:text-4xl font-bold text-center text-white mb-16 tracking-tight">Client Video Testimonials</h2>
		<div class="space-y-24">
			<!-- Testimonial 1: Video Left, Review Right -->
			<div class="flex flex-col md:flex-row items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#00d9ff33]" controls poster="public/assets/testimonials/thumbnails/Aadhya.jpg" preload="none">
						<source src="public/assets/testimonials/videos/Aadhya.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Aadhya.png" alt="AADHYA" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">AADHYA</h3>
						<p class="text-lg mb-4 italic relative z-2">Digifyce helped us scale our herbal care brand online. Their creative and performance marketing expertise is unmatched!</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 01</div>
					</div>
				</div>
			</div>
			<!-- Testimonial 2: Review Left, Video Right -->
			<div class="flex flex-col md:flex-row-reverse items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#8b5cf633]" controls poster="public/assets/testimonials/thumbnails/Aha.png" preload="none">
						<source src="public/assets/testimonials/videos/Aha.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Aha.png" alt="AHA" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">AHA</h3>
						<p class="text-lg mb-4 italic relative z-2">We saw a 3x increase in leads after working with Digifyce. The team is proactive and results-driven.</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 02</div>
					</div>
				</div>
			</div>
			<!-- Testimonial 3: Video Left, Review Right -->
			<div class="flex flex-col md:flex-row items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#00d9ff33]" controls poster="public/assets/testimonials/thumbnails/Aishwaryam.jpg" preload="none">
						<source src="public/assets/testimonials/videos/Aishwaryam.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Aishwaryam.png" alt="AISHWARYAM" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">AISHWARYAM</h3>
						<p class="text-lg mb-4 italic relative z-2">From strategy to execution, Digifyce delivered beyond our expectations. Highly recommended!</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 03</div>
					</div>
				</div>
			</div>
			<!-- Testimonial 4: Review Left, Video Right -->
			<div class="flex flex-col md:flex-row-reverse items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#8b5cf633]" controls poster="public/assets/testimonials/thumbnails/Bawse Baby.jpg" preload="none">
						<source src="public/assets/testimonials/videos/Bawse Baby.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Bawse Baby.png" alt="BAWSE BABY" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">BAWSE BABY</h3>
						<p class="text-lg mb-4 italic relative z-2">The Digifyce team is creative, responsive, and truly cares about our growth. We’re grateful for their partnership.</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 04</div>
					</div>
				</div>
			</div>
			<!-- Testimonial 5: Video Left, Review Right (extra) -->
			<div class="flex flex-col md:flex-row items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#00d9ff33]" controls poster="public/assets/testimonials/thumbnails/Sweet Smith.jpg" preload="none">
						<source src="public/assets/testimonials/videos/Sweet Smith.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Sweet Smith.png" alt="SWEET SMITH" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">SWEET SMITH</h3>
						<p class="text-lg mb-4 italic relative z-2">Digifyce’s creative team brought our vision to life. We loved the process and the results!</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 05</div>
					</div>
				</div>
			</div>
			<!-- Testimonial 6: Review Left, Video Right (extra) -->
			<div class="flex flex-col md:flex-row-reverse items-center md:items-stretch gap-10 testimonial-fade">
				<div class="md:w-1/2 w-full relative">
					<video class="rounded-2xl shadow-2xl w-full h-auto border-4 border-[#8b5cf633]" controls poster="public/assets/testimonials/thumbnails/1.png" preload="none">
						<source src="public/assets/testimonials/videos/1.mp4" type="video/mp4">
						Your browser does not support the video tag.
					</video>
				</div>
				<div class="md:w-1/2 w-full flex items-center relative">
					<div class="bg-[#181e2a] rounded-2xl p-10 shadow-2xl text-white relative overflow-visible">
						<span class="testimonial-quote">“</span>
						<img src="public/assets/cl_logos/Sweet 16.png" alt="SWEET 16" class="testimonial-avatar mx-auto md:mx-0">
						<h3 class="text-2xl font-bold mb-2 tracking-tight">SWEET 16</h3>
						<p class="text-lg mb-4 italic relative z-2">We trust Digifyce for all our digital campaigns. Their approach is fresh and effective.</p>
						<div class="text-sm text-blue-300 font-semibold">- Client Story 06</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
// Fade-in on scroll for testimonials
function revealTestimonials() {
	const testimonials = document.querySelectorAll('.testimonial-fade');
	const windowHeight = window.innerHeight;
	testimonials.forEach((el, i) => {
		const top = el.getBoundingClientRect().top;
		if (top < windowHeight - 100) {
			setTimeout(() => el.classList.add('visible'), i * 200);
		}
	});
}
window.addEventListener('scroll', revealTestimonials);
window.addEventListener('DOMContentLoaded', revealTestimonials);
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
