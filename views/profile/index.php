<?php
$title = 'Profile';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="breadcrumb-item">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">    
                        <h4 class="mb-0">Edit Profil</h4>
                    </div>
                </div>

                <form method="POST" action="/profile" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <?php 
                            $userPicture = $user['picture'] ?? null;
                            $picturePath = null;
                            if ($userPicture && file_exists(__DIR__ . '/../../uploads/' . $userPicture)) {
                                $picturePath = $baseUrl . '/uploads/' . htmlspecialchars($userPicture);
                            }
                            ?>
                            <?php if ($picturePath): ?>
                            <img src="<?= $picturePath ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                                <span class="text-white fw-bold" style="font-size: 3rem;"><?= strtoupper(substr($user['namalengkap'], 0, 1)) ?></span>
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
                            <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                        </div>
                        
                        <?php if ($user['kodesales']): ?>
                        <div class="mb-3">
                            <label class="form-label">Kode Sales</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['kodesales']) ?>" disabled>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="/dashboard" class="btn btn-secondary"><?= icon('cancel', 'me-1 mb-1', 18) ?>Batal</a>
                        <div>
                            <button type="submit" class="btn btn-primary"><?= icon('save', 'me-1 mb-1', 18) ?>Update Profil</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

