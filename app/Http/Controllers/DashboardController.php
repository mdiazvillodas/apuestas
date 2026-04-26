<?php

namespace App\Http\Controllers;

use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $settledBets = $user->bets()
            ->whereIn('status', ['won', 'lost'])
            ->whereNotNull('profit');

        $wonBets = (clone $settledBets)
            ->where('status', 'won')
            ->count();

        $lostBets = (clone $settledBets)
            ->where('status', 'lost')
            ->count();

        $netProfit = (clone $settledBets)->sum('profit');
        $coinsWon = (clone $settledBets)
            ->where('profit', '>', 0)
            ->sum('profit');
        $coinsLost = abs((clone $settledBets)
            ->where('profit', '<', 0)
            ->sum('profit'));
        $pendingBets = $user->bets()
            ->where('status', 'pending')
            ->count();

        $dailyProfit = (clone $settledBets)
            ->selectRaw('DATE(updated_at) as day, SUM(profit) as profit')
            ->where('updated_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('profit', 'day');

        $runningProfit = 0;
        $chartLabels = [];
        $chartValues = [];

        foreach (CarbonPeriod::create(now()->subDays(13)->startOfDay(), now()->startOfDay()) as $date) {
            $day = $date->toDateString();
            $runningProfit += (float) ($dailyProfit[$day] ?? 0);

            $chartLabels[] = $date->format('M d');
            $chartValues[] = round($runningProfit, 2);
        }

        $recentBets = $user->bets()
            ->with('event.teamA', 'event.teamB')
            ->latest()
            ->take(4)
            ->get();

        $bestBet = $user->bets()
            ->with('event.teamA', 'event.teamB')
            ->where('status', 'won')
            ->where('profit', '>', 0)
            ->orderByDesc('profit')
            ->first();

        $totalSettled = $wonBets + $lostBets;
        $winRate = $totalSettled > 0 ? round(($wonBets / $totalSettled) * 100) : 0;

        return view('dashboard', [
            'wonBets'  => $wonBets,
            'lostBets' => $lostBets,
            'coins'    => $user->coins,
            'netProfit' => $netProfit,
            'coinsWon' => $coinsWon,
            'coinsLost' => $coinsLost,
            'pendingBets' => $pendingBets,
            'winRate' => $winRate,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'recentBets' => $recentBets,
            'bestBet' => $bestBet,
        ]);
    }
}
