// WebAuthn helper functions
class WebAuthnHelper {
  // Check if WebAuthn is supported
  static isSupported() {
    return (
      typeof window.PublicKeyCredential !== "undefined" &&
      !WebAuthnHelper.isAndroidWebView()
    );
  }

  // Detect if running in Android WebView
  static isAndroidWebView() {
    const userAgent = navigator.userAgent;
    // Check for Android WebView indicators
    return (
      /Android/.test(userAgent) &&
      (/wv/.test(userAgent) || /Version\/\d+\.\d+/.test(userAgent)) &&
      !/Chrome\/[.0-9]*/.test(userAgent)
    );
  }

  // Check if running in any WebView (iOS or Android)
  static isWebView() {
    const userAgent = navigator.userAgent;
    // Android WebView
    if (WebAuthnHelper.isAndroidWebView()) {
      return true;
    }
    // iOS WebView (WKWebView or UIWebView)
    if (/iPhone|iPad/.test(userAgent)) {
      return (
        !userAgent.includes("Safari/") ||
        (userAgent.includes("WebKit") && !userAgent.includes("Version/"))
      );
    }
    return false;
  }

  // Convert base64url to ArrayBuffer
  static base64UrlToArrayBuffer(base64url) {
    const base64 = base64url.replace(/-/g, "+").replace(/_/g, "/");
    const binaryString = window.atob(base64);
    const bytes = new Uint8Array(binaryString.length);
    for (let i = 0; i < binaryString.length; i++) {
      bytes[i] = binaryString.charCodeAt(i);
    }
    return bytes.buffer;
  }

  // Convert ArrayBuffer to base64url
  static arrayBufferToBase64Url(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = "";
    for (let i = 0; i < bytes.byteLength; i++) {
      binary += String.fromCharCode(bytes[i]);
    }
    const base64 = window.btoa(binary);
    return base64.replace(/\+/g, "-").replace(/\//g, "_").replace(/=/g, "");
  }

  // Convert credential to format for sending to server
  static credentialToJSON(credential) {
    if (credential instanceof Array) {
      return credential.map((c) => WebAuthnHelper.credentialToJSON(c));
    }

    if (credential instanceof ArrayBuffer) {
      return WebAuthnHelper.arrayBufferToBase64Url(credential);
    }

    if (credential instanceof Object) {
      const obj = {};
      for (let key in credential) {
        obj[key] = WebAuthnHelper.credentialToJSON(credential[key]);
      }
      return obj;
    }

    return credential;
  }

  // Start registration process
  static async registerBiometric() {
    // Check if we're in WebView and try native biometric first
    if (WebAuthnHelper.isWebView()) {
      try {
        return await WebAuthnHelper.registerNativeBiometric();
      } catch (error) {
        console.log(
          "Native biometric registration failed, falling back to WebAuthn:",
          error.message
        );
        // Fall through to WebAuthn if native fails
      }
    }

    if (!WebAuthnHelper.isSupported()) {
      throw new Error(
        "WebAuthn tidak didukung di browser ini. Pastikan menggunakan browser modern (Chrome, Firefox, Edge, Safari)"
      );
    }

    try {
      // Get registration options from server
      const response = await fetch("/api/webauthn/registration/start", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        credentials: "include",
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.error || "Gagal memulai registrasi");
      }

      const options = data.options;

      // Convert challenge to ArrayBuffer
      options.challenge = WebAuthnHelper.base64UrlToArrayBuffer(
        options.challenge
      );
      options.user.id = WebAuthnHelper.base64UrlToArrayBuffer(options.user.id);

      // Convert allowCredentials if exists
      if (options.allowCredentials) {
        options.allowCredentials = options.allowCredentials.map((cred) => ({
          ...cred,
          id: WebAuthnHelper.base64UrlToArrayBuffer(cred.id),
        }));
      }

      // Create credential
      const credential = await navigator.credentials.create({
        publicKey: options,
      });

      // Convert credential to JSON for sending to server
      const publicKeyArrayBuffer = credential.response.getPublicKey
        ? credential.response.getPublicKey()
        : null;

      const credentialJSON = {
        id: credential.id,
        rawId: WebAuthnHelper.arrayBufferToBase64Url(credential.rawId),
        type: credential.type,
        response: {
          clientDataJSON: WebAuthnHelper.arrayBufferToBase64Url(
            credential.response.clientDataJSON
          ),
          attestationObject: WebAuthnHelper.arrayBufferToBase64Url(
            credential.response.attestationObject
          ),
        },
      };

      // Add publicKey if available
      if (publicKeyArrayBuffer) {
        credentialJSON.response.publicKey =
          WebAuthnHelper.arrayBufferToBase64Url(publicKeyArrayBuffer);
      }

      // Send to server
      const completeResponse = await fetch(
        "/api/webauthn/registration/complete",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          credentials: "include",
          body: JSON.stringify({
            credential: credentialJSON,
          }),
        }
      );

      const completeData = await completeResponse.json();

      if (!completeData.success) {
        throw new Error(completeData.error || "Gagal menyelesaikan registrasi");
      }

      return completeData;
    } catch (error) {
      console.error("WebAuthn registration error:", error);
      throw error;
    }
  }

  // Start authentication process
  static async authenticateBiometric(username) {
    // Check if we're in WebView and try native biometric first
    if (WebAuthnHelper.isWebView()) {
      try {
        return await WebAuthnHelper.authenticateNativeBiometric(username);
      } catch (error) {
        console.log(
          "Native biometric failed, falling back to WebAuthn:",
          error.message
        );
        // Fall through to WebAuthn if native fails
      }
    }

    if (!WebAuthnHelper.isSupported()) {
      throw new Error("WebAuthn tidak didukung di browser ini");
    }

    try {
      // Get authentication options from server
      const response = await fetch("/api/webauthn/authentication/start", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        credentials: "include",
        body: JSON.stringify({ username }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.error || "Gagal memulai autentikasi");
      }

      const options = data.options;

      // Convert challenge to ArrayBuffer
      options.challenge = WebAuthnHelper.base64UrlToArrayBuffer(
        options.challenge
      );

      // Convert allowCredentials
      if (options.allowCredentials && options.allowCredentials.length > 0) {
        options.allowCredentials = options.allowCredentials.map((cred) => ({
          ...cred,
          id: WebAuthnHelper.base64UrlToArrayBuffer(cred.id),
        }));
      }

      // Get credential
      const credential = await navigator.credentials.get({
        publicKey: options,
      });

      // Convert credential to JSON
      const credentialJSON = {
        id: credential.id,
        rawId: WebAuthnHelper.arrayBufferToBase64Url(credential.rawId),
        type: credential.type,
        response: {
          authenticatorData: WebAuthnHelper.arrayBufferToBase64Url(
            credential.response.authenticatorData
          ),
          clientDataJSON: WebAuthnHelper.arrayBufferToBase64Url(
            credential.response.clientDataJSON
          ),
          signature: WebAuthnHelper.arrayBufferToBase64Url(
            credential.response.signature
          ),
          userHandle: credential.response.userHandle
            ? WebAuthnHelper.arrayBufferToBase64Url(
                credential.response.userHandle
              )
            : null,
        },
      };

      // Send to server
      const completeResponse = await fetch(
        "/api/webauthn/authentication/complete",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          credentials: "include",
          body: JSON.stringify({
            credential: credentialJSON,
          }),
        }
      );

      const completeData = await completeResponse.json();

      if (!completeData.success) {
        throw new Error(completeData.error || "Autentikasi gagal");
      }

      return completeData;
    } catch (error) {
      console.error("WebAuthn authentication error:", error);
      throw error;
    }
  }

  // Check if user has biometric credentials
  static async hasCredentials(username) {
    try {
      const response = await fetch("/api/webauthn/authentication/start", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        credentials: "include",
        body: JSON.stringify({ username }),
      });

      if (!response.ok) {
        return false;
      }

      const data = await response.json();

      if (data.success && data.options) {
        const allowCredentials = data.options.allowCredentials;
        if (allowCredentials && Array.isArray(allowCredentials)) {
          return allowCredentials.length > 0;
        }
      }

      return false;
    } catch (error) {
      console.error("Error checking credentials:", error);
      return false;
    }
  }

  // List user credentials
  static async listCredentials() {
    const response = await fetch("/api/webauthn/credentials", {
      method: "GET",
      credentials: "include",
    });

    const data = await response.json();
    return data.success ? data.credentials : [];
  }

  // Delete credential
  static async deleteCredential(credentialId) {
    const response = await fetch("/api/webauthn/credentials/delete", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
      body: JSON.stringify({ credential_id: credentialId }),
    });

    const data = await response.json();
    return data.success;
  }

  // Native biometric authentication for WebView
  static async authenticateNativeBiometric(username) {
    return new Promise((resolve, reject) => {
      // Check if Android interface is available
      if (typeof Android !== "undefined" && Android.authenticateBiometric) {
        try {
          // Call Android native biometric
          Android.authenticateBiometric(username, (result) => {
            if (result.success) {
              resolve(result);
            } else {
              reject(
                new Error(
                  result.error || "Native biometric authentication failed"
                )
              );
            }
          });
        } catch (error) {
          reject(
            new Error("Failed to call native biometric: " + error.message)
          );
        }
      }
      // Check if iOS interface is available
      else if (
        typeof webkit !== "undefined" &&
        webkit.messageHandlers &&
        webkit.messageHandlers.biometric
      ) {
        try {
          // Setup callback for iOS
          window.biometricCallback = (result) => {
            if (result.success) {
              resolve(result);
            } else {
              reject(
                new Error(
                  result.error || "Native biometric authentication failed"
                )
              );
            }
          };

          // Call iOS native biometric
          webkit.messageHandlers.biometric.postMessage({
            action: "authenticate",
            username: username,
          });
        } catch (error) {
          reject(
            new Error("Failed to call native biometric: " + error.message)
          );
        }
      } else {
        reject(new Error("Native biometric interface not available"));
      }
    });
  }

  // Register native biometric for WebView
  static async registerNativeBiometric() {
    return new Promise((resolve, reject) => {
      // Check if Android interface is available
      if (typeof Android !== "undefined" && Android.registerBiometric) {
        try {
          Android.registerBiometric((result) => {
            if (result.success) {
              resolve(result);
            } else {
              reject(
                new Error(
                  result.error || "Native biometric registration failed"
                )
              );
            }
          });
        } catch (error) {
          reject(
            new Error(
              "Failed to call native biometric registration: " + error.message
            )
          );
        }
      }
      // Check if iOS interface is available
      else if (
        typeof webkit !== "undefined" &&
        webkit.messageHandlers &&
        webkit.messageHandlers.biometric
      ) {
        try {
          // Setup callback for iOS
          window.biometricRegisterCallback = (result) => {
            if (result.success) {
              resolve(result);
            } else {
              reject(
                new Error(
                  result.error || "Native biometric registration failed"
                )
              );
            }
          };

          // Call iOS native biometric registration
          webkit.messageHandlers.biometric.postMessage({
            action: "register",
          });
        } catch (error) {
          reject(
            new Error(
              "Failed to call native biometric registration: " + error.message
            )
          );
        }
      } else {
        reject(new Error("Native biometric interface not available"));
      }
    });
  }
}
