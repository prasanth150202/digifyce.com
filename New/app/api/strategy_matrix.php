<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$matrix = $pdo->query('SELECT id, quadrant, title, diagnosis, position FROM strategy_matrix ORDER BY position')->fetchAll();
foreach ($matrix as &$item) {
    $stmt = $pdo->prepare('SELECT step_text FROM strategy_steps WHERE matrix_id=?');
    $stmt->execute([$item['id']]);
    $item['steps'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
echo json_encode(['success'=>true,'data'=>$matrix]);
