# 🐛 DEBUGGING CHECKLIST - Android PDF Download

## Jika masih ada masalah, gunakan checklist ini:

### 1. Check Server Response Headers

**Chrome DevTools:**

```
F12 → Network → Click Download request → Headers tab

Response Headers (harus ada):
✓ Content-Type: application/pdf
✓ Content-Disposition: attachment; filename="*.pdf"
✓ Cache-Control: no-store, no-cache
```

**Terminal Check:**

```bash
curl -I "http://localhost/laporan/daftar-stok?export=pdf" | grep -i content
```

Expected output:

```
Content-Type: application/pdf
Content-Disposition: attachment; filename="Daftar_Stok_*.pdf"
Cache-Control: no-store, no-cache, must-revalidate, max-age=0
```

---

### 2. Check File Size

**Problem**: File terlalu kecil atau kosong

**Check:**

- File HTML minimal ~5KB
- File PDF (setelah generated) ~20-50KB typical

```bash
# Check file size setelah download
ls -lh ~/Downloads/Daftar_Stok_*.pdf

# Harus lebih dari 1KB (tidak empty)
wc -c ~/Downloads/Daftar_Stok_*.pdf
```

---

### 3. Browser Console Errors

**F12 → Console tab**

**Expected logs (SUCCESS):**

```javascript
Download PDF: { url: "/laporan/daftar-stok?...", filename: "Daftar_Stok_...", action: "open" }
Successfully downloaded: Daftar_Stok_...
DPS Laporan Download Helper loaded
```

**If you see ERROR:**

```javascript
// Error 1: DPSDownload not available
"DPSDownload not available, using browser fallback"
→ Fix: Check dhainako-download-sdk.js included in header.php

// Error 2: Download error
"Download error: ..."
→ Fix: Check Android app SDK version and permissions

// Error 3: Exception
"DPSDownload exception: ..."
→ Fix: Check browser console for full error stack
```

---

### 4. Test in Browser (Desktop/Mobile)

**Desktop:**

```
1. Open http://localhost/laporan/daftar-stok
2. Click "Download PDF"
3. File should download to Downloads folder
4. Open file with PDF reader
5. Check: filename = Daftar_Stok_YYYYMMDD_HHMI.pdf (NOT .pdf.html)
```

**Mobile Browser:**

```
1. Open in Chrome/Firefox on Android
2. Click "Download PDF"
3. Notification should say "Downloaded: Daftar_Stok_..."
4. Open file
5. Should open in PDF viewer (Google Drive, Adobe Reader, etc)
```

---

### 5. Test in Android App

**WebView Debug:**

```
1. Enable USB Debugging on Android device
2. Connect to computer
3. Chrome: chrome://inspect
4. Open remote debugger for your app
5. Console tab: check for JS errors
6. Network tab: check response headers
```

**File System Check:**

```
1. Connect Android via ADB
2. adb shell
3. ls -la /sdcard/Download/
4. Check filename is correct (no .html suffix)
```

---

### 6. Common Issues & Solutions

| Issue                | Symptom                    | Solution                                           |
| -------------------- | -------------------------- | -------------------------------------------------- |
| **Double Extension** | Filename: `...pdf.html`    | Already fixed in v1.1+                             |
| **Wrong MIME Type**  | Content-Type: text/html    | Already fixed in v1.1+                             |
| **File Empty**       | File 0 bytes               | Check database query, no data returned             |
| **Cache Issue**      | Old version downloaded     | Clear browser cache (Ctrl+Shift+Delete)            |
| **Permission Issue** | Cannot save file (Android) | Check app permissions: WRITE_EXTERNAL_STORAGE      |
| **No Fallback**      | Desktop browser blank      | Check DPSDownload load sequence in header.php      |
| **Timeout**          | Download never completes   | Check for large reports (>100MB), pagination issue |
| **Encoding Issue**   | File corrupted/garbled     | Check UTF-8 encoding in header.php BOM             |

---

### 7. Log Analysis

**PHP Error Log:**

```bash
# Check if there are errors during PDF generation
tail -f /var/log/php-errors.log

# Expected: No errors
# If error: Check database connectivity, missing fields
```

**Browser Console (F12):**

```javascript
// Filter by "Download" to see all download-related logs
console.log("messages containing 'Download'");
```

---

### 8. Test Different Scenarios

```javascript
// Test 1: Basic download (no filters)
downloadReportWithFilters("/laporan/daftar-stok", {}, "Daftar_Stok");

// Test 2: With filters
downloadReportWithFilters(
  "/laporan/daftar-stok",
  {
    search: "test",
    kodepabrik: "P001",
    kodegolongan: "G001",
    kondisi_stok: "ada",
  },
  "Daftar_Stok",
);

// Test 3: Excel export
downloadLaporanExcel("/laporan/daftar-stok", {}, "Daftar_Stok");

// Test 4: Share (Android only)
shareReport("/laporan/daftar-stok", {}, "Daftar_Stok");

// Test 5: Save without open (Android only)
saveReport("/laporan/daftar-stok", {}, "Daftar_Stok");
```

---

### 9. Version Check

**Check if fixes applied:**

**Method 1: Check source code**

```bash
grep -n "Content-Type: application/pdf" controllers/LaporanController.php
# Should find match at line 391 or nearby
```

**Method 2: Check file size**

```bash
wc -l controllers/LaporanController.php
# Should be ~1620+ lines (added error handling)
```

**Method 3: Check git log**

```bash
git log --oneline | grep -i "android\|pdf\|download"
# Should see: "fix: Android PDF download - correct MIME type..."
```

---

### 10. Performance Check

**Large Report Download:**

```
- Daftar Stok 10,000+ items
- Expected time: <5 seconds
- File size: 50-100KB
- Memory: <10MB peak

If slower: Check pagination in query
If larger: Check if including unnecessary fields
```

---

### 11. Network Check (Android)

**Check if network issue:**

```bash
# Test with curl
curl -I -H "User-Agent: Mozilla/5.0" \
  "http://your-server/laporan/daftar-stok?export=pdf" \

# Should return 200 OK with application/pdf
```

**Test with slow network:**

- DevTools → Network → Throttle (Fast 3G)
- Try download again
- Should still work (no timeout)

---

### 12. Security Check

**CORS Headers (jika cross-domain):**

```bash
curl -H "Origin: http://localhost:3000" -v \
  http://your-server/laporan/daftar-stok?export=pdf

Response should include:
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST
```

**Authentication:**

```javascript
// Should not require auth for laporan (public or logged-in user)
// Check if redirected to login page instead of download
```

---

### 13. Still Having Issues?

**Step 1: Enable Debug Mode**

```php
// Add to top of LaporanController.php temporarily
define('DEBUG_DOWNLOAD', true);

// In downloadAsHTML():
if (DEBUG_DOWNLOAD) {
    error_log("Download: " . $filename . ", Type: " . (strpos($filename, '.pdf') !== false ? 'PDF' : 'HTML'));
}
```

**Step 2: Check Logs**

```bash
tail -f /var/log/apache2/error.log
tail -f storage/logs/app.log
```

**Step 3: Collect Debug Info**

- HTTP headers from DevTools
- Console errors (full stack trace)
- File size and signature
- Android SDK version
- Database query results

**Step 4: Report Issue**
Include:

- Error message
- Expected vs actual behavior
- HTTP headers
- Browser/app details
- Step-by-step reproduction

---

## Quick Reference Commands

```bash
# Check MIME type
file ~/Downloads/Daftar_Stok_*.pdf

# Verify PDF structure
head -c 10 ~/Downloads/Daftar_Stok_*.pdf
# Should start with: %PDF-1.4

# Check file encoding
chardet ~/Downloads/Daftar_Stok_*.pdf
# Should be: UTF-8

# Test download with wget
wget -v http://localhost/laporan/daftar-stok?export=pdf -O test.pdf

# Check Android download folder
adb shell "ls -la /sdcard/Download/ | grep -i daftar"

# Pull file from Android for inspection
adb pull /sdcard/Download/Daftar_Stok_*.pdf ~/test_download.pdf
```

---

**Last Updated**: 2026-01-17
**Status**: ✅ Fixes Applied - Ready for Testing
