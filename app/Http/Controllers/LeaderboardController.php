<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $activeLeague = null;
        $availableLeagues = collect();
        $userQuery = User::with('bets');

        if ($request->user()) {
            $availableLeagues = $request->user()
                ->leagues()
                ->orderBy('name')
                ->get();
        }

        if ($request->filled('league')) {
            abort_unless($request->user(), 403);

            $activeLeague = League::with('members')
                ->findOrFail($request->integer('league'));

            abort_unless(
                $activeLeague->members->contains('id', $request->user()->id),
                403
            );

            $userQuery->whereIn('id', $activeLeague->members->pluck('id'));
        }

        $users = $userQuery
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

                $net = $user->bets
                    ->whereNotNull('profit')
                    ->sum('profit');

                $user->coins_won = $won;
                $user->coins_lost = $lost;
                $user->balance = $net;

                return $user;
            })
            ->sortByDesc('coins_won')
            ->values();

        return view('leaderboard.index', [
            'users' => $users,
            'activeLeague' => $activeLeague,
            'availableLeagues' => $availableLeagues,
        ]);
    }

}
