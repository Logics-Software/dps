# 📥 Custom Download Error Handling

## 🎯 **Overview**

Sistem custom alert untuk menangani error download file/gambar dengan pesan yang user-friendly dan informative.

## 🔧 **Components**

### **1. DownloadController.php**
Controller khusus untuk menangani download dengan error handling:

```php
// Routes
GET /download/file?path={file_path}&name={filename}
GET /download/check?path={file_path}
```

#### **Features:**
- ✅ **File Existence Check** - Verify file exists before download
- ✅ **Permission Check** - Ensure file is readable
- ✅ **Size Validation** - Prevent memory issues with large files
- ✅ **Security** - Prevent directory traversal attacks
- ✅ **Proper Headers** - Set correct MIME types and download headers
- ✅ **Streaming** - Stream large files in chunks

### **2. download-handler.js**
JavaScript handler untuk intercept download links dan show custom alerts:

#### **Features:**
- ✅ **Auto Detection** - Automatically detects download links
- ✅ **Pre-flight Check** - Checks file status before download
- ✅ **Custom Alerts** - Beautiful error/success messages
- ✅ **Loading States** - Shows loading spinner during download
- ✅ **Error Mapping** - Maps HTTP status codes to user-friendly messages

### **3. download-alerts.css**
Custom styling untuk alert modals dan notifications:

#### **Features:**
- ✅ **Modal Alerts** - Full-screen modal untuk important errors
- ✅ **Toast Notifications** - Quick feedback untuk success/minor errors
- ✅ **Progress Indicators** - Progress bars untuk large downloads
- ✅ **Responsive Design** - Works on mobile dan desktop
- ✅ **Dark Mode Support** - Automatic dark mode detection

## 🚀 **How It Works**

### **1. Download Flow:**
```
User clicks download link
    ↓
JavaScript intercepts click
    ↓
Pre-flight check (HEAD request)
    ↓
If file exists: Start download
If file missing: Show error alert
    ↓
Monitor download progress
    ↓
Show success/error message
```

### **2. Error Types Handled:**

#### **404 - File Not Found:**
```
"File 'document.pdf' tidak ditemukan. 
File mungkin telah dihapus atau dipindahkan."
```

#### **403 - Access Denied:**
```
"Akses ditolak untuk file 'document.pdf'. 
Anda tidak memiliki izin untuk mengunduh file ini."
```

#### **500 - Server Error:**
```
"Terjadi kesalahan server saat mengunduh file 'document.pdf'. 
Silakan coba lagi nanti."
```

#### **413 - File Too Large:**
```
"File 'document.pdf' terlalu besar untuk diunduh."
```

#### **Network Error:**
```
"Tidak dapat terhubung ke server. 
Periksa koneksi internet Anda."
```

## 📱 **User Experience**

### **Success Download:**
- ✅ Loading spinner pada button
- ✅ File downloads normally
- ✅ Success toast notification
- ✅ Button returns to normal state

### **Error Download:**
- ❌ Loading spinner pada button
- ❌ Error detected
- ❌ Custom error modal with details
- ❌ Button returns to normal state
- ❌ User gets clear explanation

## 🔧 **Implementation**

### **1. Update Download Links:**

**Before:**
```html
<a href="/uploads/file.pdf" download="file.pdf">
    Download
</a>
```

**After:**
```html
<a href="/download/file?path=file.pdf&name=file.pdf" 
   class="download-link" 
   download="file.pdf"
   data-filename="file.pdf">
    Download
</a>
```

### **2. Auto-Loading:**
Scripts dan CSS automatically loaded pada pages dengan downloads:
- `/messages/` - Message attachments
- `/orders/` - Order files  
- `/visits/` - Visit files

### **3. Manual Integration:**
Untuk pages lain, tambahkan ke view:

```php
// In view file
$additionalScripts = [
    $baseUrl . '/assets/js/download-handler.js'
];

// In header
<link href="<?= $baseUrl ?>/assets/css/download-alerts.css" rel="stylesheet">
```

## 🎨 **Customization**

### **1. Error Messages:**
Edit `getErrorMessage()` in `download-handler.js`:

```javascript
getErrorMessage(status, filename) {
    switch (status) {
        case 404:
            return `Custom 404 message for ${filename}`;
        // ... other cases
    }
}
```

### **2. Alert Styling:**
Modify `download-alerts.css`:

```css
.download-alert-content.error {
    border-top: 4px solid #your-color;
}
```

### **3. File Size Limits:**
Adjust in `DownloadController.php`:

```php
// Change 100MB limit
if ($fileInfo['size'] > 50 * 1024 * 1024) { // 50MB
    // Error handling
}
```

## 🧪 **Testing**

### **1. Test Error Scenarios:**

#### **File Not Found:**
```bash
# Delete file and try download
rm uploads/test.pdf
# Click download link -> Should show 404 error
```

#### **Permission Denied:**
```bash
# Remove read permission
chmod 000 uploads/test.pdf
# Click download link -> Should show 403 error
```

#### **Large File:**
```bash
# Create large file
dd if=/dev/zero of=uploads/large.pdf bs=1M count=200
# Click download link -> Should show size error
```

### **2. Test Success Scenario:**
```bash
# Ensure file exists and readable
chmod 644 uploads/test.pdf
# Click download link -> Should download successfully
```

## 📊 **Error Monitoring**

### **1. Server-Side Logging:**
```php
// In DownloadController.php
error_log("Download error: " . $e->getMessage());
```

### **2. Client-Side Logging:**
```javascript
// In download-handler.js
console.error('Download error:', error);
```

### **3. Analytics Integration:**
```javascript
// Track download errors
if (typeof gtag !== 'undefined') {
    gtag('event', 'download_error', {
        'error_type': status,
        'file_name': filename
    });
}
```

## 🔒 **Security Features**

### **1. Path Sanitization:**
```php
// Prevent directory traversal
$path = str_replace(['../', '..\\'], '', $path);
```

### **2. Authentication Check:**
```php
// Require login
Auth::requireAuth();
```

### **3. File Type Validation:**
```php
// Only allow specific file types
$allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];
```

## 📋 **Browser Support**

- ✅ **Chrome 60+**
- ✅ **Firefox 55+**
- ✅ **Safari 12+**
- ✅ **Edge 79+**
- ✅ **Mobile browsers**

## 🚀 **Performance**

### **1. Lazy Loading:**
- Scripts only loaded on pages with downloads
- CSS only loaded when needed

### **2. File Streaming:**
- Large files streamed in 8KB chunks
- Prevents memory exhaustion

### **3. Caching:**
- Static assets cached with version numbers
- Browser caching headers set

---

## 🎯 **Result**

**Users now get clear, actionable error messages when downloads fail, improving the overall user experience and reducing support requests!** 🎉

### **Before:**
- Generic browser download error
- No user feedback
- Confusion about what went wrong

### **After:**
- ✅ Clear error messages
- ✅ Specific problem identification  
- ✅ Actionable solutions
- ✅ Professional UI/UX
- ✅ Loading states
- ✅ Success confirmations
