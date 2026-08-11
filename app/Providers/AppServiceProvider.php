<?php

namespace App\Providers;

use App\Policies\ProdukPolicy;
use App\Policies\AuctionPolicy;
use App\Policies\TransaksiPolicy;
use App\Policies\LinktreePolicy;
use App\Policies\UserPolicy;
use App\Services\AuthorizationService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantManager as singleton
        $this->app->singleton('tenant', function ($app) {
            return new \App\Services\TenantManager();
        });

        // Register AuthorizationService as singleton
        $this->app->singleton(AuthorizationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default pagination view
        Paginator::defaultView('components.pagination');

        Blade::directive('decimal', function ($expression) {
            return "<?php echo number_format($expression, 2, ',', '.'); ?>";
        });

        // Force HTTPS for ngrok development
        if (str_contains(config('app.url'), 'ngrok-free.app')) {
            \URL::forceScheme('https');
        }

        // Apply service config overrides from database
        try {
            \App\Services\ServiceConfigOverride::applyAll();
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet
        }

        // =====================================================
        // POLICY REGISTRATION
        // =====================================================

        Gate::policy(\App\Models\Vendor\Produk::class, ProdukPolicy::class);
        Gate::policy(\App\Models\Auction::class, AuctionPolicy::class);
        Gate::policy(\App\Models\Vendor\Transaksi::class, TransaksiPolicy::class);
        Gate::policy(\App\Models\Vendor\Linktree::class, LinktreePolicy::class);
        Gate::policy(\App\Models\User::class, UserPolicy::class);

        // =====================================================
        // RATE LIMITING
        // =====================================================

        // General API rate limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Public page rate limit (linktree, welcome page)
        RateLimiter::for('public-page', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Manual transfer (prevent spam)
        RateLimiter::for('manual-transfer', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Auth routes (prevent brute force)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Webhook (high volume allowed)
        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        // Vendor POS actions
        RateLimiter::for('vendor-pos', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}
