<?php
$title = 'Master Customer';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

// Helper function to generate sort URL
if (!function_exists('getSortUrl')) {
    function getSortUrl($column, $currentSortBy, $currentSortOrder, $search, $perPage, $status) {
        $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
        $params = http_build_query([
            'page' => 1,
            'per_page' => $perPage,
            'search' => $search,
            'sort_by' => $column,
            'sort_order' => $newSortOrder,
            'status' => $status
        ]);
        return '/mastercustomer?' . $params;
    }
}

// Helper function to get sort icon
if (!function_exists('getSortIcon')) {
    function getSortIcon($column, $currentSortBy, $currentSortOrder) {
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
        } else {
            $iconPath = $baseUrl . '/assets/icons/arrow-down.svg';
            return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort-down" class="sort-icon icon-inline" width="14" height="14">';
        }
    }
}

require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Customer</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Customer</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row search-filter-card">
                    <form method="GET" action="/mastercustomer" id="searchForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-5 col-lg-6">
                            <input type="text" class="form-control" name="search" placeholder="Cari nama customer, alamat, kota, atau nama WP..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <?php $normalizedStatus = strtolower($status ?? ''); ?>
                                <option value="" <?= $normalizedStatus === '' ? 'selected' : '' ?>>Semua Status</option>
                                <option value="baru" <?= $normalizedStatus === 'baru' ? 'selected' : '' ?>>Baru</option>
                                <option value="updated" <?= $normalizedStatus === 'updated' ? 'selected' : '' ?>>Updated</option>
                                <option value="aktif" <?= $normalizedStatus === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $normalizedStatus === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                <?php foreach ([10, 20, 40, 50, 100, 200, 500] as $pp): ?>
                                <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2 col-lg-1">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                        <div class="col-6 col-md-2 col-lg-1">
                            <a href="/mastercustomer?page=1&per_page=10&sort_by=<?= htmlspecialchars($sortBy) ?>&sort_order=<?= htmlspecialchars($sortOrder) ?>&status=" class="btn btn-outline-secondary w-100">Reset</a>
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
                                    <a href="<?= getSortUrl('id', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        ID <?= getSortIcon('id', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('kodecustomer', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Kode <?= getSortIcon('kodecustomer', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('namacustomer', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Nama Customer <?= getSortIcon('namacustomer', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Alamat Customer</th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('kotacustomer', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Kota <?= getSortIcon('kotacustomer', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>No Telepon</th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('status', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        Status <?= getSortIcon('status', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('statuspkp', $sortBy, $sortOrder, $search, $perPage, $status) ?>" class="sort-link">
                                        PKP <?= getSortIcon('statuspkp', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                            <?php
                                $statusValue = strtolower($customer['status'] ?? 'baru');
                                switch ($statusValue) {
                                    case 'updated':
                                        $statusBadgeClass = 'bg-info text-dark';
                                        break;
                                    case 'aktif':
                                        $statusBadgeClass = 'bg-success';
                                        break;
                                    case 'nonaktif':
                                        $statusBadgeClass = 'bg-secondary';
                                        break;
                                    case 'baru':
                                    default:
                                        $statusBadgeClass = 'bg-primary';
                                        break;
                                }
                            ?>
                            <tr>
                                <td align="center"><?= $customer['id'] ?></td>
                                <td><?= htmlspecialchars($customer['kodecustomer']) ?></td>
                                <td><?= htmlspecialchars($customer['namacustomer'].', '.$customer['namabadanusaha']) ?></td>
                                <td><?= htmlspecialchars($customer['alamatcustomer'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($customer['kotacustomer'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($customer['notelepon'] ?? '-') ?></td>
                                <td align="center">
                                    <span class="badge <?= $statusBadgeClass ?>">
                                        <?= htmlspecialchars(ucfirst($statusValue)) ?>
                                    </span>
                                </td>
                                <td align="center">
                                    <?php $statusPkpValue = strtolower($customer['statuspkp'] ?? 'pkp'); ?>
                                    <span class="badge <?= $statusPkpValue === 'pkp' ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= $statusPkpValue === 'pkp' ? 'PKP' : 'Non PKP' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/mastercustomer/edit/<?= $customer['id'] ?>" class="btn btn-sm btn-warning"><?= icon('update', 'me-0 mb-1', 14) ?></a>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort_by=<?= $sortBy ?>&sort_order=<?= $sortOrder ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 3;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        $buildLink = function ($p) use ($perPage, $search, $status) {
                            return '?page=' . $p
                                . '&per_page=' . $perPage
                                . '&search=' . urlencode($search)
                                . '&status=' . urlencode($status);
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
                            <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort_by=<?= $sortBy ?>&sort_order=<?= $sortOrder ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

