<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbonnementActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAgence()) {
            abort(403, 'Accès réservé aux agences.');
        }

        $agence = $user->agence;

        if (!$agence || !$agence->aAbonnementActif()) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Vous devez souscrire un abonnement actif pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}