protected $commands = [
    \App\Console\Commands\SyncQuartiers::class,
];

protected function schedule(Schedule $schedule)
{
    $schedule->command('rappels:rendezvous')->everyMinute();
}

protected function schedule(Schedule $schedule)
{
    // Renouvellement des abonnements chaque jour
    $schedule->command('abonnements:renouveler')->daily();
}