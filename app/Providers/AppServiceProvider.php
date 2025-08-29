<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Lang;

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
        // Add resources/lang as an additional language path
        Lang::addJsonPath(resource_path('lang'));

        // Also add namespace for PHP translation files if they exist
        if (is_dir(resource_path('lang'))) {
            $this->loadTranslationsFrom(resource_path('lang'), 'custom');
        }
    }
}
