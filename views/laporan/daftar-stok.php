<?php
$title = 'Laporan Daftar Stok';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

if (!function_exists('getSortUrlLaporanStok')) {
    function getSortUrlLaporanStok($column, $currentSortBy, $currentSortOrder, $search, $perPage, $kodepabrik, $kodegolongan, $kondisiStok) {
        $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
        $params = http_build_query([
            'page' => 1,
            'per_page' => $perPage,
            'search' => $search,
            'kodepabrik' => $kodepabrik,
            'kodegolongan' => $kodegolongan,
            'kondisi_stok' => $kondisiStok,
            'sort_by' => $column,
            'sort_order' => $newSortOrder
        ]);
        return '/laporan/daftar-stok?' . $params;
    }
}

if (!function_exists('getSortIconLaporanStok')) {
    function getSortIconLaporanStok($column, $currentSortBy, $currentSortOrder) {
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = rtrim($config['base_url'], '/');
        if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
            $baseUrl = '/';
        }

        if ($currentSortBy != $column) {
            $iconPath = $baseUrl . '/assets/icons/arrows-up-down.svg';
            return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort" class="sort-icon icon-inline" width="14" height="14">';
        }

        if ($currentSortOrder == 'ASC') {
            $iconPath = $baseUrl . '/assets/icons/arrow-up.svg';
            return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort-up" class="sort-icon icon-inline" width="14" height="14">';
        }

        $iconPath = $baseUrl . '/assets/icons/arrow-down.svg';
        return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort-down" class="sort-icon icon-inline" width="14" height="14">';
    }
}

require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Daftar Stok</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Laporan Daftar Stok</h4>
                    <div class="d-flex gap-2">
                        <?php
                        $exportParams = [];
                        if (!empty($search)) $exportParams['search'] = $search;
                        if (!empty($kodepabrik)) $exportParams['kodepabrik'] = $kodepabrik;
                        if (!empty($kodegolongan)) $exportParams['kodegolongan'] = $kodegolongan;
                        if (!empty($kondisiStok) && $kondisiStok !== 'semua') $exportParams['kondisi_stok'] = $kondisiStok;
                        if (!empty($sortBy)) $exportParams['sort_by'] = $sortBy;
                        if (!empty($sortOrder)) $exportParams['sort_order'] = $sortOrder;
                        $exportQuery = http_build_query($exportParams);
                        ?>
                        <a href="/laporan/daftar-stok?export=excel<?= !empty($exportQuery) ? '&' . $exportQuery : '' ?>" class="btn btn-success btn-sm">
                            <?= icon('file-excel', 'mb-1 me-2', 16) ?> Export Excel
                        </a>
                        <a href="/laporan/daftar-stok?export=pdf<?= !empty($exportQuery) ? '&' . $exportQuery : '' ?>" class="btn btn-danger btn-sm" target="_blank">
                            <?= icon('file-pdf', 'mb-1 me-2', 16) ?> Export PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="/laporan/daftar-stok" class="mb-3">
                    <div class="row g-2 align-items-end search-filter-card">
                        <div class="col-12 col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Cari nama barang atau kandungan..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-6 col-md-2">
                            <select name="kodepabrik" class="form-select" onchange="this.form.submit()">
                                <option value="">Pabrik</option>
                                <?php foreach ($pabriks as $pabrik): ?>
                                <option value="<?= htmlspecialchars($pabrik['kodepabrik']) ?>" <?= $kodepabrik === $pabrik['kodepabrik'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pabrik['namapabrik']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select name="kodegolongan" class="form-select" onchange="this.form.submit()">
                                <option value="">Golongan</option>
                                <?php foreach ($golongans as $golongan): ?>
                                <option value="<?= htmlspecialchars($golongan['kodegolongan']) ?>" <?= $kodegolongan === $golongan['kodegolongan'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($golongan['namagolongan']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select name="kondisi_stok" class="form-select" onchange="this.form.submit()">
                                <option value="semua" <?= ($kondisiStok ?? 'semua') === 'semua' ? 'selected' : '' ?>>Stok</option>
                                <option value="ada" <?= ($kondisiStok ?? '') === 'ada' ? 'selected' : '' ?>>Stok > 0</option>
                                <option value="kosong" <?= ($kondisiStok ?? '') === 'kosong' ? 'selected' : '' ?>>Stok = 0</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Cari</button>
                        </div>
                        <div class="col-6 col-md-1">
                            <a href="/laporan/daftar-stok" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted">Total: <strong><?= number_format($total) ?></strong> barang</small>
                    </div>
                    <div>
                        <?php
                        $queryParams = [];
                        if (!empty($search)) $queryParams['search'] = $search;
                        if (!empty($kodepabrik)) $queryParams['kodepabrik'] = $kodepabrik;
                        if (!empty($kodegolongan)) $queryParams['kodegolongan'] = $kodegolongan;
                        if (!empty($kondisiStok) && $kondisiStok !== 'semua') $queryParams['kondisi_stok'] = $kondisiStok;
                        if (!empty($sortBy)) $queryParams['sort_by'] = $sortBy;
                        if (!empty($sortOrder)) $queryParams['sort_order'] = $sortOrder;
                        $baseQueryForPerPage = http_build_query($queryParams);
                        ?>
                        <select name="per_page" class="form-select form-select-sm d-inline-block" style="width: 100px;" onchange="window.location.href='?per_page=' + this.value + '<?= !empty($baseQueryForPerPage) ? '&' . $baseQueryForPerPage : '' ?>'">
                            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                            <option value="200" <?= $perPage == 200 ? 'selected' : '' ?>>200</option>
                            <option value="500" <?= $perPage == 500 ? 'selected' : '' ?>>500</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>
                                    <a href="<?= getSortUrlLaporanStok('namabarang', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC', $search ?? '', $perPage ?? 50, $kodepabrik ?? '', $kodegolongan ?? '', $kondisiStok ?? 'semua') ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        Nama Barang
                                        <?= getSortIconLaporanStok('namabarang', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC') ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrlLaporanStok('satuan', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC', $search ?? '', $perPage ?? 50, $kodepabrik ?? '', $kodegolongan ?? '', $kondisiStok ?? 'semua') ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        Satuan
                                        <?= getSortIconLaporanStok('satuan', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC') ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrlLaporanStok('pabrik', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC', $search ?? '', $perPage ?? 50, $kodepabrik ?? '', $kodegolongan ?? '', $kondisiStok ?? 'semua') ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        Pabrik
                                        <?= getSortIconLaporanStok('pabrik', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC') ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrlLaporanStok('stok', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC', $search ?? '', $perPage ?? 50, $kodepabrik ?? '', $kodegolongan ?? '', $kondisiStok ?? 'semua') ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        Stok
                                        <?= getSortIconLaporanStok('stok', $sortBy ?? 'namabarang', $sortOrder ?? 'ASC') ?>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($barangs)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data barang</td>
                            </tr>
                            <?php else: ?>
                            <?php 
                            $no = ($page - 1) * $perPage + 1;
                            foreach ($barangs as $barang): 
                            ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($barang['namabarang'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($barang['satuan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($barang['pabrik'] ?? '-') ?></td>
                                <td class="text-end"><?= number_format((float)($barang['stok'] ?? 0), 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php
                        $queryParams = [];
                        if (!empty($search)) $queryParams['search'] = $search;
                        if (!empty($kodepabrik)) $queryParams['kodepabrik'] = $kodepabrik;
                        if (!empty($kodegolongan)) $queryParams['kodegolongan'] = $kodegolongan;
                        if (!empty($kondisiStok) && $kondisiStok !== 'semua') $queryParams['kondisi_stok'] = $kondisiStok;
                        if (!empty($sortBy)) $queryParams['sort_by'] = $sortBy;
                        if (!empty($sortOrder)) $queryParams['sort_order'] = $sortOrder;
                        $queryParams['per_page'] = $perPage;
                        $baseQuery = http_build_query($queryParams);
                        ?>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&<?= $baseQuery ?>">Previous</a>
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
                            echo '<li class="page-item"><a class="page-link" href="?page=1&' . $baseQuery . '">1</a></li>';
                            if ($start > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            $active = $page == $i ? 'active' : '';
                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&' . $baseQuery . '">' . $i . '</a></li>';
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&' . $baseQuery . '">' . $totalPages . '</a></li>';
                        }
                        ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&<?= $baseQuery ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

