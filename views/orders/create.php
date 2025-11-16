<?php
$title = 'Buat Order';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/orders">Transaksi Order</a></li>
				<li class="breadcrumb-item active">Buat Order</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white">
		<h4 class="mb-0">Order Baru: <?= htmlspecialchars($noorder) ?></h4>
	</div>
	<div class="card-body">
		<form method="POST" action="">
			<input type="hidden" name="noorder" value="<?= htmlspecialchars($noorder) ?>">
			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Status PKP</label>
					<select name="statuspkp" class="form-select">
						<option value="pkp" <?= ($statuspkp ?? 'pkp') === 'pkp' ? 'selected' : '' ?>>PKP</option>
						<option value="nonpkp" <?= ($statuspkp ?? '') === 'nonpkp' ? 'selected' : '' ?>>Non PKP</option>
					</select>
				</div>
				<div class="col-md-9">
					<label class="form-label">Customer</label>
					<select name="kodecustomer" class="form-select">
						<option value="">Pilih Customer</option>
						<?php foreach (($customers ?? []) as $c): ?>
						<option value="<?= htmlspecialchars($c['kodecustomer']) ?>" <?= ($selectedCustomer ?? '') === ($c['kodecustomer'] ?? '') ? 'selected' : '' ?>>
							<?= htmlspecialchars(($c['namacustomer'] ?? '') . ' (' . ($c['kodecustomer'] ?? '') . ')') ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-12">
					<label class="form-label">Keterangan</label>
					<input type="text" name="keterangan" class="form-control" value="<?= htmlspecialchars($keterangan ?? '') ?>" maxlength="50">
				</div>
			</div>

			<hr>

			<div class="table-responsive">
				<table class="table" id="detailTable">
					<thead>
						<tr>
							<th>Barang</th>
							<th class="text-end">Jumlah</th>
							<th class="text-end">Harga</th>
							<th class="text-end">Diskon</th>
							<th class="text-end">Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody id="detailBody">
						<tr>
							<td>
								<select name="detail_kodebarang[]" class="form-select">
									<option value="">Pilih Barang</option>
									<?php foreach (($barangs ?? []) as $b): ?>
									<option value="<?= htmlspecialchars($b['kodebarang']) ?>"><?= htmlspecialchars(($b['namabarang'] ?? '') . ' (' . ($b['kodebarang'] ?? '') . ')') ?></option>
									<?php endforeach; ?>
								</select>
								<input type="hidden" name="detail_satuan[]" value="">
							</td>
							<td><input type="number" name="detail_jumlah[]" class="form-control text-end" min="0" step="1" value="1"></td>
							<td><input type="number" name="detail_harga[]" class="form-control text-end" min="0" step="1" value="0"></td>
							<td><input type="number" name="detail_discount[]" class="form-control text-end" min="0" step="0.01" value="0"></td>
							<td class="text-end align-middle">-</td>
							<td class="text-end align-middle">
								<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Hapus</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="d-flex justify-content-between align-items-center">
				<button class="btn btn-outline-primary" type="button" onclick="addRow()">Tambah Barang</button>
				<div class="fs-6">Grand Total: <strong id="grandTotal">0</strong></div>
			</div>

			<div class="mt-4 text-end">
				<a href="/orders" class="btn btn-secondary me-2">Batal</a>
				<button class="btn btn-primary" type="submit">Simpan Order</button>
			</div>
		</form>
	</div>
</div>

<script>
function addRow() {
	const body = document.getElementById('detailBody');
	const tr = document.createElement('tr');
	tr.innerHTML = body.children[0].innerHTML;
	body.appendChild(tr);
}
function removeRow(btn) {
	const tr = btn.closest('tr');
	const body = document.getElementById('detailBody');
	if (body.children.length > 1) {
		tr.remove();
	}
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


