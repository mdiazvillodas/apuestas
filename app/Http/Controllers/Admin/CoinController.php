<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CoinGrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoinController extends Controller
{
    private const COINS_TO_ADD = 1000;

    private function ensureAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function preview(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)->get();

        return view('leaderboard.add-coins-preview', [
            'users'  => $users,
            'amount' => self::COINS_TO_ADD,
        ]);
    }

    public function confirm(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)->get();

        DB::transaction(function () use ($users) {
            foreach ($users as $user) {

                // 1. Sumamos coins
                $user->increment('coins', self::COINS_TO_ADD);

                // 2. Registramos el grant
                CoinGrant::create([
                    'user_id'  => $user->id,
                    'admin_id' => auth()->id(),
                    'amount'   => self::COINS_TO_ADD,
                    'reason'   => 'manual grant',
                ]);
            }
        });

        return redirect()
            ->route('leaderboard.index')
            ->with('success', 'Coins added successfully.');
    }
}
