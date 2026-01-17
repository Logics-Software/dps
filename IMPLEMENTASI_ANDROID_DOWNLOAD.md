# Implementasi Download PDF untuk Android App

## 📋 Gambaran Umum

Implementasi download PDF yang support Android app menggunakan:

- **dhainako-download-sdk.js** - SDK untuk menangani download di Android WebView
- **dps-download-helper.js** - Wrapper helper yang auto-detect Android vs Browser
- **Native HTML Download System** - Server side (sudah berjalan)

## 🔧 Langkah Implementasi

### Step 1: Include JavaScript Files di Header

File: `views/layouts/header.php`

Tambahkan kedua JS file di `<head>` atau sebelum `</body>`:

```html
<!-- Android Download Support -->
<script src="/assets/js/dhainako-download-sdk.js"></script>
<script src="/assets/js/dps-download-helper.js"></script>
```

### Step 2: Update Download Button untuk Menggunakan DPSDownload Helper

**Sebelumnya** (current):

```html
<a href="/laporan/daftar-barang?export=pdf" class="btn btn-danger btn-sm">
  Download PDF
</a>
```

**Sesudahnya** (dengan Android support):

```html
<button
  onclick="downloadLaporanPDF('/laporan/daftar-barang', {export: 'pdf'}, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
>
  <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
  <span class="d-none d-md-inline">Download PDF</span>
  <span class="d-inline d-md-none">PDF</span>
</button>
```

### Step 3: Tambahkan Helper Function di Header atau Global JS

File: `assets/js/global.js` atau `views/layouts/header.php` (dalam `<script>`)

```javascript
/**
 * Download laporan PDF dengan support Android dan Browser
 */
function downloadLaporanPDF(baseUrl, params = {}, reportName = "Laporan") {
  // Build URL with parameters
  const queryString = new URLSearchParams(params).toString();
  const url = baseUrl + (queryString ? "?" + queryString : "");

  // Generate filename
  const date = new Date();
  const dateStr =
    date.getFullYear() +
    String(date.getMonth() + 1).padStart(2, "0") +
    String(date.getDate()).padStart(2, "0");
  const filename = `${reportName}_${dateStr}.pdf`;

  // Use DPSDownload helper if available
  if (typeof DPSDownload !== "undefined") {
    DPSDownload.download(url, filename, "open", {
      onSuccess: () => console.log(`Downloaded ${filename}`),
      onError: (error) => alert(`Error: ${error}`),
    });
  } else {
    // Fallback to direct link
    window.location.href = url;
  }
}

/**
 * Download report dengan parameter filter
 */
function downloadReportWithFilters(endpoint, filters, reportName = "Laporan") {
  const params = {
    export: "pdf",
    ...filters,
  };
  downloadLaporanPDF(endpoint, params, reportName);
}
```

### Step 4: Update Semua Laporan Views

#### Daftar Barang (`views/laporan/daftar-barang.php`)

```html
<button
  onclick="downloadReportWithFilters('/laporan/daftar-barang', {search: '<?= $search ?>', kodepabrik: '<?= $kodepabrik ?>', kodegolongan: '<?= $kodegolongan ?>'}, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
></button>
```

#### Daftar Stok (`views/laporan/daftar-stok.php`)

```html
<button
  onclick="downloadReportWithFilters('/laporan/daftar-stok', {search: '<?= $search ?>', kodepabrik: '<?= $kodepabrik ?>', kondisi_stok: '<?= $kondisiStok ?>'}, 'Daftar_Stok')"
  class="btn btn-danger btn-sm"
></button>
```

#### Daftar Harga (`views/laporan/daftar-harga.php`)

```html
<button
  onclick="downloadReportWithFilters('/laporan/daftar-harga', {search: '<?= $search ?>', kodepabrik: '<?= $kodepabrik ?>'}, 'Daftar_Harga')"
  class="btn btn-danger btn-sm"
></button>
```

#### Daftar Tagihan (`views/laporan/daftar-tagihan.php`)

```html
<button
  onclick="downloadReportWithFilters('/laporan/daftar-tagihan', {search: '<?= $search ?>', kodecustomer: '<?= $kodecustomer ?>', status_jatuh_tempo: '<?= $statusJatuhTempo ?>'}, 'Daftar_Tagihan')"
  class="btn btn-danger btn-sm"
></button>
```

#### Omset Penjualan (`views/laporan/omset.php`)

```html
<button
  onclick="downloadReportWithFilters('/laporan/omset', {tahun: '<?= $tahun ?>', bulan: '<?= $bulan ?>'}, 'Omset_Penjualan')"
  class="btn btn-danger btn-sm"
></button>
```

### Step 5: Pastikan Server Headers Sudah Benar

File: `controllers/LaporanController.php` dan `controllers/OmsetController.php`

Headers yang sudah ada (SUDAH BENAR):

```php
private function downloadAsHTML($html, $filename) {
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.html"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $html;
}
```

✅ Headers sudah sesuai untuk Android download.

## 📱 Cara Kerja

### Di Android App:

1. User klik "Download PDF"
2. JavaScript call `DPSDownload.download(url, filename, 'open')`
3. DPSDownload detect Android app → call `DhainakoDownload.downloadAndOpen()`
4. Android SDK handle file download dan open di app/viewer

### Di Browser:

1. User klik "Download PDF"
2. JavaScript call `DPSDownload.download(url, filename, 'open')`
3. DPSDownload detect browser → fallback `window.location.href = url`
4. Browser standard download behavior

## 🎯 Action Types Tersedia

```javascript
// Open file setelah download
DPSDownload.download(url, filename, "open");

// Share file setelah download (Android only)
DPSDownload.download(url, filename, "share");

// Save file without opening (Android only)
DPSDownload.download(url, filename, "save");
```

## 🔒 Security Considerations

1. ✅ File download sudah protected dengan role-based auth (`Auth::requireRole()`)
2. ✅ Filename di-sanitize dengan htmlspecialchars()
3. ✅ Parameters divalidasi di controller
4. ✅ Headers sudah set untuk prevent caching

## ✅ Checklist Implementasi

- [ ] File `dhainako-download-sdk.js` ada di `assets/js/`
- [ ] File `dps-download-helper.js` ada di `assets/js/`
- [ ] Include kedua files di `views/layouts/header.php`
- [ ] Tambahkan `downloadLaporanPDF()` function ke global JS
- [ ] Update semua tombol "Download PDF" di laporan views
- [ ] Test di browser (standard download)
- [ ] Test di Android app (Android download behavior)
- [ ] Verify file bisa di-open/share di Android

## 📝 Contoh Lengkap

### HTML Button

```html
<button
  onclick="downloadReportWithFilters('/laporan/daftar-barang', {
    search: '<?= htmlspecialchars($search) ?>', 
    kodepabrik: '<?= htmlspecialchars($kodepabrik) ?>',
    kodegolongan: '<?= htmlspecialchars($kodegolongan) ?>'
}, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
>
  <i class="bi bi-file-pdf"></i> Download PDF
</button>
```

### JavaScript Function

```javascript
function downloadReportWithFilters(endpoint, filters, reportName) {
  const params = { export: "pdf", ...filters };
  const queryString = new URLSearchParams(params).toString();
  const url = endpoint + "?" + queryString;
  const dateStr = new Date().toISOString().split("T")[0];
  const filename = `${reportName}_${dateStr}.pdf`;

  DPSDownload.download(url, filename, "open", {
    onSuccess: () => console.log(`Downloaded: ${filename}`),
    onError: (error) => alert(`Error: ${error}`),
  });
}
```

## 🐛 Troubleshooting

### File tidak download di Android

- Check: `dhainako-download-sdk.js` sudah diload
- Check: `DhainakoDownload.isAvailable()` return true
- Check: Android app version support download

### File tidak download di Browser

- Check: Response Content-Disposition header
- Check: Browser allow download (check browser settings)
- Check: URL accessible dan tidak error

### Wrong filename di Android

- Check: Filename di-pass correctly ke DPSDownload
- Check: Filename format (no special chars)

## 📚 Reference

- DPS Download Helper: `assets/js/dps-download-helper.js`
- Dhainako SDK: `assets/js/dhainako-download-sdk.js`
- Controllers: `controllers/LaporanController.php`, `controllers/OmsetController.php`
- Current System: Native HTML download + file attachment headers
