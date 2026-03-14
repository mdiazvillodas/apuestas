<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\FixtureRepositoryInterface;
use App\Repositories\FakeFixtureRepository;
use App\Repositories\ApiFootballRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FixtureRepositoryInterface::class, function () {

            return env('FIXTURE_DATA_SOURCE') === 'api'
                ? new ApiFootballRepository()
                : new FakeFixtureRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        view()->composer('*', function () {
            if (Auth::check()) {
                $user = Auth::user();

                if (is_null($user->last_seen_coins)) {
                    $user->last_seen_coins = $user->coins;
                    $user->save();
                }
            }
        });
    }
}