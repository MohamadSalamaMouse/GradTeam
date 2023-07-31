<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->authenticate($request, $guards) === 'authentication_failed') {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }

    /**
     * Redirect the user if they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Check if the request is an API request
        if ($request->is('api/*') || $request->expectsJson()) {
            // For API requests, return 'authentication_failed' instead of redirecting
            return 'authentication_failed';
        } else {
            // For non-API requests, redirect to the login route
            return route('login');
        }
    }
}
