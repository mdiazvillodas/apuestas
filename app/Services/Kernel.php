<?php
$schedule->call(function () {

    app(\App\Services\FixtureSyncService::class)
        ->sync(
            config('fixtures.league_id'),
            config('fixtures.season')
        );

})->everyThirtyMinutes();
