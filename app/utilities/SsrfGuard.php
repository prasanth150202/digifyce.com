<?php
// Validates that a user-submitted URL is safe to fetch from the server
// (blocks non-http(s) schemes, embedded credentials, and any hostname that
// resolves to a private/loopback/link-local/reserved IP address).
class SsrfGuard
{
    public static function isUrlSafe(string $url): array
    {
        $url = trim($url);
        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return ['safe' => false, 'reason' => 'Malformed URL', 'normalized_url' => null];
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return ['safe' => false, 'reason' => 'Unsupported URL scheme', 'normalized_url' => null];
        }

        if (!empty($parts['user']) || !empty($parts['pass'])) {
            return ['safe' => false, 'reason' => 'URLs with embedded credentials are not allowed', 'normalized_url' => null];
        }

        $host = $parts['host'];

        $ips = [];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips[] = $host;
        } else {
            $records = @dns_get_record($host, DNS_A + DNS_AAAA);
            if (is_array($records)) {
                foreach ($records as $record) {
                    if (!empty($record['ip'])) {
                        $ips[] = $record['ip'];
                    }
                    if (!empty($record['ipv6'])) {
                        $ips[] = $record['ipv6'];
                    }
                }
            }
            if (empty($ips)) {
                $fallback = @gethostbynamel($host);
                if (is_array($fallback)) {
                    $ips = array_merge($ips, $fallback);
                }
            }
        }

        if (empty($ips)) {
            return ['safe' => false, 'reason' => 'Could not resolve host', 'normalized_url' => null];
        }

        foreach ($ips as $ip) {
            $public = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            if ($public === false) {
                return ['safe' => false, 'reason' => 'Host resolves to a private or reserved IP address', 'normalized_url' => null];
            }
        }

        return ['safe' => true, 'reason' => null, 'normalized_url' => $url];
    }
}
