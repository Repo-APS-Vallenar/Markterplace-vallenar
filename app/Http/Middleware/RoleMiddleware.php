<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Si no tiene el rol adecuado, aborta con error 403 (prohibido)
            abort(403, 'Unauthorized action.');
        }

        // Si tiene el rol adecuado, continua con la solicitud
        return $next($request);
    }
}
