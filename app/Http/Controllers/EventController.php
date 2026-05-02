<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $now = now('UTC');

        $events = Event::with(['bets' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->where('status', '!=', 'draft')
            ->where('betting_opens_at', '<=', $now)
            ->where('betting_closes_at', '>=', $now->copy()->subHours(12))
            ->orderBy('betting_closes_at')
            ->get();

        return view('events.index', [
            'events' => $events,
        ]);
    }

    public function show(Event $event)
    {
        $myBet = $event->bets()
            ->where('user_id', auth()->id())
            ->first();

        if ($event->computed_status !== 'open' && ! $myBet) {
            return redirect()
                ->route('events.index')
                ->withErrors('This event is not open for betting.');
        }

        return view('events.show', [
            'event' => $event,
            'myBet' => $myBet,
        ]);
    }
}
