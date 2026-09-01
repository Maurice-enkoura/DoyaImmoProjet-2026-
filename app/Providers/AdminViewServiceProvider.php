<?php
// app/Providers/AdminViewServiceProvider.php

namespace App\Providers;

use App\Models\Agence;
use App\Models\Signalement;
use App\Models\RendezVous;
use App\Models\MiseEnVedette; // ✅ AJOUT
use App\Enums\StatutSignalementEnum;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            // Agences en attente de validation
            $agencesEnAttente = Agence::where('statut_validation', false)
                ->where('est_refusee', false) // ✅ Exclure les refusées
                ->count();
            
            // Signalements en attente
            $signalementsEnAttente = Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count();
            
            // Rendez-vous en attente (planifiés)
            $rendezVousEnAttente = RendezVous::where('statut', StatutRendezVousEnum::PLANIFIE)->count();

            // ✅ Mises en vedette en attente
            $misesEnAttente = MiseEnVedette::where('statut', 'en_attente')->count();

            $view->with(compact(
                'agencesEnAttente',
                'signalementsEnAttente',
                'rendezVousEnAttente',
                'misesEnAttente' // ✅ AJOUT
            ));
        });
    }

    public function register(): void
    {
        //
    }
}