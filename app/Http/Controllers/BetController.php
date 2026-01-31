<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $user = auth()->user();

        // 1. El evento debe estar abierto
        if ($event->computed_status !== 'open') {
            return back()->withErrors('Este evento no acepta apuestas.');
        }

        // 2. Validación básica
        $request->validate([
            'selection' => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        // 3. Saldo suficiente
        if ($user->coins < $request->amount) {
            return back()->withErrors('Saldo insuficiente.');
        }

        $alreadyBet = Bet::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyBet) {
            return back()->withErrors('Ya apostaste en este evento.');
        }


        // 4. Transacción segura
        DB::transaction(function () use ($user, $event, $request) {
            // descontar coins
            $user->decrement('coins', $request->amount);

            // crear apuesta
            Bet::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'selection' => $request->selection,
                'amount' => $request->amount,
            ]);
        });

        return back()->with('success', 'Apuesta realizada con éxito');
    }
}
