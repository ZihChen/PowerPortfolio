<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param array $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guard)
    {

        if (Auth::guard($guard)->check()) {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
