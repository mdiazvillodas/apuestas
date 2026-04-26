<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BetController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $user = auth()->user();

        if ($event->computed_status !== 'open') {
            return back()->withErrors('This event is not open for betting.');
        }

        $request->validate([
            'selection' => 'required|in:team_a,team_b,draw',
            'amount' => 'required|integer|min:1|max:' . $user->coins,
        ]);

        DB::transaction(function () use ($user, $event, $request) {
            $lockedUser = $user->newQuery()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if ($lockedUser->coins < $request->amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient balance.',
                ]);
            }

            $alreadyBet = Bet::where('user_id', $lockedUser->id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->exists();

            if ($alreadyBet) {
                throw ValidationException::withMessages([
                    'selection' => 'You already placed a bet on this event.',
                ]);
            }

            $lockedUser->decrement('coins', $request->amount);

            Bet::create([
                'user_id' => $lockedUser->id,
                'event_id' => $event->id,
                'selection' => $request->selection,
                'amount' => $request->amount,
            ]);
        });

        return back()->with('success', 'Bet placed successfully.');
    }
}
