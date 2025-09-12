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
Route::get('/pubg-uc', [ShopController::class, 'pubgUc'])->name('pubg-uc');
Route::post('/set-locale', [HomeController::class, 'setLocale'])->name('set-locale');

// Search and Filter Routes
Route::get('/search', [ShopController::class, 'search'])->name('search');
Route::get('/api/products/filter', [ShopController::class, 'filter'])->name('products.filter');

// Authentication Routes (Laravel Breeze handles these via auth.php)
require __DIR__.'/auth.php';

// Dashboard redirect for authenticated users
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('profile.edit');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
        Route::post('/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('validate-coupon');
    });

    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/track', [OrderController::class, 'track'])->name('track');
    });

    // Favorites/Wishlist Routes
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/{favorite}', [FavoriteController::class, 'remove'])->name('remove');
        Route::post('/move-to-cart/{favorite}', [FavoriteController::class, 'moveToCart'])->name('move-to-cart');
    });

    // Review Routes
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Resource Routes
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('pricing-rules', App\Http\Controllers\Admin\PricingRuleController::class);
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/settings/test-api', [App\Http\Controllers\Admin\SettingController::class, 'testApi'])->name('settings.test-api');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
        Route::get('/customers', [App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
        Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // API Sync with LikeCard
    Route::prefix('api-sync')->name('api-sync.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ApiSyncController::class, 'index'])->name('index');
        Route::post('/categories', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncCategories'])->name('categories');
        Route::post('/products', [App\Http\Controllers\Admin\ApiSyncController::class, 'syncProducts'])->name('products');
        Route::post('/check-balance', [App\Http\Controllers\Admin\ApiSyncController::class, 'checkBalance'])->name('balance');
        Route::post('/test-connection', [App\Http\Controllers\Admin\ApiSyncController::class, 'testConnection'])->name('test');
        Route::get('/logs', [App\Http\Controllers\Admin\ApiSyncController::class, 'logs'])->name('logs');
    });

    // Bulk Actions
    Route::post('/products/bulk-update', [App\Http\Controllers\Admin\ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('/products/bulk-delete', [App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('/products/apply-margin', [App\Http\Controllers\Admin\ProductController::class, 'applyMargin'])->name('products.apply-margin');
    Route::post('/categories/bulk-update', [App\Http\Controllers\Admin\CategoryController::class, 'bulkUpdate'])->name('categories.bulk-update');
    Route::post('/orders/bulk-update', [App\Http\Controllers\Admin\OrderController::class, 'bulkUpdate'])->name('orders.bulk-update');
});

// API Routes for AJAX calls
Route::middleware(['auth', 'throttle:api'])->prefix('api')->name('api.')->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
    Route::post('/favorites/check', [FavoriteController::class, 'check'])->name('favorites.check');
    Route::get('/products/search', [ShopController::class, 'searchApi'])->name('products.search');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// Webhook Routes for Payment Gateways
// Route::prefix('webhooks')->name('webhooks.')->group(function () {
//     Route::post('/stripe', [App\Http\Controllers\WebhookController::class, 'handleStripe'])->name('stripe');
//     Route::post('/paypal', [App\Http\Controllers\WebhookController::class, 'handlePaypal'])->name('paypal');
//     Route::post('/likecard', [App\Http\Controllers\WebhookController::class, 'handleLikecard'])->name('likecard');
// });

// Fallback Route
Route::fallback(function () {
    return view('errors.404');
});
