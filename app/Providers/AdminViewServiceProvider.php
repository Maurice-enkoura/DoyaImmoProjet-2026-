<?php
// app/Providers/AdminViewServiceProvider.php

namespace App\Providers;

use App\Models\Agence;
use App\Models\Signalement;
use App\Models\RendezVous;
use App\Enums\StatutSignalementEnum;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('admin.layout', function ($view) {
            // Agences en attente de validation
            $agencesEnAttente = Agence::where('statut_validation', false)->count();
            
            // Signalements en attente
            $signalementsEnAttente = Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count();
            
            // Rendez-vous en attente (planifiés)
            $rendezVousEnAttente = RendezVous::where('statut', StatutRendezVousEnum::PLANIFIE)->count();

            $view->with(compact(
                'agencesEnAttente',
                'signalementsEnAttente',
                'rendezVousEnAttente'
            ));
        });
    }

    public function register(): void
    {
        //
    }
}