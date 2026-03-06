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

        $service->sync(1, 2026);

        $this->info('Fixtures synced successfully.');
    }
}