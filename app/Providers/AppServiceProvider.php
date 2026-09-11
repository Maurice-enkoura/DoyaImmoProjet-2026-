<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\RendezVous;
use App\Observers\RendezVousObserver;
use App\View\Composers\NotificationComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Injecte les notifications dans tous les layouts du dashboard
        View::composer([
            'layouts.dashboard-agence',
            'layouts.dashboard-particulier',
            'layouts.dashboard-admin',
        ], NotificationComposer::class);


        
        // Enregistrer l'Observer pour RendezVous
        URL::forceScheme('https');
        RendezVous::observe(RendezVousObserver::class);
    }
}
