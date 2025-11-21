const WebAuthnHelper = {
  // Fungsi utama untuk login biometrik
  authenticateBiometric: function (username) {
    return new Promise((resolve, reject) => {
      // 1. DETEKSI: Apakah aplikasi berjalan di Android WebView kita?
      if (window.Android && window.Android.authenticateBiometric) {
        console.log("Android Bridge detected: Menggunakan Native Biometric");

        // 2. PERSIAPAN: Buat fungsi penerima (callback) yang akan dipanggil Android nanti
        window.biometricCallback = function (result) {
          console.log("Android callback received:", result);

          if (result.success) {
            // Jika Android bilang sukses, kita selesaikan Promise ini
            resolve(result);
          } else {
            // Jika Android bilang gagal, kita lempar error
            reject(
              new Error(result.error || "Authentication failed from Android")
            );
          }
        };

        // 3. EKSEKUSI: Panggil fungsi Java di Android
        window.Android.authenticateBiometric(username, "biometricCallback");
      } else {
        // 4. FALLBACK: Jika dibuka di Chrome/Laptop biasa (Bukan App Android)
        console.log(
          "Android Bridge NOT detected: Menggunakan WebAuthn Standar"
        );

        // --- PASTE KODE LAMA ANDA DI SINI ---
        // Masukkan logika navigator.credentials.get(...) yang lama di sini
        // agar fitur ini tetap jalan saat dibuka di laptop.

        reject(new Error("Fitur ini hanya berjalan di Aplikasi Android."));
      }
    });
  },

  // Fungsi Register (Opsional, jika dipakai)
  registerBiometric: function () {
    return new Promise((resolve, reject) => {
      if (window.Android && window.Android.registerBiometric) {
        window.biometricRegisterCallback = function (result) {
          if (result.success) resolve(result);
          else reject(new Error(result.error));
        };
        window.Android.registerBiometric("biometricRegisterCallback");
      } else {
        reject(new Error("Not supported on this platform"));
      }
    });
  },

  // Fungsi Cek Credential (Opsional)
  hasCredentials: function (username) {
    return new Promise((resolve, reject) => {
      if (window.Android && window.Android.hasCredentials) {
        window.hasCredentialsCallback = function (result) {
          resolve(result.hasCredentials);
        };
        window.Android.hasCredentials(username, "hasCredentialsCallback");
      } else {
        resolve(false);
      }
    });
  },
};
