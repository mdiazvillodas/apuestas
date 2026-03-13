<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Repositories\Contracts\FixtureRepositoryInterface;

class ApiFootballRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $response = Http::withHeaders([
            'x-apisports-key' => config('fixtures.api_key'),
        ])
        ->withoutVerifying()
        ->get('https://v3.football.api-sports.io/fixtures', [
            'date' => now()->toDateString()
        ]);

        if (!$response->successful()) {

            logger()->error('API Football error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $fixtures = collect($response->json('response'));

        // filtramos solo la liga que queremos (Premier League = 39)
        $fixtures = $fixtures->filter(function ($fixture) {
            return isset($fixture['league']['id']) && $fixture['league']['id'] == 39;
        });

        logger()->info('Premier fixtures found', [
            'count' => $fixtures->count()
        ]);

        return $fixtures->values()->all();
    }
}