<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/section_defs.php';

$pdo = Database::getInstance();

// Ensure tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS builder_pages (
    id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL,
    meta_desc VARCHAR(500) DEFAULT NULL, status ENUM('draft','published') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_slug (slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS builder_sections (
    id INT AUTO_INCREMENT PRIMARY KEY, page_id INT NOT NULL,
    section_type VARCHAR(64) NOT NULL, sort_order INT DEFAULT 0,
    config LONGTEXT NOT NULL DEFAULT '{}',
    FOREIGN KEY (page_id) REFERENCES builder_pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pageId   = intval($_GET['id'] ?? 0);
$page     = null;
$sections = [];

if ($pageId) {
    $page = $pdo->prepare("SELECT * FROM builder_pages WHERE id = ?");
    $page->execute([$pageId]);
    $page = $page->fetch(PDO::FETCH_ASSOC);
    if (!$page) { header('Location: page_builder'); exit; }

    $rows = $pdo->prepare("SELECT * FROM builder_sections WHERE page_id = ? ORDER BY sort_order ASC");
    $rows->execute([$pageId]);
    foreach ($rows->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $sections[] = [
            'type'   => $row['section_type'],
            'config' => json_decode($row['config'], true) ?: [],
        ];
    }
}

$pageTitle = $pageId ? 'Edit Page' : 'New Page';
include __DIR__ . '/../views/admin_header.php';

// Export section defs to JS (strip PHP-only info, keep what JS needs)
$jsDefs = [];
foreach ($SECTION_DEFS as $type => $def) {
    $jsDefs[$type] = [
        'label'         => $def['label'],
        'icon'          => $def['icon'],
        'color'         => $def['color'],
        'preview_field' => $def['preview_field'],
        'fields'        => $def['fields'],
        'defaults'      => $def['defaults'],
    ];
}
?>

<style>
.section-card { transition: box-shadow .15s; }
.section-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.12) !important; }
.type-pill { cursor: pointer; border: 2px solid transparent; transition: border-color .15s, transform .15s; border-radius: 12px; }
.type-pill:hover { border-color: #0066ff; transform: translateY(-2px); }
.type-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center; }
.rep-item { background:#f8fafc; }
.builder-sidebar { width:240px;flex-shrink:0; }
@media(max-width:768px){ .builder-sidebar{width:100%;} }
</style>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i> Page saved successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Top bar -->
<form id="builderForm" method="post" action="page_builder_save">
<input type="hidden" name="page_id" value="<?= $pageId ?>">
<input type="hidden" name="sections_json" id="sectionsJson">

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold mb-1">Page Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required
                    value="<?= htmlspecialchars($page['title'] ?? '') ?>" placeholder="My Landing Page">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold mb-1">Slug <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted">/</span>
                    <input type="text" name="slug" id="slugInput" class="form-control font-monospace" required
                        value="<?= htmlspecialchars(ltrim($page['slug'] ?? '', '/')) ?>" placeholder="my-landing-page">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="draft"     <?= ($page['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="col-md-3 text-end d-flex gap-2 justify-content-end align-items-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Save
                </button>
                <?php if ($pageId): ?>
                <a href="page_builder_preview?id=<?= $pageId ?>" target="_blank"
                   class="btn btn-outline-warning" title="Preview last saved state">
                    <i class="fas fa-eye me-1"></i> Preview
                </a>
                <?php endif; ?>
                <a href="page_builder" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
        <!-- SEO fields (collapsible) -->
        <div class="mt-3 pt-3 border-top">
            <a class="text-muted small" data-bs-toggle="collapse" href="#seoFields">
                <i class="fas fa-search me-1"></i> SEO Settings <i class="fas fa-chevron-down ms-1" style="font-size:10px"></i>
            </a>
            <div class="collapse" id="seoFields">
                <div class="row g-3 mt-1">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" maxlength="120">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Meta Description</label>
                        <input type="text" name="meta_desc" class="form-control form-control-sm"
                            value="<?= htmlspecialchars($page['meta_desc'] ?? '') ?>" maxlength="320">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<!-- Builder area -->
<div class="d-flex gap-3 align-items-start">

    <!-- Section type palette -->
    <div class="builder-sidebar">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold small"><i class="fas fa-shapes me-2"></i>Add Section</div>
            <div class="card-body p-2">
                <?php foreach ($SECTION_DEFS as $type => $def): ?>
                <div class="type-pill p-2 mb-1 d-flex align-items-center gap-2"
                    onclick="addSection('<?= $type ?>')">
                    <div class="type-icon" style="background:<?= $def['color'] ?>22">
                        <i class="fas <?= $def['icon'] ?>" style="color:<?= $def['color'] ?>;font-size:16px"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small"><?= $def['label'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Section list -->
    <div class="flex-grow-1">
        <div id="sectionList"></div>
        <div id="emptyState" class="card border-dashed border-2 text-center py-5 text-muted d-none">
            <i class="fas fa-layer-group fa-2x mb-3 d-block opacity-25"></i>
            Click a section type on the left to add it to your page.
        </div>
    </div>
</div>

<!-- ── Section editor modal ── -->
<div class="modal fade" id="editorModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editorTitle"><i class="fas fa-pencil-alt me-2"></i></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editorFields"></div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary px-4" onclick="saveEdit()">
                    <i class="fas fa-check me-2"></i>Apply
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Type picker modal ── -->
<div class="modal fade" id="pickerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Choose Section Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="pickerGrid"></div>
            </div>
        </div>
    </div>
</div>

<script>
// ── State ──────────────────────────────────────────────────────────────
const DEFS = <?= json_encode($jsDefs, JSON_UNESCAPED_UNICODE) ?>;
let sections = <?= json_encode($sections, JSON_UNESCAPED_UNICODE) ?>;
let editIdx  = -1;

// Auto-generate slug from title
document.querySelector('[name="title"]').addEventListener('input', function () {
    const s = document.getElementById('slugInput');
    if (!s.dataset.touched) {
        s.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
});
document.getElementById('slugInput').addEventListener('input', function () {
    this.dataset.touched = '1';
});

// ── Render section list ────────────────────────────────────────────────
function renderSections() {
    const list  = document.getElementById('sectionList');
    const empty = document.getElementById('emptyState');
    list.innerHTML = '';
    if (!sections.length) { empty.classList.remove('d-none'); return; }
    empty.classList.add('d-none');

    sections.forEach((sec, idx) => {
        const def = DEFS[sec.type];
        const raw = sec.config[def.preview_field] || '';
        const preview = raw.replace(/<[^>]+>/g, '').substring(0, 70) || '(empty)';

        const card = document.createElement('div');
        card.className = 'card border-0 shadow-sm section-card mb-2';
        card.innerHTML = `
        <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
            <div style="width:36px;height:36px;border-radius:8px;background:${def.color}22;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                <i class="fas ${def.icon}" style="color:${def.color};font-size:14px"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold small">${def.label}</div>
                <div class="text-muted small text-truncate" style="max-width:400px">${esc(preview)}</div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <button onclick="editSection(${idx})" class="btn btn-sm btn-outline-primary" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button onclick="moveSection(${idx},-1)" class="btn btn-sm btn-outline-secondary" ${idx===0?'disabled':''}>
                    <i class="fas fa-chevron-up"></i>
                </button>
                <button onclick="moveSection(${idx},1)" class="btn btn-sm btn-outline-secondary" ${idx===sections.length-1?'disabled':''}>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <button onclick="removeSection(${idx})" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
        list.appendChild(card);
    });
}

// ── Section CRUD ───────────────────────────────────────────────────────
function addSection(type) {
    const def = DEFS[type];
    sections.push({ type, config: JSON.parse(JSON.stringify(def.defaults)) });
    renderSections();
    // Auto-open editor for the new section
    editSection(sections.length - 1);
}

function removeSection(idx) {
    if (!confirm('Remove this section?')) return;
    sections.splice(idx, 1);
    renderSections();
}

function moveSection(idx, dir) {
    const to = idx + dir;
    if (to < 0 || to >= sections.length) return;
    [sections[idx], sections[to]] = [sections[to], sections[idx]];
    renderSections();
}

// ── Edit modal ─────────────────────────────────────────────────────────
function editSection(idx) {
    editIdx = idx;
    const sec = sections[idx];
    const def = DEFS[sec.type];

    document.getElementById('editorTitle').innerHTML =
        `<i class="fas ${def.icon} me-2" style="color:${def.color}"></i>Edit – ${def.label}`;

    let html = '';
    for (const [key, field] of Object.entries(def.fields)) {
        html += field.type === 'repeater'
            ? buildRepeaterField(key, field, sec.config[key] || [])
            : buildSimpleField(key, field, sec.config[key] ?? '');
    }
    document.getElementById('editorFields').innerHTML = html;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('editorModal')).show();
}

function saveEdit() {
    const sec = sections[editIdx];
    const def = DEFS[sec.type];
    const fields = document.getElementById('editorFields');
    const cfg = {};

    for (const [key, field] of Object.entries(def.fields)) {
        if (field.type === 'repeater') {
            cfg[key] = collectRepeater(fields, key, field.sub_fields);
        } else {
            const el = fields.querySelector(`[data-key="${key}"]`);
            cfg[key] = el ? el.value : '';
        }
    }
    sections[editIdx].config = cfg;
    renderSections();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editorModal')).hide();
}

// ── Field builders ─────────────────────────────────────────────────────
function buildSimpleField(key, field, val) {
    const ph = field.placeholder ? `placeholder="${esc(field.placeholder)}"` : '';
    if (field.type === 'select') {
        let opts = '';
        for (const [v, l] of Object.entries(field.options)) {
            opts += `<option value="${esc(v)}" ${val===v?'selected':''}>${esc(l)}</option>`;
        }
        return `<div class="mb-3"><label class="form-label fw-bold small">${esc(field.label)}</label>
            <select data-key="${key}" class="form-select form-select-sm">${opts}</select></div>`;
    }
    if (field.type === 'textarea') {
        return `<div class="mb-3"><label class="form-label fw-bold small">${esc(field.label)}</label>
            <textarea data-key="${key}" class="form-control form-control-sm" rows="3" ${ph}>${esc(val)}</textarea></div>`;
    }
    return `<div class="mb-3"><label class="form-label fw-bold small">${esc(field.label)}</label>
        <input type="text" data-key="${key}" class="form-control form-control-sm" value="${esc(val)}" ${ph}></div>`;
}

function buildRepeaterField(key, field, items) {
    let itemsHtml = '';
    items.forEach((item, i) => { itemsHtml += buildRepItem(key, field.sub_fields, item, i); });

    return `<div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-bold small mb-0">${esc(field.label)}</label>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRepItem('${key}')">
                <i class="fas fa-plus me-1"></i>Add Item
            </button>
        </div>
        <div id="rep-${key}" class="repeater-wrap">${itemsHtml}</div>
    </div>`;
}

function buildRepItem(key, subFields, data, idx) {
    let inner = `<div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small fw-bold">Item ${idx+1}</span>
        <button type="button" class="btn btn-sm btn-outline-danger" style="padding:1px 7px;font-size:12px"
            onclick="this.closest('.rep-item').remove();reindexRep('${key}')">✕</button>
    </div><div class="row g-2">`;
    for (const [sf, sfDef] of Object.entries(subFields)) {
        const val = data[sf] ?? '';
        const ph  = sfDef.placeholder ? `placeholder="${esc(sfDef.placeholder)}"` : '';
        const col = sfDef.type === 'textarea' ? 'col-12' : 'col-md-6';
        if (sfDef.type === 'textarea') {
            inner += `<div class="${col}"><label class="form-label form-label-sm text-muted mb-1" style="font-size:11px">${esc(sfDef.label)}</label>
                <textarea data-sf="${sf}" class="form-control form-control-sm" rows="2" ${ph}>${esc(val)}</textarea></div>`;
        } else {
            inner += `<div class="${col}"><label class="form-label form-label-sm text-muted mb-1" style="font-size:11px">${esc(sfDef.label)}</label>
                <input type="text" data-sf="${sf}" class="form-control form-control-sm" value="${esc(val)}" ${ph}></div>`;
        }
    }
    inner += `</div>`;
    return `<div class="rep-item border rounded p-3 mb-2" data-rep-key="${key}">${inner}</div>`;
}

function addRepItem(key) {
    const wrap = document.getElementById(`rep-${key}`);
    const def  = DEFS[sections[editIdx].type];
    const field = def.fields[key];
    const idx   = wrap.querySelectorAll('.rep-item').length;
    wrap.insertAdjacentHTML('beforeend', buildRepItem(key, field.sub_fields, {}, idx));
}

function reindexRep(key) {
    document.querySelectorAll(`[data-rep-key="${key}"] .d-flex .text-muted.fw-bold`).forEach((el, i) => {
        el.textContent = `Item ${i+1}`;
    });
}

function collectRepeater(container, key, subFields) {
    const items = [];
    container.querySelectorAll(`[data-rep-key="${key}"]`).forEach(item => {
        const obj = {};
        for (const sf of Object.keys(subFields)) {
            const el = item.querySelector(`[data-sf="${sf}"]`);
            obj[sf] = el ? el.value : '';
        }
        items.push(obj);
    });
    return items;
}

// ── Helpers ────────────────────────────────────────────────────────────
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Form submit ────────────────────────────────────────────────────────
document.getElementById('builderForm').addEventListener('submit', function () {
    document.getElementById('sectionsJson').value = JSON.stringify(sections);
});

// ── Init ───────────────────────────────────────────────────────────────
renderSections();
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
