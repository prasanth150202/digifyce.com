<?php
// HMAC signing for every request between this site and the external
// website-audit service (tools/website_audit, deployed on Render): job
// dispatch (AuditSpawner), result callbacks (app/api/website_audit_callback.php)
// and case study downloads (app/api/audit_case_study_file.php). Mirrors
// tools/website_audit/signing.py - the shared secret (.env
// AUDIT_SHARED_SECRET) never travels over the wire, and the timestamp bounds
// how long a captured request could be replayed.
class AuditSignature
{
    const MAX_CLOCK_SKEW_SECONDS = 300;

    private static $dotenvLoaded = false;

    // Reads a setting from .env (or the real environment), null when unset.
    public static function env(string $key): ?string
    {
        if (!isset($_ENV[$key])) {
            self::loadDotenv();
        }
        $value = $_ENV[$key] ?? getenv($key);
        return ($value === false || $value === '') ? null : (string) $value;
    }

    // cURL-ready headers signing $body.
    public static function headers(string $body): array
    {
        $timestamp = (string) time();
        return [
            'X-Audit-Timestamp: ' . $timestamp,
            'X-Audit-Signature: ' . self::sign($timestamp, $body),
        ];
    }

    // Verifies the current request's signature headers against its raw body.
    public static function verifyRequest(string $body): bool
    {
        $timestamp = $_SERVER['HTTP_X_AUDIT_TIMESTAMP'] ?? '';
        $signature = $_SERVER['HTTP_X_AUDIT_SIGNATURE'] ?? '';

        if (self::env('AUDIT_SHARED_SECRET') === null || !ctype_digit($timestamp) || $signature === '') {
            return false;
        }
        if (abs(time() - (int) $timestamp) > self::MAX_CLOCK_SKEW_SECONDS) {
            return false;
        }
        return hash_equals(self::sign($timestamp, $body), $signature);
    }

    private static function sign(string $timestamp, string $body): string
    {
        return hash_hmac('sha256', $timestamp . '.' . $body, (string) self::env('AUDIT_SHARED_SECRET'));
    }

    private static function loadDotenv(): void
    {
        if (self::$dotenvLoaded) {
            return;
        }
        self::$dotenvLoaded = true;

        $dotenv = __DIR__ . '/../../.env';
        if (!file_exists($dotenv)) {
            return;
        }
        $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($key, $value) = array_map('trim', explode('=', $line, 2));
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }
}
