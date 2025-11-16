<?php
$title = 'Master Supplier';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

if (!function_exists('getSortUrlMastersupplier')) {
    function getSortUrlMastersupplier($column, $currentSortBy, $currentSortOrder, $search, $perPage, $status) {
        $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
        $params = http_build_query([
            'page' => 1,
            'per_page' => $perPage,
            'search' => $search,
            'status' => $status,
            'sort_by' => $column,
            'sort_order' => $newSortOrder
        ]);
        return '/mastersupplier?' . $params;
    }
}

if (!function_exists('getSortIconMastersupplier')) {
    function getSortIconMastersupplier($column, $currentSortBy, $currentSortOrder) {
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
                <li class="breadcrumb-item active">Supplier</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Supplier</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row search-filter-card">
                    <form method="GET" action="/mastersupplier" id="searchForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="Cari kode, nama, atau alamat supplier..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="" <?= $status === '' ? 'selected' : '' ?>>Semua Status</option>
                                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="per_page" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ([10, 20, 40, 60, 100] as $pp): ?>
                                    <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="/mastersupplier?page=1&per_page=10&status=&sort_by=<?= htmlspecialchars($sortBy) ?>&sort_order=<?= htmlspecialchars($sortOrder) ?>" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
                        <input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder) ?>">
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('id', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        ID <?= getSortIconMastersupplier('id', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('kodesupplier', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Kode <?= getSortIconMastersupplier('kodesupplier', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('namasupplier', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Nama Supplier <?= getSortIconMastersupplier('namasupplier', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Alamat Supplier</th>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('kontakperson', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Kontak Person <?= getSortIconMastersupplier('kontakperson', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('notelepon', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        No Telepon <?= getSortIconMastersupplier('notelepon', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrlMastersupplier('status', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Status <?= getSortIconMastersupplier('status', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($suppliers)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td align="center"><?= $supplier['id'] ?></td>
                                <td><?= htmlspecialchars($supplier['kodesupplier']) ?></td>
                                <td><?= htmlspecialchars($supplier['namasupplier']) ?></td>
                                <td><?= htmlspecialchars($supplier['alamatsupplier'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($supplier['kontakperson'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($supplier['notelepon'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= ($supplier['status'] ?? '') === 'aktif' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars(ucfirst($supplier['status'] ?? '-')) ?>
                                    </span>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 3;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        $buildLink = function ($p) use ($perPage, $search) {
                            return '?page=' . $p . '&per_page=' . $perPage . '&search=' . urlencode($search);
                        };
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . $buildLink(1) . '">1</a></li>';
                            if ($start > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="' . $buildLink($i) . '">' . $i . '</a></li>';
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . $buildLink($totalPages) . '">' . $totalPages . '</a></li>';
                        }
                        ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


