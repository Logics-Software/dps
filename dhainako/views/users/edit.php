<?php
$title = 'Edit User';
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
                <li class="breadcrumb-item"><a href="/users">Users</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Data User</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/users/edit/<?= $user['id'] ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required placeholder="Masukkan username">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="namalengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namalengkap" name="namalengkap" value="<?= htmlspecialchars($user['namalengkap']) ?>" required placeholder="Masukkan nama lengkap">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="contoh@email.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="manajemen" <?= $user['role'] == 'manajemen' ? 'selected' : '' ?>>Manajemen</option>
                                <option value="sales" <?= $user['role'] == 'sales' ? 'selected' : '' ?>>Sales</option>
                                <option value="operator" <?= $user['role'] == 'operator' ? 'selected' : '' ?>>Operator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3" id="kodesales-wrapper">
                            <label for="kodesales" class="form-label">Master Sales <span class="text-danger" id="kodesales-required">*</span></label>
                            <select class="form-select" id="kodesales" name="kodesales">
                                <option value="">Pilih Sales</option>
                                <?php if (isset($mastersales) && !empty($mastersales)): ?>
                                <?php foreach ($mastersales as $ms): ?>
                                <option value="<?= htmlspecialchars($ms['kodesales']) ?>" <?= ($user['kodesales'] ?? '') == $ms['kodesales'] ? 'selected' : '' ?>><?= htmlspecialchars($ms['kodesales']) ?> - <?= htmlspecialchars($ms['namasales']) ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" <?= $user['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="non aktif" <?= $user['status'] == 'non aktif' ? 'selected' : '' ?>>Non Aktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="picture" class="form-label">Foto Profil</label>
                        <?php if ($user['picture'] && file_exists(__DIR__ . '/../../uploads/' . $user['picture'])): ?>
                        <div class="mb-3">
                            <p class="mb-2"><strong>Foto Saat Ini:</strong></p>
                            <img src="<?= htmlspecialchars($baseUrl) ?>/uploads/<?= htmlspecialchars($user['picture']) ?>" alt="Current Picture" class="img-thumbnail rounded image-preview-lg">
                        </div>
                        <?php else: ?>
                        <div class="mb-3">
                            <p class="mb-2 text-muted"><em>Tidak ada foto profil</em></p>
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF (Max 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
                        <div id="picture-preview" class="mt-2"></div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="/users" class="btn btn-secondary">
                            <?= icon('back', 'me-2', 16) ?> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?= icon('update', 'me-2', 16) ?> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const kodesalesWrapper = document.getElementById('kodesales-wrapper');
    const kodesalesSelect = document.getElementById('kodesales');
    const kodesalesRequired = document.getElementById('kodesales-required');
    
    function toggleKodesales() {
        if (roleSelect.value === 'sales') {
            kodesalesWrapper.style.display = 'block';
            kodesalesSelect.setAttribute('required', 'required');
            kodesalesRequired.style.display = 'inline';
        } else {
            kodesalesWrapper.style.display = 'block';
            kodesalesSelect.removeAttribute('required');
            kodesalesSelect.value = '';
            kodesalesRequired.style.display = 'none';
        }
    }
    
    // Initial check
    toggleKodesales();
    
    // Listen for role changes
    roleSelect.addEventListener('change', toggleKodesales);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

