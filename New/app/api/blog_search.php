<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();

$q = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '';
if ($q) {
    $stmt = $pdo->prepare('SELECT id, title, slug, excerpt, featured_image, published_at FROM blogs WHERE status="published" AND (title LIKE ? OR excerpt LIKE ?) ORDER BY published_at DESC LIMIT 20');
    $stmt->execute([$q, $q]);
    echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
} else {
    echo json_encode(['success'=>false,'error'=>'No query']);
}
