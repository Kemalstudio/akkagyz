<?php

use App\Http\Controllers\Api\MobileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/config', [MobileController::class, 'config']);
    Route::get('/products', [MobileController::class, 'products']);
    Route::get('/products/{product}', [MobileController::class, 'product']);
    Route::get('/categories', [MobileController::class, 'categories']);
    Route::get('/filters', [MobileController::class, 'filters']);
    Route::post('/track-order', [MobileController::class, 'trackOrder'])->middleware('throttle:15,1');
    Route::post('/auth/register', [MobileController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/auth/login', [MobileController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/auth/forgot-password', [MobileController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/auth/reset-password', [MobileController::class, 'resetPassword'])->middleware('throttle:10,1');

    Route::middleware('mobile.auth')->group(function () {
        Route::get('/me', [MobileController::class, 'me']);
        Route::patch('/me', [MobileController::class, 'updateMe']);
        Route::patch('/me/password', [MobileController::class, 'updatePassword']);
        Route::post('/auth/logout', [MobileController::class, 'logout']);
        Route::get('/cart', [MobileController::class, 'cart']);
        Route::post('/cart/{product}', [MobileController::class, 'cartUpdate']);
        Route::get('/wishlist', [MobileController::class, 'wishlist']);
        Route::post('/wishlist/{product}', [MobileController::class, 'wishlistToggle']);
        Route::get('/orders', [MobileController::class, 'orders']);
        Route::post('/checkout', [MobileController::class, 'checkout'])->middleware('throttle:10,1');
        Route::get('/orders/{order}', [MobileController::class, 'order']);
        Route::patch('/orders/{order}/cancel', [MobileController::class, 'cancelOrder']);
        Route::post('/orders/{order}/repeat', [MobileController::class, 'repeatOrder']);
        Route::post('/products/{product}/reviews', [MobileController::class, 'review']);
    });
});
