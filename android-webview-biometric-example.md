# Android WebView Biometric Integration

## Masalah WebAuthn di Android WebView

WebAuthn tidak bekerja di Android WebView karena:
- Security restrictions
- Limited API access
- Different browser engine

## Solusi: JavaScript Bridge + Native Biometric

### 1. Android Side (Java/Kotlin)

```kotlin
// MainActivity.kt
class MainActivity : AppCompatActivity() {
    private lateinit var webView: WebView
    private lateinit var biometricPrompt: BiometricPrompt
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        setupWebView()
        setupBiometric()
    }
    
    private fun setupWebView() {
        webView = findViewById(R.id.webview)
        webView.settings.javaScriptEnabled = true
        webView.settings.domStorageEnabled = true
        
        // Add JavaScript interface
        webView.addJavascriptInterface(BiometricInterface(), "Android")
        
        webView.loadUrl("https://dps.logics-ti.com")
    }
    
    private fun setupBiometric() {
        val executor = ContextCompat.getMainExecutor(this)
        biometricPrompt = BiometricPrompt(this as FragmentActivity, executor,
            object : BiometricPrompt.AuthenticationCallback() {
                override fun onAuthenticationError(errorCode: Int, errString: CharSequence) {
                    super.onAuthenticationError(errorCode, errString)
                    // Send error to JavaScript
                    runOnUiThread {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: false, error: '$errString'})", 
                            null
                        )
                    }
                }
                
                override fun onAuthenticationSucceeded(result: BiometricPrompt.AuthenticationResult) {
                    super.onAuthenticationSucceeded(result)
                    // Send success to JavaScript
                    runOnUiThread {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: true, redirect: '/dashboard'})", 
                            null
                        )
                    }
                }
                
                override fun onAuthenticationFailed() {
                    super.onAuthenticationFailed()
                    // Send failure to JavaScript
                    runOnUiThread {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: false, error: 'Authentication failed'})", 
                            null
                        )
                    }
                }
            })
    }
    
    inner class BiometricInterface {
        @JavascriptInterface
        fun authenticateBiometric(username: String, callback: String) {
            runOnUiThread {
                val promptInfo = BiometricPrompt.PromptInfo.Builder()
                    .setTitle("Biometric Authentication")
                    .setSubtitle("Login dengan sidik jari atau wajah")
                    .setNegativeButtonText("Batal")
                    .build()
                    
                biometricPrompt.authenticate(promptInfo)
            }
        }
        
        @JavascriptInterface
        fun registerBiometric(callback: String) {
            // Handle biometric registration
            runOnUiThread {
                webView.evaluateJavascript(
                    "window.biometricRegisterCallback({success: true})", 
                    null
                )
            }
        }
    }
}
```

### 2. AndroidManifest.xml

```xml
<uses-permission android:name="android.permission.USE_BIOMETRIC" />
<uses-permission android:name="android.permission.USE_FINGERPRINT" />
```

### 3. build.gradle (app level)

```gradle
dependencies {
    implementation 'androidx.biometric:biometric:1.1.0'
}
```

## Cara Kerja:

1. **WebView Detection** - JavaScript mendeteksi jika berjalan di WebView
2. **Native Bridge** - Memanggil fungsi Android native melalui JavaScript interface
3. **Biometric Prompt** - Android menampilkan dialog biometric native
4. **Callback** - Hasil dikirim kembali ke JavaScript melalui callback

## Testing:

1. Build dan install APK
2. Buka aplikasi (akan load WebView dengan website)
3. Coba login dengan biometric
4. Seharusnya muncul dialog biometric native Android

## Fallback Options:

Jika native biometric gagal, sistem akan:
1. Coba WebAuthn (jika didukung)
2. Fallback ke PIN/password
3. Show error message yang informatif
