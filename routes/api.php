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

// API Routes for AJAX calls
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCount'])->name('api.cart.count');
    Route::post('/favorites/check', [App\Http\Controllers\FavoriteController::class, 'check'])->name('api.favorites.check');
    Route::get('/products/search', [App\Http\Controllers\ShopController::class, 'searchApi'])->name('api.products.search');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
});
