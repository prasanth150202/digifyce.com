<?php
// One-shot script: fix blog content where PDF buttons have href="http://localhost/pdf-download..."
// (created before the APP_URL fix). Replaces with the correct /digifyce/ path.
// DELETE this file after running it.

require_once __DIR__ . '/config/database.php';

$envFile = __DIR__ . '/.env';
$appUrl = 'http://localhost/digifyce';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        if ($k === 'APP_URL') { $appUrl = rtrim($v, '/'); break; }
    }
}

$pdo = Database::getInstance();

// Find all blogs with content containing the bad URL
$rows = $pdo->query("SELECT id, title, content FROM blogs WHERE content LIKE '%http://localhost/pdf-download%'")->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>Found " . count($rows) . " blog(s) with wrong PDF URLs.\n\n";

$updated = 0;
foreach ($rows as $row) {
    $fixed = str_replace(
        'http://localhost/pdf-download',
        $appUrl . '/pdf-download',
        $row['content']
    );
    if ($fixed !== $row['content']) {
        $stmt = $pdo->prepare("UPDATE blogs SET content = ? WHERE id = ?");
        $stmt->execute([$fixed, $row['id']]);
        echo "Fixed: [{$row['id']}] {$row['title']}\n";
        $updated++;
    }
}

echo "\nDone. Updated $updated blog(s).\n";
echo "\nDelete this file now: unlink('" . __FILE__ . "') or delete fix_pdf_urls.php manually.\n";
echo "</pre>";
