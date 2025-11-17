<?php
$title = 'Transaksi Order';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

// Helper function to generate sort URL
if (!function_exists('getSortUrl')) {
    function getSortUrl($column, $currentSortBy, $currentSortOrder, $search, $status, $dateFilter, $rawStartDate, $rawEndDate, $perPage) {
        $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
        $params = [
            'page' => 1,
            'per_page' => $perPage,
            'search' => $search,
            'status' => $status,
            'periode' => $dateFilter,
            'sort_by' => $column,
            'sort_order' => $newSortOrder
        ];
        if ($dateFilter === 'custom' && !empty($rawStartDate)) {
            $params['start_date'] = $rawStartDate;
        }
        if ($dateFilter === 'custom' && !empty($rawEndDate)) {
            $params['end_date'] = $rawEndDate;
        }
        return '/orders?' . http_build_query(array_filter($params));
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
					<li class="breadcrumb-item active">Transaksi Order</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="card">
		<div class="card-header d-flex justify-content-between align-items-center">
			<h4 class="mb-0 me-auto">Daftar Order</h4>
			<?php if (Auth::isSales()): ?>
			<a href="/orders/create" class="btn btn-primary btn-sm"><?= icon('square-plus', 'me-1 mb-1', 18) ?> Buat Order</a>
			<?php endif; ?>
		</div>

		<div class="card-body">
			<form method="GET" class="row g-2 mb-3" id="filterForm">
				<div class="col-6 col-md-3">
					<input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="form-control" placeholder="Cari customer...">
				</div>
				<div class="col-6 col-md-2">
					<select name="status" class="form-select">
						<option value="">Semua Status</option>
						<option value="order" <?= ($status ?? '') === 'order' ? 'selected' : '' ?>>Order</option>
						<option value="faktur" <?= ($status ?? '') === 'faktur' ? 'selected' : '' ?>>Faktur</option>
					</select>
				</div>
				<div class="col-6 col-md-2">
					<select name="periode" id="periodeFilter" class="form-select" onchange="toggleCustomDate()">
						<?php 
						$periodeOptions = [
							'today' => 'Hari ini',
							'week' => 'Minggu ini',
							'month' => 'Bulan ini',
							'year' => 'Tahun ini',
							'custom' => 'Custom'
						];
						$currentPeriode = $dateFilter ?? 'today';
						foreach ($periodeOptions as $key => $label): 
						?>
						<option value="<?= $key ?>" <?= $currentPeriode === $key ? 'selected' : '' ?>><?= $label ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-6 col-md-2" id="customDateContainer" style="display: <?= ($dateFilter ?? 'today') === 'custom' ? 'block' : 'none' ?>;">
					<input type="date" name="start_date" value="<?= htmlspecialchars($rawStartDate ?? '') ?>" class="form-control" placeholder="Dari">
				</div>
				<div class="col-6 col-md-2" id="customDateEndContainer" style="display: <?= ($dateFilter ?? 'today') === 'custom' ? 'block' : 'none' ?>;">
					<input type="date" name="end_date" value="<?= htmlspecialchars($rawEndDate ?? '') ?>" class="form-control" placeholder="Sampai">
				</div>
				<div class="col-6 col-md-2">
					<select name="per_page" class="form-select">
						<?php foreach ([10, 25, 50, 100, 200, 500, 1000] as $pp): ?>
						<option value="<?= $pp ?>" <?= ($perPage ?? 10) == $pp ? 'selected' : '' ?>><?= $pp ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-6 col-md-auto d-grid d-md-block">
					<button class="btn btn-filter btn-secondary w-100" type="submit"><?= icon('filter', 'me-2 mb-0', 18) ?> Terapkan</button>
				</div>
				<div class="col-6 col-md-auto d-grid d-md-block">
					<a class="btn btn-filter btn-outline-secondary w-100" href="/orders"><?= icon('filter-circle-xmark', 'me-2 mb-0', 18) ?> Reset</a>
				</div>
				<input type="hidden" name="page" value="1">
				<input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy ?? 'tanggalorder') ?>">
				<input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder ?? 'DESC') ?>">
			</form>

			<div class="table-responsive">
				<table class="table table-striped align-middle">
					<thead>
						<tr>
							<th class="th-sortable <?= ($sortBy ?? 'tanggalorder') === 'noorder' ? (($sortOrder ?? 'DESC') === 'ASC' ? 'sorted-asc' : 'sorted-desc') : '' ?>">
								<a href="<?= getSortUrl('noorder', $sortBy ?? 'tanggalorder', $sortOrder ?? 'DESC', $search ?? '', $status ?? '', $dateFilter ?? 'today', $rawStartDate ?? '', $rawEndDate ?? '', $perPage ?? 10) ?>" class="text-decoration-none text-dark">
									No Order
								</a>
							</th>
							<th class="th-sortable <?= ($sortBy ?? 'tanggalorder') === 'tanggalorder' ? (($sortOrder ?? 'DESC') === 'ASC' ? 'sorted-asc' : 'sorted-desc') : '' ?>">
								<a href="<?= getSortUrl('tanggalorder', $sortBy ?? 'tanggalorder', $sortOrder ?? 'DESC', $search ?? '', $status ?? '', $dateFilter ?? 'today', $rawStartDate ?? '', $rawEndDate ?? '', $perPage ?? 10) ?>" class="text-decoration-none text-dark">
									Tanggal
								</a>
							</th>
							<th class="th-sortable <?= ($sortBy ?? 'tanggalorder') === 'namacustomer' ? (($sortOrder ?? 'DESC') === 'ASC' ? 'sorted-asc' : 'sorted-desc') : '' ?>">
								<a href="<?= getSortUrl('namacustomer', $sortBy ?? 'tanggalorder', $sortOrder ?? 'DESC', $search ?? '', $status ?? '', $dateFilter ?? 'today', $rawStartDate ?? '', $rawEndDate ?? '', $perPage ?? 10) ?>" class="text-decoration-none text-dark">
									Customer
								</a>
							</th>
							<th>Alamat</th>
							<th class="text-end">Nilai</th>
							<th>Status</th>
							<th class="th-sortable <?= ($sortBy ?? 'tanggalorder') === 'nopenjualan' ? (($sortOrder ?? 'DESC') === 'ASC' ? 'sorted-asc' : 'sorted-desc') : '' ?>">
								<a href="<?= getSortUrl('nopenjualan', $sortBy ?? 'tanggalorder', $sortOrder ?? 'DESC', $search ?? '', $status ?? '', $dateFilter ?? 'today', $rawStartDate ?? '', $rawEndDate ?? '', $perPage ?? 10) ?>" class="text-decoration-none text-dark">
									No.Faktur
								</a>
							</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
					<?php if (empty($orders)): ?>
						<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
					<?php else: foreach ($orders as $row): ?>
						<tr>
							<td class="fw-semibold"><?= htmlspecialchars($row['noorder']) ?></td>
							<td><?= htmlspecialchars(date('d/m/Y', strtotime($row['tanggalorder']))) ?></td>
							<td><?= htmlspecialchars(($row['namacustomer'] ?? '') . (!empty($row['namabadanusaha']) ? ', ' . $row['namabadanusaha'] : '')) ?></td>
							<td><?= htmlspecialchars(($row['alamatcustomer'] ?? '') . (!empty($row['kota']) ? ', ' . $row['kota'] : '')) ?></td>
							<td class="text-end"><?= number_format((float)($row['nilaiorder'] ?? 0), 0, ',', '.') ?></td>
							<td align="center"><span class="badge bg-<?= ($row['status'] ?? '') === 'faktur' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></span></td>
							<td><?= htmlspecialchars(($row['nopenjualan'] ?? '-')) ?></td>
							<td>
								<div class="d-flex gap-1">
									<a href="/orders/view/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-info text-white"><?= icon('show', 'mb-0', 16) ?></a>
									<?php if (($row['status'] ?? '') === 'order'): ?>
									<a href="/orders/edit/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-warning"><?= icon('pen-to-square', 'mb-0', 16) ?></a>
									<a href="/orders/delete/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-danger" onclick="event.preventDefault(); confirmDelete('Apakah Anda yakin ingin menghapus order <strong><?= htmlspecialchars($row['noorder']) ?></strong>?', this.href); return false;"><?= icon('trash-can', 'mb-0', 16) ?></a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endforeach; endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
function toggleCustomDate() {
	const periodeFilter = document.getElementById('periodeFilter');
	const customDateContainer = document.getElementById('customDateContainer');
	const customDateEndContainer = document.getElementById('customDateEndContainer');
	
	if (periodeFilter.value === 'custom') {
		customDateContainer.style.display = 'block';
		customDateEndContainer.style.display = 'block';
	} else {
		customDateContainer.style.display = 'none';
		customDateEndContainer.style.display = 'none';
	}
}

document.addEventListener('DOMContentLoaded', function() {
	toggleCustomDate();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


