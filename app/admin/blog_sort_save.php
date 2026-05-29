<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['ok'=>false]); exit; }
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$ids  = $data['ids'] ?? [];

if (!is_array($ids) || empty($ids)) {
    echo json_encode(['ok' => false, 'error' => 'No IDs provided']);
    exit;
}

$pdo  = Database::getInstance();
$stmt = $pdo->prepare("UPDATE blogs SET sort_order = ? WHERE id = ?");
foreach ($ids as $order => $id) {
    $stmt->execute([(int)$order, (int)$id]);
}

echo json_encode(['ok' => true]);
