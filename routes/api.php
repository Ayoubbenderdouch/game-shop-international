<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Cart count endpoint - handles both authenticated and non-authenticated users
Route::middleware(['web'])->get('/cart/count', function() {
    if (auth()->check()) {
        $count = auth()->user()->cartItems()->sum('quantity');
        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    }

    // Return 0 for non-authenticated users instead of 401
    return response()->json([
        'count' => 0,
        'success' => true
    ]);
})->name('api.cart.count');

// Authenticated API routes for cart
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('api.cart.add');
    Route::patch('/cart/update/{cartItem}', [App\Http\Controllers\CartController::class, 'update'])->name('api.cart.update');
    Route::delete('/cart/remove/{cartItem}', [App\Http\Controllers\CartController::class, 'remove'])->name('api.cart.remove');
});

// Authenticated API routes for favorites
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/favorites/check', [App\Http\Controllers\FavoriteController::class, 'check'])->name('api.favorites.check');
    Route::get('/favorites/count', function() {
        $count = auth()->user()->favorites()->count();
        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    })->name('api.favorites.count');
    Route::post('/favorites/toggle/{product}', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('api.favorites.toggle');
});

// Public API routes (if needed for search)
Route::middleware(['web'])->group(function () {
    Route::get('/products/search', [App\Http\Controllers\ShopController::class, 'searchApi'])->name('api.products.search');
    Route::get('/products/filter', [App\Http\Controllers\ShopController::class, 'filter'])->name('api.products.filter');
});

// Authenticated API routes for notifications
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
});
