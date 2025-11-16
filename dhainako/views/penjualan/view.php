<?php
$title = 'Detail Penjualan';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

require __DIR__ . '/../layouts/header.php';

?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/penjualan">Transaksi Penjualan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (!empty($penjualan)): ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Penjualan</h4>
                <a href="/penjualan" class="btn btn-secondary btn-sm"><?= icon('back', 'me-2', 14) ?> Kembali</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">No Penjualan</div>
                        <span class="fw-bold"><?= htmlspecialchars($penjualan['nopenjualan']) ?></span>
                        <span><?php 
                                $statuspkp = $penjualan['statuspkp'] ?? null;
                                if ($statuspkp === 'pkp') {
                                    echo '<span class="badge bg-success small" style="font-size: 0.7em;">PKP</span>';
                                } elseif ($statuspkp === 'nonpkp') {
                                    echo '<span class="badge bg-secondary small" style="font-size: 0.7em;">Non PKP</span>';
                                } else {
                                    echo '-';
                                }
                                ?>
                        </span>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Tanggal Penjualan</div>
                        <div class="fw-semibold"><?= $penjualan['tanggalpenjualan'] ? date('d/m/Y', strtotime($penjualan['tanggalpenjualan'])) : '-' ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">No Order</div>
                        <div class="fw-semibold"><?= htmlspecialchars($penjualan['noorder'] ?? '-') ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Tanggal Order</div>
                        <div class="fw-semibold"><?= $penjualan['tanggalorder'] ? date('d/m/Y', strtotime($penjualan['tanggalorder'])) : '-' ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Jatuh Tempo</div>
                        <div class="fw-semibold"><?= $penjualan['tanggaljatuhtempo'] ? date('d/m/Y', strtotime($penjualan['tanggaljatuhtempo'])) : '-' ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Customer</div>
                        <div class="fw-semibold"><?= htmlspecialchars($penjualan['namacustomer'] ?? '-') ?></div>
                        <div class="text-muted small"><?= htmlspecialchars(($penjualan['alamatcustomer'] ?? '') . ' ' . ($penjualan['kotacustomer'] ?? '')) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Sales</div>
                        <div class="fw-semibold"><?= htmlspecialchars($penjualan['namasales'] ?? '-') ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Pengirim</div>
                        <div class="fw-semibold"><?= htmlspecialchars($penjualan['pengirim'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Detail Barang</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>No.batch</th>
                                <th>ED</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Jumlah Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($details)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Tidak ada detail barang</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($details as $index => $detail): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($detail['kodebarang']) ?></td>
                                <td><?= htmlspecialchars($detail['namabarang'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($detail['satuan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($detail['nomorbatch'] ?? '-') ?></td>
                                <td><?= $detail['expireddate'] ? date('d/m/Y', strtotime($detail['expireddate'])) : '-' ?></td>
                                <td class="text-end"><?= number_format((float)$detail['jumlah'], 0, ',', '.') ?></td>
                                <td class="text-end"><?= number_format((float)$detail['hargasatuan'], 0, ',', '.') ?></td>
                                <td class="text-end"><?= number_format((float)$detail['discount'], 2, ',', '.') ?></td>
                                <td class="text-end"><?= number_format((float)$detail['jumlahharga'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-end">
                            <div class="small text-muted">DPP</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['dpp'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                        <div class="text-end mt-2 d-md-none">
                            <div class="small text-muted">PPN</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['ppn'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 d-none d-md-block">
                        <div class="text-end">
                            <div class="small text-muted">PPN</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['ppn'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-end">
                            <div class="small text-muted">Nilai Penjualan</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['nilaipenjualan'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                        <div class="text-end mt-2 d-md-none">
                            <div class="small text-muted">Saldo Penjualan</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['saldopenjualan'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 d-none d-md-block">
                        <div class="text-end">
                            <div class="small text-muted">Saldo Penjualan</div>
                            <div class="fw-bold"><?= number_format((float)($penjualan['saldopenjualan'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


