<?php
// hero_save.php: Save or update hero section content in the database
require_once __DIR__ . '/../../db.php';

$headline = $_POST['headline'] ?? '';
$subtext = $_POST['subtext'] ?? '';
$cta_label = $_POST['cta_label'] ?? '';
$cta_url = $_POST['cta_url'] ?? '';

// Simple upsert (replace all for now)
if ($headline) {
    $stmt = $mysqli->prepare("REPLACE INTO hero_section (id, headline, subtext, cta_label, cta_url) VALUES (1, ?, ?, ?, ?)");
    $stmt->bind_param('ssss', $headline, $subtext, $cta_label, $cta_url);
    $stmt->execute();
    $stmt->close();
    echo 'Hero section saved.';
} else {
    echo 'Headline is required.';
}
?>
