<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$tags = $pdo->query('SELECT * FROM blog_tags ORDER BY name')->fetchAll();
?>
<?php
$pageTitle = 'Manage Tags';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 mb-4">
	<div class="card-header">
		<i class="fas fa-tags me-2"></i>Add Tag
	</div>
	<div class="card-body">
		<form method="post" action="tag_save.php" class="row g-3">
			<div class="col-md-5">
				<input type="text" name="name" placeholder="Tag name" class="form-control" required>
			</div>
			<div class="col-md-5">
				<input type="text" name="slug" placeholder="Slug (optional)" class="form-control">
			</div>
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary w-100">Add</button>
			</div>
		</form>
	</div>
</div>

<div class="card border-0">
	<div class="card-header">
		<i class="fas fa-list me-2"></i>Tag List
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-hover mb-0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Slug</th>
						<th class="text-end">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($tags as $tag): ?>
					<tr>
						<td><?= htmlspecialchars($tag['name']) ?></td>
						<td><?= htmlspecialchars($tag['slug']) ?></td>
						<td class="text-end">
							<a href="tag_delete.php?id=<?= $tag['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete tag?')">
								<i class="fas fa-trash"></i>
							</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include __DIR__ . '/../views/admin_footer.php'; ?>
