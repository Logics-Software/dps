# 📊 Arsitektur Download System - Android App Support

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        DPS DOWNLOAD ARCHITECTURE                        │
└─────────────────────────────────────────────────────────────────────────┘

                              USER INTERFACE
                         (Views/Laporan Pages)
                                  │
                    ┌─────────────┴─────────────┐
                    │                           │
            ┌───────▼──────┐          ┌────────▼─────────┐
            │ Download PDF │          │ Download Excel   │
            │   (Button)   │          │    (Button)      │
            └───────┬──────┘          └────────┬─────────┘
                    │                         │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼─────────────┐
                    │  JavaScript Functions   │
                    │  (dps-laporan-download. │
                    │         js)             │
                    └────────────┬─────────────┘
                                 │
         ┌───────────────────────┼───────────────────────┐
         │                       │                       │
    ┌────▼────┐         ┌───────▼────┐         ┌────────▼─────┐
    │ Browser │         │ Android    │         │  iOS/Other   │
    │ Test?   │         │ App Test?  │         │   (Future)   │
    └────┬────┘         └───────┬────┘         └────────┬─────┘
         │                     │                       │
    ┌────▼────────────┐   ┌────▼──────────────┐   ┌────▼────────┐
    │ DPSDownload     │   │ DhainakoDownload  │   │  Standard   │
    │ (Fallback)      │   │ (Android SDK)     │   │ Download    │
    │                 │   │                   │   │             │
    │ window.location │   │ .downloadAndOpen()│   │ (via SDK)   │
    │ .href = url     │   │ .downloadAndShare │   │             │
    │                 │   │ .downloadAndSave  │   │             │
    └────┬────────────┘   └────┬──────────────┘   └────┬────────┘
         │                     │                       │
         │                  ┌──▼──┐                    │
         │                  │HTTP │                    │
         │                  │GET  │                    │
         │                  └──┬──┘                    │
         │                     │                       │
         └─────────────────────┼───────────────────────┘
                               │
                 ┌─────────────▼─────────────┐
                 │  SERVER (PHP)            │
                 │  Controllers:            │
                 │  - LaporanController     │
                 │  - OmsetController       │
                 └──────────┬────────────┐──┘
                            │            │
                 ┌──────────▼─┐  ┌────────▼──────┐
                 │ SQL Query  │  │ HTML Generation
                 │ (Data)     │  │ (PDF Content) │
                 └──────────┬─┘  └────────┬──────┘
                            │             │
                 ┌──────────▼─────────────▼──────┐
                 │ Response Headers:             │
                 │ Content-Type: text/html       │
                 │ Content-Disposition:          │
                 │ attachment; filename="..."    │
                 │ Pragma: no-cache              │
                 │ Expires: 0                    │
                 └──────────┬────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              │                           │
         ┌────▼─────┐            ┌───────▼──────┐
         │ Browser  │            │ Android App  │
         │ Download │            │ Download +   │
         │ Folder   │            │ Open/Share   │
         └──────────┘            └──────────────┘
```

## 📁 File Structure

```
dps/
├── assets/
│   └── js/
│       ├── dhainako-download-sdk.js      ← Android SDK (PROVIDED)
│       ├── dps-download-helper.js        ← Wrapper (PROVIDED)
│       └── dps-laporan-download.js       ← Laporan helpers (CREATED)
│
├── controllers/
│   ├── LaporanController.php             ← PDF generation (OK)
│   └── OmsetController.php               ← Omset PDF (OK)
│
├── views/
│   ├── layouts/
│   │   └── header.php                   ← Include JS files (NEED UPDATE)
│   └── laporan/
│       ├── daftar-barang.php            ← Update buttons (TODO)
│       ├── daftar-stok.php              ← Update buttons (TODO)
│       ├── daftar-harga.php             ← Update buttons (TODO)
│       ├── daftar-tagihan.php           ← Update buttons (TODO)
│       └── omset.php                    ← Update buttons (TODO)
│
├── IMPLEMENTASI_ANDROID_DOWNLOAD.md     ← Full docs (CREATED)
├── QUICK_REFERENCE_ANDROID_DOWNLOAD.md ← Quick ref (CREATED)
└── CONTOH_IMPLEMENTASI_ANDROID_DOWNLOAD.php ← Examples (CREATED)
```

## 🔄 Request/Response Flow

### Browser Request

```
GET /laporan/daftar-barang?export=pdf&search=test HTTP/1.1
        │
        └──→ [LaporanController] daftarBarang()
                │
                ├─→ Query data
                │
                ├─→ Generate HTML
                │
                ├─→ Set Headers:
                │   Content-Type: text/html
                │   Content-Disposition: attachment
                │
                └─→ Echo HTML
                    │
                    └──→ Browser Standard Download Dialog
                         │
                         └──→ File saved to Downloads/
```

### Android App Request

```
GET /laporan/daftar-barang?export=pdf&search=test HTTP/1.1
        │
        └──→ DhainakoDownload.downloadAndOpen(url, filename)
                │
                ├─→ [LaporanController] daftarBarang()
                │    │
                │    ├─→ Query data
                │    │
                │    ├─→ Generate HTML
                │    │
                │    ├─→ Set Headers (same as browser)
                │    │
                │    └─→ Echo HTML
                │
                ├─→ Download Manager (Android)
                │    │
                │    └─→ Save to app cache
                │
                └─→ Open in Viewer/App
                    │
                    └──→ Display to user OR Share Dialog
```

## 🎯 Decision Tree

```
User clicks "Download PDF"
    │
    ├─→ JS: downloadReportWithFilters()
    │    │
    │    ├─→ DPSDownload available?
    │    │    │
    │    │    ├─ YES → DPSDownload.download()
    │    │    │    │
    │    │    │    ├─ Android app detected?
    │    │    │    │  │
    │    │    │    │  ├─ YES → DhainakoDownload.downloadAndOpen()
    │    │    │    │  │         │
    │    │    │    │  │         └─→ Android SDK handles download
    │    │    │    │  │             │
    │    │    │    │  │             ├─ Open in viewer
    │    │    │    │  │             ├─ Share to apps
    │    │    │    │  │             └─ Save to Downloads
    │    │    │    │  │
    │    │    │    │  └─ NO → Fallback: window.location.href
    │    │    │    │           │
    │    │    │    │           └─→ Browser standard download
    │    │    │    │
    │    │    │    └─→ Callbacks:
    │    │    │        ├─ onProgress()
    │    │    │        ├─ onSuccess()
    │    │    │        └─ onError()
    │    │    │
    │    │    └─ NO → Direct: window.location.href
    │    │             │
    │    │             └─→ Browser download (no Android support)
    │
    └─→ Server: Send HTML file with headers
         │
         └─→ File downloaded/opened
```

## 🔐 Security Layers

```
Request comes in
    │
    ├─ [Layer 1] Role-based Auth
    │   └─ Auth::requireRole(['admin', 'manajemen', 'operator', 'sales'])
    │       ├─ Valid? → Continue
    │       └─ Invalid? → Error 403
    │
    ├─ [Layer 2] Parameter Validation
    │   └─ Validate: search, filters, sort params
    │       ├─ Valid? → Use in query
    │       └─ Invalid? → Use default or error
    │
    ├─ [Layer 3] SQL Injection Prevention
    │   └─ Use prepared statements
    │       ├─ WHERE mb.namabarang LIKE ?
    │       └─ Params: ["%$search%"]
    │
    ├─ [Layer 4] Output Encoding
    │   └─ htmlspecialchars() for all user data
    │       └─ In HTML tables: &lt;, &gt;, &amp;, etc.
    │
    └─ [Layer 5] HTTP Headers
        └─ Prevent caching/replay
            ├─ Pragma: no-cache
            ├─ Expires: 0
            └─ Content-Disposition: attachment
```

## 📊 File Size Comparison

| Format               | Size       | Generation | Download Speed |
| -------------------- | ---------- | ---------- | -------------- |
| HTML                 | ~50-100KB  | Fast       | Fast           |
| CSV                  | ~30-50KB   | Very Fast  | Very Fast      |
| PDF (mPDF)           | ~100-200KB | Slow       | Slow           |
| PDF (Current System) | N/A        | N/A        | N/A            |

**Current implementation**: HTML download → Browser convert to PDF (user control)

## 🚀 Performance Notes

- ✅ No external PDF library = fast generation
- ✅ Simple HTML = small file size
- ✅ Browser-native print = user friendly
- ✅ Android SDK optimized = good app integration
- ✅ Caching disabled = always fresh data

## 📱 Browser/App Support

| Platform              | Status    | Method               |
| --------------------- | --------- | -------------------- |
| Chrome Browser        | ✅ Full   | Standard download    |
| Firefox Browser       | ✅ Full   | Standard download    |
| Safari Browser        | ✅ Full   | Standard download    |
| Android Chrome        | ✅ Full   | Standard download    |
| Android App (WebView) | ✅ Full   | DhainakoDownload SDK |
| iOS App               | ⏳ Future | DhainakoDownload SDK |

---

**Next Steps**:

1. Include JS files di header.php
2. Update laporan buttons (copy-paste from examples)
3. Test di browser
4. Test di Android app
5. Verify file open/share works
