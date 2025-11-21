# 🔧 Testing & Debugging Commands untuk Android WebView Biometric

## 📱 **1. Android Development Commands**

### Build & Install APK
```bash
# Build debug APK
./gradlew assembleDebug

# Install ke device
adb install app/build/outputs/apk/debug/app-debug.apk

# Install dan jalankan langsung
adb install -r app/build/outputs/apk/debug/app-debug.apk && adb shell am start -n com.dhainako.app/.MainActivity
```

### Debugging dengan ADB
```bash
# Monitor log untuk debugging
adb logcat | grep -E "(BiometricInterface|WebView|Console)"

# Clear log sebelum testing
adb logcat -c

# Monitor log real-time dengan filter
adb logcat -s "BiometricInterface:D" "WebView:D" "chromium:I"

# Check device biometric capability
adb shell dumpsys fingerprint
```

## 🌐 **2. WebView Testing URLs**

### Testing URLs (Load di Android WebView):
```
https://dps.logics-ti.com/simple-android-test.html
https://dps.logics-ti.com/android-debug-test.html  
https://dps.logics-ti.com/webview-debug.html
```

### Local Testing (jika ada local server):
```
http://10.0.2.2:8080/simple-android-test.html  # Android Emulator
http://192.168.1.100:8080/simple-android-test.html  # Real Device
```

## 🔍 **3. Browser Developer Tools**

### Enable WebView Debugging:
1. **Di Android Code**, tambahkan:
   ```java
   if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
       WebView.setWebContentsDebuggingEnabled(true);
   }
   ```

2. **Di Chrome Desktop**:
   - Buka `chrome://inspect/#devices`
   - Pilih WebView dari device
   - Click "Inspect"

### Console Commands untuk Testing:
```javascript
// Test basic interface
typeof Android !== 'undefined'

// Test methods availability
Object.getOwnPropertyNames(Android || {})

// Test WebAuthn Helper
WebAuthnHelper.isSupported()
WebAuthnHelper.isWebView()
WebAuthnHelper.isAndroidWebView()
WebAuthnHelper.isNativeBiometricAvailable()

// Manual callback test
window.biometricCallback = (result) => console.log('Callback:', result);
Android.authenticateBiometric('testuser', 'callback');
```

## 📋 **4. Step-by-Step Testing Process**

### Phase 1: Basic Interface Test
```bash
# 1. Load simple-android-test.html
# 2. Check Android Logcat
adb logcat | grep "BiometricInterface"

# 3. Expected results:
# ✅ Android interface found!
# ✅ Android.test() result: Android interface is working!
```

### Phase 2: Method Availability Test
```javascript
// Di Chrome DevTools Console:
console.log('Methods:', ['test', 'authenticateBiometric', 'registerBiometric', 'hasCredentials'].map(m => `${m}: ${typeof Android[m]}`));
```

### Phase 3: Biometric Hardware Test
```bash
# 1. Click "Test Register" di simple-android-test.html
# 2. Check logcat untuk BiometricManager results
# 3. Expected: Success atau error message yang jelas
```

### Phase 4: Authentication Test
```bash
# 1. Click "Test Authentication"
# 2. Should show biometric prompt
# 3. Check callback results
```

## 🚨 **5. Common Issues & Solutions**

### Issue: "Android interface not found"
```bash
# Check:
adb logcat | grep "addJavascriptInterface"

# Solution: Pastikan interface ditambahkan SEBELUM loadUrl()
```

### Issue: "Methods not available"
```bash
# Check Java class:
# - @JavascriptInterface annotation
# - Public methods
# - Proper class instantiation

# Debug:
adb logcat | grep "BiometricInterface"
```

### Issue: "Biometric not working"
```bash
# Check device capability:
adb shell dumpsys fingerprint

# Check permissions di AndroidManifest.xml:
# - USE_BIOMETRIC
# - USE_FINGERPRINT
```

## 📊 **6. Testing Checklist**

### ✅ Pre-Testing Checklist:
- [ ] Android app built dengan latest code
- [ ] WebView debugging enabled
- [ ] Device has biometric enrolled
- [ ] Permissions granted
- [ ] Internet connection available

### ✅ Testing Steps:
1. [ ] Load `simple-android-test.html`
2. [ ] Test basic interface (should show ✅)
3. [ ] Test all methods (should show ✅ for all)
4. [ ] Test hasCredentials (should return boolean)
5. [ ] Test register (should show biometric status)
6. [ ] Test authentication (should show biometric prompt)

### ✅ Expected Results:
- [ ] No "interface not found" errors
- [ ] All methods available
- [ ] Biometric prompt appears
- [ ] Callbacks work properly
- [ ] No JavaScript errors in console

## 🔧 **7. Advanced Debugging**

### Network Debugging:
```bash
# Monitor network requests
adb shell am start -a android.intent.action.VIEW -d "chrome://net-internals/#events"
```

### Performance Monitoring:
```bash
# Monitor WebView performance
adb shell dumpsys webviewupdate
```

### Memory Usage:
```bash
# Check memory usage
adb shell dumpsys meminfo com.dhainako.app
```

## 📝 **8. Log Analysis**

### Success Patterns:
```
BiometricInterface: authenticateBiometric called with username: testuser
BiometricInterface: registerBiometric called
WebView: Android interface available: true
```

### Error Patterns:
```
WebView Console: Android interface not found
BiometricInterface: Error in authenticateBiometric
chromium: [ERROR] JavaScript execution failed
```

## 🎯 **9. Quick Test Script**

Buat file `quick-test.sh`:
```bash
#!/bin/bash
echo "🔧 Quick Android WebView Test"

echo "📱 Installing APK..."
adb install -r app/build/outputs/apk/debug/app-debug.apk

echo "🚀 Starting app..."
adb shell am start -n com.dhainako.app/.MainActivity

echo "📊 Monitoring logs..."
adb logcat -c
adb logcat | grep -E "(BiometricInterface|WebView|Console)" &

echo "✅ Ready for testing!"
echo "Load: https://dps.logics-ti.com/simple-android-test.html"
```

Jalankan dengan: `chmod +x quick-test.sh && ./quick-test.sh`
