<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$tools = $pdo->query('SELECT name, logo_url, position FROM tools_tech ORDER BY position')->fetchAll();
echo json_encode(['success'=>true,'data'=>$tools]);
