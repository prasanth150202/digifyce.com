<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$cases = $pdo->query('SELECT title, description, image_url, metrics_json, position FROM case_studies ORDER BY position')->fetchAll();
echo json_encode(['success'=>true,'data'=>$cases]);
