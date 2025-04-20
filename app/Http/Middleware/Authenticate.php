<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Since default user login is disabled, we only care about admin.
        if (! $request->expectsJson()) {
            // Assume any unauthenticated access attempt requiring login
            // should go to the admin login page.
            return route('admin.login');
            // if ($request->is('admin') || $request->is('admin/*')) {
            //      return route('admin.login');
            // }
            // // No default 'login' route anymore, perhaps redirect to homepage?
            // return route('/'); // Or throw an exception?
        }
    }
}
