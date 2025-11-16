<?php
$title = 'Ubah Password';
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
                <li class="breadcrumb-item"><a href="/profile">Profile</a></li>
                <li class="breadcrumb-item active">Ubah Password</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Form Ubah Password</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/profile/change-password">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
                        <div class="position-relative password-field">
                            <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Masukkan password lama">
                            <button type="button" class="password-toggle" data-target="current_password" aria-label="Tampilkan password lama">
                                <span class="password-toggle-icon-show"><?= icon('eye', '', 18) ?></span>
                                <span class="password-toggle-icon-hide d-none"><?= icon('eye-slash', '', 18) ?></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="position-relative password-field">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter">
                            <button type="button" class="password-toggle" data-target="new_password" aria-label="Tampilkan password baru">
                                <span class="password-toggle-icon-show"><?= icon('eye', '', 18) ?></span>
                                <span class="password-toggle-icon-hide d-none"><?= icon('eye-slash', '', 18) ?></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="position-relative password-field">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Ulangi password baru">
                            <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Tampilkan konfirmasi password">
                                <span class="password-toggle-icon-show"><?= icon('eye', '', 18) ?></span>
                                <span class="password-toggle-icon-hide d-none"><?= icon('eye-slash', '', 18) ?></span>
                            </button>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="/profile" class="btn btn-secondary">
                            <?= icon('back', 'me-2', 16) ?> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <?= icon('save', 'me-2', 16) ?> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

