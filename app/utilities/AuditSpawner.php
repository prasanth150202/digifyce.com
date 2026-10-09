<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuditSignature.php';

// Hands a website audit to the external audit service (tools/website_audit,
// deployed on Render) - Scrapy, Chromium and OpenAI run there, not on this
// server. The job carries everything the service needs (URL, lead context,
// case study list) so it never touches MySQL; it reports progress and the
// final result back through app/api/website_audit_callback.php.
//
// The service replies as soon as it has queued the job, and the row is then
// marked 'running'. On any failure the row stays 'pending', so
// website_audit_status.php's self-heal re-dispatches it - which also covers
// the service still waking up from Render's free-tier sleep. Used from
// leadform.php (initial trigger), the public status endpoint (self-heal
// respawn), and the admin retry button.
class AuditSpawner
{
    public static function spawn(int $auditId): bool
    {
        $config = require __DIR__ . '/../../config/website_audit.php';

        try {
            $serviceUrl = AuditSignature::env('AUDIT_SERVICE_URL');
            if ($serviceUrl === null || AuditSignature::env('AUDIT_SHARED_SECRET') === null) {
                throw new RuntimeException('AUDIT_SERVICE_URL / AUDIT_SHARED_SECRET not set in .env');
            }

            $pdo = Database::getInstance();
            $job = self::buildJob($pdo, $auditId, $config['max_case_studies']);
            if ($job === null) {
                throw new RuntimeException('audit row not found');
            }

            $body = json_encode($job, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            if ($body === false) {
                throw new RuntimeException('could not encode job: ' . json_last_error_msg());
            }

            $ch = curl_init(rtrim($serviceUrl, '/') . '/audit');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], AuditSignature::headers($body)),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $config['service_connect_timeout_seconds'],
                CURLOPT_TIMEOUT => $config['service_timeout_seconds'],
            ]);
            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $httpCode !== 202) {
                throw new RuntimeException("service did not accept the job (HTTP $httpCode) $curlError " . substr((string) $response, 0, 300));
            }

            // A callback can race ahead of this (the service starts instantly,
            // or even fails instantly) - only move a still-pending row.
            $pdo->prepare("UPDATE website_audits SET status = 'running', updated_at = NOW() WHERE id = ? AND status = 'pending'")
                ->execute([$auditId]);

            return true;
        } catch (Throwable $e) {
            self::log($config, "audit_id=$auditId dispatch failed: " . $e->getMessage());
            return false;
        }
    }

    // Resolves a case_studies.file_path (e.g. "storage/case_studies/x.pdf")
    // to an absolute path - only if it really is a file inside
    // storage/case_studies, never anywhere else on disk.
    public static function resolveCaseStudyFile(string $filePath): ?string
    {
        $baseDir = realpath(__DIR__ . '/../../storage/case_studies');
        $absPath = realpath(__DIR__ . '/../../' . ltrim($filePath, '/'));

        if ($baseDir === false || $absPath === false || !is_file($absPath)) {
            return null;
        }
        return strpos($absPath, $baseDir . DIRECTORY_SEPARATOR) === 0 ? $absPath : null;
    }

    private static function buildJob(PDO $pdo, int $auditId, int $maxCaseStudies): ?array
    {
        $stmt = $pdo->prepare(
            "SELECT wa.url, wa.token, l.industry, l.has_ads, l.ad_spend, l.roas
             FROM website_audits wa
             JOIN lead_form_submissions l ON l.id = wa.lead_id
             WHERE wa.id = ?"
        );
        $stmt->execute([$auditId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $runsAds = $row['has_ads'] === 'yes';

        return [
            'audit_id' => $auditId,
            'token' => $row['token'],
            'url' => $row['url'],
            'lead_context' => [
                'industry' => $row['industry'] ?: null,
                'runs_ads' => $runsAds,
                'ad_spend' => $runsAds ? $row['ad_spend'] : null,
                'roas' => $runsAds ? $row['roas'] : null,
            ],
            'case_studies' => self::caseStudies($pdo, $maxCaseStudies),
        ];
    }

    // The service downloads each listed file from
    // app/api/audit_case_study_file.php. "version" changes whenever the file
    // does, so its extracted-text cache picks up re-uploads.
    private static function caseStudies(PDO $pdo, int $limit): array
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, title, description, file_path FROM case_studies
                 WHERE file_path IS NOT NULL ORDER BY position ASC LIMIT ?"
            );
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table missing in some environment - the library is supplementary
            // context, never a reason to block the audit.
            return [];
        }

        $caseStudies = [];
        foreach ($rows as $row) {
            $absPath = self::resolveCaseStudyFile((string) $row['file_path']);
            if ($absPath === null) {
                continue;
            }
            $caseStudies[] = [
                'id' => (int) $row['id'],
                'title' => $row['title'],
                'description' => $row['description'] ?? '',
                'file_name' => basename($absPath),
                'version' => filemtime($absPath) . '-' . filesize($absPath),
            ];
        }
        return $caseStudies;
    }

    private static function log(array $config, string $message): void
    {
        if (!is_dir($config['log_dir'])) {
            @mkdir($config['log_dir'], 0755, true);
        }
        @file_put_contents($config['log_dir'] . '/dispatch.log', date('c') . ' ' . $message . PHP_EOL, FILE_APPEND);
    }
}
