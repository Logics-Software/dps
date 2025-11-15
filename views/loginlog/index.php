<?php
$title = 'Login Log';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Login Log</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="/login-logs" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="success" <?= ($status ?? '') === 'success' ? 'selected' : '' ?>>Success</option>
                            <option value="failed" <?= ($status ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="logout" <?= ($status ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($dateFrom ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($dateTo ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="/login-logs" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Login At</th>
                            <th>Logout At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No data found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['id']) ?></td>
                            <td><?= htmlspecialchars($log['username'] ?? 'N/A') ?> (<?= htmlspecialchars($log['namalengkap'] ?? 'N/A') ?>)</td>
                            <td><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($log['login_at'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($log['logout_at'] ?? '-') ?></td>
                            <td>
                                <?php
                                $statusClass = 'secondary';
                                if ($log['status'] === 'success') $statusClass = 'success';
                                elseif ($log['status'] === 'failed') $statusClass = 'danger';
                                elseif ($log['status'] === 'logout') $statusClass = 'info';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars($log['status'] ?? 'N/A') ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (isset($total) && $total > 0): ?>
            <div class="mt-3">
                <p>Total: <?= $total ?> records</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

