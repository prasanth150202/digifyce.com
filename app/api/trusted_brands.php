<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$brands = $pdo->query('SELECT name, logo_url, position FROM trusted_brands ORDER BY position')->fetchAll();
echo json_encode(['success'=>true,'data'=>$brands]);
