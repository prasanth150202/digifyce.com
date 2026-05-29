<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');
$pdo = Database::getInstance();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    // Find related by category or tags
    $stmt = $pdo->prepare('SELECT category_id FROM blogs WHERE id=?');
    $stmt->execute([$id]);
    $cat = $stmt->fetchColumn();
    $sql = 'SELECT b.id, b.title, b.slug, b.featured_image FROM blogs b WHERE b.status="published" AND b.id<>? AND (b.category_id=? OR b.id IN (SELECT blog_id FROM blog_tag_map WHERE tag_id IN (SELECT tag_id FROM blog_tag_map WHERE blog_id=?))) ORDER BY b.published_at DESC LIMIT 4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $cat, $id]);
    echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
} else {
    echo json_encode(['success'=>false,'error'=>'No id']);
}
