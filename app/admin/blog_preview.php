<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$id  = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: blogs.php'); exit; }

// Load blog by ID regardless of status
$stmt = $pdo->prepare('
    SELECT b.*, a.name as author_name, a.avatar_url as author_avatar, a.bio as author_bio,
           c.name as category_name, c.slug as category_slug
    FROM blogs b
    LEFT JOIN blog_authors a ON b.author_id = a.id
    LEFT JOIN blog_categories c ON b.category_id = c.id
    WHERE b.id = ?
');
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$blog) { header('Location: blogs.php'); exit; }

// Tags
$tagsStmt = $pdo->prepare('SELECT t.name, t.slug FROM blog_tag_map m JOIN blog_tags t ON m.tag_id = t.id WHERE m.blog_id = ?');
$tagsStmt->execute([$id]);
$blog['tags'] = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

// Env / appUrl (blog view uses these)
$envFile = __DIR__ . '/../../.env';
$appUrl  = 'http://localhost/digifyce';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $_ENV[$k] = $v;
            if ($k === 'APP_URL') $appUrl = rtrim($v, '/');
        }
    }
}

// Variables the blog view template expects
$publishedDate = !empty($blog['published_at']) ? date('M d, Y', strtotime($blog['published_at'])) : date('M d, Y');
$estimatedRead = max(1, ceil(str_word_count(strip_tags($blog['content'])) / 200));
$pageTitle     = $blog['meta_title'] ?: ($blog['title'] . ' | Preview');
$pageDescription = $blog['meta_description'] ?: substr(strip_tags($blog['excerpt'] ?? $blog['content']), 0, 160);

// Related post (blog view uses $nextBlog)
$nextBlog = null;
if (!empty($blog['category_id'])) {
    $r = $pdo->prepare('SELECT id, title, slug, featured_image FROM blogs WHERE category_id=? AND id!=? AND status="published" ORDER BY published_at DESC LIMIT 1');
    $r->execute([$blog['category_id'], $id]);
    $nextBlog = $r->fetch(PDO::FETCH_ASSOC);
}

// Preview bar HTML (injected after <body> via output buffering)
$isDraft     = $blog['status'] !== 'published';
$barBorder   = $isDraft ? '#f59e0b' : '#22c55e';
$statusLabel = match($blog['status']) { 'published' => 'Published', 'scheduled' => 'Scheduled', default => 'Draft Preview' };
$publishBtn  = $isDraft
    ? '<a href="blog_publish?id=' . $id . '&status=published" style="padding:7px 20px;background:#0066ff;color:#fff;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none" onclick="return confirm(\'Publish this post?\')">Publish</a>'
    : '<a href="blog_publish?id=' . $id . '&status=draft" style="padding:7px 20px;background:#374151;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none" onclick="return confirm(\'Move back to draft?\')">Unpublish</a>';

$previewBar = '
<div id="blogPreviewBar" style="position:fixed;top:0;left:0;right:0;z-index:99999;
    background:#111;border-bottom:2px solid ' . $barBorder . ';
    padding:0 20px;height:52px;display:flex;align-items:center;justify-content:space-between;
    font-family:\'Space Grotesk\',sans-serif;box-shadow:0 2px 16px rgba(0,0,0,.6)">
    <div style="display:flex;align-items:center;gap:12px;min-width:0">
        <span style="background:' . $barBorder . ';color:#000;padding:3px 12px;border-radius:4px;
            font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;flex-shrink:0">' . htmlspecialchars($statusLabel) . '</span>
        <span style="color:rgba(255,255,255,.8);font-size:14px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'
            . htmlspecialchars($blog['title']) . '</span>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-shrink:0">
        ' . $publishBtn . '
        <a href="blog_edit.php?id=' . $id . '"
           style="padding:7px 18px;background:rgba(255,255,255,.1);color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none">
           ← Edit
        </a>
        <button onclick="document.getElementById(\'blogPreviewBar\').style.display=\'none\';document.body.style.paddingTop=\'0\'"
            style="width:32px;height:32px;background:rgba(255,255,255,.08);border:none;border-radius:6px;color:#aaa;cursor:pointer;font-size:16px">
            ×
        </button>
    </div>
</div>
<div style="height:52px"></div>';  // spacer

// Buffer the full blog view output, then inject the bar right after <body ...>
ob_start();
include __DIR__ . '/../../app/views/blog.php';
$html = ob_get_clean();

// Inject preview bar immediately after opening <body tag
echo preg_replace('/(<body[^>]*>)/i', '$1' . $previewBar, $html, 1);
