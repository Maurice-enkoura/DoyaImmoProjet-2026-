<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SyncQuartiers::class,
        \App\Console\Commands\SupprimerDemandesExpirees::class,
        \App\Console\Commands\SyncRendezVousStatuts::class,
              \App\Console\Commands\GenererCreneauxHebdomadaires::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ✅ Rappels de rendez-vous
        $schedule->command('rappels:rendezvous')->everyMinute();

        $schedule->command('creneaux:generer-hebdomadaire');

        // ✅ Renouvellement des abonnements
        $schedule->command('abonnements:renouveler')->daily();
          $schedule->command('rendezvous:sync-statuts')->daily();

        // ✅ Nettoyer les demandes expirées toutes les 6 heures
        $schedule->command('demandes:supprimer-expirees')
            ->everySixHours()
            ->appendOutputTo(storage_path('logs/demandes-expirees.log'));

        // ✅ Nettoyer les logs tous les jours
        $schedule->command('log:clear')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}