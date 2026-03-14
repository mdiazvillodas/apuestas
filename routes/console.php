<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Event;
use App\Services\FixtureSyncService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {

    logger()->info('RUNNING SCHEDULED TASKS');

    app(FixtureSyncService::class)
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

})->everyFiveMinutes();