<?php
// metrics_save.php: Save edited metrics from admin form
require_once __DIR__ . '/../../db.php';
if (!empty($_POST['ids'])) {
    foreach ($_POST['ids'] as $i => $id) {
        $label = $_POST['labels'][$i];
        $value = $_POST['values'][$i];
        $desc = $_POST['descriptions'][$i];
        $stmt = $mysqli->prepare("UPDATE metrics SET label=?, value=?, description=? WHERE id=?");
        $stmt->bind_param('sssi', $label, $value, $desc, $id);
        $stmt->execute();
        $stmt->close();
    }
    echo 'Metrics updated.';
} else {
    echo 'No metrics to update.';
}
?>
