<?php
$title = 'Tambah Aktivitas';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/tabelaktivitas">Tabel Aktivitas</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0">Tambah Aktivitas Baru</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/tabelaktivitas/create">
                    <div class="mb-3">
                        <label for="aktivitas" class="form-label">Nama Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="aktivitas" name="aktivitas" required placeholder="Masukkan nama aktivitas">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <?php foreach ($allowedStatuses as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>" <?= $option === 'aktif' ? 'selected' : '' ?>><?= htmlspecialchars(ucwords($option)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/tabelaktivitas" class="btn btn-secondary">
                            <?= icon('back', 'me-2', 16) ?> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?= icon('save', 'me-2', 16) ?> Simpan Aktivitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


