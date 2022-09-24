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
        Commands\HappyBirthday::class,
        Commands\IncompleteUserInfo::class,
        Commands\FirmaPendiente::class,
        Commands\FirmaExpirada::class,
        Commands\UnDiaPago::class,
        Commands\PendienteNovacion::class,
        Commands\AnalisisCreditosIncompletos::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('passengers:happy_birthday')->dailyAt('08:00');
        $schedule->command('incomplete:info')->everyMinute();
        $schedule->command('firma:pendiente')->daily();
        $schedule->command('firma:expirada')->daily();
        $schedule->command('undia:pago')->daily();
        $schedule->command('credito:moroso')->daily();
        $schedule->command('pendiente:novacion')->daily();
        $schedule->command('analisis:incompleto')->daily();
        // $schedule->command('inspire')
        //          ->hourly();
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
