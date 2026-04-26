<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FixtureSyncService;

class SyncFixtures extends Command
{
    protected $signature = 'fixtures:sync';
    protected $description = 'Sync fixtures from repository';

    public function handle(FixtureSyncService $service)
    {
        $this->info('Starting fixture sync...');

        $service->sync(
            config('fixtures.league_id'),
            config('fixtures.season')
        );

        $this->info('Fixtures synced successfully.');
    }
}
