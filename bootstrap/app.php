<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'locale' => \App\Http\Middleware\LocaleMiddleware::class,
            'country.restriction' => \App\Http\Middleware\CheckCountryRestriction::class,
            'csp' => \App\Http\Middleware\ContentSecurityPolicy::class,
            'international' => \App\Http\Middleware\SetInternationalPreferences::class,
        ]);

        // Apply CSP, Locale and International middleware to web group
        $middleware->appendToGroup('web', \App\Http\Middleware\ContentSecurityPolicy::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\LocaleMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetInternationalPreferences::class);

        // Set the authenticated redirect path
        $middleware->redirectGuestsTo('/login');

        // Set the guest redirect path
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
