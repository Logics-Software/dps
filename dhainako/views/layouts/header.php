<?php
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = rtrim($config['base_url'], '/');
// Fallback to relative path if base_url is not set correctly
if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
    $baseUrl = '/';
}

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
    <title><?= $title ?? 'PBF System' ?> - PBF System</title>
    <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-32.png">
    <link rel="icon" type="image/png" sizes="64x64" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-64.png">
    <link rel="apple-touch-icon" sizes="128x128" href="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo-128.png">
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

    if (!empty($additionalInlineStyles)):
        $inlineStyles = is_array($additionalInlineStyles) ? $additionalInlineStyles : [$additionalInlineStyles];
        foreach ($inlineStyles as $inlineStyle):
            if (!empty($inlineStyle)):
    ?>
    <style><?= $inlineStyle ?></style>
    <?php
            endif;
        endforeach;
    endif;
    ?>
</head>
<body class="<?= Auth::check() ? 'logged-in' : '' ?>">
    <?php if (Auth::check()): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/dashboard">
                <img src="<?= htmlspecialchars($baseUrl) ?>/assets/images/logo.svg" alt="DPS Logo" width="32" height="32" class="me-2">
                <span>PBF System</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <?php if (Auth::isManajemen()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            User
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUser">
                            <li><a class="dropdown-item" href="/users">Manajemen User</a></li>
                            <li><a class="dropdown-item" href="/login-logs">Data Login Log</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if (Auth::check()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTabel" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Tabel
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownTabel">
                            <li><a class="dropdown-item" href="/tabelpabrik">Tabel Pabrik</a></li>
                            <li><a class="dropdown-item" href="/tabelgolongan">Tabel Golongan</a></li>
                            <?php if (!Auth::isSales()): ?>
                            <li><a class="dropdown-item" href="/tabelaktivitas">Tabel Aktivitas</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMaster" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Master
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMaster">
                            <li><a class="dropdown-item" href="/masterbarang">Master Barang</a></li>
                            <li><a class="dropdown-item" href="/mastercustomer">Master Customer</a></li>
                            <li><a class="dropdown-item" href="/mastersupplier">Master Supplier</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTransaksi" role="#" data-bs-toggle="dropdown" aria-expanded="false">
                            Transaksi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownTransaksi">
                            <li><a class="dropdown-item" href="/orders">Transaksi Order</a></li>
                            <li><a class="dropdown-item" href="/penjualan">Transaksi Penjualan</a></li>
                            <li><a class="dropdown-item" href="/penerimaan">Transaksi Inkaso</a></li>
                        </ul>
                    </li>
                    <?php if (Auth::isSales()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/visits">Kunjungan</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLaporan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Laporan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownLaporan">
                            <li><a class="dropdown-item" href="/laporan/daftar-barang">Laporan Daftar Barang</a></li>
                            <li><a class="dropdown-item" href="/laporan/daftar-stok">Laporan Daftar Stok</a></li>
                            <li><a class="dropdown-item" href="/laporan/daftar-harga">Laporan Daftar Harga Barang</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Order Penjualan</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Faktur Penjualan</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Inkaso</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Omset Penjualan</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php 
                            $currentUser = Auth::user();
                            $userPicture = $currentUser['picture'] ?? null;
                            $picturePath = null;
                            if ($userPicture && file_exists(__DIR__ . '/../../uploads/' . $userPicture)) {
                                $picturePath = $baseUrl . '/uploads/' . htmlspecialchars($userPicture);
                            }
                            ?>
                            <?php if ($picturePath): ?>
                            <img src="<?= $picturePath ?>" alt="Profile" class="rounded-circle me-2 avatar-img avatar-img-sm avatar-border-light">
                            <?php else: ?>
                            <div class="bg-light avatar-placeholder avatar-placeholder-sm avatar-placeholder-light me-2">
                                <span class="text-primary fw-bold avatar-initial-sm">
                                    <?= strtoupper(substr($currentUser['namalengkap'], 0, 1)) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($currentUser['namalengkap']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="px-3 py-2 text-center border-bottom">
                                <?php if ($picturePath): ?>
                                <img src="<?= $picturePath ?>" alt="Profile" class="rounded-circle mb-2 avatar-img avatar-img-lg avatar-border-muted">
                                <?php else: ?>
                                <div class="bg-secondary avatar-placeholder avatar-placeholder-lg mb-2">
                                    <span class="text-white fw-bold avatar-initial-lg">
                                        <?= strtoupper(substr($currentUser['namalengkap'], 0, 1)) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <div class="fw-bold profile-dropdown-name"><?= htmlspecialchars($currentUser['namalengkap']) ?></div>
                                <small class="profile-dropdown-email"><?= htmlspecialchars($currentUser['email']) ?></small>
                            </li>
                            <li><a class="dropdown-item" href="/profile"><?= icon('profile', 'me-2', 16) ?> Edit Profile</a></li>
                            <li><a class="dropdown-item" href="/profile/change-password"><?= icon('key', 'me-2', 16) ?> Ubah Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><?= icon('logout', 'me-2', 16) ?> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <div class="container-fluid content-container">
        <?php 
        $successMessage = Session::flash('success');
        if ($successMessage): 
        ?>
        <div class="alert alert-success alert-dismissible fade show alert-feedback-success" role="alert">
            <span class="alert-feedback-text-success">
                <?= htmlspecialchars($successMessage) ?>
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php 
        $errorMessage = Session::flash('error');
        if ($errorMessage): 
        ?>
        <div class="alert alert-danger alert-dismissible fade show alert-feedback-danger" role="alert">
            <span class="alert-feedback-text-danger">
                <?= htmlspecialchars($errorMessage) ?>
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

