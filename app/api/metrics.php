<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$metrics = $pdo->query('SELECT label, value, description FROM metrics')->fetchAll();
echo json_encode(['success'=>true,'data'=>$metrics]);
