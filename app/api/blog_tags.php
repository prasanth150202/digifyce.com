<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();

$stmt = $pdo->query('SELECT id, name, slug FROM blog_tags ORDER BY name');
echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
