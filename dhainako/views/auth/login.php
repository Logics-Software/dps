<?php
$title = 'Login';
require __DIR__ . '/../layouts/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-card-header text-center">
            <div class="login-logo-wrapper">
                <span class="login-logo">
                    <img src="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-64.png" alt="PBF Logo" width="56" height="56">
                </span>
            </div>
            <h1 class="login-title mb-1">Login System</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="/login" class="login-form needs-validation" novalidate>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="Masukkan username">
                    <label for="username">Username</label>
                    <div class="invalid-feedback">Username wajib diisi.</div>
                </div>

                <div class="form-floating mb-3 position-relative password-field">
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password">
                        <span class="password-toggle-icon-show"><?= icon('eye', '', 18) ?></span>
                        <span class="password-toggle-icon-hide d-none"><?= icon('eye-slash', '', 18) ?></span>
                    </button>
                    <div class="invalid-feedback">Password wajib diisi.</div>
                </div>

                <div class="login-buttons-wrapper d-flex gap-2 mt-3 mb-2">
                    <button type="submit" class="btn btn-gradient flex-grow-1" id="loginSubmitBtn">
                        <?= icon('login', 'me-2', 20) ?> Login
                    </button>
                    <button type="button" class="btn btn-gradient d-md-none" id="btnMobileBiometric" style="display: none;" title="Login dengan Biometrik">
                        <?= icon('fingerprint', '', 24) ?>
                    </button>
                </div>
            </form>
            
            <!-- Biometric Login Section -->
            <div id="biometricSection" class="text-center mt-3" style="display: none;">
                <button type="button" id="btnBiometricLogin" class="btn btn-outline-primary w-100"  style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M12 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path>
                        <path d="M12 3c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"></path>
                    </svg>
                    Login dengan Biometrik (Fingerprint)
                </button>
                <div id="biometricError" class="alert alert-danger mt-2 mb-0" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/webauthn.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const biometricSection = document.getElementById('biometricSection');
    const btnBiometricLogin = document.getElementById('btnBiometricLogin');
    const btnMobileBiometric = document.getElementById('btnMobileBiometric');
    const biometricError = document.getElementById('biometricError');

    // Check WebAuthn support
    if (!WebAuthnHelper.isSupported()) {
        biometricSection.style.display = 'none';
        return;
    }

    // Check if user has credentials when username changes
    let checkCredentialsTimeout;
    usernameInput.addEventListener('input', function() {
        clearTimeout(checkCredentialsTimeout);
        const username = this.value.trim();
        
        if (username.length < 3) {
            biometricSection.style.display = 'none';
            return;
        }

        checkCredentialsTimeout = setTimeout(async function() {
            try {
                console.log('Checking credentials for username:', username);
                const hasCredentials = await WebAuthnHelper.hasCredentials(username);
                console.log('Credentials check result:', hasCredentials);
                
                if (hasCredentials) {
                    console.log('Showing biometric section');
                    biometricSection.style.display = 'block';
                    biometricError.style.display = 'none';
                } else {
                    console.log('Hiding biometric section - no credentials found');
                    biometricSection.style.display = 'none';
                }
                
                // Update mobile biometric button
                updateMobileBiometricButton();
            } catch (error) {
                console.error('Error checking credentials:', error);
                console.error('Error details:', error.message, error.stack);
                biometricSection.style.display = 'none';
            }
        }, 500);
    });

    // Save username to localStorage when form is submitted successfully
    const loginForm = document.querySelector('.login-form');
    loginForm.addEventListener('submit', function(e) {
        const username = usernameInput.value.trim();
        if (username.length > 0) {
            localStorage.setItem('last_username', username);
        }
    });

    // Load and fill last username in input field (mobile only)
    function loadLastUsername() {
        // Only fill on mobile
        const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
        if (!isMobile) {
            return;
        }

        const lastUsername = localStorage.getItem('last_username');
        const currentUsername = usernameInput.value.trim();
        
        // Only fill if there's a last username and input is empty
        if (lastUsername && lastUsername.length > 0 && currentUsername.length === 0) {
            usernameInput.value = lastUsername;
            // Trigger input event to check for biometric after a short delay
            setTimeout(function() {
                usernameInput.dispatchEvent(new Event('input', { bubbles: true }));
            }, 300);
        }
    }

    // Load last username on page load
    loadLastUsername();

    // Function to update mobile biometric button visibility
    function updateMobileBiometricButton() {
        const username = usernameInput.value.trim();
        const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
        
        if (username.length >= 3 && WebAuthnHelper.isSupported() && isMobile && btnMobileBiometric) {
            WebAuthnHelper.hasCredentials(username).then(function(hasCredentials) {
                if (hasCredentials) {
                    btnMobileBiometric.style.display = 'flex';
                    btnMobileBiometric.style.alignItems = 'center';
                    btnMobileBiometric.style.justifyContent = 'center';
                } else {
                    btnMobileBiometric.style.display = 'none';
                }
            }).catch(function(error) {
                console.error('Error checking credentials for mobile biometric button:', error);
                if (btnMobileBiometric) {
                    btnMobileBiometric.style.display = 'none';
                }
            });
        } else {
            if (btnMobileBiometric) {
                btnMobileBiometric.style.display = 'none';
            }
        }
    }

    // Check mobile biometric button when username changes
    usernameInput.addEventListener('input', function() {
        updateMobileBiometricButton();
    });

    // Check mobile biometric button on window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateMobileBiometricButton, 250);
    });

    // Also check on page load if username is pre-filled
    if (usernameInput.value.trim().length >= 3) {
        setTimeout(async function() {
            const username = usernameInput.value.trim();
            try {
                const hasCredentials = await WebAuthnHelper.hasCredentials(username);
                console.log('Initial credentials check:', hasCredentials);
                if (hasCredentials) {
                    biometricSection.style.display = 'block';
                }
                updateMobileBiometricButton();
            } catch (error) {
                console.error('Initial credentials check error:', error);
            }
        }, 1000);
    }

    // Initial check for mobile biometric button
    setTimeout(function() {
        updateMobileBiometricButton();
    }, 500);

    // Handle mobile biometric button click
    if (btnMobileBiometric) {
        btnMobileBiometric.addEventListener('click', async function() {
            const username = usernameInput.value.trim();
            
            if (!username) {
                showBiometricError('Masukkan username terlebih dahulu');
                usernameInput.focus();
                return;
            }

            btnMobileBiometric.disabled = true;
            btnMobileBiometric.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            biometricError.style.display = 'none';

            try {
                const result = await WebAuthnHelper.authenticateBiometric(username);
                
                if (result.success && result.redirect) {
                    // Save username to localStorage before redirect
                    if (username.length > 0) {
                        localStorage.setItem('last_username', username);
                    }
                    window.location.href = result.redirect;
                } else {
                    showBiometricError(result.error || 'Login biometrik gagal');
                }
            } catch (error) {
                console.error('Biometric login error:', error);
                
                let errorMessage = 'Gagal melakukan login biometrik. ';
                if (error.name === 'NotAllowedError') {
                    errorMessage += 'Autentikasi dibatalkan atau tidak diizinkan.';
                } else if (error.name === 'NotFoundError') {
                    errorMessage += 'Tidak ada credential biometrik ditemukan.';
                } else if (error.name === 'InvalidStateError') {
                    errorMessage += 'Credential sudah digunakan atau tidak valid.';
                } else if (error.message) {
                    errorMessage += error.message;
                } else {
                    errorMessage += 'Pastikan perangkat Anda mendukung biometrik dan sudah dikonfigurasi.';
                }
                
                showBiometricError(errorMessage);
            } finally {
                btnMobileBiometric.disabled = false;
                btnMobileBiometric.innerHTML = '<?= icon('fingerprint', '', 24) ?>';
            }
        });
    }

    // Handle biometric login
    btnBiometricLogin.addEventListener('click', async function() {
        const username = usernameInput.value.trim();
        
        if (!username) {
            showBiometricError('Masukkan username terlebih dahulu');
            usernameInput.focus();
            return;
        }

        btnBiometricLogin.disabled = true;
        btnBiometricLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        biometricError.style.display = 'none';

        try {
            const result = await WebAuthnHelper.authenticateBiometric(username);
            
            if (result.success && result.redirect) {
                // Save username to localStorage before redirect
                if (username.length > 0) {
                    localStorage.setItem('last_username', username);
                }
                window.location.href = result.redirect;
            } else {
                showBiometricError(result.error || 'Login biometrik gagal');
            }
        } catch (error) {
            console.error('Biometric login error:', error);
            
            let errorMessage = 'Gagal melakukan login biometrik. ';
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Autentikasi dibatalkan atau tidak diizinkan.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'Tidak ada credential biometrik ditemukan.';
            } else if (error.name === 'InvalidStateError') {
                errorMessage += 'Credential sudah digunakan atau tidak valid.';
            } else if (error.message) {
                errorMessage += error.message;
            } else {
                errorMessage += 'Pastikan perangkat Anda mendukung biometrik dan sudah dikonfigurasi.';
            }
            
            showBiometricError(errorMessage);
        } finally {
            btnBiometricLogin.disabled = false;
            btnBiometricLogin.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M12 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path><path d="M12 3c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"></path></svg>Login dengan Biometrik (Fingerprint)';
        }
    });

    function showBiometricError(message) {
        biometricError.textContent = message;
        biometricError.style.display = 'block';
        setTimeout(function() {
            biometricError.style.display = 'none';
        }, 5000);
    }
});
</script>

<style>
.divider {
    position: relative;
    text-align: center;
    margin: 1rem 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #dee2e6;
}

.divider-text {
    position: relative;
    background: white;
    padding: 0 0.75rem;
    color: #6c757d;
    font-size: 0.875rem;
}

#btnBiometricLogin {
    display: flex;
    align-items: center;
    justify-content: center;
}

#btnBiometricLogin svg {
    flex-shrink: 0;
}

#biometricError {
    font-size: 0.875rem;
}

.login-buttons-wrapper {
    display: flex;
    align-items: stretch;
}

#btnMobileBiometric {
    display: none;
    min-width: 50px;
    padding: 0.5rem;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

#btnMobileBiometric svg,
#btnMobileBiometric img {
    width: 24px;
    height: 24px;
}

/* Show mobile biometric button only on mobile */
@media (max-width: 767.98px) {
    #btnMobileBiometric {
        display: flex;
    }
}

/* Hide on desktop */
@media (min-width: 768px) {
    #btnMobileBiometric {
        display: none !important;
    }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

