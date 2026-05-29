<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::getInstance();
    $hero = $pdo->query("SELECT * FROM technology_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $panels = $pdo->query("SELECT * FROM technology_panels WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($panels as &$p) {
        $p['bullets_json']  = $p['bullets_json']  ? json_decode($p['bullets_json'],  true) : [];
        $p['image_paths']   = $p['image_paths']   ? array_map('trim', explode(',', $p['image_paths'])) : [];
    }
    unset($p);

    echo json_encode(['success' => true, 'data' => ['hero' => $hero, 'panels' => $panels]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
