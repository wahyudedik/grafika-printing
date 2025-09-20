<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DevMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userType = $request->user()->usertype;
        if ($userType === 'dev') {
            return $next($request);
        } else {
            // Redirect to appropriate dashboard based on user type
            if ($userType === 'vendor') {
                return redirect()->route('vendor.dashboard');
            } elseif ($userType === 'user') {
                return redirect()->route('user.dashboard');
            } else {
                return redirect()->route('welcome')->with('error', 'Akses ditolak. Hanya developer yang dapat mengakses halaman ini.');
            }
        }
    }
}
