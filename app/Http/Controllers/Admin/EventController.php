<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Bet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bets;
use App\Models\Team;
use Carbon\Carbon;



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
                    $profit = $winAmount - $bet->amount;

                    $bet->update([
                        'status' => 'won',
                        'payout_multiplier' => $payout,
                        'payout_amount' => $winAmount,
                        'profit' => $profit,
                    ]);

                    $bet->user->increment('coins', $winAmount);

                } else {

                    $bet->update([
                        'status' => 'lost',
                        'payout_multiplier' => null,
                        'payout_amount' => 0,
                        'profit' => -$bet->amount,
                    ]);
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

    public function edit(Event $event)
{
    return view('admin.events.edit', compact('event'));
}

public function update(Request $request, Event $event)
{
    $request->validate([
        'betting_opens_at'  => 'nullable|date',
        'betting_closes_at' => 'nullable|date|after_or_equal:betting_opens_at',
        'starts_at'         => 'nullable|date|after_or_equal:betting_closes_at',
    ]);

    $event->update([
        'betting_opens_at'  => $request->betting_opens_at,
        'betting_closes_at' => $request->betting_closes_at,
        'starts_at'         => $request->starts_at,
    ]);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Evento actualizado correctamente');
}
// MUESTRA EL FORM
public function importForm()
{
    return view('admin.events.import');
}

public function import(Request $request)
{
    $request->validate([
        'csv' => 'required|file|mimes:csv,txt',
    ]);

    $rows = array_map('str_getcsv', file($request->file('csv')->getRealPath()));
    $header = array_map('trim', array_shift($rows));

    $preview = [];
    $hasErrors = false;

    foreach ($rows as $index => $row) {
        $data = array_combine($header, $row);

        $teamA = Team::find($data['team_a_id'] ?? null);
        $teamB = Team::find($data['team_b_id'] ?? null);

        // Valores por defecto (CLAVE para evitar undefined)
        $valid = false;
        $error = null;
        $opensAt = $closesAt = $startsAt = null;

        // Parseo de fechas (dd/mm/yyyy HH:ii)
        try {
            if (!empty($data['betting_opens_at'])) {
                $opensAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['betting_opens_at'])
                );
            }

            if (!empty($data['betting_closes_at'])) {
                $closesAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['betting_closes_at'])
                );
            }

            if (!empty($data['starts_at'])) {
                $startsAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['starts_at'])
                );
            }
        } catch (\Exception $e) {
            $error = 'Invalid date format';
        }

        // Validaciones
        $validTeams = $teamA && $teamB;

        if (!$error) {
            if (!$validTeams) {
                $error = 'Invalid team';
            } elseif (!$opensAt || !$closesAt || !$startsAt) {
                $error = 'Missing date';
            } elseif (!$opensAt->lt($closesAt)) {
                $error = 'Betting close must be after betting open';
            } elseif (!$closesAt->lt($startsAt)) {
                $error = 'Event start must be after betting close';
            } else {
                $valid = true;
            }
        }

        if (!$valid) {
            $hasErrors = true;
        }

        $preview[] = [
            'row' => $index + 2,
            'title' => $data['title'] ?? null,

            'team_a_id' => $data['team_a_id'] ?? null,
            'team_a_name' => $teamA ? $teamA->name : null,

            'team_b_id' => $data['team_b_id'] ?? null,
            'team_b_name' => $teamB ? $teamB->name : null,

            'betting_opens_at' => $data['betting_opens_at'] ?? null,
            'betting_closes_at' => $data['betting_closes_at'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,

            'valid' => $valid,
            'error' => $error,
        ];
    }

    return view('admin.events.import-preview', [
        'preview' => $preview,
        'hasErrors' => $hasErrors,
        'csvData' => json_encode($rows),
        'csvHeader' => json_encode($header),
    ]);
}

public function importConfirm(Request $request)
{
    $rows = json_decode($request->csv_data, true);
    $header = json_decode($request->csv_header, true);

    DB::transaction(function () use ($rows, $header) {

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            // Validar equipos otra vez (defensivo)
            if (
                !Team::find($data['team_a_id'] ?? null) ||
                !Team::find($data['team_b_id'] ?? null)
            ) {
                throw new \Exception('Invalid team in CSV');
            }

            // Parsear fechas (MISMO formato que preview)
            try {
                $opensAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['betting_opens_at'])
                );

                $closesAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['betting_closes_at'])
                );

                $startsAt = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    trim($data['starts_at'])
                );
            } catch (\Exception $e) {
                throw new \Exception('Invalid date format in CSV');
            }

            Event::create([
                'title' => $data['title'],
                'team_a_id' => $data['team_a_id'],
                'team_b_id' => $data['team_b_id'],

                // Fechas YA normalizadas
                'betting_opens_at' => $opensAt,
                'betting_closes_at' => $closesAt,
                'starts_at' => $startsAt,

                // SIEMPRE draft
                'status' => 'draft',
            ]);
        }
    });

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Events imported successfully');
}


}

