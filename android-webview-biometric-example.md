# Android WebView Biometric Integration

## Masalah WebAuthn di Android WebView

WebAuthn tidak bekerja di Android WebView karena:

- Security restrictions
- Limited API access
- Different browser engine

## Solusi: JavaScript Bridge + Native Biometric

### 1. Android Side (Java)

```java
// MainActivity.java
import android.os.Bundle;
import android.webkit.JavascriptInterface;
import android.webkit.WebView;
import android.webkit.WebSettings;
import androidx.appcompat.app.AppCompatActivity;
import androidx.biometric.BiometricManager;
import androidx.biometric.BiometricPrompt;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.FragmentActivity;
import java.util.concurrent.Executor;

public class MainActivity extends AppCompatActivity {
    private WebView webView;
    private BiometricPrompt biometricPrompt;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        setupWebView();
        setupBiometric();
    }

    private void setupWebView() {
        webView = findViewById(R.id.webview);
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setAllowFileAccess(true);
        webSettings.setAllowContentAccess(true);
        webSettings.setAllowFileAccessFromFileURLs(true);
        webSettings.setAllowUniversalAccessFromFileURLs(true);

        // IMPORTANT: Add JavaScript interface BEFORE loading URL
        BiometricInterface biometricInterface = new BiometricInterface();
        webView.addJavascriptInterface(biometricInterface, "Android");

        // Enable debugging
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.KITKAT) {
            android.webkit.WebView.setWebContentsDebuggingEnabled(true);
        }

        // Add WebViewClient for debugging
        webView.setWebViewClient(new android.webkit.WebViewClient() {
            @Override
            public void onPageFinished(android.webkit.WebView view, String url) {
                super.onPageFinished(view, url);
                android.util.Log.d("WebView", "Page finished loading: " + url);

                // Test if interface is available
                webView.evaluateJavascript(
                    "console.log('Android interface available:', typeof Android !== 'undefined'); " +
                    "console.log('Android methods:', Object.getOwnPropertyNames(Android || {}));",
                    null
                );
            }

            @Override
            public void onReceivedError(android.webkit.WebView view, int errorCode, String description, String failingUrl) {
                super.onReceivedError(view, errorCode, description, failingUrl);
                android.util.Log.e("WebView", "Error loading page: " + description);
            }
        });

        // Add WebChromeClient for console logging
        webView.setWebChromeClient(new android.webkit.WebChromeClient() {
            @Override
            public boolean onConsoleMessage(android.webkit.ConsoleMessage consoleMessage) {
                android.util.Log.d("WebView Console", consoleMessage.message() + " -- From line " +
                    consoleMessage.lineNumber() + " of " + consoleMessage.sourceId());
                return super.onConsoleMessage(consoleMessage);
            }
        });

        webView.loadUrl("https://dps.logics-ti.com");
    }

    private void setupBiometric() {
        Executor executor = ContextCompat.getMainExecutor(this);
        biometricPrompt = new BiometricPrompt((FragmentActivity) this, executor,
            new BiometricPrompt.AuthenticationCallback() {
                @Override
                public void onAuthenticationError(int errorCode, CharSequence errString) {
                    super.onAuthenticationError(errorCode, errString);
                    // Send error to JavaScript
                    runOnUiThread(() -> {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: false, error: '" + errString + "'})",
                            null
                        );
                    });
                }

                @Override
                public void onAuthenticationSucceeded(BiometricPrompt.AuthenticationResult result) {
                    super.onAuthenticationSucceeded(result);
                    // Send success to JavaScript
                    runOnUiThread(() -> {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: true, redirect: '/dashboard'})",
                            null
                        );
                    });
                }

                @Override
                public void onAuthenticationFailed() {
                    super.onAuthenticationFailed();
                    // Send failure to JavaScript
                    runOnUiThread(() -> {
                        webView.evaluateJavascript(
                            "window.biometricCallback({success: false, error: 'Authentication failed'})",
                            null
                        );
                    });
                }
            });
    }

    public class BiometricInterface {

        // Test method to verify interface is working
        @JavascriptInterface
        public String test() {
            return "Android interface is working!";
        }

        @JavascriptInterface
        public void authenticateBiometric(String username, String callback) {
            android.util.Log.d("BiometricInterface", "authenticateBiometric called with username: " + username);

            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    try {
                        BiometricPrompt.PromptInfo promptInfo = new BiometricPrompt.PromptInfo.Builder()
                            .setTitle("Biometric Authentication")
                            .setSubtitle("Login dengan sidik jari atau wajah")
                            .setNegativeButtonText("Batal")
                            .build();

                        biometricPrompt.authenticate(promptInfo);
                    } catch (Exception e) {
                        android.util.Log.e("BiometricInterface", "Error in authenticateBiometric", e);
                        webView.evaluateJavascript(
                            "if(window.biometricCallback) window.biometricCallback({success: false, error: 'Error: " + e.getMessage() + "'})",
                            null
                        );
                    }
                }
            });
        }

        @JavascriptInterface
        public void registerBiometric(String callback) {
            android.util.Log.d("BiometricInterface", "registerBiometric called");

            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    try {
                        // Check if biometric hardware is available
                        BiometricManager biometricManager = BiometricManager.from(MainActivity.this);
                        int canAuthenticate = biometricManager.canAuthenticate(BiometricManager.Authenticators.BIOMETRIC_WEAK);

                        String jsCallback = "if(window.biometricRegisterCallback) window.biometricRegisterCallback";

                        switch (canAuthenticate) {
                            case BiometricManager.BIOMETRIC_SUCCESS:
                                webView.evaluateJavascript(jsCallback + "({success: true})", null);
                                break;
                            case BiometricManager.BIOMETRIC_ERROR_NO_HARDWARE:
                                webView.evaluateJavascript(jsCallback + "({success: false, error: 'No biometric hardware'})", null);
                                break;
                            case BiometricManager.BIOMETRIC_ERROR_HW_UNAVAILABLE:
                                webView.evaluateJavascript(jsCallback + "({success: false, error: 'Biometric hardware unavailable'})", null);
                                break;
                            case BiometricManager.BIOMETRIC_ERROR_NONE_ENROLLED:
                                webView.evaluateJavascript(jsCallback + "({success: false, error: 'No biometric enrolled'})", null);
                                break;
                            default:
                                webView.evaluateJavascript(jsCallback + "({success: false, error: 'Biometric not available'})", null);
                                break;
                        }
                    } catch (Exception e) {
                        android.util.Log.e("BiometricInterface", "Error in registerBiometric", e);
                        webView.evaluateJavascript(
                            "if(window.biometricRegisterCallback) window.biometricRegisterCallback({success: false, error: 'Error: " + e.getMessage() + "'})",
                            null
                        );
                    }
                }
            });
        }

        @JavascriptInterface
        public void hasCredentials(String username, String callback) {
            android.util.Log.d("BiometricInterface", "hasCredentials called with username: " + username);

            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    try {
                        // Check if biometric is enrolled and available
                        BiometricManager biometricManager = BiometricManager.from(MainActivity.this);
                        boolean hasCredentials = biometricManager.canAuthenticate(BiometricManager.Authenticators.BIOMETRIC_WEAK) == BiometricManager.BIOMETRIC_SUCCESS;

                        webView.evaluateJavascript(
                            "if(window.hasCredentialsCallback) window.hasCredentialsCallback({hasCredentials: " + hasCredentials + "})",
                            null
                        );
                    } catch (Exception e) {
                        android.util.Log.e("BiometricInterface", "Error in hasCredentials", e);
                        webView.evaluateJavascript(
                            "if(window.hasCredentialsCallback) window.hasCredentialsCallback({hasCredentials: false, error: '" + e.getMessage() + "'})",
                            null
                        );
                    }
                }
            });
        }
    }
}
```

### 2. Layout XML

```xml
<!-- res/layout/activity_main.xml -->
<?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:orientation="vertical">

    <WebView
        android:id="@+id/webview"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />

</LinearLayout>
```

### 3. AndroidManifest.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="com.dhainako.app">

    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.USE_BIOMETRIC" />
    <uses-permission android:name="android.permission.USE_FINGERPRINT" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:theme="@style/AppTheme">

        <activity
            android:name=".MainActivity"
            android:exported="true">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>

            <!-- Android App Links -->
            <intent-filter android:autoVerify="true">
                <action android:name="android.intent.action.VIEW" />
                <category android:name="android.intent.category.DEFAULT" />
                <category android:name="android.intent.category.BROWSABLE" />
                <data android:scheme="https"
                      android:host="dps.logics-ti.com" />
            </intent-filter>
        </activity>

    </application>

</manifest>
```

### 4. build.gradle (app level)

```gradle
android {
    compileSdkVersion 34

    defaultConfig {
        applicationId "com.dhainako.app"
        minSdkVersion 23
        targetSdkVersion 34
        versionCode 1
        versionName "1.0"
    }
}

dependencies {
    implementation 'androidx.appcompat:appcompat:1.6.1'
    implementation 'androidx.biometric:biometric:1.1.0'
    implementation 'androidx.fragment:fragment:1.6.2'
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
