<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Repositories\Contracts\FixtureRepositoryInterface;

class ApiFootballRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $date = now()->toDateString();

        logger()->info('API REPOSITORY RUNNING', [
            'league' => $leagueId,
            'season' => $season,
            'api_key_present' => config('fixtures.api_key') ? true : false
        ]);

        $response = Http::withHeaders([
            'x-apisports-key' => config('fixtures.api_key'),
        ])
        ->get('https://v3.football.api-sports.io/fixtures', [
            'date' => $date
        ]);

        if (!$response->successful()) {

            logger()->error('API Football request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        }

        $fixtures = collect($response->json('response'));

        logger()->info('API Football response', [
            'total' => $fixtures->count()
        ]);

        return $fixtures
            ->filter(fn($f) => $f['league']['id'] == $leagueId)
            ->values()
            ->all();
    }
}