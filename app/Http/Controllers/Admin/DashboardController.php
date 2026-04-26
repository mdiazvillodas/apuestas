<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBets = Bet::count();
        $totalUsers = User::count();
        $totalCoinsStaked = Bet::sum('amount');

        $dailyBets = Bet::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as bets_count, SUM(amount) as coins_staked')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $chartLabels = [];
        $chartCoins = [];
        $chartBets = [];

        foreach (CarbonPeriod::create(now()->subDays(13)->startOfDay(), now()->startOfDay()) as $date) {
            $day = $date->toDateString();
            $row = $dailyBets->get($day);

            $chartLabels[] = $date->format('M d');
            $chartCoins[] = (int) ($row->coins_staked ?? 0);
            $chartBets[] = (int) ($row->bets_count ?? 0);
        }

        $topBettors = User::query()
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('COUNT(bets.id) as bets_count')
            ->selectRaw('COALESCE(SUM(bets.amount), 0) as coins_staked')
            ->join('bets', 'bets.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('coins_staked')
            ->limit(5)
            ->get();

        $unverifiedUsers = User::query()
            ->whereNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at']);

        return view('admin.dashboard', [
            'totalBets' => $totalBets,
            'totalUsers' => $totalUsers,
            'totalCoinsStaked' => $totalCoinsStaked,
            'chartLabels' => $chartLabels,
            'chartCoins' => $chartCoins,
            'chartBets' => $chartBets,
            'topBettors' => $topBettors,
            'unverifiedUsers' => $unverifiedUsers,
        ]);
    }

    public function verifyEmail(User $user): RedirectResponse
    {
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'User email verified.');
    }
}
