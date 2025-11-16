<?php
$title = 'Dashboard';
require __DIR__ . '/../layouts/header.php';

$role = $role ?? 'default';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-gradient mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Selamat datang, <strong><?= htmlspecialchars($user['namalengkap']) ?></strong>!</p>
            </div>
            <div>
                <span class="badge bg-info text-dark"><?= ucfirst($user['role']) ?></span>
            </div>
        </div>
    </div>
</div>

<?php if ($role === 'admin'): ?>
    <!-- Admin Dashboard -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card primary">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h2 class="text-primary mb-0"><?= number_format($totalUsers ?? 0) ?></h2>
                    <small class="text-muted">Pengguna terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card info">
                <div class="card-body">
                    <h5>Total Sales</h5>
                    <h2 class="text-info mb-0"><?= number_format($totalSales ?? 0) ?></h2>
                    <small class="text-muted">Sales terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card success">
                <div class="card-body">
                    <h5>Total Customers</h5>
                    <h2 class="text-success mb-0"><?= number_format($totalCustomers ?? 0) ?></h2>
                    <small class="text-muted">Customer terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card warning">
                <div class="card-body">
                    <h5>Total Barang</h5>
                    <h2 class="text-warning mb-0"><?= number_format($totalBarang ?? 0) ?></h2>
                    <small class="text-muted">Barang terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hari Ini</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Penjualan:</strong> <?= number_format($todayPenjualan ?? 0) ?> transaksi</p>
                    <p class="mb-2"><strong>Penerimaan:</strong> <?= number_format($todayPenerimaan ?? 0) ?> transaksi</p>
                    <p class="mb-0"><strong>Order:</strong> <?= number_format($todayOrder ?? 0) ?> transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Penjualan:</strong> <?= number_format($monthPenjualan ?? 0) ?> transaksi</p>
                    <p class="mb-2"><strong>Penerimaan:</strong> <?= number_format($monthPenerimaan ?? 0) ?> transaksi</p>
                    <p class="mb-0"><strong>Omset:</strong> Rp <?= number_format($totalOmset ?? 0, 0, ',', '.') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <a href="/users" class="btn btn-sm btn-outline-primary w-100 mb-2">Manajemen User</a>
                    <a href="/penjualan" class="btn btn-sm btn-outline-success w-100 mb-2">Transaksi Penjualan</a>
                    <a href="/penerimaan" class="btn btn-sm btn-outline-info w-100">Transaksi Inkaso</a>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'manajemen'): ?>
    <!-- Manajemen Dashboard -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card primary">
                <div class="card-body">
                    <h5>Omset Bulan Ini</h5>
                    <h2 class="text-primary mb-0">Rp <?= number_format($totalOmset ?? 0, 0, ',', '.') ?></h2>
                    <small class="text-muted">Total penjualan bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card warning">
                <div class="card-body">
                    <h5>Total Piutang</h5>
                    <h2 class="text-warning mb-0">Rp <?= number_format($totalPiutang ?? 0, 0, ',', '.') ?></h2>
                    <small class="text-muted">Piutang belum lunas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card success">
                <div class="card-body">
                    <h5>Penerimaan Bulan Ini</h5>
                    <h2 class="text-success mb-0">Rp <?= number_format($totalPenerimaan ?? 0, 0, ',', '.') ?></h2>
                    <small class="text-muted">Total penerimaan bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card info">
                <div class="card-body">
                    <h5>Total Customers</h5>
                    <h2 class="text-info mb-0"><?= number_format($totalCustomers ?? 0) ?></h2>
                    <small class="text-muted">Customer terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hari Ini</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Penjualan:</strong> <?= number_format($todayPenjualan ?? 0) ?> transaksi</p>
                    <p class="mb-2"><strong>Penerimaan:</strong> <?= number_format($todayPenerimaan ?? 0) ?> transaksi</p>
                    <p class="mb-0"><strong>Order:</strong> <?= number_format($todayOrder ?? 0) ?> transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Penjualan:</strong> <?= number_format($monthPenjualan ?? 0) ?> transaksi</p>
                    <p class="mb-2"><strong>Penerimaan:</strong> <?= number_format($monthPenerimaan ?? 0) ?> transaksi</p>
                    <p class="mb-0"><strong>Order:</strong> <?= number_format($monthOrder ?? 0) ?> transaksi</p>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'operator'): ?>
    <!-- Operator Dashboard -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card primary">
                <div class="card-body">
                    <h5>Order Hari Ini</h5>
                    <h2 class="text-primary mb-0"><?= number_format($todayOrder ?? 0) ?></h2>
                    <small class="text-muted">Transaksi order hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card success">
                <div class="card-body">
                    <h5>Penjualan Hari Ini</h5>
                    <h2 class="text-success mb-0"><?= number_format($todayPenjualan ?? 0) ?></h2>
                    <small class="text-muted">Transaksi penjualan hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card warning">
                <div class="card-body">
                    <h5>Stok Rendah</h5>
                    <h2 class="text-warning mb-0"><?= number_format($lowStockCount ?? 0) ?></h2>
                    <small class="text-muted">Barang stok ≤ 10</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card info">
                <div class="card-body">
                    <h5>Total Barang</h5>
                    <h2 class="text-info mb-0"><?= number_format($totalBarang ?? 0) ?></h2>
                    <small class="text-muted">Barang terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Order:</strong> <?= number_format($monthOrder ?? 0) ?> transaksi</p>
                    <p class="mb-0"><strong>Penjualan:</strong> <?= number_format($monthPenjualan ?? 0) ?> transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Status Order</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($orderStatusCounts)): ?>
                        <?php foreach ($orderStatusCounts as $status => $count): ?>
                            <p class="mb-2">
                                <strong><?= ucfirst($status) ?>:</strong> <?= number_format($count) ?>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mb-0 text-muted">Tidak ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'sales'): ?>
    <!-- Sales Dashboard -->
    <?php if (isset($error)): ?>
        <div class="alert alert-warning">
            <strong>Peringatan:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php else: ?>
        <!-- Shortcut Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="/visits" class="btn btn-primary btn-lg px-4">
                        <?= icon('location-dot', 'me-2', 20) ?> Kunjungan
                    </a>
                    <a href="/orders" class="btn btn-success btn-lg px-4">
                        <?= icon('boxes-stacked', 'me-2', 20) ?> Order
                    </a>
                    <a href="/penerimaan" class="btn btn-info btn-lg px-4">
                        <?= icon('money-check-dollar', 'me-2', 20) ?> Inkaso
                    </a>
                </div>
            </div>
        </div>

        <!-- Kunjungan Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Kunjungan</h5>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card primary">
                    <div class="card-body">
                        <h5>Kunjungan Hari Ini</h5>
                        <h2 class="text-primary mb-0"><?= number_format($todayVisits ?? 0) ?></h2>
                        <small class="text-muted">Total kunjungan hari ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card success">
                    <div class="card-body">
                        <h5>Kunjungan Bulan Ini</h5>
                        <h2 class="text-success mb-0"><?= number_format($monthVisits ?? 0) ?></h2>
                        <small class="text-muted">Total kunjungan bulan ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card info">
                    <div class="card-body">
                        <h5>Kunjungan Tahun Ini</h5>
                        <h2 class="text-info mb-0"><?= number_format($yearVisits ?? 0) ?></h2>
                        <small class="text-muted">Total kunjungan tahun ini</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Order</h5>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card warning">
                    <div class="card-body">
                        <h5>Order Hari Ini</h5>
                        <h2 class="text-warning mb-1"><?= number_format($todayOrderJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($todayOrderNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai order hari ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card success">
                    <div class="card-body">
                        <h5>Order Bulan Ini</h5>
                        <h2 class="text-success mb-1"><?= number_format($monthOrderJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($monthOrderNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai order bulan ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card info">
                    <div class="card-body">
                        <h5>Order Tahun Ini</h5>
                        <h2 class="text-info mb-1"><?= number_format($yearOrderJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($yearOrderNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai order tahun ini</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inkaso/Penerimaan Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Inkaso</h5>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card danger">
                    <div class="card-body">
                        <h5>Inkaso Hari Ini</h5>
                        <h2 class="text-danger mb-1"><?= number_format($todayPenerimaanJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($todayPenerimaanNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai inkaso hari ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card success">
                    <div class="card-body">
                        <h5>Inkaso Bulan Ini</h5>
                        <h2 class="text-success mb-1"><?= number_format($monthPenerimaanJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($monthPenerimaanNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai inkaso bulan ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card info">
                    <div class="card-body">
                        <h5>Inkaso Tahun Ini</h5>
                        <h2 class="text-info mb-1"><?= number_format($yearPenerimaanJumlah ?? 0) ?></h2>
                        <p class="mb-0"><strong>Nilai:</strong> Rp <?= number_format($yearPenerimaanNilai ?? 0, 0, ',', '.') ?></p>
                        <small class="text-muted">Jumlah dan nilai inkaso tahun ini</small>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($activeVisit): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>Kunjungan Aktif:</strong> Anda sedang melakukan kunjungan ke 
                        <strong><?= htmlspecialchars($activeVisit['namacustomer'] ?? 'Customer') ?></strong>
                        <a href="/visits" class="btn btn-sm btn-outline-light float-end">Lihat Detail</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php else: ?>
    <!-- Default Dashboard -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card primary">
                <div class="card-body">
                    <h5>Role</h5>
                    <h4 class="text-primary mb-0"><?= ucfirst($user['role']) ?></h4>
                    <small class="text-muted">Hak akses Anda</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card success">
                <div class="card-body">
                    <h5>Status</h5>
                    <h4 class="text-success mb-0"><?= ucfirst($user['status']) ?></h4>
                    <small class="text-muted">Status akun</small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

