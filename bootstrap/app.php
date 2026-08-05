<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->alias([
                'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
                'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
                'dev' => \App\Http\Middleware\DevMiddleware::class,
                'vendor' => \App\Http\Middleware\VendorMiddleware::class,
                'user' => \App\Http\Middleware\UserMiddleware::class,
                'tenants' => \App\Http\Middleware\SetTenantContext::class,
                'Tenant' => \App\Facades\Tenant::class,
                'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
                'input.sanitize' => \App\Http\Middleware\InputSanitizer::class,
            ])
            ->group(
                'tenant',
                [
                    // \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
                    // \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
                ]
            )
            ->group(
                'security',
                [
                    \App\Http\Middleware\SecurityHeaders::class,
                    \App\Http\Middleware\InputSanitizer::class,
                ]
            )
            ->validateCsrfTokens(except: [
                'api/xendit/webhook',
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
