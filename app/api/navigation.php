<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$nav = $pdo->query('SELECT label, url, is_footer, position, parent_id FROM navigation ORDER BY position')->fetchAll();
echo json_encode(['success'=>true,'data'=>$nav]);
