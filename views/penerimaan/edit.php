<?php
$title = 'Ubah Penerimaan Piutang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
	<div class="col-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/penerimaan">Penerimaan Piutang</a></li>
				<li class="breadcrumb-item active">Ubah</li>
			</ol>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-header bg-white">
		<h4 class="mb-0">Penerimaan: <?= htmlspecialchars($penerimaan['nopenerimaan'] ?? '') ?></h4>
	</div>
	<div class="card-body">
		<form method="POST" action="">
			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Tanggal</label>
					<input type="date" name="tanggalpenerimaan" class="form-control" value="<?= htmlspecialchars($penerimaan['tanggalpenerimaan'] ?? date('Y-m-d')) ?>">
				</div>
				<div class="col-md-3">
					<label class="form-label">Status PKP</label>
					<select name="statuspkp" class="form-select">
						<?php $sp = strtolower($statuspkp ?? ($penerimaan['statuspkp'] ?? 'pkp')); ?>
						<option value="pkp" <?= $sp === 'pkp' ? 'selected' : '' ?>>PKP</option>
						<option value="nonpkp" <?= $sp === 'nonpkp' ? 'selected' : '' ?>>Non PKP</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Jenis</label>
					<select name="jenispenerimaan" class="form-select">
						<?php $jp = $penerimaan['jenispenerimaan'] ?? 'tunai'; ?>
						<option value="tunai" <?= $jp === 'tunai' ? 'selected' : '' ?>>Tunai</option>
						<option value="transfer" <?= $jp === 'transfer' ? 'selected' : '' ?>>Transfer</option>
						<option value="giro" <?= $jp === 'giro' ? 'selected' : '' ?>>Giro</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Customer</label>
					<select name="kodecustomer" class="form-select">
						<?php foreach (($customers ?? []) as $c): ?>
						<option value="<?= htmlspecialchars($c['kodecustomer']) ?>" <?= ($penerimaan['kodecustomer'] ?? '') === ($c['kodecustomer'] ?? '') ? 'selected' : '' ?>>
							<?= htmlspecialchars(($c['namacustomer'] ?? '') . ' (' . ($c['kodecustomer'] ?? '') . ')') ?>
						</option>
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
						<?php foreach (($details ?? []) as $idx => $d): ?>
						<tr>
							<td>
								<select name="details[<?= $idx ?>][nopenjualan]" class="form-select">
									<?php foreach (($availablePenjualan ?? []) as $p): ?>
									<option value="<?= htmlspecialchars($p['nopenjualan']) ?>" <?= ($d['nopenjualan'] ?? '') === ($p['nopenjualan'] ?? '') ? 'selected' : '' ?>>
										<?= htmlspecialchars(($p['nopenjualan'] ?? '') . ' - ' . ($p['namacustomer'] ?? '')) ?>
									</option>
									<?php endforeach; ?>
								</select>
							</td>
							<td><input type="text" name="details[<?= $idx ?>][nogiro]" class="form-control" value="<?= htmlspecialchars($d['nogiro'] ?? '') ?>"></td>
							<td><input type="date" name="details[<?= $idx ?>][tanggalcair]" class="form-control" value="<?= htmlspecialchars($d['tanggalcair'] ?? '') ?>"></td>
							<td><input type="number" name="details[<?= $idx ?>][piutang]" class="form-control text-end" value="<?= htmlspecialchars($d['piutang'] ?? 0) ?>" step="1" min="0"></td>
							<td><input type="number" name="details[<?= $idx ?>][potongan]" class="form-control text-end" value="<?= htmlspecialchars($d['potongan'] ?? 0) ?>" step="1" min="0"></td>
							<td><input type="number" name="details[<?= $idx ?>][lainlain]" class="form-control text-end" value="<?= htmlspecialchars($d['lainlain'] ?? 0) ?>" step="1" min="0"></td>
							<td><input type="number" name="details[<?= $idx ?>][netto]" class="form-control text-end" value="<?= htmlspecialchars($d['netto'] ?? 0) ?>" step="1" min="0"></td>
							<td class="text-end align-middle"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Hapus</button></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Total Piutang</label>
					<input type="number" class="form-control text-end" name="totalpiutang" value="<?= htmlspecialchars($penerimaan['totalpiutang'] ?? 0) ?>" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Potongan</label>
					<input type="number" class="form-control text-end" name="totalpotongan" value="<?= htmlspecialchars($penerimaan['totalpotongan'] ?? 0) ?>" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Lain-lain</label>
					<input type="number" class="form-control text-end" name="totallainlain" value="<?= htmlspecialchars($penerimaan['totallainlain'] ?? 0) ?>" step="1" min="0">
				</div>
				<div class="col-md-3">
					<label class="form-label">Total Netto</label>
					<input type="number" class="form-control text-end" name="totalnetto" value="<?= htmlspecialchars($penerimaan['totalnetto'] ?? 0) ?>" step="1" min="0">
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
let rowIdx = <?= (int)max(count($details ?? []), 1) ?>;
function addRow(){
	const body = document.getElementById('detailBody');
	const template = body.children[0].outerHTML.replaceAll('[0]', '['+rowIdx+']').replace(/\[\d+\]/g, '['+rowIdx+']');
	const tr = document.createElement('tr');
	tr.innerHTML = template;
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


