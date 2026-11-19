<?php
// Appends a lead row to a Google Sheet through a Google Apps Script web app
// (see tools/google-sheets/d2c-leads.gs for the script and setup steps).
//
// .env settings:
//   D2C_LEADS_SHEET_WEBHOOK  the Apps Script web app URL (ends in /exec)
//   D2C_LEADS_SHEET_TOKEN    shared secret; must match SECRET_TOKEN in the script
//
// The URL and token stay server side, so visitors can't post to the sheet directly.
class GoogleSheetLead
{
    private static $dotenvLoaded = false;

    public static function env(string $key): ?string
    {
        if (!isset($_ENV[$key])) {
            self::loadDotenv();
        }
        $value = $_ENV[$key] ?? getenv($key);
        return ($value === false || $value === '') ? null : (string) $value;
    }

    // Returns true when the script confirms the row was written.
    public static function send(array $row): bool
    {
        $webhook = self::env('D2C_LEADS_SHEET_WEBHOOK');
        if ($webhook === null) {
            error_log('GoogleSheetLead: D2C_LEADS_SHEET_WEBHOOK is not set in .env');
            return false;
        }

        $row['token'] = (string) self::env('D2C_LEADS_SHEET_TOKEN');

        // Apps Script answers a POST with a 302 to googleusercontent.com; curl
        // follows it as a GET, which is how Google returns the script's output.
        $ch = curl_init($webhook);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($row),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $data = is_string($response) ? json_decode($response, true) : null;
        if ($httpCode === 200 && is_array($data) && ($data['ok'] ?? false) === true) {
            return true;
        }

        error_log(sprintf(
            'GoogleSheetLead: write failed (HTTP %d%s): %s',
            $httpCode,
            $curlError ? ", cURL: $curlError" : '',
            substr((string) $response, 0, 300)
        ));
        return false;
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
        foreach (file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
            list($key, $value) = array_map('trim', explode('=', $line, 2));
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }
}
