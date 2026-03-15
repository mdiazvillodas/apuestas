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

                continue;
            }

            $fixtures = $fixtures->merge(
                $response->json('response') ?? []
            );
        }

        return $fixtures
            ->filter(fn($f) => $f['league']['id'] == $leagueId)
            ->values()
            ->all();
    }
}