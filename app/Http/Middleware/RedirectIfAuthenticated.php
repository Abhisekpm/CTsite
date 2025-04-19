<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If the admin guard is checked and authenticated, redirect to /admin
                if ($guard === 'admin') {
                    // Assuming 'admin.home' is the name for the GET /admin route
                    return redirect()->route('admin.home');
                }
                // Otherwise (default guard 'web' or null), redirect to standard HOME
                // return redirect(RouteServiceProvider::HOME);
                 // Check if RouteServiceProvider::HOME exists, otherwise use a default like '/'
                return redirect(defined(RouteServiceProvider::class . '::HOME') ? RouteServiceProvider::HOME : '/');
            }
        }

        return $next($request);
    }
}
