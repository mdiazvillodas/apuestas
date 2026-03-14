<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;
use App\Models\Event;

class Kernel extends ConsoleKernel
{
    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);

        logger()->info('KERNEL LOADED - SCHEDULER INITIALIZED');
    }

protected function schedule(Schedule $schedule): void
{
    $schedule->call(function () {

        logger()->info('RUNNING SCHEDULED TASKS');

        app(\App\Services\FixtureSyncService::class)
            ->sync(
                config('fixtures.league_id'),
                config('fixtures.season')
            );

        Event::where('status','draft')
            ->where('starts_at','<=',now()->addHours(24))
            ->update([
                'status'=>'open',
                'betting_opens_at'=>now()
            ]);

        Event::where('status','open')
            ->where('starts_at','<=',now())
            ->update([
                'status'=>'closed'
            ]);

    });
}
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}