<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\DemandeImmobiliere;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutRendezVousEnum;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Partager les variables avec toutes les vues du dashboard agence
        View::composer('layouts.dashboard-agence', function ($view) {
            $user = Auth::user();
            $abonnementActuel = null;
            $besoinsDisponibles = 0;
            $rendezvousAVenir = 0;

            if ($user && $user->isAgence()) {
                $agence = $user->agence;
                
                if ($agence) {
                    $abonnementActuel = $agence->abonnements()
                        ->where('statut', true)
                        ->where('date_fin', '>', now())
                        ->first();
                    
                    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
                    
                    $rendezvousAVenir = $agence->rendezVous()
                        ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
                        ->where('date_visite', '>=', now()->toDateString())
                        ->count();
                }
            }

            $view->with([
                'abonnementActuel' => $abonnementActuel,
                'besoinsDisponibles' => $besoinsDisponibles,
                'rendezvousAVenir' => $rendezvousAVenir,
            ]);
        });
    }

    public function register(): void
    {
        //
    }
}