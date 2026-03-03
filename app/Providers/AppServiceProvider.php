<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS en producción
        if (app()->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        // Inicializar last_seen_coins si es null
        view()->composer('*', function () {
            if (Auth::check()) {
                $user = Auth::user();

                // Inicialización
                if (is_null($user->last_seen_coins)) {
                    $user->last_seen_coins = $user->coins;
                    $user->save();
                }

                // Si hay diferencia
                if ($user->coins != $user->last_seen_coins) {
                    // NO actualizamos todavía aquí
                    // Solo dejamos que la vista calcule delta
                }
            }
        });

    }
}
