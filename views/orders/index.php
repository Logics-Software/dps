<?php
$title = 'Transaksi Order';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
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
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h4 class="mb-0">Daftar Order</h4>
		<?php if (Auth::isSales()): ?>
		<a href="/orders/create" class="btn btn-primary btn-sm">Buat Order</a>
		<?php endif; ?>
	</div>
	<div class="card-body">
		<form method="GET" class="row g-2 mb-3">
			<div class="col-12 col-md-4">
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
				<select name="per_page" class="form-select">
					<?php foreach ([10,20,40,60,100] as $pp): ?>
					<option value="<?= $pp ?>" <?= ($perPage ?? 10) == $pp ? 'selected' : '' ?>><?= $pp ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-12 col-md-4 d-grid d-md-block">
				<button class="btn btn-secondary me-2" type="submit">Terapkan</button>
				<a class="btn btn-outline-secondary" href="/orders">Reset</a>
			</div>
		</form>

		<div class="table-responsive">
			<table class="table table-striped align-middle">
				<thead>
					<tr>
						<th>No Order</th>
						<th>Tanggal</th>
						<th>Customer</th>
						<th class="text-end">Nilai</th>
						<th>Status</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($orders)): ?>
					<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
				<?php else: foreach ($orders as $row): ?>
					<tr>
						<td class="fw-semibold"><?= htmlspecialchars($row['noorder']) ?></td>
						<td><?= htmlspecialchars(date('d/m/Y', strtotime($row['tanggalorder']))) ?></td>
						<td><?= htmlspecialchars($row['namacustomer'] ?? '-') ?></td>
						<td class="text-end"><?= number_format((float)($row['nilaiorder'] ?? 0), 0, ',', '.') ?></td>
						<td><span class="badge bg-<?= ($row['status'] ?? '') === 'faktur' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></span></td>
						<td>
							<a href="/orders/view/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-info text-white">Lihat</a>
							<?php if (($row['status'] ?? '') === 'order'): ?>
							<a href="/orders/edit/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-warning">Ubah</a>
							<a href="/orders/delete/<?= urlencode($row['noorder']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus order ini?')">Hapus</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


