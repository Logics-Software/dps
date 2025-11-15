<?php
$title = 'Check-out Kunjungan';
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

$additionalStyles = $additionalStyles ?? [];
$additionalStyles[] = 'https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css';
$additionalStyles[] = $baseUrl . '/assets/css/mapbox-gl-geocoder.css';

$additionalScripts = $additionalScripts ?? [];
$additionalScripts[] = 'https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js';
$additionalScripts[] = $baseUrl . '/assets/js/mapbox-gl-geocoder.min.js';

$mapboxToken = $mapboxToken ?? ($config['mapbox_access_token'] ?? '');
$hasMapbox = !empty($mapboxToken);

require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Check-out</h2>
        </div>
        <div>
            <a href="/visits" class="btn btn-secondary"><?= icon('back', 'mb-1 me-2', 16) ?>  Kembali</a>
        </div>
    </div>
</div>

<?php if (empty($visit)): ?>
<div class="alert alert-danger">Data kunjungan tidak ditemukan.</div>
<?php else: ?>

<form method="POST" action="/visits/checkout/<?= $visit['visit_id'] ?>" id="visitCheckoutForm" enctype="multipart/form-data">
    <input type="hidden" name="check_out_lat" id="checkOutLat">
    <input type="hidden" name="check_out_long" id="checkOutLong">

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Detail Kunjungan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="fw-semibold mb-1"><?= htmlspecialchars($visit['namacustomer'] ?? '-') ?></h5>
                    <div class="text-muted small mb-2">
                        <?= htmlspecialchars($visit['kodecustomer']) ?> &bull; Alamat: <?= htmlspecialchars($visit['alamatcustomer'].', '.$visit['kotacustomer'].', '.$visit['kotacustomer'] ?? '-') ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">Mulai</div>
                            <div class="fw-semibold"><?= date('d/m/Y H:i', strtotime($visit['check_in_time'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Lokasi Check-in</div>
                            <div class="fw-semibold">Lat <?= htmlspecialchars(number_format($visit['check_in_lat'], 6)) ?>, Lng <?= htmlspecialchars(number_format($visit['check_in_long'], 6)) ?></div>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-semibold mb-3">Aktivitas Kunjungan</h6>
                <form action="/visits/<?= $visit['visit_id'] ?>/activities" method="POST" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Aktivitas</label>
                        <select name="activity_type" class="form-select" required>
                            <option value="">Pilih aktivitas</option>
                            <?php if (!empty($activityOptions)): ?>
                                <?php foreach ($activityOptions as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Belum ada aktivitas aktif</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" placeholder="Catatan singkat aktivitas">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary" <?= empty($activityOptions) ? 'disabled' : '' ?>>Tambah</button>
                    </div>
                </form>

                <div class="list-group small">
                    <?php if (empty($activities)): ?>
                        <div class="list-group-item text-muted">Belum ada aktivitas tercatat.</div>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($activity['activity_type']) ?></strong>
                                <span class="text-muted"><?= date('d/m/Y H:i', strtotime($activity['timestamp'])) ?></span>
                            </div>
                            <div><?= nl2br(htmlspecialchars($activity['deskripsi'] ?? '-')) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <hr>

                <h6 class="fw-semibold mb-3">Upload File/Gambar/Foto</h6>
                <div class="mb-3">
                    <input type="file" form="visitCheckoutForm" name="visit_files[]" id="visitFiles" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
                    <div class="form-text">Maksimal 5MB per file. Bisa upload lebih dari 1 file.</div>
                    <div id="filePreview" class="mt-2"></div>
                    <?php if (!empty($visitFiles)): ?>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">File yang sudah diupload:</small>
                            <div class="list-group list-group-flush">
                                <?php foreach ($visitFiles as $file): ?>
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            $isImage = in_array(strtolower(pathinfo($file['original_filename'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                                            if ($isImage): 
                                            ?>
                                                <img src="<?= $baseUrl . '/uploads/' . htmlspecialchars($file['filename']) ?>" alt="<?= htmlspecialchars($file['original_filename']) ?>" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <span class="me-2">📄</span>
                                            <?php endif; ?>
                                            <div>
                                                <div class="small fw-semibold"><?= htmlspecialchars($file['original_filename']) ?></div>
                                                <div class="small text-muted"><?= number_format($file['file_size'] / 1024, 2) ?> KB</div>
                                            </div>
                                        </div>
                                        <a href="<?= $baseUrl . '/uploads/' . htmlspecialchars($file['filename']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Selesaikan Kunjungan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan Akhir</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control" placeholder="Ringkasan hasil kunjungan"><?= htmlspecialchars($visit['catatan'] ?? '') ?></textarea>
                    </div>
                    <!-- Hidden file input inside form as backup -->
                    <input type="file" name="visit_files[]" id="visitFilesBackup" class="d-none" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Koordinat Check-out</div>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Lat</span>
                                <input type="text" class="form-control" id="displayOutLat" readonly>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Lng</span>
                                <input type="text" class="form-control" id="displayOutLng" readonly>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-light w-100 text-primary" id="btnCaptureCheckout" <?= $hasMapbox ? '' : 'disabled' ?>>Refresh Lokasi Saat Ini</button>
                        </div>
                        <div class="small text-muted mt-2" id="checkoutStatus">
                            <?= $hasMapbox
                                ? 'Menunggu pembacaan lokasi perangkat...'
                                : 'Mapbox access token belum tersedia. Tambahkan MAPBOX_ACCESS_TOKEN untuk mengaktifkan peta.'
                            ?>
                        </div>
                    </div>
                    <div class="mapbox-wrapper mapbox-height-240">
                        <div id="mapboxCheckout" class="mapbox-canvas-220"></div>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <button type="submit" class="btn btn-success" id="btnSubmitCheckout" disabled>Selesaikan Kunjungan</button>
                </div>
            </div>
    </div>
</div>
</form>

<?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

<?php if ($hasMapbox && !empty($visit)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapboxToken = <?= json_encode($mapboxToken) ?>;
    mapboxgl.accessToken = mapboxToken;

    const map = new mapboxgl.Map({
        container: 'mapboxCheckout',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [<?= (float)$visit['check_in_long'] ?>, <?= (float)$visit['check_in_lat'] ?>],
        zoom: 13
    });

    new mapboxgl.Marker({ color: '#2563eb' })
        .setLngLat([<?= (float)$visit['check_in_long'] ?>, <?= (float)$visit['check_in_lat'] ?>])
        .setPopup(new mapboxgl.Popup({ offset: 16 }).setHTML('<strong>Lokasi Check-in</strong>'))
        .addTo(map);

    let checkoutMarker = null;
    const btnCapture = document.getElementById('btnCaptureCheckout');
    const displayLat = document.getElementById('displayOutLat');
    const displayLng = document.getElementById('displayOutLng');
    const hiddenLat = document.getElementById('checkOutLat');
    const hiddenLng = document.getElementById('checkOutLong');
    const statusText = document.getElementById('checkoutStatus');
    const submitBtn = document.getElementById('btnSubmitCheckout');
    let geoWatchId = null;

    function updateCheckoutLocation(lat, lng) {
        hiddenLat.value = lat;
        hiddenLng.value = lng;
        displayLat.value = lat.toFixed(6);
        displayLng.value = lng.toFixed(6);
        submitBtn.disabled = false;

        if (checkoutMarker) {
            checkoutMarker.setLngLat([lng, lat]);
        } else {
            checkoutMarker = new mapboxgl.Marker({ color: '#10b981' })
                .setLngLat([lng, lat])
                .setPopup(new mapboxgl.Popup({ offset: 16 }).setHTML('<strong>Lokasi Check-out</strong>'))
                .addTo(map);
        }

        map.easeTo({ center: [lng, lat], zoom: 15 });
    }

    function setStatus(message, type = 'muted') {
        statusText.className = 'small text-' + type;
        statusText.textContent = message;
    }

    function handleGeoSuccess(position) {
        const { latitude, longitude } = position.coords;
        updateCheckoutLocation(latitude, longitude);
        setStatus('Koordinat check-out otomatis diperbarui dari lokasi perangkat.', 'success');
    }

    function handleGeoError(error) {
        console.error(error);
        setStatus('Gagal memperoleh lokasi otomatis. Pastikan izin lokasi telah diberikan.', 'danger');
    }

    if (navigator.geolocation) {
        setStatus('Mengambil lokasi perangkat...', 'info');
        geoWatchId = navigator.geolocation.watchPosition(handleGeoSuccess, handleGeoError, {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 15000
        });
    } else {
        setStatus('Perangkat tidak mendukung geolocation.', 'danger');
    }

    btnCapture?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            setStatus('Perangkat tidak mendukung geolocation.', 'danger');
            return;
        }
        setStatus('Mengambil lokasi GPS...', 'info');
        navigator.geolocation.getCurrentPosition(handleGeoSuccess, handleGeoError, { enableHighAccuracy: true });
    });

    map.on('click', (ev) => {
        updateCheckoutLocation(ev.lngLat.lat, ev.lngLat.lng);
        setStatus('Koordinat check-out dapat diedit dengan klik peta.', 'success');
    });

    window.addEventListener('beforeunload', () => {
        if (geoWatchId !== null && navigator.geolocation) {
            navigator.geolocation.clearWatch(geoWatchId);
        }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('visitFiles');
    const fileInputBackup = document.getElementById('visitFilesBackup');
    const filePreview = document.getElementById('filePreview');
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    
    // Sync files from external input to backup input inside form
    if (fileInput && fileInputBackup) {
        fileInput.addEventListener('change', function(e) {
            // Copy files to backup input inside form
            if (e.target.files && e.target.files.length > 0) {
                // Create new FileList-like object
                const dataTransfer = new DataTransfer();
                Array.from(e.target.files).forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInputBackup.files = dataTransfer.files;
            }
            
            filePreview.innerHTML = '';
            const files = Array.from(e.target.files);
            
            if (files.length === 0) {
                return;
            }
            
            const validFiles = [];
            const errors = [];
            
            files.forEach((file, index) => {
                // Check file size
                if (file.size > maxFileSize) {
                    errors.push(`File "${file.name}" terlalu besar (maksimal 5MB)`);
                    return;
                }
                
                // Check file type
                const extension = file.name.split('.').pop().toLowerCase();
                const allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                if (!allowedTypes.includes(extension)) {
                    errors.push(`File "${file.name}" tidak diizinkan (hanya: ${allowedTypes.join(', ')})`);
                    return;
                }
                
                validFiles.push(file);
            });
            
            // Show errors
            if (errors.length > 0) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.innerHTML = '<strong>Error:</strong><ul class="mb-0 mt-2"><li>' + errors.join('</li><li>') + '</li></ul>';
                filePreview.appendChild(errorDiv);
            }
            
            // Show preview for valid files
            if (validFiles.length > 0) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'alert alert-info';
                previewDiv.innerHTML = '<strong>File yang akan diupload:</strong><ul class="mb-0 mt-2">' + 
                    validFiles.map(file => {
                        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                        return `<li>${file.name} (${sizeMB} MB)</li>`;
                    }).join('') + '</ul>';
                filePreview.appendChild(previewDiv);
            }
        });
    }
});
</script>

