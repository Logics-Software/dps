/**
 * Dhainako Download SDK
 * JavaScript library for downloading files in Dhainako Android WebView app
 *
 * Features:
 * - Download and automatically open files (PDF, images, documents)
 * - Download and share files via WhatsApp, Email, etc.
 * - Download and save files to cache
 * - Progress tracking
 * - Error handling
 *
 * @version 1.0.0
 */
const DhainakoDownload = (function () {
  "use strict";
  // Check if running in Dhainako Android app
  const isAndroidApp = typeof AndroidDownload !== "undefined";
  // Callbacks registry
  const callbacks = {};
  /**
   * Download file and immediately open with appropriate viewer
   * @param {string} url - URL to download from (absolute or relative)
   * @param {string} filename - Filename to save as (e.g., "invoice.pdf")
   * @param {object} options - Optional callbacks {onProgress, onSuccess, onError}
   */
  function downloadAndOpen(url, filename, options = {}) {
    download(url, filename, "OPEN", options);
  }
  /**
   * Download file and immediately show share dialog
   * @param {string} url - URL to download from
   * @param {string} filename - Filename to save as
   * @param {object} options - Optional callbacks
   */
  function downloadAndShare(url, filename, options = {}) {
    download(url, filename, "SHARE", options);
  }
  /**
   * Download file and save to cache (no additional action)
   * @param {string} url - URL to download from
   * @param {string} filename - Filename to save as
   * @param {object} options - Optional callbacks
   */
  function downloadAndSave(url, filename, options = {}) {
    download(url, filename, "SAVE", options);
  }
  /**
   * Main download function
   * @private
   */
  function download(url, filename, action, options) {
    // Validate parameters
    if (!url || !filename) {
      console.error("DhainakoDownload: URL and filename are required");
      if (options.onError) options.onError("URL and filename are required");
      return;
    }
    // Check if running in Android app
    if (!isAndroidApp) {
      console.warn(
        "DhainakoDownload: Not running in Dhainako Android app, using fallback",
      );
      fallbackDownload(url, filename);
      return;
    }
    // Convert relative URL to absolute
    const absoluteUrl = makeAbsoluteUrl(url);
    // Register callbacks
    if (options.onProgress || options.onSuccess || options.onError) {
      callbacks[filename] = options;
    }
    // Call Android bridge
    try {
      AndroidDownload.downloadFile(absoluteUrl, filename, action);
      console.log(
        `DhainakoDownload: Started download - ${filename} (${action})`,
      );
    } catch (error) {
      console.error("DhainakoDownload: Error calling Android bridge", error);
      if (options.onError) options.onError(error.message);
    }
  }
  /**
   * Cancel ongoing download
   * @param {string} filename - Filename of download to cancel
   */
  function cancelDownload(filename) {
    if (!isAndroidApp) {
      console.warn("DhainakoDownload: Cancel not supported in browser");
      return;
    }
    try {
      AndroidDownload.cancelDownload(filename);
      console.log(`DhainakoDownload: Canceled download - ${filename}`);
    } catch (error) {
      console.error("DhainakoDownload: Error canceling download", error);
    }
  }
  /**
   * Convert relative URL to absolute
   * @private
   */
  function makeAbsoluteUrl(url) {
    if (url.startsWith("http://") || url.startsWith("https://")) {
      return url;
    }
    // Relative URL - convert to absolute
    const base = window.location.origin;
    if (url.startsWith("/")) {
      return base + url;
    } else {
      const path = window.location.pathname.substring(
        0,
        window.location.pathname.lastIndexOf("/") + 1,
      );
      return base + path + url;
    }
  }
  /**
   * Fallback download for browsers (not Android app)
   * @private
   */
  function fallbackDownload(url, filename) {
    const link = document.createElement("a");
    link.href = url;
    link.download = filename;
    link.style.display = "none";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
  /**
   * Check if SDK is available (running in Dhainako Android app)
   */
  function isAvailable() {
    return isAndroidApp;
  }
  // ========== Android Callbacks (called from FileDownloadBridge) ==========
  window.DhainakoDownloadCallbacks = {
    onProgress: function (filename, progress) {
      console.log(`DhainakoDownload: Progress - ${filename}: ${progress}%`);
      const cb = callbacks[filename];
      if (cb && cb.onProgress) {
        cb.onProgress(progress);
      }
    },
    onSuccess: function (filename, action) {
      console.log(`DhainakoDownload: Success - ${filename} (${action})`);
      const cb = callbacks[filename];
      if (cb && cb.onSuccess) {
        cb.onSuccess(filename, action);
      }
      // Cleanup callback
      delete callbacks[filename];
    },
    onError: function (filename, error) {
      console.error(`DhainakoDownload: Error - ${filename}: ${error}`);
      const cb = callbacks[filename];
      if (cb && cb.onError) {
        cb.onError(error);
      }
      // Cleanup callback
      delete callbacks[filename];
    },
    onCanceled: function (filename) {
      console.log(`DhainakoDownload: Canceled - ${filename}`);
      const cb = callbacks[filename];
      if (cb && cb.onCanceled) {
        cb.onCanceled();
      }
      // Cleanup callback
      delete callbacks[filename];
    },
  };
  // Public API
  return {
    downloadAndOpen,
    downloadAndShare,
    downloadAndSave,
    cancelDownload,
    isAvailable,

    // Aliases
    open: downloadAndOpen,
    share: downloadAndShare,
    save: downloadAndSave,
    cancel: cancelDownload,
  };
})();
// Export for CommonJS/Module environments
if (typeof module !== "undefined" && module.exports) {
  module.exports = DhainakoDownload;
}
