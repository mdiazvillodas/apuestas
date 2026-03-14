<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Event;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Sync de fixtures desde la API
        $schedule->call(function () {

            logger()->info('FIXTURE SYNC RUNNING');

            app(\App\Services\FixtureSyncService::class)
                ->sync(
                    config('fixtures.league_id'),
                    config('fixtures.season')
                );

        })->everyThirtyMinutes()->withoutOverlapping();

        // Abrir apuestas automáticamente
        $schedule->call(function () {

            Event::where('status', 'draft')
                ->where('starts_at', '<=', now()->addHours(24))
                ->update([
                    'status' => 'open',
                    'betting_opens_at' => now(),
                ]);

        })->everyFiveMinutes();

        // Cerrar apuestas automáticamente
        $schedule->call(function () {

            Event::where('status', 'open')
                ->where('starts_at', '<=', now())
                ->update([
                    'status' => 'closed',
                ]);

        })->everyFiveMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}