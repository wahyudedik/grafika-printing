<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only set tenant context for vendor users
            if ($user->usertype === 'vendor') {
                $vendorUser = $user->vendorUser->first();

                if ($vendorUser) {
                    // Set the tenant context
                    Tenant::setVendor($vendorUser);
                } else {
                    // No vendor context available
                    abort(403, 'No vendor context available for this user');
                }
            }
            // For dev and user types, no tenant context needed
        }

        return $next($request);
    }
}
