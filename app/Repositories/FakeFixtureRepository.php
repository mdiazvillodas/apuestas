<?php

namespace App\Repositories;

use App\Repositories\Contracts\FixtureRepositoryInterface;

class FakeFixtureRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $path = storage_path('app/fake/fixtures.json');

        if (!file_exists($path)) {
            return [];
        }

        $json = json_decode(file_get_contents($path), true);

        return $json['response'] ?? [];
    }
}