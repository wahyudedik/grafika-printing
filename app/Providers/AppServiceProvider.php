<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default pagination view to Bootstrap-compatible (Tabler) for Laravel 13+
        Paginator::defaultView('components.pagination');

        Blade::directive('decimal', function ($expression) {
            return "<?php echo number_format($expression, 2, ',', '.'); ?>";
        });

        // Force HTTPS for ngrok development
        if (str_contains(config('app.url'), 'ngrok-free.app')) {
            \URL::forceScheme('https');
        }

        // Apply service config overrides from database
        // This allows admin to manage API keys via panel instead of .env only
        try {
            \App\Services\ServiceConfigOverride::applyAll();
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet (e.g., during migration)
        }
    }
}
