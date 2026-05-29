<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/seo.php';
$_seoPdo = Database::getInstance();
$_seo = load_page_seo($_seoPdo, 'd2c-branding');
$pageTitle = $_seo['meta_title'] ?: 'D2C Branding Services in India - Digifyce';
$pageDescription = $_seo['meta_description'] ?: 'Build a Scalable Brand That Drives Growth and Customer Loyalty with Digifyce\'s strategic D2C branding services.';
$bodyClass = 'd2c-branding';
$appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/digifyce2', '/');
require_once __DIR__ . '/config/database.php';
$pdo        = Database::getInstance();
$hero       = $pdo->query("SELECT * FROM d2c_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$cta        = $pdo->query("SELECT * FROM d2c_cta WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$introTags  = $pdo->query("SELECT tag_name FROM d2c_intro_tags WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);
$challenges = $pdo->query("SELECT * FROM d2c_challenges WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$pillars    = $pdo->query("SELECT * FROM d2c_pillars WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$sols       = $pdo->query("SELECT * FROM d2c_solutions WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$steps      = $pdo->query("SELECT * FROM d2c_steps WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$mets       = $pdo->query("SELECT * FROM d2c_metrics WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$feats      = $pdo->query("SELECT * FROM d2c_why_features WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$d2c_sh     = [];
foreach ($pdo->query("SELECT * FROM d2c_section_headers")->fetchAll(PDO::FETCH_ASSOC) as $row) { $d2c_sh[$row['slug']] = $row; }
include __DIR__ . '/app/views/header.php';
?>
<style>
  /* ─── RESET & ROOT ──────────────────────────────────── */
  *,
  *::before,
  *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  :root {
    --ink: #06080f;
    --blue: #0066ff;
    --blue-d: #0050cc;
    --white: #ffffff;
    --g100: #f0f2f5;
    --g200: #d1d5db;
    --g400: #9ca3af;
    --g600: #4b5563;
    --g800: #1a1d27;
    --g900: #0d0f18;
    --line: rgba(255, 255, 255, 0.07);
    --blue-glow: rgba(0, 102, 255, 0.35);
  }

  body {
    background: var(--ink);
    color: var(--white);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  /* ─── GRID OVERLAY ───────────────────────────────────── */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background-image:
      linear-gradient(rgba(0, 102, 255, .04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0, 102, 255, .04) 1px, transparent 1px);
    background-size: 72px 72px;
  }

  /* ─── TYPOGRAPHY SCALE ───────────────────────────────── */
  .label {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--g400);
  }

  .label-blue {
    color: var(--blue);
  }

  .split-header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: end;
    margin-bottom: 4rem;
  }

  @media (max-width: 768px) {
    .split-header {
      grid-template-columns: 1fr;
      gap: 1.5rem;
      align-items: start;
    }
  }

  /* ─── SECTION CONTAINER ──────────────────────────────── */
  .wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 4rem;
    position: relative;
    z-index: 1;
  }

  @media(max-width:1024px) {
    .wrap {
      padding: 0 2rem;
    }
  }

  @media(max-width:640px) {
    .wrap {
      padding: 0 1rem;
    }
  }

  /* ─── DIVIDER LINE ───────────────────────────────────── */
  .hr {
    width: 100%;
    height: 1px;
    background: var(--line);
  }

  /* ─── BUTTONS ────────────────────────────────────────── */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    border-radius: 4px;
    font-weight: 700;
    font-size: .9rem;
    letter-spacing: .03em;
    text-decoration: none;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
  }

  .btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, .1);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .3s ease;
  }

  .btn:hover::after {
    transform: scaleX(1);
  }

  .btn-solid {
    background: var(--blue);
    color: var(--white);
    box-shadow: 0 0 0 0 var(--blue-glow);
  }

  .btn-solid:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px var(--blue-glow);
  }

  .btn-outline {
    border: 1px solid rgba(255, 255, 255, .2);
    color: var(--white);
  }

  .btn-outline:hover {
    border-color: var(--blue);
    color: var(--blue);
  }

  .btn svg {
    transition: transform .25s ease;
  }

  .btn:hover svg {
    transform: translateX(4px);
  }

  @media (min-width: 1024px) {

    .btn span.material-symbols-outlined,
    .btn svg {
      display: inline-block;
      opacity: 0;
      transform: translateY(20px);
      pointer-events: none;
    }

    .btn:hover span.material-symbols-outlined,
    .btn:hover svg {
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

  @media (max-width: 1023px) {

    .btn span.material-symbols-outlined,
    .btn svg {
      opacity: 1;
      transform: none;
    }
  }

  /* ─── SCROLL REVEALS ──────────────────────────────────── */
  [data-reveal] {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity .75s cubic-bezier(.16, 1, .3, 1), transform .75s cubic-bezier(.16, 1, .3, 1);
  }

  [data-reveal="left"] {
    transform: translateX(-28px);
  }

  [data-reveal="right"] {
    transform: translateX(28px);
  }

  [data-reveal="scale"] {
    transform: scale(.95);
  }

  [data-reveal].in {
    opacity: 1;
    transform: none;
  }

  [data-delay="1"] {
    transition-delay: .1s;
  }

  [data-delay="2"] {
    transition-delay: .2s;
  }

  [data-delay="3"] {
    transition-delay: .3s;
  }

  [data-delay="4"] {
    transition-delay: .4s;
  }

  [data-delay="5"] {
    transition-delay: .5s;
  }

  /* ════════════════════════════════════════════════════════
   HERO
════════════════════════════════════════════════════════ */
  #hero {
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 0 0 4rem;
    position: relative;
    overflow: hidden;
  }

  @media (max-width: 768px) {
    #hero {
      padding: 8rem 0 3rem;
      min-height: auto;
    }
  }

  .hero-bg-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: clamp(9rem, 22vw, 22rem);
    font-weight: 900;
    letter-spacing: -.04em;
    color: rgba(255, 255, 255, 0.05);
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0);
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
    z-index: 0;
    animation: driftBg 18s ease-in-out infinite alternate;
  }

  @keyframes driftBg {
    to {
      transform: translate(-50%, -50%) scale(1.04) translateX(20px);
    }
  }

  .hero-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2.5rem 0;
    border-bottom: 1px solid var(--line);
    margin-bottom: 6rem;
  }

  .hero-index {
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .15em;
    color: var(--g600);
  }

  .hero-ping {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .12em;
    color: var(--blue);
    text-transform: uppercase;
  }

  .ping-dot {
    width: 8px;
    height: 8px;
    background: var(--blue);
    border-radius: 50%;
    position: relative;
  }

  .ping-dot::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: var(--blue);
    animation: pingAnim 1.6s ease-out infinite;
  }

  @keyframes pingAnim {
    0% {
      transform: scale(1);
      opacity: 1
    }

    100% {
      transform: scale(2.8);
      opacity: 0
    }
  }

  .hero-headline {
    font-size: clamp(3rem, 7.5vw, 3.5rem);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -.035em;
    position: relative;
    z-index: 1;
  }

  .hero-headline .line-accent {
    color: var(--blue);
    display: block;
  }

  .hero-headline .line-dim {
    color: var(--g400);
    display: block;
  }

  .hero-bottom {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: end;
    gap: 3rem;
    margin-top: 0;
    padding-top: 0;
  }

  .hero-sub {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--g400);

  }

  .hero-stats {
    display: flex;
    gap: 3rem;
  }

  .hero-stat {
    text-align: right;
  }

  .hero-stat .num {
    font-size: 2.4rem;
    font-weight: 900;
    color: var(--white);
    letter-spacing: -.02em;
    line-height: 1;
  }

  .hero-stat .lbl {
    font-size: .65rem;
    font-weight: 600;
    color: var(--g600);
    letter-spacing: .12em;
    text-transform: uppercase;
    margin-top: 4px;
  }

  @media(max-width:768px) {
    .hero-bottom {
      grid-template-columns: 1fr;
    }

    .hero-stats {
      justify-content: flex-start;
    }

    .hero-stat {
      text-align: left;
    }

    .hero-bg-text {
      font-size: 28vw;
    }

    /* Remove oversized decorative background word on mobile */
    .hero-bg-text {
      display: none;
    }
  }

  /* ════════════════════════════════════════════════════════
   MARQUEE STRIP
════════════════════════════════════════════════════════ */
  #marquee-strip {
    background: var(--blue);
    padding: 1rem 0;
    overflow: hidden;
    white-space: nowrap;
  }

  .mq-track {
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

  /* ════════════════════════════════════════════════════════
   INTRO  (full-width statement)
════════════════════════════════════════════════════════ */
  #intro {
    padding: 7rem 0;
  }

  @media (max-width: 768px) {
    #intro {
      padding: 4rem 0;
    }
  }

  .intro-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 6rem;
    align-items: start;
  }

  .intro-number {
    font-size: 10rem;
    font-weight: 900;
    line-height: 1;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, 1);
    user-select: none;
  }

  .intro-body h2 {
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -.03em;
    margin-bottom: 2rem;
  }

  .intro-body p {
    font-size: 1.05rem;
    line-height: 1.8;
    color: var(--g400);
    margin-bottom: 2rem;
  }

  .intro-tags {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: 2.5rem;
  }

  .tag {
    padding: 6px 16px;
    border-radius: 100px;
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .06em;
    border: 1px solid rgba(255, 255, 255, .1);
    color: var(--g400);
    transition: border-color .25s, color .25s;
  }

  .tag:hover {
    border-color: var(--blue);
    color: var(--blue);
  }

  @media(max-width:900px) {
    .intro-grid {
      grid-template-columns: 1fr;
      gap: 2rem;
    }

    .intro-number {
      font-size: 6rem;
    }
  }

  /* ════════════════════════════════════════════════════════
   CHALLENGES  (accordion rows)
════════════════════════════════════════════════════════ */
  #challenges {
    padding: 7rem 0;
  }

  @media (max-width: 768px) {
    #challenges {
      padding: 4rem 0;
    }
  }

  .section-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 4rem;
  }

  .section-header h2 {
    font-size: clamp(2rem, 3.5vw, 2.8rem);
    font-weight: 800;
    letter-spacing: -.025em;
  }

  .challenge-row {
    border-top: 1px solid var(--line);
    overflow: hidden;
    transition: background .3s ease;
  }

  .challenge-row:last-child {
    border-bottom: 1px solid var(--line);
  }

  .challenge-row:hover {
    background: rgba(0, 102, 255, .04);
  }

  .ch-trigger {
    width: 100%;
    display: grid;
    grid-template-columns: 80px 1fr auto;
    align-items: center;
    gap: 2rem;
    padding: 2rem 0;
    background: none;
    border: none;
    color: var(--white);
    text-align: left;
  }

  .ch-num {
    font-size: .75rem;
    font-weight: 700;
    color: var(--g600);
    letter-spacing: .12em;
  }

  .ch-title {
    font-size: 1.3rem;
    font-weight: 700;
    transition: color .25s;
  }

  .challenge-row:hover .ch-title {
    color: var(--blue);
  }

  .ch-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .35s ease, border-color .25s, background .25s;
    flex-shrink: 0;
  }

  .challenge-row.open .ch-icon {
    transform: rotate(45deg);
    border-color: var(--blue);
    background: var(--blue);
  }

  .challenge-row.open .ch-title {
    color: var(--blue);
  }

  .ch-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height .45s cubic-bezier(.4, 0, .2, 1), padding .45s ease;
    padding: 0 0 0 calc(80px + 2rem);
  }

  .challenge-row.open .ch-body {
    max-height: 200px;
    padding-bottom: 2rem;
  }

  .ch-body p {
    font-size: 1rem;
    line-height: 1.75;
    color: var(--g400);
    max-width: 600px;
  }

  @media(max-width:640px) {
    .ch-trigger {
      grid-template-columns: 48px 1fr auto;
      gap: 1rem;
    }

    .ch-body {
      padding-left: calc(48px + 1rem);
    }
  }

  /* ════════════════════════════════════════════════════════
   THREE PILLARS  (fill-up hover panels)
════════════════════════════════════════════════════════ */
  #pillars {
    padding: 7rem 0;
  }

  @media (max-width: 768px) {
    #pillars {
      padding: 4rem 0;
    }
  }

  .pillars-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
    margin-top: 4rem;
  }

  .pillar-card {
    position: relative;
    padding: 3rem 2.5rem;
    border-right: 1px solid var(--line);
    overflow: hidden;
    transition: color .4s ease;
  }

  .pillar-card:last-child {
    border-right: none;
  }

  .pillar-fill {
    position: absolute;
    inset: 0;
    background: var(--blue);
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform .55s cubic-bezier(.4, 0, .2, 1);
    z-index: 0;
  }

  .pillar-card:hover .pillar-fill {
    transform: scaleY(1);
  }

  .pillar-content {
    position: relative;
    z-index: 1;
  }

  .pillar-num {
    font-size: 4rem;
    font-weight: 900;
    color: rgba(255, 255, 255, .06);
    line-height: 1;
    margin-bottom: 1.5rem;
    letter-spacing: -.04em;
  }

  .pillar-card:hover .pillar-num {
    color: rgb(255, 255, 255);
  }

  .pillar-name {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
  }

  .pillar-card:hover .pillar-name {
    color: var(--white);
  }

  .pillar-text {
    font-size: .9rem;
    line-height: 1.7;
    color: var(--g400);
    transition: color .3s;
  }

  .pillar-card:hover .pillar-text {
    color: rgba(255, 255, 255, .8);
  }

  .pillar-dots {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    margin-top: 2rem;
  }

  .pillar-dot {
    font-size: .7rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 3px;
    background: rgba(255, 255, 255, .06);
    color: var(--g400);
    transition: background .3s, color .3s;
  }

  .pillar-card:hover .pillar-dot {
    background: rgba(255, 255, 255, .15);
    color: var(--white);
  }

  @media(max-width:900px) {
    .pillars-grid {
      grid-template-columns: 1fr;
      border: none;
    }

    .pillar-card {
      border-right: none;
      border-bottom: 1px solid var(--line);
    }

    .pillar-card:last-child {
      border-bottom: none;
    }
  }

  @media(max-width:768px) {
    .pillars-grid.mobile-scroll-grid {
      border: none;
      border-radius: 0;
    }

    .pillars-grid.mobile-scroll-grid .pillar-card {
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 2.5rem 1.5rem;
      background: var(--g900);
    }
  }

  /* ════════════════════════════════════════════════════════
   SOLUTIONS  (alternating split rows)
════════════════════════════════════════════════════════ */
  #solutions {
    padding: 7rem 0;
  }

  @media (max-width: 768px) {
    #solutions {
      padding: 4rem 0;
    }
  }

  .solutions-list {
    margin-top: 4rem;
  }

  .sol-row {
    display: grid;
    grid-template-columns: 60px 1fr 1fr auto;
    align-items: center;
    gap: 2rem;
    padding: 2.5rem 0;
    border-top: 1px solid var(--line);
    position: relative;
    transition: background .25s;
    cursor: pointer;
  }

  .sol-row::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    height: 1px;
    width: 0;
    background: var(--blue);
    transition: width .6s cubic-bezier(.4, 0, .2, 1);
  }

  .sol-row:hover::after {
    width: 100%;
  }

  .sol-row:hover {
    background: rgba(0, 102, 255, .03);
  }

  .sol-row:last-child {
    border-bottom: 1px solid var(--line);
  }

  .sol-idx {
    font-size: .7rem;
    font-weight: 700;
    color: var(--g600);
    letter-spacing: .1em;
  }

  .sol-row:hover .sol-idx {
    color: var(--blue);
  }

  .sol-name {
    font-size: 1.25rem;
    font-weight: 700;
  }

  .sol-desc {
    font-size: .9rem;
    line-height: 1.6;
    color: var(--g400);
  }

  .sol-arrow {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color .25s, background .25s, transform .25s;
    flex-shrink: 0;
  }

  .sol-row:hover .sol-arrow {
    border-color: var(--blue);
    background: var(--blue);
    transform: rotate(-45deg);
  }

  @media(max-width:768px) {
    .solutions-list.mobile-scroll-grid .sol-row {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding: 2.5rem 2rem;
      border: 1px solid var(--line);
      border-radius: 12px;
      background: var(--g900);
      gap: 1.25rem;
      border-bottom: 1px solid var(--line) !important;
    }

    .sol-row {
      grid-template-columns: 40px 1fr;
      grid-template-rows: auto auto auto;
      gap: .75rem 1rem;
    }

    .sol-arrow {
      display: none;
    }

    .sol-desc {
      grid-column: 2;
    }
  }

  /* ════════════════════════════════════════════════════════
   PROCESS  (horizontal scroll timeline)
════════════════════════════════════════════════════════ */
  #process {
    padding: 7rem 0;
    overflow: hidden;
  }

  .process-stage {
    position: relative;
    min-height: auto;
  }

  @media (max-width: 992px) {
    #process {
      padding: 2rem 0;
      overflow: hidden;
    }

    .process-stage {
      position: relative;
      min-height: auto;
    }
  }

  .process-header {
    margin-bottom: 4rem;
  }

  .process-scroll {
    overflow: hidden;
    scrollbar-width: none;
    padding-bottom: 0.5rem;
  }

  .process-scroll::-webkit-scrollbar {
    display: none;
  }

  .process-track {
    display: flex;
    gap: 0;
    width: max-content;
    will-change: transform;
  }

  @media (max-width: 768px) {
    .process-scroll.mobile-scroll-grid {
      display: block !important;
      overflow-x: auto !important;
      overflow-y: hidden !important;
      -webkit-overflow-scrolling: touch;
      touch-action: pan-x;
      padding-bottom: .25rem;
      margin: 0;
      padding-left: 0;
    }

    .process-scroll.mobile-scroll-grid>.process-track {
      display: inline-flex !important;
      flex-wrap: nowrap;
      width: max-content !important;
      gap: .75rem;
      margin-right: 0 !important;
      min-width: 0 !important;
      flex: none !important;
      scroll-snap-align: unset !important;
    }

    .process-scroll.mobile-scroll-grid>.process-track>.process-step,
    .process-scroll.mobile-scroll-grid>.process-track>.process-step-with-cta {
      flex: 0 0 78vw !important;
      min-width: 78vw !important;
      max-width: 78vw !important;
    }

    .process-header {
      margin-bottom: 1rem;
    }

    .process-step-with-cta,
    .process-step {
      flex: 0 0 72vw;
      min-width: 72vw;
      max-width: 72vw;
      padding: 0 1rem 1.5rem 0;
    }

    .step-circle {
      margin-bottom: 1.25rem;
    }

    .step-desc {
      max-width: none;
    }

    .process-inline-cta {
      flex: 0 0 72vw;
      min-width: 72vw;
      max-width: 72vw;
      padding: 0 1rem 1.5rem 0;
      gap: .5rem;
    }
  }

  .process-step {

    padding: 0rem 1.75rem 3rem 0;
    position: relative;
    opacity: .45;
    transform: translateY(14px);
    transition: opacity .35s ease, transform .35s ease;
  }

  .process-step.in-view {
    opacity: 1;
    transform: translateY(0);
  }

  .process-step::before {
    content: '';
    position: absolute;
    top: 28px;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, var(--blue), rgba(0, 102, 255, .15));
  }

  @media (max-width: 768px) {
    .process-step::before {
      display: none;
    }
  }

  .process-step:first-child::before {
    left: 0;
  }

  .step-circle {
    position: relative;
    z-index: 1;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 1px solid rgba(0, 102, 255, .4);
    background: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 800;
    color: var(--blue);
    letter-spacing: .06em;
    margin-bottom: 2.5rem;
    transition: background .3s, border-color .3s;
  }

  .process-step:hover .step-circle {
    background: var(--blue);
    color: var(--white);
    border-color: var(--blue);
  }

  .step-title {
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: .75rem;
  }

  .step-desc {
    font-size: .85rem;
    line-height: 1.65;
    color: var(--g400);
    max-width: 260px;
  }

  .process-inline-cta {
    flex: 0 0 auto;
    min-width: 360px;
    padding: 0 0 3rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    opacity: .45;
    transform: translateY(14px);
    transition: opacity .35s ease, transform .35s ease;
  }

  .process-inline-cta p {
    color: var(--g400);
    line-height: 1.6;
    max-width: 320px;
  }

  .process-inline-cta.in-view {
    opacity: 1;
    transform: translateY(0);
  }

  @media (max-width: 768px) {
    .process-inline-cta {
      min-width: 85vw;
      flex-direction: column;
      align-items: flex-start;
    }
  }

  /* ════════════════════════════════════════════════════════
   METRICS  (full-width oversized)
════════════════════════════════════════════════════════ */
  #metrics {
    padding: 2rem 0 7rem 0;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
  }

  @media (max-width: 768px) {
    #metrics {
      padding: .5rem 0 4rem 0;
    }
  }

  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    position: relative;
  }

  .metric-item {
    padding: 4rem 3rem;
    border-right: 1px solid var(--line);
    position: relative;
  }

  .metric-item:last-child {
    border-right: none;
  }

  .metric-num {
    font-size: clamp(3.5rem, 5vw, 5.5rem);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -.04em;
    color: var(--white);
    margin-bottom: .75rem;
  }

  .metric-unit {
    color: var(--blue);
  }

  .metric-label {
    font-size: .7rem;
    font-weight: 700;
    color: var(--g600);
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .metric-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: var(--blue);
    width: 0;
    transition: width 1.4s cubic-bezier(.4, 0, .2, 1);
  }

  .metric-item.counted .metric-bar {
    width: 100%;
  }

  @media(max-width:768px) {
    .metrics-grid {
      grid-template-columns: 1fr 1fr;
    }

    .metric-item:nth-child(2) {
      border-right: none;
    }

    .metric-item:nth-child(3) {
      border-top: 1px solid var(--line);
      border-right: 1px solid var(--line);
    }

    .metric-item:nth-child(4) {
      border-top: 1px solid var(--line);
      border-right: none;
    }

    .metric-item {
      padding: 2.5rem 2rem;
    }
  }

  @media(max-width:480px) {
    .metrics-grid {
      grid-template-columns: 1fr;
    }

    .metric-item {
      border-right: none !important;
      border-top: 1px solid var(--line) !important;
    }

    .metric-item:first-child {
      border-top: none !important;
    }
  }

  /* ════════════════════════════════════════════════════════
   WHY US  (2-col feature list)
════════════════════════════════════════════════════════ */
  #why {
    padding: 7rem 0;
  }

  @media (max-width: 768px) {
    #why {
      padding: 4rem 0;
    }
  }

  .why-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: start;
    margin-top: 4rem;
  }

  .why-left h3 {
    font-size: clamp(2rem, 3.5vw, 3rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -.03em;
    margin-bottom: 1.5rem;
  }

  .why-left {
    position: sticky;
    top: 7rem;
    align-self: start;
  }

  .why-left p {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--g400);
    margin-bottom: 2.5rem;
  }

  .why-right {
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  .why-feature {
    padding: 1.75rem 0;
    border-bottom: 1px solid var(--line);
    display: grid;
    grid-template-columns: 28px 1fr;
    gap: 1.25rem;
    align-items: start;
  }

  .why-feature:first-child {
    border-top: 1px solid var(--line);
  }

  .why-feature-icon {
    color: var(--blue);
    margin-top: 3px;
  }

  .why-feature strong {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: .35rem;
  }

  .why-feature p {
    font-size: .85rem;
    line-height: 1.65;
    color: var(--g400);
  }

  @media(max-width:900px) {
    .why-grid {
      grid-template-columns: 1fr;
      gap: 3rem;
    }

    .why-left {
      position: relative;
      top: auto;
    }
  }

  /* ════════════════════════════════════════════════════════
   CTA  (full-bleed blue)
════════════════════════════════════════════════════════ */
  #cta-final {
    background: var(--blue);
    padding: 8rem 0;
    position: relative;
    overflow: hidden;
  }

  @media (max-width: 768px) {
    #cta-final {
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
    max-width: 520px;
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
    background: var(--g100);
    transform: translateY(-3px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, .3);
  }

  .btn-white::after {
    background: rgba(0, 102, 255, .06);
  }

  /* ─── BRAND PREVIEW WIDGET (hero right) ──────────────── */
  .brand-widget {
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
    background: var(--g900);
    min-width: 280px;
  }

  .bw-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--line);
    display: flex;
    align-items: center;
    gap: .75rem;
  }

  .bw-dots {
    display: flex;
    gap: 6px;
  }

  .bw-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .1);
  }

  .bw-dot.a {
    background: #ff5f57;
  }

  .bw-dot.b {
    background: #febc2e;
  }

  .bw-dot.c {
    background: #28c840;
  }

  .bw-title {
    font-size: .7rem;
    font-weight: 600;
    color: var(--g600);
    letter-spacing: .1em;
  }

  .bw-body {
    padding: 1.5rem;
  }

  .bw-logo-box {
    height: 80px;
    border-radius: 6px;
    background: var(--blue);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
    transition: background .4s ease;
  }

  .bw-logo-box::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, .15) 0%, transparent 60%);
  }

  .bw-logo-text {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--white);
    letter-spacing: -.02em;
  }

  .bw-swatches {
    display: flex;
    gap: .5rem;
    margin-bottom: 1.25rem;
  }

  .bw-swatch {
    flex: 1;
    height: 32px;
    border-radius: 4px;
    transition: transform .2s;
    border: 2px solid transparent;
  }

  .bw-swatch:hover {
    transform: scaleY(1.12);
  }

  .bw-swatch.active {
    border-color: var(--white);
  }

  .bw-rows {
    display: flex;
    flex-direction: column;
    gap: .5rem;
  }

  .bw-row {
    height: 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, .07);
  }

  .bw-row.w80 {
    width: 80%;
  }

  .bw-row.w60 {
    width: 60%;
  }

  .bw-row.w70 {
    width: 70%;
  }

  /* ─── MOBILE SCROLL GRID (from about-us) ────────────── */
  @media (max-width: 768px) {
    .mobile-scroll-grid {
      display: flex !important;
      flex-wrap: nowrap !important;
      overflow-x: auto !important;
      scroll-snap-type: x mandatory;
      -ms-overflow-style: none;
      scrollbar-width: none;
      padding-bottom: 1rem;
      margin: 0 -1rem;
      /* Adjust for wrap padding */
      padding-left: 1rem;
    }

    .mobile-scroll-grid::-webkit-scrollbar {
      display: none;
    }

    .mobile-scroll-grid>* {
      flex: 0 0 85vw !important;
      scroll-snap-align: start;
      margin-right: 1rem;
      min-width: 260px;
    }

    .mobile-scroll-grid>*:last-child {
      margin-right: 2rem;
    }

    .metrics-grid.mobile-scroll-grid .metric-item {
      padding: 2.5rem 2rem;
      background: var(--g900);
    }
  }
</style>

<!-- ══ HERO ════════════════════════════════════════════ -->
<section id="hero" style="margin-top:2rem;">
  <div class="wrap" style="flex:1;display:flex;flex-direction:column;">

    <div class="hero-bg-text"><?= htmlspecialchars($d2c_sh['hero_bg']['heading'] ?? 'BRAND') ?></div>

    <div style="flex:1; display:flex; align-items:end ; gap:4rem; flex-wrap:wrap; position:relative;z-index:1;">
      <div style="flex:1;min-width:300px;">
        <div class="label label-blue" style="margin-bottom:1.5rem;" data-reveal>
          <?= htmlspecialchars($hero['badge_text'] ?? 'Build a Scalable Brand That Drives Growth and Customer Loyalty') ?>
        </div>
        <h1 class="hero-headline" data-reveal data-delay="1">
          <span><?= htmlspecialchars($hero['headline_main'] ?? 'D2C Branding Services in') ?></span>
          <span class="line-accent"><?= htmlspecialchars($hero['headline_accent'] ?? 'India') ?></span>
        </h1>
      </div>
    </div>

    <div class="hero-bottom" data-reveal data-delay="3">
      <div>
        <p class="hero-sub"><?= htmlspecialchars($hero['sub_description'] ?? 'In today\'s competitive D2C market, branding is more than just design, it builds trust, shapes customer perception and drives buying decisions.') ?></p>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:2rem;">
          <a href="<?= htmlspecialchars($hero['btn1_url'] ?? 'leadform.php') ?>"
            class="btn btn-solid relative z-50 transition-[transform,box-shadow,background-color] duration-300 cursor-pointer">
            <?= htmlspecialchars($hero['btn1_label'] ?? "Let's Build Your Brand for Growth") ?>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </a>
          <a href="<?= htmlspecialchars($hero['btn2_url'] ?? '#pillars') ?>" class="btn btn-outline relative z-50">
            <?= htmlspecialchars($hero['btn2_label'] ?? 'Our Approach') ?>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ MARQUEE ═════════════════════════════════════════ -->
<?php
$_mq_items = json_decode($d2c_sh['marquee']['heading'] ?? '[]', true) ?: ['BRAND IDENTITY','LOGO DESIGN','PACKAGING','BRAND STRATEGY','VISUAL GUIDELINES','D2C GROWTH'];
$_mq_str = implode(' <span class="mq-sep">◆</span> ', array_map('htmlspecialchars', $_mq_items));
?>
<div id="marquee-strip">
  <div class="mq-track">
    <?= $_mq_str ?> <span class="mq-sep">◆</span> <?= $_mq_str ?> <span class="mq-sep">◆</span>&nbsp;
  </div>
</div>

<!-- ══ INTRO ═══════════════════════════════════════════ -->
<section id="intro">
  <div class="wrap">
    <div class="intro-grid">
      <div class="intro-number" data-reveal="left"><?= htmlspecialchars($d2c_sh['intro']['eyebrow'] ?? 'WHY') ?></div>
      <div data-reveal data-delay="1">
        <div class="label" style="margin-bottom:1.5rem; font-size:24px"><?= htmlspecialchars($d2c_sh['intro']['heading'] ?? 'Understanding the Challenges') ?></div>

        <p><?= htmlspecialchars($d2c_sh['intro']['sub_text'] ?? 'Many D2C brands struggle to achieve consistent growth because their branding lacks clarity and direction. In a crowded digital marketplace, customers are constantly exposed to multiple choices and without a strong brand identity, it becomes difficult to capture attention and build trust.') ?></p>
        <p><?= htmlspecialchars($d2c_sh['intro']['extra_text'] ?? 'At Digifyce, we help businesses build clear, consistent, and impactful brand identities across every customer touchpoint, closing the gaps that reduce conversion rates and long-term brand recall.') ?></p>
        <div class="intro-tags">
          <?php foreach ($introTags as $t): ?>
            <span class="tag"><?= htmlspecialchars($t) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ CHALLENGES ══════════════════════════════════════ -->
<section id="challenges" style="background:var(--g900);">
  <div class="wrap">
    <div class="section-header" data-reveal>
      <div>
        <div class="label" style="margin-bottom:.75rem;"><?= htmlspecialchars($d2c_sh['challenges']['eyebrow'] ?? 'Common Friction Points') ?></div>
        <h2><?= htmlspecialchars($d2c_sh['challenges']['heading'] ?? 'Common Challenges Include') ?></h2>
        <?php if (!empty($d2c_sh['challenges']['sub_text'])): ?>
        <p><?= htmlspecialchars($d2c_sh['challenges']['sub_text']) ?></p>
        <?php else: ?>
        <p>These gaps not only affect customer perception but also reduce conversion rates and long-term brand recall.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php foreach ($challenges as $i => $ch): ?>
      <div class="challenge-row" data-reveal data-delay="<?= min($i, 4) ?>">
        <button class="ch-trigger" aria-expanded="false">
          <span class="ch-num">0<?= $i + 1 ?></span>
          <span class="ch-title"><?= htmlspecialchars($ch['title']) ?></span>
          <span class="ch-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </span>
        </button>
        <div class="ch-body">
          <p><?= htmlspecialchars($ch['description']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══ PILLARS ════════════════════════════════════════ -->
<section id="pillars" style="padding: 8rem 0;">
  <div class="wrap">
    <div class="split-header" data-reveal>
      <div>
        <div class="label" style="margin-bottom:.75rem;"><?= htmlspecialchars($d2c_sh['pillars']['eyebrow'] ?? 'Our Philosophy') ?></div>
        <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:800;letter-spacing:-.025em;line-height:1.1;">
          <?= htmlspecialchars($d2c_sh['pillars']['heading'] ?? 'Our Approach to Building High-Performance D2C Brands') ?></h2>
      </div>
      <p style="color:var(--g400);line-height:1.75;font-size:.95rem;"><?= nl2br(htmlspecialchars($d2c_sh['pillars']['sub_text'] ?? 'At Digifyce, we approach branding as a strategic business asset, not just a creative output. We focus on building a complete brand ecosystem that aligns with your product, audience, and market dynamics. Every element of your brand is carefully crafted to communicate value, establish trust, and influence buying behavior.')) ?></p>
    </div>
    <div class="pillars-grid mobile-scroll-grid">
      <?php foreach ($pillars as $p): ?>
        <div class="pillar-card" data-reveal>
          <div class="pillar-fill"></div>
          <div class="pillar-content">
            <div class="pillar-num"><?= htmlspecialchars($p['number']) ?></div>
            <div class="pillar-name"><?= htmlspecialchars($p['name']) ?></div>
            <p class="pillar-text"><?= htmlspecialchars($p['text']) ?></p>
            <div class="pillar-dots">
              <?php foreach (json_decode($p['dots_json'], true) ?? [] as $d): ?>
                <span class="pillar-dot"><?= htmlspecialchars($d) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ SOLUTIONS ══════════════════════════════════════ -->
<section id="solutions" style="background:var(--g900);">
  <div class="wrap">
    <div class="section-header" data-reveal>
      <div>
        <div class="label" style="margin-bottom:.75rem;"><?= htmlspecialchars($d2c_sh['solutions']['eyebrow'] ?? 'What We Deliver') ?></div>
        <h2><?= htmlspecialchars($d2c_sh['solutions']['heading'] ?? 'Comprehensive D2C Branding Solutions') ?></h2>
      </div>
    </div>
    <div class="solutions-list mobile-scroll-grid">
      <?php foreach ($sols as $i => $s): ?>
        <div class="sol-row" data-reveal data-delay="<?= min($i % 3, 4) ?>" role="button" tabindex="0"
          aria-label="Go to lead form">
          <span class="sol-idx">0<?= $i + 1 ?></span>
          <span class="sol-name"><?= htmlspecialchars($s['name']) ?></span>
          <span class="sol-desc"><?= htmlspecialchars($s['description']) ?></span>
          <span class="sol-arrow">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7" />
            </svg>
          </span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ PROCESS ════════════════════════════════════════ -->
<section id="process">
  <div class="wrap process-stage">
    <div class="process-header" data-reveal>
      <div class="label" style="margin-bottom:.75rem;"><?= htmlspecialchars($d2c_sh['process']['eyebrow'] ?? 'How We Work') ?></div>
      <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);font-weight:800;letter-spacing:-.025em;"><?= htmlspecialchars($d2c_sh['process']['heading'] ?? 'End-to-End Branding Process') ?></h2>
    </div>
    <div class="process-scroll mobile-scroll-grid" id="processScroll">
      <div class="process-track" id="processTrack">
        <?php foreach ($steps as $i => $s): ?>
          <?php if ($i === 0): ?>
            <div class="process-step-with-cta" data-reveal style="display: flex; flex-direction: column; gap: 0;">
              <div class="process-step" style="padding-bottom: 0;">
                <div class="step-circle"><?= htmlspecialchars($s['step_number']) ?></div>
                <div class="step-title"><?= htmlspecialchars($s['title']) ?></div>
                <div class="step-desc"><?= htmlspecialchars($s['description']) ?></div>
              </div>
              <div class="process-inline-cta"
                style="display: flex; flex-direction: column; align-items: flex-start; gap: 10px; padding-top: 0;">
                <p><?= htmlspecialchars($d2c_sh['process_cta']['sub_text'] ?? 'Speak with our team and get a tailored D2C branding roadmap.') ?></p>
                <a href="<?= htmlspecialchars($d2c_sh['process_cta']['btn_url'] ?? 'leadform.php') ?>" class="btn btn-solid" style="margin-top: 0;">
                  <?= htmlspecialchars($d2c_sh['process_cta']['btn_label'] ?? 'Ready to turn this process into growth') ?>
                  <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="process-step" data-reveal data-delay="<?= min($i, 5) ?>">
              <div class="step-circle"><?= htmlspecialchars($s['step_number']) ?></div>
              <div class="step-title"><?= htmlspecialchars($s['title']) ?></div>
              <div class="step-desc"><?= htmlspecialchars($s['description']) ?></div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══ METRICS ════════════════════════════════════════ -->
<section id="metrics">
  <div class="wrap">
    <div class="section-header" data-reveal>
      <div>
        <div class="label" style="margin-bottom:.75rem;"><?= htmlspecialchars($d2c_sh['metrics']['eyebrow'] ?? 'Why It Works') ?></div>
        <h2><?= htmlspecialchars($d2c_sh['metrics']['heading'] ?? 'Branding Impact at a Glance') ?></h2>
        <?php if (!empty($d2c_sh['metrics']['sub_text'])): ?>
        <p><?= htmlspecialchars($d2c_sh['metrics']['sub_text']) ?></p>
        <?php else: ?>
        <p>Our D2C branding process is designed to improve trust, recall, and conversion across every touchpoint.</p>
        <?php endif; ?>
      </div>
    </div>
    <div class="metrics-grid mobile-scroll-grid">
      <?php foreach ($mets as $m): ?>
        <div class="metric-item" style="border: 1px solid var(--line); border-radius: 12px;">
          <div class="metric-num">
            <span class="counter-val" data-target="<?= (int)$m['value'] ?>"><?= htmlspecialchars($m['value']) ?></span><span
              class="metric-unit"><?= htmlspecialchars($m['unit']) ?></span>
          </div>
          <div class="metric-label"><?= htmlspecialchars($m['label']) ?></div>
          <div class="metric-bar" style="width:<?= (int)$m['bar_pct'] ?>%;"></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ WHY US ══════════════════════════════════════════ -->
<section id="why">
  <div class="wrap">
    <div class="label" data-reveal style="margin-bottom:1rem;"><?= htmlspecialchars($d2c_sh['why']['eyebrow'] ?? 'Building Brands That are Performance Driven.') ?></div>
    <div class="why-grid">
      <div class="why-left" data-reveal="left">
        <h3><?= htmlspecialchars($d2c_sh['why']['heading'] ?? 'Why Choose Digifyce') ?></h3>
        <p><?= htmlspecialchars($d2c_sh['why']['sub_text'] ?? 'At Digifyce, we value our clients and understand that D2C brands require a specialized approach. We work as a growth partner, helping you build a brand that is ready to scale in the Indian market.') ?></p>
        <a href="<?= htmlspecialchars($d2c_sh['why']['btn_url'] ?? 'leadform.php') ?>" class="btn btn-solid" style="margin-top:.5rem;">
          <?= htmlspecialchars($d2c_sh['why']['btn_label'] ?? 'Start Your Brand Journey') ?>
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>
      <div class="why-right" data-reveal="right">
        <?php foreach ($feats as $f): ?>
          <div class="why-feature">
            <svg class="why-feature-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <div>
              <strong><?= htmlspecialchars($f['title']) ?></strong>
              <p><?= htmlspecialchars($f['description']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══ CTA ════════════════════════════════════════════ -->
<section id="cta-final">
  <div class="cta-bg-text"><?= htmlspecialchars($d2c_sh['cta_bg']['heading'] ?? 'GROW') ?></div>
  <div class="wrap">
    <div class="cta-inner" data-reveal>
      <h2><?= nl2br(htmlspecialchars($cta['heading'] ?? 'Build a Brand That Stands Out and Scales')) ?></h2>
      <p><?= htmlspecialchars($cta['description'] ?? 'Your brand is one of your most valuable assets. Let\'s create something clear, consistent, and built for long-term growth.') ?></p>
      <a href="<?= htmlspecialchars($cta['btn_url'] ?? 'leadform.php') ?>" class="btn btn-white" style="font-size:1rem;padding:18px 44px;">
        <?= htmlspecialchars($cta['btn_label'] ?? 'Get Started with Digifyce Today') ?>
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
      </a>
    </div>
  </div>
</section>

<script>
  (function () {
    /* ── Scroll Reveals ────────────────────── */
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.classList.add('in');
        // Counter animation
        e.target.querySelectorAll('.counter-val').forEach(el => {
          if (el.dataset.counted) return;
          el.dataset.counted = '1';
          const target = +el.dataset.target;
          const parent = el.closest('.metric-item');
          if (parent) parent.classList.add('counted');
          let start = 0, dur = 1600, begin = null;
          const easeOut = t => 1 - Math.pow(1 - t, 3);
          const step = (ts) => {
            if (!begin) begin = ts;
            const p = Math.min((ts - begin) / dur, 1);
            el.textContent = Math.round(easeOut(p) * target);
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = target;
          };
          requestAnimationFrame(step);
        });
      });
    }, { threshold: 0.12 });
    document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));

    /* ── Metric items observer ─────────────── */
    const mio = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target.querySelector('.counter-val');
        if (!el || el.dataset.counted) return;
        el.dataset.counted = '1';
        const target = +el.dataset.target;
        e.target.classList.add('counted');
        let start = 0, dur = 1600, begin = null;
        const easeOut = t => 1 - Math.pow(1 - t, 3);
        const step = ts => {
          if (!begin) begin = ts;
          const p = Math.min((ts - begin) / dur, 1);
          el.textContent = Math.round(easeOut(p) * target);
          if (p < 1) requestAnimationFrame(step); else el.textContent = target;
        };
        requestAnimationFrame(step);
      });
    }, { threshold: 0.3 });
    document.querySelectorAll('.metric-item').forEach(el => mio.observe(el));

    /* ── Accordion ─────────────────────────── */
    document.querySelectorAll('.ch-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const row = btn.closest('.challenge-row');
        const isOpen = row.classList.contains('open');
        document.querySelectorAll('.challenge-row.open').forEach(r => r.classList.remove('open'));
        if (!isOpen) row.classList.add('open');
      });
    });

    /* ── Solutions lead form jump ───────── */
    document.querySelectorAll('.sol-row').forEach(row => {
      const goLeadForm = () => {
        window.location.href = 'leadform.php';
      };
      row.addEventListener('click', goLeadForm);
      row.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          goLeadForm();
        }
      });
    });

    /* ── Brand Widget Swatches ─────────────── */
    const swatches = document.querySelectorAll('.bw-swatch');
    const logoBox = document.getElementById('logoBox');
    const logoText = document.getElementById('logoText');
    swatches.forEach(sw => {
      sw.addEventListener('click', () => {
        swatches.forEach(s => s.classList.remove('active'));
        sw.classList.add('active');
        logoBox.style.background = sw.dataset.color;
        logoText.textContent = sw.dataset.name;
      });
    });

    /* ── Drag-scroll process ───────────────── */
    const scroll = document.getElementById('processScroll');
    const processSection = document.getElementById('process');
    const processTrack = document.getElementById('processTrack');
    if (scroll && processSection) {
      // Reveal each process step as it enters the horizontal viewport.
      const processIo = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          entry.target.classList.toggle('in-view', entry.isIntersecting);
        });
      }, { root: scroll, threshold: 0.55 });
      scroll.querySelectorAll('.process-step, .process-inline-cta').forEach((step) => processIo.observe(step));

      let isDown = false, startX, sLeft;
      scroll.addEventListener('mousedown', e => { isDown = true; startX = e.pageX - scroll.offsetLeft; sLeft = scroll.scrollLeft; });
      scroll.addEventListener('mouseleave', () => isDown = false);
      scroll.addEventListener('mouseup', () => isDown = false);
      scroll.addEventListener('mousemove', e => {
        if (!isDown) return; e.preventDefault();
        scroll.scrollLeft = sLeft - (e.pageX - scroll.offsetLeft - startX);
      });

      // Horizontal process timeline powered by GSAP using transform-based track animation.
      let mm = null;
      const initProcessTimeline = () => {
        if (!processTrack) return;
        if (mm) {
          mm.revert();
          mm = null;
        }

        if (!(window.gsap && window.ScrollTrigger)) return;
        gsap.registerPlugin(ScrollTrigger);

        mm = gsap.matchMedia();

        mm.add('(min-width: 993px)', () => {
          const getDistance = () => Math.max(processTrack.scrollWidth - scroll.clientWidth, 0);
          const distance = getDistance();
          if (distance <= 0) return;

          gsap.set(processTrack, { x: 0 });
          const tween = gsap.to(processTrack, {
            x: () => -getDistance(),
            ease: 'none'
          });

          const trigger = ScrollTrigger.create({
            id: 'processHorizontal',
            trigger: processSection,
            start: 'top top',
            end: () => `+=${getDistance() + 220}`,
            pin: true,
            scrub: 1,
            anticipatePin: 1,
            invalidateOnRefresh: true,
            animation: tween
          });

          return () => {
            trigger.kill();
            tween.kill();
            gsap.set(processTrack, { clearProps: 'transform' });
          };
        });

        mm.add('(max-width: 992px)', () => {
          gsap.set(processTrack, { clearProps: 'transform' });
        });

        ScrollTrigger.refresh();
      };

      initProcessTimeline();
      window.addEventListener('load', initProcessTimeline, { once: true });
      let resizeTick = false;
      window.addEventListener('resize', () => {
        if (resizeTick) return;
        resizeTick = true;
        requestAnimationFrame(() => {
          initProcessTimeline();
          resizeTick = false;
        });
      });
    }
  })();
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
