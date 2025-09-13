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

// Authenticated API routes for favorites
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/favorites/check', [App\Http\Controllers\FavoriteController::class, 'check'])->name('api.favorites.check');
});

// Public API routes (if needed for search)
Route::middleware(['web'])->group(function () {
    Route::get('/products/search', [App\Http\Controllers\ShopController::class, 'searchApi'])->name('api.products.search');
});

// Authenticated API routes for notifications
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
});
