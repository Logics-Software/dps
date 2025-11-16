<?php
$title = 'Data Penjualan';
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
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Data Penjualan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Penjualan</h4>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="/penjualan" class="mb-3" id="penjualanFilterForm">
                    <div class="row g-2 align-items-end search-filter-card">
                        <div class="col-12 col-lg-3">
                            <input type="text" class="form-control" name="search" placeholder="Cari no penjualan, customer, sales" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-6 col-lg-2">
                            <select name="statuspkp" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="pkp" <?= $statuspkp === 'pkp' ? 'selected' : '' ?>>PKP</option>
                                <option value="nonpkp" <?= $statuspkp === 'nonpkp' ? 'selected' : '' ?>>Non PKP</option>
                            </select>
                        </div>
                        <div class="col-6 col-lg-2">
                            <select name="periode" id="periodeFilter" class="form-select" onchange="handlePenjualanPeriodeChange(true)">
                                <option value="today" <?= $periode === 'today' ? 'selected' : '' ?>>Hari ini</option>
                                <option value="week" <?= $periode === 'week' ? 'selected' : '' ?>>Minggu ini</option>
                                <option value="month" <?= $periode === 'month' ? 'selected' : '' ?>>Bulan ini</option>
                                <option value="year" <?= $periode === 'year' ? 'selected' : '' ?>>Tahun ini</option>
                                <option value="custom" <?= $periode === 'custom' ? 'selected' : '' ?>>Custom</option>
                            </select>
                        </div>
                        <div class="col-6 col-lg-2" id="penjualanStartWrapper">
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate ?? '') ?>">
                        </div>
                        <div class="col-6 col-lg-2" id="penjualanEndWrapper">
                            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate ?? '') ?>">
                        </div>
                        <div class="col-6 col-lg-2">
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                <?php foreach ($perPageOptions as $option): ?>
                                <option value="<?= $option ?>" <?= $perPage == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-lg-3 d-flex gap-2 justify-content-lg-end">
                            <button type="submit" class="btn btn-secondary flex-grow-1 flex-lg-grow-0">Terapkan</button>
                            <a href="/penjualan" class="btn btn-outline-secondary flex-grow-1 flex-lg-grow-0">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No Penjualan</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Sales</th>
                                <th class="text-end">Nilai Penjualan</th>
                                <th class="text-end">Saldo</th>
                                <th>No Order</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penjualan)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data penjualan</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($penjualan as $row): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nopenjualan']) ?></td>
                                <td><?= $row['tanggalpenjualan'] ? date('d/m/Y', strtotime($row['tanggalpenjualan'])) : '-' ?></td>
                                <td><?= htmlspecialchars($row['namacustomer'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['namasales'] ?? '-') ?></td>
                                <td class="text-end"><?= number_format((float)$row['nilaipenjualan'], 0, ',', '.') ?></td>
                                <td class="text-end"><?= number_format((float)$row['saldopenjualan'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['noorder'] ?? '-') ?></td>
                                <td class="text-center">
                                    <a href="/penjualan/view/<?= urlencode($row['nopenjualan']) ?>" class="btn btn-sm btn-info text-white"><?= icon('show', 'me-0 mb-1', 14) ?></a>
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
                            <a class="page-link" href="<?= buildPenjualanQuery($page - 1, $perPage, $search, $periode, $startDate, $endDate, $statuspkp) ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 5;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . buildPenjualanQuery(1, $perPage, $search, $periode, $startDate, $endDate, $statuspkp) . '">1</a></li>';
                            if ($start > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            $active = $page == $i ? 'active' : '';
                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . buildPenjualanQuery($i, $perPage, $search, $periode, $startDate, $endDate, $statuspkp) . '">' . $i . '</a></li>';
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . buildPenjualanQuery($totalPages, $perPage, $search, $periode, $startDate, $endDate, $statuspkp) . '">' . $totalPages . '</a></li>';
                        }
                        ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= buildPenjualanQuery($page + 1, $perPage, $search, $periode, $startDate, $endDate, $statuspkp) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php
function buildPenjualanQuery($page, $perPage, $search, $periode, $startDate, $endDate, $statuspkp = null) {
    $params = [
        'page' => max($page, 1),
        'per_page' => $perPage,
        'search' => $search,
        'periode' => $periode,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];
    if (!empty($statuspkp)) {
        $params['statuspkp'] = $statuspkp;
    }
    return '/penjualan?' . http_build_query($params);
}
?>

<script>
function handlePenjualanPeriodeChange(triggerSubmit = false) {
    const select = document.getElementById('periodeFilter');
    const isCustom = select.value === 'custom';
    const startWrapper = document.getElementById('penjualanStartWrapper');
    const endWrapper = document.getElementById('penjualanEndWrapper');
    startWrapper.style.display = isCustom ? 'block' : 'none';
    endWrapper.style.display = isCustom ? 'block' : 'none';
    if (!isCustom && triggerSubmit) {
        const form = document.getElementById('penjualanFilterForm');
        form.querySelector('input[name="start_date"]').value = '';
        form.querySelector('input[name="end_date"]').value = '';
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    handlePenjualanPeriodeChange(false);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


