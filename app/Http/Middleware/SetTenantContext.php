<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

            try {
                switch ($user->usertype) {
                    case 'vendor':
                        // Set vendor tenant context
                        $vendorUser = $user->vendorUser->first();
                        if ($vendorUser) {
                            Tenant::setVendor($vendorUser);
                        } else {
                            Log::error('No vendor context available', [
                                'user_id' => $user->id,
                                'user_type' => $user->usertype
                            ]);
                            Auth::logout();
                            return redirect('/login')->with('error', 'No vendor account associated with this user.');
                        }
                        break;

                    case 'user':
                        // Set user tenant context
                        Tenant::setUser($user);
                        break;

                    case 'dev':
                    case 'admin':
                    case 'superadmin':
                        // No tenant context for admin users (global access)
                        break;

                    default:
                        Log::warning('Unknown user type', [
                            'user_id' => $user->id,
                            'user_type' => $user->usertype
                        ]);
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Tenant context setup failed', [
                    'user_id' => $user->id,
                    'user_type' => $user->usertype,
                    'error' => $e->getMessage()
                ]);

                // Only logout for vendor users, others can continue
                if ($user->usertype === 'vendor') {
                    Auth::logout();
                    return redirect('/login')->with('error', 'Failed to setup tenant context.');
                }
            }
        }

        return $next($request);
    }
}
