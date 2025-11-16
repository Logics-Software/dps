<?php
$title = 'Transaksi Penerimaan Piutang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item active">Penerimaan Piutang</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h4 class="mb-0">Daftar Penerimaan</h4>
		<?php if (Auth::isSales()): ?>
		<a href="/penerimaan/create" class="btn btn-primary btn-sm">Buat Penerimaan</a>
		<?php endif; ?>
	</div>
	<div class="card-body">
		<form method="GET" class="row g-2 mb-3">
			<div class="col-12 col-md-4">
				<input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="form-control" placeholder="Cari no/inkaso/customer...">
			</div>
			<div class="col-6 col-md-2">
				<select name="status" class="form-select">
					<option value="">Semua Status</option>
					<option value="belumproses" <?= ($status ?? '') === 'belumproses' ? 'selected' : '' ?>>Belum Proses</option>
					<option value="proses" <?= ($status ?? '') === 'proses' ? 'selected' : '' ?>>Proses</option>
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
				<a class="btn btn-outline-secondary" href="/penerimaan">Reset</a>
			</div>
		</form>

		<div class="table-responsive">
			<table class="table table-striped align-middle">
				<thead>
					<tr>
						<th>No Penerimaan</th>
						<th>Tanggal</th>
						<th>Customer</th>
						<th>Sales</th>
						<th class="text-end">Netto</th>
						<th>Status</th>
						<th>No Inkaso</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($penerimaan)): ?>
					<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
				<?php else: foreach ($penerimaan as $row): ?>
					<tr>
						<td class="fw-semibold"><?= htmlspecialchars($row['nopenerimaan']) ?></td>
						<td><?= htmlspecialchars(date('d/m/Y', strtotime($row['tanggalpenerimaan']))) ?></td>
						<td><?= htmlspecialchars($row['namacustomer'] ?? '-') ?></td>
						<td><?= htmlspecialchars($row['namasales'] ?? '-') ?></td>
						<td class="text-end"><?= number_format((float)($row['totalnetto'] ?? 0), 0, ',', '.') ?></td>
						<td><span class="badge bg-<?= ($row['status'] ?? '') === 'proses' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></span></td>
						<td><?= htmlspecialchars($row['noinkaso'] ?? '-') ?></td>
						<td>
							<a href="/penerimaan/view/<?= urlencode($row['nopenerimaan']) ?>" class="btn btn-sm btn-info text-white">Lihat</a>
							<?php if (($row['status'] ?? '') === 'belumproses' && Auth::isSales()): ?>
							<a href="/penerimaan/edit/<?= urlencode($row['nopenerimaan']) ?>" class="btn btn-sm btn-warning">Ubah</a>
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


