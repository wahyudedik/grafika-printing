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
| API routes dengan versioning (/api/v1/).
|
| Struktur:
|   /api/v1/*           — V1 routes (canonical, semua routes baru)
|   /api/mobile/*       — DEPRECATED → redirect ke /api/v1/mobile/*
|   /api/xendit/*       — DEPRECATED → redirect ke /api/v1/xendit/* (kecuali webhook)
|   /api/xendit/webhook — TETAP di path ini (Xendit tidak support custom path)
|
| Rate Limiting:
|   - Default: 60 requests per minute (api limiter) — diterapkan ke semua v1 routes
|   - Auth: 5 requests per minute (auth limiter) — diterapkan ke login & register
|   - Webhook: 100 requests per minute (webhook limiter) — diterapkan ke Xendit webhook
|
*/

// =============================================================================
// Xendit Webhook — TIDAK BOLEH DI-REDIRECT
// Xendit webhook URL sudah dikonfigurasi di dashboard Xendit.
// Path harus tetap /api/xendit/webhook (tanpa v1 prefix).
// =============================================================================
Route::prefix('xendit')->name('api.webhook.')->group(function () {
    Route::post('/webhook', [XenditWebhookController::class, 'handleWebhook'])
        ->middleware([\App\Http\Middleware\XenditWebhookMiddleware::class, 'throttle:webhook'])
        ->name('xendit');
});

// =============================================================================
// V1 Routes — Semua routes baru di bawah /api/v1/
// =============================================================================
Route::prefix('v1')->middleware('throttle:api')->group(function () {

    // --- Authenticated user endpoint ---
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    })->name('api.v1.user');

    // --- Mobile Authentication Routes ---
    // Login & register menggunakan throttle:auth (5 per minute) untuk brute force protection
    Route::prefix('mobile')->name('api.v1.mobile.')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])
            ->middleware('throttle:auth')
            ->name('login');
        Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])
            ->middleware('throttle:auth')
            ->name('register');
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
        Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'user'])
            ->middleware('auth:sanctum')
            ->name('user');
    });

    // --- Xendit API Routes (protected, selain webhook) ---
    Route::prefix('xendit')->name('api.v1.xendit.')->group(function () {
        Route::middleware(['auth:sanctum,web'])->group(function () {
            Route::get('/payments/{payment}/status', [XenditPaymentController::class, 'getPaymentStatus'])
                ->name('payment.status');
            Route::get('/payments/{payment}', [XenditPaymentController::class, 'showPayment'])
                ->name('payment.show');
            Route::post('/payments/{payment}/expire', [XenditPaymentController::class, 'expirePayment'])
                ->name('payment.expire');
            Route::get('/payment-methods', [XenditPaymentController::class, 'getPaymentMethods'])
                ->name('payment.methods');
        });
    });

});

// =============================================================================
// Backward Compatibility — Redirect旧routes ke v1
// HTTP 301 (Permanent Redirect) untuk SEO + caching.
// CATATAN: Xendit webhook TIDAK di-redirect (lihat di atas).
//
// Untuk POST requests, gunakan 307 (Temporary Redirect) agar HTTP method
// tidak berubah (POST tetap POST, bukan POST → GET seperti 301).
// =============================================================================
Route::post('/mobile/login', fn() => redirect()->route('api.v1.mobile.login', [], 307));
Route::post('/mobile/register', fn() => redirect()->route('api.v1.mobile.register', [], 307));
Route::post('/mobile/logout', fn() => redirect()->route('api.v1.mobile.logout', [], 307));
Route::get('/mobile/user', fn() => redirect()->route('api.v1.mobile.user', [], 301));

Route::middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/xendit/payments/{payment}/status', fn($payment) => redirect()->route('api.v1.xendit.payment.status', $payment, 301));
    Route::get('/xendit/payments/{payment}', fn($payment) => redirect()->route('api.v1.xendit.payment.show', $payment, 301));
    Route::post('/xendit/payments/{payment}/expire', fn($payment) => redirect()->route('api.v1.xendit.payment.expire', $payment, 307));
    Route::get('/xendit/payment-methods', fn() => redirect()->route('api.v1.xendit.payment.methods', [], 301));
});
