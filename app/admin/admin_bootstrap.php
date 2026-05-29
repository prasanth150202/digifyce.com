<?php
$dotenv = __DIR__ . '/../../.env';
if (!isset($_ENV['APP_URL']) && file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
    }
}
$appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');

function admin_login_url(): string {
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');
    return $base . '/app/admin/login.php';
}
