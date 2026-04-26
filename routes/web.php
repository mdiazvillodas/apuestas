<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\EventController as PlayerEventController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\CoinGrantController;
use App\Services\BrevoMailer;
use App\Http\Controllers\Admin\PlatformSettingsController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});


Route::get('/leaderboard', [LeaderboardController::class, 'index'])
    ->name('leaderboard.index');



/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Player routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Lista de eventos disponibles para apostar
    Route::get('/events', [PlayerEventController::class, 'index'])
        ->name('events.index');

    // Crear apuesta
    Route::post('/events/{event}/bet', [BetController::class, 'store'])
        ->name('bets.store');

    // Historial de apuestas del usuario
    Route::get('/my-bets', function () {
        $bets = auth()->user()
            ->bets()
            ->with('event')
            ->latest()
            ->get();

        return view('bets.my', compact('bets'));
    })->name('bets.my');

    Route::get('/my-leagues', [LeagueController::class, 'index'])
        ->name('leagues.index');

    Route::post('/my-leagues', [LeagueController::class, 'store'])
        ->name('leagues.store');

    Route::post('/my-leagues/{league}/join-requests', [LeagueController::class, 'requestJoin'])
        ->name('leagues.join-requests.store');

    Route::post('/my-leagues/{league}/join-requests/{joinRequest}/accept', [LeagueController::class, 'acceptRequest'])
        ->name('leagues.join-requests.accept');

    Route::post('/my-leagues/{league}/join-requests/{joinRequest}/reject', [LeagueController::class, 'rejectRequest'])
        ->name('leagues.join-requests.reject');

    Route::delete('/my-leagues/{league}/members/{user}', [LeagueController::class, 'removeMember'])
        ->name('leagues.members.destroy');

    Route::post('/leaderboard/coins/preview', [CoinController::class, 'preview'])
        ->name('admin.coins.preview');

    Route::post('/leaderboard/coins/confirm', [CoinController::class, 'confirm'])
        ->name('admin.coins.confirm');    

    Route::get('/events/{event}', [PlayerEventController::class, 'show'])
        ->name('events.show');
});



Route::post('/user/consume-coins-delta', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $user->last_seen_coins = $user->coins;
        $user->save();
    }

    return response()->json(['status' => 'ok']);
})->middleware('auth');


/*Route::get('/test-mail', function () {

    BrevoMailer::send(
        'mariano.diazvillodas@gmail.com',
        'NanoBet test',
        '<h1>Email test</h1>'
    );

    return 'mail sent';
});*/


/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/users/{user}/verify-email', [AdminDashboardController::class, 'verifyEmail'])
            ->name('users.verify-email');

        // ABM de eventos
        Route::get('/events', [AdminEventController::class, 'index'])
            ->name('events.index');

        Route::get('/events/create', [AdminEventController::class, 'create'])
            ->name('events.create');

        Route::post('/events', [AdminEventController::class, 'store'])
            ->name('events.store');

        // Liquidar evento
        Route::post('/events/{event}/settle', [AdminEventController::class, 'settle'])
            ->name('events.settle');

        Route::post('/events/{event}/open', [AdminEventController::class, 'open'])
            ->name('events.open');
        Route::get('/bets', [AdminEventController::class, 'bets'])
            ->name('bets.index');    

        Route::get('/teams/search', [TeamController::class, 'search'])
            ->name('teams.search');
        Route::resource('teams', TeamController::class)
            ->except(['show']);
        Route::get('/coin-grants', [CoinGrantController::class, 'index'])
            ->name('coin-grants.index');
        Route::get('/events/import', [AdminEventController::class, 'importForm'])
            ->name('events.import.form');
        Route::post('/events/import', [AdminEventController::class, 'import'])
            ->name('events.import');
        Route::post('/events/import/confirm', [AdminEventController::class, 'importConfirm'])
            ->name('events.import.confirm');
        Route::get('/run-fixture-sync', function () {
            app(\App\Services\FixtureSyncService::class)
                ->sync(
                    config('fixtures.league_id'),
                    config('fixtures.season')
                );

            return 'Fixture sync completed';
        });
        Route::get('/settings', [PlatformSettingsController::class, 'edit'])
            ->name('settings.edit');
        Route::put('/settings', [PlatformSettingsController::class, 'update'])
            ->name('settings.update');        
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])
            ->name('events.edit');

        Route::put('/events/{event}', [AdminEventController::class, 'update'])
            ->name('events.update');

        Route::get('/clear-events', function () {

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            \App\Models\Bet::truncate();
            \App\Models\Event::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return 'Events and bets cleared';

        });
        Route::get('/test-api', function () {

            $results = [];

            for ($i = 0; $i < 7; $i++) {

                $date = now()->addDays($i)->toDateString();

                $response = Http::withHeaders([
                    'x-apisports-key' => config('fixtures.api_key'),
                ])->get('https://v3.football.api-sports.io/fixtures', [
                    'date' => $date
                ]);

                $results[] = [
                    'date' => $date,
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'count' => count($response->json('response') ?? []),
                ];
            }

            return [
                'api_key_present' => config('fixtures.api_key') ? true : false,
                'league_id' => config('fixtures.league_id'),
                'season' => config('fixtures.season'),
                'results' => $results
            ];
        });       

    });
