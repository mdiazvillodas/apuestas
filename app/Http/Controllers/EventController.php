<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Listado de eventos para players
     */
    public function index()
    {
        $now = now();

        $events = Event::with(['bets' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->where('betting_opens_at', '<=', $now)
            ->where('betting_closes_at', '>=', $now->copy()->subHours(12))
            ->orderBy('betting_opens_at')
            ->get();

        return view('events.index', [
            'events' => $events,
        ]);
    }


    /**
     * Detalle de evento / apostar
     */
    public function show(Event $event)
    {
        // ❌ Seguridad dura: nunca se puede apostar fuera de open
        if ($event->computed_status !== 'open') {
            return redirect()
                ->route('events.index')
                ->withErrors('This event is not open for betting.');
        }

        $myBet = $event->bets()
            ->where('user_id', auth()->id())
            ->first();

        return view('events.show', [
            'event' => $event,
            'myBet' => $myBet,
        ]);
    }
}
