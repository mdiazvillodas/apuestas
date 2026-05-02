<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Team;
use App\Repositories\Contracts\FixtureRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FixtureSyncService
{
    protected FixtureRepositoryInterface $repository;

    public function __construct(FixtureRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /*public function sync(int $leagueId, int $season): void
    {
        $fixtures = $this->repository->getFixtures($leagueId, $season);

        foreach ($fixtures as $fixture) {
            DB::transaction(function () use ($fixture) {
                $this->syncSingleFixture($fixture);
            });
        }
    }*/
    public function sync(int $leagueId, int $season): void
    {
        $fixtures = $this->repository->getFixtures($leagueId, $season);

        logger()->info('Fixtures count', [
            'count' => count($fixtures)
        ]);

        foreach ($fixtures as $fixture) {

            logger()->info('Processing fixture', [
                'id' => $fixture['fixture']['id'] ?? null
            ]);

            DB::transaction(function () use ($fixture) {
                $this->syncSingleFixture($fixture);
            });
        }
    }        
protected function syncSingleFixture(array $fixture): void
{
    if (
        empty($fixture['teams']['home']['name']) ||
        empty($fixture['teams']['away']['name'])
    ) {
        return;
    }

    $externalId = $fixture['fixture']['id'];

    $startsAt = Carbon::parse($fixture['fixture']['date']);
    $bettingOpensAt = $startsAt->copy()->subDays(7);
    $bettingClosesAt = $startsAt;

    $event = Event::where('external_id', $externalId)->first();

    if (!$event) {
        $event = $this->createEventFromFixture($fixture);
    } else {

        if ($event->auto_managed && $event->status !== 'finished') {

            $event->update([
                'starts_at' => $startsAt,
                'betting_opens_at' => $bettingOpensAt,
                'betting_closes_at' => $bettingClosesAt,
                'round' => $fixture['league']['round'] ?? $event->round,
                ...$this->scoreFieldsFromFixture($fixture),
            ]);
        }
    }

    $this->handleAutoOpen($event);
    $this->handleAutoClose($event);
    $this->handleAutoSettle($event, $fixture);
}

protected function createEventFromFixture(array $fixture): Event
{
    $home = $fixture['teams']['home'];
    $away = $fixture['teams']['away'];
    $startsAt = Carbon::parse($fixture['fixture']['date']);
    $bettingOpensAt = $startsAt->copy()->subDays(7);
    $bettingClosesAt = $startsAt;

    $teamA = Team::firstOrCreate(
        ['name' => $home['name']],
        ['logo_path' => $home['logo'] ?? 'default.png']
    );

    $teamB = Team::firstOrCreate(
        ['name' => $away['name']],
        ['logo_path' => $away['logo'] ?? 'default.png']
    );

    return Event::create([
        'title' => $teamA->name . ' vs ' . $teamB->name,
        'team_a_id' => $teamA->id,
        'team_b_id' => $teamB->id,
        'starts_at' => $startsAt,
        'betting_opens_at' => $bettingOpensAt,
        'betting_closes_at' => $bettingClosesAt,
        'status' => 'draft',
        ...$this->scoreFieldsFromFixture($fixture),
        'external_id' => $fixture['fixture']['id'],
        'source' => 'api',
        'round' => $fixture['league']['round'] ?? null,
        'auto_managed' => true,
    ]);
}

protected function handleAutoOpen(Event $event): void
{
    if (!$event->auto_managed) return;
    if ($event->status !== 'draft') return;
    if (!$event->starts_at) return;

    // Auto-open betting five days before kickoff.
    if (now()->diffInDays($event->starts_at, false) <= 5) {

        $event->update([
            'status' => 'open',
            'betting_opens_at' => now(),
            'betting_closes_at' => $event->starts_at,
        ]);
    }
}

    protected function handleAutoClose(Event $event): void
    {
        if (!$event->auto_managed) return;
        if ($event->status !== 'open') return;

        if (now()->greaterThanOrEqualTo($event->starts_at)) {
            $event->update([
                'status' => 'closed'
            ]);
        }
    }

protected function handleAutoSettle(Event $event, array $fixture): void
{
    if (!$event->auto_managed) return;

    if ($event->status === 'finished') return;

    if (! in_array($fixture['fixture']['status']['short'] ?? null, ['FT', 'AET', 'PEN'], true)) return;

    $homeGoals = $fixture['goals']['home'] ?? 0;
    $awayGoals = $fixture['goals']['away'] ?? 0;

    if ($homeGoals > $awayGoals) {
        $result = 'team_a';
    } elseif ($awayGoals > $homeGoals) {
        $result = 'team_b';
    } else {
        $result = 'draw';
    }

    app(\App\Services\SettlementService::class)
        ->settle($event, $result);
}

protected function scoreFieldsFromFixture(array $fixture): array
{
    $homeScore = $fixture['goals']['home'] ?? null;
    $awayScore = $fixture['goals']['away'] ?? null;

    return [
        'team_a_score' => is_numeric($homeScore) ? (int) $homeScore : null,
        'team_b_score' => is_numeric($awayScore) ? (int) $awayScore : null,
        'match_status_short' => $fixture['fixture']['status']['short'] ?? null,
        'match_status_long' => $fixture['fixture']['status']['long'] ?? null,
        'score_updated_at' => now(),
    ];
}
}
