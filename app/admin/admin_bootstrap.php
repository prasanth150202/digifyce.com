<?php
$dotenv = __DIR__ . '/../../.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
    }
}
require_once __DIR__ . '/../utilities/AppUrl.php';
$appUrl = AppUrl::resolve();

function admin_login_url(): string {
    return AppUrl::resolve() . '/app/admin/login.php';
}
