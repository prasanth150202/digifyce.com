<?php
// footer.php: Contains the full footer HTML with dynamic navigation and settings
$appUrl = $appUrl ?? 'http://localhost/digifyce2';
$footerLogo = '';
$footerDescription = '';
$footerCopyright = '© ' . date('Y') . ' Digifyce Performance. All rights reserved.';
$siteLogo = '';
$footerPages = [];

try {
    require_once __DIR__ . '/../../config/database.php';
    $pdo = Database::getInstance();
    $settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('footer_logo','footer_description','footer_copyright','site_logo')")->fetchAll();
    foreach ($settings as $row) {
        if ($row['setting_key'] === 'footer_logo') {
            $footerLogo = $row['setting_value'];
        } elseif ($row['setting_key'] === 'footer_description') {
            $footerDescription = $row['setting_value'];
        } elseif ($row['setting_key'] === 'footer_copyright') {
            $footerCopyright = $row['setting_value'];
        } elseif ($row['setting_key'] === 'site_logo') {
            $siteLogo = $row['setting_value'];
        }
    }
    $footerPages = $pdo->query('SELECT title, slug FROM pages ORDER BY title ASC')->fetchAll();
} catch (Exception $e) {
    // Use defaults if database fails
}
?>
<footer class="py-8 sm:py-12 lg:py-16 bg-[#020406] border-t border-white/5">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-12 lg:gap-16 mb-12 sm:mb-16 lg:mb-20">
            <div>
                <?php if (!empty($footerLogo)): ?>
                    <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($footerLogo, '/')) ?>" alt="Footer Logo" class="h-16 sm:h-20 lg:h-24 w-auto mb-6 sm:mb-8">
                <?php elseif (!empty($siteLogo)): ?>
                    <img src="<?= htmlspecialchars($appUrl . '/' . ltrim($siteLogo, '/')) ?>" alt="Logo" class="h-16 sm:h-20 lg:h-24 w-auto mb-6 sm:mb-8">
                <?php else: ?>
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tighter mb-6 sm:mb-8">DIGIFYCE.</div>
                <?php endif; ?>
                <p class="max-w-xs text-slate-500 text-sm leading-loose uppercase tracking-wider">
                    <?= htmlspecialchars($footerDescription ?: 'Premium performance agency for the world\'s most ambitious direct-to-consumer brands.') ?>
                </p>
            </div>
            
            <?php
                // Fetch dynamic footer navigation from database grouped by footer_group
                try {
                    require_once __DIR__ . '/../../config/database.php';
                    $pdo = Database::getInstance();
                    $footerItems = $pdo->query('SELECT label, url, footer_group FROM navigation WHERE is_footer = 1 ORDER BY position ASC')->fetchAll();
                    
                    if (!empty($footerItems)) {
                        // Group items by footer_group
                        $groupedItems = [];
                        foreach ($footerItems as $item) {
                            $group = $item['footer_group'] ?? 'Other';
                            if (!isset($groupedItems[$group])) {
                                $groupedItems[$group] = [];
                            }
                            $groupedItems[$group][] = $item;
                        }
                        
                        // Display exactly 4 groups (fill with empty columns if needed)
                        $groupNames = array_keys($groupedItems);
                        $groupsToShow = [];
                        for ($i = 0; $i < 4; $i++) {
                            if (isset($groupNames[$i])) {
                                $groupsToShow[] = $groupNames[$i];
                            } else {
                                $groupsToShow[] = null;
                            }
                        }
                        foreach ($groupsToShow as $groupName) {
                            if ($groupName !== null) {
                                $items = $groupedItems[$groupName];
                                echo '<div>';
                                echo '<h6 class="text-[10px] uppercase tracking-[0.3em] text-white/40 mb-6 sm:mb-8 font-bold">' . htmlspecialchars($groupName) . '</h6>';
                                echo '<ul class="text-sm space-y-3 sm:space-y-4">';
                                foreach ($items as $item) {
                                    $url = trim($item['url']);
                                    $isExternal = strpos($url, 'http') === 0 || strpos($url, '//') === 0;
                                    $isAnchor = strpos($url, '#') === 0;
                                    if (!$isExternal && !$isAnchor) {
                                        if (strpos($url, '/') === 0) {
                                            $url = $appUrl . $url;
                                        } else {
                                            $url = $appUrl . '/' . ltrim($url, '/');
                                        }
                                    }
                                    $target = $isExternal ? ' target="_blank"' : '';
                                    echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="' . htmlspecialchars($url) . '"' . $target . '>' . htmlspecialchars($item['label']) . '</a></li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                            } else {
                                // Empty column for layout
                                echo '<div></div>';
                            }
                        }
                    } else {
                        // Fallback footer links
                        echo '<div>';
                        echo '<h6 class="text-[10px] uppercase tracking-[0.3em] text-white/40 mb-6 sm:mb-8 font-bold">Inquiries</h6>';
                        echo '<ul class="text-sm space-y-3 sm:space-y-4">';
                        echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">growth@digifyce.com</a></li>';
                        echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">Careers</a></li>';
                        echo '</ul>';
                        echo '</div>';
                        echo '<div>';
                        echo '<h6 class="text-[10px] uppercase tracking-[0.3em] text-white/40 mb-6 sm:mb-8 font-bold">Network</h6>';
                        echo '<ul class="text-sm space-y-3 sm:space-y-4">';
                        echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">LinkedIn</a></li>';
                        echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">Twitter / X</a></li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    // Fallback footer links on error
                    echo '<div>';
                    echo '<h6 class="text-[10px] uppercase tracking-[0.3em] text-white/40 mb-6 sm:mb-8 font-bold">Inquiries</h6>';
                    echo '<ul class="text-sm space-y-3 sm:space-y-4">';
                    echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">growth@digifyce.com</a></li>';
                    echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">Careers</a></li>';
                    echo '</ul>';
                    echo '</div>';
                    echo '<div>';
                    echo '<h6 class="text-[10px] uppercase tracking-[0.3em] text-white/40 mb-6 sm:mb-8 font-bold">Network</h6>';
                    echo '<ul class="text-sm space-y-3 sm:space-y-4">';
                    echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">LinkedIn</a></li>';
                    echo '<li><a class="hover:text-[var(--electric-blue)] transition-colors" href="#">Twitter / X</a></li>';
                    echo '</ul>';
                    echo '</div>';
                }
            ?>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 pt-6 sm:pt-8 border-t border-white/5 text-[10px] uppercase tracking-[0.2em] text-slate-700">
            <div class="text-center sm:text-left"><?= htmlspecialchars($footerCopyright) ?></div>
            <?php if (!empty($footerPages)): ?>
                <div class="flex flex-wrap justify-center sm:justify-end gap-4 sm:gap-8">
                    <?php foreach ($footerPages as $page): ?>
                        <a href="<?= $appUrl ?>/pages/<?= htmlspecialchars($page['slug']) ?>">
                            <?= htmlspecialchars($page['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>
</body>
</html>
