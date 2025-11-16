<?php
$title = 'Login Log';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

if (!function_exists('getLoginLogSortUrl')) {
    function getLoginLogSortUrl($column, $currentSortBy, $currentSortOrder, $queryParams) {
        $newSortOrder = ($currentSortBy === $column && strtoupper($currentSortOrder) === 'ASC') ? 'DESC' : 'ASC';
        $queryParams['sort_by'] = $column;
        $queryParams['sort_order'] = $newSortOrder;
        $queryParams['page'] = 1;
        return '/login-logs?' . http_build_query($queryParams);
    }
}

if (!function_exists('getLoginLogSortIcon')) {
    function getLoginLogSortIcon($column, $currentSortBy, $currentSortOrder) {
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = rtrim($config['base_url'], '/');
        if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
            $baseUrl = '/';
        }

        if ($currentSortBy !== $column) {
            $iconPath = $baseUrl . '/assets/icons/arrows-up-down.svg';
            return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort" width="14" height="14">';
        }

        if (strtoupper($currentSortOrder) === 'ASC') {
            $iconPath = $baseUrl . '/assets/icons/arrow-up.svg';
        } else {
            $iconPath = $baseUrl . '/assets/icons/arrow-down.svg';
        }
        return '<img src="' . htmlspecialchars($iconPath) . '" alt="sort" width="14" height="14">';
    }
}

function formatDateTime($value) {
    if (empty($value)) {
        return '-';
    }
    return date('d/m/Y H:i:s', strtotime($value));
}

function formatDuration($start, $end) {
    if (empty($start) || empty($end)) {
        return '-';
    }
    $startTime = new DateTime($start);
    $endTime = new DateTime($end);
    $diff = $startTime->diff($endTime);
    $parts = [];
    if ($diff->d) {
        $parts[] = $diff->d . ' hr';
    }
    if ($diff->h) {
        $parts[] = $diff->h . ' jam';
    }
    if ($diff->i) {
        $parts[] = $diff->i . ' mnt';
    }
    if ($diff->s && !$parts) {
        $parts[] = $diff->s . ' dtk';
    }
    return $parts ? implode(' ', $parts) : ($diff->s . ' dtk');
}

$queryParams = [
    'search' => $search,
    'status' => $status,
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'per_page' => $perPage
];

require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Login Log</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0">Aktivitas Login Pengguna</h4>
            </div>
            <div class="card-body">
                <div class="row search-filter-card">
                    <form method="GET" action="/login-logs" id="logFilterForm">
                        <div class="row g-2">
                            <div class="col-12 col-md-6 col-lg-3">
                                <input type="text" class="form-control" name="search" placeholder="Cari username, nama, IP, user agent..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-12 col-md-3 col-lg-1">
                                <select name="status" class="form-select">
                                    <option value="">Status</option>
                                    <option value="success" <?= $status === 'success' ? 'selected' : '' ?>>Berhasil</option>
                                    <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Gagal</option>
                                    <option value="logout" <?= $status === 'logout' ? 'selected' : '' ?>>Logout</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
                            </div>
                            <div class="col-4 col-md-3 col-lg-1">
                                <select name="per_page" class="form-select" onchange="document.getElementById('logFilterForm').submit()">
                                    <?php foreach ($validPerPage as $pp): ?>
                                    <option value="<?= $pp ?>" <?= $perPage == $pp ? 'selected' : '' ?>><?= $pp ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4 col-md-3 col-lg-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary w-100">Terapkan</button>
                            </div>
                            <div class="col-4 col-md-3 col-lg-1 d-flex align-items-end">
                                <a href="/login-logs" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                        <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
                        <input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder) ?>">
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="sortable">
                                    <a href="<?= getLoginLogSortUrl('login_at', $sortBy, $sortOrder, $queryParams) ?>" class="sort-link">Login <?= getLoginLogSortIcon('login_at', $sortBy, $sortOrder) ?></a>
                                </th>
                                <th class="sortable">
                                    <a href="<?= getLoginLogSortUrl('logout_at', $sortBy, $sortOrder, $queryParams) ?>" class="sort-link">Logout <?= getLoginLogSortIcon('logout_at', $sortBy, $sortOrder) ?></a>
                                </th>
                                <th>Durasi</th>
                                <th>User</th>
                                <th class="sortable">
                                    <a href="<?= getLoginLogSortUrl('status', $sortBy, $sortOrder, $queryParams) ?>" class="sort-link">Status <?= getLoginLogSortIcon('status', $sortBy, $sortOrder) ?></a>
                                </th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data log.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= formatDateTime($log['login_at']) ?></td>
                                <td><?= formatDateTime($log['logout_at']) ?></td>
                                <td><?= formatDuration($log['login_at'], $log['logout_at']) ?></td>
                                <td>
                                    <?php if (!empty($log['username'])): ?>
                                        <strong><?= htmlspecialchars($log['username']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($log['namalengkap'] ?? '-') ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">User tidak dikenal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['status'] === 'success'): ?>
                                        <span class="badge bg-success">Berhasil</span>
                                    <?php elseif ($log['status'] === 'failed'): ?>
                                        <span class="badge bg-danger">Gagal</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">Logout</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                                <td class="table-text-wrap-260">
                                    <small><?= htmlspecialchars($log['user_agent'] ?? '-') ?></small>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search ?? '') ?>&date_from=<?= urlencode($dateFrom ?? '') ?>&date_to=<?= urlencode($dateTo ?? '') ?>">Previous</a>
                        </li>
                        <?php
                        $maxLinks = 3;
                        $half = (int)floor($maxLinks / 2);
                        $start = max(1, $page - $half);
                        $end = min($totalPages, $start + $maxLinks - 1);
                        if ($end - $start + 1 < $maxLinks) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                        $buildLink = function ($p) use ($perPage, $search, $dateFrom, $dateTo) {
                            return '?page=' . $p
                                . '&per_page=' . $perPage
                                . '&search=' . urlencode($search ?? '')
                                . '&date_from=' . urlencode($dateFrom ?? '')
                                . '&date_to=' . urlencode($dateTo ?? '');
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
                            <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&search=<?= urlencode($search ?? '') ?>&date_from=<?= urlencode($dateFrom ?? '') ?>&date_to=<?= urlencode($dateTo ?? '') ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

