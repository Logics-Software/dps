<?php
$title = 'Transaksi Penjualan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item active">Transaksi Penjualan</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h4 class="mb-0">Daftar Penjualan</h4>
	</div>
	<div class="card-body">
		<form method="GET" class="row g-2 mb-3">
			<div class="col-12 col-md-4">
				<input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="form-control" placeholder="Cari no faktur / customer...">
			</div>
			<div class="col-6 col-md-2">
				<select name="periode" class="form-select">
					<?php foreach (['today'=>'Hari ini','week'=>'Minggu ini','month'=>'Bulan ini','year'=>'Tahun ini','custom'=>'Custom'] as $k=>$v): ?>
					<option value="<?= $k ?>" <?= ($periode ?? 'today') === $k ? 'selected' : '' ?>><?= $v ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-6 col-md-2">
				<select name="per_page" class="form-select">
					<?php foreach (($perPageOptions ?? [10,20,50,75,100]) as $pp): ?>
					<option value="<?= $pp ?>" <?= ($perPage ?? 10) == $pp ? 'selected' : '' ?>><?= $pp ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-12 col-md-4 d-grid d-md-block">
				<button class="btn btn-secondary me-2" type="submit">Terapkan</button>
				<a class="btn btn-outline-secondary" href="/penjualan">Reset</a>
			</div>
		</form>

		<div class="table-responsive">
			<table class="table table-striped align-middle">
				<thead>
					<tr>
						<th>No Faktur</th>
						<th>Tanggal</th>
						<th>Customer</th>
						<th>Sales</th>
						<th class="text-end">Nilai</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($penjualan)): ?>
					<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
				<?php else: foreach ($penjualan as $row): ?>
					<tr>
						<td class="fw-semibold"><?= htmlspecialchars($row['nopenjualan']) ?></td>
						<td><?= htmlspecialchars(date('d/m/Y', strtotime($row['tanggalpenjualan']))) ?></td>
						<td><?= htmlspecialchars($row['namacustomer'] ?? '-') ?></td>
						<td><?= htmlspecialchars($row['namasales'] ?? '-') ?></td>
						<td class="text-end"><?= number_format((float)($row['nilaipenjualan'] ?? 0), 0, ',', '.') ?></td>
						<td><a href="/penjualan/view/<?= urlencode($row['nopenjualan']) ?>" class="btn btn-sm btn-info text-white">Lihat</a></td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


