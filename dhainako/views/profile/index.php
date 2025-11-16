<?php
$title = 'Profile';
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
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Informasi Profil</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/profile" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <?php 
                        $userPicture = $user['picture'] ?? null;
                        $picturePath = null;
                        if ($userPicture && file_exists(__DIR__ . '/../../uploads/' . $userPicture)) {
                            $picturePath = $baseUrl . '/uploads/' . htmlspecialchars($userPicture);
                        }
                        ?>
                        <?php if ($picturePath): ?>
                        <img src="<?= $picturePath ?>" alt="Profile Picture" class="profile-picture rounded-circle mb-3">
                        <?php else: ?>
                        <div class="profile-picture-placeholder rounded-circle mx-auto mb-3">
                            <?= strtoupper(substr($user['namalengkap'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label for="picture" class="btn btn-sm btn-outline-primary">
                                📷 Ganti Foto
                            </label>
                            <input type="file" class="d-none" id="picture" name="picture" accept="image/*">
                            <p class="text-muted mt-2 mb-0"><small>Format: JPG, PNG, GIF (Max 2MB)</small></p>
                        </div>
                    </div>
                    
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
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="contoh@email.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control form-control-static" value="<?= ucfirst($user['role']) ?>" disabled>
                    </div>
                    
                    <?php if ($user['kodesales']): ?>
                    <div class="mb-3">
                        <label class="form-label">Kode Sales</label>
                        <input type="text" class="form-control form-control-static" value="<?= htmlspecialchars($user['kodesales']) ?>" disabled>
                    </div>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="/dashboard" class="btn btn-secondary">
                            <?= icon('back', 'me-2', 16) ?> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?= icon('update', 'me-2', 16) ?> Update Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Biometric Credentials Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0">Biometrik Login</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Daftarkan biometrik (fingerprint/face ID) untuk login lebih cepat dan aman.</p>
                
                <div id="biometricSection">
                    <div id="biometricNotSupported" class="alert alert-warning" style="display: none;">
                        Browser Anda tidak mendukung WebAuthn. Pastikan menggunakan browser modern (Chrome, Firefox, Edge, Safari).
                    </div>
                    
                    <div id="biometricCredentialsList" class="mb-3"></div>
                    
                    <button type="button" id="btnRegisterBiometric" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <path d="M12 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path>
                            <path d="M12 3c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"></path>
                        </svg>
                        Daftarkan Biometrik
                    </button>
                    
                    <div id="biometricMessage" class="alert mt-3 mb-0" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/webauthn.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const biometricSection = document.getElementById('biometricSection');
    const biometricNotSupported = document.getElementById('biometricNotSupported');
    const biometricCredentialsList = document.getElementById('biometricCredentialsList');
    const btnRegisterBiometric = document.getElementById('btnRegisterBiometric');
    const biometricMessage = document.getElementById('biometricMessage');

    // Check WebAuthn support
    if (!WebAuthnHelper.isSupported()) {
        biometricNotSupported.style.display = 'block';
        btnRegisterBiometric.style.display = 'none';
        return;
    }

    // Load credentials
    loadCredentials();

    // Register biometric
    btnRegisterBiometric.addEventListener('click', async function() {
        btnRegisterBiometric.disabled = true;
        btnRegisterBiometric.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        hideMessage();

        try {
            const result = await WebAuthnHelper.registerBiometric();
            
            if (result.success) {
                showMessage('Biometrik berhasil didaftarkan! Anda sekarang dapat menggunakan biometrik untuk login.', 'success');
                loadCredentials();
            } else {
                showMessage(result.error || 'Gagal mendaftarkan biometrik', 'danger');
            }
        } catch (error) {
            console.error('Biometric registration error:', error);
            
            let errorMessage = 'Gagal mendaftarkan biometrik. ';
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Autentikasi dibatalkan atau tidak diizinkan. Pastikan biometrik sudah dikonfigurasi di perangkat Anda.';
            } else if (error.name === 'InvalidStateError') {
                errorMessage += 'Credential sudah terdaftar atau tidak valid.';
            } else if (error.message) {
                errorMessage += error.message;
            } else {
                errorMessage += 'Pastikan perangkat Anda mendukung biometrik dan sudah dikonfigurasi.';
            }
            
            showMessage(errorMessage, 'danger');
        } finally {
            btnRegisterBiometric.disabled = false;
            btnRegisterBiometric.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M12 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path><path d="M12 3c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"></path></svg>Daftarkan Biometrik';
        }
    });

    async function loadCredentials() {
        try {
            const credentials = await WebAuthnHelper.listCredentials();
            
            if (credentials.length === 0) {
                biometricCredentialsList.innerHTML = '<p class="text-muted mb-0">Belum ada biometrik terdaftar.</p>';
                return;
            }

            let html = '<div class="list-group">';
            credentials.forEach(cred => {
                const createdDate = new Date(cred.created_at).toLocaleDateString('id-ID');
                const lastUsed = cred.last_used_at ? new Date(cred.last_used_at).toLocaleDateString('id-ID') : 'Belum pernah digunakan';
                const shortId = cred.credential_id.substring(0, 20) + '...';
                
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Biometrik Credential</div>
                            <small class="text-muted">ID: ${shortId}</small><br>
                            <small class="text-muted">Dibuat: ${createdDate} | Terakhir digunakan: ${lastUsed}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCredential('${cred.credential_id}')">
                            Hapus
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            
            biometricCredentialsList.innerHTML = html;
        } catch (error) {
            console.error('Error loading credentials:', error);
            biometricCredentialsList.innerHTML = '<p class="text-danger mb-0">Gagal memuat daftar biometrik.</p>';
        }
    }

    window.deleteCredential = async function(credentialId) {
        if (!confirm('Yakin ingin menghapus biometrik ini?')) {
            return;
        }

        try {
            const success = await WebAuthnHelper.deleteCredential(credentialId);
            
            if (success) {
                showMessage('Biometrik berhasil dihapus', 'success');
                loadCredentials();
            } else {
                showMessage('Gagal menghapus biometrik', 'danger');
            }
        } catch (error) {
            console.error('Error deleting credential:', error);
            showMessage('Gagal menghapus biometrik', 'danger');
        }
    };

    function showMessage(message, type) {
        biometricMessage.textContent = message;
        biometricMessage.className = 'alert alert-' + type + ' mt-3 mb-0';
        biometricMessage.style.display = 'block';
        setTimeout(hideMessage, 5000);
    }

    function hideMessage() {
        biometricMessage.style.display = 'none';
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

