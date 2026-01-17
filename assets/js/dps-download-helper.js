/**
 * DPS Download Helper
 * Global helper functions for downloading files in DPS app
 * Compatible with both Android WebView and browsers
 *
 * @version 1.0.0
 * @requires dhainako-download-sdk.js (for Android app)
 */
const DPSDownload = (function () {
  "use strict";
  /**
   * Check if running in Android app
   */
  function isAndroidApp() {
    return (
      typeof DhainakoDownload !== "undefined" && DhainakoDownload.isAvailable()
    );
  }
  /**
   * Generic download function
   */
  function download(url, filename, action = "open", options = {}) {
    if (isAndroidApp()) {
      // Android app - use SDK
      switch (action) {
        case "open":
          DhainakoDownload.downloadAndOpen(url, filename, options);
          break;
        case "share":
          DhainakoDownload.downloadAndShare(url, filename, options);
          break;
        case "save":
          DhainakoDownload.downloadAndSave(url, filename, options);
          break;
        default:
          DhainakoDownload.downloadAndOpen(url, filename, options);
      }
    } else {
      // Browser - fallback to normal download
      if (options.newTab) {
        window.open(url, "_blank");
      } else {
        window.location.href = url;
      }
    }
  }
  /**
   * Download invoice/nota penjualan
   * @param {string} noPenjualan - Nomor penjualan
   * @param {string} action - 'open', 'share', or 'save'
   */
  function downloadInvoice(noPenjualan, action = "open") {
    const url = `/api/invoice/generate?no=${encodeURIComponent(noPenjualan)}`;
    const filename = `Invoice_${noPenjualan}_${getDateString()}.pdf`;

    download(url, filename, action, {
      onProgress: (progress) => {
        console.log(`Download invoice ${noPenjualan}: ${progress}%`);
      },
      onSuccess: () => {
        console.log(`Invoice ${noPenjualan} ${action} successful`);
      },
      onError: (error) => {
        alert(`Gagal download invoice: ${error}`);
      },
    });
  }
  /**
   * Download order file attachment
   * @param {string} path - File path in uploads
   * @param {string} name - File name
   * @param {string} action - 'open', 'share', or 'save'
   */
  function downloadOrderFile(path, name, action = "open") {
    const url = `/download/file?path=${encodeURIComponent(path)}&name=${encodeURIComponent(name)}`;

    download(url, name, action, {
      onProgress: (progress) => {
        console.log(`Download ${name}: ${progress}%`);
      },
      onError: (error) => {
        alert(`Gagal download file: ${error}`);
      },
    });
  }
  /**
   * Download report/laporan
   * @param {string} reportType - Type of report (omset, stok, piutang, etc.)
   * @param {object} params - Filter parameters
   * @param {string} action - 'open', 'share', or 'save'
   */
  function downloadReport(reportType, params = {}, action = "open") {
    const queryString = new URLSearchParams(params).toString();
    const url = `/laporan/export-${reportType}${queryString ? "?" + queryString : ""}`;
    const filename = `Laporan_${capitalizeFirst(reportType)}_${getDateString()}.pdf`;

    download(url, filename, action, {
      onProgress: (progress) => {
        console.log(`Export ${reportType}: ${progress}%`);
      },
      onSuccess: () => {
        console.log(`Report ${reportType} exported successfully`);
      },
      onError: (error) => {
        alert(`Gagal export laporan: ${error}`);
      },
    });
  }
  /**
   * Download nota penerimaan
   * @param {string} noPenerimaan - Nomor penerimaan
   * @param {string} action - 'open', 'share', or 'save'
   */
  function downloadNotaPenerimaan(noPenerimaan, action = "open") {
    const url = `/api/nota-penerimaan/generate?no=${encodeURIComponent(noPenerimaan)}`;
    const filename = `Nota_Penerimaan_${noPenerimaan}_${getDateString()}.pdf`;

    download(url, filename, action);
  }
  /**
   * Download surat order
   * @param {string} noOrder - Nomor order
   * @param {string} action - 'open', 'share', or 'save'
   */
  function downloadSuratOrder(noOrder, action = "open") {
    const url = `/api/surat-order/generate?no=${encodeURIComponent(noOrder)}`;
    const filename = `Surat_Order_${noOrder}_${getDateString()}.pdf`;

    download(url, filename, action);
  }
  /**
   * Download multiple files (batch download)
   * @param {Array} files - Array of {url, filename}
   */
  function downloadMultiple(files) {
    if (!isAndroidApp()) {
      alert("Batch download hanya tersedia di aplikasi Android");
      return;
    }
    let completed = 0;
    const total = files.length;
    files.forEach((file, index) => {
      // Delay each download by 500ms to avoid overwhelming the system
      setTimeout(() => {
        DhainakoDownload.downloadAndSave(file.url, file.filename, {
          onSuccess: () => {
            completed++;
            console.log(`Downloaded ${completed}/${total}: ${file.filename}`);

            if (completed === total) {
              alert(`Semua file berhasil didownload (${total} file)`);
            }
          },
          onError: (error) => {
            console.error(`Failed to download ${file.filename}:`, error);
          },
        });
      }, index * 500);
    });
  }
  // ========== Helper Functions ==========
  /**
   * Get current date string (YYYYMMDD)
   */
  function getDateString() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    return `${year}${month}${day}`;
  }
  /**
   * Capitalize first letter
   */
  function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
  // Public API
  return {
    // Main functions
    download,
    downloadInvoice,
    downloadOrderFile,
    downloadReport,
    downloadNotaPenerimaan,
    downloadSuratOrder,
    downloadMultiple,

    // Utility
    isAndroidApp,

    // Shortcuts
    invoice: downloadInvoice,
    orderFile: downloadOrderFile,
    report: downloadReport,
    penerimaan: downloadNotaPenerimaan,
    order: downloadSuratOrder,
  };
})();
// Log initialization
console.log(
  "DPS Download Helper loaded. Android app:",
  DPSDownload.isAndroidApp(),
);
