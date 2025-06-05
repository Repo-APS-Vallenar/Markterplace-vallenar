<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Convertir string con separador a array (por ejemplo: 'admin|seller')
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            $roles = explode('|', $roles[0]);
        }

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
