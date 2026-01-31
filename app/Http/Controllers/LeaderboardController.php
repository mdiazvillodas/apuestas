<?php

namespace App\Http\Controllers;

use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('coins')->get();

        return view('leaderboard.index', [
            'users' => $users,
        ]);
    }
}
