<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PubgUcController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\UserAdminController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Locale switch route
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en'])) { // Add more locales when available
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('checkout.process');

    // PUBG UC
    Route::get('/pubg-uc', [PubgUcController::class, 'index'])->name('pubg-uc.index');
    Route::post('/pubg-uc/charge', [PubgUcController::class, 'charge'])->name('pubg-uc.charge');
    Route::get('/pubg-uc/orders', [PubgUcController::class, 'orders'])->name('pubg-uc.orders');
    Route::get('/pubg-uc/success/{order}', [PubgUcController::class, 'success'])->name('pubg-uc.success');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Add more admin routes as needed
});
