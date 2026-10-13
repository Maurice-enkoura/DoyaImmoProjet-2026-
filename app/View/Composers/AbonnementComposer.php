<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Abonnement;

class AbonnementComposer
{
    public function compose(View $view): void
    {
        $user   = Auth::user();
        $agence = $user->agence ?? null;

        $abonnementActif = config('abonnement.actif', false);
        $modeGratuit     = config('abonnement.mode_gratuit', true);

        $abonnementActuel = null;
        $offresRestantes  = 0;
        $limiteOffres     = PHP_INT_MAX;

        if ($abonnementActif && !$modeGratuit && $agence) {
            // ⚠️ IMPORTANT : statut = 1 (entier), pas 'actif' (string)
            // Vu la requête SQL dans le stack trace :
            $abonnementActuel = Abonnement::where('agence_id', $agence->id)
                ->where('statut', 1)              // <-- entier, pas string
                ->where('date_fin', '>', now())   // <-- vérifier que ce n'est pas expiré
                ->latest()
                ->first();

            if ($abonnementActuel) {
                $limiteOffres    = $abonnementActuel->limite_offres ?? PHP_INT_MAX;
                $offresUtilisees = $abonnementActuel->offres_utilisees ?? 0;
                $offresRestantes = max(0, $limiteOffres - $offresUtilisees);
            }
        }

        $view->with([
            'abonnementActif'  => $abonnementActif,
            'modeGratuit'      => $modeGratuit,
            'abonnementActuel' => $abonnementActuel,
            'offresRestantes'  => $offresRestantes,
            'limiteOffres'     => $limiteOffres,
        ]);
    }
}