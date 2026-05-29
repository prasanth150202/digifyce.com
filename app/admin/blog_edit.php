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

<script src="assest/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
  license_key: 'gpl',
  selector: '#content',
  height: 500,

  plugins: 'advlist autolink lists link image table code paste',
  toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | code',

  paste_as_text: false,
  paste_remove_styles: true,
  paste_remove_spans: true,
  paste_strip_class_attributes: 'all',

  valid_elements: 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,strong,b,em,i,a[href],img[src|alt],br',

  paste_preprocess: function(plugin, args) {
    let content = args.content;
    content = content.replace(/\s+(class|style|lang|xml:lang|role|aria-[a-z-]+|data-[^=]*)="[^"]*"/gi, '');
    content = content.replace(/<\/?(span|div|font|o:p|w:[^>]*)[^>]*>/gi, '');
    content = content.replace(/<\/ul>\s*<ul>/gi, '');
    content = content.replace(/<\/ol>\s*<ol>/gi, '');
    content = content.replace(/<p[^>]*>\s*(&nbsp;|\s)*<\/p>/gi, '');
    args.content = content;
  },

  // Sync editor content back to textarea before every form submit
  setup: function(editor) {
    editor.on('submit', function() {
      editor.save();
    });
  }
});

// Belt-and-suspenders: also sync on the form's submit event
document.addEventListener('DOMContentLoaded', function () {
  var form = document.querySelector('form[enctype="multipart/form-data"]');
  if (form) {
    form.addEventListener('submit', function () {
      if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
      }
    });
  }
});
</script>
<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> Blog post saved successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

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
                <input type="text" name="slug" id="slugField" class="form-control" required
                       value="<?= htmlspecialchars($blog['slug'] ?? '') ?>">
                <script>
                // Auto-generate slug from title only for new posts (empty slug field)
                (function () {
                    var titleEl = document.querySelector('[name="title"]');
                    var slugEl  = document.getElementById('slugField');
                    var touched = slugEl.value !== '';
                    if (titleEl && slugEl) {
                        titleEl.addEventListener('input', function () {
                            if (!touched) {
                                slugEl.value = this.value
                                    .toLowerCase()
                                    .replace(/[^a-z0-9\s-]/g, '')
                                    .trim()
                                    .replace(/\s+/g, '-');
                            }
                        });
                        slugEl.addEventListener('input', function () { touched = true; });
                    }
                })();
                </script>
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
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($blog['meta_title'] ?? '') ?>" placeholder="SEO page title (60 chars max)">
                <small class="text-muted">Recommended: 50-60 characters</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="2" placeholder="SEO page description (160 chars max)"><?= htmlspecialchars($blog['meta_description'] ?? '') ?></textarea>
                <small class="text-muted">Recommended: 150-160 characters</small>
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
            <div class="col-12 d-flex gap-3 align-items-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save
                </button>
                <?php if ($blog): ?>
                <a href="blog_preview?id=<?= $blog['id'] ?>" target="_blank" class="btn btn-outline-warning">
                    <i class="fas fa-eye me-2"></i>Preview
                </a>
                <?php endif; ?>
                <a href="blogs.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>


<?php include __DIR__ . '/../views/admin_footer.php'; ?>
