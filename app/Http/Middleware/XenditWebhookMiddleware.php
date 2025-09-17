<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class XenditWebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF verification for Xendit webhooks
        if ($request->is('xendit/webhook')) {
            // Log webhook request for debugging
            Log::info('Xendit webhook received', [
                'headers' => $request->headers->all(),
                'body' => $request->getContent(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}
