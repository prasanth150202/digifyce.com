<?php
// Launches the Python website-audit runner as a detached background
// process (Windows: `start /B`) so the HTTP request that triggers it
// returns immediately. Used from leadform.php (initial trigger), the
// public status endpoint (self-heal respawn), and the admin retry button.
class AuditSpawner
{
    public static function spawn(int $auditId): bool
    {
        $config = require __DIR__ . '/../../config/website_audit.php';

        if (!is_dir($config['log_dir'])) {
            mkdir($config['log_dir'], 0755, true);
        }

        // Two separate files, not one shared between stdout/stderr: on Windows,
        // pointing both descriptors at the same path silently drops whichever
        // stream's writes lose the race for the file handle (verified empirically -
        // stderr output disappeared entirely when sharing one file).
        $outLog = $config['log_dir'] . '/audit_' . $auditId . '.out.log';
        $errLog = $config['log_dir'] . '/audit_' . $auditId . '.err.log';

        $cmd = 'start "" /B '
            . escapeshellarg($config['python_exe']) . ' '
            . escapeshellarg($config['script_path']) . ' '
            . escapeshellarg((string) $auditId);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $outLog, 'a'],
            2 => ['file', $errLog, 'a'],
        ];

        $cwd = dirname($config['script_path']);
        $process = @proc_open($cmd, $descriptors, $pipes, $cwd);
        if (!is_resource($process)) {
            return false;
        }

        fclose($pipes[0]);
        proc_close($process);

        return true;
    }
}
