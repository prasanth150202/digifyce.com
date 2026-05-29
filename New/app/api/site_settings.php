<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$settings = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
$data = [];
foreach ($settings as $row) {
    $data[$row['setting_key']] = $row['setting_value'];
}
echo json_encode(['success'=>true,'data'=>$data]);
