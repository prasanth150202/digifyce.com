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

if (!$slug) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

$pdo = Database::getInstance();
$stmt = $pdo->prepare('SELECT id, title, content FROM pages WHERE slug = ?');
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

$appUrl = getenv('APP_URL') ?: 'http://localhost/digifyce2';
$pageTitle = ($page['title'] ?? 'Page') . ' | Digifyce';
$bodyClass = 'bg-background-dark font-display text-white';

$tailwindConfig = '<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#0d69f2",
                    "background-light": "#f5f7f8",
                    "background-dark": "#05070a",
                },
                fontFamily: {
                    "display": ["Space Grotesk", "sans-serif"]
                },
            },
        },
    }
</script>';

$extraHead = '';

include __DIR__ . '/app/views/header.php';
?>

<main class="min-h-screen py-32 px-4 sm:px-6 lg:px-8 bg-background-dark">
    <div class="max-w-4xl mx-auto">
        <article class="prose prose-invert max-w-none">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tighter mb-8 text-white">
                <?= htmlspecialchars($page['title']) ?>
            </h1>
            
            <div class="prose prose-invert max-w-none mt-8 text-slate-300 leading-relaxed">
                <?= $page['content'] ?>
            </div>
        </article>
    </div>
</main>

<?php include __DIR__ . '/app/views/footer.php'; ?>
