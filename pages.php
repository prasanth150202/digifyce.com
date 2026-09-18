<?php
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path) {
        $pos = strpos($path, '/pages/');
        if ($pos !== false) {
            $slug = trim(substr($path, $pos + 7), '/');
        }
    }
}

$slug = strtolower(trim((string) $slug));

if (!$slug) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

$pdo = Database::getInstance();
$stmt = $pdo->prepare('SELECT id, title, content, meta_title, meta_description FROM pages WHERE slug = ?');
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

$appUrl = getenv('APP_URL') ?: 'http://localhost/digifyce2';
$pageTitle = $page['meta_title'] ?: ($page['title'] . ' | Digifyce');
$pageDescription = $page['meta_description'] ?: (substr(strip_tags($page['content']), 0, 160));
$bodyClass = 'bg-background-dark font-display text-white';

$extraHead = '';

include __DIR__ . '/app/views/header.php';
?>

<style>
.page-content { color: #cbd5e1; line-height: 1.8; font-size: 1rem; }
.page-content h1,.page-content h2,.page-content h3,.page-content h4 {
    color: #f1f5f9; font-weight: 700; margin-top: 2rem; margin-bottom: 0.75rem; line-height: 1.3;
}
.page-content h1 { font-size: 2rem; }
.page-content h2 { font-size: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem; }
.page-content h3 { font-size: 1.2rem; color: #94a3b8; }
.page-content p  { margin-bottom: 1.1rem; }
.page-content ul,.page-content ol { padding-left: 1.5rem; margin-bottom: 1.1rem; }
.page-content li { margin-bottom: 0.4rem; }
.page-content a  { color: #0d69f2; text-decoration: underline; }
.page-content strong,.page-content b { color: #f1f5f9; font-weight: 600; }
.page-content hr { border-color: rgba(255,255,255,0.1); margin: 2rem 0; }
.page-content blockquote { border-left: 3px solid #0d69f2; padding-left: 1rem; color: #94a3b8; margin: 1.5rem 0; }
</style>

<main class="min-h-screen py-32 px-4 sm:px-6 lg:px-8 bg-background-dark">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tighter mb-10 text-white border-b border-white/10 pb-6">
            <?= htmlspecialchars($page['title']) ?>
        </h1>
        <div class="page-content">
            <?php
            $content = $page['content'];
            // If no HTML tags present, content is plain text/markdown — convert to HTML
            if (strip_tags($content) === $content) {
                // Normalize Windows line endings
                $content = str_replace("\r\n", "\n", $content);
                $content = str_replace("\r", "\n", $content);
                // Markdown headings
                $content = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $content);
                $content = preg_replace('/^## (.+)$/m',  '<h2>$1</h2>', $content);
                $content = preg_replace('/^# (.+)$/m',   '<h2>$1</h2>', $content);
                // Numbered section headings: "1. Heading Text" on its own line
                $content = preg_replace('/^(\d+\.\s+[A-Z][^\n]{3,60})$/m', '<h2>$1</h2>', $content);
                // Markdown links [text](url)
                $content = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $content);
                // Bold **text**
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                // Bullet lines starting with * or -
                $content = preg_replace('/^[\*\-] (.+)$/m', '<li>$1</li>', $content);
                // Wrap consecutive <li> in <ul>
                $content = preg_replace('/(<li>(?:.|\n)*?<\/li>)/s', '<ul>$0</ul>', $content);
                $content = preg_replace('/<\/ul>\s*<ul>/', '', $content);
                // Split on double newlines into paragraphs
                $paragraphs = preg_split('/\n{2,}/', trim($content));
                $content = '';
                foreach ($paragraphs as $para) {
                    $para = trim($para);
                    if (!$para) continue;
                    if (preg_match('/^<(h[1-6]|ul|ol)/', $para)) {
                        $content .= $para . "\n";
                    } else {
                        // Single newlines within a paragraph become spaces
                        $content .= '<p>' . preg_replace('/\n/', ' ', $para) . '</p>' . "\n";
                    }
                }
            }
            echo $content;
            ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/app/views/footer.php'; ?>
