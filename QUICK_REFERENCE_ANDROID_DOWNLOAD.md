# 🚀 Quick Reference - Android Download Implementation

## 📋 File yang Sudah Ada

| File                       | Lokasi          | Fungsi                                 |
| -------------------------- | --------------- | -------------------------------------- |
| `dhainako-download-sdk.js` | `/assets/js/`   | Android SDK untuk download             |
| `dps-download-helper.js`   | `/assets/js/`   | Wrapper auto-detect Android/Browser    |
| `dps-laporan-download.js`  | `/assets/js/`   | Helper functions untuk laporan         |
| `LaporanController.php`    | `/controllers/` | Server-side PDF generation (sudah OK)  |
| `OmsetController.php`      | `/controllers/` | Server-side PDF untuk Omset (sudah OK) |

## 🔧 3 Langkah Implementasi (Simple)

### Step 1: Include Files di Header

File: `views/layouts/header.php`

```html
<!-- Add before closing </body> -->
<script src="/assets/js/dhainako-download-sdk.js"></script>
<script src="/assets/js/dps-download-helper.js"></script>
<script src="/assets/js/dps-laporan-download.js"></script>
```

### Step 2: Update Download Button (Simple Version)

```html
<!-- BEFORE -->
<a href="/laporan/daftar-barang?export=pdf" class="btn btn-danger btn-sm"
  >Download PDF</a
>

<!-- AFTER -->
<button
  onclick="downloadReportWithFilters('/laporan/daftar-barang', {}, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
>
  Download PDF
</button>
```

### Step 3: Update Download Button (With Filters)

```html
<!-- AFTER (with filters) -->
<button
  onclick="downloadReportWithFilters('/laporan/daftar-barang', {
    search: '<?= htmlspecialchars($search ?? '') ?>',
    kodepabrik: '<?= htmlspecialchars($kodepabrik ?? '') ?>'
}, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
>
  Download PDF
</button>
```

## 📱 Fungsi JavaScript yang Tersedia

### Fungsi Utama

```javascript
// Download PDF (auto Android/Browser)
downloadReportWithFilters(
  "/laporan/daftar-barang",
  { search: "test" },
  "Daftar_Barang",
);

// Download Excel
downloadLaporanExcel(
  "/laporan/daftar-barang",
  { search: "test" },
  "Daftar_Barang",
);

// Share (Android only)
shareReport("/laporan/daftar-barang", { search: "test" }, "Daftar_Barang");

// Save tanpa open
saveReport("/laporan/daftar-barang", { search: "test" }, "Daftar_Barang");

// Check apakah di Android app
isAndroidApp(); // return true/false
```

## 📚 Laporan yang Perlu di-Update

- [x] Server-side sudah OK (LaporanController, OmsetController)
- [ ] `views/laporan/daftar-barang.php`
- [ ] `views/laporan/daftar-stok.php`
- [ ] `views/laporan/daftar-harga.php`
- [ ] `views/laporan/daftar-tagihan.php`
- [ ] `views/laporan/omset.php`

## 🎯 Parameter per Laporan

| Laporan         | Endpoint                  | Parameters                                        |
| --------------- | ------------------------- | ------------------------------------------------- |
| Daftar Barang   | `/laporan/daftar-barang`  | search, kodepabrik, kodegolongan, kondisi_stok    |
| Daftar Stok     | `/laporan/daftar-stok`    | search, kodepabrik, kodegolongan, kondisi_stok    |
| Daftar Harga    | `/laporan/daftar-harga`   | search, kodepabrik, kodegolongan                  |
| Daftar Tagihan  | `/laporan/daftar-tagihan` | search, kodecustomer, status_jatuh_tempo, sort_by |
| Omset Penjualan | `/laporan/omset`          | tahun, bulan                                      |

## ✅ Testing Checklist

### Browser Test

```javascript
// Di console (F12)
downloadReportWithFilters("/laporan/daftar-barang", {}, "Test");
// Expected: File download di browser
```

### Android App Test

```javascript
// Di console Android WebView
isAndroidApp(); // Should return true
downloadReportWithFilters("/laporan/daftar-barang", {}, "Test");
// Expected: File open di Android app atau viewer
```

## 🔒 Security Notes

✅ Sudah implemented:

- Role-based auth di controller (`Auth::requireRole()`)
- Parameter validation di controller
- Filename sanitization (`htmlspecialchars()`)
- No caching headers

## 📝 Code Template (Copy-Paste)

### For Laporan View

```html
<?php
// Setup download parameters
$downloadParams = [
    'search' =>
$search ?? '', 'kodepabrik' => $kodepabrik ?? '', 'kodegolongan' =>
$kodegolongan ?? '' ]; $downloadParamsJson =
htmlspecialchars(json_encode($downloadParams)); ?>

<!-- Download Button -->
<button
  onclick="downloadReportWithFilters('/laporan/daftar-barang', <?php echo $downloadParamsJson; ?>, 'Daftar_Barang')"
  class="btn btn-danger btn-sm"
>
  <?= icon('file-pdf', 'mb-1 me-2', 16) ?>
  <span class="d-none d-md-inline">Download PDF</span>
</button>
```

## 🎬 How It Works

```
User Click Download Button
        ↓
JavaScript: downloadReportWithFilters()
        ↓
Check: isAndroidApp()?
    ├─ YES → DhainakoDownload.downloadAndOpen()
    │           ↓
    │       Android SDK handles download
    │           ↓
    │       File opens in app/viewer
    │
    └─ NO → window.location.href = url
                ↓
            Browser standard download
                ↓
            File saved to Downloads
```

## 🐛 Quick Debug

| Problem             | Debug                                | Solution                    |
| ------------------- | ------------------------------------ | --------------------------- |
| File not download   | Check console for errors             | Include JS files correctly  |
| Wrong filename      | Check parameter format               | Use `htmlspecialchars()`    |
| Android not working | `typeof DhainakoDownload` in console | Update Android app with SDK |
| Parameters lost     | Check URL in console                 | Use proper URL encoding     |

## 📞 Support Files

- Full docs: [IMPLEMENTASI_ANDROID_DOWNLOAD.md](./IMPLEMENTASI_ANDROID_DOWNLOAD.md)
- Examples: [CONTOH_IMPLEMENTASI_ANDROID_DOWNLOAD.php](./CONTOH_IMPLEMENTASI_ANDROID_DOWNLOAD.php)
- Helper source: [assets/js/dps-laporan-download.js](./assets/js/dps-laporan-download.js)

---

**Ready to implement?** Start with Step 1 di atas, test di browser dulu, baru test di Android app! 🚀
