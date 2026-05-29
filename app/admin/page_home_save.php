<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = Database::getInstance();
    $page_slug = 'home';

    // Save text inputs
    if (isset($_POST['content'])) {
        $stmt = $pdo->prepare("
            INSERT INTO page_content (page_slug, section_key, content) 
            VALUES (:slug, :key, :content)
            ON DUPLICATE KEY UPDATE content = VALUES(content)
        ");

        foreach ($_POST['content'] as $key => $content) {
            $stmt->execute([
                ':slug' => $page_slug,
                ':key' => $key,
                ':content' => $content
            ]);
        }
    }

    // Save file uploads (specifically case_img_path)
    if (isset($_FILES['case_img_path']) && $_FILES['case_img_path']['error'] === UPLOAD_ERR_OK) {
        $upload = $_FILES['case_img_path'];
        $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp', 'gif'];
        $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed, true)) {
            $baseName = pathinfo($upload['name'], PATHINFO_FILENAME);
            $baseName = preg_replace('/[^a-z0-9-_]/i', '-', $baseName);
            $baseName = trim($baseName, '-');
            if ($baseName === '') {
                $baseName = 'case_img';
            }
            $filename = $baseName . '-' . time() . '.' . $ext;

            $uploadDir = __DIR__ . '/../../public/assets/img';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $destination = $uploadDir . '/' . $filename;
            
            if (move_uploaded_file($upload['tmp_name'], $destination)) {
                $imgPath = 'public/assets/img/' . $filename;
                
                $stmt = $pdo->prepare("
                    INSERT INTO page_content (page_slug, section_key, content) 
                    VALUES (:slug, 'case_img_path', :content)
                    ON DUPLICATE KEY UPDATE content = VALUES(content)
                ");
                $stmt->execute([
                    ':slug' => $page_slug,
                    ':content' => $imgPath
                ]);
            }
        }
    }

    header('Location: page_home_edit.php?success=1');
    exit;
}
?>