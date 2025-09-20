<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        Blade::directive('decimal', function ($expression) {
            return "<?php echo number_format($expression, 2, ',', '.'); ?>";
        });

        // Force HTTPS for ngrok development
        if (str_contains(config('app.url'), 'ngrok-free.app')) {
            \URL::forceScheme('https');
        }
    }
}
