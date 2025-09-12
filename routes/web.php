<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('product.show');
Route::get('/pubg-uc', [ShopController::class, 'pubgUc'])->name('pubg-uc');
Route::post('/set-locale', [HomeController::class, 'setLocale'])->name('set-locale');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/password/reset', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordController::class, 'reset'])->name('password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Cart Routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    });

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Order Routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    });

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // Favorites Routes
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/add', [FavoriteController::class, 'add'])->name('favorites.add');
        Route::delete('/{favorite}', [FavoriteController::class, 'remove'])->name('favorites.remove');
    });

    // Review Routes
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Resource Routes
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('pricing-rules', App\Http\Controllers\Admin\PricingRuleController::class);

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/customers', [App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('reports.customers');
    Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    // API Sync
    Route::get('/api-sync', [App\Http\Controllers\Admin\ApiSyncController::class, 'index'])->name('api-sync.index');
    Route::post('/api-sync/categories', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncCategories'])->name('api-sync.categories');
    Route::post('/api-sync/products', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncProducts'])->name('api-sync.products');
    Route::post('/api-sync/check-balance', [App\Http\Controllers\Admin\ApiSyncController::class, 'checkBalance'])->name('api-sync.balance');

    // Bulk Actions
    Route::post('/products/bulk-update', [App\Http\Controllers\Admin\ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('/products/apply-margin', [App\Http\Controllers\Admin\ProductController::class, 'applyMargin'])->name('products.apply-margin');
    Route::post('/categories/bulk-update', [App\Http\Controllers\Admin\CategoryController::class, 'bulkUpdate'])->name('categories.bulk-update');
});

require __DIR__.'/auth.php';
