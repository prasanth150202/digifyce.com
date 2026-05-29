<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$authors = $pdo->query('SELECT * FROM blog_authors ORDER BY name')->fetchAll();
?>
<?php
$pageTitle = 'Manage Authors';
include __DIR__ . '/../views/admin_header.php';
?>

<div class="card border-0 mb-4">
	<div class="card-header">
		<i class="fas fa-user-plus me-2"></i>Add Author
	</div>
	<div class="card-body">
		<form method="post" action="author_save.php" enctype="multipart/form-data" class="row g-3">
			<div class="col-md-6">
				<input type="text" name="name" placeholder="Author name" class="form-control" required>
			</div>
			<div class="col-md-6">
				<input type="file" name="avatar" class="form-control">
			</div>
			<div class="col-12">
				<textarea name="bio" placeholder="Bio" class="form-control" rows="3"></textarea>
			</div>
			<div class="col-12">
				<button type="submit" class="btn btn-primary">Add Author</button>
			</div>
		</form>
	</div>
</div>

<div class="card border-0">
	<div class="card-header">
		<i class="fas fa-users me-2"></i>Author List
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-hover mb-0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Avatar</th>
						<th class="text-end">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($authors as $author): ?>
					<tr>
						<td><?= htmlspecialchars($author['name']) ?></td>
						<td>
							<?php if ($author['avatar_url']): ?>
								<img src="<?= $appUrl ?>/storage/uploads/<?= htmlspecialchars($author['avatar_url']) ?>" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
							<?php else: ?>
								<span class="text-muted">—</span>
							<?php endif; ?>
						</td>
						<td class="text-end">
							<a href="author_delete.php?id=<?= $author['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete author?')">
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
