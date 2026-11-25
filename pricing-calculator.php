<?php
$pageTitle = 'D2C Pricing Calculator – Digifyce';
$pageDescription = 'Find the selling price that breaks even on ads. Enter your manufacturing, packaging and shipping costs and get a price and breakeven ROAS for every product and variant.';
include __DIR__ . '/app/views/header.php';
?>
<style>
    /* Page-scoped styles. Kept as plain CSS (not Tailwind utilities) because
       public/assets/css/tailwind.min.css is a pre-built JIT bundle and only
       contains classes that were present when it was last compiled.
       Text colours are chosen to stay at or above WCAG AA contrast on the dark background. */
    /* Top padding clears the site's fixed nav (h-20 = 80px, sm:h-24 = 96px). */
    .pc-section { background: #030508; padding: calc(80px + 1.5rem) 0 4rem; }
    @media (min-width: 640px) { .pc-section { padding-top: calc(96px + 2rem); } }
    .pc-wrap { width: 92%; max-width: 940px; margin: 0 auto; transition: max-width .3s ease; }
    .pc-wrap.is-wide { max-width: 1240px; }
    .pc-eyebrow { font-size: 11px; text-transform: uppercase; letter-spacing: .3em; color: #60a5fa; }
    .pc-title { margin-top: .5rem; font-size: clamp(1.6rem, 3.2vw, 2.4rem); font-weight: 700; letter-spacing: -0.03em; line-height: 1.15; }
    .pc-lede { margin-top: .5rem; color: #a3b1c6; max-width: 640px; font-size: 15px; line-height: 1.55; }
    .pc-sr { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    .pc-section :focus-visible { outline: 2px solid #60a5fa; outline-offset: 2px; }
    .pc-section .pc-input:focus-visible { outline: none; }

    .pc-card { border: 1px solid rgba(255,255,255,.1); border-radius: 14px; background: rgba(255,255,255,.015); }
    .pc-main { margin-top: 1.5rem; padding: 1.25rem; }
    @media (min-width: 640px) { .pc-main { padding: 1.75rem; } }

    /* Progress: step buttons with a thin bar under them */
    .pc-steps { display: flex; gap: .5rem; flex-wrap: wrap; }
    .pc-step { display: inline-flex; align-items: center; gap: .5rem; min-height: 40px; padding: .35rem .9rem .35rem .45rem; border-radius: 999px; border: 1px solid rgba(255,255,255,.12); background: transparent; color: #a3b1c6; font: inherit; font-size: 13px; cursor: pointer; transition: border-color .2s, color .2s; }
    .pc-step:hover { color: #fff; border-color: rgba(255,255,255,.3); }
    .pc-step-n { width: 24px; height: 24px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; background: rgba(255,255,255,.08); }
    .pc-step.is-done { color: #e2e8f0; }
    .pc-step.is-done .pc-step-n { background: rgba(0,102,255,.35); color: #fff; }
    .pc-step.is-current { color: #fff; border-color: var(--electric-blue); }
    .pc-step.is-current .pc-step-n { background: var(--electric-blue); color: #fff; }
    .pc-track { height: 4px; margin-top: .9rem; border-radius: 999px; background: rgba(255,255,255,.1); overflow: hidden; }
    .pc-fill { height: 100%; border-radius: 999px; background: var(--electric-blue); transition: width .5s ease-out; }

    .pc-body { margin-top: 1.5rem; }
    .pc-h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.02em; }
    .pc-h2:focus { outline: none; }
    .pc-h3 { font-size: 1rem; font-weight: 700; }
    .pc-help { margin-top: .3rem; color: #a3b1c6; font-size: 14px; line-height: 1.5; }
    .pc-label { font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: #a3b1c6; font-weight: 600; }

    /* Fields */
    .pc-field label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: #a3b1c6; font-weight: 600; }
    .pc-input-wrap { position: relative; margin-top: .4rem; }
    .pc-input { width: 100%; min-height: 44px; border-radius: 10px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.05); padding: .65rem .85rem; color: #fff; font: inherit; font-size: 15px; outline: none; transition: border-color .2s, background .2s, box-shadow .2s; }
    .pc-input::placeholder { color: #7c8aa0; }
    .pc-input:focus { border-color: #60a5fa; background: rgba(0,102,255,.08); box-shadow: 0 0 0 3px rgba(96,165,250,.25); }
    .pc-input[aria-invalid="true"] { border-color: #f87171; box-shadow: 0 0 0 3px rgba(248,113,113,.2); }
    .pc-input.has-pre { padding-left: 1.75rem; }
    .pc-pre { position: absolute; top: 50%; left: .8rem; transform: translateY(-50%); color: #a3b1c6; font-size: 14px; pointer-events: none; }
    .pc-error { display: flex; gap: .4rem; align-items: center; margin-top: .9rem; padding: .6rem .8rem; border-radius: 10px; background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.35); font-size: 14px; color: #fecaca; }
    .pc-error[hidden] { display: none; }
    .pc-error .material-symbols-outlined { font-size: 18px; }

    /* Buttons */
    .pc-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; min-height: 44px; border-radius: 999px; padding: .65rem 1.3rem; font: inherit; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; cursor: pointer; transition: background .2s, color .2s, border-color .2s, opacity .2s; border: 1px solid transparent; }
    .pc-btn .material-symbols-outlined { font-size: 18px; }
    .pc-btn-primary { background: var(--electric-blue); color: #fff; }
    .pc-btn-primary:hover { background: #fff; color: #05070a; }
    .pc-btn-primary[disabled] { opacity: .6; cursor: wait; }
    .pc-btn-ghost { background: transparent; color: #e2e8f0; border-color: rgba(255,255,255,.25); }
    .pc-btn-ghost:hover { border-color: #60a5fa; color: #93c5fd; }
    .pc-link { display: inline-flex; align-items: center; gap: .3rem; min-height: 36px; padding: .25rem .1rem; background: none; border: 0; color: #93c5fd; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; }
    .pc-link:hover { color: #fff; text-decoration: underline; }
    .pc-link .material-symbols-outlined { font-size: 18px; }
    .pc-link small { color: #a3b1c6; font-weight: 400; font-size: 13px; }
    .pc-icon-btn { width: 40px; height: 40px; flex: none; border-radius: 999px; border: 1px solid rgba(255,255,255,.16); background: transparent; color: #cbd5e1; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: color .2s, border-color .2s; }
    .pc-icon-btn .material-symbols-outlined { font-size: 18px; }
    .pc-icon-btn:hover { color: #fff; border-color: rgba(255,255,255,.4); }
    .pc-icon-btn.is-danger:hover { color: #fca5a5; border-color: rgba(248,113,113,.6); }

    /* Back / Next stay pinned to the bottom of the screen while filling in */
    .pc-nav { position: sticky; bottom: 0; z-index: 5; display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem -1.25rem -1.25rem; padding: .85rem 1.25rem; background: rgba(8,11,18,.96); backdrop-filter: blur(8px); border-top: 1px solid rgba(255,255,255,.1); border-radius: 0 0 14px 14px; }
    @media (min-width: 640px) { .pc-nav { margin: 1.75rem -1.75rem -1.75rem; padding: .9rem 1.75rem; } }
    .pc-nav-hint { font-size: 13px; color: #a3b1c6; display: none; }
    @media (min-width: 720px) { .pc-nav-hint { display: block; } }

    /* Step 1: "What does your brand sell?" dropdown */
    .pc-req { color: #60a5fa; }
    .pc-brand { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 1rem; margin-top: 1rem; }
    @media (max-width: 640px) { .pc-brand { grid-template-columns: minmax(0,1fr); } }
    .pc-select-wrap { position: relative; margin-top: .4rem; }
    .pc-select { text-overflow: ellipsis; }
    .pc-select.is-optional.is-empty { color: #a3b1c6; } /* optional: no :invalid, so grey via class */
    .pc-select { appearance: none; -webkit-appearance: none; padding-right: 2.75rem; cursor: pointer; color-scheme: dark; }
    .pc-select:invalid { color: #a3b1c6; } /* "Select a category" shown in grey until chosen */
    .pc-select option { background: #0a0e15; color: #f1f5f9; }
    .pc-select-wrap::after { content: ''; position: absolute; right: 1rem; top: 50%; width: 8px; height: 8px; margin-top: -6px; border-right: 2px solid #a3b1c6; border-bottom: 2px solid #a3b1c6; transform: rotate(45deg); pointer-events: none; }

    /* Step 1: products */
    .pc-products { display: grid; gap: .75rem; margin-top: 1.1rem; }
    .pc-product { border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 1rem 1.1rem; background: rgba(255,255,255,.02); }
    .pc-prow { display: flex; gap: .75rem; align-items: flex-end; }
    .pc-prow .pc-field { flex: 1; }
    .pc-vgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: .6rem; margin-top: .75rem; }
    .pc-vitem { display: flex; gap: .5rem; align-items: center; }
    .pc-vitem .pc-input-wrap { margin-top: 0; flex: 1; }
    .pc-pfoot { margin-top: .5rem; }

    /* Step 2: compact cost grid per product (stacks on phones) */
    .pc-cost { border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 1rem 1.1rem; background: rgba(255,255,255,.02); }
    .pc-cost + .pc-cost { margin-top: .75rem; }
    .pc-chead, .pc-crow { display: grid; grid-template-columns: var(--cols); gap: .6rem; align-items: center; }
    .pc-chead { margin-top: .8rem; padding-bottom: .35rem; font-size: 11px; text-transform: uppercase; letter-spacing: .1em; font-weight: 600; color: #a3b1c6; }
    .pc-chead span:last-child, .pc-cbase { text-align: right; }
    .pc-crow { padding: .45rem 0; border-top: 1px solid rgba(255,255,255,.06); }
    .pc-cname { font-size: 14px; font-weight: 600; color: #e2e8f0; overflow-wrap: anywhere; }
    .pc-ccell .pc-input-wrap { margin-top: 0; }
    .pc-cbase { font-size: 15px; font-weight: 700; color: #fff; font-variant-numeric: tabular-nums; }
    .pc-cl { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
    @media (max-width: 640px) {
        .pc-chead { display: none; }
        .pc-crow { grid-template-columns: 1fr 1fr; padding: .75rem 0; }
        .pc-cname, .pc-cbase { grid-column: 1 / -1; }
        .pc-cbase { text-align: left; font-size: 14px; }
        .pc-cl { position: static; width: auto; height: auto; overflow: visible; clip: auto; white-space: normal; display: block; margin-bottom: .3rem; font-size: 11px; text-transform: uppercase; letter-spacing: .1em; font-weight: 600; color: #a3b1c6; }
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
    .pc-chip { display: inline-flex; align-items: center; gap: .35rem; font-size: 13px; color: #e2e8f0; border: 1px solid rgba(255,255,255,.16); border-radius: 999px; padding: .2rem .25rem .2rem .75rem; }
    .pc-chip button { width: 28px; height: 28px; border-radius: 999px; background: none; border: 0; color: #cbd5e1; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .pc-chip button:hover { color: #fca5a5; background: rgba(248,113,113,.12); }
    .pc-chip .material-symbols-outlined { font-size: 16px; }

    /* Step 3: results table */
    .pc-result-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 1rem; }
    .pc-actions { display: flex; flex-wrap: wrap; gap: .6rem; }
    .pc-sheet { margin-top: 1rem; border: 1px solid rgba(255,255,255,.12); border-radius: 12px; overflow: auto; max-height: 62vh; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.22) transparent; }
    .pc-sheet::-webkit-scrollbar { height: 8px; width: 8px; }
    .pc-sheet::-webkit-scrollbar-track, .pc-sheet::-webkit-scrollbar-corner { background: transparent; }
    .pc-sheet::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); border-radius: 999px; }
    .pc-sheet::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.32); }
    .pc-sheet::-webkit-scrollbar-button { display: none; }
    .pc-sheet table { border-collapse: separate; border-spacing: 0; font-size: 14px; min-width: 100%; }
    .pc-sheet th, .pc-sheet td { padding: .65rem .9rem; white-space: nowrap; border-bottom: 1px solid rgba(255,255,255,.07); }
    .pc-sheet thead th { position: sticky; top: 0; z-index: 2; background: #0d121b; color: #a3b1c6; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; text-align: right; }
    .pc-sheet td, .pc-sheet tbody th { background: #070a10; color: #dbe3ee; text-align: right; font-weight: 400; font-variant-numeric: tabular-nums; }
    .pc-sheet .is-text { text-align: left; }
    .pc-sheet .pc-stick { position: sticky; left: 0; z-index: 1; box-shadow: 1px 0 0 rgba(255,255,255,.1); }
    .pc-sheet tbody th.pc-stick { color: #fff; font-weight: 600; }
    .pc-sheet thead th.pc-stick { z-index: 3; }
    .pc-sheet td.is-key { color: #fff; font-weight: 700; background: #0b1426; }
    .pc-sheet thead th.is-key { color: #93c5fd; background: #0f1a2e; }
    .pc-sheet tbody tr:last-child > * { border-bottom: 0; }
    .pc-sheet tr.is-group > * { border-top: 1px solid rgba(255,255,255,.16); }
    .pc-sheet-note { margin-top: .5rem; font-size: 13px; color: #a3b1c6; }

    /* Lead modal */
    .pc-modal { position: fixed; inset: 0; z-index: 70; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(2,4,8,.8); backdrop-filter: blur(4px); }
    .pc-modal.is-open { display: flex; }
    .pc-dialog { position: relative; width: 100%; max-width: 440px; max-height: calc(100vh - 2rem); overflow-y: auto; border-radius: 16px; border: 1px solid rgba(255,255,255,.14); background: #0a0e15; padding: 1.75rem 1.5rem 1.5rem; box-shadow: 0 30px 80px rgba(0,0,0,.6); }
    .pc-dialog .pc-icon-btn { position: absolute; top: .9rem; right: .9rem; }
    .pc-dialog .pc-field + .pc-field { margin-top: .9rem; }
    .pc-dialog form { margin-top: 1.1rem; }
    .pc-dialog .pc-btn-primary { width: 100%; margin-top: 1.25rem; }
    .pc-privacy { margin-top: .8rem; font-size: 13px; color: #a3b1c6; text-align: center; }
    .pc-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

    .pc-toast { position: fixed; left: 50%; bottom: 90px; transform: translateX(-50%) translateY(20px); opacity: 0; background: #fff; color: #05070a; padding: .65rem 1.1rem; border-radius: 999px; font-size: 14px; font-weight: 600; transition: opacity .25s, transform .25s; pointer-events: none; z-index: 80; }
    .pc-toast.is-on { opacity: 1; transform: translateX(-50%) translateY(0); }

    @media (prefers-reduced-motion: reduce) {
        .pc-section *, .pc-section *::before, .pc-section *::after { transition: none !important; animation: none !important; }
    }
</style>

<section class="pc-section">
    <div class="pc-wrap" id="pcWrap">
        <div class="pc-eyebrow">Pricing Calculator</div>
        <h1 class="pc-title">Price your products to break even on ads.</h1>
        <p class="pc-lede">Enter what one unit costs you. We'll give you the selling price and the ROAS your ads must hit, for every product and variant.</p>

        <div class="pc-card pc-main">
            <nav class="pc-steps" id="pcSteps" aria-label="Calculator steps"></nav>
            <div class="pc-track" id="pcTrack" role="progressbar" aria-label="Progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="33">
                <div class="pc-fill" id="pcFill" style="width:33%"></div>
            </div>
            <p class="pc-sr" id="pcStatus" aria-live="polite"></p>

            <div class="pc-body" id="pcBody"></div>

            <div class="pc-nav" id="pcNav">
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

    <!-- Contact details, asked once before the prices are shown -->
    <div class="pc-modal" id="pcModal" aria-hidden="true">
        <div class="pc-dialog" role="dialog" aria-modal="true" aria-labelledby="pcModalTitle" aria-describedby="pcModalDesc">
            <button type="button" class="pc-icon-btn" data-a="close-modal" aria-label="Close"><span class="material-symbols-outlined" aria-hidden="true">close</span></button>
            <div class="pc-eyebrow">Almost there</div>
            <h2 class="pc-h2" id="pcModalTitle" style="margin-top:.5rem">Where should we send your prices?</h2>
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
                <p class="pc-privacy">We'll only use these details to get in touch about your pricing.</p>
            </form>
        </div>
    </div>

    <div class="pc-toast" id="pcToast" role="status" aria-live="polite"></div>
</section>

<script>
(function () {
    'use strict';

    var STORE_KEY = 'digifyce_pricing_calc_v1';
    var LEAD_URL = <?= json_encode($appUrl . '/app/api/pricing_calculator_lead.php') ?>;
    var STEPS = ['Products', 'Unit Costs', 'Price Sheet'];
    var RESULT_STEP = STEPS.length - 1;

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

    var root = document.querySelector('.pc-section');
    var body = document.getElementById('pcBody');
    var modal = document.getElementById('pcModal');
    var form = document.getElementById('pcLeadForm');
    var moreOpen = {}; // product id → "Other costs" panel open (kept across re-renders)

    /* ---------- state ----------
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
            var out = { step: Math.min(s.step || 0, RESULT_STEP), unlocked: !!s.unlocked, industry: s.industry || '', budget: s.budget || '', products: s.products };
            if (out.step === RESULT_STEP && !out.unlocked) out.step = RESULT_STEP - 1;
            return out;
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

    /* ---------- results table: one column list drives the page view and the download ---------- */

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

    /* ---------- steps ---------- */

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

    function stepResult() {
        return '<div class="pc-result-head">' +
                '<div><h2 class="pc-h2" id="pcStepTitle" tabindex="-1">Your price sheet</h2>' +
                '<p class="pc-help">Selling price and breakeven ROAS are highlighted.</p></div>' +
                '<div class="pc-actions">' +
                    '<button type="button" class="pc-btn pc-btn-primary" data-a="download">' + icon('download') + 'Download sheet</button>' +
                    '<button type="button" class="pc-btn pc-btn-ghost" data-a="edit-costs">' + icon('edit') + 'Edit costs</button>' +
                    '<button type="button" class="pc-btn pc-btn-ghost" data-a="restart">' + icon('restart_alt') + 'Start over</button>' +
                '</div>' +
            '</div>' +
            renderSheet() +
            '<p class="pc-sheet-note">Scroll sideways to see every column.</p>';
    }

    var STEP_RENDER = [stepProducts, stepCosts, stepResult];
    var NAV_HINT = ['Press Enter to continue', 'Press Enter to see your prices', ''];

    /* ---------- render ---------- */

    function refreshBases() {
        Array.prototype.forEach.call(body.querySelectorAll('[data-base]'), function (el) {
            var ids = el.getAttribute('data-base').split(':');
            var p = findP(ids[0]), v = p && findV(p, ids[1]);
            if (v) el.textContent = inr(calc(p, v).base);
        });
    }

    function renderNav() {
        var n = STEPS.length, i = S.step, pct = Math.round(((i + 1) / n) * 100);
        document.getElementById('pcFill').style.width = pct + '%';
        document.getElementById('pcTrack').setAttribute('aria-valuenow', pct);
        document.getElementById('pcTrack').setAttribute('aria-valuetext', 'Step ' + (i + 1) + ' of ' + n);
        document.getElementById('pcStatus').textContent = 'Step ' + (i + 1) + ' of ' + n + ': ' + STEPS[i];
        document.getElementById('pcSteps').innerHTML = STEPS.map(function (s, k) {
            var cls = k === i ? 'is-current' : (k < i ? 'is-done' : '');
            var state = k < i ? ', completed' : (k === i ? ', current step' : '');
            return '<button type="button" class="pc-step ' + cls + '" data-a="goto" data-s="' + k + '"' + (k === i ? ' aria-current="step"' : '') + ' aria-label="Step ' + (k + 1) + ': ' + s + state + '">' +
                '<span class="pc-step-n" aria-hidden="true">' + (k < i ? '✓' : k + 1) + '</span>' + s + '</button>';
        }).join('');
        document.getElementById('pcBack').style.visibility = i === 0 ? 'hidden' : 'visible';
        // The results step has its own buttons; on the costs step "Next" becomes "See my prices".
        document.getElementById('pcNav').style.display = i === RESULT_STEP ? 'none' : '';
        document.getElementById('pcNextText').textContent = i === RESULT_STEP - 1 ? 'See my prices' : 'Next';
        document.getElementById('pcNavHint').textContent = NAV_HINT[i];
        document.getElementById('pcWrap').classList.toggle('is-wide', i === RESULT_STEP);
    }

    function render() {
        renderNav();
        body.innerHTML = STEP_RENDER[S.step]();
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
        // Moving forward checks every step in between.
        for (var k = S.step; k < step; k++) {
            if (k !== S.step) { S.step = k; save(); render(); }
            if (!validateStep(k)) return;
        }
        if (step === RESULT_STEP && !S.unlocked) {
            if (S.step !== RESULT_STEP - 1) { S.step = RESULT_STEP - 1; save(); render(); }
            openModal();
            return;
        }
        S.step = step;
        save();
        render();
        scrollToCard();
        focusStepTitle();
    }

    /* ---------- lead modal ---------- */

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
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
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

    function formError(msg, inputId) {
        var box = document.getElementById('pcFormError');
        box.innerHTML = icon('error') + '<span>' + esc(msg) + '</span>';
        box.hidden = false;
        if (inputId) {
            var el = document.getElementById(inputId);
            el.setAttribute('aria-invalid', 'true'); el.setAttribute('aria-describedby', 'pcFormError'); el.focus();
        }
    }

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

        function done() { btn.disabled = false; btn.removeAttribute('aria-busy'); }
        fetch(LEAD_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function (res) {
            return res.json().catch(function () { return { success: false }; });
        }).then(function (data) {
            done();
            if (!data || !data.success) return formError((data && data.message) || 'Something went wrong. Please try again.');
            S.unlocked = true;
            form.reset();
            closeModal();
            goTo(RESULT_STEP);
        }).catch(function () {
            done();
            formError('Could not connect. Please check your internet and try again.');
        });
    });

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
        if (e.key === 'Escape' && modal.classList.contains('is-open')) { closeModal(); return; }
        if (e.key !== 'Enter' || modal.classList.contains('is-open')) return;
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
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });

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
        goto: function (el) { goTo(parseInt(el.getAttribute('data-s'), 10)); },
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
            moreOpen = {};
            save(); render(); scrollToCard(); focusStepTitle();
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

    render();
})();
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
