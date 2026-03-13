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
            'league' => 39,
            'season' => 2025,
        ]);

        if (!$response->successful()) {

            logger()->error('API Football error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        return $response->json('response') ?? [];
    }
}