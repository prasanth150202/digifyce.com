<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();
$hero = $pdo->query('SELECT headline, subtext, cta_label, cta_url FROM hero_section LIMIT 1')->fetch();
echo json_encode(['success'=>true,'data'=>$hero]);
