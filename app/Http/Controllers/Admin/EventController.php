<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bets;


class EventController extends Controller
{

    public function index()
    {
        $events = Event::orderBy('starts_at')->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',

        'betting_opens_at' => 'required|date',
        'betting_closes_at' => 'required|date|after:betting_opens_at',
        'starts_at' => 'required|date|after:betting_closes_at',

        // los IDs pueden venir o no
        'team_a_id' => 'nullable|exists:teams,id',
        'team_b_id' => 'nullable|exists:teams,id',

        // fallback si hay que crear
        'team_a_name' => 'nullable|string|max:100',
        'team_b_name' => 'nullable|string|max:100',

        'team_a_logo' => 'nullable|image',
        'team_b_logo' => 'nullable|image',
    ]);

    /*
    |--------------------------------------------------------------------------
    | TEAM A
    |--------------------------------------------------------------------------
    */
    if ($request->team_a_id) {
        $teamA = \App\Models\Team::find($request->team_a_id);
    } else {
        if (!$request->team_a_name || !$request->file('team_a_logo')) {
            return back()->withErrors('Team A is missing.');
        }

        $logoA = $request->file('team_a_logo')->store('teams', 'public');

        $teamA = \App\Models\Team::create([
            'name' => $request->team_a_name,
            'logo_path' => $logoA,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TEAM B
    |--------------------------------------------------------------------------
    */
    if ($request->team_b_id) {
        $teamB = \App\Models\Team::find($request->team_b_id);
    } else {
        if (!$request->team_b_name || !$request->file('team_b_logo')) {
            return back()->withErrors('Team B is missing.');
        }

        $logoB = $request->file('team_b_logo')->store('teams', 'public');

        $teamB = \App\Models\Team::create([
            'name' => $request->team_b_name,
            'logo_path' => $logoB,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE EVENT
    |--------------------------------------------------------------------------
    */
    Event::create([
        'title' => $request->title,

        'team_a_id' => $teamA->id,
        'team_b_id' => $teamB->id,

        'betting_opens_at' => $request->betting_opens_at,
        'betting_closes_at' => $request->betting_closes_at,
        'starts_at' => $request->starts_at,

        'status' => 'draft',
    ]);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Event created successfully.');
}


    public function settle(Request $request, Event $event)
    {
        $request->validate([
            'result' => 'required|string',
        ]);

        // evitar doble liquidación
        if ($event->status === 'finished') {
            return back()->withErrors('Este evento ya fue liquidado.');
        }

        DB::transaction(function () use ($event, $request) {

            // guardar resultado y cerrar evento
            $event->update([
                'result' => $request->result,
                'status' => 'finished',
            ]);

            // solo apuestas pendientes
            $bets = $event->bets()->where('status', 'pending')->get();

            foreach ($bets as $bet) {
                if ($bet->selection === $event->result) {

                    $payout = $event->payoutFor($bet->selection);
                    $winAmount = round($bet->amount * $payout, 2);

                    $bet->update(['status' => 'won']);
                    $bet->user->increment('coins', $winAmount);

                } else {
                    $bet->update(['status' => 'lost']);
                }
            }
        });

        return back()->with('success', 'Evento liquidado correctamente.');
    }
    public function open(Event $event)
    {
        if ($event->status !== 'draft') {
            return back()->withErrors('El evento ya está abierto o cerrado.');
        }

        $event->update([
            'status' => 'open',
        ]);

        return back()->with('success', 'Evento abierto a apuestas.');
    }

    public function bets()
    {
        $bets = Bet::with(['user', 'event'])
            ->latest()
            ->get();

        return view('admin.bets.index', compact('bets'));
    }


}
