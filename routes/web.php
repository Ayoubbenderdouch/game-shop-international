<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('product.show');
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('category.show');
Route::post('/set-locale', [HomeController::class, 'setLocale'])->name('set-locale');

// Search and Filter Routes
Route::get('/search', [ShopController::class, 'search'])->name('search');
Route::get('/api/products/filter', [ShopController::class, 'filter'])->name('products.filter');

// Include authentication routes from Breeze
require __DIR__.'/auth.php';

// Protected Routes - Require Authentication
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
    });

    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Favorites Routes
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/{favorite}', [FavoriteController::class, 'remove'])->name('remove');
    });

    // Review Routes
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes - Require Admin Role
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard - Optimized route
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Categories Management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Products Management
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::post('/products/bulk-update', [App\Http\Controllers\Admin\ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('/products/apply-margin', [App\Http\Controllers\Admin\ProductController::class, 'applyMargin'])->name('products.apply-margin');

    // Orders Management
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);

    // Users Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Pricing Rules
    Route::resource('pricing-rules', App\Http\Controllers\Admin\PricingRuleController::class);

    // Settings - UPDATED WITH MISSING ROUTES
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('settings.test-email');
    Route::get('/settings/clear-cache', [App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
        Route::get('/customers', [App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
        Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // API Sync Routes
    Route::prefix('api-sync')->name('api-sync.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ApiSyncController::class, 'index'])->name('index');
        Route::post('/test-connection', [App\Http\Controllers\Admin\ApiSyncController::class, 'testConnection'])->name('test-connection');
        Route::post('/balance', [App\Http\Controllers\Admin\ApiSyncController::class, 'balance'])->name('balance');
        Route::post('/categories', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncCategories'])->name('categories');
        Route::post('/products', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncProducts'])->name('products');
        Route::post('/full-sync', [App\Http\Controllers\Admin\ApiSyncController::class, 'fullSync'])->name('full-sync');
        Route::post('/settings', [App\Http\Controllers\Admin\ApiSyncController::class, 'saveSettings'])->name('settings');
    });
});

// Fallback route - must be last
Route::fallback(function () {
    return view('errors.404');
});
