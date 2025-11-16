<?php
$title = 'Transaksi Order';
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
                <li class="breadcrumb-item active">Transaksi Order</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Order</h4>
                    <?php if (($currentUser['role'] ?? '') === 'sales'): ?>
                    <a href="/orders/create" class="btn btn-primary btn-sm"><?= icon('add', 'me-2', 16) ?> Buat Order</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row search-filter-card">
                    <form method="GET" action="/orders" class="mb-3" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-lg-3">
                                <input type="text" class="form-control" name="search" placeholder="Cari customer..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-6 col-lg-2">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="order" <?= $status === 'order' ? 'selected' : '' ?>>Order</option>
                                    <option value="faktur" <?= $status === 'faktur' ? 'selected' : '' ?>>Faktur</option>
                                </select>
                            </div>
                            <div class="col-6 col-lg-2">
                                <select name="date_filter" id="dateFilter" class="form-select" onchange="handleDateFilterChange(true)">
                                    <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Hari ini</option>
                                    <option value="week" <?= $dateFilter === 'week' ? 'selected' : '' ?>>Minggu ini</option>
                                    <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>Bulan ini</option>
                                    <option value="custom" <?= $dateFilter === 'custom' ? 'selected' : '' ?>>Custom</option>
                                </select>
                            </div>
                            <div class="col-6 col-lg-2" id="startDateWrapper">
                                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($rawStartDate ?? $startDate) ?>">
                            </div>
                            <div class="col-6 col-lg-2" id="endDateWrapper">
                                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($rawEndDate ?? $endDate) ?>">
                            </div>
                            <div class="col-12 col-lg-1">
                                <select name="per_page" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ([10, 20, 40, 60, 100] as $pp): ?>
                                    <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-lg-4 d-lg-flex justify-content-lg-end">
                                <div class="row g-2 w-100">
                                    <div class="col-6 col-lg-6">
                                        <button type="submit" class="btn btn-secondary w-100">Terapkan</button>
                                    </div>
                                    <div class="col-6 col-lg-6">
                                        <a href="/orders" class="btn btn-outline-secondary w-100">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No Order</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Nilai Order</th>
                                <th align="center">Status</th>
                                <th>No.Faktur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($order['noorder']) ?></td>
                                <td align="center"><?= date('d/m/Y', strtotime($order['tanggalorder'])) ?></td>
                                <td><?= htmlspecialchars($order['namacustomer'] ?? '-') ?></td>
                                <td align="right"><?= number_format((float)$order['nilaiorder'], 0, ',', '.') ?></td>
                                <td align="center">
                                    <span class="badge bg-<?= ($order['status'] === 'faktur') ? 'success' : 'warning' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($order['nopenjualan'] ?? '-') ?></td>
                                <td align="center">
                                    <a href="/orders/view/<?= urlencode($order['noorder']) ?>" class="btn btn-sm btn-info text-white"><?= icon('show', 'me-0 mb-1', 14) ?></a>
                                    <?php if ($order['status'] === 'order' && (($currentUser['role'] ?? '') !== 'sales' || ($currentUser['kodesales'] ?? '') === $order['kodesales'])): ?>
                                    <a href="/orders/edit/<?= urlencode($order['noorder']) ?>" class="btn btn-sm btn-warning"><?= icon('update', 'me-0 mb-1', 14) ?></a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteOrder('<?= htmlspecialchars($order['noorder']) ?>')"><?= icon('delete', 'me-0 mb-1', 14) ?></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&date_filter=<?= urlencode($dateFilter) ?>&start_date=<?= urlencode($rawStartDate ?? $startDate) ?>&end_date=<?= urlencode($rawEndDate ?? $endDate) ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 3;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1&per_page=' . $perPage . '&search=' . urlencode($search) . '&status=' . urlencode($status) . '&date_filter=' . urlencode($dateFilter) . '&start_date=' . urlencode($rawStartDate ?? $startDate) . '&end_date=' . urlencode($rawEndDate ?? $endDate) . '">1</a></li>';
                            if ($start > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '&per_page=' . $perPage . '&search=' . urlencode($search) . '&status=' . urlencode($status) . '&date_filter=' . urlencode($dateFilter) . '&start_date=' . urlencode($rawStartDate ?? $startDate) . '&end_date=' . urlencode($rawEndDate ?? $endDate) . '">' . $i . '</a></li>';
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&per_page=' . $perPage . '&search=' . urlencode($search) . '&status=' . urlencode($status) . '&date_filter=' . urlencode($dateFilter) . '&start_date=' . urlencode($rawStartDate ?? $startDate) . '&end_date=' . urlencode($rawEndDate ?? $endDate) . '">' . $totalPages . '</a></li>';
                        }
                        ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&date_filter=<?= urlencode($dateFilter) ?>&start_date=<?= urlencode($rawStartDate ?? $startDate) ?>&end_date=<?= urlencode($rawEndDate ?? $endDate) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function handleDateFilterChange(triggerSubmit = false) {
    const filter = document.getElementById('dateFilter').value;
    const startWrapper = document.getElementById('startDateWrapper');
    const endWrapper = document.getElementById('endDateWrapper');
    const isCustom = filter === 'custom';
    startWrapper.style.display = isCustom ? 'block' : 'none';
    endWrapper.style.display = isCustom ? 'block' : 'none';
    if (!isCustom && triggerSubmit) {
        document.querySelector('input[name="start_date"]').value = '';
        document.querySelector('input[name="end_date"]').value = '';
        document.getElementById('filterForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    handleDateFilterChange(false);
});

function confirmDeleteOrder(noorder) {
    if (!noorder) {
        return;
    }
    const deleteUrl = `/orders/delete/${encodeURIComponent(noorder)}`;
    showConfirmModal({
        title: 'Konfirmasi Hapus',
        message: `Apakah Anda yakin ingin menghapus order <strong>${noorder}</strong>?`,
        buttonText: 'Hapus',
        buttonClass: 'btn-danger',
        onConfirm: function () {
            window.location.href = deleteUrl;
        }
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
