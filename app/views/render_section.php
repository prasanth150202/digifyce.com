<?php
/**
 * Frontend section renderer for the Page Builder.
 * Call render_section($type, $config) to get the HTML for any section.
 */

function render_section(string $type, array $cfg): string {
    return match ($type) {
        'hero'         => _sec_hero($cfg),
        'features'     => _sec_features($cfg),
        'stats'        => _sec_stats($cfg),
        'cta'          => _sec_cta($cfg),
        'text_image'   => _sec_text_image($cfg),
        'faq'          => _sec_faq($cfg),
        'testimonials' => _sec_testimonials($cfg),
        'services'     => _sec_services($cfg),
        'content'      => _sec_content($cfg),
        default        => '',
    };
}

function _h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// ─── HERO ────────────────────────────────────────────────────────────────────
function _sec_hero(array $c): string {
    $bg = $c['bg'] ?? 'dark';
    $bgStyle = match($bg) {
        'blue'      => 'background:linear-gradient(135deg,#003eb3 0%,#0066ff 60%,#003eb3 100%)',
        'light'     => 'background:#f8fafc',
        default     => 'background:#05070a',
    };
    $textColor  = $bg === 'light' ? '#111' : '#fff';
    $subColor   = $bg === 'light' ? '#555' : 'rgba(255,255,255,.7)';
    $badgeStyle = $bg === 'light'
        ? 'border:1px solid #0066ff33;color:#0066ff'
        : 'border:1px solid rgba(0,102,255,.4);color:#60a5fa';

    $badge      = _h($c['badge']      ?? '');
    $headline   = $c['headline']      ?? '';        // HTML allowed
    $subtext    = _h($c['subtext']    ?? '');
    $ctaLabel   = _h($c['cta_label']  ?? '');
    $ctaUrl     = _h($c['cta_url']    ?? '#');
    $cta2Label  = _h($c['cta2_label'] ?? '');
    $cta2Url    = _h($c['cta2_url']   ?? '#');

    ob_start(); ?>
<section style="<?= $bgStyle ?>;padding:100px 24px;position:relative;overflow:hidden">
    <div style="max-width:820px;margin:0 auto;text-align:center;position:relative;z-index:1">
        <?php if ($badge): ?>
        <span style="display:inline-block;padding:6px 18px;border-radius:50px;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:28px;<?= $badgeStyle ?>"><?= $badge ?></span>
        <?php endif; ?>
        <h1 style="color:<?= $textColor ?>;font-size:clamp(36px,5vw,64px);font-weight:700;line-height:1.12;margin:0 0 24px;font-family:'Space Grotesk',sans-serif">
            <?= $headline ?>
        </h1>
        <?php if ($subtext): ?>
        <p style="color:<?= $subColor ?>;font-size:18px;max-width:600px;margin:0 auto 40px;line-height:1.7">
            <?= $subtext ?>
        </p>
        <?php endif; ?>
        <?php if ($ctaLabel || $cta2Label): ?>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
            <?php if ($ctaLabel): ?>
            <a href="<?= $ctaUrl ?>" style="display:inline-flex;align-items:center;padding:14px 36px;background:#0066ff;color:#fff;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;transition:background .2s"
               onmouseover="this.style.background='#0052cc'" onmouseout="this.style.background='#0066ff'"><?= $ctaLabel ?></a>
            <?php endif; ?>
            <?php if ($cta2Label): ?>
            <a href="<?= $cta2Url ?>" style="display:inline-flex;align-items:center;padding:14px 36px;border:2px solid rgba(255,255,255,.25);color:<?= $textColor ?>;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none"><?= $cta2Label ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── FEATURES ────────────────────────────────────────────────────────────────
function _sec_features(array $c): string {
    $badge    = _h($c['badge']    ?? '');
    $title    = _h($c['title']    ?? '');
    $subtitle = _h($c['subtitle'] ?? '');
    $cols     = intval($c['columns'] ?? 3);
    $items    = $c['items'] ?? [];
    $gap      = (int)(100 / $cols) - 2;

    ob_start(); ?>
<section style="background:#05070a;padding:96px 24px">
    <div style="max-width:1200px;margin:0 auto">
        <?php if ($badge || $title): ?>
        <div style="text-align:center;margin-bottom:64px">
            <?php if ($badge): ?>
            <span style="display:inline-block;padding:5px 16px;border-radius:50px;border:1px solid rgba(0,102,255,.4);color:#60a5fa;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:20px"><?= $badge ?></span>
            <?php endif; ?>
            <?php if ($title): ?>
            <h2 style="color:#fff;font-size:clamp(28px,4vw,44px);font-weight:700;margin:0 0 16px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2>
            <?php endif; ?>
            <?php if ($subtitle): ?>
            <p style="color:rgba(255,255,255,.6);font-size:17px;max-width:580px;margin:0 auto"><?= $subtitle ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(<?= $cols===4?'220px':'280px' ?>,1fr));gap:24px">
        <?php foreach ($items as $item): ?>
            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px 28px;transition:border-color .2s"
                 onmouseover="this.style.borderColor='rgba(0,102,255,.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                <?php if (!empty($item['icon'])): ?>
                <div style="width:52px;height:52px;background:rgba(0,102,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:24px">
                    <i class="<?= _h($item['icon']) ?>" style="color:#0066ff;font-size:22px"></i>
                </div>
                <?php endif; ?>
                <h3 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 12px;font-family:'Space Grotesk',sans-serif"><?= _h($item['title'] ?? '') ?></h3>
                <p style="color:rgba(255,255,255,.6);font-size:14px;line-height:1.7;margin:0"><?= _h($item['text'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── STATS ───────────────────────────────────────────────────────────────────
function _sec_stats(array $c): string {
    $title    = _h($c['title']    ?? '');
    $subtitle = _h($c['subtitle'] ?? '');
    $items    = $c['items'] ?? [];

    ob_start(); ?>
<section style="background:#020406;padding:80px 24px;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06)">
    <div style="max-width:1100px;margin:0 auto">
        <?php if ($title || $subtitle): ?>
        <div style="text-align:center;margin-bottom:56px">
            <?php if ($title): ?><h2 style="color:#fff;font-size:32px;font-weight:700;margin:0 0 12px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2><?php endif; ?>
            <?php if ($subtitle): ?><p style="color:rgba(255,255,255,.5);font-size:16px;margin:0"><?= $subtitle ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:2px;border:1px solid rgba(255,255,255,.08);border-radius:16px;overflow:hidden">
        <?php foreach ($items as $item): ?>
            <div style="padding:40px 28px;text-align:center;background:rgba(255,255,255,.02)">
                <div style="font-size:clamp(36px,4vw,52px);font-weight:800;color:#0066ff;font-family:'Space Grotesk',sans-serif;line-height:1"><?= _h($item['value'] ?? '') ?></div>
                <div style="color:#fff;font-size:15px;font-weight:600;margin-top:10px"><?= _h($item['label'] ?? '') ?></div>
                <?php if (!empty($item['sub'])): ?>
                <div style="color:rgba(255,255,255,.4);font-size:12px;margin-top:6px"><?= _h($item['sub']) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── CTA BANNER ──────────────────────────────────────────────────────────────
function _sec_cta(array $c): string {
    $style    = $c['style'] ?? 'blue';
    $headline = $c['headline'] ?? '';
    $subtext  = _h($c['subtext']   ?? '');
    $ctaLabel = _h($c['cta_label'] ?? '');
    $ctaUrl   = _h($c['cta_url']   ?? '#');

    $bg = match($style) {
        'gradient' => 'background:linear-gradient(135deg,#003eb3,#0066ff)',
        'dark'     => 'background:#111827',
        default    => 'background:#0066ff',
    };

    ob_start(); ?>
<section style="<?= $bg ?>;padding:80px 24px;text-align:center">
    <div style="max-width:700px;margin:0 auto">
        <h2 style="color:#fff;font-size:clamp(28px,4vw,44px);font-weight:700;margin:0 0 16px;line-height:1.2;font-family:'Space Grotesk',sans-serif"><?= $headline ?></h2>
        <?php if ($subtext): ?>
        <p style="color:rgba(255,255,255,.8);font-size:17px;margin:0 0 36px"><?= $subtext ?></p>
        <?php endif; ?>
        <?php if ($ctaLabel): ?>
        <a href="<?= $ctaUrl ?>"
           style="display:inline-flex;align-items:center;padding:16px 44px;background:#fff;color:#0066ff;border-radius:8px;font-weight:700;font-size:16px;text-decoration:none"><?= $ctaLabel ?></a>
        <?php endif; ?>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── TEXT + IMAGE ─────────────────────────────────────────────────────────────
function _sec_text_image(array $c): string {
    $imgPos  = ($c['image_position'] ?? 'right') === 'left' ? 'row-reverse' : 'row';
    $badge   = _h($c['badge']      ?? '');
    $title   = $c['title']         ?? '';
    $content = _h($c['content']    ?? '');
    $ctaLbl  = _h($c['cta_label']  ?? '');
    $ctaUrl  = _h($c['cta_url']    ?? '#');
    $imgUrl  = _h($c['image_url']  ?? '');

    ob_start(); ?>
<section style="background:#05070a;padding:96px 24px">
    <div style="max-width:1100px;margin:0 auto;display:flex;gap:64px;align-items:center;flex-wrap:wrap;flex-direction:<?= $imgPos ?>">
        <div style="flex:1;min-width:280px">
            <?php if ($badge): ?>
            <span style="display:inline-block;padding:5px 16px;border-radius:50px;border:1px solid rgba(0,102,255,.4);color:#60a5fa;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:20px"><?= $badge ?></span>
            <?php endif; ?>
            <h2 style="color:#fff;font-size:clamp(26px,3.5vw,40px);font-weight:700;margin:0 0 20px;line-height:1.2;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2>
            <p style="color:rgba(255,255,255,.65);font-size:16px;line-height:1.8;margin:0 0 32px"><?= $content ?></p>
            <?php if ($ctaLbl): ?>
            <a href="<?= $ctaUrl ?>" style="display:inline-flex;align-items:center;padding:13px 32px;background:#0066ff;color:#fff;border-radius:8px;font-weight:700;font-size:14px;text-decoration:none"><?= $ctaLbl ?></a>
            <?php endif; ?>
        </div>
        <div style="flex:1;min-width:260px">
            <?php if ($imgUrl): ?>
            <img src="<?= $imgUrl ?>" alt="" style="width:100%;border-radius:16px;display:block;border:1px solid rgba(255,255,255,.08)">
            <?php else: ?>
            <div style="width:100%;aspect-ratio:16/10;background:rgba(255,255,255,.04);border:1px dashed rgba(255,255,255,.15);border-radius:16px;display:flex;align-items:center;justify-content:center">
                <span style="color:rgba(255,255,255,.2);font-size:14px">No image set</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── FAQ ─────────────────────────────────────────────────────────────────────
function _sec_faq(array $c): string {
    static $faqCount = 0;
    $uid      = 'faq' . (++$faqCount);
    $title    = _h($c['title']    ?? '');
    $subtitle = _h($c['subtitle'] ?? '');
    $items    = $c['items'] ?? [];

    ob_start(); ?>
<section style="background:#05070a;padding:96px 24px">
    <div style="max-width:760px;margin:0 auto">
        <?php if ($title || $subtitle): ?>
        <div style="text-align:center;margin-bottom:56px">
            <?php if ($title): ?><h2 style="color:#fff;font-size:clamp(28px,4vw,40px);font-weight:700;margin:0 0 14px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2><?php endif; ?>
            <?php if ($subtitle): ?><p style="color:rgba(255,255,255,.55);font-size:16px;margin:0"><?= $subtitle ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        <div>
        <?php foreach ($items as $i => $item): ?>
            <div style="border-bottom:1px solid rgba(255,255,255,.1)">
                <button onclick="toggleFaq('<?= $uid ?>-<?= $i ?>')"
                    style="width:100%;background:none;border:none;color:#fff;text-align:left;padding:22px 0;display:flex;justify-content:space-between;align-items:center;cursor:pointer;font-size:16px;font-weight:600;font-family:'Space Grotesk',sans-serif">
                    <?= _h($item['question'] ?? '') ?>
                    <span id="<?= $uid ?>-<?= $i ?>-icon" style="color:#0066ff;font-size:20px;line-height:1;flex-shrink:0;margin-left:16px">+</span>
                </button>
                <div id="<?= $uid ?>-<?= $i ?>" style="display:none;padding:0 0 22px">
                    <p style="color:rgba(255,255,255,.65);font-size:15px;line-height:1.75;margin:0"><?= _h($item['answer'] ?? '') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<script>
function toggleFaq(id){
    var el=document.getElementById(id),ic=document.getElementById(id+'-icon');
    var open=el.style.display!=='none';
    el.style.display=open?'none':'block';
    ic.textContent=open?'+':'−';
}
</script>
    <?php return ob_get_clean();
}

// ─── TESTIMONIALS ─────────────────────────────────────────────────────────────
function _sec_testimonials(array $c): string {
    $title    = _h($c['title']    ?? '');
    $subtitle = _h($c['subtitle'] ?? '');
    $items    = $c['items'] ?? [];

    ob_start(); ?>
<section style="background:#020406;padding:96px 24px">
    <div style="max-width:1100px;margin:0 auto">
        <?php if ($title || $subtitle): ?>
        <div style="text-align:center;margin-bottom:60px">
            <?php if ($title): ?><h2 style="color:#fff;font-size:clamp(28px,4vw,42px);font-weight:700;margin:0 0 14px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2><?php endif; ?>
            <?php if ($subtitle): ?><p style="color:rgba(255,255,255,.55);font-size:16px;margin:0"><?= $subtitle ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px">
        <?php foreach ($items as $item): ?>
            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;position:relative">
                <div style="color:#0066ff;font-size:36px;font-family:serif;line-height:1;margin-bottom:16px">&ldquo;</div>
                <p style="color:rgba(255,255,255,.85);font-size:15px;line-height:1.75;margin:0 0 28px;font-style:italic"><?= _h($item['quote'] ?? '') ?></p>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#0066ff,#003eb3);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;flex-shrink:0">
                        <?= strtoupper(substr($item['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;font-family:'Space Grotesk',sans-serif"><?= _h($item['name'] ?? '') ?></div>
                        <div style="color:rgba(255,255,255,.45);font-size:12px"><?= _h(($item['role'] ?? '') . ($item['company'] ? ', ' . $item['company'] : '')) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── SERVICES ────────────────────────────────────────────────────────────────
function _sec_services(array $c): string {
    $badge    = _h($c['badge']    ?? '');
    $title    = _h($c['title']    ?? '');
    $subtitle = _h($c['subtitle'] ?? '');
    $items    = $c['items'] ?? [];

    ob_start(); ?>
<section style="background:#05070a;padding:96px 24px">
    <div style="max-width:1200px;margin:0 auto">
        <?php if ($badge || $title): ?>
        <div style="text-align:center;margin-bottom:64px">
            <?php if ($badge): ?>
            <span style="display:inline-block;padding:5px 16px;border-radius:50px;border:1px solid rgba(0,102,255,.4);color:#60a5fa;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:20px"><?= $badge ?></span>
            <?php endif; ?>
            <?php if ($title): ?><h2 style="color:#fff;font-size:clamp(28px,4vw,44px);font-weight:700;margin:0 0 16px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2><?php endif; ?>
            <?php if ($subtitle): ?><p style="color:rgba(255,255,255,.6);font-size:17px;max-width:560px;margin:0 auto"><?= $subtitle ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:2px;border:1px solid rgba(255,255,255,.08);border-radius:16px;overflow:hidden">
        <?php foreach ($items as $item): ?>
            <?php $tag = !empty($item['url']) ? 'a' : 'div'; $href = !empty($item['url']) ? "href=\"" . _h($item['url']) . "\"" : ''; ?>
            <<?= $tag ?> <?= $href ?> style="display:block;padding:36px 32px;background:rgba(255,255,255,.02);text-decoration:none;transition:background .2s"
                onmouseover="this.style.background='rgba(0,102,255,.06)'" onmouseout="this.style.background='rgba(255,255,255,.02)'">
                <?php if (!empty($item['icon'])): ?>
                <div style="width:48px;height:48px;background:rgba(0,102,255,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:20px">
                    <i class="<?= _h($item['icon']) ?>" style="color:#0066ff;font-size:20px"></i>
                </div>
                <?php endif; ?>
                <h3 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 10px;font-family:'Space Grotesk',sans-serif"><?= _h($item['title'] ?? '') ?></h3>
                <p style="color:rgba(255,255,255,.55);font-size:14px;line-height:1.7;margin:0"><?= _h($item['text'] ?? '') ?></p>
                <?php if (!empty($item['url'])): ?>
                <span style="display:inline-flex;align-items:center;color:#0066ff;font-size:13px;font-weight:600;margin-top:16px">Learn more →</span>
                <?php endif; ?>
            </<?= $tag ?>>
        <?php endforeach; ?>
        </div>
    </div>
</section>
    <?php return ob_get_clean();
}

// ─── CONTENT BLOCK ────────────────────────────────────────────────────────────
function _sec_content(array $c): string {
    $title   = _h($c['title']   ?? '');
    $content = $c['content']    ?? '';   // HTML allowed
    $align   = $c['align']      === 'center' ? 'center' : 'left';
    $bg      = ($c['bg'] ?? 'dark') === 'light' ? '#f8fafc' : '#05070a';
    $textClr = ($c['bg'] ?? 'dark') === 'light' ? '#222'    : 'rgba(255,255,255,.75)';
    $titleClr= ($c['bg'] ?? 'dark') === 'light' ? '#111'    : '#fff';
    $maxW    = match($c['max_width'] ?? 'medium') {
        'narrow' => '640px', 'wide' => '1100px', default => '800px'
    };

    ob_start(); ?>
<section style="background:<?= $bg ?>;padding:80px 24px">
    <div style="max-width:<?= $maxW ?>;margin:0 auto;text-align:<?= $align ?>">
        <?php if ($title): ?>
        <h2 style="color:<?= $titleClr ?>;font-size:clamp(24px,3.5vw,38px);font-weight:700;margin:0 0 28px;font-family:'Space Grotesk',sans-serif"><?= $title ?></h2>
        <?php endif; ?>
        <div style="color:<?= $textClr ?>;font-size:16px;line-height:1.85"><?= $content ?></div>
    </div>
</section>
    <?php return ob_get_clean();
}
