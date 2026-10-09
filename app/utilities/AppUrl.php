<?php
// Resolves the application's base URL for the current request.
//
// .env's APP_URL used to be trusted verbatim as "the" app URL, but it only
// ever holds whatever host was true on the machine that last edited .env
// (usually http://localhost/digifyce). Any time the same codebase is
// reached through a different host - a LAN IP, an ngrok tunnel used for
// testing, a staging domain - every redirect, JS polling URL, and report/
// screenshot link built from that hardcoded value points at a host the
// visitor's own browser can't reach.
//
// Fix: take the scheme + host from the actual inbound request (honoring
// X-Forwarded-Proto/Host, which ngrok and other reverse proxies set) and
// only borrow the path portion (e.g. "/digifyce") from .env, since that
// reflects where the app is installed on disk and isn't something the
// request can tell us. Falls back to the raw .env value when there's no
// request context at all (CLI scripts, cron jobs).
class AppUrl
{
    public static function resolve(): string
    {
        self::loadDotenv();
        $configured = rtrim($_ENV['APP_URL'] ?? '', '/');

        if (empty($_SERVER['HTTP_HOST'])) {
            return $configured;
        }

        $path = $configured !== '' ? rtrim((string) parse_url($configured, PHP_URL_PATH), '/') : '';

        $scheme = 'http';
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $scheme = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
        } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = 'https';
        }

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $path;
    }

    private static function loadDotenv(): void
    {
        if (isset($_ENV['APP_URL'])) {
            return;
        }
        $dotenv = __DIR__ . '/../../.env';
        if (!file_exists($dotenv)) {
            return;
        }
        $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($key, $value) = array_map('trim', explode('=', $line, 2));
            $_ENV[$key] = $value;
        }
    }
}
