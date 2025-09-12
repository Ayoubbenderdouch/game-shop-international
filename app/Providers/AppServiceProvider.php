<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force the application URL in local environment
        if ($this->app->environment('local')) {
            // Force the root URL to match what's in the config
            URL::forceRootUrl(config('app.url'));

            // If you're using HTTPS locally (unlikely), uncomment this:
            // URL::forceScheme('https');
        }

        // For production with HTTPS
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
