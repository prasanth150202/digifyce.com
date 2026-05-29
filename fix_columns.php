<?php
require_once __DIR__ . '/config/database.php';
$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fixes = [
    // Drop and recreate service_hero with correct columns
    "DROP TABLE IF EXISTS service_hero",
    "CREATE TABLE service_hero (
      id INT AUTO_INCREMENT PRIMARY KEY,
      badge_text VARCHAR(255) DEFAULT '',
      headline_main VARCHAR(500) DEFAULT '',
      headline_accent VARCHAR(255) DEFAULT '',
      sub_description TEXT,
      btn1_label VARCHAR(100) DEFAULT '',
      btn1_url VARCHAR(500) DEFAULT '',
      btn2_label VARCHAR(100) DEFAULT '',
      btn2_url VARCHAR(500) DEFAULT ''
    )",
    "INSERT INTO service_hero (id, badge_text, headline_main, headline_accent, sub_description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
    (1,'Full-Service Digital Agency','We Build Brands That','Grow Online','End-to-end digital solutions — from brand identity to performance marketing — designed to drive measurable growth for your business.','Explore Services','#services','Talk to Us','leadform.php')",

    // Drop and recreate d2c_cta with correct columns
    "DROP TABLE IF EXISTS d2c_cta",
    "CREATE TABLE d2c_cta (
      id INT AUTO_INCREMENT PRIMARY KEY,
      heading TEXT,
      subtext TEXT,
      btn_label VARCHAR(100) DEFAULT '',
      btn_url VARCHAR(500) DEFAULT ''
    )",
    "INSERT INTO d2c_cta (id, heading, subtext, btn_label, btn_url) VALUES
    (1,'Ready to Build a D2C Brand That Grows?','Let us help you build a brand that connects with customers and drives direct revenue.','Start Your D2C Journey','leadform.php')",
];

$ok = 0; $fail = 0; $errors = [];
foreach ($fixes as $stmt) {
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        $fail++;
        $errors[] = htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Column Fix</title>
<style>body{font-family:sans-serif;max-width:600px;margin:40px auto;padding:20px;background:#f8fafc}.box{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px}.ok{color:#16a34a}.fail{color:#dc2626}a{color:#0066ff}</style>
</head>
<body>
<div class="box">
  <h2>Column Fix Complete</h2>
  <p class="ok">✔ <?= $ok ?> statements executed successfully.</p>
  <?php if ($fail): ?>
    <p class="fail">✘ <?= $fail ?> failed:</p>
    <pre style="background:#fef2f2;padding:12px;border-radius:6px;font-size:13px"><?= implode("\n", $errors) ?></pre>
  <?php else: ?>
    <p class="ok">All columns fixed with no errors.</p>
  <?php endif; ?>
  <hr>
  <p><a href="app/admin/dashboard.php">Go to Admin Dashboard →</a></p>
  <p style="color:#888;font-size:13px">⚠️ Delete <code>fix_columns.php</code> and <code>run_migrations.php</code> after this step.</p>
</div>
</body>
</html>
