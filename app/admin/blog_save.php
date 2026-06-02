<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: 'n-a';
}

$id               = $_POST['id']               ?? null;
$title            = trim($_POST['title']            ?? '');
$slug             = trim($_POST['slug']             ?? '');
$excerpt          = trim($_POST['excerpt']          ?? '');
$content          = $_POST['content']          ?? '';
$meta_title       = trim($_POST['meta_title']       ?? '');
$meta_description = trim($_POST['meta_description'] ?? '');
$author_id        = ($_POST['author_id']   ?? '') !== '' ? (int)$_POST['author_id']   : null;
$category_id      = ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null;
$status           = in_array($_POST['status'] ?? '', ['draft','published','scheduled']) ? $_POST['status'] : 'draft';
$scheduled_at     = ($_POST['scheduled_at'] ?? '') !== '' ? $_POST['scheduled_at'] : null;
$tags             = $_POST['tags'] ?? [];

// Build slug from title if empty, then ensure uniqueness
if ($slug === '') {
    $slug = slugify($title);
}
if ($slug === '' || $slug === 'n-a') {
    $slug = 'post-' . time();
}

// Guarantee slug is unique (skip current row when editing)
$slugBase    = $slug;
$slugSuffix  = 1;
$checkSql    = $id
    ? 'SELECT id FROM blogs WHERE slug = ? AND id != ? LIMIT 1'
    : 'SELECT id FROM blogs WHERE slug = ? LIMIT 1';
while (true) {
    $chk = $pdo->prepare($checkSql);
    $chk->execute($id ? [$slug, $id] : [$slug]);
    if (!$chk->fetch()) break;
    $slug = $slugBase . '-' . $slugSuffix++;
}

// Ensure uploads directory exists and is writable
$uploadDir = __DIR__ . '/../../storage/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle image upload
$featured_image = null;
$uploadError    = null;
if (!empty($_FILES['featured_image']['name'])) {
    $fileErr = $_FILES['featured_image']['error'];
    if ($fileErr !== UPLOAD_ERR_OK) {
        $uploadError = match($fileErr) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File too large (server limit: ' . ini_get('upload_max_filesize') . '). Use a smaller image.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temp folder — contact your host.',
            UPLOAD_ERR_CANT_WRITE => 'Server could not write the temp file — contact your host.',
            default               => 'Upload failed (PHP error code ' . $fileErr . ').',
        };
    } else {
        $allowedExts = ['jpg','jpeg','png','gif','webp','avif'];
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            $uploadError = 'Invalid file type "' . $ext . '". Allowed: jpg, jpeg, png, gif, webp, avif.';
        } elseif (!is_writable($uploadDir)) {
            $uploadError = 'Upload folder is not writable on the server (storage/uploads/). Set permissions to 755.';
        } else {
            $filename = uniqid('blog_', true) . '.' . $ext;
            $dest     = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $dest)) {
                $featured_image = $filename;
            } else {
                $uploadError = 'move_uploaded_file failed — check that storage/uploads/ exists and is writable.';
            }
        }
    }
}
if ($uploadError) {
    $_SESSION['upload_error'] = $uploadError;
}

$removeImage = ($_POST['remove_image'] ?? '0') === '1';

if ($id) {
    // Update
    $sql = 'UPDATE blogs SET title=?, slug=?, excerpt=?, content=?, meta_title=?, meta_description=?, author_id=?, category_id=?, status=?, scheduled_at=?, updated_at=NOW()';
    $params = [$title, $slug, $excerpt, $content, $meta_title, $meta_description, $author_id, $category_id, $status, $scheduled_at];
    if ($featured_image) {
        // New image uploaded — replace existing
        $sql .= ', featured_image=?';
        $params[] = $featured_image;
    } elseif ($removeImage) {
        // Explicitly cleared by admin
        $sql .= ', featured_image=NULL';
        // Optionally delete the file from disk
        $oldRow = $pdo->prepare('SELECT featured_image FROM blogs WHERE id=?');
        $oldRow->execute([$id]);
        $oldFile = $oldRow->fetchColumn();
        if ($oldFile && file_exists($uploadDir . '/' . $oldFile)) {
            @unlink($uploadDir . '/' . $oldFile);
        }
    }
    $sql .= ' WHERE id=?';
    $params[] = $id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $blog_id = $id;
    // Remove old tags
    $pdo->prepare('DELETE FROM blog_tag_map WHERE blog_id=?')->execute([$blog_id]);
} else {
    // Assign new post a sort_order at the end of the current list
    $nextOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order) + 1, 0) FROM blogs")->fetchColumn();
    // Insert
    $stmt = $pdo->prepare('INSERT INTO blogs (title, slug, excerpt, content, meta_title, meta_description, author_id, category_id, status, scheduled_at, featured_image, sort_order, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())');
    $stmt->execute([$title, $slug, $excerpt, $content, $meta_title, $meta_description, $author_id, $category_id, $status, $scheduled_at, $featured_image, $nextOrder]);
    $blog_id = $pdo->lastInsertId();
}
// Insert tags
if ($tags && $blog_id) {
    $tag_stmt = $pdo->prepare('INSERT INTO blog_tag_map (blog_id, tag_id) VALUES (?, ?)');
    foreach ($tags as $tag_id) {
        $tag_stmt->execute([$blog_id, $tag_id]);
    }
}
$redirect = "blog_edit.php?id=$blog_id&saved=1";
if ($uploadError) $redirect .= '&img_err=1';
header("Location: $redirect");
exit;
