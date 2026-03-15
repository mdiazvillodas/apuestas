<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Repositories\Contracts\FixtureRepositoryInterface;

class ApiFootballRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $fixtures = collect();

        // TEST: pedir solo el viernes 20-03-2026
        $date = '2026-03-20';

        logger()->info('REQUESTING FIXTURES TEST', [
            'date' => $date
        ]);

        $response = Http::withHeaders([
            'x-apisports-key' => config('fixtures.api_key'),
        ])->get('https://v3.football.api-sports.io/fixtures', [
            'date' => $date
        ]);

        if (!$response->successful()) {

            logger()->error('API Football request failed', [
                'date' => $date,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        }

        $fixtures = collect(
            $response->json('response') ?? []
        );

        logger()->info('API RESPONSE TEST', [
            'total_fixtures_returned' => $fixtures->count()
        ]);

        return $fixtures
            ->filter(fn($f) => $f['league']['id'] == $leagueId)
            ->values()
            ->all();
    }
}