<?php
require_once __DIR__ . '/SsrfGuard.php';
require_once __DIR__ . '/AuditSpawner.php';

// Shared "start a background website audit for this lead, if eligible" logic.
// Used both by the early draft-save path (app/api/lead_draft_start.php) and
// leadform.php's fallback path, so the SSRF/rate-limit checks live in one
// place rather than being copy-pasted. Never throws - any skip (unsafe URL,
// rate-limited, quota exceeded) just means no audit starts; the caller's own
// success path must never depend on this succeeding.
class AuditTrigger
{
    /**
     * Returns ['token' => string|null, 'reason' => string|null]. reason is
     * null on success or plain ineligibility (no website / didn't ask for an
     * audit - nothing worth telling the lead), 'quota_exceeded' when this
     * exact URL has already been audited the max number of times (distinct
     * so the UI can show a specific "you've used your free audits for this
     * site" message instead of a generic failure), or 'unavailable' for any
     * other skip (unsafe URL, IP rate limit, concurrency limit).
     */
    public static function maybeStart(PDO $pdo, int $leadId, ?string $ipAddress, ?string $hasWebsite, ?string $wantAudit, ?string $website): array
    {
        if ($hasWebsite !== 'yes' || $wantAudit !== 'yes' || empty($website)) {
            return ['token' => null, 'reason' => null];
        }

        $safety = SsrfGuard::isUrlSafe($website);
        if (!$safety['safe']) {
            return ['token' => null, 'reason' => 'unavailable'];
        }

        $auditConfig = require __DIR__ . '/../../config/website_audit.php';

        $urlCountStmt = $pdo->prepare("SELECT COUNT(*) FROM website_audits WHERE url = ?");
        $urlCountStmt->execute([$safety['normalized_url']]);
        $urlCount = (int) $urlCountStmt->fetchColumn();

        if ($urlCount >= $auditConfig['max_audits_per_url']) {
            return ['token' => null, 'reason' => 'quota_exceeded'];
        }

        $dayCountStmt = $pdo->prepare("SELECT COUNT(*) FROM website_audits WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 DAY)");
        $dayCountStmt->execute([$ipAddress]);
        $dayCount = (int) $dayCountStmt->fetchColumn();

        $concurrentCount = (int) $pdo->query("SELECT COUNT(*) FROM website_audits WHERE status IN ('pending','running')")->fetchColumn();

        if ($dayCount >= $auditConfig['max_audits_per_ip_per_day'] || $concurrentCount >= $auditConfig['max_concurrent_audits']) {
            return ['token' => null, 'reason' => 'unavailable'];
        }

        $token = bin2hex(random_bytes(16));
        $insertAudit = $pdo->prepare("INSERT INTO website_audits (lead_id, url, token, ip_address, status) VALUES (?, ?, ?, ?, 'pending')");
        $insertAudit->execute([$leadId, $safety['normalized_url'], $token, $ipAddress]);
        AuditSpawner::spawn((int) $pdo->lastInsertId());

        return ['token' => $token, 'reason' => null];
    }
}
