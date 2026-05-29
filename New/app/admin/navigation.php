<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Navigation Management';
include __DIR__ . '/../views/admin_header.php';

$pdo = Database::getInstance();
$navItems = $pdo->query('SELECT id, label, url, position, is_footer, parent_id, footer_group FROM navigation ORDER BY is_footer ASC, position ASC')->fetchAll();

// Separate header and footer nav
$headerNav = array_filter($navItems, fn($item) => !$item['is_footer']);
$footerNav = array_filter($navItems, fn($item) => $item['is_footer']);
$navMap = [];
foreach ($navItems as $item) {
    $navMap[$item['id']] = $item;
}
$headerParents = array_filter($headerNav, fn($item) => empty($item['parent_id']));

$navCtaLabel = 'Audit';
$navCtaUrl = '#';
try {
    $settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('nav_cta_label','nav_cta_url')")->fetchAll();
    foreach ($settings as $row) {
        if ($row['setting_key'] === 'nav_cta_label' && $row['setting_value'] !== '') {
            $navCtaLabel = $row['setting_value'];
        }
        if ($row['setting_key'] === 'nav_cta_url' && $row['setting_value'] !== '') {
            $navCtaUrl = $row['setting_value'];
        }
    }
} catch (Exception $e) {
    // Use defaults
}
?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4">Navigation Management</h1>

    <!-- Header Navigation -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Header Navigation Menu</h5>
        </div>
        <div class="card-body">
            <button class="btn btn-success mb-3" onclick="openModal('header')">+ Add Header Menu Item</button>
            <p class="text-muted small mb-3">Drag items to reorder</p>
            
            <div id="headerNavList" class="list-group sortable-list" data-type="header">
                <?php foreach ($headerNav as $item): ?>
                    <?php
                        $label = $item['label'];
                        $parentLabel = '';
                        if (!empty($item['parent_id']) && isset($navMap[$item['parent_id']])) {
                            $parentLabel = $navMap[$item['parent_id']]['label'];
                            $label = $parentLabel . ' / ' . $label;
                        }
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $item['id'] ?>" draggable="true">
                        <div>
                            <span class="handle me-2" style="cursor: move; color: #999;">⋮⋮</span>
                            <h6 class="mb-1 d-inline"><?= htmlspecialchars($label) ?></h6>
                            <small class="text-muted d-block"><?= htmlspecialchars($item['url']) ?></small>
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning" onclick="editNav(<?= $item['id'] ?>, 'header')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteNav(<?= $item['id'] ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (empty($headerNav)): ?>
                <p class="text-muted mt-3">No header menu items yet. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Header CTA Settings -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Header CTA</h5>
        </div>
        <div class="card-body">
            <form method="post" action="site_settings_save.php" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">CTA Title</label>
                    <input type="text" name="nav_cta_label" class="form-control" value="<?= htmlspecialchars($navCtaLabel) ?>" placeholder="Audit">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CTA Link</label>
                    <input type="text" name="nav_cta_url" class="form-control" value="<?= htmlspecialchars($navCtaUrl) ?>" placeholder="/contact.php or https://...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save CTA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Navigation -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Footer Navigation Links</h5>
        </div>
        <div class="card-body">
            <button class="btn btn-success mb-3" onclick="openModal('footer')">+ Add Footer Link</button>
            <p class="text-muted small mb-3">Drag items to reorder</p>
            
            <div id="footerNavList" class="list-group sortable-list" data-type="footer">
                <?php foreach ($footerNav as $item): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $item['id'] ?>" draggable="true">
                        <div>
                            <span class="handle me-2" style="cursor: move; color: #999;">⋮⋮</span>
                            <h6 class="mb-1 d-inline"><?= htmlspecialchars($item['label']) ?></h6>
                            <small class="text-muted d-block"><?= htmlspecialchars($item['url']) ?></small>
                            <span class="badge bg-info"><?= htmlspecialchars($item['footer_group'] ?? 'Ungrouped') ?></span>
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning" onclick="editNav(<?= $item['id'] ?>, 'footer')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteNav(<?= $item['id'] ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (empty($footerNav)): ?>
                <p class="text-muted mt-3">No footer links yet. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit -->
<div class="modal fade" id="navModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Navigation Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="navForm">
                    <input type="hidden" id="navId" value="">
                    <input type="hidden" id="navType" value="">
                    
                    <div class="mb-3">
                        <label for="navLabel" class="form-label">Menu Label</label>
                        <input type="text" class="form-control" id="navLabel" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="navUrl" class="form-label">URL/Path</label>
                        <input type="text" class="form-control" id="navUrl" placeholder="e.g., /service.php, #methodology" required>
                    </div>

                    <div class="mb-3" id="parentField">
                        <label for="navParent" class="form-label">Parent Menu (optional)</label>
                        <select class="form-select" id="navParent">
                            <option value="">No Parent</option>
                            <?php foreach ($headerParents as $parent): ?>
                                <option value="<?= $parent['id'] ?>"><?= htmlspecialchars($parent['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="footerGroupField" style="display: none;">
                        <label for="navFooterGroup" class="form-label">Footer Category</label>
                        <input type="text" class="form-control" id="navFooterGroup" placeholder="e.g., Services, Company, Resources">
                        <small class="text-muted">Group this link under one of 3 main footer sections</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNav()">Save Item</button>
            </div>
        </div>
    </div>
</div>

<script>
const apiUrl = '<?= $appUrl ?>/app/api/navigation_admin.php';
let currentModal = null;
let editingId = null;
let draggedItem = null;

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    initDragAndDrop();
});

function initDragAndDrop() {
    const lists = document.querySelectorAll('.sortable-list');
    lists.forEach(list => {
        const items = list.querySelectorAll('.list-group-item');
        items.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('drop', handleDrop);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
        });
    });
}

function handleDragStart(e) {
    draggedItem = this;
    this.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    draggedItem = null;
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.style.opacity = '1';
        item.style.borderTop = '';
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    if (this !== draggedItem) {
        this.style.borderTop = '3px solid #007bff';
    }
}

function handleDragLeave(e) {
    this.style.borderTop = '';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    if (this !== draggedItem) {
        const list = this.parentNode;
        const allItems = Array.from(list.querySelectorAll('.list-group-item'));
        const draggedIndex = allItems.indexOf(draggedItem);
        const targetIndex = allItems.indexOf(this);
        
        if (draggedIndex < targetIndex) {
            this.parentNode.insertBefore(draggedItem, this.nextSibling);
        } else {
            this.parentNode.insertBefore(draggedItem, this);
        }
        
        saveOrder(list);
    }
    return false;
}

function saveOrder(list) {
    const type = list.getAttribute('data-type');
    const items = Array.from(list.querySelectorAll('.list-group-item')).map((item, index) => ({
        id: parseInt(item.getAttribute('data-id')),
        position: index + 1
    }));
    
    fetch(`${apiUrl}?action=reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            alert('Error saving order');
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}


function openModal(type) {
    editingId = null;
    document.getElementById('navType').value = type;
    document.getElementById('navId').value = '';
    document.getElementById('navLabel').value = '';
    document.getElementById('navUrl').value = '';
    document.getElementById('navParent').value = '';
    document.getElementById('navFooterGroup').value = '';
    document.getElementById('modalTitle').textContent = type === 'header' ? 'Add Header Menu Item' : 'Add Footer Link';
    toggleParentField(type);
    currentModal = new bootstrap.Modal(document.getElementById('navModal'));
    currentModal.show();
}

function editNav(id, type) {
    fetch(`${apiUrl}?action=list`)
        .then(r => r.json())
        .then(res => {
            if (!res.data || !Array.isArray(res.data)) {
                alert('Error fetching navigation items');
                return;
            }
            const item = res.data.find(i => parseInt(i.id) === parseInt(id));
            if (item) {
                editingId = id;
                document.getElementById('navType').value = type;
                document.getElementById('navId').value = id;
                document.getElementById('navLabel').value = item.label || '';
                document.getElementById('navUrl').value = item.url || '';
                document.getElementById('navParent').value = item.parent_id || '';
                document.getElementById('navFooterGroup').value = item.footer_group || '';
                document.getElementById('modalTitle').textContent = type === 'header' ? 'Edit Header Menu Item' : 'Edit Footer Link';
                toggleParentField(type);
                currentModal = new bootstrap.Modal(document.getElementById('navModal'));
                currentModal.show();
            } else {
                alert('Item not found');
            }
        })
        .catch(err => {
            alert('Error fetching item: ' + err.message);
        });
}

function toggleParentField(type) {
    document.getElementById('parentField').style.display = type === 'header' ? 'block' : 'none';
    document.getElementById('footerGroupField').style.display = type === 'footer' ? 'block' : 'none';
}

function saveNav() {
    const id = document.getElementById('navId').value;
    const label = document.getElementById('navLabel').value;
    const url = document.getElementById('navUrl').value;
    const type = document.getElementById('navType').value;
    const isFooter = type === 'footer';
    const parentIdRaw = document.getElementById('navParent').value;
    const parentId = parentIdRaw ? parseInt(parentIdRaw) : null;
    const footerGroup = document.getElementById('navFooterGroup').value || null;

    if (!label || !url) {
        alert('Please fill in all fields');
        return;
    }

    const action = id ? 'update' : 'add';
    const payload = {
        label,
        url,
        is_footer: isFooter,
        parent_id: isFooter ? null : parentId,
        footer_group: isFooter ? footerGroup : null,
        ...(id && { id: parseInt(id) })
    };

    fetch(`${apiUrl}?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            currentModal.hide();
            location.reload();
        } else {
            alert('Error: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('Error saving: ' + err.message);
    });
}

function deleteNav(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch(`${apiUrl}?action=delete`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                alert('Error: ' + (res.message || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Error deleting: ' + err.message);
        });
    }
}
</script>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
