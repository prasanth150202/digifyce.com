<?php
/**
 * One-time migration: removes the ON UPDATE CURRENT_TIMESTAMP clause from
 * blogs.updated_at. That clause was silently re-triggering on every single
 * page view because blog.php's harmless view_count increment (UPDATE blogs
 * SET view_count=view_count+1) touches the row, making every post's schema
 * dateModified report "just now" regardless of whether it was ever actually
 * edited. blog_save.php already sets updated_at=NOW() explicitly on real
 * edits (line 98), so it never relied on this column behavior to begin with,
 * dropping it only stops the false touches, real edits are unaffected.
 * Safe to run more than once. Delete this file after running.
 */
require_once __DIR__ . '/config/database.php';

$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ok = 0; $errors = [];

try {
    $pdo->exec("ALTER TABLE blogs MODIFY updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    $ok++;
} catch (PDOException $e) {
    $errors[] = htmlspecialchars('ALTER TABLE blogs MODIFY updated_at -> ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Blog updated_at Fix</title>
<style>
  body { font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 20px; background: #f8fafc; }
  .ok { color: #16a34a; } .fail { color: #dc2626; }
  .box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; }
  pre { background: #fef2f2; border-radius: 6px; padding: 12px; font-size: 13px; overflow-x: auto; }
</style>
</head>
<body>
<div class="box">
  <h2>Blog updated_at Fix</h2>
  <?php if ($errors): ?>
    <p class="fail">✘ Migration failed:</p>
    <pre><?= implode("\n", $errors) ?></pre>
  <?php else: ?>
    <p class="ok">✔ blogs.updated_at no longer auto-touches on every UPDATE.</p>
    <p>Blog post dateModified will now only change when a post is actually edited and saved through the admin panel, not on every page view.</p>
  <?php endif; ?>
  <hr>
  <p style="color:#888;font-size:13px;">⚠️ Delete <code>migrate_blog_updated_at_fix.php</code> from your server after this step.</p>
</div>
</body>
</html>
