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
        ])->get('https://v3.football.api-sports.io/fixtures', [
            'next' => 20
        ]);

        if (!$response->successful()) {

            logger()->error('API Football error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $fixtures = collect($response->json('response'));

        return $fixtures
            ->filter(fn($f) => $f['league']['id'] == 39) // Premier League
            ->values()
            ->all();
    }
}