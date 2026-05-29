<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'creative-dev');
$pageTitle = $_seo['meta_title'] ?: 'Creative Development Services in India - Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'Professional creative development services — graphic design, video editing, ad creatives, brand visuals and AI-powered creatives that drive engagement and conversions.';
$bodyClass = 'creative-dev';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$_pdo = Database::getInstance();
$pains = $_pdo->query("SELECT * FROM creative_pains WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$pillars = $_pdo->query("SELECT * FROM creative_pillars WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$svcs = $_pdo->query("SELECT * FROM creative_services WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps = $_pdo->query("SELECT * FROM creative_steps WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mets = $_pdo->query("SELECT * FROM creative_metrics WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$wds = $_pdo->query("SELECT * FROM creative_why_cards WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cd_hero = $_pdo->query("SELECT * FROM creative_hero WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cd_chips = $_pdo->query("SELECT * FROM creative_stat_chips WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cd_cta = $_pdo->query("SELECT * FROM creative_cta WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cd_sh = [];
foreach ($_pdo->query("SELECT * FROM cd_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $cd_sh[$row['slug']] = $row;
}
$cd_vcards_raw = $_pdo->query("SELECT * FROM cd_video_cards WHERE is_active=1 ORDER BY track, sort_order")->fetchAll(PDO::FETCH_ASSOC);
$cd_vcards = [1 => [], 2 => []];
foreach ($cd_vcards_raw as $vc) {
  $cd_vcards[(int) $vc['track']][] = $vc;
}
include __DIR__ . '/app/views/header.php';
?>
<style>
  /* ══════════════════════════════════════════
   ROOT & RESET
══════════════════════════════════════════ */
  *,
  *::before,
  *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  :root {
    --ink: #05070e;
    --ink2: #0b0e1a;
    --blue: #0066ff;
    --bluedim: rgba(0, 102, 255, .12);
    --blueglow: rgba(0, 102, 255, .4);
    --white: #ffffff;
    --off: #f4f5f7;
    --g100: #e8eaf0;
    --g300: #c0c4d0;
    --g500: #7a7f93;
    --g700: #2e3245;
    --g900: #0e1120;
    --rule: rgba(255, 255, 255, .06);
  }

  html {
    scroll-behavior: smooth;
  }

  body {
    background: var(--ink);
    color: var(--white);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  /* ── WRAPPER ─────────────────────────────── */
  .w {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 2.5rem;
    position: relative;
    z-index: 2;
  }

  @media(max-width:600px) {
    .w {
      padding: 0 1.25rem;
    }
  }

  /* ── UTILS ───────────────────────────────── */
  .label-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--blue);
  }

  .label-tag::before {
    content: '';
    width: 20px;
    height: 1px;
    background: var(--blue);
  }

  /* ── REVEAL ──────────────────────────────── */
  .rv {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity .8s cubic-bezier(.16, 1, .3, 1), transform .8s cubic-bezier(.16, 1, .3, 1);
  }

  .rv.show {
    opacity: 1;
    transform: none;
  }

  .rv-l {
    opacity: 0;
    transform: translateX(-30px);
    transition: opacity .8s cubic-bezier(.16, 1, .3, 1), transform .8s cubic-bezier(.16, 1, .3, 1);
  }

  .rv-l.show {
    opacity: 1;
    transform: none;
  }

  .rv-r {
    opacity: 0;
    transform: translateX(30px);
    transition: opacity .8s cubic-bezier(.16, 1, .3, 1), transform .8s cubic-bezier(.16, 1, .3, 1);
  }

  .rv-r.show {
    opacity: 1;
    transform: none;
  }

  .t1 {
    transition-delay: .1s
  }

  .t2 {
    transition-delay: .2s
  }

  .t3 {
    transition-delay: .3s
  }

  .t4 {
    transition-delay: .4s
  }

  .t5 {
    transition-delay: .5s
  }

  /* ══════════════════════════════════════════
   HERO
══════════════════════════════════════════ */
  #hero {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 0;
    position: relative;
    overflow: hidden;
    background: var(--ink);
    margin-top: 30px;
  }

  .hero-noise {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.03'/%3E%3C/svg%3E");
    opacity: .5;
  }

  .hero-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2.25rem 2.5rem;
    border-bottom: 1px solid var(--rule);
    position: relative;
    z-index: 2;
  }

  .hero-top-id {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .18em;
    color: var(--g500);
    text-transform: uppercase;
  }

  .live-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .14em;
    color: var(--blue);
    text-transform: uppercase;
  }

  .live-dot {
    width: 7px;
    height: 7px;
    background: var(--blue);
    border-radius: 50%;
  }

  .hero-main {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 0;
    min-height: 0;
    padding: 0 2.5rem;
    position: relative;
    z-index: 2;
  }

  @media(max-width:1000px) {
    .hero-main {
      grid-template-columns: 1fr;
    }
  }

  .hero-left {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 5rem 4rem 5rem 0;
    border-right: 1px solid var(--rule);
  }

  @media(max-width:1000px) {
    .hero-left {
      border-right: none;
      padding: 4rem 0 2rem;
    }
  }

  .hero-kicker {
    margin-bottom: 1.75rem;
  }

  .scramble-title {
    font-size: clamp(3rem, 6vw, 3.5rem);
    font-weight: 900;
    line-height: .95;
    letter-spacing: -.04em;
    overflow: hidden;
  }

  .scramble-title .line {
    display: block;
    overflow: hidden;
  }

  .scramble-title .line span {
    display: block;
    transform: translateY(110%);
    animation: lineUp .9s cubic-bezier(.16, 1, .3, 1) forwards;
  }

  .scramble-title .line:nth-child(1) span {
    animation-delay: .1s;
  }

  .scramble-title .line:nth-child(2) span {
    animation-delay: .25s;
  }

  .scramble-title .line:nth-child(3) span {
    animation-delay: .4s;
    color: var(--blue);
  }

  @keyframes lineUp {
    to {
      transform: translateY(0);
    }
  }

  .hero-sub {
    font-size: 1.05rem;
    line-height: 1.75;
    color: var(--g500);

    margin: 2rem 0 2.5rem;
    opacity: 0;
    animation: fadeIn .8s .7s forwards ease;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }

  .hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    opacity: 0;
    animation: fadeIn .8s .85s forwards ease;
  }

  .hero-right {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 3rem 0 3rem 3rem;
    gap: 1rem;
  }

  @media(max-width:1000px) {
    .hero-right {
      padding: 2rem 0 3rem;
    }
  }

  /* Animated canvas in hero right */
  .creative-canvas {
    width: 100%;
    aspect-ratio: 1;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--rule);
    border-radius: 12px;
    background: var(--g900);
  }

  .cc-svg {
    width: 100%;
    height: 100%;
  }

  .cc-grid line {
    stroke: rgba(0, 102, 255, .08);
    stroke-width: .5;
  }

  .cc-ring {
    fill: none;
    stroke: var(--blue);
    stroke-width: 1.5;
    stroke-dasharray: 8 4;
    transform-origin: center;
    animation: spinRing 12s linear infinite;
  }

  .cc-ring2 {
    animation-duration: 18s;
    animation-direction: reverse;
    stroke: rgba(0, 102, 255, .3);
  }

  @keyframes spinRing {
    to {
      transform: rotate(360deg);
    }
  }

  .cc-bar {
    animation: growBar 2s cubic-bezier(.16, 1, .3, 1) forwards;
  }

  @keyframes growBar {
    from {
      transform: scaleX(0);
      transform-origin: left;
    }

    to {
      transform: scaleX(1);
    }
  }

  .cc-fade {
    opacity: 0;
    animation: ccFade .6s ease forwards;
  }

  @keyframes ccFade {
    to {
      opacity: 1;
    }
  }

  /* Stat chips */
  .hero-chips {
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
  }

  .chip {
    flex: 1;
    min-width: 100px;
    padding: 1rem 1.25rem;
    border: 1px solid var(--rule);
    border-radius: 8px;
    background: var(--g900);
    opacity: 0;
    animation: fadeIn .7s ease forwards;
  }

  .chip:nth-child(1) {
    animation-delay: .95s
  }

  .chip:nth-child(2) {
    animation-delay: 1.05s
  }

  .chip:nth-child(3) {
    animation-delay: 1.15s
  }

  .chip-num {
    font-size: 1.8rem;
    font-weight: 900;
    letter-spacing: -.03em;
    color: var(--white);
  }

  .chip-lbl {
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--g500);
    margin-top: 3px;
  }

  /* ── BTNS ───────────────────────────────── */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 30px;
    border-radius: 6px;
    font-weight: 700;
    font-size: .88rem;
    letter-spacing: .02em;
    text-decoration: none;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
    border: none;
  }

  .btn-solid {
    background: var(--blue);
    color: var(--white);
  }

  .btn-solid:hover {
    background: #0052d4;
    transform: translateY(-2px);
    box-shadow: 0 10px 36px var(--blueglow);
  }

  .btn-ghost {
    border: 1px solid var(--rule);
    color: var(--white);
    background: transparent;
  }

  .btn-ghost:hover {
    border-color: rgba(0, 102, 255, .5);
    color: var(--blue);
  }

  @media (min-width: 1024px) {

    .btn svg,
    .btn .material-symbols-outlined {
      display: inline-block;
      opacity: 0;
      transform: translateY(20px);
      pointer-events: none;
    }

    .btn:hover svg,
    .btn:hover .material-symbols-outlined {
      opacity: 1;
      animation: iconJump 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes iconJump {
      0% {
        transform: translateY(100%);
        opacity: 0;
      }

      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }
  }

  /* Fallback/Mobile: Keep icons visible and use a simple shift */
  @media (max-width: 1023px) {

    .btn svg,
    .btn .material-symbols-outlined {
      opacity: 1;
      transform: none;
    }

    .btn:hover svg,
    .btn:hover .material-symbols-outlined {
      transform: translateX(4px);
    }
  }

  /* ══════════════════════════════════════════
   MARQUEE  (blue background)
══════════════════════════════════════════ */
  #mq {
    background: var(--blue);
    overflow: hidden;
    padding: 1rem 0;
    white-space: nowrap;
  }

  .mq-t {
    display: inline-block;
    animation: mq 22s linear infinite;
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .9);
  }

  @keyframes mq {
    to {
      transform: translateX(-50%);
    }
  }

  .mq-sep {
    color: rgba(255, 255, 255, .35);
    margin: 0 1.5rem;
  }

  /* ══════════════════════════════════════════
   WHY IT MATTERS  (dark section)
══════════════════════════════════════════ */
  #why {
    background: var(--ink2);
    padding: 5rem 0;
  }

  .why-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: start;
  }

  @media(max-width:900px) {
    .why-inner {
      grid-template-columns: 1fr;
      gap: 3rem;
    }
  }

  .why-statement {
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -.035em;
    color: var(--white);
  }

  .why-statement em {
    font-style: normal;
    color: var(--blue);
  }

  .why-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    margin-top: .5rem;
  }

  .why-item {
    display: flex;
    align-items: start;
    gap: 1.25rem;
    padding: 1.25rem 0;
    border-bottom: 1px solid var(--rule);
  }

  .why-item:first-child {
    border-top: 1px solid var(--rule);
  }

  .wi-num {
    font-size: .65rem;
    font-weight: 800;
    color: var(--blue);
    letter-spacing: .1em;
    flex-shrink: 0;
    margin-top: 3px;
  }

  .wi-text {
    font-size: .92rem;
    line-height: 1.6;
    color: var(--g300);
    font-weight: 500;
  }

  /* ══════════════════════════════════════════
   APPROACH  (3D tilt cards)
══════════════════════════════════════════ */
  #approach {
    padding: 5rem 0;
    background: var(--ink2);
  }

  .approach-header {
    display: flex;
    justify-content: space-between;
    align-items: end;
    margin-bottom: 4rem;
  }

  .approach-header h2 {
    font-size: clamp(2rem, 3.5vw, 2.8rem);
    font-weight: 900;
    letter-spacing: -.03em;
  }

  @media(max-width:700px) {
    .approach-header {
      flex-direction: column;
      align-items: start;
      gap: 1rem;
    }
  }

  .tilt-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    perspective: 1200px;
  }

  @media(max-width:900px) {
    .tilt-grid {
      grid-template-columns: none;
      grid-auto-flow: column;
      grid-auto-columns: minmax(325px, 82%);
      overflow-x: auto;
      gap: 1rem;
      margin: 0 -1.25rem;
      padding: 0 1.25rem 1rem;
      scroll-snap-type: x mandatory;
      scroll-padding: 0 1.25rem;
      -webkit-overflow-scrolling: touch;
    }

    .tilt-card {
      scroll-snap-align: start;
    }
  }

  .tilt-card {
    border: 1px solid var(--rule);
    border-radius: 12px;
    padding: 2.75rem 2.25rem;
    background: var(--g900);
    position: relative;
    overflow: hidden;
    transition: box-shadow .3s ease, border-color .3s ease;
    will-change: transform;
  }

  .tilt-card:hover {
    border-color: rgba(0, 102, 255, .35);
    box-shadow: 0 24px 60px rgba(0, 0, 0, .5), 0 0 0 1px rgba(0, 102, 255, .12);
  }

  .tilt-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 12px;
    background: radial-gradient(circle at var(--mx, 50%) var(--my, 50%), rgba(0, 102, 255, .1) 0%, transparent 65%);
    opacity: 0;
    transition: opacity .4s ease;
  }

  .tilt-card:hover::before {
    opacity: 1;
  }

  .tc-icon {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    background: var(--bluedim);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    border: 1px solid rgba(0, 102, 255, .2);
  }

  .tc-num {
    font-size: 5rem;
    font-weight: 900;
    letter-spacing: -.04em;
    line-height: 1;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, .06);
    margin-bottom: 1.5rem;
    user-select: none;
  }

  .tc-name {
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: .75rem;
  }

  .tc-desc {
    font-size: .9rem;
    line-height: 1.7;
    color: var(--g500);
    margin-bottom: 1.75rem;
  }

  .tc-tags {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
  }

  .tc-tag {
    font-size: .68rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 3px;
    background: rgba(255, 255, 255, .04);
    color: var(--g500);
    letter-spacing: .04em;
  }

  /* ══════════════════════════════════════════
   SERVICES DIRECTORY  (hover selector)
══════════════════════════════════════════ */
  #services {
    padding: 5rem 0;
    background: var(--ink);
  }

  .svc-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    margin-top: 4rem;
    border: 1px solid var(--rule);
    border-radius: 12px;
    overflow: hidden;
  }

  @media(max-width:900px) {
    .svc-layout {
      grid-template-columns: 1fr;
    }
  }

  .svc-list {
    border-right: 1px solid var(--rule);
  }

  @media(max-width:900px) {
    .svc-list {
      border-right: none;
      border-bottom: 1px solid var(--rule);
    }
  }

  .svc-item {
    padding: 2rem 2.5rem;
    border-bottom: 1px solid var(--rule);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    transition: background .25s ease;
    position: relative;
    overflow: hidden;
  }

  .svc-item:last-child {
    border-bottom: none;
  }

  .svc-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--blue);
    transform: scaleY(0);
    transition: transform .3s ease;
  }

  .svc-item.active,
  .svc-item:hover {
    background: rgba(0, 102, 255, .06);
  }

  .svc-item.active::before,
  .svc-item:hover::before {
    transform: scaleY(1);
  }

  .svc-item.active .si-name {
    color: var(--white);
  }

  .svc-item:hover .si-name {
    color: var(--white);
  }

  .si-left {
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }

  .si-num {
    font-size: .65rem;
    font-weight: 800;
    color: var(--g500);
    letter-spacing: .1em;
    flex-shrink: 0;
  }

  .svc-item.active .si-num {
    color: var(--blue);
  }

  .si-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--g500);
    transition: color .25s;
  }

  .si-arrow {
    opacity: 0;
    transition: opacity .25s, transform .25s;
    color: var(--blue);
    flex-shrink: 0;
  }

  .svc-item.active .si-arrow,
  .svc-item:hover .si-arrow {
    opacity: 1;
    transform: translateX(4px);
  }

  @media(max-width:768px) {
    .svc-list {
      display: flex !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory;
      -ms-overflow-style: none;
      scrollbar-width: none;
      border-bottom: 1px solid var(--rule);
      border-right: none !important;
    }

    .svc-list::-webkit-scrollbar {
      display: none;
    }

    .svc-item {
      flex: 0 0 auto !important;
      white-space: nowrap;
      padding: 1.25rem 1.5rem !important;
      border-bottom: none !important;
      scroll-snap-align: start;
    }

    .svc-item::before {
      top: auto !important;
      bottom: 0 !important;
      width: 100% !important;
      height: 2px !important;
      transform: scaleX(0) !important;
      transform-origin: left;
    }

    .svc-item.active::before {
      transform: scaleX(1) !important;
    }

    .si-arrow {
      display: none !important;
    }

    .si-left {
      gap: 0.75rem;
    }

    .si-name {
      font-size: 0.9rem;
    }

    .svc-panel {
      padding: 2.5rem 1.25rem !important;
      min-height: auto !important;
    }
  }

  .svc-panel {
    background: var(--g900);
    padding: 3.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 420px;
  }

  .sp-content {
    display: none;
  }

  .sp-content.active {
    display: flex;
    flex-direction: column;
    gap: 0;
    animation: spIn .4s cubic-bezier(.16, 1, .3, 1) forwards;
  }

  @keyframes spIn {
    from {
      opacity: 0;
      transform: translateX(16px);
    }

    to {
      opacity: 1;
      transform: none;
    }
  }

  .sp-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: var(--bluedim);
    border: 1px solid rgba(0, 102, 255, .25);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
  }

  .sp-title {
    font-size: 1.6rem;
    font-weight: 900;
    letter-spacing: -.02em;
    margin-bottom: 1rem;
  }

  .sp-desc {
    font-size: .9rem;
    line-height: 1.75;
    color: var(--g500);
    margin-bottom: 2rem;
  }

  .sp-bullets {
    display: flex;
    flex-direction: column;
    gap: .6rem;
  }

  .sp-bullet {
    display: flex;
    align-items: start;
    gap: .75rem;
    font-size: .85rem;
    color: var(--g300);
    line-height: 1.5;
  }

  .sp-bullet::before {
    content: '→';
    color: var(--blue);
    flex-shrink: 0;
    font-weight: 700;
    font-size: .8rem;
    margin-top: 1px;
  }

  /* ══════════════════════════════════════════
   VIDEO SPOTLIGHT  (dark, dramatique)
══════════════════════════════════════════ */
  #video {
    padding: 5rem 0;
    background: var(--g900);
    overflow: hidden;
    position: relative;
  }

  .video-bg-text {
    position: absolute;
    right: -2%;
    top: 50%;
    transform: translateY(-50%);
    font-size: clamp(6rem, 14vw, 16rem);
    font-weight: 900;
    letter-spacing: -.04em;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, .04);
    pointer-events: none;
    white-space: nowrap;
    user-select: none;
  }

  .video-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: center;
    position: relative;
    z-index: 2;
  }

  @media(max-width:900px) {
    .video-grid {
      grid-template-columns: 1fr;
      gap: 3rem;
    }
  }

  .video-left h2 {
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -.035em;
    margin: 1.5rem 0 1.25rem;
  }

  .video-left p {
    font-size: .95rem;
    line-height: 1.75;
    color: var(--g500);
    margin-bottom: 2.5rem;
  }

  .video-types {
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  .vt-row {
    padding: 1.1rem 0;
    border-bottom: 1px solid var(--rule);
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: .9rem;
    font-weight: 600;
    color: var(--g500);
    transition: color .25s, padding-left .25s;
  }

  .vt-row:hover {
    color: var(--white);
    padding-left: 8px;
  }

  .vt-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--blue);
    flex-shrink: 0;
  }

  .video-right {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  @media(max-width:900px) {
    .video-right {
      display: grid;
      grid-auto-flow: column;
      grid-auto-columns: minmax(260px, 82%);
      overflow-x: auto;
      gap: 1rem;
      margin: 0 -1.25rem;
      padding: 0 1.25rem 1rem;
      scroll-snap-type: x mandatory;
      scroll-padding: 0 1.25rem;
      -webkit-overflow-scrolling: touch;
    }

    .vr-card {
      scroll-snap-align: start;
    }
  }

  .vr-card {
    border: 1px solid var(--rule);
    border-radius: 10px;
    padding: 2rem;
    background: rgba(255, 255, 255, .02);
    transition: border-color .25s, background .25s;
    position: relative;
    overflow: hidden;
  }

  .vr-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0, 102, 255, .06), transparent 60%);
    opacity: 0;
    transition: opacity .3s;
  }

  .vr-card:hover {
    border-color: rgba(0, 102, 255, .35);
    background: rgba(0, 102, 255, .04);
  }

  .vr-card:hover::after {
    opacity: 1;
  }

  .vr-label {
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: var(--blue);
    margin-bottom: .6rem;
  }

  .vr-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin-bottom: .4rem;
  }

  .vr-desc {
    font-size: .83rem;
    line-height: 1.6;
    color: var(--g500);
  }

  /* ══════════════════════════════════════════
   PROCESS  (connected horizontal stepper)
══════════════════════════════════════════ */
  #process {
    padding: 5rem 0;
    background: var(--ink);
  }

  .process-track {
    position: relative;
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0;
    margin-top: 5rem;
  }

  @media(max-width:1000px) {
    .process-track {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media(max-width:600px) {
    .process-track {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .process-track::before {
    content: '';
    position: absolute;
    top: 22px;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, var(--blue), rgba(0, 102, 255, .1));
    z-index: 0;
  }

  @media(max-width:1000px) {
    .process-track::before {
      display: none;
    }
  }

  .ps {
    padding: 0 1.5rem 3rem 0;
    position: relative;
    z-index: 1;
  }

  .ps-node {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: var(--ink);
    border: 1px solid rgba(0, 102, 255, .4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .65rem;
    font-weight: 800;
    color: var(--blue);
    letter-spacing: .06em;
    margin-bottom: 2rem;
    transition: background .3s, border-color .3s, box-shadow .3s;
  }

  .ps:hover .ps-node {
    background: var(--blue);
    color: var(--white);
    border-color: var(--blue);
    box-shadow: 0 0 24px var(--blueglow);
  }

  .ps-title {
    font-size: .95rem;
    font-weight: 800;
    margin-bottom: .6rem;
    line-height: 1.2;
  }

  .ps-desc {
    font-size: .8rem;
    line-height: 1.6;
    color: var(--g500);
  }

  /* ══════════════════════════════════════════
   METRICS  (ring counters)
══════════════════════════════════════════ */
  #metrics {
    padding: 5rem 0;
    background: var(--ink2);
    border-top: 1px solid var(--rule);
  }

  .metrics-header {
    display: flex;
    justify-content: space-between;
    align-items: end;
    margin-bottom: 5rem;
  }

  @media(max-width:700px) {
    .metrics-header {
      flex-direction: column;
      align-items: start;
      gap: 1rem;
    }
  }

  .metrics-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2px;
    border: 1px solid var(--rule);
    border-radius: 12px;
    overflow: hidden;
  }

  @media(max-width:700px) {
    .metrics-row {
      grid-template-columns: 1fr 1fr;
    }
  }

  .met {
    padding: 3.5rem 2.5rem;
    background: var(--g900);
    position: relative;
    overflow: hidden;
    transition: background .3s;
  }

  .met:hover {
    background: rgba(0, 102, 255, .07);
  }

  .met::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--blue);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 1.2s cubic-bezier(.4, 0, .2, 1);
  }

  .met.fired::before {
    transform: scaleX(1);
  }

  .met-val {
    font-size: clamp(3rem, 5vw, 5rem);
    font-weight: 900;
    letter-spacing: -.04em;
    line-height: 1;
    margin-bottom: .6rem;
  }

  .met-val .cnt {
    color: var(--white);
  }

  .met-val .unit {
    color: var(--blue);
    font-size: .7em;
  }

  .met-lbl {
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--g500);
  }

  .met-bg {
    position: absolute;
    bottom: -1rem;
    right: -1rem;
    font-size: 7rem;
    font-weight: 900;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, .03);
    line-height: 1;
    user-select: none;
    pointer-events: none;
  }

  /* ══════════════════════════════════════════
   WHY DIGIFYCE  (diagonal split cards)
══════════════════════════════════════════ */
  #why2 {
    padding: 5rem 0;
    background: var(--ink);
  }

  .wd-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 4rem;
  }

  @media(max-width:900px) {
    .wd-grid {
      grid-template-columns: none;
      grid-auto-flow: column;
      grid-auto-columns: minmax(260px, 82%);
      overflow-x: auto;
      gap: 1rem;
      margin: 0 -1.25rem;
      padding: 0 1.25rem 1rem;
      scroll-snap-type: x mandatory;
      scroll-padding: 0 1.25rem;
      -webkit-overflow-scrolling: touch;
    }

    .wd-card {
      scroll-snap-align: start;
    }
  }

  @media(max-width:600px) {

    .tilt-card,
    .vr-card,
    .wd-card {
      padding: 10px 1.5rem;
    }

    .tc-num {
      font-size: 3.8rem;
    }

    .wd-title,
    .vr-title {
      font-size: 1rem;
    }
  }

  .wd-card {
    padding: 15px;
    border: 1px solid var(--rule);
    border-radius: 10px;
    background: var(--g900);
    position: relative;
    overflow: hidden;
    transition: border-color .3s, transform .3s, box-shadow .3s;
  }

  .wd-card:hover {
    border-color: rgba(0, 102, 255, .35);
    transform: translateY(-4px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, .4);
  }

  .wd-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--blue);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .5s ease;
  }

  .wd-card:hover::after {
    transform: scaleX(1);
  }

  .wd-idx {
    font-size: .65rem;
    font-weight: 800;
    color: var(--blue);
    letter-spacing: .14em;
    margin-bottom: 1.5rem;
  }

  .wd-title {
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: .75rem;
  }

  .wd-desc {
    font-size: .87rem;
    line-height: 1.7;
    color: var(--g500);
  }

  /* ══════════════════════════════════════════
   CTA  (d2c-branding style)
══════════════════════════════════════════ */
  #cta {
    background: var(--blue);
    padding: 8rem 0;
    position: relative;
    overflow: hidden;
  }

  @media(max-width:768px) {
    #cta {
      padding: 5rem 0;
    }
  }

  .cta-bg-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: clamp(6rem, 18vw, 18rem);
    font-weight: 900;
    letter-spacing: -.04em;
    color: rgba(0, 0, 0, .12);
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
  }

  .cta-inner {
    text-align: center;
    position: relative;
    z-index: 1;
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 2.5rem;
  }

  @media(max-width:600px) {
    .cta-inner {
      padding: 0 1.25rem;
    }
  }

  .cta-inner h2 {
    font-size: clamp(2.5rem, 5vw, 5rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -.04em;
    color: var(--white);
    margin-bottom: 1.5rem;
  }

  .cta-inner p {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, .75);
    max-width: 560px;
    margin: 0 auto 3rem;
    line-height: 1.7;
  }

  .btn-white {
    background: var(--white);
    color: var(--blue);
    font-weight: 800;
    box-shadow: 0 8px 40px rgba(0, 0, 0, .2);
  }

  .btn-white:hover {
    background: var(--off);
    transform: translateY(-3px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, .3);
  }

  .btn-white::after {
    background: rgba(0, 102, 255, .06);
  }
</style>

<!-- ══ HERO ════════════════════════════════════════ -->
<section id="hero">
  <div class="hero-noise"></div>

  <div class="hero-main">
    <div class="hero-left">
      <div class="hero-kicker"><span
          class="label-tag"><?= htmlspecialchars($cd_hero['kicker'] ?? 'Powerful Creative Solutions That Build Brands and Drive Engagement') ?></span>
      </div>
      <h1 class="scramble-title">
        <span class="line"><span><?= htmlspecialchars($cd_hero['h1_line1'] ?? 'Creative Development') ?></span></span>
        <span class="line"></span>
        <span
          class="line"><span><?= htmlspecialchars($cd_hero['h1_line2_accent'] ?? 'Services in India') ?></span></span>
      </h1>
      <p class="hero-sub"><?= htmlspecialchars($cd_hero['hero_sub'] ?? '') ?></p>
      <div class="hero-actions">
        <a href="<?= htmlspecialchars($cd_hero['btn1_url'] ?? 'leadform.php') ?>" class="btn btn-solid">
          <?= htmlspecialchars($cd_hero['btn1_label'] ?? 'Create Designs That Converts') ?>
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
        <a href="<?= htmlspecialchars($cd_hero['btn2_url'] ?? '#services') ?>"
          class="btn btn-ghost"><?= htmlspecialchars($cd_hero['btn2_label'] ?? 'View Services') ?></a>
      </div>
    </div>
    <div class="hero-right">
      <!-- Animated Creative Canvas -->
      <div class="creative-canvas">
        <svg class="cc-svg" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
          <!-- Grid -->
          <g class="cc-grid">
            <?php for ($i = 0; $i <= 400; $i += 40): ?>
              <line x1="<?= $i ?>" y1="0" x2="<?= $i ?>" y2="400" />
              <line x1="0" y1="<?= $i ?>" x2="400" y2="<?= $i ?>" />
            <?php endfor; ?>
          </g>
          <!-- Rings -->
          <circle cx="200" cy="200" r="120" class="cc-ring" stroke-dasharray="12 6" />
          <circle cx="200" cy="200" r="80" class="cc-ring cc-ring2" stroke-dasharray="6 10" />
          <!-- Center dot -->
          <circle cx="200" cy="200" r="6" fill="#0066ff" class="cc-fade" style="animation-delay:.5s" />
          <!-- Bars -->
          <rect x="60" y="280" width="80" height="8" rx="2" fill="rgba(0,102,255,.6)" class="cc-bar cc-fade"
            style="animation-delay:.2s" />
          <rect x="60" y="296" width="50" height="6" rx="2" fill="rgba(0,102,255,.3)" class="cc-bar cc-fade"
            style="animation-delay:.4s" />
          <rect x="60" y="310" width="65" height="6" rx="2" fill="rgba(0,102,255,.2)" class="cc-bar cc-fade"
            style="animation-delay:.6s" />
          <!-- Shape -->
          <rect x="260" y="100" width="80" height="80" rx="8" fill="none" stroke="rgba(0,102,255,.25)" stroke-width="1"
            class="cc-fade" style="animation-delay:.3s" />
          <rect x="270" y="110" width="60" height="60" rx="5" fill="rgba(0,102,255,.08)" class="cc-fade"
            style="animation-delay:.5s" />
          <!-- Triangle -->
          <polygon points="200,50 240,120 160,120" fill="none" stroke="rgba(0,102,255,.2)" stroke-width="1"
            class="cc-fade" style="animation-delay:.7s" />
          <!-- Corner marks -->
          <path d="M60 60 L60 80 M60 60 L80 60" stroke="rgba(0,102,255,.4)" stroke-width="1.5" fill="none" />
          <path d="M340 60 L340 80 M340 60 L320 60" stroke="rgba(0,102,255,.4)" stroke-width="1.5" fill="none" />
          <path d="M60 340 L60 320 M60 340 L80 340" stroke="rgba(0,102,255,.4)" stroke-width="1.5" fill="none" />
          <path d="M340 340 L340 320 M340 340 L320 340" stroke="rgba(0,102,255,.4)" stroke-width="1.5" fill="none" />
          <!-- Label -->
          <text x="200" y="210" text-anchor="middle" font-size="10" font-weight="700" fill="rgba(0,102,255,.5)"
            font-family="monospace" letter-spacing="4" class="cc-fade" style="animation-delay:.8s">CREATIVE</text>
        </svg>
      </div>
      <!-- Stat chips -->
      <div class="hero-chips">
        <?php foreach ($cd_chips as $chip): ?>
          <div class="chip">
            <div class="chip-num"><?= htmlspecialchars($chip['chip_num']) ?></div>
            <div class="chip-lbl"><?= htmlspecialchars($chip['chip_lbl']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══ MARQUEE ══════════════════════════════════════ -->
<div id="mq">
  <div class="mq-t">
    GRAPHIC DESIGN SERVICES <span class="mq-sep">◆</span> SOCIAL MEDIA CREATIVES <span class="mq-sep">◆</span> AD
    BANNERS AND PERFORMANCE CREATIVES <span class="mq-sep">◆</span> MARKETING MATERIALS <span class="mq-sep">◆</span>
    WEBSITE CREATIVES <span class="mq-sep">◆</span> BRAND VISUAL DEVELOPMENT <span class="mq-sep">◆</span> AI-POWERED
    CREATIVE OPTIMIZATION <span class="mq-sep">◆</span> GRAPHIC DESIGN SERVICES <span class="mq-sep">◆</span> SOCIAL
    MEDIA CREATIVES <span class="mq-sep">◆</span> AD BANNERS AND PERFORMANCE CREATIVES <span class="mq-sep">◆</span>
    MARKETING MATERIALS <span class="mq-sep">◆</span> WEBSITE CREATIVES <span class="mq-sep">◆</span> BRAND VISUAL
    DEVELOPMENT <span class="mq-sep">◆</span> AI-POWERED CREATIVE OPTIMIZATION <span class="mq-sep">◆</span>&nbsp;
  </div>
</div>

<!-- ══ WHY IT MATTERS (white) ═══════════════════════ -->
<section id="why">
  <div class="w">
    <div class="why-inner">
      <div class="rv-l">
        <div class="label-tag" style="color:#0066ff;margin-bottom:1.5rem;">
          <?= htmlspecialchars($cd_sh['pains']['eyebrow'] ?? 'Why Creative Development Matters') ?>
        </div>
        <p class="why-statement">
          <?= htmlspecialchars($cd_sh['pains']['heading'] ?? 'Customers interact with visuals before they interact with your product.') ?>
        </p>
        <p style="font-size:1rem;line-height:1.75;color:var(--g500);margin-top:1.5rem;max-width:420px;">
          <?= htmlspecialchars($cd_sh['pains']['sub_text'] ?? 'A weak creative presence can reduce trust, lower engagement, and negatively affect conversions even when your product or service is excellent. Many businesses struggle because their creatives are inconsistent, outdated, or disconnected from their marketing strategy.') ?>
        </p>
      </div>
      <div class="rv">
        <div class="label-tag" style="color:#0066ff;margin-bottom:1.5rem;">
          <?= htmlspecialchars($cd_sh['pains_right']['eyebrow'] ?? 'Common Challenges') ?>
        </div>
        <div class="why-list">
          <?php foreach ($pains as $i => $p): ?>
            <div class="why-item">
              <span class="wi-num">0<?= $i + 1 ?></span>
              <span class="wi-text"><?= htmlspecialchars($p['text']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ APPROACH (tilt cards) ════════════════════════ -->
<section id="approach">
  <div class="w">
    <div class="approach-header rv">
      <div>
        <div class="label-tag" style="margin-bottom:.75rem;">
          <?= htmlspecialchars($cd_sh['pillars']['eyebrow'] ?? 'Our Approach to Creative Development') ?>
        </div>
        <h2><?= htmlspecialchars($cd_sh['pillars']['heading'] ?? 'Strategic Creative Systems Built for Performance') ?>
        </h2>
      </div>
      <p style="max-width:340px;font-size:.9rem;line-height:1.7;color:var(--g500);">
        <?= htmlspecialchars($cd_sh['pillars']['sub_text'] ?? 'We do not create random designs, we create strategic visual systems that align with your brand identity, marketing goals, and customer psychology. We combine human creativity with smart AI-powered tools to improve speed, efficiency, and creative performance.') ?>
      </p>
    </div>
    <div class="tilt-grid">
      <?php foreach ($pillars as $i => $p):
        $tags = json_decode($p['tags_json'] ?? '[]', true) ?: []; ?>
        <div class="tilt-card rv t<?= $i + 1 ?>" data-tilt>
          <div class="tc-num"><?= htmlspecialchars($p['number']) ?></div>
          <div class="tc-icon">
            <svg width="24" height="24" fill="none" stroke="#0066ff" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                d="<?= htmlspecialchars($p['svg_path']) ?>" />
            </svg>
          </div>
          <div class="tc-name"><?= htmlspecialchars($p['name']) ?></div>
          <p class="tc-desc"><?= htmlspecialchars($p['description']) ?></p>
          <div class="tc-tags"><?php foreach ($tags as $t): ?><span
                class="tc-tag"><?= htmlspecialchars($t) ?></span><?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ SERVICES DIRECTORY ════════════════════════════ -->
<section id="services">
  <div class="w">
    <div class="rv" style="margin-bottom:1rem;">
      <div class="label-tag" style="margin-bottom:.75rem;">
        <?= htmlspecialchars($cd_sh['services']['eyebrow'] ?? 'Complete Creative Development Solutions') ?>
      </div>
      <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:900;letter-spacing:-.03em;">
        <?= htmlspecialchars($cd_sh['services']['heading'] ?? 'End-to-End Creative Services') ?>
      </h2>
      <?php if (!empty($cd_sh['services']['sub_text'])): ?>
        <p style="font-size:1rem;color:var(--g500);margin-top:1rem;max-width:800px;">
          <?= htmlspecialchars($cd_sh['services']['sub_text']) ?>
        </p>
      <?php endif; ?>
    </div>
    <div class="svc-layout rv t1">
      <!-- Left list -->
      <div class="svc-list" id="svcList">
        <?php foreach ($svcs as $i => $s): ?>
          <div class="svc-item <?= $i === 0 ? 'active' : '' ?>" data-svc="<?= $i ?>">
            <div class="si-left">
              <span class="si-num">0<?= $i + 1 ?></span>
              <span class="si-name"><?= htmlspecialchars($s['name']) ?></span>
            </div>
            <svg class="si-arrow" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6" />
            </svg>
          </div>
        <?php endforeach; ?>
      </div>
      <!-- Right panel -->
      <div class="svc-panel" id="svcPanel">
        <?php foreach ($svcs as $i => $sd):
          $bullets = json_decode($sd['bullets_json'] ?? '[]', true) ?: []; ?>
          <div class="sp-content <?= $i === 0 ? 'active' : '' ?>" data-panel="<?= $i ?>">
            <div class="sp-icon">
              <svg width="26" height="26" fill="none" stroke="#0066ff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                  d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
              </svg>
            </div>
            <div class="sp-title"><?= htmlspecialchars($sd['name']) ?></div>
            <?php if (!empty($sd['subtitle'])): ?>
              <div class="sp-sub"
                style="font-size:.75rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:#0066ff;margin-bottom:.75rem;">
                <?= htmlspecialchars($sd['subtitle']) ?>
              </div>
            <?php endif; ?>
            <div class="sp-desc"><?= htmlspecialchars($sd['description']) ?></div>
            <div class="sp-bullets">
              <?php foreach ($bullets as $b): ?>
                <div class="sp-bullet"><?= htmlspecialchars($b) ?></div><?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══ VIDEO EDITING: CINEMATIC TICKER ══════════════════════════════ -->
<style>
  .video-cinematic {
    padding: 8rem 0;
    background: #020617;
    /* Very dark slate */
    position: relative;
    overflow: hidden;
  }

  /* Background glow */
  .vc-glow {
    position: absolute;
    top: 50%;
    left: 30%;
    width: 800px;
    height: 800px;
    background: radial-gradient(circle, rgba(0, 102, 255, 0.08) 0%, rgba(0, 0, 0, 0) 70%);
    transform: translate(-50%, -50%);
    pointer-events: none;
    z-index: 0;
  }

  .vc-container {
    max-width: 90rem;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 10;
  }

  .vc-left {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .vc-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.2rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 99px;
    color: #0066ff;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    width: max-content;
    backdrop-filter: blur(10px);
  }

  .vc-title {
    font-size: clamp(3rem, 5vw, 4.5rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -0.03em;
    color: #fff;
    margin-bottom: 1rem;
  }

  .vc-desc {
    font-size: 1.15rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.6);
    max-width: 90%;
  }

  .vc-highlight {
    margin-top: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 102, 255, 0.1), rgba(0, 0, 0, 0));
    border-left: 3px solid #0066ff;
    border-radius: 0 1rem 1rem 0;
    color: #fff;
    font-weight: 500;
    font-size: 1.1rem;
    line-height: 1.6;
  }

  /* Right Side: Tickers */
  .vc-right {
    height: 700px;
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    overflow: hidden;
    /* Gradient mask to fade out top and bottom */
    -webkit-mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
    mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
  }

  .vc-ticker-track {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    width: 100%;
    will-change: transform;
  }

  .vc-ticker-track.up {
    animation: scrollUp 25s linear infinite;
  }

  .vc-ticker-track.down {
    animation: scrollDown 25s linear infinite;
  }

  .vc-right:hover .vc-ticker-track {
    animation-play-state: paused;
  }

  @keyframes scrollUp {
    0% {
      transform: translateY(0);
    }

    100% {
      transform: translateY(-50%);
    }
  }

  @keyframes scrollDown {
    0% {
      transform: translateY(-50%);
    }

    100% {
      transform: translateY(0);
    }
  }

  .vc-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.5rem;
    padding: 2rem;
    backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    transition: all 0.4s ease;
    cursor: default;
    position: relative;
    overflow: hidden;
  }

  .vc-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at top right, rgba(0, 102, 255, 0.2) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.4s ease;
  }

  .vc-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(0, 102, 255, 0.4);
    transform: scale(1.02);
  }

  .vc-card:hover::before {
    opacity: 1;
  }

  .vc-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(0, 102, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0066ff;
    border: 1px solid rgba(0, 102, 255, 0.2);
    position: relative;
    z-index: 2;
  }

  .vc-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.3;
    position: relative;
    z-index: 2;
  }

  @media (max-width: 1024px) {
    .vc-container {
      grid-template-columns: 1fr;
      gap: 3rem;
    }

    .vc-right {
      height: 500px;
    }

    .vc-desc {
      max-width: 100%;
    }
  }

  @media (max-width: 640px) {
    .vc-right {
      display: flex;
      flex-direction: row;
      gap: 1rem;
      height: auto;
      overflow-x: auto;
      overflow-y: hidden;
      scroll-snap-type: x mandatory;
      padding-bottom: 1.5rem;
      mask-image: none;
      -webkit-mask-image: none;
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .vc-right::-webkit-scrollbar {
      display: none;
    }

    .vc-ticker-track {
      display: contents;
      animation: none !important;
    }

    .vc-card {
      flex: 0 0 85%;
      scroll-snap-align: center;
      max-width: 320px;
    }

    .vc-card.dup {
      display: none !important;
    }
  }
</style>

<section class="video-cinematic" id="video">
  <div class="vc-glow"></div>

  <div class="vc-container">
    <!-- Left side info -->
    <div class="vc-left rv-l">
      <div class="vc-tag ">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
          <path d="M8 5v14l11-7z" />
        </svg>
        <?= htmlspecialchars($cd_sh['video']['eyebrow'] ?? 'Visual Assets That Convert') ?>
      </div>
      <h2 class="vc-title"><?= htmlspecialchars($cd_sh['video']['heading'] ?? 'Video Editing Services') ?></h2>
      <p class="vc-desc">
        <?= htmlspecialchars($cd_sh['video']['sub_text'] ?? 'Video content is one of the most powerful tools for marketing, storytelling, and customer engagement. Strong video editing improves clarity, professionalism, and conversion performance.') ?>
      </p>
      <?php if (!empty($cd_sh['video']['extra_text'])): ?>
        <p class="vc-desc"><?= htmlspecialchars($cd_sh['video']['extra_text']) ?></p>
      <?php else: ?>
        <p class="vc-desc">Short-form video content drives visibility and engagement across platforms like Instagram,
          Facebook, and YouTube.</p>
      <?php endif; ?>
      <div class="vc-highlight">
        <?= htmlspecialchars($cd_sh['video']['btn_label'] ?? 'At Digifyce, we provide professional video editing services designed for ads, branding, social media, and business communication. Our goal is to transform raw content into high-performing visual assets that connect with your audience.') ?>
      </div>
    </div>

    <!-- Right side Tickers -->
    <?php
    $_vc_svgs = [
      1 => [
        1 => '<path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
        2 => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        3 => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
      ],
      2 => [
        1 => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>',
        2 => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>',
        3 => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
      ],
    ];
    $_vc_defaults = [1 => ['Instagram Reels', 'Product showcase videos', 'Viral-format content edits'], 2 => ['Short-form promotional videos', 'Launch announcement videos', 'Social media story videos']];
    ?>
    <div class="vc-right rv-r">
      <?php foreach ([1 => 'up', 2 => 'down'] as $_t => $_dir):
        $_cards = !empty($cd_vcards[$_t]) ? $cd_vcards[$_t] : array_map(fn($t) => ['title' => $t], $_vc_defaults[$_t]); ?>
        <div class="vc-ticker-track <?= $_dir ?>">
          <?php foreach ($_cards as $_ci => $_vc):
            $_svg = $_vc_svgs[$_t][($_ci % 3) + 1] ?? $_vc_svgs[$_t][1]; ?>
            <div class="vc-card">
              <div class="vc-card-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24"><?= $_svg ?></svg></div>
              <div class="vc-card-title"><?= htmlspecialchars($_vc['title']) ?></div>
            </div>
          <?php endforeach; ?>
          <?php foreach ($_cards as $_ci => $_vc):
            $_svg = $_vc_svgs[$_t][($_ci % 3) + 1] ?? $_vc_svgs[$_t][1]; ?>
            <div class="vc-card dup">
              <div class="vc-card-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24"><?= $_svg ?></svg></div>
              <div class="vc-card-title"><?= htmlspecialchars($_vc['title']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ PROCESS ══════════════════════════════════════ -->
<section class="py-20 md:py-32 bg-[#020617] relative z-50" id="process">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16 md:mb-24">
      <h2 class="text-xs uppercase tracking-[0.4em] text-[var(--blue)] mb-6">
        <?= htmlspecialchars($cd_sh['process']['eyebrow'] ?? 'Execution') ?>
      </h2>
      <h3 class="text-4xl md:text-6xl font-black tracking-tighter text-white">
        <?= htmlspecialchars($cd_sh['process']['heading'] ?? 'Our Creative Development Process') ?>
      </h3>
    </div>

    <div class="grid lg:grid-cols-[1fr_1.5fr] gap-12 lg:gap-24 relative items-start">

      <!-- Sticky Left Indicator -->
      <div class="lg:sticky lg:top-40 hidden lg:flex flex-col items-center justify-center">
        <div class="relative w-64 h-64 flex items-center justify-center">
          <!-- Rotating dash ring -->
          <svg class="absolute inset-0 w-full h-full animate-[spin_20s_linear_infinite] opacity-20"
            viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="48" fill="none" stroke="var(--blue)" stroke-width="1" stroke-dasharray="4 4" />
          </svg>

          <!-- Glowing Background -->
          <div class="absolute inset-0 bg-[var(--blue)]/10 rounded-full blur-2xl"></div>

          <div class="relative z-10 text-center">
            <span class="text-sm font-bold tracking-[0.3em] uppercase text-slate-400 block mb-2">Step</span>
            <div class="text-7xl font-black text-white flex items-baseline justify-center gap-2">
              <span id="active-step-number">01</span>
              <span class="text-2xl text-slate-500">/06</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Scrolling Right Content -->
      <div class="space-y-12 md:space-y-32 py-10" id="process-steps-container">
        <?php foreach ($steps as $idx => $step) {
          $num = str_pad($step['step_number'], 2, '0', STR_PAD_LEFT);
          echo '<div class="process-card flex flex-col md:flex-row gap-6 md:gap-10 items-start opacity-30 transition-all duration-700" data-step="' . $num . '">';
          echo '<div class="lg:hidden text-5xl font-black text-[var(--blue)] opacity-50">' . $num . '</div>';
          echo '<div class="flex-1 bg-white/5 border border-white/10 p-8 md:p-12 rounded-3xl hover:border-[var(--blue)]/50 transition-colors group">';
          echo '<h4 class="text-2xl md:text-4xl font-bold text-white mb-4 tracking-tight group-hover:text-[var(--blue)] transition-colors">' . htmlspecialchars($step['title']) . '</h4>';
          echo '<p class="text-lg text-slate-400 leading-relaxed">' . htmlspecialchars($step['description']) . '</p>';
          echo '</div>';
          echo '</div>';
        }
        ?>
      </div>

    </div>
  </div>
</section>

<!-- ══ METRICS ══════════════════════════════════════ -->
<section id="metrics">
  <div class="w">
    <div class="metrics-header rv">
      <div>
        <div class="label-tag" style="margin-bottom:.75rem;">
          <?= htmlspecialchars($cd_sh['metrics']['eyebrow'] ?? 'Business Impact of Strong Creative Development') ?>
        </div>
        <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:900;letter-spacing:-.03em;">
          <?= htmlspecialchars($cd_sh['metrics']['heading'] ?? 'Strategic Creative Builds Business Value') ?>
        </h2>
      </div>
      <p style="max-width:360px;font-size:.9rem;line-height:1.7;color:var(--g500);">
        <?= htmlspecialchars($cd_sh['metrics']['sub_text'] ?? 'Strategic creative development creates measurable business value and makes brand growth easier to sustain.') ?>
      </p>
    </div>
    <div class="metrics-row">
      <?php foreach ($mets as $m): ?>
        <div class="met" data-met="<?= htmlspecialchars($m['value']) ?>">
          <div class="met-val"><span class="cnt" data-target="<?= htmlspecialchars($m['value']) ?>">0</span><span
              class="unit"><?= htmlspecialchars($m['unit']) ?></span>
          </div>
          <div class="met-lbl"><?= htmlspecialchars($m['label']) ?></div>
          <div class="met-bg"><?= htmlspecialchars($m['value']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ WHY DIGIFYCE ══════════════════════════════════ -->
<section id="why2">
  <div class="w">
    <div class="rv" style="margin-bottom:2rem;">
      <div class="label-tag" style="margin-bottom:.75rem;">
        <?= htmlspecialchars($cd_sh['why']['eyebrow'] ?? 'Business Impact') ?>
      </div>
      <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:900;letter-spacing:-.03em;">
        <?= htmlspecialchars($cd_sh['why']['heading'] ?? 'Why Choose Digifyce') ?>
      </h2>
      <p
        style="margin-top: 1rem; color: #94a3b8; font-size: 1.1rem; max-width: 800px; line-height: 1.7; margin-bottom: 2rem;">
        <?= htmlspecialchars($cd_sh['why']['sub_text'] ?? 'At Digifyce, we value our clients by creating visuals that drive real business impact. By combining graphic design and video editing under one system, we ensure your creative assets are built for performance and scale.') ?>
      </p>
      <div>
        <a href="<?= htmlspecialchars($cd_sh['why']['btn_url'] ?? 'leadform.php') ?>" class="btn btn-solid">
          <?= htmlspecialchars($cd_sh['why']['btn_label'] ?? 'Scale Your Creatives Today') ?>
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>
    </div>
    <div class="wd-grid">
      <?php foreach ($wds as $i => $wd): ?>
        <div class="wd-card rv t<?= ($i % 3) + 1 ?>">
          <div class="wd-idx"><?= htmlspecialchars($wd['number']) ?></div>
          <div class="wd-title"><?= htmlspecialchars($wd['title']) ?></div>
          <div class="wd-desc"><?= htmlspecialchars($wd['description']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ CTA (d2c-branding style) ═════════════════════════ -->
<section id="cta">
  <div class="cta-bg-text"><?= htmlspecialchars($cd_cta['bg_text'] ?? 'GROW') ?></div>
  <div class="cta-inner rv">
    <h2><?= htmlspecialchars($cd_cta['heading'] ?? 'Lets Build Creative Assets That Perform.') ?></h2>
    <p><?= htmlspecialchars($cd_cta['description'] ?? '') ?></p>
    <a href="<?= htmlspecialchars($cd_cta['btn_url'] ?? 'leadform.php') ?>" class="btn btn-white"
      style="font-size:1rem;padding:18px 44px;">
      <?= htmlspecialchars($cd_cta['btn_label'] ?? 'Start Your Creative Journey with Digifyce') ?>
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
    </a>
  </div>
</section>

<script>
  (function () {
    /* ── Scroll Reveals ─────────────────── */
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('show');
      });
    }, { threshold: .1 });
    document.querySelectorAll('.rv,.rv-l,.rv-r').forEach(el => io.observe(el));

    /* ── Metric Counters ─────────────────── */
    const mio = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('fired');
        const cnt = e.target.querySelector('.cnt');
        if (!cnt || cnt.dataset.done) return;
        cnt.dataset.done = '1';
        const target = +cnt.dataset.target;
        let start = null, dur = 1500;
        const ease = t => 1 - Math.pow(1 - t, 3);
        const step = ts => {
          if (!start) start = ts;
          const p = Math.min((ts - start) / dur, 1);
          cnt.textContent = Math.round(ease(p) * target);
          if (p < 1) requestAnimationFrame(step); else cnt.textContent = target;
        };
        requestAnimationFrame(step);
        mio.unobserve(e.target);
      });
    }, { threshold: .3 });
    document.querySelectorAll('.met').forEach(el => mio.observe(el));

    /* ── Tilt Cards ─────────────────────── */
    document.querySelectorAll('[data-tilt]').forEach(card => {
      card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - .5;
        const y = (e.clientY - r.top) / r.height - .5;
        card.style.transform = `perspective(900px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) scale(1.02)`;
        card.style.setProperty('--mx', ((x + .5) * 100) + '%');
        card.style.setProperty('--my', ((y + .5) * 100) + '%');
      });
      card.addEventListener('mouseleave', () => { card.style.transform = ''; });
    });

    /* ── Service Selector ───────────────── */
    const items = document.querySelectorAll('.svc-item');
    const panels = document.querySelectorAll('.sp-content');
    const svcList = document.getElementById('svcList');

    function activateSvc(item) {
      const idx = item.dataset.svc;
      items.forEach(i => i.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      item.classList.add('active');
      const panel = document.querySelector(`.sp-content[data-panel="${idx}"]`);
      if (panel) panel.classList.add('active');

      if (window.innerWidth <= 768 && svcList) {
        item.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest',
          inline: 'center'
        });
      }
    }

    items.forEach(item => {
      item.addEventListener('mouseenter', () => activateSvc(item));
      item.addEventListener('click', () => activateSvc(item));
    });
    /* ── Sticky Process Step Tracker ───────────────── */
    const processCards = gsap.utils.toArray('.process-card');
    const activeStepEl = document.getElementById('active-step-number');

    if (processCards.length > 0) {
      function activateStep(card) {
        processCards.forEach(c => {
          c.style.opacity = '0.3';
          c.style.transform = 'scale(0.95)';
        });
        card.style.opacity = '1';
        card.style.transform = 'scale(1)';

        if (activeStepEl) {
          activeStepEl.innerText = card.getAttribute('data-step');
          gsap.fromTo(activeStepEl, { scale: 1.5, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.5, ease: "back.out(1.7)" });
        }
      }

      processCards.forEach((card, i) => {
        ScrollTrigger.create({
          trigger: card,
          start: "top 60%",
          end: "bottom 40%",
          onEnter: () => activateStep(card),
          onEnterBack: () => activateStep(card),
        });
      });
    }

  })();
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>