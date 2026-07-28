<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable Scramble docs entirely in production:
        // - The /docs/api and /docs/api.json routes will return 404
        // - The testing reset-database endpoint is also excluded
        if ($this->app->environment('production')) {
            Scramble::ignoreDefaultRoutes();
        }

    }
}
