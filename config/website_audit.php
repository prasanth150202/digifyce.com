<?php
// Shared configuration for the website-audit background job.
// Included via: $auditConfig = require __DIR__ . '/../config/website_audit.php';

$toolsDir = __DIR__ . '/../tools/website_audit';

return [
    // Use the console python.exe (not pythonw.exe) so stdout/stderr can
    // actually be redirected to the per-audit log file for debugging.
    'python_exe' => $toolsDir . '/venv/Scripts/python.exe',
    'script_path' => $toolsDir . '/audit_runner.py',
    'log_dir' => __DIR__ . '/../storage/logs/website_audits',
    'max_audits_per_ip_per_day' => 3,
    'max_concurrent_audits' => 5,
    // Same exact URL can only be audited this many times total (any lead,
    // any IP) - a 3rd attempt on an already-audited site is quota-blocked
    // with a distinct message instead of silently re-running the crawl+AI
    // pipeline again.
    'max_audits_per_url' => 2,
    'respawn_threshold_seconds' => 20,
    'max_attempts' => 2,
    // A row stuck in 'running' this long is presumed hung and is
    // force-failed so the lead's UI never spins forever. Raw worst case:
    // 240s crawl (spider.py now covers up to 60 pages for genuinely
    // whole-site coverage) + 30s screenshot + all three sequential OpenAI
    // calls (plan/execute/recommended-implementations) each bounded at
    // 2x their own timeout by the SDK's max_retries=1, AND EACH retried
    // once more at the application level in ai_analyzer.py's
    // _chat_json_with_retry (added after a single transient OpenAI 500
    // was observed silently collapsing an entire audit to the much
    // weaker rule-based fallback) = up to 2x2x45s (plan) + 2x2x120s
    // (execute) + 2x2x75s (recommended-implementations) = 960s AI budget,
    // for a 1230s raw worst case, buffered to 1350s.
    'max_running_seconds' => 1350,
];
