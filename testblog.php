<?php
require_once __DIR__ . '/config/database.php';

function clean_blog_output($html) {

    // Normalize spaces
    $html = preg_replace('/\s+/', ' ', $html);

    // Remove class, style, data-* attributes (ALL, not just first match)
    $html = preg_replace('/\s*(class|style|data-[^=]*)="[^"]*"/i', '', $html);

    // Remove MS Office tags + wrappers
    $html = preg_replace('/<\/?(span|div|font|o:p)[^>]*>/i', '', $html);

    // Fix repeated UL/OL issues
    $html = preg_replace('/<\/(ul|ol)>\s*<\1>/i', '', $html);

    // Remove empty tags
    $html = preg_replace('/<(\w+)>\s*<\/\1>/', '', $html);

    // Allow only safe tags (VERY IMPORTANT)
    $allowed = '<p><br><strong><b><em><i><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img>';
    $html = strip_tags($html, $allowed);

    return trim($html);
}


// 🔥 TEST INPUT (simulate your DB content)
$dirty_html = file_get_contents(__DIR__ . '/Pasted text.txt');

// Clean it
$clean_html = clean_blog_output($dirty_html);

// Output
echo "<h2>Cleaned Output:</h2>";
echo '<div style="padding:20px;border:1px solid #ccc;">';
echo $clean_html;
echo '</div>';