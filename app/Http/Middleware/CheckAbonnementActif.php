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
            // ✅ SUPPRIMER LE 'error' POUR ÉVITER LE DOUBLE MESSAGE
            // La vue gère déjà l'affichage "Aucun abonnement actif"
            return redirect()->route('agence.abonnement');
        }

        return $next($request);
    }
}