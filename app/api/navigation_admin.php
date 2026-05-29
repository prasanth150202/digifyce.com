<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Check if user is authenticated and is admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
$pdo = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'list') {
            // Get all navigation items
            $stmt = $pdo->query('SELECT id, label, url, position, is_footer, parent_id, footer_group FROM navigation ORDER BY is_footer ASC, position ASC');
            $nav = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $nav]);
        }
        break;

    case 'POST':
        if ($action === 'add') {
            // Add new navigation item
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['label']) || !isset($data['url'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }
            
            // Get max position
            $result = $pdo->query('SELECT MAX(position) as max_pos FROM navigation WHERE is_footer = ' . ($data['is_footer'] ? 1 : 0))->fetch();
            $position = ($result['max_pos'] ?? 0) + 1;
            
            $stmt = $pdo->prepare('INSERT INTO navigation (label, url, position, is_footer, parent_id, footer_group) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $data['label'],
                $data['url'],
                $position,
                $data['is_footer'] ? 1 : 0,
                $data['parent_id'] ?? null,
                $data['footer_group'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } elseif ($action === 'update') {
            // Update navigation item
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing ID']);
                exit;
            }
            
            $stmt = $pdo->prepare('UPDATE navigation SET label = ?, url = ?, is_footer = ?, parent_id = ?, footer_group = ? WHERE id = ?');
            $stmt->execute([
                $data['label'],
                $data['url'],
                $data['is_footer'] ? 1 : 0,
                $data['parent_id'] ?? null,
                $data['footer_group'] ?? null,
                $data['id']
            ]);
            
            echo json_encode(['success' => true]);
        } elseif ($action === 'reorder') {
            // Reorder navigation items
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['items'])) {
                echo json_encode(['success' => false, 'message' => 'Missing items']);
                exit;
            }
            
            foreach ($data['items'] as $index => $item) {
                $stmt = $pdo->prepare('UPDATE navigation SET position = ? WHERE id = ?');
                $stmt->execute([$index + 1, $item['id']]);
            }
            
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing ID']);
                exit;
            }
            
            $stmt = $pdo->prepare('DELETE FROM navigation WHERE id = ?');
            $stmt->execute([$data['id']]);
            
            echo json_encode(['success' => true]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
