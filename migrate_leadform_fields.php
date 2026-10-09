<?php
/**
 * One-time migration: adds the lead-qualification columns to lead_form_submissions
 * (main_objective, business_type, industry, is_qualified) for databases created
 * before the "leadform fields added" change. Safe to run more than once —
 * each ALTER is wrapped individually and a "Duplicate column" failure just
 * means that column is already there. Delete this file after running.
 *
 * Protected by a key so it can't be triggered by anyone stumbling on the URL
 * while it's still deployed. Visit as:
 *   https://digifyce.com/run-lead-migration?key=29ab7bb2c33078df5f615706b9cd6462
 */
if (($_GET['key'] ?? '') !== '29ab7bb2c33078df5f615706b9cd6462') {
    http_response_code(403);
    die('Forbidden.');
}

require_once __DIR__ . '/config/database.php';

$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statements = [
    "ALTER TABLE lead_form_submissions ADD COLUMN main_objective VARCHAR(32) AFTER company",
    "ALTER TABLE lead_form_submissions ADD COLUMN business_type VARCHAR(32) AFTER main_objective",
    "ALTER TABLE lead_form_submissions ADD COLUMN industry VARCHAR(32) AFTER business_type",
    "ALTER TABLE lead_form_submissions ADD COLUMN is_qualified TINYINT(1) NOT NULL DEFAULT 0 AFTER budget",
];

$ok = 0; $skipped = 0; $errors = [];

foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        // Error code 1060 = Duplicate column name -> already migrated, safe to skip.
        if ($e->getCode() == '42S21' || strpos($e->getMessage(), '1060') !== false) {
            $skipped++;
        } else {
            $errors[] = htmlspecialchars($stmt . ' -> ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Lead Form Migration</title>
<style>
  body { font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 20px; background: #f8fafc; }
  .ok { color: #16a34a; } .skip { color: #64748b; } .fail { color: #dc2626; }
  .box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; }
  pre { background: #fef2f2; border-radius: 6px; padding: 12px; font-size: 13px; overflow-x: auto; }
  a { color: #0066ff; }
</style>
</head>
<body>
<div class="box">
  <h2>Lead Form Migration</h2>
  <p class="ok">✔ <?= $ok ?> column(s) added.</p>
  <p class="skip">– <?= $skipped ?> column(s) already existed (skipped).</p>
  <?php if ($errors): ?>
    <p class="fail">✘ <?= count($errors) ?> statement(s) failed:</p>
    <pre><?= implode("\n", $errors) ?></pre>
  <?php else: ?>
    <p>lead_form_submissions now matches schema.sql. Lead form submissions should work again.</p>
  <?php endif; ?>
  <hr>
  <p style="color:#888;font-size:13px;">⚠️ Delete <code>migrate_leadform_fields.php</code> from your server after this step.</p>
</div>
</body>
</html>
