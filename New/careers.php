<?php
$pageTitle = 'Digifyce Careers & Talent Recruitment';
$bodyClass = 'bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 selection:bg-primary selection:text-white';
$tailwindConfig = '<script id="tailwind-config">
		tailwind.config = {
			darkMode: "class",
			theme: {
				extend: {
					colors: {
						"primary": "#0d69f2",
						"background-light": "#f5f7f8",
						"background-dark": "#0a0a0a",
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
		body {
			font-family: "Space Grotesk", sans-serif
		}
		.grainy-overlay {
			background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuDOH9s6trylLl4KgLwWW-8T0Mb76zqCRqM_bvbEZXoeWMd4TqadKQfJvXwmcfH7ue4B0fSw4Bxm_04Mf0XlTcGOqtIpalqhdIXDZySxWvTXJ1M1FsYEQ1E08CTsVdQcXMJRqbcKb48f4THMs98HWLvlI0O6e2PBWgk5f4ZiPvjV0lB2m1O__EYHVybCC383mVCGhvBcYwsxzjStk1V9BT0g5BdvtOn2mfIxJm6Ox36B9JTxLzUGi6EnQe9vsXAX6JPwNyJ1W8BzpA);
			opacity: 0.05
		}
		.drawing-effect {
			transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
			border: 1px solid rgba(255, 255, 255, 0.1)
		}
		.drawing-effect:hover {
			border-color: #0d69f2;
			box-shadow: 0 0 15px rgba(13, 105, 242, 0.3)
		}
		.technical-grid-line {
			background-image: linear-gradient(to right, rgba(13, 105, 242, 0.2) 1px, transparent 1px), linear-gradient(to bottom, rgba(13, 105, 242, 0.2) 1px, transparent 1px);
			background-size: 40px 40px
		}
		.input-glow:focus {
			outline: none;
			border-color: #0d69f2;
			box-shadow: 0 0 10px rgba(13, 105, 242, 0.4);
		}
	</style>';

// Fetch job openings from database
$jobOpenings = [];
try {
    require_once __DIR__ . '/config/database.php';
    $pdo = Database::getInstance();
    $stmt = $pdo->query('SELECT id, title, division, location, description, requirements FROM job_openings WHERE is_active = 1 ORDER BY position ASC');
    $jobOpenings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to empty array if table doesn't exist
    $jobOpenings = [];
}

include __DIR__ . '/app/views/header.php';
?>

<main class="bg-background-dark">
	<section class="relative w-full min-h-screen flex flex-col items-center justify-center overflow-hidden pt-24 bg-black" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuAnOhpZaqtXF7csDj7rgWZRDYAXwAUEVLSfBF6xDOHRj-A0KNJsW2ErMnpV8FAintFDT90TS7Xkh2jyg9HMbn_nAt_hJKj0SyENcilIKMhj3bsPPMfzR9MDAc1ysxCxTVw_TwhtywVpA6bJoYjUCyQyuAWJ8AoLEZA5jz0tp4gKjDER4PjHOOAfeqr1gbiVfNJcNITvJaD4Lt44PSjEeO3H6F7ogYUjQGgundqqeQ5tzY-opgW_5cHNSUIbZIICi7_f9WKmOaFWlg'); background-size: cover; background-position: center; background-attachment: fixed;">
		<div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/40 to-black opacity-80"></div>
		<div class="grainy-overlay absolute inset-0"></div>
		<div class="relative z-10 text-center px-4 max-w-5xl">
			<div class="mb-4 inline-flex items-center gap-2 px-3 py-1 border border-primary/30 bg-primary/10 rounded-full">
				<span class="relative flex h-2 w-2">
					<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
					<span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
				</span>
				<span class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary">Now Recruiting Elite Talent</span>
			</div>
			<h1 class="text-white text-6xl md:text-9xl font-black leading-none tracking-tighter mb-8 uppercase">
				JOIN THE <span class="text-primary italic">ELITE</span>
			</h1>
			<p class="text-slate-400 text-lg md:text-2xl font-light tracking-wide max-w-2xl mx-auto leading-relaxed">
				We don't hire employees. We recruit <span class="text-white font-medium">growth architects</span> to build the future of performance marketing.
			</p>
			<div class="mt-12">
				<a class="group relative inline-flex items-center gap-4 px-12 py-5 bg-primary text-white font-bold uppercase tracking-[0.2em] text-sm overflow-hidden transition-all hover:pr-16" href="#openings">
					<span class="relative z-10">View Openings</span>
					<span class="material-symbols-outlined transition-all group-hover:translate-x-2">arrow_forward</span>
					<div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
				</a>
			</div>
		</div>
		<div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce text-slate-500">
			<span class="material-symbols-outlined">expand_more</span>
		</div>
	</section>

	<section class="py-24 bg-background-dark border-y border-white/5 overflow-hidden">
		<div class="max-w-7xl mx-auto px-6 mb-16">
			<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">Core Philosophy</h2>
			<h3 class="text-white text-4xl font-bold tracking-tight">The Digifyce Standard</h3>
		</div>
		<div class="flex overflow-x-auto pb-12 px-6 md:px-20 gap-8 no-scrollbar scroll-smooth">
			<div class="min-w-[320px] md:min-w-[400px] group border border-white/10 p-10 bg-white/5 backdrop-blur-sm relative overflow-hidden transition-all hover:border-primary/50">
				<div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-100 transition-opacity">
					<span class="material-symbols-outlined text-6xl text-primary">analytics</span>
				</div>
				<span class="text-6xl font-black text-white/5 group-hover:text-primary/20 transition-colors">01</span>
				<h4 class="text-white text-2xl font-bold mt-4 mb-3 uppercase tracking-wider">Data-Driven</h4>
				<p class="text-slate-400 leading-relaxed font-light">Precision over intuition. Every decision is anchored in real-time metrics and technical graph nexus models.</p>
				<div class="mt-8 h-px w-full bg-white/10 group-hover:bg-primary transition-all"></div>
			</div>
			<div class="min-w-[320px] md:min-w-[400px] group border border-white/10 p-10 bg-white/5 backdrop-blur-sm relative overflow-hidden transition-all hover:border-primary/50">
				<div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-100 transition-opacity">
					<span class="material-symbols-outlined text-6xl text-primary">architecture</span>
				</div>
				<span class="text-6xl font-black text-white/5 group-hover:text-primary/20 transition-colors">02</span>
				<h4 class="text-white text-2xl font-bold mt-4 mb-3 uppercase tracking-wider">Minimalist</h4>
				<p class="text-slate-400 leading-relaxed font-light">Eliminating the noise. We build clean geometry into our strategies to ensure maximum clarity and execution speed.</p>
				<div class="mt-8 h-px w-full bg-white/10 group-hover:bg-primary transition-all"></div>
			</div>
			<div class="min-w-[320px] md:min-w-[400px] group border border-white/10 p-10 bg-white/5 backdrop-blur-sm relative overflow-hidden transition-all hover:border-primary/50">
				<div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-100 transition-opacity">
					<span class="material-symbols-outlined text-6xl text-primary">target</span>
				</div>
				<span class="text-6xl font-black text-white/5 group-hover:text-primary/20 transition-colors">03</span>
				<h4 class="text-white text-2xl font-bold mt-4 mb-3 uppercase tracking-wider">Results-Only</h4>
				<p class="text-slate-400 leading-relaxed font-light">Outcome is our only KPI. We focus on target precision to deliver unmatched growth for our elite partners.</p>
				<div class="mt-8 h-px w-full bg-white/10 group-hover:bg-primary transition-all"></div>
			</div>
		</div>
	</section>

	<section class="py-24 bg-background-dark" id="openings">
		<div class="max-w-5xl mx-auto px-6">
			<div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
				<div>
					<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">Opportunities</h2>
					<h3 class="text-white text-5xl font-black tracking-tight uppercase">Open Positions</h3>
				</div>
				<div class="text-slate-500 font-mono text-xs uppercase tracking-widest">
					Showing <?= count($jobOpenings) ?> Active <?= count($jobOpenings) === 1 ? 'Node' : 'Nodes' ?>
				</div>
			</div>
			<div class="space-y-4">
				<?php if (empty($jobOpenings)): ?>
				<div class="text-slate-500 text-center py-12">No open positions at the moment. Check back soon!</div>
				<?php else: ?>
					<?php foreach ($jobOpenings as $job): ?>
					<div class="group drawing-effect p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 cursor-pointer bg-white/[0.02] hover:bg-white/5 transition-all" onclick="viewJobDetails(<?= htmlspecialchars(json_encode($job)) ?>)" style="cursor: pointer;">
						<div class="flex-1">
							<div class="flex items-center gap-3 mb-2">
								<span class="text-primary text-xs font-bold uppercase tracking-widest"><?= htmlspecialchars($job['division'] ?? 'General') ?></span>
								<span class="h-1 w-1 rounded-full bg-slate-700"></span>
								<span class="text-slate-500 text-xs font-bold uppercase tracking-widest"><?= htmlspecialchars($job['location'] ?? 'Remote') ?></span>
							</div>
							<h4 class="text-white text-2xl font-bold group-hover:text-primary transition-colors"><?= htmlspecialchars($job['title']) ?></h4>
						</div>
						<div class="flex items-center gap-8">
							<button class="flex items-center justify-center size-12 rounded-full border border-white/10 group-hover:border-primary group-hover:bg-primary text-white transition-all">
								<span class="material-symbols-outlined">north_east</span>
							</button>
						</div>
					</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="py-24 relative overflow-hidden bg-background-dark technical-grid-line">
		<div class="max-w-7xl mx-auto px-6 relative z-10">
			<div class="text-center mb-16">
				<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">Ecosystem Perks</h2>
				<h3 class="text-white text-5xl font-black tracking-tight uppercase">Why Architects Choose Us</h3>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
				<div class="border border-white/10 p-10 bg-background-dark/80 backdrop-blur-md relative group">
					<div class="absolute -inset-px border border-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
					<div class="size-14 border border-primary/30 flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-all">
						<span class="material-symbols-outlined">public</span>
					</div>
					<h4 class="text-white text-xl font-bold mb-3 uppercase">Remote Ecosystem</h4>
					<p class="text-slate-400 font-light leading-relaxed italic text-sm">Work from anywhere. Our infrastructure is built for global high-performers.</p>
				</div>
				<div class="border border-white/10 p-10 bg-background-dark/80 backdrop-blur-md relative group">
					<div class="absolute -inset-px border border-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
					<div class="size-14 border border-primary/30 flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-all">
						<span class="material-symbols-outlined">payments</span>
					</div>
					<h4 class="text-white text-xl font-bold mb-3 uppercase">Performance Bonuses</h4>
					<p class="text-slate-400 font-light leading-relaxed italic text-sm">Direct skin in the game. Win big with the partners you grow.</p>
				</div>
				<div class="border border-white/10 p-10 bg-background-dark/80 backdrop-blur-md relative group">
					<div class="absolute -inset-px border border-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
					<div class="size-14 border border-primary/30 flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-all">
						<span class="material-symbols-outlined">hub</span>
					</div>
					<h4 class="text-white text-xl font-bold mb-3 uppercase">High-Growth Network</h4>
					<p class="text-slate-400 font-light leading-relaxed italic text-sm">Surround yourself with the top 1% of performance marketing talent.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="py-24 bg-background-dark border-t border-white/10" id="application-section">
		<div class="max-w-7xl mx-auto px-6">
			<div class="flex flex-col md:flex-row gap-16 md:gap-24">
				<div class="md:w-1/3 space-y-8">
					<div>
						<h2 class="text-primary text-sm font-bold uppercase tracking-[0.4em] mb-4">Talent Pitch</h2>
						<h3 class="text-white text-5xl md:text-6xl font-black tracking-tighter uppercase leading-none">SPONTANEOUS APPLICATION</h3>
					</div>
					<div class="space-y-6">
						<p class="text-slate-400 text-lg font-light leading-relaxed">
							Don\'t see your specific designation? <span class="text-white font-medium">Pitch us.</span> We are always architecting space for elite outliers who break conventional growth models.
						</p>
						<div class="flex items-start gap-4 p-4 border-l-2 border-primary bg-white/5">
							<span class="material-symbols-outlined text-primary">tips_and_updates</span>
							<p class="text-xs text-slate-300 uppercase tracking-widest font-bold">Show us the technical leverage you bring to the command center.</p>
						</div>
					</div>
				</div>
				<div class="md:w-2/3">
					<form id="jobApplicationForm" class="space-y-8" enctype="multipart/form-data">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div class="space-y-2">
								<label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Full Name *</label>
								<input name="full_name" class="w-full bg-white/5 border border-white/10 px-4 py-4 text-white placeholder:text-slate-700 input-glow transition-all rounded-none" placeholder="John Smith" type="text" required/>
							</div>
							<div class="space-y-2">
								<label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Email Address *</label>
								<input name="email" class="w-full bg-white/5 border border-white/10 px-4 py-4 text-white placeholder:text-slate-700 input-glow transition-all rounded-none" placeholder="john.smith@example.com" type="email" required/>
							</div>
						</div>
						<div class="space-y-2">
							<label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Portfolio / LinkedIn URL *</label>
							<input name="portfolio_url" class="w-full bg-white/5 border border-white/10 px-4 py-4 text-white placeholder:text-slate-700 input-glow transition-all rounded-none" placeholder="https://linkedin.com/in/yourprofile" type="url" required/>
						</div>
						<div class="space-y-2">
							<label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Cover Letter / Why You're a Fit *</label>
							<textarea name="cover_letter" class="w-full bg-white/5 border border-white/10 px-4 py-4 text-white placeholder:text-slate-700 input-glow transition-all rounded-none resize-none" placeholder="Tell us about your experience, what excites you about this role, and how you can contribute to our team..." rows="4" required></textarea>
						</div>
						<div class="space-y-2">
							<label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Curriculum Vitae</label>
							<div class="border-2 border-dashed border-white/10 bg-white/[0.02] p-8 text-center group hover:border-primary/50 transition-colors cursor-pointer relative" id="cvUploadArea">
								<input name="cv" class="absolute inset-0 opacity-0 cursor-pointer" type="file" accept=".pdf,.doc,.docx" id="cvFileInput"/>
								<span class="material-symbols-outlined text-4xl text-slate-700 group-hover:text-primary transition-colors mb-2">upload_file</span>
								<p class="text-sm text-slate-500 group-hover:text-slate-300" id="cvFileName">Click or drag your resume/CV here</p>
								<p class="text-[10px] text-slate-700 mt-1 uppercase tracking-widest italic">PDF or DOCX • Max 10MB</p>
							</div>
						</div>
						<div id="applicationMessage" class="hidden p-4 rounded"></div>
						<div>
							<button class="w-full bg-primary text-white font-black uppercase tracking-[0.3em] py-6 hover:bg-primary/90 transition-all flex items-center justify-center gap-4 group" type="submit">
								Submit Application
								<span class="material-symbols-outlined text-xl group-hover:translate-x-2 transition-transform">send</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</main>

<!-- Job Details Modal -->
<div id="jobDetailsModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4" onclick="closeJobModal(event)">
	<div class="bg-background-dark border border-white/10 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
		<div class="sticky top-0 bg-gradient-to-b from-background-dark to-background-dark/90 border-b border-white/10 p-6 flex justify-between items-start">
			<div>
				<div class="flex items-center gap-3 mb-2">
					<span class="text-primary text-xs font-bold uppercase tracking-widest" id="modalDivision"></span>
					<span class="h-1 w-1 rounded-full bg-slate-700"></span>
					<span class="text-slate-500 text-xs font-bold uppercase tracking-widest" id="modalLocation"></span>
				</div>
				<h2 class="text-white text-3xl font-black uppercase tracking-tight" id="modalTitle"></h2>
			</div>
			<button onclick="closeJobModal()" class="text-slate-500 hover:text-white transition-colors">
				<span class="material-symbols-outlined text-2xl">close</span>
			</button>
		</div>
		
		<div class="p-6 space-y-6">
			<div id="modalDescription" class="text-slate-300 leading-relaxed"></div>
			
			<div id="modalRequirementsSection" class="hidden">
				<h3 class="text-white text-lg font-bold uppercase tracking-wider mb-4">Requirements</h3>
				<div id="modalRequirements" class="text-slate-300 leading-relaxed space-y-2"></div>
			</div>
			
			<div class="border-t border-white/10 pt-6 space-y-3">
				<button class="w-full bg-primary hover:bg-primary/80 text-white font-black uppercase tracking-[0.3em] py-4 rounded transition-all flex items-center justify-center gap-4 group" onclick="applyForJob()">
					Apply Now
					<span class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
				</button>
				<button class="w-full bg-white/5 hover:bg-white/10 text-white font-bold uppercase tracking-[0.2em] py-3 rounded transition-all border border-white/10 hover:border-white/20" onclick="closeJobModal()">
					Back to Openings
				</button>
			</div>
		</div>
	</div>
</div>

<script>
function viewJobDetails(job) {
	document.getElementById('modalTitle').textContent = job.title;
	document.getElementById('modalDivision').textContent = job.division || 'General';
	document.getElementById('modalLocation').textContent = job.location || 'Remote';
	
	// Handle description (convert line breaks)
	const description = job.description || 'No description provided.';
	document.getElementById('modalDescription').innerHTML = description.replace(/\n/g, '<br>');
	
	// Handle requirements if they exist
	const requirementsSection = document.getElementById('modalRequirementsSection');
	if (job.requirements && job.requirements.trim()) {
		requirementsSection.classList.remove('hidden');
		const requirements = job.requirements.replace(/\n/g, '<br>');
		document.getElementById('modalRequirements').innerHTML = requirements;
	} else {
		requirementsSection.classList.add('hidden');
	}
	
	// Show modal
	document.getElementById('jobDetailsModal').classList.remove('hidden');
}

function closeJobModal(event) {
	// If clicking on overlay, close it
	if (event && event.target.id !== 'jobDetailsModal') {
		return;
	}
	document.getElementById('jobDetailsModal').classList.add('hidden');
}

function applyForJob() {
	// Close the modal and scroll to application form
	document.getElementById('jobDetailsModal').classList.add('hidden');
	const applicationSection = document.getElementById('application-section');
	if (applicationSection) {
		applicationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
	if (event.key === 'Escape' && !document.getElementById('jobDetailsModal').classList.contains('hidden')) {
		closeJobModal();
	}
});

// File upload display
const cvFileInput = document.getElementById('cvFileInput');
const cvFileName = document.getElementById('cvFileName');

if (cvFileInput) {
	cvFileInput.addEventListener('change', function() {
		if (this.files && this.files[0]) {
			cvFileName.textContent = this.files[0].name;
		} else {
			cvFileName.textContent = 'Click or drag your resume/CV here';
		}
	});
}

// Job Application Form Handler
document.getElementById('jobApplicationForm').addEventListener('submit', async function(e) {
	e.preventDefault();
	
	const form = e.target;
	const submitBtn = form.querySelector('button[type="submit"]');
	const messageDiv = document.getElementById('applicationMessage');
	const submitBtnText = submitBtn.querySelector('span:first-child');
	
	// Disable button
	submitBtn.disabled = true;
	const originalText = submitBtnText.textContent;
	submitBtnText.textContent = 'Submitting...';
	messageDiv.classList.add('hidden');
	
	try {
		const formData = new FormData(form);
		
		const response = await fetch('app/api/job_application_submit.php', {
			method: 'POST',
			body: formData
		});
		
		const data = await response.json();
		
		if (data.success) {
			messageDiv.textContent = data.message;
			messageDiv.className = 'p-4 rounded bg-primary/20 border border-primary text-white';
			messageDiv.classList.remove('hidden');
			form.reset();
			cvFileName.textContent = 'Click or drag your resume/CV here';
			
			// Scroll to message
			messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		} else {
			messageDiv.textContent = data.message || 'An error occurred';
			messageDiv.className = 'p-4 rounded bg-red-500/20 border border-red-500 text-red-200';
			messageDiv.classList.remove('hidden');
		}
	} catch (error) {
		messageDiv.textContent = 'Network error. Please try again.';
		messageDiv.className = 'p-4 rounded bg-red-500/20 border border-red-500 text-red-200';
		messageDiv.classList.remove('hidden');
	} finally {
		submitBtn.disabled = false;
		submitBtnText.textContent = originalText;
	}
});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
