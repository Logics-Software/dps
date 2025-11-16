<?php
$title = 'Buat Penerimaan Piutang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/penerimaan">Penerimaan Piutang</a></li>
				<li class="breadcrumb-item active">Buat</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white">
		<h4 class="mb-0">Penerimaan: <?= htmlspecialchars($nopenerimaan ?? '') ?></h4>
	</div>
	<div class="card-body">
		<form method="POST" action="">
			<input type="hidden" name="nopenerimaan" value="<?= htmlspecialchars($nopenerimaan ?? '') ?>">
			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Status PKP</label>
					<select name="statuspkp" class="form-select">
						<option value="pkp" <?= ($statuspkp ?? 'pkp') === 'pkp' ? 'selected' : '' ?>>PKP</option>
						<option value="nonpkp" <?= ($statuspkp ?? '') === 'nonpkp' ? 'selected' : '' ?>>Non PKP</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Jenis</label>
					<select name="jenispenerimaan" class="form-select">
						<option value="tunai">Tunai</option>
						<option value="transfer">Transfer</option>
						<option value="giro">Giro</option>
					</select>
				</div>
				<div class="col-md-6">
					<label class="form-label">Customer</label>
					<select name="kodecustomer" class="form-select">
						<option value="">Pilih Customer</option>
						<?php foreach (($customers ?? []) as $c): ?>
						<option value="<?= htmlspecialchars($c['kodecustomer']) ?>"><?= htmlspecialchars(($c['namacustomer'] ?? '') . ' (' . ($c['kodecustomer'] ?? '') . ')') ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<hr>

			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>No Penjualan</th>
							<th>Giro</th>
							<th>Tgl Cair</th>
							<th class="text-end">Piutang</th>
							<th class="text-end">Potongan</th>
							<th class="text-end">Lain-lain</th>
							<th class="text-end">Netto</th>
							<th></th>
						</tr>
					</thead>
					<tbody id="detailBody">
						<tr>
							<td>
								<select name="details[0][nopenjualan]" class="form-select">
									<?php foreach (($availablePenjualan ?? []) as $p): ?>
									<option value="<?= htmlspecialchars($p['nopenjualan']) ?>"><?= htmlspecialchars(($p['nopenjualan'] ?? '') . ' - ' . ($p['namacustomer'] ?? '')) ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td><input type="text" name="details[0][nogiro]" class="form-control"></td>
							<td><input type="date" name="details[0][tanggalcair]" class="form-control"></td>
							<td><input type="number" name="details[0][piutang]" class="form-control text-end" value="0" step="1" min="0"></td>
							<td><input type="number" name="details[0][potongan]" class="form-control text-end" value="0" step="1" min="0"></td>
							<td><input type="number" name="details[0][lainlain]" class="form-control text-end" value="0" step="1" min="0"></td>
							<td><input type="number" name="details[0][netto]" class="form-control text-end" value="0" step="1" min="0"></td>
							<td class="text-end align-middle"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Hapus</button></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Total Piutang</label>
					<input type="number" class="form-control text-end" name="totalpiutang" value="0" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Potongan</label>
					<input type="number" class="form-control text-end" name="totalpotongan" value="0" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Lain-lain</label>
					<input type="number" class="form-control text-end" name="totallainlain" value="0" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Netto</label>
					<input type="number" class="form-control text-end" name="totalnetto" value="0" step="1" min="0">
				</div>
			</div>

			<div class="mt-4 d-flex justify-content-between align-items-center">
				<button type="button" class="btn btn-outline-primary" onclick="addRow()">Tambah Baris</button>
				<div>
					<a href="/penerimaan" class="btn btn-secondary me-2">Batal</a>
					<button class="btn btn-primary" type="submit">Simpan</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
let rowIdx = 1;
function addRow(){
	const body = document.getElementById('detailBody');
	const tr = document.createElement('tr');
	tr.innerHTML = body.children[0].innerHTML.replaceAll('[0]', '['+rowIdx+']');
	body.appendChild(tr);
	rowIdx++;
}
function removeRow(btn){
	const tr = btn.closest('tr');
	const body = document.getElementById('detailBody');
	if (body.children.length > 1) {
		tr.remove();
	}
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


