<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
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

        $user = $request->user();
        $userType = $user->usertype;

        if ($userType === 'vendor') {
            // Check if user has vendor relationship
            if (!$user->vendorUser || $user->vendorUser->isEmpty()) {
                return redirect('/login')->with('error', 'No vendor account associated with this user.');
            }

            return $next($request);
        } else {
            // Redirect to appropriate dashboard based on user type
            if ($userType === 'dev') {
                return redirect()->route('admin.dashboard');
            } elseif ($userType === 'user') {
                return redirect()->route('user.dashboard');
            } else {
                return redirect('/login')->with('error', 'Invalid user type.');
            }
        }
    }
}
