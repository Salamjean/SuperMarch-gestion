<?php

use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Magasinier\MagasinierCategoryController;
use App\Http\Controllers\Magasinier\MagasinierDashboardController;
use App\Http\Controllers\Magasinier\MagasinierProductController;
use Illuminate\Support\Facades\Route;

// Home → login directement
Route::get('/', [AdminAuthController::class, 'showLogin'])->name('home');

// Auth (unique login page)
Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::delete('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Admin protected routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/employees/blocked', [EmployeeController::class, 'blocked'])->name('employees.blocked');
    Route::post('/employees/{id}/unblock', [EmployeeController::class, 'unblock'])->name('employees.unblock');
    Route::resource('employees', EmployeeController::class);

    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers',  SupplierController::class);
    Route::get('/products/check-barcode', [ProductController::class, 'checkBarcode'])->name('products.check-barcode');
    Route::resource('products',   ProductController::class);
    Route::get('/restock-requests', [AdminDashboardController::class, 'restockRequestsIndex'])->name('restock-requests.index');
    Route::post('/stock/request/{id}/resolve', [AdminDashboardController::class, 'resolveRestock'])->name('stock.resolve');

    // Nouvelles routes d'administration
    // 1. Historique des Ventes & Facturation
    Route::get('/sales', [App\Http\Controllers\Admin\AdminSaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{id}', [App\Http\Controllers\Admin\AdminSaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{id}/refund', [App\Http\Controllers\Admin\AdminSaleController::class, 'refund'])->name('sales.refund');

    // 2. Sessions de Caisse
    Route::get('/cash-sessions', [App\Http\Controllers\Admin\AdminCashSessionController::class, 'index'])->name('cash-sessions.index');
    Route::get('/cash-sessions/{id}', [App\Http\Controllers\Admin\AdminCashSessionController::class, 'show'])->name('cash-sessions.show');

    // 3. Gestion Clientèle (Fidélité & Crédits)
    Route::resource('customers', App\Http\Controllers\Admin\AdminCustomerController::class);
    Route::post('/customers/{id}/adjust-points', [App\Http\Controllers\Admin\AdminCustomerController::class, 'adjustPoints'])->name('customers.adjust-points');
    Route::post('/customers/{id}/pay-debt', [App\Http\Controllers\Admin\AdminCustomerController::class, 'payDebt'])->name('customers.pay-debt');
});

// Employee protected routes
Route::prefix('employee')->name('employee.')->middleware('employee')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::post('/pos/checkout', [EmployeeDashboardController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/sync', [EmployeeDashboardController::class, 'syncSales'])->name('pos.sync');
    Route::post('/stock/request', [EmployeeDashboardController::class, 'requestRestock'])->name('stock.request');

    // Sessions de caisse
    Route::post('/pos/session/open', [EmployeeDashboardController::class, 'openSession'])->name('pos.session.open');
    Route::post('/pos/session/close', [EmployeeDashboardController::class, 'closeSession'])->name('pos.session.close');

    // Gestion Clientèle
    Route::get('/customers/search', [EmployeeDashboardController::class, 'searchCustomers'])->name('customers.search');
    Route::post('/customers', [EmployeeDashboardController::class, 'storeCustomer'])->name('customers.store');

    // Annulations & Retours
    Route::post('/pos/sales/{id}/refund', [EmployeeDashboardController::class, 'refundSale'])->name('pos.sales.refund');

    // Gestion Profil
    Route::post('/profile/update', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Magasinier protected routes
Route::prefix('magasinier')->name('magasinier.')->middleware('magasinier')->group(function () {
    Route::get('/dashboard', [MagasinierDashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', MagasinierCategoryController::class);

    // Produits
    Route::get('/products/check-barcode', [MagasinierProductController::class, 'checkBarcode'])->name('products.check-barcode');
    Route::resource('products', MagasinierProductController::class);

    // Fournisseurs
    Route::resource('suppliers', \App\Http\Controllers\Magasinier\MagasinierSupplierController::class);

    // Demandes de réapprovisionnement
    Route::get('/restock-requests', [MagasinierDashboardController::class, 'restockRequestsIndex'])->name('restock-requests.index');
    Route::post('/stock/request/{id}/resolve', [MagasinierDashboardController::class, 'resolveRestock'])->name('stock.resolve');

    // Profil
    Route::get('/profile', [MagasinierDashboardController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile/update', [MagasinierDashboardController::class, 'updateProfile'])->name('profile.update');
});
