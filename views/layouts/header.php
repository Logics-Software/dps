<?php
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
// Fallback to relative path if base_url is not set correctly
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}
// Define BASE_URL constant for compatibility
define('BASE_URL', $baseUrl);

// Helper function to display icon
if (!function_exists('icon')) {
    function icon($name, $class = '', $size = 16) {
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = rtrim($config['base_url'], '/');
        if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
            $baseUrl = '/';
        }
        $iconPath = $baseUrl . '/assets/icons/' . $name . '.svg';
        $classes = trim('icon-inline ' . $class);
        $classAttr = ' class="' . htmlspecialchars($classes) . '"';
        return '<img src="' . htmlspecialchars($iconPath) . '" alt="' . htmlspecialchars($name) . '" width="' . $size . '" height="' . $size . '"' . $classAttr . '>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'DPS Online' ?> - DPS Online</title>
    <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-32.png">
    <link rel="icon" type="image/png" sizes="64x64" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-64.png">
    <link rel="apple-touch-icon" sizes="128x128" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-128.png">
    
    <!-- Font Preloading for Chrome/Edge - Prioritize bold for brand text -->
    <link rel="preload" href="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/fonts/inter/inter-bold.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/fonts/inter/inter-semibold.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/fonts/inter/inter-medium.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/fonts/inter/inter-regular.woff2') ?>" as="font" type="font/woff2" crossorigin>
    
    <link href="<?= htmlspecialchars($baseUrl) ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($baseUrl) ?>/assets/css/style.css" rel="stylesheet">
    <?php if (!empty($additionalStyles)):
        $styles = is_array($additionalStyles) ? $additionalStyles : [$additionalStyles];
        foreach ($styles as $styleHref):
            if (!empty($styleHref)):
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($styleHref) ?>">
    <?php
            endif;
        endforeach;
    endif;
    ?>
</head>
<body class="<?= Auth::check() ? 'has-header' : '' ?>"><?php
// Get user data if logged in
$currentUser = Auth::check() ? Auth::user() : null;
$appConfig = require __DIR__ . '/../../config/app.php';
if (Auth::check() && $currentUser): ?><header class="app-header">
        <nav class="navbar">
            <div class="container-fluid">
                <div class="header-content">
                    <!-- Logo Section -->
                    <div class="header-logo-section">
                        <a href="/dashboard" class="d-flex align-items-center text-decoration-none">
                            <img src="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo.png" alt="Logo" class="header-logo">
                        </a>
                        <h1 class="header-app-name"><?= htmlspecialchars($appConfig['app_name']) ?></h1>
                    </div>

                    <!-- Hamburger Menu Button (Mobile Only) -->
                    <button class="hamburger-menu-toggle" type="button" id="hamburgerMenuToggle" aria-label="Toggle menu" aria-expanded="false">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>

                    <!-- Navigation Menu -->
                    <nav class="header-nav-menu" id="headerNavMenu">
                        <a href="/dashboard" class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/dashboard' ? 'active' : '' ?>">Dashboard</a>
                        
                        <?php if (Auth::check() && Auth::isSales()): ?>
                        <a href="/visits" class="nav-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/visits') !== false ? 'active' : '' ?>">Kunjungan</a>
                        <?php endif; ?>
                        
                        <?php if (Auth::isManajemen()): ?>
                        <div class="nav-dropdown">
                            <button class="nav-dropdown-toggle" type="button" aria-expanded="false">
                                User
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 0.25rem;">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown-menu">
                                <a href="/users" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/users') !== false ? 'active' : '' ?>">Manajemen User</a>
                                <a href="/login-logs" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/login-logs') !== false ? 'active' : '' ?>">Login Logs</a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::check() && in_array($currentUser['role'] ?? '', ['admin', 'manajemen', 'operator', 'sales'])): ?>
                        <div class="nav-dropdown">
                            <button class="nav-dropdown-toggle" type="button" aria-expanded="false">
                                Master
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 0.25rem;">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown-menu">
                                <a href="/masterbarang" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/masterbarang') !== false ? 'active' : '' ?>">Master Barang</a>
                                <a href="/mastercustomer" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/mastercustomer') !== false ? 'active' : '' ?>">Master Customer</a>
                                <?php if (Auth::check() && in_array($currentUser['role'] ?? '', ['admin', 'manajemen', 'operator', 'sales'])): ?>
                                <a href="/mastersales" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/mastersales') !== false ? 'active' : '' ?>">Master Sales</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::check() && (in_array($currentUser['role'] ?? '', ['admin', 'manajemen', 'operator', 'sales']))): ?>
                        <div class="nav-dropdown">
                            <button class="nav-dropdown-toggle" type="button" aria-expanded="false">
                                Tabel
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 0.25rem;">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown-menu">
                                <?php if (Auth::check() && in_array($currentUser['role'] ?? '', ['admin', 'manajemen', 'operator', 'sales'])): ?>
                                <a href="/tabelpabrik" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/tabelpabrik') !== false ? 'active' : '' ?>">Tabel Pabrik</a>
                                <a href="/tabelgolongan" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/tabelgolongan') !== false ? 'active' : '' ?>">Tabel Golongan</a>
                                <?php endif; ?>
                                <?php if (Auth::check() && in_array($currentUser['role'] ?? '', ['admin', 'manajemen', 'operator'])): ?>
                                <a href="/tabelaktivitas" class="nav-dropdown-item <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/tabelaktivitas') !== false ? 'active' : '' ?>">Tabel Aktivitas</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </nav>

                    <!-- User Profile Section -->
                    <div class="header-user-profile">
                        <div class="user-profile-dropdown" id="userProfileDropdown">
                            <button class="user-profile-toggle" type="button" id="userProfileToggle" aria-expanded="false">
                                <div class="user-avatar">
                                    <?php if (!empty($currentUser['picture'])): ?>
                                        <?php 
                                        $config = require __DIR__ . '/../../config/app.php';
                                        $pictureUrl = $baseUrl . $config['upload_url'] . htmlspecialchars($currentUser['picture']);
                                        $fallbackText = strtoupper(substr($currentUser['username'] ?? 'U', 0, 1));
                                        ?>
                                        <img src="<?= $pictureUrl ?>" alt="<?= htmlspecialchars($currentUser['namalengkap'] ?? $currentUser['username'] ?? 'User') ?>" class="user-avatar-img" data-fallback="<?= htmlspecialchars($fallbackText) ?>" onerror="this.style.display='none'; if(!this.parentElement.querySelector('.avatar-fallback')) { var span = document.createElement('span'); span.className='avatar-fallback'; span.textContent=this.getAttribute('data-fallback'); this.parentElement.appendChild(span); }">
                                    <?php else: ?>
                                        <span class="avatar-fallback"><?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="user-name"><?= htmlspecialchars($currentUser['namalengkap'] ?? $currentUser['username'] ?? 'User') ?></span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 0.25rem;">
                                    <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="user-dropdown-menu">
                                <div class="dropdown-header mb-2">
                                    <p class="dropdown-user-name"><?= htmlspecialchars($currentUser['namalengkap'] ?? $currentUser['username'] ?? 'User') ?></p>
                                    <?php if (!empty($currentUser['email'])): ?>
                                        <p class="dropdown-user-email"><?= htmlspecialchars($currentUser['email']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="/profile" class="dropdown-item">Profile</a>
                                <a href="/profile/change-password" class="dropdown-item">Ubah Password</a>
                                <a href="/settings" class="dropdown-item">Settings</a>
                                <div class="dropdown-divider"></div>
                                <a href="/logout" class="dropdown-item danger">
                                    <?= icon('logout', 'me-2', 16) ?> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
    // Toggle user profile dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('userProfileDropdown');
        const toggle = document.getElementById('userProfileToggle');
        
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Hamburger menu toggle
        const hamburgerToggle = document.getElementById('hamburgerMenuToggle');
        const navMenu = document.getElementById('headerNavMenu');
        
        if (hamburgerToggle && navMenu) {
            hamburgerToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = hamburgerToggle.getAttribute('aria-expanded') === 'true';
                hamburgerToggle.setAttribute('aria-expanded', !isExpanded);
                hamburgerToggle.classList.toggle('active');
                navMenu.classList.toggle('show');
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!hamburgerToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    hamburgerToggle.setAttribute('aria-expanded', 'false');
                    hamburgerToggle.classList.remove('active');
                    navMenu.classList.remove('show');
                }
            });

            // Close mobile menu when clicking on a nav link
            const navLinks = navMenu.querySelectorAll('.nav-link, .nav-dropdown-item');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    hamburgerToggle.setAttribute('aria-expanded', 'false');
                    hamburgerToggle.classList.remove('active');
                    navMenu.classList.remove('show');
                });
            });
        }

        // Handle dropdown menus
        const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
        dropdownToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.closest('.nav-dropdown');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Close all other dropdowns
                dropdownToggles.forEach(function(otherToggle) {
                    if (otherToggle !== toggle) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        otherToggle.closest('.nav-dropdown').classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                this.setAttribute('aria-expanded', !isExpanded);
                dropdown.classList.toggle('show', !isExpanded);
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-dropdown')) {
                dropdownToggles.forEach(function(toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.closest('.nav-dropdown').classList.remove('show');
                });
            }
        });
    });
    </script><?php endif; ?><?php require __DIR__ . '/../partials/alerts.php'; ?>

