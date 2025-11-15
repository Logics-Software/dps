<?php
// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/controllers/api/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

// Start session
Session::start();

// Initialize router
$router = new Router();

// Root route - handled in Router dispatch

// Auth routes
$router->get('/login', 'AuthController', 'login');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');

// WebAuthn API routes
$router->post('/api/webauthn/registration/start', 'ApiWebAuthnController', 'registrationStart');
$router->post('/api/webauthn/registration/complete', 'ApiWebAuthnController', 'registrationComplete');
$router->post('/api/webauthn/authentication/start', 'ApiWebAuthnController', 'authenticationStart');
$router->post('/api/webauthn/authentication/complete', 'ApiWebAuthnController', 'authenticationComplete');
$router->get('/api/webauthn/credentials', 'ApiWebAuthnController', 'listCredentials');
$router->post('/api/webauthn/credentials/delete', 'ApiWebAuthnController', 'deleteCredential');

// Dashboard routes
$router->get('/dashboard', 'DashboardController', 'index');

// Login Log routes (admin/manajemen only)
$router->get('/login-logs', 'LoginLogController', 'index');

// User management routes (admin/manajemen only)
$router->get('/users', 'UserController', 'index');
$router->get('/users/create', 'UserController', 'create');
$router->post('/users/create', 'UserController', 'create');
$router->get('/users/edit/{id}', 'UserController', 'edit');
$router->post('/users/edit/{id}', 'UserController', 'edit');
$router->get('/users/delete/{id}', 'UserController', 'delete');

// Profile routes
$router->get('/profile', 'ProfileController', 'index');
$router->post('/profile', 'ProfileController', 'update');
$router->get('/profile/change-password', 'ProfileController', 'changePassword');
$router->post('/profile/change-password', 'ProfileController', 'changePassword');
$router->get('/settings', 'ProfileController', 'settings');

// Master Barang routes
$router->get('/masterbarang', 'MasterbarangController', 'index');
$router->get('/masterbarang/view/{id}', 'MasterbarangController', 'show');
$router->get('/masterbarang/edit/{id}', 'MasterbarangController', 'edit');
$router->post('/masterbarang/edit/{id}', 'MasterbarangController', 'edit');

// Master Customer routes
$router->get('/mastercustomer', 'MastercustomerController', 'index');
$router->get('/mastercustomer/map', 'MastercustomerController', 'map');
$router->get('/mastercustomer/edit/{id}', 'MastercustomerController', 'edit');
$router->post('/mastercustomer/edit/{id}', 'MastercustomerController', 'edit');
$router->post('/mastercustomer/{id}/coordinates', 'MastercustomerController', 'updateCoordinates');

// Master Supplier routes
$router->get('/mastersupplier', 'MastersupplierController', 'index');

// Tabel Aktivitas routes
$router->get('/tabelaktivitas', 'TabelaktivitasController', 'index');
$router->get('/tabelaktivitas/create', 'TabelaktivitasController', 'create');
$router->post('/tabelaktivitas/create', 'TabelaktivitasController', 'create');
$router->get('/tabelaktivitas/edit/{id}', 'TabelaktivitasController', 'edit');
$router->post('/tabelaktivitas/edit/{id}', 'TabelaktivitasController', 'edit');
$router->get('/tabelaktivitas/delete/{id}', 'TabelaktivitasController', 'delete');

// Tabel Golongan routes
$router->get('/tabelgolongan', 'TabelgolonganController', 'index');

// Tabel Pabrik routes
$router->get('/tabelpabrik', 'TabelpabrikController', 'index');

// Visit routes (sales only)
$router->get('/visits', 'VisitController', 'index');
$router->get('/visits/check-in', 'VisitController', 'checkin');
$router->post('/visits/check-in', 'VisitController', 'checkin');
$router->get('/visits/checkout/{id}', 'VisitController', 'checkout');
$router->post('/visits/checkout/{id}', 'VisitController', 'checkout');
$router->get('/visits/nearest-customers', 'VisitController', 'nearestCustomers');
$router->post('/visits/customer/{id}/coordinates', 'VisitController', 'updateCustomerCoordinates');
$router->get('/visits/{id}/files', 'VisitController', 'getVisitFiles');
$router->post('/visits/{id}/activities', 'VisitController', 'createActivity');

// API routes (no authentication required - for VB bridging)
$router->get('/api/users', 'ApiController', 'users');
$router->post('/api/users', 'ApiController', 'users');
$router->put('/api/users', 'ApiController', 'users');
$router->delete('/api/users', 'ApiController', 'users');

// API Mastersales routes (no authentication required)
$router->get('/api/mastersales', 'ApiMastersalesController', 'index');
$router->post('/api/mastersales', 'ApiMastersalesController', 'index');
$router->put('/api/mastersales', 'ApiMastersalesController', 'index');
$router->delete('/api/mastersales', 'ApiMastersalesController', 'index');

// API Tabelpabrik routes (no authentication required)
$router->get('/api/tabelpabrik', 'ApiTabelpabrikController', 'index');
$router->post('/api/tabelpabrik', 'ApiTabelpabrikController', 'index');
$router->put('/api/tabelpabrik', 'ApiTabelpabrikController', 'index');
$router->delete('/api/tabelpabrik', 'ApiTabelpabrikController', 'index');

// API Tabelgolongan routes (no authentication required)
$router->get('/api/tabelgolongan', 'ApiTabelgolonganController', 'index');
$router->post('/api/tabelgolongan', 'ApiTabelgolonganController', 'index');
$router->put('/api/tabelgolongan', 'ApiTabelgolonganController', 'index');
$router->delete('/api/tabelgolongan', 'ApiTabelgolonganController', 'index');

// API Mastersupplier routes (no authentication required)
$router->get('/api/mastersupplier', 'ApiMastersupplierController', 'index');
$router->post('/api/mastersupplier', 'ApiMastersupplierController', 'index');
$router->put('/api/mastersupplier', 'ApiMastersupplierController', 'index');
$router->delete('/api/mastersupplier', 'ApiMastersupplierController', 'index');

// API Masterbarang routes (no authentication required)
$router->get('/api/masterbarang', 'ApiMasterbarangController', 'index');
$router->post('/api/masterbarang', 'ApiMasterbarangController', 'index');
$router->put('/api/masterbarang', 'ApiMasterbarangController', 'index');
$router->delete('/api/masterbarang', 'ApiMasterbarangController', 'index');

// API Penjualan routes
$router->get('/api/penjualan', 'ApiPenjualanController', 'index');
$router->post('/api/penjualan', 'ApiPenjualanController', 'index');
$router->put('/api/penjualan', 'ApiPenjualanController', 'index');
$router->patch('/api/penjualan', 'ApiPenjualanController', 'index');
$router->delete('/api/penjualan', 'ApiPenjualanController', 'index');

// API Penerimaan routes
$router->get('/api/penerimaan', 'ApiPenerimaanController', 'index');
$router->post('/api/penerimaan', 'ApiPenerimaanController', 'index');
$router->put('/api/penerimaan', 'ApiPenerimaanController', 'index');
$router->patch('/api/penerimaan', 'ApiPenerimaanController', 'index');
$router->delete('/api/penerimaan', 'ApiPenerimaanController', 'index');

// API Mastercustomer routes (no authentication required)
$router->get('/api/mastercustomer', 'ApiMastercustomerController', 'index');
$router->post('/api/mastercustomer', 'ApiMastercustomerController', 'index');
$router->put('/api/mastercustomer', 'ApiMastercustomerController', 'index');
$router->delete('/api/mastercustomer', 'ApiMastercustomerController', 'index');

// Dispatch
$router->dispatch();

