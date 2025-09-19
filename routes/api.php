<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\XenditWebhookController;

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

// Mobile Authentication Routes
Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'user'])->middleware('auth:sanctum')->name('user');
});

// Xendit API Routes
Route::prefix('xendit')->name('api.xendit.')->group(function () {
    // Webhook route (no auth required, skip CSRF)
    Route::post('/webhook', [XenditWebhookController::class, 'handleWebhook'])
        ->middleware([\App\Http\Middleware\XenditWebhookMiddleware::class])
        ->name('webhook');

    // Payment routes (auth required - supports both web session and API token)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/auctions/{auction}/payment', [XenditPaymentController::class, 'showPaymentPage'])->name('payment.show-page');
        Route::post('/auctions/{auction}/payment', [XenditPaymentController::class, 'createPaymentLink'])->name('payment.create');
        Route::get('/payments/{payment}/status', [XenditPaymentController::class, 'getPaymentStatus'])->name('payment.status');
        Route::get('/payments/{payment}', [XenditPaymentController::class, 'showPayment'])->name('payment.show');
        Route::post('/payments/{payment}/expire', [XenditPaymentController::class, 'expirePayment'])->name('payment.expire');
        Route::get('/payment-methods', [XenditPaymentController::class, 'getPaymentMethods'])->name('payment.methods');
    });
});
