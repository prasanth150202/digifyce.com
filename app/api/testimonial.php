<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../config/database.php';

try {
    $items = Database::getInstance()
        ->query("SELECT * FROM testimonial_items WHERE is_active=1 ORDER BY sort_order ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => ['items' => $items]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
