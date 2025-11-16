<?php
$title = 'Manajemen Users';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

// Helper function to generate sort URL
if (!function_exists('getSortUrl')) {
    function getSortUrl($column, $currentSortBy, $currentSortOrder, $search, $perPage) {
        $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
        $params = http_build_query([
            'page' => 1,
            'per_page' => $perPage,
            'search' => $search,
            'sort_by' => $column,
            'sort_order' => $newSortOrder
        ]);
        return '/users?' . $params;
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
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Users</h4>
                    <a href="/users/create" class="btn btn-primary btn-sm"><?= icon('add', 'me-2', 16) ?> Tambah User</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row search-filter-card">
                    <form method="GET" action="/users" id="searchForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" name="search" placeholder="Cari username, nama, atau email..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-4 col-md-2">
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                <?php foreach ([10, 20, 40, 60, 100] as $pp): ?>
                                <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                        <div class="col-4 col-md-2">
                            <a href="/users?page=1&per_page=10&sort_by=<?= htmlspecialchars($sortBy) ?>&sort_order=<?= htmlspecialchars($sortOrder) ?>" class="btn btn-outline-secondary w-100">Reset</a>
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
                                    <a href="<?= getSortUrl('id', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        ID <?= getSortIcon('id', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Foto</th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('username', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Username <?= getSortIcon('username', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('namalengkap', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Nama Lengkap <?= getSortIcon('namalengkap', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('email', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Email <?= getSortIcon('email', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('role', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Role <?= getSortIcon('role', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Kode Sales</th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('status', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Status <?= getSortIcon('status', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getSortUrl('created_at', $sortBy, $sortOrder, $search, $perPage) ?>" class="sort-link">
                                        Created At <?= getSortIcon('created_at', $sortBy, $sortOrder) ?>
                                    </a>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td align="center"><?= $user['id'] ?></td>
                                <td>
                                    <?php if ($user['picture'] && file_exists(__DIR__ . '/../../uploads/' . $user['picture'])): ?>
                                    <img src="<?= htmlspecialchars($baseUrl) ?>/uploads/<?= htmlspecialchars($user['picture']) ?>" alt="Profile" class="rounded-circle avatar-img avatar-img-md avatar-border-muted">
                                    <?php else: ?>
                                    <div class="bg-secondary avatar-placeholder avatar-placeholder-md">
                                        <span class="text-white fw-bold"><?= strtoupper(substr($user['namalengkap'], 0, 1)) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['namalengkap']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="badge bg-info"><?= ucfirst($user['role']) ?></span></td>
                                <td><?= htmlspecialchars($user['kodesales'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning"><?= icon('update', 'me-0 mb-1', 14) ?></a>
                                    <?php if ($user['id'] != Auth::user()['id']): ?>
                                    <a href="/users/delete/<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete('Apakah Anda yakin ingin menghapus user <strong><?= htmlspecialchars($user['namalengkap']) ?></strong>?', this.href)"><?= icon('cancel', 'me-0 mb-1', 14) ?></a>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&sort_by=<?= $sortBy ?>&sort_order=<?= $sortOrder ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 3;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        $buildLink = function ($p) use ($perPage, $search, $sortBy, $sortOrder) {
                            return '?page=' . $p
                                . '&per_page=' . $perPage
                                . '&search=' . urlencode($search)
                                . '&sort_by=' . urlencode($sortBy)
                                . '&sort_order=' . urlencode($sortOrder);
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
                            <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search) ?>&sort_by=<?= $sortBy ?>&sort_order=<?= $sortOrder ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

