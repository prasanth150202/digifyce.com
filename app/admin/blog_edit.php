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
  statusbar: false,

  plugins: 'advlist autolink lists link image table code paste',
  toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | table | code | insertcta insertpdf',

  paste_as_text: false,
  paste_remove_spans: true,
  paste_strip_class_attributes: 'all',

  valid_elements: 'p[style],h1,h2,h3,h4,h5,h6,ul,ol,li,strong,b,em,i,a[href|style|target|rel|download],img[src|alt|style],br,div[style|class],' +
    'table[width|cellpadding|cellspacing|border|style],thead,tbody,tfoot,' +
    'tr,th[colspan|rowspan|scope|style|align],td[colspan|rowspan|style|align|width]',

  paste_preprocess: function(plugin, args) {
    let content = args.content;
    // Strip Word/Office junk attributes but preserve style on table cells
    content = content.replace(/\s+(class|lang|xml:lang|role|aria-[a-z-]+|data-[^=]*)="[^"]*"/gi, '');
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

    editor.ui.registry.addButton('insertcta', {
      text: 'Add CTA',
      tooltip: 'Insert a Call-to-Action button into the content',
      onAction: function() {
        var modal = new bootstrap.Modal(document.getElementById('ctaModal'));
        document.getElementById('ctaModalText').value = '';
        document.getElementById('ctaModalUrl').value = '';
        document.getElementById('ctaAlignCenter').checked = true;
        window._tinymceEditorForCta = editor;
        modal.show();
        setTimeout(function(){ document.getElementById('ctaModalText').focus(); }, 400);
      }
    });

    editor.ui.registry.addButton('insertpdf', {
      text: 'Add PDF',
      tooltip: 'Insert a PDF download button into the content',
      onAction: function() {
        document.getElementById('pdfModalLabel').value = '';
        document.getElementById('pdfModalDownloadName').value = '';
        document.getElementById('pdfAlignCenter').checked = true;
        document.getElementById('pdfModalStatus').textContent = '';
        document.getElementById('pdfModalStatus').className = '';
        window._pdfSelectedFile = null;
        document.getElementById('pdfFileDisplay').textContent = 'No file chosen';
        document.getElementById('pdfFileDisplay').style.color = '';
        document.getElementById('pdfFileInvalid').style.display = 'none';
        window._tinymceEditorForPdf = editor;
        var modal = new bootstrap.Modal(document.getElementById('pdfModal'), { focus: false });
        modal.show();
        setTimeout(function(){ document.getElementById('pdfModalLabel').focus(); }, 400);
      }
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
<?php if (isset($_GET['img_err']) && !empty($_SESSION['upload_error'])): ?>
<div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Image not saved:</strong> <?= htmlspecialchars($_SESSION['upload_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['upload_error']); endif; ?>

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
                <input type="file" name="featured_image" class="form-control" accept="image/*">
                <?php if (!empty($blog['featured_image'])): ?>
                    <div class="mt-2 d-flex align-items-start gap-2" id="currentImageWrap">
                        <img src="<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($blog['featured_image']) ?>"
                             alt="Featured" class="rounded" style="width:160px;height:100px;object-fit:cover;">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="removeImage()">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                            <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                            <div id="removeImageNote" class="text-muted small mt-1" style="display:none">
                                Image will be removed on save.
                            </div>
                        </div>
                    </div>
                    <script>
                    function removeImage() {
                        document.getElementById('removeImageFlag').value = '1';
                        document.getElementById('currentImageWrap').style.opacity = '0.3';
                        document.getElementById('removeImageNote').style.display = 'block';
                        event.currentTarget.textContent = 'Undo';
                        event.currentTarget.className = 'btn btn-sm btn-outline-secondary';
                        event.currentTarget.onclick = function() {
                            document.getElementById('removeImageFlag').value = '0';
                            document.getElementById('currentImageWrap').style.opacity = '1';
                            document.getElementById('removeImageNote').style.display = 'none';
                            this.innerHTML = '<i class="fas fa-trash me-1"></i> Remove';
                            this.className = 'btn btn-sm btn-outline-danger';
                            this.onclick = removeImage;
                        };
                    }
                    </script>
                <?php else: ?>
                    <input type="hidden" name="remove_image" value="0">
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


<!-- ── CTA Insert Modal ──────────────────────────────────────────────────────── -->
<div class="modal fade" id="ctaModal" tabindex="-1" aria-labelledby="ctaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ctaModalLabel">
                    <i class="fas fa-mouse-pointer me-2 text-primary"></i>Insert CTA Button
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Button Text <span class="text-danger">*</span></label>
                    <input type="text" id="ctaModalText" class="form-control"
                           placeholder="e.g. Get a Free Consultation" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Button URL <span class="text-danger">*</span></label>
                    <input type="text" id="ctaModalUrl" class="form-control"
                           placeholder="e.g. /contact or https://example.com">
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold small">Alignment</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ctaAlign" id="ctaAlignLeft" value="left">
                            <label class="form-check-label small" for="ctaAlignLeft">Left</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ctaAlign" id="ctaAlignCenter" value="center" checked>
                            <label class="form-check-label small" for="ctaAlignCenter">Center</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ctaAlign" id="ctaAlignRight" value="right">
                            <label class="form-check-label small" for="ctaAlignRight">Right</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="ctaInsertBtn">
                    <i class="fas fa-plus me-1"></i> Insert CTA
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Positions the TinyMCE cursor after the current top-level block before
// inserting, so buttons are never nested inside existing content.
function insertAtRootBlock(editor, html) {
    var body = editor.getBody();
    var node = editor.selection.getNode();
    while (node && node.parentNode && node.parentNode !== body) {
        node = node.parentNode;
    }
    if (node && node !== body) {
        var rng = editor.dom.createRng();
        rng.setStartAfter(node);
        rng.setEndAfter(node);
        editor.selection.setRng(rng);
    }
    editor.insertContent(html);
}

(function () {
    function doInsertCta() {
        var text  = document.getElementById('ctaModalText').value.trim();
        var url   = document.getElementById('ctaModalUrl').value.trim();
        var align = document.querySelector('input[name="ctaAlign"]:checked').value;

        if (!text || !url) {
            document.getElementById(text ? 'ctaModalUrl' : 'ctaModalText').classList.add('is-invalid');
            return;
        }

        var safeText = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        var safeUrl  = url.replace(/"/g,'&quot;');

        var html = '<p style="text-align:' + align + ';margin:2rem 0;">' +
            '<a href="' + safeUrl + '" target="_blank" rel="noopener" ' +
            'style="display:inline-block;background-color:#0d69f2;color:#ffffff;' +
            'padding:14px 32px;border-radius:6px;font-weight:700;text-decoration:none;' +
            'font-size:15px;letter-spacing:0.05em;text-transform:uppercase;">' +
            safeText + '</a></p>';

        if (window._tinymceEditorForCta) {
            insertAtRootBlock(window._tinymceEditorForCta, html);
        }

        bootstrap.Modal.getInstance(document.getElementById('ctaModal')).hide();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('ctaInsertBtn').addEventListener('click', doInsertCta);

        ['ctaModalText', 'ctaModalUrl'].forEach(function (id) {
            var el = document.getElementById(id);
            el.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') doInsertCta();
            });
            el.addEventListener('input', function () {
                this.classList.remove('is-invalid');
            });
        });
    });
})();
</script>

<!-- ── PDF Download Insert Modal ────────────────────────────────────────────── -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalHeading" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalHeading">
                    <i class="fas fa-file-pdf me-2 text-danger"></i>Insert PDF Download Button
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Button Label <span class="text-danger">*</span></label>
                    <input type="text" id="pdfModalLabel" class="form-control"
                           placeholder="e.g. Download Our Guide" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">PDF File <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="pdfChooseFileBtn">
                            <i class="fas fa-folder-open me-1"></i>Choose PDF…
                        </button>
                        <span id="pdfFileDisplay" class="small text-muted">No file chosen</span>
                    </div>
                    <div id="pdfFileInvalid" class="small text-danger mt-1" style="display:none">Please select a PDF file.</div>
                    <small class="text-muted">Only PDF files are accepted.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Downloaded File Name <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" id="pdfModalDownloadName" class="form-control"
                           placeholder="e.g. digifyce-guide-2025.pdf" maxlength="200">
                    <small class="text-muted">What the browser names the file when the user saves it.</small>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold small">Alignment</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pdfAlign" id="pdfAlignLeft" value="left">
                            <label class="form-check-label small" for="pdfAlignLeft">Left</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pdfAlign" id="pdfAlignCenter" value="center" checked>
                            <label class="form-check-label small" for="pdfAlignCenter">Center</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pdfAlign" id="pdfAlignRight" value="right">
                            <label class="form-check-label small" for="pdfAlignRight">Right</label>
                        </div>
                    </div>
                </div>
                <div id="pdfModalStatus" class="mt-3 small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="pdfInsertBtn">
                    <i class="fas fa-upload me-1"></i> Upload &amp; Insert
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var appBase = <?= json_encode(rtrim($appUrl ?? '', '/')) ?>;

    var insertBtn   = document.getElementById('pdfInsertBtn');
    var statusEl    = document.getElementById('pdfModalStatus');
    var fileDisplay = document.getElementById('pdfFileDisplay');
    var fileInvalid = document.getElementById('pdfFileInvalid');

    function setStatus(msg, type) {
        statusEl.textContent = msg;
        statusEl.className   = 'mt-3 small text-' + type;
    }

    function resetBtn() {
        insertBtn.disabled  = false;
        insertBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload &amp; Insert';
    }

    // Create a fresh input element in document.body each time to avoid Windows file dialog freeze
    document.getElementById('pdfChooseFileBtn').addEventListener('click', function () {
        var inp    = document.createElement('input');
        inp.type   = 'file';
        inp.accept = '.pdf,application/pdf';
        inp.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0;width:0;height:0;';
        document.body.appendChild(inp);

        inp.addEventListener('change', function () {
            if (inp.files.length) {
                window._pdfSelectedFile = inp.files[0];
                fileDisplay.textContent  = inp.files[0].name;
                fileDisplay.style.color  = '';
                fileInvalid.style.display = 'none';
            }
            if (document.body.contains(inp)) { document.body.removeChild(inp); }
        });

        inp.addEventListener('cancel', function () {
            if (document.body.contains(inp)) { document.body.removeChild(inp); }
        });

        inp.click();
    });

    insertBtn.addEventListener('click', function () {
        var label        = document.getElementById('pdfModalLabel').value.trim();
        var downloadName = document.getElementById('pdfModalDownloadName').value.trim();
        var align        = document.querySelector('input[name="pdfAlign"]:checked').value;

        document.getElementById('pdfModalLabel').classList.remove('is-invalid');
        fileInvalid.style.display = 'none';

        if (!label) { document.getElementById('pdfModalLabel').classList.add('is-invalid'); return; }
        if (!window._pdfSelectedFile) { fileInvalid.style.display = 'block'; return; }

        if (!window._pdfSelectedFile.name.toLowerCase().endsWith('.pdf')) {
            setStatus('Only PDF files are allowed.', 'danger');
            return;
        }

        insertBtn.disabled  = true;
        insertBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading…';
        setStatus('Uploading PDF, please wait…', 'muted');

        var formData = new FormData();
        formData.append('pdf', window._pdfSelectedFile);

        fetch('blog_pdf_upload.php', { method: 'POST', body: formData })
            .then(function (r) {
                return r.text().then(function (text) {
                    try { return JSON.parse(text); }
                    catch (e) { throw new Error('Server returned: ' + text.substring(0, 120)); }
                });
            })
            .then(function (data) {
                if (!data.ok) {
                    setStatus('Upload failed: ' + (data.error || 'Unknown error'), 'danger');
                    resetBtn();
                    return;
                }

                var safeLabel = label.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                var fileUrl   = appBase + '/pdf-download?file=' + encodeURIComponent(data.filename)
                                       + (downloadName ? '&name=' + encodeURIComponent(downloadName) : '');

                var html = '<p style="text-align:' + align + ';margin:2rem 0;">' +
                    '<a href="' + fileUrl + '"' +
                    ' style="display:inline-flex;align-items:center;gap:10px;' +
                    'background-color:#1e293b;color:#e2e8f0;padding:14px 28px;border-radius:6px;' +
                    'font-weight:700;text-decoration:none;font-size:14px;letter-spacing:0.05em;' +
                    'text-transform:uppercase;border:2px solid #0d69f2;">' +
                    '&#11015; ' + safeLabel + '</a></p>';

                if (window._tinymceEditorForPdf) { insertAtRootBlock(window._tinymceEditorForPdf, html); }
                bootstrap.Modal.getInstance(document.getElementById('pdfModal')).hide();
                resetBtn();
            })
            .catch(function (err) {
                setStatus(err.message || 'Network error — please try again.', 'danger');
                resetBtn();
            });
    });

    document.getElementById('pdfModalLabel').addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });
})();
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
