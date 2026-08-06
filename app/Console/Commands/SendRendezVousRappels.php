<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RendezVous;
use App\Notifications\RappelVisiteNotification;
use Carbon\Carbon;

class SendRendezVousRappels extends Command
{
    protected $signature = 'rappels:rendezvous';
    protected $description = 'Envoie les rappels de rendez-vous';

    public function handle()
    {
        // Rappel 24h avant
        $dateDemain = Carbon::tomorrow()->toDateString();
        $rendezVous24h = RendezVous::where('date_visite', $dateDemain)
            ->where('statut', 'confirme')
            ->get();

        foreach ($rendezVous24h as $rdv) {
            $rdv->particulier->user->notify(new RappelVisiteNotification($rdv, '24h'));
            $rdv->agence->user->notify(new RappelVisiteNotification($rdv, '24h'));
        }

        // Rappel 1h avant
        $dateAujourdhui = Carbon::today()->toDateString();
        $heureDans1h = Carbon::now()->addHour()->format('H:i');

        $rendezVous1h = RendezVous::where('date_visite', $dateAujourdhui)
            ->where('heure_visite', $heureDans1h)
            ->where('statut', 'confirme')
            ->get();

        foreach ($rendezVous1h as $rdv) {
            $rdv->particulier->user->notify(new RappelVisiteNotification($rdv, '1h'));
            $rdv->agence->user->notify(new RappelVisiteNotification($rdv, '1h'));
        }

        $this->info('Rappels envoyés avec succès.');
    }
}