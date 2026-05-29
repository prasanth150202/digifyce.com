<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();

// Fetch categories, tags, authors
$categories = $pdo->query('SELECT id, name FROM blog_categories ORDER BY name')->fetchAll();
$tags = $pdo->query('SELECT id, name FROM blog_tags ORDER BY name')->fetchAll();
$authors = $pdo->query('SELECT id, name FROM blog_authors ORDER BY name')->fetchAll();

// If editing
$blog = null;
$selected_tags = [];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM blogs WHERE id=?');
    $stmt->execute([$_GET['id']]);
    $blog = $stmt->fetch();
    if ($blog) {
        $tag_stmt = $pdo->prepare('SELECT tag_id FROM blog_tag_map WHERE blog_id=?');
        $tag_stmt->execute([$blog['id']]);
        $selected_tags = array_column($tag_stmt->fetchAll(), 'tag_id');
    }
}
?>
<?php
$pageTitle = $blog ? 'Edit Blog' : 'New Blog';
include __DIR__ . '/../views/admin_header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
tinymce.init({ 
    selector: '#content', 
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_style: "@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap'); body { font-family:'Space Grotesk', sans-serif; font-size:14px }"
});
</script>

<div class="card border-0">
    <div class="card-header">
        <i class="fas fa-pen me-2"></i><?= $blog ? 'Edit' : 'Create' ?> Blog Post
    </div>
    <div class="card-body">
        <form method="post" action="blog_save.php" enctype="multipart/form-data" class="row g-3">
            <?php if ($blog): ?><input type="hidden" name="id" value="<?= $blog['id'] ?>"><?php endif; ?>
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($blog['title'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars($blog['slug'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="2"><?= htmlspecialchars($blog['excerpt'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Content</label>
                <textarea id="content" name="content" class="form-control"><?= htmlspecialchars($blog['content'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control">
                <?php if (!empty($blog['featured_image'])): ?>
                    <img src="<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($blog['featured_image']) ?>" alt="Featured" class="mt-2 rounded" style="width: 160px; height: 100px; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Author</label>
                <select name="author_id" class="form-select">
                    <option value="">-- Select --</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= isset($blog['author_id']) && $blog['author_id'] == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">-- Select --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= isset($blog['category_id']) && $blog['category_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="draft" <?= (isset($blog['status']) && $blog['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= (isset($blog['status']) && $blog['status'] == 'published') ? 'selected' : '' ?>>Published</option>
                    <option value="scheduled" <?= (isset($blog['status']) && $blog['status'] == 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Schedule Date</label>
                <input type="datetime-local" name="scheduled_at" value="<?= isset($blog['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($blog['scheduled_at'])) : '' ?>" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Tags</label>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($tags as $t): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="<?= $t['id'] ?>" id="tag-<?= $t['id'] ?>" <?= in_array($t['id'], $selected_tags) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tag-<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 d-flex gap-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="blogs.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
