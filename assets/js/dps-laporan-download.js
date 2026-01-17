/**
 * DPS Laporan Download Helper
 * Fungsi helper untuk download laporan dengan support Android dan Browser
 *
 * Requirements:
 * - dps-download-helper.js
 * - dhainako-download-sdk.js (untuk Android app)
 */

(function () {
  "use strict";

  /**
   * Download laporan PDF dengan support Android dan Browser
   * @param {string} baseUrl - Base URL endpoint laporan
   * @param {object} params - Query parameters untuk laporan
   * @param {string} reportName - Nama laporan untuk filename
   * @param {string} action - Action: 'open', 'share', 'save' (default: 'open')
   */
  window.downloadLaporanPDF = function (
    baseUrl,
    params = {},
    reportName = "Laporan",
    action = "open",
  ) {
    // Ensure export=pdf is set
    params.export = "pdf";

    // Build URL with parameters
    const queryString = new URLSearchParams(params).toString();
    const url = baseUrl + (queryString ? "?" + queryString : "");

    // Generate filename with timestamp
    const date = new Date();
    const dateStr =
      date.getFullYear() +
      String(date.getMonth() + 1).padStart(2, "0") +
      String(date.getDate()).padStart(2, "0") +
      "_" +
      String(date.getHours()).padStart(2, "0") +
      String(date.getMinutes()).padStart(2, "0");

    // Clean up reportName - remove .pdf extension if present
    const cleanReportName = reportName.replace(/\.pdf$/i, "");
    const filename = `${cleanReportName}_${dateStr}.pdf`;

    console.log("Download PDF:", { url, filename, action });

    // Use DPSDownload helper if available
    if (typeof DPSDownload !== "undefined" && DPSDownload.download) {
      try {
        DPSDownload.download(url, filename, action, {
          onProgress: (progress) => {
            console.log(`Download progress: ${progress}%`);
          },
          onSuccess: () => {
            console.log(`Successfully downloaded: ${filename}`);
            // Optional: show toast notification
            if (typeof showToast === "function") {
              showToast(`${reportName} berhasil didownload`, "success");
            }
          },
          onError: (error) => {
            console.error(`Download error: ${error}`);
            // Fallback to browser download on error
            console.warn("DPSDownload failed, using browser fallback");
            window.location.href = url;
          },
        });
      } catch (e) {
        console.error("DPSDownload exception:", e);
        // Fallback to browser download
        window.location.href = url;
      }
    } else {
      console.warn("DPSDownload not available, using browser fallback");
      // Fallback to direct link for browser
      window.location.href = url;
    }
  };

  /**
   * Download report dengan parameter filter
   * Wrapper yang lebih mudah digunakan di HTML onclick
   */
  window.downloadReportWithFilters = function (
    endpoint,
    filters,
    reportName = "Laporan",
    action = "open",
  ) {
    downloadLaporanPDF(endpoint, filters, reportName, action);
  };

  /**
   * Download Excel laporan
   */
  window.downloadLaporanExcel = function (
    baseUrl,
    params = {},
    reportName = "Laporan",
  ) {
    params.export = "excel";
    const queryString = new URLSearchParams(params).toString();
    const url = baseUrl + (queryString ? "?" + queryString : "");

    // Use DPSDownload if available for consistency
    if (typeof DPSDownload !== "undefined") {
      const date = new Date();
      const dateStr =
        date.getFullYear() +
        String(date.getMonth() + 1).padStart(2, "0") +
        String(date.getDate()).padStart(2, "0");
      const filename = `${reportName}_${dateStr}.xlsx`;

      DPSDownload.download(url, filename, "save", {
        onSuccess: () => {
          console.log(`Excel downloaded: ${filename}`);
          if (typeof showToast === "function") {
            showToast(`${reportName} berhasil didownload`, "success");
          }
        },
        onError: (error) => {
          alert(`Gagal download ${reportName}: ${error}`);
        },
      });
    } else {
      // Fallback
      window.location.href = url;
    }
  };

  /**
   * Build download button onclick handler
   * Usage: buildDownloadOnClick('/laporan/daftar-barang', {search: 'test'}, 'Daftar_Barang')
   */
  window.buildDownloadOnClick = function (endpoint, filters, reportName) {
    return `downloadReportWithFilters('${endpoint}', ${JSON.stringify(filters)}, '${reportName}')`;
  };

  /**
   * Check apakah running di Android app
   */
  window.isAndroidApp = function () {
    return typeof DPSDownload !== "undefined" && DPSDownload.isAndroidApp();
  };

  /**
   * Share file (Android only)
   */
  window.shareReport = function (endpoint, filters, reportName) {
    if (!isAndroidApp()) {
      alert("Share hanya tersedia di aplikasi Android");
      return;
    }
    downloadLaporanPDF(endpoint, filters, reportName, "share");
  };

  /**
   * Save file tanpa membuka (Android only)
   */
  window.saveReport = function (endpoint, filters, reportName) {
    if (!isAndroidApp()) {
      // Di browser, ini equivalent dengan standard download
      downloadLaporanPDF(endpoint, filters, reportName, "open");
      return;
    }
    downloadLaporanPDF(endpoint, filters, reportName, "save");
  };

  console.log("DPS Laporan Download Helper loaded");
})();
