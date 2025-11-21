const WebAuthnHelper = {
  authenticateBiometric: function (username) {
    return new Promise((resolve, reject) => {
      // 1. Cek apakah jalan di Android App kita
      if (window.Android && window.Android.authenticateBiometric) {
        console.log("Android Bridge detected");

        // Siapkan callback global yang akan dipanggil oleh Android
        window.biometricCallback = function (result) {
          console.log("Android callback received", result);
          if (result.success) {
            resolve(result);
          } else {
            reject(new Error(result.error || "Biometric failed"));
          }
        };

        // Panggil fungsi Native Android
        // Parameter ke-2 "callback" sebenernya diabaikan di Java karena hardcoded ke window.biometricCallback
        window.Android.authenticateBiometric(username, "biometricCallback");
      } else {
        // 2. Fallback ke WebAuthn Standar (untuk Browser Desktop/Chrome)
        // ... Masukkan logika WebAuthn lama Anda di sini ...
        // navigator.credentials.get(...)
        console.log("Using Standard WebAuthn");
        reject(
          new Error(
            "Fitur WebAuthn standar belum diimplementasikan di snippet ini"
          )
        );
      }
    });
  },
};
