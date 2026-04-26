<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Repositories\Contracts\FixtureRepositoryInterface;

class ApiFootballRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $fixtures = collect();

        for ($i = 0; $i < 7; $i++) {

            $date = now()->addDays($i)->toDateString();

            logger()->info('REQUESTING FIXTURES', [
                'date' => $date,
                'league_id' => $leagueId,
                'season' => $season,
            ]);

            $response = Http::withHeaders([
                'x-apisports-key' => config('fixtures.api_key'),
            ])->get('https://v3.football.api-sports.io/fixtures', [
                'date' => $date,
                'league' => $leagueId,
                'season' => $season,
            ]);

            if (!$response->successful()) {

                logger()->error('API Football request failed', [
                    'date' => $date,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                continue;
            }

            $dailyFixtures = $response->json('response') ?? [];

            logger()->info('API Football fixtures response', [
                'date' => $date,
                'count' => count($dailyFixtures),
            ]);

            $fixtures = $fixtures->merge($dailyFixtures);
        }

        return $fixtures
            ->values()
            ->all();
    }
}
