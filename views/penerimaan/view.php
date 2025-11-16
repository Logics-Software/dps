<?php
$title = 'Detail Penerimaan Piutang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/penerimaan">Penerimaan Piutang</a></li>
				<li class="breadcrumb-item active">Detail</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h4 class="mb-0">No: <?= htmlspecialchars($penerimaan['nopenerimaan'] ?? '') ?></h4>
		<a href="/penerimaan" class="btn btn-secondary btn-sm">Kembali</a>
	</div>
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-md-3"><strong>Tanggal</strong><br><?= htmlspecialchars(date('d/m/Y', strtotime($penerimaan['tanggalpenerimaan'] ?? date('Y-m-d')))) ?></div>
			<div class="col-md-3"><strong>Customer</strong><br><?= htmlspecialchars($penerimaan['namacustomer'] ?? '-') ?></div>
			<div class="col-md-3"><strong>Sales</strong><br><?= htmlspecialchars($penerimaan['namasales'] ?? '-') ?></div>
			<div class="col-md-3"><strong>Status</strong><br><span class="badge bg-<?= ($penerimaan['status'] ?? '') === 'proses' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($penerimaan['status'] ?? '')) ?></span></div>
		</div>

		<div class="table-responsive">
			<table class="table table-striped align-middle">
				<thead>
					<tr>
						<th>No Faktur</th>
						<th class="text-end">Piutang</th>
						<th class="text-end">Potongan</th>
						<th class="text-end">Lain-lain</th>
						<th class="text-end">Netto</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (($details ?? []) as $d): ?>
					<tr>
						<td><?= htmlspecialchars($d['nopenjualan'] ?? '') ?></td>
						<td class="text-end"><?= number_format((float)($d['piutang'] ?? 0), 0, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['potongan'] ?? 0), 0, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['lainlain'] ?? 0), 0, ',', '.') ?></td>
						<td class="text-end"><?= number_format((float)($d['netto'] ?? 0), 0, ',', '.') ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


