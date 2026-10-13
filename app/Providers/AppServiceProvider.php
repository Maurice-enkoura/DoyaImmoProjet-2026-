<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\RendezVous;
use App\Observers\RendezVousObserver;
use App\View\Composers\NotificationComposer;
use App\View\Composers\AbonnementComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Notifications (déjà présent)
        View::composer([
            'layouts.dashboard-agence',
            'layouts.dashboard-particulier',
            'layouts.dashboard-admin',
        ], NotificationComposer::class);

        // ✅ AJOUT : Abonnement pour le layout agence
        View::composer(
            'layouts.dashboard-agence',
            AbonnementComposer::class
        );

        URL::forceScheme('https');
        RendezVous::observe(RendezVousObserver::class);
    }
}