<?php
$title = 'Dashboard';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}
require __DIR__ . '/../layouts/header.php';

$user = $user ?? Auth::user();
$role = $role ?? ($user['role'] ?? '');
$stats = $stats ?? [];
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="mb-0">Dashboard</h1>
            <!-- <h3 class="mb-0">Selamat Datang, <?= htmlspecialchars($user['namalengkap'] ?? 'User') ?>!</h3> -->
        </div>
    </div>

    <?php if ($role === 'admin' || $role === 'manajemen'): ?>
        <!-- Dashboard Admin & Manajemen -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Order Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                        <a href="/orders" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Penjualan Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_penjualan'] ?? 0) ?></h3>
                        <a href="/penjualan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Inkaso Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_penerimaan'] ?? 0) ?></h3>
                        <a href="/penerimaan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Total Users</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_users'] ?? 0) ?></h3>
                        <?php if ($role === 'admin'): ?>
                        <a href="/users" class="btn btn-sm btn-outline-primary mt-2">Kelola User</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Access</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/orders" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice', 'me-2', 18) ?> Transaksi Order
                            </a>
                            <a href="/penjualan" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice-dollar', 'me-2', 18) ?> Transaksi Penjualan
                            </a>
                            <a href="/penerimaan" class="btn btn-outline-primary text-start">
                                <?= icon('money-bill-transfer', 'me-2', 18) ?> Transaksi Inkaso
                            </a>
                            <?php if ($role === 'admin'): ?>
                            <a href="/users" class="btn btn-outline-primary text-start">
                                <?= icon('users', 'me-2', 18) ?> Manajemen User
                            </a>
                            <a href="/login-logs" class="btn btn-outline-primary text-start">
                                <?= icon('clock-rotate-left', 'me-2', 18) ?> Login Logs
                            </a>
                            <?php endif; ?>
                            <a href="/messages" class="btn btn-outline-primary text-start">
                                <?= icon('envelope', 'me-2', 18) ?> Pesan
                                <?php if (($stats['unread_messages'] ?? 0) > 0): ?>
                                <span class="badge bg-danger ms-auto"><?= $stats['unread_messages'] ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Sistem</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Tanggal:</strong> <?= date('d F Y') ?></p>
                        <p class="mb-2"><strong>Waktu:</strong> <?= date('H:i:s') ?></p>
                        <p class="mb-0"><strong>Role:</strong> <?= htmlspecialchars(ucfirst($role)) ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'operator'): ?>
        <!-- Dashboard Operator -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Order Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                        <a href="/orders" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Penjualan Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_penjualan'] ?? 0) ?></h3>
                        <a href="/penjualan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Inkaso Hari Ini</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_penerimaan'] ?? 0) ?></h3>
                        <a href="/penerimaan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Access</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/orders" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice', 'me-2', 18) ?> Transaksi Order
                            </a>
                            <a href="/penjualan" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice-dollar', 'me-2', 18) ?> Transaksi Penjualan
                            </a>
                            <a href="/penerimaan" class="btn btn-outline-primary text-start">
                                <?= icon('money-bill-transfer', 'me-2', 18) ?> Transaksi Inkaso
                            </a>
                            <a href="/messages" class="btn btn-outline-primary text-start">
                                <?= icon('envelope', 'me-2', 18) ?> Pesan
                                <?php if (($stats['unread_messages'] ?? 0) > 0): ?>
                                <span class="badge bg-danger ms-auto"><?= $stats['unread_messages'] ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Tanggal:</strong> <?= date('d F Y') ?></p>
                        <p class="mb-2"><strong>Waktu:</strong> <?= date('H:i:s') ?></p>
                        <p class="mb-0"><strong>Role:</strong> Operator</p>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'sales'): ?>
        <!-- Dashboard Sales -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Order Saya</h5>
                        <h3 class="mb-0"><?= number_format($stats['my_orders'] ?? 0) ?></h3>
                        <a href="/orders" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Penjualan Saya</h5>
                        <h3 class="mb-0"><?= number_format($stats['my_penjualan'] ?? 0) ?></h3>
                        <a href="/penjualan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2">Inkaso Saya</h5>
                        <h3 class="mb-0"><?= number_format($stats['my_penerimaan'] ?? 0) ?></h3>
                        <a href="/penerimaan" class="btn btn-sm btn-outline-primary mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Access</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/orders/create" class="btn btn-primary text-start">
                                <?= icon('square-plus-dark', 'me-2', 18) ?> Buat Order Baru
                            </a>
                            <a href="/visits" class="btn btn-outline-primary text-start">
                                <?= icon('location-dark', 'me-2', 18) ?> Kunjungan
                            </a>
                            <a href="/penerimaan/create" class="btn btn-outline-primary text-start">
                                <?= icon('square-plus-dark', 'me-2', 18) ?> Buat Inkaso Baru
                            </a>
                            <a href="/orders" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice', 'me-2', 18) ?> Daftar Order
                            </a>
                            <a href="/penjualan" class="btn btn-outline-primary text-start">
                                <?= icon('file-invoice-dollar', 'me-2', 18) ?> Daftar Penjualan
                            </a>
                            <a href="/penerimaan" class="btn btn-outline-primary text-start">
                                <?= icon('money-bill-transfer', 'me-2', 18) ?> Daftar Inkaso
                            </a>
                            <a href="/messages" class="btn btn-outline-primary text-start">
                                <?= icon('envelope', 'me-2', 18) ?> Pesan
                                <?php if (($stats['unread_messages'] ?? 0) > 0): ?>
                                <span class="badge bg-danger ms-auto"><?= $stats['unread_messages'] ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Laporan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/laporan/daftar-barang" class="btn btn-outline-primary text-start w-100">Daftar Barang</a>
                            <a href="/laporan/daftar-stok" class="btn btn-outline-primary text-start w-100">Daftar Stok</a>
                            <a href="/laporan/daftar-harga" class="btn btn-outline-primary text-start w-100">Daftar Harga Barang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Default Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Dashboard</h5>
                        <p class="card-text">Selamat datang di sistem DPS Online.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
