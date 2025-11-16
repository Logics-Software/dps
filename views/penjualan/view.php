<?php
$title = 'Detail Penjualan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/penjualan">Transaksi Penjualan</a></li>
				<li class="breadcrumb-item active">Detail Penjualan</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h4 class="mb-0">Faktur: <?= htmlspecialchars($penjualan['nopenjualan'] ?? '') ?></h4>
		<a href="/penjualan" class="btn btn-secondary btn-sm">Kembali</a>
	</div>
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-md-3"><strong>Tanggal</strong><br><?= htmlspecialchars(date('d/m/Y', strtotime($penjualan['tanggalpenjualan'] ?? date('Y-m-d')))) ?></div>
			<div class="col-md-3"><strong>Customer</strong><br><?= htmlspecialchars($penjualan['namacustomer'] ?? '-') ?></div>
			<div class="col-md-3"><strong>Sales</strong><br><?= htmlspecialchars($penjualan['namasales'] ?? '-') ?></div>
			<div class="col-md-3"><strong>Nilai</strong><br><?= number_format((float)($penjualan['nilaipenjualan'] ?? 0), 0, ',', '.') ?></div>
		</div>

		<div class="table-responsive">
			<table class="table table-striped align-middle">
				<thead>
					<tr>
						<th>Kode</th>
						<th>Nama</th>
						<th class="text-end">Jumlah</th>
						<th class="text-end">Harga</th>
						<th class="text-end">Diskon</th>
						<th class="text-end">Jumlah Harga</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (($details ?? []) as $d): ?>
					<tr>
						<td><?= htmlspecialchars($d['kodebarang'] ?? '') ?></td>
						<td><?= htmlspecialchars($d['namabarang'] ?? '') ?></td>
						<td class="text-end"><?= number_format((float)($d['jumlah'] ?? 0), 0, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['hargasatuan'] ?? 0), 0, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['discount'] ?? 0), 2, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['jumlahharga'] ?? 0), 0, ',', '.') ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


