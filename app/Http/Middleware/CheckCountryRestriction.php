<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCountryRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user's country is restricted
            // You can implement your country restriction logic here
            $restrictedCountries = config('app.restricted_countries', []);

            if ($user->country && in_array($user->country, $restrictedCountries)) {
                return response()->view('errors.country-restricted', [], 403);
            }
        }

        return $next($request);
    }
}
