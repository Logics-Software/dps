// Custom JavaScript for App

// Auto-hide alerts after 5 seconds
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
});

// Custom Confirmation Modal Functions
function showConfirmModal(options) {
  const modal = new bootstrap.Modal(document.getElementById("confirmModal"));
  const modalTitle = document.getElementById("confirmModalLabel");
  const modalBody = document.getElementById("confirmModalBody");
  const modalBtn = document.getElementById("confirmModalBtn");

  // Set title
  modalTitle.textContent = options.title || "Konfirmasi";

  // Set message
  modalBody.innerHTML = options.message || "Apakah Anda yakin?";

  // Set button text and class
  modalBtn.textContent = options.buttonText || "Konfirmasi";
  modalBtn.className = "btn " + (options.buttonClass || "btn-danger");

  // Remove previous event listeners
  const newBtn = modalBtn.cloneNode(true);
  modalBtn.parentNode.replaceChild(newBtn, modalBtn);

  // Set new event listener
  document
    .getElementById("confirmModalBtn")
    .addEventListener("click", function () {
      if (options.onConfirm) {
        options.onConfirm();
      }
      modal.hide();
    });

  modal.show();
}

function showAlert(options) {
  showConfirmModal({
    title: options.title || "Informasi",
    message: options.message || "",
    buttonText: options.buttonText || "OK",
    buttonClass: options.buttonClass || "btn-primary",
    onConfirm: options.onConfirm,
  });
}

// Confirm delete action
function confirmDelete(message, url) {
  showConfirmModal({
    title: "Konfirmasi Hapus",
    message: message || "Apakah Anda yakin ingin menghapus data ini?",
    buttonText: "Hapus",
    buttonClass: "btn-danger",
    onConfirm: function () {
      if (url) {
        window.location.href = url;
      }
    },
  });
  return false;
}

// Confirm save action
function confirmSave(message, callback) {
  showConfirmModal({
    title: "Konfirmasi Simpan",
    message: message || "Apakah Anda yakin ingin menyimpan data ini?",
    buttonText: "Simpan",
    buttonClass: "btn-primary",
    onConfirm: function () {
      if (callback) {
        callback();
      }
    },
  });
  return false;
}

// General confirmation
function confirmAction(title, message, buttonText, buttonClass, callback) {
  showConfirmModal({
    title: title || "Konfirmasi",
    message: message || "Apakah Anda yakin?",
    buttonText: buttonText || "Konfirmasi",
    buttonClass: buttonClass || "btn-primary",
    onConfirm: function () {
      if (callback) {
        callback();
      }
    },
  });
  return false;
}

// Form validation
document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll('form[method="POST"]');
  forms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add("was-validated");
    });
  });
});

// Image preview for file inputs
document.addEventListener("DOMContentLoaded", function () {
  const fileInputs = document.querySelectorAll(
    'input[type="file"][accept*="image"]'
  );
  fileInputs.forEach(function (input) {
    input.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        // Validate file size (2MB)
        if (file.size > 2097152) {
          alert("Ukuran file terlalu besar. Maksimal 2MB.");
          input.value = "";
          return;
        }

        // Validate file type
        const validTypes = [
          "image/jpeg",
          "image/jpg",
          "image/png",
          "image/gif",
        ];
        if (!validTypes.includes(file.type)) {
          alert("Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.");
          input.value = "";
          return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
          // Find or create preview container
          let previewContainer = document.getElementById("picture-preview");
          if (!previewContainer) {
            previewContainer = document.createElement("div");
            previewContainer.id = "picture-preview";
            previewContainer.className = "mt-2";
            input.parentElement.appendChild(previewContainer);
          }

          // Clear previous preview
          previewContainer.innerHTML = "";

          // Create preview image
          const preview = document.createElement("img");
          preview.className = "img-thumbnail rounded";
          preview.style.maxWidth = "200px";
          preview.style.height = "auto";
          preview.style.border = "2px solid #dee2e6";
          preview.src = e.target.result;
          preview.alt = "Preview";

          previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
      } else {
        // Clear preview if no file selected
        const previewContainer = document.getElementById("picture-preview");
        if (previewContainer) {
          previewContainer.innerHTML = "";
        }
      }
    });
  });
});

function initPasswordToggles() {
  const toggles = document.querySelectorAll(".password-toggle");
  toggles.forEach(function (btn) {
    const targetId = btn.getAttribute("data-target");
    if (!targetId) {
      return;
    }
    const input = document.getElementById(targetId);
    if (!input) {
      return;
    }

    btn.addEventListener("click", function () {
      const showIcon = btn.querySelector(".password-toggle-icon-show");
      const hideIcon = btn.querySelector(".password-toggle-icon-hide");

      if (input.type === "password") {
        input.type = "text";
        btn.setAttribute("aria-label", "Sembunyikan password");
        if (showIcon) {
          showIcon.classList.add("d-none");
        }
        if (hideIcon) {
          hideIcon.classList.remove("d-none");
        }
      } else {
        input.type = "password";
        btn.setAttribute("aria-label", "Tampilkan password");
        if (showIcon) {
          showIcon.classList.remove("d-none");
        }
        if (hideIcon) {
          hideIcon.classList.add("d-none");
        }
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", initPasswordToggles);

// Enhance mobile navigation interactions
document.addEventListener("DOMContentLoaded", function () {
  const navbarCollapseEl = document.getElementById("navbarNav");
  if (!navbarCollapseEl || typeof bootstrap === "undefined") {
    return;
  }

  const mobileBreakpoint = window.matchMedia("(max-width: 991.98px)");

  function ensureCollapseInstance() {
    let instance = bootstrap.Collapse.getInstance(navbarCollapseEl);
    if (!instance) {
      instance = new bootstrap.Collapse(navbarCollapseEl, { toggle: false });
    }
    return instance;
  }

  function shouldAutoCollapse() {
    return mobileBreakpoint.matches;
  }

  function collapseIfNeeded() {
    if (!shouldAutoCollapse()) {
      return;
    }
    const instance = ensureCollapseInstance();
    if (navbarCollapseEl.classList.contains("show")) {
      instance.hide();
    }
  }

  const collapseTargets = navbarCollapseEl.querySelectorAll(
    ".nav-link:not(.dropdown-toggle), .dropdown-item"
  );

  collapseTargets.forEach(function (target) {
    target.addEventListener("click", function () {
      collapseIfNeeded();
    });
  });

  mobileBreakpoint.addEventListener("change", function (event) {
    if (!event.matches) {
      const instance = bootstrap.Collapse.getInstance(navbarCollapseEl);
      if (instance && !navbarCollapseEl.classList.contains("show")) {
        instance.show();
      }
      navbarCollapseEl.style.height = "";
    }
  });
});

// Show/hide kodesales field based on role
document.addEventListener("DOMContentLoaded", function () {
  const roleSelect = document.getElementById("role");
  const kodesalesWrapper = document.getElementById("kodesales-wrapper");

  if (roleSelect && kodesalesWrapper) {
    function toggleKodesales() {
      if (roleSelect.value === "sales") {
        kodesalesWrapper.style.display = "block";
      } else {
        kodesalesWrapper.style.display = "none";
        const kodesalesField = document.getElementById("kodesales");
        if (kodesalesField) {
          kodesalesField.value = "";
        }
      }
    }

    roleSelect.addEventListener("change", toggleKodesales);
    toggleKodesales(); // Initial check
  }
});
