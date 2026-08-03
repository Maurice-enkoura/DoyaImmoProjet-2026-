<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAgenceValidee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->isAgence()) {
            abort(403, 'Accès réservé aux agences.');
        }

        $agence = $user->agence;

        if (!$agence || !$agence->estValidee()) {
            return redirect()->route('agence.dashboard')
                ->with('error', 'Votre agence doit être validée par un administrateur pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}