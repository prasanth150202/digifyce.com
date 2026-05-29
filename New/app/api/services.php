<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$services = $pdo->query('SELECT id, title, description, position FROM services ORDER BY position')->fetchAll();
foreach ($services as &$service) {
    $stmt = $pdo->prepare('SELECT tag FROM service_tags WHERE service_id=?');
    $stmt->execute([$service['id']]);
    $service['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
echo json_encode(['success'=>true,'data'=>$services]);
