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
        // Only concerned with the 'admin' guard now.
        $guards = ['admin']; // Force check only for admin guard if middleware is 'guest:admin' 
                              // or even just 'guest' if we assume only admin uses it now.

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If admin guard is authenticated, redirect to admin home
                return redirect()->route('admin.home');
                // if ($guard === 'admin') {
                //     return redirect()->route('admin.home');
                // }
                // // Default user home redirect removed
                // return redirect(defined(RouteServiceProvider::class . '::HOME') ? RouteServiceProvider::HOME : '/');
            }
        }

        return $next($request);
    }
}
