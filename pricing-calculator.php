<?php
// One file serves two addresses (see .htaccess):
//   /pricing-calculator            → the calculator (steps 1–2 + contact popup)
//   /pricing-calculator/thank-you  → thank-you page with the price sheet, after the popup is submitted
$isThanks = (($_GET['view'] ?? '') === 'thanks');

if ($isThanks) {
    $pageTitle = 'Thank you – Your price sheet | Digifyce';
    $pageDescription = 'Your selling prices and breakeven ROAS from the Digifyce pricing calculator.';
    $extraHead = '<meta name="robots" content="noindex, nofollow">';
} else {
    $pageTitle = 'D2C Pricing Calculator – Digifyce';
    $pageDescription = 'Find the selling price that breaks even on ads. Enter your manufacturing, packaging and shipping costs and get a price and breakeven ROAS for every product and variant.';
}
include __DIR__ . '/app/views/header.php';
?>
<style>
    /* Page-scoped styles. Kept as plain CSS (not Tailwind utilities) because
       public/assets/css/tailwind.min.css is a pre-built JIT bundle and only
       contains classes that were present when it was last compiled.
       Light theme: a dark title band under the site nav, then a light work area.
       Text colours keep at least WCAG AA contrast on their backgrounds. */

    /* The site nav is 60% transparent; over the light area its white links would
       wash out, so give it a solid dark background on this page only. */
    .main-nav-uppercase { background: rgba(5,7,10,.94) !important; }

    .pc-section { --blue: #0066ff; --blue-d: #0052cc; --ink: #0f172a; --ink-2: #334155; --muted: #475569; --line: #e2e8f0; --line-2: #cbd5e1; --soft: #f8fafc;
        background: #f3f6fb; color: var(--ink); padding-bottom: 4rem; }
    .pc-wrap { width: 92%; max-width: 940px; margin: 0 auto; transition: max-width .3s ease; }
    .pc-section.is-wide .pc-wrap { max-width: 1240px; }
    .pc-sr { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    .pc-section :focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .pc-section .pc-input:focus-visible { outline: none; }

    /* Dark title band. Top padding clears the fixed nav (h-20 = 80px, sm:h-24 = 96px). */
    .pc-band { background: radial-gradient(120% 140% at 85% 0%, #0d1b3a 0%, #05070a 55%); color: #f8fafc; padding: calc(80px + 1.5rem) 0 4.5rem; }
    @media (min-width: 640px) { .pc-band { padding-top: calc(96px + 2rem); } }
    .pc-eyebrow { font-size: 11px; text-transform: uppercase; letter-spacing: .3em; color: #7db4ff; font-weight: 600; }
    .pc-title { margin-top: .5rem; font-size: clamp(1.6rem, 3.2vw, 2.4rem); font-weight: 700; letter-spacing: -0.03em; line-height: 1.15; color: #fff; }

    /* Light work area; the card overlaps the band */
    .pc-card { background: #fff; border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 12px 32px rgba(15,23,42,.08); }
    .pc-main { position: relative; margin-top: -3rem; padding: 1.25rem; }
    @media (min-width: 640px) { .pc-main { padding: 1.75rem; } }

    /* Progress bar at the top of the card */
    .pc-track { height: 4px; border-radius: 999px; background: var(--line); overflow: hidden; }
    .pc-fill { height: 100%; border-radius: 999px; background: var(--blue); transition: width .5s ease-out; }

    .pc-body { margin-top: 1.5rem; }
    .pc-h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.02em; color: var(--ink); }
    .pc-h2:focus { outline: none; }
    .pc-h3 { font-size: 1rem; font-weight: 700; color: var(--ink); }
    .pc-help { margin-top: .3rem; color: var(--muted); font-size: 14px; line-height: 1.5; }

    /* Fields */
    .pc-field label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); font-weight: 600; }
    .pc-input-wrap { position: relative; margin-top: .4rem; }
    .pc-input { width: 100%; min-height: 44px; border-radius: 10px; border: 1px solid var(--line-2); background: #fff; padding: .65rem .85rem; color: var(--ink); font: inherit; font-size: 15px; outline: none; transition: border-color .2s, box-shadow .2s; }
    .pc-input::placeholder { color: #7b8798; }
    .pc-input:hover { border-color: #94a3b8; }
    .pc-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(0,102,255,.18); }
    .pc-input[aria-invalid="true"] { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.15); }
    .pc-input.has-pre { padding-left: 1.75rem; }
    .pc-pre { position: absolute; top: 50%; left: .8rem; transform: translateY(-50%); color: #64748b; font-size: 14px; pointer-events: none; }
    .pc-error { display: flex; gap: .4rem; align-items: center; margin-top: .9rem; padding: .6rem .8rem; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca; font-size: 14px; color: #b91c1c; }
    .pc-error[hidden] { display: none; }
    .pc-error .material-symbols-outlined { font-size: 18px; }

    /* Buttons */
    .pc-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; min-height: 44px; border-radius: 999px; padding: .65rem 1.3rem; font: inherit; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; cursor: pointer; transition: background .2s, color .2s, border-color .2s, opacity .2s; border: 1px solid transparent; text-decoration: none; }
    .pc-btn .material-symbols-outlined { font-size: 18px; }
    .pc-btn-primary { background: var(--blue); color: #fff; }
    .pc-btn-primary:hover { background: var(--blue-d); }
    .pc-btn-primary[disabled] { opacity: .6; cursor: wait; }
    .pc-btn-ghost { background: #fff; color: var(--ink); border-color: var(--line-2); }
    .pc-btn-ghost:hover { border-color: var(--blue); color: var(--blue-d); }
    .pc-link { display: inline-flex; align-items: center; gap: .3rem; min-height: 36px; padding: .25rem .1rem; background: none; border: 0; color: var(--blue-d); font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; }
    .pc-link:hover { color: #003d99; text-decoration: underline; }
    .pc-link .material-symbols-outlined { font-size: 18px; }
    .pc-link small { color: #64748b; font-weight: 400; font-size: 13px; }
    .pc-icon-btn { width: 40px; height: 40px; flex: none; border-radius: 999px; border: 1px solid var(--line); background: #fff; color: var(--muted); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: color .2s, border-color .2s, background .2s; }
    .pc-icon-btn .material-symbols-outlined { font-size: 18px; }
    .pc-icon-btn:hover { color: var(--ink); border-color: #94a3b8; }
    .pc-icon-btn.is-danger:hover { color: #dc2626; border-color: #fecaca; background: #fef2f2; }

    /* Back / Next stay pinned to the bottom of the screen while filling in */
    .pc-nav { position: sticky; bottom: 0; z-index: 5; display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem -1.25rem -1.25rem; padding: .85rem 1.25rem; background: rgba(255,255,255,.96); backdrop-filter: blur(8px); border-top: 1px solid var(--line); border-radius: 0 0 16px 16px; }
    @media (min-width: 640px) { .pc-nav { margin: 1.75rem -1.75rem -1.75rem; padding: .9rem 1.75rem; } }
    .pc-nav[hidden] { display: none; }
    .pc-nav-hint { font-size: 13px; color: #64748b; display: none; }
    @media (min-width: 720px) { .pc-nav-hint { display: block; } }

    /* Step 1: brand dropdowns */
    .pc-req { color: var(--blue); }
    .pc-brand { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 1rem; margin-top: 1rem; }
    @media (max-width: 640px) { .pc-brand { grid-template-columns: minmax(0,1fr); } }
    .pc-select-wrap { position: relative; margin-top: .4rem; }
    .pc-select { appearance: none; -webkit-appearance: none; padding-right: 2.75rem; cursor: pointer; color-scheme: light; text-overflow: ellipsis; }
    .pc-select:invalid, .pc-select.is-optional.is-empty { color: #64748b; } /* placeholder option shown in grey */
    .pc-select option { background: #fff; color: var(--ink); }
    .pc-select-wrap::after { content: ''; position: absolute; right: 1rem; top: 50%; width: 8px; height: 8px; margin-top: -6px; border-right: 2px solid #64748b; border-bottom: 2px solid #64748b; transform: rotate(45deg); pointer-events: none; }

    /* Step 1: products */
    .pc-products { display: grid; gap: .75rem; margin-top: 1.1rem; }
    .pc-product { border: 1px solid var(--line); border-radius: 12px; padding: 1rem 1.1rem; background: var(--soft); }
    .pc-prow { display: flex; gap: .75rem; align-items: flex-end; }
    .pc-prow .pc-field { flex: 1; }
    .pc-vgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: .6rem; margin-top: .75rem; }
    .pc-vitem { display: flex; gap: .5rem; align-items: center; }
    .pc-vitem .pc-input-wrap { margin-top: 0; flex: 1; }
    .pc-pfoot { margin-top: .5rem; }

    /* Step 2: compact cost grid per product (stacks on phones) */
    .pc-cost { border: 1px solid var(--line); border-radius: 12px; padding: 1rem 1.1rem; background: var(--soft); }
    .pc-cost + .pc-cost { margin-top: .75rem; }
    .pc-chead, .pc-crow { display: grid; grid-template-columns: var(--cols); gap: .6rem; align-items: center; }
    .pc-chead { margin-top: .8rem; padding-bottom: .35rem; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; font-weight: 600; color: var(--muted); }
    .pc-chead span:last-child, .pc-cbase { text-align: right; }
    .pc-crow { padding: .45rem 0; border-top: 1px solid var(--line); }
    .pc-cname { font-size: 14px; font-weight: 600; color: var(--ink); overflow-wrap: anywhere; }
    .pc-ccell .pc-input-wrap { margin-top: 0; }
    .pc-cbase { font-size: 15px; font-weight: 700; color: var(--ink); font-variant-numeric: tabular-nums; }
    .pc-cl { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
    @media (max-width: 640px) {
        .pc-chead { display: none; }
        .pc-crow { grid-template-columns: 1fr 1fr; padding: .75rem 0; }
        .pc-cname, .pc-cbase { grid-column: 1 / -1; }
        .pc-cbase { text-align: left; font-size: 14px; }
        .pc-cl { position: static; width: auto; height: auto; overflow: visible; clip: auto; white-space: normal; display: block; margin-bottom: .3rem; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; font-weight: 600; color: var(--muted); }
    }

    /* Other costs: folded away until needed */
    .pc-more { margin-top: .6rem; }
    .pc-more > summary { list-style: none; }
    .pc-more > summary::-webkit-details-marker { display: none; }
    .pc-more[open] > summary .pc-more-icon { transform: rotate(45deg); }
    .pc-more-icon { transition: transform .2s; }
    .pc-extra-form { display: grid; grid-template-columns: 1fr 1fr auto; gap: .6rem; margin-top: .6rem; align-items: center; }
    .pc-extra-form .pc-input-wrap { margin-top: 0; }
    @media (max-width: 560px) { .pc-extra-form { grid-template-columns: 1fr; } }
    .pc-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .6rem; }
    .pc-chip { display: inline-flex; align-items: center; gap: .35rem; font-size: 13px; color: var(--ink); background: #fff; border: 1px solid var(--line-2); border-radius: 999px; padding: .2rem .25rem .2rem .75rem; }
    .pc-chip button { width: 28px; height: 28px; border-radius: 999px; background: none; border: 0; color: #64748b; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .pc-chip button:hover { color: #dc2626; background: #fef2f2; }
    .pc-chip .material-symbols-outlined { font-size: 16px; }

    /* Thank-you page: price sheet table */
    .pc-result-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 1rem; }
    .pc-actions { display: flex; flex-wrap: wrap; gap: .6rem; }
    .pc-sheet { margin-top: 1rem; border: 1px solid var(--line); border-radius: 12px; overflow: auto; max-height: 62vh; scrollbar-width: thin; scrollbar-color: rgba(15,23,42,.25) transparent; }
    .pc-sheet::-webkit-scrollbar { height: 8px; width: 8px; }
    .pc-sheet::-webkit-scrollbar-track, .pc-sheet::-webkit-scrollbar-corner { background: transparent; }
    .pc-sheet::-webkit-scrollbar-thumb { background: rgba(15,23,42,.2); border-radius: 999px; }
    .pc-sheet::-webkit-scrollbar-thumb:hover { background: rgba(15,23,42,.35); }
    .pc-sheet::-webkit-scrollbar-button { display: none; }
    .pc-sheet table { border-collapse: separate; border-spacing: 0; font-size: 14px; min-width: 100%; }
    .pc-sheet th, .pc-sheet td { padding: .65rem .9rem; white-space: nowrap; border-bottom: 1px solid var(--line); }
    .pc-sheet thead th { position: sticky; top: 0; z-index: 2; background: #f1f5f9; color: var(--muted); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; text-align: right; }
    .pc-sheet td, .pc-sheet tbody th { background: #fff; color: #1e293b; text-align: right; font-weight: 400; font-variant-numeric: tabular-nums; }
    .pc-sheet .is-text { text-align: left; }
    .pc-sheet .pc-stick { position: sticky; left: 0; z-index: 1; box-shadow: 1px 0 0 var(--line); }
    .pc-sheet tbody th.pc-stick { color: var(--ink); font-weight: 600; }
    .pc-sheet thead th.pc-stick { z-index: 3; }
    .pc-sheet td.is-key { color: #0b3d91; font-weight: 700; background: #eff6ff; }
    .pc-sheet thead th.is-key { color: #1d4ed8; background: #dbeafe; }
    .pc-sheet tbody tr:last-child > * { border-bottom: 0; }
    .pc-sheet tr.is-group > * { border-top: 1px solid var(--line-2); }
    .pc-sheet tbody tr:hover > td, .pc-sheet tbody tr:hover > th { background: #f8fafc; }
    .pc-sheet tbody tr:hover > td.is-key { background: #e0edff; }
    .pc-sheet-note { margin-top: .5rem; font-size: 13px; color: #64748b; }

    /* Contact popup */
    .pc-modal { position: fixed; inset: 0; z-index: 70; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(15,23,42,.55); backdrop-filter: blur(3px); }
    .pc-modal.is-open { display: flex; }
    .pc-dialog { position: relative; width: 100%; max-width: 440px; max-height: calc(100vh - 2rem); overflow-y: auto; border-radius: 16px; border: 1px solid var(--line); background: #fff; color: var(--ink); padding: 1.75rem 1.5rem 1.5rem; box-shadow: 0 30px 80px rgba(15,23,42,.3); }
    .pc-dialog .pc-icon-btn { position: absolute; top: .9rem; right: .9rem; }

    /* "Almost there 🎉": the popper pops in and throws a little confetti each time the popup opens
       (CSS animations restart when the popup goes from display:none to flex). */
    .pc-almost { display: flex; align-items: center; gap: .5rem; padding-right: 2.5rem; font-size: 1.35rem; font-weight: 700; letter-spacing: -0.02em; color: var(--ink); }
    .pc-popper { position: relative; display: inline-block; font-size: 1.6rem; line-height: 1; transform-origin: 30% 80%; animation: pcPop .7s cubic-bezier(.2,1.6,.4,1) both, pcWiggle 1.2s ease-in-out .7s 2; }
    .pc-confetti { position: absolute; left: 55%; top: 35%; width: 0; height: 0; pointer-events: none; }
    .pc-confetti i { position: absolute; width: 6px; height: 9px; border-radius: 2px; opacity: 0; animation: pcBurst .9s ease-out .25s forwards; }
    .pc-confetti i:nth-child(1) { --x: 34px;  --y: -30px; --r: 120deg; background: #0066ff; }
    .pc-confetti i:nth-child(2) { --x: 46px;  --y: -6px;  --r: 220deg; background: #f59e0b; }
    .pc-confetti i:nth-child(3) { --x: 28px;  --y: 22px;  --r: 80deg;  background: #ec4899; }
    .pc-confetti i:nth-child(4) { --x: 4px;   --y: -42px; --r: 300deg; background: #22c55e; }
    .pc-confetti i:nth-child(5) { --x: -24px; --y: -34px; --r: 160deg; background: #f97316; }
    .pc-confetti i:nth-child(6) { --x: 52px;  --y: -26px; --r: 40deg;  background: #8b5cf6; width: 5px; height: 5px; border-radius: 999px; }
    .pc-confetti i:nth-child(7) { --x: -14px; --y: 16px;  --r: 260deg; background: #06b6d4; width: 5px; height: 5px; border-radius: 999px; }
    .pc-confetti i:nth-child(8) { --x: 16px;  --y: 36px;  --r: 200deg; background: #eab308; }
    @keyframes pcPop { 0% { transform: scale(0) rotate(-40deg); opacity: 0; } 70% { opacity: 1; } 100% { transform: scale(1) rotate(0); opacity: 1; } }
    @keyframes pcWiggle { 0%, 100% { transform: rotate(0); } 25% { transform: rotate(-14deg); } 75% { transform: rotate(10deg); } }
    @keyframes pcBurst { 0% { opacity: 1; transform: translate(0,0) rotate(0) scale(.6); } 100% { opacity: 0; transform: translate(var(--x), var(--y)) rotate(var(--r)) scale(1); } }
    .pc-dialog .pc-field + .pc-field { margin-top: .9rem; }
    .pc-dialog form { margin-top: 1.1rem; }
    .pc-dialog .pc-btn-primary { width: 100%; margin-top: 1.25rem; }
    .pc-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

    .pc-toast { position: fixed; left: 50%; bottom: 90px; transform: translateX(-50%) translateY(20px); opacity: 0; background: var(--ink); color: #fff; padding: .65rem 1.1rem; border-radius: 999px; font-size: 14px; font-weight: 600; transition: opacity .25s, transform .25s; pointer-events: none; z-index: 80; box-shadow: 0 10px 30px rgba(15,23,42,.25); }
    .pc-toast.is-on { opacity: 1; transform: translateX(-50%) translateY(0); }

    @media (prefers-reduced-motion: reduce) {
        .pc-section *, .pc-section *::before, .pc-section *::after { transition: none !important; animation: none !important; }
    }
</style>

<section class="pc-section<?= $isThanks ? ' is-wide' : '' ?>" id="pcSection">
    <div class="pc-band">
        <div class="pc-wrap">
            <?php if ($isThanks): ?>
                <h1 class="pc-title">Thank you<span id="pcThanksName"></span>! Your price sheet is ready.</h1>
            <?php else: ?>
                <div class="pc-eyebrow">Pricing Calculator</div>
                <h1 class="pc-title">Find your selling price and breakeven ROAS.</h1>
            <?php endif; ?>
        </div>
    </div>

    <div class="pc-wrap">
        <div class="pc-card pc-main">
            <div class="pc-track" id="pcTrack" role="progressbar" aria-label="Progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="33">
                <div class="pc-fill" id="pcFill" style="width:33%"></div>
            </div>
            <p class="pc-sr" id="pcStatus" aria-live="polite"></p>

            <div class="pc-body" id="pcBody"></div>

            <div class="pc-nav" id="pcNav"<?= $isThanks ? ' hidden' : '' ?>>
                <button type="button" class="pc-btn pc-btn-ghost" data-a="back" id="pcBack">
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>Back
                </button>
                <span class="pc-nav-hint" id="pcNavHint"></span>
                <button type="button" class="pc-btn pc-btn-primary" data-a="next" id="pcNext">
                    <span id="pcNextText">Next</span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                </button>
            </div>
        </div>
    </div>

    <?php if (!$isThanks): ?>
    <!-- Contact details, asked once before the price sheet -->
    <div class="pc-modal" id="pcModal" aria-hidden="true">
        <div class="pc-dialog" role="dialog" aria-modal="true" aria-labelledby="pcModalTitle" aria-describedby="pcModalDesc">
            <button type="button" class="pc-icon-btn" data-a="close-modal" aria-label="Close"><span class="material-symbols-outlined" aria-hidden="true">close</span></button>
            <h2 class="pc-almost" id="pcModalTitle">Almost there
                <span class="pc-popper" aria-hidden="true">🎉<span class="pc-confetti"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span></span>
            </h2>
            <p class="pc-help" id="pcModalDesc">Enter your details to see your price sheet.</p>
            <form id="pcLeadForm" novalidate>
                <div class="pc-field">
                    <label for="pcName">Full name</label>
                    <div class="pc-input-wrap"><input id="pcName" name="full_name" class="pc-input" type="text" autocomplete="name" required aria-required="true" placeholder="Your name"></div>
                </div>
                <div class="pc-field">
                    <label for="pcPhone">Phone number</label>
                    <div class="pc-input-wrap"><input id="pcPhone" name="phone" class="pc-input" type="tel" autocomplete="tel" inputmode="tel" required aria-required="true" placeholder="+91 98765 43210"></div>
                </div>
                <div class="pc-field">
                    <label for="pcEmail">Email</label>
                    <div class="pc-input-wrap"><input id="pcEmail" name="email" class="pc-input" type="email" autocomplete="email" required aria-required="true" placeholder="you@brand.com"></div>
                </div>
                <div class="pc-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <div class="pc-error" id="pcFormError" role="alert" hidden></div>
                <button type="submit" class="pc-btn pc-btn-primary" id="pcSubmit">Show my prices<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="pc-toast" id="pcToast" role="status" aria-live="polite"></div>
</section>

<script>
(function () {
    'use strict';

    var VIEW = <?= json_encode($isThanks ? 'thanks' : 'calc') ?>;
    var CALC_URL = <?= json_encode($appUrl . '/pricing-calculator') ?>;
    var THANKS_URL = <?= json_encode($appUrl . '/pricing-calculator/thank-you') ?>;
    var LEAD_URL = <?= json_encode($appUrl . '/app/api/pricing_calculator_lead.php') ?>;
    var STORE_KEY = 'digifyce_pricing_calc_v1';
    var NAME_KEY = 'digifyce_pricing_calc_name'; // first name for the thank-you greeting (this tab only)
    var STEPS = ['Products', 'Unit Costs', 'Price Sheet'];
    var RESULT_STEP = STEPS.length - 1; // the price sheet, shown on the thank-you page

    // Fixed pricing assumptions. Visitors only enter their own costs; these are
    // not editable on the page. Change them here.
    var D = {
        rto: 15,        // % of orders returned to origin (cost loaded onto delivered orders)
        pg: 2,          // % payment gateway / COD remittance fee on the selling price
        roas: 3,        // target ROAS the price is set for
        comp: 1.5,      // compare-at (MRP) = selling price × this
        gstLimit: 2000, // GST slab: selling price up to this → gstLow, above → gstHigh
        gstLow: 5,
        gstHigh: 18
    };

    // "What does your brand sell?" Same keys as the main lead form (leadform.php),
    // so admin shows the same labels for leads from either form.
    var INDUSTRIES = [
        { key: 'beauty_personal_care', label: 'Beauty & personal care products', hint: 'makeup, kajal, skincare, haircare' },
        { key: 'food_beverage_fmcg', label: 'Food, beverages & packaged consumer goods' },
        { key: 'fashion_apparel_textile', label: 'Fashion, apparel & textiles' },
        { key: 'other_consumer_product', label: 'Other consumer product' }
    ];

    // "Estimated monthly marketing budget" (optional). Same keys as leadform.php.
    var BUDGETS = [
        { key: '50000', label: '₹50,000' },
        { key: '1lakh', label: '₹1 Lakh' },
        { key: 'above_1lakh', label: 'Above ₹1 Lakh' }
    ];

    var root = document.getElementById('pcSection');
    var body = document.getElementById('pcBody');
    var modal = document.getElementById('pcModal');   // calculator page only
    var form = document.getElementById('pcLeadForm'); // calculator page only
    var moreOpen = {}; // product id → "Other costs" panel open (kept across re-renders)

    /* ---------- state ----------
       Kept in localStorage so the thank-you page can show the same products.
       S.products = [{ id, name, extras: [{ id, name, value }], variants: [{ id, name, mfg, pack, ship, x: { extraId: value }, implicit? }] }]
       A product with no variants still needs one row of costs to price. That row
       is "implicit": hidden from the variant list and labelled with the product
       name. The first "Add variant" click turns it into a real, named variant. */

    function uid() { return Math.random().toString(36).slice(2, 9); }
    function newVariant(name) { return { id: uid(), name: name, mfg: '', pack: '20', ship: '70', x: {} }; }
    function implicitVariant() { var v = newVariant(''); v.implicit = true; return v; }
    function newProduct() { return { id: uid(), name: '', extras: [], variants: [implicitVariant()] }; }
    function hasVariants(p) { return !p.variants[0].implicit; }
    function freshState() { return { step: 0, unlocked: false, industry: '', budget: '', products: [newProduct()] }; }

    function load() {
        try {
            var s = JSON.parse(localStorage.getItem(STORE_KEY));
            if (!s) return freshState();
            // Older versions priced a single product: { product, extras, variants }.
            if (!s.products && s.variants && s.variants.length) {
                s.products = [{ id: uid(), name: s.product || '', extras: s.extras || [], variants: s.variants }];
            }
            if (!s.products || !s.products.length) return freshState();
            s.products.forEach(function (p) {
                p.extras = p.extras || [];
                p.variants.forEach(function (v) { delete v.ov; v.x = v.x || {}; });
                var only = p.variants[0];
                if (p.variants.length === 1 && only.implicit === undefined && (only.name === 'Default' || !has(only.name))) {
                    only.implicit = true; only.name = '';
                }
            });
            // The calculator page only has steps 1–2; the price sheet lives on the thank-you page.
            return { step: Math.min(s.step || 0, RESULT_STEP - 1), unlocked: !!s.unlocked, industry: s.industry || '', budget: s.budget || '', products: s.products };
        } catch (e) {}
        return freshState();
    }
    var S = load();
    function save() { try { localStorage.setItem(STORE_KEY, JSON.stringify(S)); } catch (e) {} }

    function findP(id) { for (var i = 0; i < S.products.length; i++) if (S.products[i].id === id) return S.products[i]; return null; }
    function findV(p, id) { for (var i = 0; i < p.variants.length; i++) if (p.variants[i].id === id) return p.variants[i]; return null; }

    /* ---------- maths ---------- */

    function num(v) { var n = parseFloat(String(v == null ? '' : v).replace(/,/g, '')); return isFinite(n) ? n : 0; }
    function has(v) { return v !== undefined && v !== null && String(v).trim() !== ''; }

    // An extra cost is either a flat ₹ amount ("15") or a percentage of the
    // unit cost ("10%" = 10% of Manufacturing + Packaging + Shipping).
    function extraAmount(str, unitSum) {
        str = String(str || '').trim();
        if (!str) return 0;
        if (str.slice(-1) === '%') return unitSum * num(str.slice(0, -1)) / 100;
        return num(str);
    }
    function extraValue(v, e) { return v.x[e.id] !== undefined ? v.x[e.id] : e.value; }

    // Round up to the next price ending in 49 or 99 (₹326 → ₹349, ₹352 → ₹399).
    function roundPrice(x) {
        if (!(x > 0) || !isFinite(x)) return NaN;
        x = Math.round(x * 100) / 100; // drop float noise (e.g. 448.50000001)
        var h = Math.floor(x / 100) * 100;
        if (x <= h + 49) return h + 49;
        if (x <= h + 99) return h + 99;
        return h + 149;
    }

    function calc(p, v) {
        var unit = num(v.mfg) + num(v.pack) + num(v.ship);
        var extras = 0;
        p.extras.forEach(function (e) { extras += extraAmount(extraValue(v, e), unit); });
        var base = unit + extras;
        var mult = 1 + D.rto / 100;
        var adj = base * mult;
        var pg = D.pg / 100;

        // Selling price (GST-inclusive) at which GST, the gateway fee and ad spend
        // at the target ROAS leave exactly the adjusted cost, rounded up.
        function priceAt(taxPct) {
            var keep = 1 / (1 + taxPct / 100) - pg - 1 / D.roas;
            return keep > 0 ? roundPrice(adj / keep) : NaN;
        }
        // GST depends on the price and the price on GST: price at the lower slab
        // first; if that lands above the limit, re-price at the higher slab.
        var tax = D.gstLow, sp = priceAt(tax);
        if (!(sp <= D.gstLimit)) { tax = D.gstHigh; sp = priceAt(tax); }

        var ok = isFinite(sp) && sp > 0;
        var gstAmt = ok ? sp - sp / (1 + tax / 100) : 0;
        var pgAmt = ok ? sp * pg : 0;
        // What's left for ads once GST, gateway fee and adjusted cost are paid;
        // breakeven ROAS is the price divided by that.
        var room = ok ? sp - gstAmt - pgAmt - adj : 0;
        return {
            unit: unit, extras: extras, base: base, mult: mult, adj: adj, tax: tax, sp: sp, ok: ok,
            gstAmt: gstAmt, pgAmt: pgAmt,
            beRoas: room > 0 ? sp / room : NaN,
            comp: ok ? roundPrice(sp * D.comp) : NaN
        };
    }

    /* ---------- formatting ---------- */

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function inr(n) { return isFinite(n) ? '₹' + Math.round(n).toLocaleString('en-IN') : '—'; }
    function inr2(n) { return isFinite(n) ? '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''; }
    function r2(n) { return isFinite(n) ? Math.round(n * 100) / 100 : ''; }
    function pname(p, i) { return has(p.name) ? p.name : 'Product ' + (i + 1); }
    function vname(p, v, i) {
        if (v.implicit) return '';
        return has(v.name) ? v.name : 'Variant ' + (i + 1);
    }
    function fullName(p, pi, v, vi) { var n = vname(p, v, vi); return pname(p, pi) + (n ? ' · ' + n : ''); }
    function icon(name) { return '<span class="material-symbols-outlined" aria-hidden="true">' + name + '</span>'; }

    // A text input with a visible label. Cost inputs use type="text" + inputmode
    // (not type="number") so the mouse wheel can't change a value by accident.
    function input(o) {
        return '<div class="pc-input-wrap">' +
            (o.pre ? '<span class="pc-pre" aria-hidden="true">₹</span>' : '') +
            '<input id="' + o.id + '" class="pc-input' + (o.pre ? ' has-pre' : '') + '" type="text"' +
                (o.mode ? ' inputmode="' + o.mode + '"' : '') + ' autocomplete="off"' +
                ' data-f="' + o.f + '" data-p="' + o.p + '"' + (o.v ? ' data-v="' + o.v + '"' : '') +
                (o.aria ? ' aria-label="' + esc(o.aria) + '"' : '') +
                (o.required ? ' aria-required="true"' : '') +
                (o.ph !== undefined ? ' placeholder="' + esc(o.ph) + '"' : '') +
                ' value="' + esc(o.value) + '">' +
        '</div>';
    }

    /* ---------- price sheet: one column list drives the table and the download ---------- */

    function otherCostsText(p, v) {
        return p.extras.map(function (e) {
            var val = String(extraValue(v, e) || '').trim();
            return e.name + ' ' + (val.slice(-1) === '%' ? val : '₹' + num(val));
        }).join('; ');
    }

    // kind: 'text' | 'inr' (2 decimals) | 'price' (whole ₹) | 'pct' | 'x'
    var COLUMNS = [
        { h: 'Product', kind: 'text', get: function (r) { return r.product; } },
        { h: 'Variant', kind: 'text', get: function (r) { return r.variant || '—'; } },
        { h: 'Selling price', kind: 'price', key: true, get: function (r) { return r.c.sp; } },
        { h: 'Breakeven ROAS', kind: 'x', key: true, get: function (r) { return r.c.beRoas; } },
        { h: 'Manufacturing', kind: 'inr', get: function (r) { return num(r.v.mfg); } },
        { h: 'Packaging', kind: 'inr', get: function (r) { return num(r.v.pack); } },
        { h: 'Shipping', kind: 'inr', get: function (r) { return num(r.v.ship); } },
        { h: 'Other costs', kind: 'inr', get: function (r) { return r.c.extras; } },
        { h: 'Other cost details', kind: 'text', get: function (r) { return otherCostsText(r.p, r.v) || '—'; } },
        { h: 'Cost per unit', kind: 'inr', get: function (r) { return r.c.base; } },
        { h: 'RTO %', kind: 'pct', get: function () { return D.rto; } },
        { h: 'RTO multiplier', kind: 'x', get: function (r) { return r.c.mult; } },
        { h: 'Adjusted cost', kind: 'inr', get: function (r) { return r.c.adj; } },
        { h: 'GST %', kind: 'pct', get: function (r) { return r.c.tax; } },
        { h: 'PG fee %', kind: 'pct', get: function () { return D.pg; } },
        { h: 'Target ROAS', kind: 'x', get: function () { return D.roas; } },
        { h: 'GST in price', kind: 'inr', get: function (r) { return r.c.gstAmt; } },
        { h: 'PG fee', kind: 'inr', get: function (r) { return r.c.pgAmt; } },
        { h: 'Compare-at multiplier', kind: 'x', get: function () { return D.comp; } },
        { h: 'Compare-at price', kind: 'price', get: function (r) { return r.c.comp; } }
    ];

    function sheetRows() {
        var rows = [];
        S.products.forEach(function (p, pi) {
            p.variants.forEach(function (v, vi) {
                rows.push({ p: p, v: v, product: pname(p, pi), variant: vname(p, v, vi), c: calc(p, v), first: vi === 0 });
            });
        });
        return rows;
    }

    // Plain text shown in a cell (escape before putting it in HTML).
    function displayCell(kind, val) {
        if (kind === 'text') return String(val);
        if (!isFinite(val)) return '';
        if (kind === 'inr') return inr2(val);
        if (kind === 'price') return inr(val);
        if (kind === 'pct') return val + '%';
        return (Math.round(val * 100) / 100).toFixed(2) + 'x';
    }
    function rawCell(kind, val) {
        if (kind === 'text') return val;
        if (!isFinite(val)) return '';
        if (kind === 'x') return (Math.round(val * 100) / 100).toFixed(2);
        return r2(val);
    }

    function renderSheet() {
        var head = '<tr>' + COLUMNS.map(function (c, i) {
            var cls = [c.kind === 'text' ? 'is-text' : '', i === 0 ? 'pc-stick' : '', c.key ? 'is-key' : ''].join(' ').trim();
            return '<th scope="col"' + (cls ? ' class="' + cls + '"' : '') + '>' + esc(c.h) + '</th>';
        }).join('') + '</tr>';
        var bodyRows = sheetRows().map(function (r, ri) {
            return '<tr' + (r.first && ri > 0 ? ' class="is-group"' : '') + '>' + COLUMNS.map(function (c, ci) {
                var cls = [c.kind === 'text' ? 'is-text' : '', ci === 0 ? 'pc-stick' : '', c.key ? 'is-key' : ''].join(' ').trim();
                var tag = ci === 0 ? 'th scope="row"' : 'td';
                return '<' + tag + (cls ? ' class="' + cls + '"' : '') + '>' + esc(displayCell(c.kind, c.get(r))) + '</' + tag.split(' ')[0] + '>';
            }).join('') + '</tr>';
        }).join('');
        // Focusable so keyboard users can scroll the table with the arrow keys.
        return '<div class="pc-sheet" role="region" aria-label="Price sheet, scrolls sideways" tabindex="0">' +
            '<table><caption class="pc-sr">Selling price and breakeven ROAS for each product and variant</caption>' +
            '<thead>' + head + '</thead><tbody>' + bodyRows + '</tbody></table></div>';
    }

    /* ---------- calculator steps ---------- */

    // Empty error box each step renders; showStepError() below fills it in.
    function stepErrorBox() { return '<div class="pc-error" id="pcStepError" role="alert" hidden></div>'; }

    function stepProducts() {
        var cards = S.products.map(function (p, pi) {
            var variants = hasVariants(p) ? '<div class="pc-vgrid">' + p.variants.map(function (v, vi) {
                return '<div class="pc-vitem">' +
                    input({ id: 'vname_' + v.id, f: 'v.name', p: p.id, v: v.id, value: v.name, ph: 'e.g. 100 ml, Pack of 2', aria: pname(p, pi) + ', variant ' + (vi + 1) + ' name' }) +
                    '<button type="button" class="pc-icon-btn is-danger" data-a="remove-variant" data-p="' + p.id + '" data-v="' + v.id + '" aria-label="Remove variant ' + esc(vname(p, v, vi)) + '">' + icon('close') + '</button>' +
                '</div>';
            }).join('') + '</div>' : '';
            return '<div class="pc-product" role="group" aria-labelledby="pnameL_' + p.id + '">' +
                '<div class="pc-prow">' +
                    '<div class="pc-field"><label id="pnameL_' + p.id + '" for="pname_' + p.id + '">Product ' + (pi + 1) + ' name</label>' +
                        input({ id: 'pname_' + p.id, f: 'p.name', p: p.id, value: p.name, ph: 'e.g. Kajal Stick', required: true }) + '</div>' +
                    (S.products.length > 1 ? '<button type="button" class="pc-icon-btn is-danger" data-a="remove-product" data-p="' + p.id + '" aria-label="Remove ' + esc(pname(p, pi)) + '">' + icon('delete') + '</button>' : '') +
                '</div>' +
                variants +
                '<div class="pc-pfoot"><button type="button" class="pc-link" data-a="add-variant" data-p="' + p.id + '">' + icon('add') + 'Add variant <small>(size, pack or colour)</small></button></div>' +
            '</div>';
        }).join('');
        var industries = '<option value=""' + (S.industry ? '' : ' selected') + ' disabled>Select a category</option>' +
            INDUSTRIES.map(function (o) {
                return '<option value="' + o.key + '"' + (S.industry === o.key ? ' selected' : '') + '>' +
                    esc(o.label + (o.hint ? ' (' + o.hint + ')' : '')) + '</option>';
            }).join('');
        var budgets = '<option value=""' + (S.budget ? '' : ' selected') + '>Select budget</option>' +
            BUDGETS.map(function (o) {
                return '<option value="' + o.key + '"' + (S.budget === o.key ? ' selected' : '') + '>' + esc(o.label) + '</option>';
            }).join('');
        return '<h2 class="pc-h2" id="pcStepTitle" tabindex="-1">About your brand</h2>' +
            '<div class="pc-brand">' +
                '<div class="pc-field"><label for="pcIndustry">What does your brand sell? <span class="pc-req" aria-hidden="true">*</span><span class="pc-sr">(required)</span></label>' +
                    '<div class="pc-select-wrap"><select id="pcIndustry" class="pc-input pc-select" required aria-required="true" aria-describedby="pcIndustryError">' + industries + '</select></div></div>' +
                '<div class="pc-field"><label for="pcBudget">Estimated monthly marketing budget</label>' +
                    '<div class="pc-select-wrap"><select id="pcBudget" class="pc-input pc-select is-optional' + (S.budget ? '' : ' is-empty') + '">' + budgets + '</select></div></div>' +
            '</div>' +
            '<div class="pc-error" id="pcIndustryError" role="alert" hidden></div>' +
            '<h2 class="pc-h2" style="margin-top:2rem">What are you pricing?</h2>' +
            '<p class="pc-help">Add each product. Variants are optional.</p>' +
            '<div class="pc-products">' + cards + '</div>' +
            stepErrorBox() +
            '<div style="margin-top:.75rem"><button type="button" class="pc-btn pc-btn-ghost" data-a="add-product">' + icon('add') + 'Add another product</button></div>';
    }

    function otherCosts(p, pi) {
        var chips = p.extras.map(function (e) {
            return '<span class="pc-chip">' + esc(e.name) + ' · ' + esc(e.value || '0') +
                '<button type="button" data-a="remove-extra" data-p="' + p.id + '" data-e="' + e.id + '" aria-label="Remove ' + esc(e.name) + '">' + icon('close') + '</button></span>';
        }).join('');
        return (chips ? '<div class="pc-chips">' + chips + '</div>' : '') +
            '<details class="pc-more" data-more="' + p.id + '"' + (moreOpen[p.id] ? ' open' : '') + '>' +
                '<summary class="pc-link">' + '<span class="material-symbols-outlined pc-more-icon" aria-hidden="true">add</span>Add another cost <small>(labels, printing, courier bag, COD…)</small></summary>' +
                '<div class="pc-extra-form">' +
                    '<div class="pc-input-wrap"><input class="pc-input" type="text" id="exName_' + p.id + '" data-extra-name="' + p.id + '" aria-label="' + esc(pname(p, pi)) + ': cost name" placeholder="Name (e.g. Printing)"></div>' +
                    '<div class="pc-input-wrap"><input class="pc-input" type="text" id="exVal_' + p.id + '" data-extra-val="' + p.id + '" aria-label="' + esc(pname(p, pi)) + ': cost amount, flat or percentage" placeholder="Amount (15 or 10%)"></div>' +
                    '<button type="button" class="pc-btn pc-btn-primary" data-a="add-extra" data-p="' + p.id + '">' + icon('add') + 'Add</button>' +
                '</div>' +
            '</details>';
    }

    function stepCosts() {
        var sections = S.products.map(function (p, pi) {
            var cols = 'minmax(110px,1.2fr) repeat(' + (3 + p.extras.length) + ', minmax(84px,1fr)) minmax(80px,.7fr)';
            var heads = ['Manufacturing', 'Packaging', 'Shipping'].concat(p.extras.map(function (e) { return e.name; }));
            var headRow = '<div class="pc-chead" style="--cols:' + cols + '" aria-hidden="true"><span>' + (hasVariants(p) ? 'Variant' : '') + '</span>' +
                heads.map(function (h) { return '<span>' + esc(h) + '</span>'; }).join('') + '<span>Per unit</span></div>';
            var rows = p.variants.map(function (v, vi) {
                var who = vname(p, v, vi) || pname(p, pi);
                function cell(id, label, f, value, opts) {
                    opts = opts || {};
                    return '<div class="pc-ccell"><label class="pc-cl" for="' + id + '">' + esc(label) + '</label>' +
                        input({ id: id, f: f, p: p.id, v: v.id, value: value, pre: !opts.text, mode: opts.text ? '' : 'decimal', ph: opts.ph || '0', aria: who + ' ' + label.toLowerCase() + ' cost', required: opts.required }) + '</div>';
                }
                return '<div class="pc-crow" style="--cols:' + cols + '" role="group" aria-label="' + esc(who) + '">' +
                    '<div class="pc-cname">' + esc(hasVariants(p) ? who : 'Cost per piece') + '</div>' +
                    cell('mfg_' + v.id, 'Manufacturing', 'v.mfg', v.mfg, { required: true }) +
                    cell('pack_' + v.id, 'Packaging', 'v.pack', v.pack) +
                    cell('ship_' + v.id, 'Shipping', 'v.ship', v.ship) +
                    p.extras.map(function (e) { return cell('x_' + v.id + '_' + e.id, e.name, 'v.x.' + e.id, extraValue(v, e), { text: true, ph: '15 or 10%' }); }).join('') +
                    '<div class="pc-cbase"><span class="pc-cl">Cost per unit</span><span data-base="' + p.id + ':' + v.id + '" aria-live="off"></span></div>' +
                '</div>';
            }).join('');
            return '<div class="pc-cost" role="group" aria-labelledby="ch_' + p.id + '">' +
                '<h3 class="pc-h3" id="ch_' + p.id + '">' + esc(pname(p, pi)) + '</h3>' +
                headRow + rows + otherCosts(p, pi) +
            '</div>';
        }).join('');
        return '<h2 class="pc-h2" id="pcStepTitle" tabindex="-1">What does one unit cost you?</h2>' +
            '<p class="pc-help">Cost per piece, in ₹. Manufacturing is required.</p>' +
            '<div style="margin-top:1.1rem">' + sections + '</div>' +
            stepErrorBox();
    }

    // Thank-you page body: the price sheet.
    function thanksSheet() {
        return '<div class="pc-result-head">' +
                '<div><h2 class="pc-h2" id="pcStepTitle" tabindex="-1">Your price sheet</h2>' +
                '</div>' +
                '<div class="pc-actions">' +
                    '<button type="button" class="pc-btn pc-btn-primary" data-a="download">' + icon('download') + 'Download sheet</button>' +
                    '<button type="button" class="pc-btn pc-btn-ghost" data-a="edit-costs">' + icon('edit') + 'Edit costs</button>' +
                    '<button type="button" class="pc-btn pc-btn-ghost" data-a="restart">' + icon('restart_alt') + 'Price new products</button>' +
                '</div>' +
            '</div>' +
            renderSheet() +
            '<p class="pc-sheet-note">Scroll sideways to see every column.</p>';
    }

    var STEP_RENDER = [stepProducts, stepCosts];
    var NAV_HINT = ['Press Enter to continue', 'Press Enter to see your prices'];

    /* ---------- render ---------- */

    function currentStep() { return VIEW === 'thanks' ? RESULT_STEP : S.step; }

    function refreshBases() {
        Array.prototype.forEach.call(body.querySelectorAll('[data-base]'), function (el) {
            var ids = el.getAttribute('data-base').split(':');
            var p = findP(ids[0]), v = p && findV(p, ids[1]);
            if (v) el.textContent = inr(calc(p, v).base);
        });
    }

    function renderNav() {
        var n = STEPS.length, i = currentStep(), pct = Math.round(((i + 1) / n) * 100);
        document.getElementById('pcFill').style.width = pct + '%';
        document.getElementById('pcTrack').setAttribute('aria-valuenow', pct);
        document.getElementById('pcTrack').setAttribute('aria-valuetext', 'Step ' + (i + 1) + ' of ' + n);
        document.getElementById('pcStatus').textContent = 'Step ' + (i + 1) + ' of ' + n + ': ' + STEPS[i];
        if (VIEW === 'thanks') return; // no Back / Next on the thank-you page
        document.getElementById('pcBack').style.visibility = i === 0 ? 'hidden' : 'visible';
        document.getElementById('pcNextText').textContent = i === RESULT_STEP - 1 ? 'See my prices' : 'Next';
        document.getElementById('pcNavHint').textContent = NAV_HINT[i];
    }

    function render() {
        renderNav();
        body.innerHTML = VIEW === 'thanks' ? thanksSheet() : STEP_RENDER[S.step]();
        refreshBases();
    }

    function scrollToCard() {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var top = document.querySelector('.pc-main').getBoundingClientRect().top + window.pageYOffset - 110;
        if (window.pageYOffset > top) window.scrollTo({ top: top, behavior: reduce ? 'auto' : 'smooth' });
    }
    // After a step change, move focus to its heading so screen readers announce it.
    function focusStepTitle() {
        var h = document.getElementById('pcStepTitle');
        if (h) { try { h.focus({ preventScroll: true }); } catch (e) { h.focus(); } }
    }

    function showStepError(msg, inputId) {
        var err = document.getElementById('pcStepError');
        err.innerHTML = icon('error') + '<span>' + esc(msg) + '</span>';
        err.hidden = false;
        var inp = inputId && document.getElementById(inputId);
        if (inp) { inp.setAttribute('aria-invalid', 'true'); inp.setAttribute('aria-describedby', 'pcStepError'); inp.focus(); }
        return false;
    }

    // True when everything needed for a price sheet is filled in (no DOM needed).
    function dataComplete() {
        if (!S.industry) return false;
        return S.products.every(function (p) {
            return has(p.name) && p.variants.every(function (v) { return num(v.mfg) > 0; });
        });
    }

    // Each step must be complete before moving past it. Returns true when OK.
    function validateStep(i) {
        var k, p;
        if (i === 0) {
            if (!S.industry) {
                var box = document.getElementById('pcIndustryError');
                box.innerHTML = icon('error') + '<span>Please select what your brand sells.</span>';
                box.hidden = false;
                var sel = document.getElementById('pcIndustry');
                sel.setAttribute('aria-invalid', 'true');
                sel.focus();
                return false;
            }
            for (k = 0; k < S.products.length; k++) {
                if (!has(S.products[k].name)) return showStepError('Please enter a name for product ' + (k + 1) + '.', 'pname_' + S.products[k].id);
            }
        }
        if (i === 1) {
            for (k = 0; k < S.products.length; k++) {
                p = S.products[k];
                for (var j = 0; j < p.variants.length; j++) {
                    if (!(num(p.variants[j].mfg) > 0)) {
                        return showStepError('Please enter the manufacturing cost for ' + fullName(p, k, p.variants[j], j) + '.', 'mfg_' + p.variants[j].id);
                    }
                }
            }
        }
        return true;
    }

    function goTo(step) {
        step = Math.max(0, Math.min(RESULT_STEP, step));
        if (VIEW === 'thanks') {
            // Steps 1–2 live on the calculator page.
            if (step < RESULT_STEP) { S.step = step; save(); window.location.href = CALC_URL; }
            return;
        }
        // Moving forward checks every step in between.
        for (var k = S.step; k < step; k++) {
            if (k !== S.step) { S.step = k; save(); render(); }
            if (!validateStep(k)) return;
        }
        if (step === RESULT_STEP) {
            // The price sheet is on the thank-you page; contact details first (once).
            if (S.step !== RESULT_STEP - 1) { S.step = RESULT_STEP - 1; save(); render(); }
            if (S.unlocked) window.location.href = THANKS_URL;
            else openModal();
            return;
        }
        S.step = step;
        save();
        render();
        scrollToCard();
        focusStepTitle();
    }

    /* ---------- contact popup (calculator page only) ---------- */

    var lastFocus = null;
    function openModal() {
        lastFocus = document.activeElement;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.getElementById('pcFormError').hidden = true;
        setTimeout(function () { document.getElementById('pcName').focus(); }, 30);
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
    function modalOpen() { return !!modal && modal.classList.contains('is-open'); }

    function formError(msg, inputId) {
        var box = document.getElementById('pcFormError');
        box.innerHTML = icon('error') + '<span>' + esc(msg) + '</span>';
        box.hidden = false;
        if (inputId) {
            var el = document.getElementById(inputId);
            el.setAttribute('aria-invalid', 'true'); el.setAttribute('aria-describedby', 'pcFormError'); el.focus();
        }
    }

    if (modal) {
        // Keep Tab / Shift+Tab inside the dialog while it's open.
        modal.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var items = Array.prototype.filter.call(modal.querySelectorAll('button, input'), function (el) {
                return el.tabIndex !== -1 && !el.disabled && el.offsetParent !== null;
            });
            if (!items.length) return;
            var first = items[0], last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });

        form.addEventListener('input', function (e) { e.target.removeAttribute('aria-invalid'); e.target.removeAttribute('aria-describedby'); });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = form.full_name.value.trim();
            var phone = form.phone.value.trim();
            var email = form.email.value.trim();
            var digits = phone.replace(/\D/g, '');
            if (!name) return formError('Please enter your name.', 'pcName');
            if (digits.length < 10 || digits.length > 15) return formError('Please enter a valid phone number.', 'pcPhone');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return formError('Please enter a valid email address.', 'pcEmail');

            var btn = document.getElementById('pcSubmit');
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            document.getElementById('pcFormError').hidden = true;

            var variants = [];
            S.products.forEach(function (p, pi) {
                p.variants.forEach(function (v, vi) {
                    var c = calc(p, v);
                    variants.push({
                        name: fullName(p, pi, v, vi), mfg: num(v.mfg), pack: num(v.pack), ship: num(v.ship),
                        extras: p.extras.map(function (x) { return { name: x.name, value: extraValue(v, x) }; }),
                        base: Math.round(c.base), selling_price: c.sp, compare_at: c.comp,
                        gst: c.tax, breakeven_roas: isFinite(c.beRoas) ? c.beRoas.toFixed(2) + 'x' : '—'
                    });
                });
            });
            var payload = {
                full_name: name, phone: phone, email: email, website: form.website.value,
                product: S.products.map(pname).join(', '),
                industry: S.industry || '',
                budget: S.budget || '',
                variants: variants
            };

            function failed(msg) { btn.disabled = false; btn.removeAttribute('aria-busy'); formError(msg); }
            fetch(LEAD_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(function (res) {
                return res.json().catch(function () { return { success: false }; });
            }).then(function (data) {
                if (!data || !data.success) return failed((data && data.message) || 'Something went wrong. Please try again.');
                S.unlocked = true;
                S.step = RESULT_STEP - 1;
                save();
                try { sessionStorage.setItem(NAME_KEY, name.split(/\s+/)[0]); } catch (err) {}
                // Its own address, so ad platforms / analytics can count it as a conversion.
                window.location.href = THANKS_URL;
            }).catch(function () {
                failed('Could not connect. Please check your internet and try again.');
            });
        });
    }

    /* ---------- events ---------- */

    function onField(el) {
        var f = el.getAttribute('data-f'), val = el.value;
        var parts = f.split('.');
        var p = findP(el.getAttribute('data-p'));
        if (!p) return;
        el.removeAttribute('aria-invalid');
        el.removeAttribute('aria-describedby');
        if (f === 'p.name') p.name = val;
        else if (parts[0] === 'v') {
            var v = findV(p, el.getAttribute('data-v'));
            if (!v) return;
            if (parts[1] === 'x') v.x[parts[2]] = val;
            else v[parts[1]] = val;
        }
        save();
        refreshBases();
    }

    root.addEventListener('input', function (e) {
        if (e.target.hasAttribute('data-f')) onField(e.target);
    });
    root.addEventListener('change', function (e) {
        if (e.target.id === 'pcBudget') {
            S.budget = e.target.value;
            e.target.classList.toggle('is-empty', !S.budget);
            save();
            return;
        }
        if (e.target.id !== 'pcIndustry') return;
        S.industry = e.target.value;
        e.target.removeAttribute('aria-invalid');
        document.getElementById('pcIndustryError').hidden = true;
        save();
    });
    root.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOpen()) { closeModal(); return; }
        if (e.key !== 'Enter' || modalOpen()) return;
        var pid = e.target.getAttribute('data-extra-name') || e.target.getAttribute('data-extra-val');
        if (pid) { e.preventDefault(); addExtra(pid); return; }
        // Enter in any step field moves on, like submitting a form.
        if (e.target.hasAttribute('data-f')) { e.preventDefault(); goTo(S.step + 1); }
    });
    // Remember which "Add another cost" panels are open across re-renders.
    root.addEventListener('toggle', function (e) {
        var pid = e.target.getAttribute && e.target.getAttribute('data-more');
        if (pid) {
            moreOpen[pid] = e.target.open;
            if (e.target.open) { var n = document.getElementById('exName_' + pid); if (n) n.focus(); }
        }
    }, true);

    function addExtra(pid) {
        var p = findP(pid);
        var n = document.getElementById('exName_' + pid), d = document.getElementById('exVal_' + pid);
        var name = n.value.trim();
        if (!name) { n.setAttribute('aria-invalid', 'true'); n.focus(); return; }
        if (!has(d.value)) { d.setAttribute('aria-invalid', 'true'); d.focus(); return; }
        p.extras.push({ id: uid(), name: name, value: d.value.trim() });
        moreOpen[pid] = false;
        save(); render();
        toast(name + ' added to ' + pname(p, S.products.indexOf(p)));
    }

    var ACTIONS = {
        next: function () { goTo(S.step + 1); },
        back: function () { goTo(S.step - 1); },
        'edit-costs': function () { goTo(1); },
        'close-modal': closeModal,
        'add-product': function () {
            var p = newProduct();
            S.products.push(p); save(); render();
            var inp = document.getElementById('pname_' + p.id);
            if (inp) inp.focus();
        },
        'remove-product': function (el) {
            var id = el.getAttribute('data-p');
            var p = findP(id), name = pname(p, S.products.indexOf(p));
            S.products = S.products.filter(function (x) { return x.id !== id; });
            save(); render();
            toast(name + ' removed');
            focusStepTitle();
        },
        'add-variant': function (el) {
            var p = findP(el.getAttribute('data-p')), v;
            if (hasVariants(p)) { v = newVariant(''); p.variants.push(v); }
            else { v = p.variants[0]; delete v.implicit; } // keeps any costs already entered
            save(); render();
            var inp = document.getElementById('vname_' + v.id);
            if (inp) inp.focus();
        },
        'remove-variant': function (el) {
            var p = findP(el.getAttribute('data-p')), id = el.getAttribute('data-v');
            if (p.variants.length === 1) {
                // Removing the last variant goes back to pricing the product on its own.
                p.variants[0].implicit = true;
                p.variants[0].name = '';
            } else {
                p.variants = p.variants.filter(function (v) { return v.id !== id; });
            }
            save(); render();
            toast('Variant removed');
            var next = document.getElementById('pname_' + p.id);
            if (next) next.focus();
        },
        'add-extra': function (el) { addExtra(el.getAttribute('data-p')); },
        'remove-extra': function (el) {
            var p = findP(el.getAttribute('data-p')), id = el.getAttribute('data-e');
            p.extras = p.extras.filter(function (e) { return e.id !== id; });
            p.variants.forEach(function (v) { delete v.x[id]; });
            save(); render();
            toast('Cost removed');
            focusStepTitle();
        },
        download: downloadSheet,
        restart: function () {
            if (!confirm('Clear all products and start over?')) return;
            var unlocked = S.unlocked, industry = S.industry, budget = S.budget; // same visitor, same brand
            S = freshState();
            S.unlocked = unlocked;
            S.industry = industry;
            S.budget = budget;
            save();
            window.location.href = CALC_URL;
        }
    };

    root.addEventListener('click', function (e) {
        var el = e.target.closest('[data-a]');
        if (!el || !root.contains(el)) return;
        var fn = ACTIONS[el.getAttribute('data-a')];
        if (fn) fn(el);
    });

    // CSV with the same columns as the on-page table; opens in Excel / Google Sheets / Numbers.
    function downloadSheet() {
        var head = COLUMNS.map(function (c) { return c.h + (c.kind === 'inr' || c.kind === 'price' ? ' (₹)' : ''); });
        var rows = sheetRows().map(function (r) {
            return COLUMNS.map(function (c) { return rawCell(c.kind, c.get(r)); });
        });
        var csv = [head].concat(rows).map(function (row) {
            return row.map(function (cell) {
                cell = String(cell);
                if (/^[=+\-@]/.test(cell)) cell = "'" + cell; // don't let spreadsheet apps run it as a formula
                return /[",\n;]/.test(cell) ? '"' + cell.replace(/"/g, '""') + '"' : cell;
            }).join(',');
        }).join('\n');
        var blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = (S.products.length === 1 ? pname(S.products[0], 0).replace(/[^\w-]+/g, '-').toLowerCase() : 'products') + '-price-sheet.csv';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
        toast('Price sheet downloaded');
    }

    var toastTimer;
    function toast(msg) {
        var t = document.getElementById('pcToast');
        t.textContent = msg; t.classList.add('is-on');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { t.classList.remove('is-on'); }, 2200);
    }

    /* ---------- start ---------- */

    if (VIEW === 'thanks') {
        // Only reachable after the contact form; otherwise send them to fill it in.
        if (!S.unlocked || !dataComplete()) { window.location.replace(CALC_URL); return; }
        try {
            var first = sessionStorage.getItem(NAME_KEY);
            if (first) document.getElementById('pcThanksName').textContent = ', ' + first;
        } catch (e) {}
    }
    render();
})();
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
