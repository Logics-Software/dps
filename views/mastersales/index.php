<?php
$title = 'Master Sales';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
	$baseUrl = '/';
}

if (!function_exists('getSortUrlMastersales')) {
	function getSortUrlMastersales($column, $currentSortBy, $currentSortOrder, $search, $perPage, $status) {
		$newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
		$params = http_build_query([
			'page' => 1,
			'per_page' => $perPage,
			'search' => $search,
			'status' => $status,
			'sort_by' => $column,
			'sort_order' => $newSortOrder
		]);
		return '/mastersales?' . $params;
	}
}

if (!function_exists('getSortIconMastersales')) {
	function getSortIconMastersales($column, $currentSortBy, $currentSortOrder) {
		$config = require __DIR__ . '/../../config/app.php';
		$baseUrl = rtrim($config['base_url'], '/');
		if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
			$baseUrl = '/';
		}

		if ($currentSortBy != $column) {
			$iconPath = $baseUrl . '/assets/icons/arrows-up-down.svg';
			return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort" class="sort-icon icon-inline" width="14" height="14">';
		}

		if ($currentSortOrder == 'ASC') {
			$iconPath = $baseUrl . '/assets/icons/arrow-up.svg';
			return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort-up" class="sort-icon icon-inline" width="14" height="14">';
		}

		$iconPath = $baseUrl . '/assets/icons/arrow-down.svg';
		return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort-down" class="sort-icon icon-inline" width="14" height="14">';
	}
}

require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
	<div class="breadcrumb-item">
		<div class="col-12">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
					<li class="breadcrumb-item active">Master Sales</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">

				<div class="card-header">
					<div class="d-flex align-items-center">
						<h4 class="mb-0">Daftar Sales</h4>
					</div>
				</div>
				
				<div class="card-body">
					<div class="row mb-3">
						<form method="GET" action="/mastersales" id="searchForm">
							<div class="row g-2 align-items-end">
								<div class="col-12 col-md-4">
									<input type="text" class="form-control" name="search" placeholder="Cari kode, nama, atau alamat sales..." value="<?= htmlspecialchars($search) ?>">
								</div>
								<div class="col-6 col-md-2">
									<select name="status" class="form-select" onchange="this.form.submit()">
										<option value="" <?= $status === '' ? 'selected' : '' ?>>Semua Status</option>
										<option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
										<option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
									</select>
								</div>
								<div class="col-6 col-md-2">
									<select name="per_page" class="form-select" onchange="this.form.submit()">
										<?php foreach ([10, 25, 50, 100, 200, 500, 1000] as $pp): ?>
										<option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-6 col-md-2">
									<button type="submit" class="btn btn-filter btn-secondary w-100">Filter</button>
								</div>
								<div class="col-6 col-md-2">
									<a href="/mastersales?page=1&per_page=10&status=&sort_by=<?= htmlspecialchars($sortBy) ?>&sort_order=<?= htmlspecialchars($sortOrder) ?>" class="btn btn-filter btn-outline-secondary w-100">Reset</a>
								</div>
							</div>
							<input type="hidden" name="page" value="1">
							<input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
							<input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder) ?>">
						</form>
					</div>

					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th class="th-sortable">
										<a href="<?= getSortUrlMastersales('kodesales', $sortBy, $sortOrder, $search, $perPage, $status) ?>">
											Kode
										</a>
									</th>
									<th class="th-sortable">
										<a href="<?= getSortUrlMastersales('namasales', $sortBy, $sortOrder, $search, $perPage, $status) ?>">
											Nama Sales
										</a>
									</th>
									<th>Alamat</th>
									<th>No Telepon</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($items)): ?>
								<tr>
									<td colspan="7" class="text-center">Tidak ada data</td>
								</tr>
								<?php else: ?>
								<?php foreach ($items as $row): ?>
								<tr>
									<td><?= htmlspecialchars($row['kodesales'] ?? '') ?></td>
									<td><?= htmlspecialchars($row['namasales'] ?? '') ?></td>
									<td><?= htmlspecialchars($row['alamatsales'] ?? '-') ?></td>
									<td><?= htmlspecialchars($row['notelepon'] ?? '-') ?></td>
									<td align="center">
										<span class="badge bg-<?= (strtolower($row['status'] ?? '') === 'aktif') ? 'success' : 'danger' ?>">
											<?= htmlspecialchars(ucfirst($row['status'] ?? '-')) ?>
										</span>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<?php if ($totalPages > 1): ?>
					<nav>
						<ul class="pagination justify-content-center">
							<li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
								<a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">Previous</a>
							</li>
							<?php
							$maxLinks = 3;
							$half = (int)floor($maxLinks / 2);
							$start = max(1, $page - $half);
							$end = min($totalPages, $start + $maxLinks - 1);
							if ($end - $start + 1 < $maxLinks) {
								$start = max(1, $end - $maxLinks + 1);
							}
							$buildLink = function ($p) use ($perPage, $search) {
								return '?page=' . $p . '&per_page=' . $perPage . '&search=' . urlencode($search);
							};
							if ($start > 1) {
								echo '<li class="page-item"><a class="page-link" href="' . $buildLink(1) . '">1</a></li>';
								if ($start > 2) {
									echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
								}
							}
							for ($i = $start; $i <= $end; $i++) {
								echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="' . $buildLink($i) . '">' . $i . '</a></li>';
							}
							if ($end < $totalPages) {
								if ($end < $totalPages - 1) {
									echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
								}
								echo '<li class="page-item"><a class="page-link" href="' . $buildLink($totalPages) . '">' . $totalPages . '</a></li>';
							}
							?>
							<li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
								<a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">Next</a>
							</li>
						</ul>
					</nav>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>