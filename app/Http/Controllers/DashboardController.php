<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $wonBets = $user->bets()
            ->where('status', 'won')
            ->count();

        $lostBets = $user->bets()
            ->where('status', 'lost')
            ->count();

        return view('dashboard', [
            'wonBets'  => $wonBets,
            'lostBets' => $lostBets,
            'coins'    => $user->coins,
        ]);
    }
}
