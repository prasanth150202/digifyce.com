<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::getInstance();
    $hero       = $pdo->query("SELECT * FROM products_hero WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $crm        = $pdo->query("SELECT * FROM products_crm_section WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $crmFeats   = $pdo->query("SELECT * FROM products_crm_features WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
    $zingbot    = $pdo->query("SELECT * FROM products_zingbot_section WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $zingFeats  = $pdo->query("SELECT * FROM products_zingbot_features WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
    $cta        = $pdo->query("SELECT * FROM products_cta WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => [
        'hero'           => $hero,
        'crm'            => $crm,
        'crm_features'   => $crmFeats,
        'zingbot'        => $zingbot,
        'zingbot_features' => $zingFeats,
        'cta'            => $cta,
    ]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
