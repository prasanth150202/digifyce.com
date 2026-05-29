<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::getInstance();

    $heroStmt = $pdo->query("SELECT * FROM service_hero WHERE id = 1 LIMIT 1");
    $hero = $heroStmt ? $heroStmt->fetch(PDO::FETCH_ASSOC) : null;

    $blocksStmt = $pdo->query("SELECT * FROM service_blocks WHERE is_active = 1 ORDER BY sort_order ASC");
    $blocks = $blocksStmt ? $blocksStmt->fetchAll(PDO::FETCH_ASSOC) : [];

    foreach ($blocks as &$block) {
        $block['left_metrics_json'] = $block['left_metrics_json'] ? json_decode($block['left_metrics_json'], true) : [];
        $block['right_metrics_json'] = $block['right_metrics_json'] ? json_decode($block['right_metrics_json'], true) : [];
        $block['case_study_stats_json'] = $block['case_study_stats_json'] ? json_decode($block['case_study_stats_json'], true) : [];
        $block['tech_badges'] = $block['tech_badges'] ? array_map('trim', explode(',', $block['tech_badges'])) : [];
    }
    unset($block);

    echo json_encode(['success' => true, 'data' => ['hero' => $hero, 'blocks' => $blocks]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
