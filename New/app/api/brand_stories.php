<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$stories = $pdo->query('SELECT title, client_name, video_url, position FROM brand_stories ORDER BY position')->fetchAll();
echo json_encode(['success'=>true,'data'=>$stories]);
