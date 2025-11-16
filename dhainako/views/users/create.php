<?php
$title = 'Tambah User';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/users">Users</a></li>
                <li class="breadcrumb-item active">Tambah User</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Tambah Data User</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/users/create" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="namalengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namalengkap" name="namalengkap" required placeholder="Masukkan nama lengkap">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="contoh@email.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="position-relative password-field">
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Minimal 6 karakter">
                                <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password">
                                    <span class="password-toggle-icon-show"><?= icon('eye', '', 18) ?></span>
                                    <span class="password-toggle-icon-hide d-none"><?= icon('eye-slash', '', 18) ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="manajemen">Manajemen</option>
                                <option value="sales" selected>Sales</option>
                                <option value="operator">Operator</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="kodesales-wrapper">
                            <label for="kodesales" class="form-label">Master Sales <span class="text-danger" id="kodesales-required">*</span></label>
                            <select class="form-select" id="kodesales" name="kodesales">
                                <option value="">Pilih Sales</option>
                                <?php if (isset($mastersales) && !empty($mastersales)): ?>
                                <?php foreach ($mastersales as $ms): ?>
                                <option value="<?= htmlspecialchars($ms['kodesales']) ?>"><?= htmlspecialchars($ms['kodesales']) ?> - <?= htmlspecialchars($ms['namasales']) ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" selected>Aktif</option>
                                <option value="non aktif">Non Aktif</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="picture" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF (Max 2MB)</small>
                            <div id="picture-preview" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="/users" class="btn btn-secondary">
                            <?= icon('back', 'me-2', 16) ?> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?= icon('save', 'me-2', 16) ?> Simpan User
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

