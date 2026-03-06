<?php

namespace App\Repositories\Contracts;

interface FixtureRepositoryInterface
{
    public function getFixtures(int $leagueId, int $season): array;
}