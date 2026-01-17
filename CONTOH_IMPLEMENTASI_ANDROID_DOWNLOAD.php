<?php
/**
 * CONTOH IMPLEMENTASI DOWNLOAD PDF UNTUK ANDROID APP
 * 
 * File ini menunjukkan cara update laporan view untuk support Android app download.
 * Copy dan adapt ke laporan lainnya (daftar-stok.php, daftar-harga.php, dst)
 */

// Bagian yang perlu diubah di views/laporan/daftar-barang.php

// =========================================
// 1. BUTTON DOWNLOAD PDF (sebelum)
// =========================================
/*
<a href="/laporan/daftar-barang?export=pdf<?= !empty($exportQuery) ? '&' . $exportQuery : '' ?>" class="btn btn-danger btn-sm">
    <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
    <span class="d-none d-md-inline">Download PDF</span>
    <span class="d-inline d-md-none">PDF</span>
</a>
*/

// =========================================
// 2. BUTTON DOWNLOAD PDF (sesudah)
// =========================================
/*
<button onclick="downloadReportWithFilters('/laporan/daftar-barang', {
    search: '<?= htmlspecialchars($search ?? '') ?>',
    kodepabrik: '<?= htmlspecialchars($kodepabrik ?? '') ?>',
    kodegolongan: '<?= htmlspecialchars($kodegolongan ?? '') ?>',
    kondisi_stok: '<?= htmlspecialchars($kondisiStok ?? 'semua') ?>'
}, 'Daftar_Barang')" class="btn btn-danger btn-sm" type="button">
    <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
    <span class="d-none d-md-inline">Download PDF</span>
    <span class="d-inline d-md-none">PDF</span>
</button>
*/

// =========================================
// 3. STRUKTUR LENGKAP BUTTON (dengan options)
// =========================================
/*
<?php
// Setup download parameters
$downloadParams = [
    'search' => $search ?? '',
    'kodepabrik' => $kodepabrik ?? '',
    'kodegolongan' => $kodegolongan ?? '',
    'kondisi_stok' => $kondisiStok ?? 'semua'
];
$downloadParamsJson = htmlspecialchars(json_encode($downloadParams));
?>

<div class="btn-group" role="group">
    <!-- Download PDF -->
    <button onclick="downloadReportWithFilters('/laporan/daftar-barang', <?php echo $downloadParamsJson; ?>, 'Daftar_Barang')" 
            class="btn btn-danger btn-sm" 
            type="button"
            title="Download sebagai PDF">
        <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
        <span class="d-none d-md-inline">Download PDF</span>
        <span class="d-inline d-md-none">PDF</span>
    </button>
    
    <!-- Export Excel -->
    <button onclick="downloadLaporanExcel('/laporan/daftar-barang', <?php echo $downloadParamsJson; ?>, 'Daftar_Barang')" 
            class="btn btn-success btn-sm" 
            type="button"
            title="Export sebagai Excel">
        <?= icon('file-excel', 'mb-1 me-2', 16) ?>
        <span class="d-none d-md-inline">Export Excel</span>
        <span class="d-inline d-md-none">Excel</span>
    </button>
    
    <!-- Share (Android only) -->
    <?php if (false): // Enable jika ingin show share button ?>
    <button onclick="if(isAndroidApp()) { shareReport('/laporan/daftar-barang', <?php echo $downloadParamsJson; ?>, 'Daftar_Barang'); } else { alert('Share hanya di Android'); }" 
            class="btn btn-info btn-sm" 
            type="button"
            title="Share PDF">
        <?= icon('share-2', 'mb-1 me-2', 16) ?>
        <span class="d-none d-md-inline">Share</span>
    </button>
    <?php endif; ?>
</div>
*/

// =========================================
// 4. ALTERNATIF DENGAN DROPDOWN MENU
// =========================================
/*
<div class="btn-group">
    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
        Download
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" 
               onclick="downloadReportWithFilters('/laporan/daftar-barang', {...params...}, 'Daftar_Barang', 'open')" 
               href="javascript:void(0)">
                <i class="bi bi-download me-2"></i>Open
            </a>
        </li>
        <li>
            <a class="dropdown-item" 
               onclick="shareReport('/laporan/daftar-barang', {...params...}, 'Daftar_Barang')" 
               href="javascript:void(0)">
                <i class="bi bi-share me-2"></i>Share (Android)
            </a>
        </li>
        <li>
            <a class="dropdown-item" 
               onclick="saveReport('/laporan/daftar-barang', {...params...}, 'Daftar_Barang')" 
               href="javascript:void(0)">
                <i class="bi bi-folder-plus me-2"></i>Save
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" 
               onclick="downloadLaporanExcel('/laporan/daftar-barang', {...params...}, 'Daftar_Barang')" 
               href="javascript:void(0)">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Excel
            </a>
        </li>
    </ul>
</div>
*/

// =========================================
// 5. JAVASCRIPT DI HEADER (perlu ditambahkan)
// =========================================
/*
<!-- Android Download Support -->
<script src="/assets/js/dhainako-download-sdk.js"></script>
<script src="/assets/js/dps-download-helper.js"></script>
<script src="/assets/js/dps-laporan-download.js"></script>
*/

// =========================================
// 6. CHECKLIST UNTUK SETIAP LAPORAN
// =========================================
/*
LAPORAN: Daftar Barang
- [ ] Update button onclick ke downloadReportWithFilters
- [ ] Add parameters: search, kodepabrik, kodegolongan, kondisi_stok
- [ ] Test di browser
- [ ] Test di Android app

LAPORAN: Daftar Stok
- [ ] Update button onclick ke downloadReportWithFilters
- [ ] Add parameters: search, kodepabrik, kodegolongan, kondisi_stok
- [ ] Test di browser
- [ ] Test di Android app

LAPORAN: Daftar Harga
- [ ] Update button onclick ke downloadReportWithFilters
- [ ] Add parameters: search, kodepabrik, kodegolongan
- [ ] Test di browser
- [ ] Test di Android app

LAPORAN: Daftar Tagihan
- [ ] Update button onclick ke downloadReportWithFilters
- [ ] Add parameters: search, kodecustomer, status_jatuh_tempo, sort_by
- [ ] Test di browser
- [ ] Test di Android app

LAPORAN: Omset Penjualan
- [ ] Update button onclick ke downloadReportWithFilters
- [ ] Add parameters: tahun, bulan
- [ ] Test di browser
- [ ] Test di Android app
*/

// =========================================
// 7. TIPS & TROUBLESHOOTING
// =========================================
/*

TIPS:
1. Gunakan htmlspecialchars() untuk PHP variables di onclick
2. Pastikan parameters match dengan yang diterima controller
3. Gunakan nama report yang deskriptif (misal: Daftar_Barang, Omset_Penjualan)
4. Test di browser dulu sebelum di Android

DEBUGGING:
1. Buka DevTools (F12) → Console
2. Ketik: DPSDownload.isAndroidApp() → return true/false
3. Ketik: typeof DhainakoDownload → check if available
4. Check console log untuk download progress

ERROR HANDLING:
- Browser: gunakan window.location.href sebagai fallback
- Android: gunakan alert() untuk error messages
- Async: use options.onSuccess dan options.onError callbacks

PERFORMANCE:
- File size: HTML download cepat, tidak perlu PDF library
- Network: same sebagai link download sebelumnya
- Mobile: optimized untuk Android WebView

*/
?>
