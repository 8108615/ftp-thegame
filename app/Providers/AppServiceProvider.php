<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        \Illuminate\Support\Facades\App::setLocale('es');
        Gate::before(function ($user, $ability) {
        return $user->hasRole('SUPER ADMIN') ? true : null;
    });

    // Definimos una Gate que el controlador pueda usar
    Gate::define('descargar-archivo', function ($user, $archivo) {
        return $user->id === $archivo->user_id ||
               $archivo->compartidos()->where('user_id', $user->id)->exists() ||
               $archivo->carpeta->compartidos()->where('user_id', $user->id)->exists();
    });
    }
}
