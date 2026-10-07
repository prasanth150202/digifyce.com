<?php
// Shared configuration for the website-audit background job.
// Included via: $auditConfig = require __DIR__ . '/../config/website_audit.php';
//
// The audit itself runs on the external Python service (tools/website_audit,
// deployed on Render) - see AuditSpawner. Its URL and the shared signing
// secret come from .env: AUDIT_SERVICE_URL and AUDIT_SHARED_SECRET.

return [
    // Kept short: dispatch happens inside the lead's own request, and the
    // service replies the moment it has queued the job. A timeout (e.g. the
    // service still waking from Render's free-tier sleep) just leaves the
    // row 'pending' for the status endpoint's self-heal to re-dispatch.
    'service_connect_timeout_seconds' => 10,
    'service_timeout_seconds' => 20,
    'log_dir' => __DIR__ . '/../storage/logs/website_audits',
    // How many case study documents (in admin order) the service may draw
    // on for the AI's case-study matching.
    'max_case_studies' => 20,
    'max_audits_per_ip_per_day' => 3,
    'max_concurrent_audits' => 5,
    // Same exact URL can only be audited this many times total (any lead,
    // any IP) - a 3rd attempt on an already-audited site is quota-blocked
    // with a distinct message instead of silently re-running the crawl+AI
    // pipeline again.
    'max_audits_per_url' => 2,
    'respawn_threshold_seconds' => 20,
    // Re-dispatch attempts after the first. Each waits at least
    // respawn_threshold_seconds, so 4 gives a sleeping free-tier service
    // well over a minute to wake before the audit is given up on.
    'max_attempts' => 4,
    // A row stuck in 'running' this long is presumed lost (service
    // restarted/redeployed mid-job) and is force-failed so the lead's UI
    // never spins forever. Counted from the service's 'running' callback,
    // i.e. from when the job actually started. Raw worst case of the job
    // itself: 240s crawl (spider.py covers up to 60 pages for genuinely
    // whole-site coverage) + 30s screenshot + all three sequential OpenAI
    // calls (plan/execute/recommended-implementations) each bounded at
    // 2x their own timeout by the SDK's max_retries=1, AND EACH retried
    // once more at the application level in ai_analyzer.py's
    // _chat_json_with_retry = up to 2x2x45s (plan) + 2x2x120s (execute) +
    // 2x2x75s (recommended-implementations) = 960s AI budget, for a 1230s
    // raw worst case. The service kills a job at 1260s (api.py) and reports
    // it failed, so this buffer only fires when that report never arrives.
    'max_running_seconds' => 1350,
];
