<?php

namespace App\Http\Controllers;

use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::with('bets')
            ->get()
            ->map(function ($user) {

                $won = $user->bets
                    ->where('profit', '>', 0)
                    ->sum('profit');

                $lost = abs(
                    $user->bets
                        ->where('profit', '<', 0)
                        ->sum('profit')
                );

                $user->coins_won = $won;
                $user->coins_lost = $lost;
                $user->balance = $won - $lost;

                return $user;
            })
            ->sortByDesc('balance')
            ->values();

        return view('leaderboard.index', [
            'users' => $users,
        ]);
    }

}
