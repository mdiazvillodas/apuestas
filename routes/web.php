<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\EventController as PlayerEventController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\CoinGrantController;


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
Route::middleware('auth')->group(function () {

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

    Route::post('/leaderboard/coins/preview', [CoinController::class, 'preview'])
        ->name('admin.coins.preview');

    Route::post('/leaderboard/coins/confirm', [CoinController::class, 'confirm'])
        ->name('admin.coins.confirm');    

    Route::get('/events/{event}', [PlayerEventController::class, 'show'])
        ->name('events.show');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

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
    
    
    });
