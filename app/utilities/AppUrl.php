<?php
// Resolves the application's base URL for the current request.
//
// .env's APP_URL is a fixed value (usually http://localhost/digifyce), so
// any time the same codebase is reached through a different host — a LAN
// IP, an ngrok tunnel used for a demo, a staging domain — every stylesheet,
// script, image and canonical link built from that hardcoded value points
// at a host the visitor's own browser can't reach (that's why a shared
// ngrok link showed up unstyled: the CSS href was still "localhost").
//
// Fix: take the scheme + host from the actual inbound request (honoring
// X-Forwarded-Proto, which ngrok and most reverse proxies set) and only
// borrow the path portion (e.g. "/digifyce") from .env, since that reflects
// where the app is installed on disk and isn't something the request can
// tell us. Falls back to the raw .env value when there's no request
// context at all (CLI scripts, cron jobs) or when .env hasn't been loaded
// into $_ENV by the caller yet.
class AppUrl
{
    public static function resolve(): string
    {
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
}
