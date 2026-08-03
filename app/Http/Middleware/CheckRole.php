<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Convertir l'énumération en valeur string si nécessaire
        $userRole = $request->user()->role;
        if (is_object($userRole) && method_exists($userRole, 'value')) {
            $userRole = $userRole->value;
        }

        if (!in_array($userRole, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}