<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Repositories\Contracts\FixtureRepositoryInterface;

class ApiFootballRepository implements FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array
    {
        $from = now()->toDateString();
        $to = now()->addDays(7)->toDateString();

        logger()->info('REQUESTING FIXTURES', [
            'from' => $from,
            'to' => $to,
            'league_id' => $leagueId,
            'season' => $season,
        ]);

        $response = Http::withHeaders([
            'x-apisports-key' => config('fixtures.api_key'),
        ])->get('https://v3.football.api-sports.io/fixtures', [
            'league' => $leagueId,
            'season' => $season,
            'from' => $from,
            'to' => $to,
            'timezone' => 'UTC',
        ]);

        if (!$response->successful()) {
            logger()->error('API Football request failed', [
                'from' => $from,
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $payload = $response->json() ?? [];
        $fixtures = $payload['response'] ?? [];
        $errors = $payload['errors'] ?? [];

        logger()->info('API Football fixtures range response', [
            'from' => $from,
            'to' => $to,
            'count' => count($fixtures),
            'errors' => $errors,
            'paging' => $payload['paging'] ?? null,
        ]);

        if (!empty($errors)) {
            logger()->warning('API Football returned errors.', [
                'errors' => $errors,
            ]);
        }

        return $fixtures;
    }
}
