<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update last login time
        $request->user()->update(['last_login_at' => now()]);

        $userType = $request->user()->usertype;

        if ($userType === 'dev') {
            return redirect()->route('admin.dashboard');
        }
        if ($userType === 'user') {
            return redirect()->intended(route('user.dashboard', absolute: false));
        }
        if ($userType === 'vendor') {
            return redirect()->intended(route('vendor.dashboard', absolute: false));
        }

        // Fallback for unknown user types
        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
