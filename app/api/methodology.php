<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$steps = $pdo->query('SELECT step_number, title, description FROM methodology_steps ORDER BY step_number')->fetchAll();
echo json_encode(['success'=>true,'data'=>$steps]);
