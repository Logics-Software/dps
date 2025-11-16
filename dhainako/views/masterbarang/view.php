<?php
$title = 'Detail Barang';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

$currentUser = Auth::user();

require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/masterbarang">Master Barang</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <!-- <h4 class="mb-0"><?= htmlspecialchars($item['namabarang']) ?></h4> -->
                    <small class="text-muted">Kode: <?= htmlspecialchars($item['kodebarang']) ?></small>
                </div>
                <div>
                    <a href="/masterbarang" class="btn btn-secondary btn-sm"><?= icon('back', 'me-2', 14) ?> Kembali</a>
                    <?php if ($currentUser && in_array($currentUser['role'], ['admin', 'manajemen', 'operator'])): ?>
                    <!-- <a href="/masterbarang/edit/<?= $item['id'] ?>" class="btn btn-warning btn-sm"><?= icon('update', 'me-2', 14) ?> Edit</a> -->
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h4 class="mb-2"><?= htmlspecialchars($item['namabarang']) ?></h4>
                        <dl class="row mb-0">
                            <dt class="col-5">Satuan</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['satuan'] ?? '-') ?></dd>
                            <dt class="col-5">Kandungan</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['kandungan'] ?? '-') ?></dd>
                            <dt class="col-5">NIE</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['nie'] ?? '-') ?></dd>
                            <dt class="col-5">OOT</dt>
                            <dd class="col-7"><?= ucfirst($item['oot'] ?? '-') ?></dd>
                            <dt class="col-5">Prekursor</dt>
                            <dd class="col-7"><?= ucfirst($item['prekursor'] ?? '-') ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5">Pabrik</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['namapabrik'] ?? $item['kodepabrik'] ?? '-') ?></dd>
                            <dt class="col-5">Golongan</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['namagolongan'] ?? $item['kodegolongan'] ?? '-') ?></dd>
                            <dt class="col-5">Supplier</dt>
                            <dd class="col-7"><?= htmlspecialchars($item['namasupplier'] ?? $item['kodesupplier'] ?? '-') ?></dd>
                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                <span class="badge bg-<?= ($item['status'] ?? 'aktif') === 'aktif' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($item['status'] ?? 'aktif') ?>
                                </span>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted">Harga Pokok</h6>
                        <dl class="row mb-0">
                            <dt class="col-5">HPP</dt>
                            <dd class="col-7"><?= is_null($item['hpp']) ? '-' : number_format((float)$item['hpp'], 0, ',', '.') ?></dd>
                            <dt class="col-5">Harga Beli</dt>
                            <dd class="col-7"><?= is_null($item['hargabeli']) ? '-' : number_format((float)$item['hargabeli'], 0, ',', '.') ?></dd>
                            <dt class="col-5">Diskon Beli</dt>
                            <dd class="col-7"><?= is_null($item['discountbeli']) ? '-' : number_format((float)$item['discountbeli'], 2, ',', '.') . ' %' ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted">Harga Jual & Stok</h6>
                        <dl class="row mb-0">
                        <dt class="col-5">Harga Jual</dt>
                            <dd class="col-7"><?= is_null($item['hargajual']) ? '-' : number_format((float)$item['hargajual'], 0, ',', '.') ?></dd>
                            <dt class="col-5">Diskon Jual</dt>
                            <dd class="col-7"><?= is_null($item['discountjual']) ? '-' : number_format((float)$item['discountjual'], 2, ',', '.') . ' %' ?></dd>
                            <dt class="col-5">Stok Akhir</dt>
                            <dd class="col-7"><?= is_null($item['stokakhir']) ? '-' : number_format((float)$item['stokakhir'], 0, ',', '.') ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


