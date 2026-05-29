<?php

$pageTitle = 'Technology | Digifyce';
$bodyClass = 'bg-[#05070a] text-white';

include __DIR__ . '/app/views/header.php';

?>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

<style>

/* ===============================
   MOBILE OPTIMIZATION
================================= */

@media (max-width: 1024px) {

    .horizontal-wrapper {
        overflow-x: hidden;
    }

    .horizontal-track {
        flex-direction: column !important;
        gap: 2rem !important;
        width: 100% !important;
    }

    .panel {
        min-width: 100% !important;
        width: 100%;
        padding: 1.5rem !important;
        border-radius: 1.5rem !important;
    }

    .image-slider {
        flex-direction: row !important;
    }

    .slider-img {
        min-width: 100% !important;
        max-width: 100% !important;
        height: 240px;
        object-fit: cover;
    }

    .py-24,
    .lg\:py-32 {
        padding-top: 2.5rem !important;
        padding-bottom: 2.5rem !important;
    }

    .text-4xl,
    .lg\:text-6xl,
    .md\:text-5xl {
        font-size: 2rem !important;
        line-height: 2.5rem !important;
    }

    .text-xl {
        font-size: 1.1rem !important;
    }

    .p-12 {
        padding: 1.5rem !important;
    }

    .mb-20 {
        margin-bottom: 2rem !important;
    }
}

@media (max-width: 640px) {

    .panel {
        padding: 1rem !important;
    }

    .text-4xl,
    .lg\:text-6xl,
    .md\:text-5xl {
        font-size: 1.5rem !important;
        line-height: 2rem !important;
    }

    .text-xl {
        font-size: 1rem !important;
    }

    .p-12 {
        padding: 1rem !important;
    }

    .mb-20 {
        margin-bottom: 1.5rem !important;
    }
}
.panel {
    margin-left: calc((100vw - 85vw) / 2);
    margin-right: calc((100vw - 85vw) / 2);
}

</style>

<main class="min-h-screen">

<!-- HERO -->
<section class="py-24 lg:py-32 border-b border-white/5">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        <span class="text-[var(--electric-blue)] font-bold tracking-[0.3em] text-[10px] uppercase mb-6 block">
            Technology
        </span>

        <h1 class="text-6xl lg:text-8xl font-bold leading-[0.85] tracking-tighter mb-8">
            Technology Engineered for Conversion & Scale
        </h1>

        <p class="text-white/50 text-lg mt-6 max-w-2xl">
            Every technology layer we build exists for one purpose —
            improve performance, automation and growth efficiency.
        </p>

    </div>
</section>
 

<!-- TECHNOLOGY STACK -->
<section class="py-24 border-t border-white/5 overflow-hidden" id="tech-stack-section">

   

    <div class="horizontal-wrapper relative">
        <div class="horizontal-track flex gap-10">

            <!-- PANEL 1 -->
            <div class="min-w-[85vw] glass-card rounded-3xl p-12">

                <div class="grid lg:grid-cols-2 gap-12 items-start">

                    <div>
                        <span class="text-xs font-mono text-[var(--electric-blue)]">01. DEVELOPMENT</span>

                        <h3 class="text-4xl md:text-5xl font-semibold mt-4 mb-8">
                            High Performance Websites
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-xl font-bold mb-2">Website Development</h4>
                                <p class="text-gray-400">
                                    Custom-built websites focused on performance, SEO structure and scalability.
                                </p>
                            </div>

                            <div>
                                <h4 class="text-xl font-bold mb-2">High Conversion Ecommerce</h4>
                                <p class="text-gray-400">
                                    Ecommerce systems engineered for optimized checkout flow and revenue growth.
                                </p>
                            </div>

                            <div>
                                <h4 class="text-xl font-bold mb-2">High Conversion Landing Pages</h4>
                                <p class="text-gray-400">
                                    Landing pages built for paid traffic and lead capture efficiency.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="image-slider flex">
                            <img src="public/assets/tech-img/tech-web.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-web1.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-web2.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-web3.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-web4.png" class="slider-img w-full shrink-0 object-cover ">
                        </div>
                    </div>

                </div>
            </div>

            <!-- PANEL 2 -->
            <div class=" min-w-[85vw] glass-card rounded-3xl p-12">
                <div class="grid lg:grid-cols-2 gap-12 items-center">

                    <div>
                        <span class="text-xs font-mono text-[var(--electric-blue)]">02. CRM SYSTEMS</span>

                        <h3 class="text-4xl md:text-5xl font-semibold mt-4 mb-8">
                            Centralized Customer Infrastructure
                        </h3>

                        <p class="text-xl text-gray-300 leading-relaxed">
                            Connect marketing, sales and communication into one operational layer.
                            Track leads, automate follow-ups and maintain full pipeline visibility.
                        </p>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="image-slider flex">
                            <img src="public/assets/tech-img/tech-crm.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-crm2.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-crm3.png" class="slider-img w-full shrink-0 object-cover ">
                        </div>
                    </div>

                </div>
            </div>

            <!-- PANEL 3 -->
            <div class=" min-w-[85vw] glass-card rounded-3xl p-12">
                <div class="grid lg:grid-cols-2 gap-12 items-center">

                    <div>
                        <span class="text-xs font-mono text-[var(--electric-blue)]">03. AUTOMATION</span>

                        <h3 class="text-4xl md:text-5xl font-semibold mt-4 mb-8">
                            Automation & Conversational Bots
                        </h3>

                        <p class="text-xl text-gray-300 leading-relaxed">
                            AI and rule-based automation bots deployed across WhatsApp,
                            websites and CRM workflows to reduce manual workload and
                            increase response speed.
                        </p>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="image-slider flex">
                            <img src="public/assets/tech-img/tech-zing1.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-zing2.png" class="slider-img w-full shrink-0 object-cover ">
                            <img src="public/assets/tech-img/tech-zing3.png" class="slider-img w-full shrink-0 object-cover ">
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</section>

</main>

<script>

gsap.registerPlugin(ScrollTrigger);

/* DESKTOP ONLY */
let mm = gsap.matchMedia();

mm.add("(min-width: 1025px)", () => {

    const section = document.querySelector("#tech-stack-section");
    const track = document.querySelector(".horizontal-track");
    const panels = gsap.utils.toArray(".panel");

    const getScrollAmount = () => {
        return track.scrollWidth - window.innerWidth;
    };

    gsap.to(track, {
        x: () => -getScrollAmount(),
        ease: "none",
        scrollTrigger: {
            trigger: section,
            start: "top top",
            end: () => "+=" + getScrollAmount(),
            scrub: 1,
            pin: true,
            invalidateOnRefresh: true
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

<?php include __DIR__ . '/app/views/footer.php'; ?>
