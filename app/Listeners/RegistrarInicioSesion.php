<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\HistoriaActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class RegistrarInicioSesion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // 1. Guardar el registro en la base de datos (como ya lo tienes)
        HistoriaActividad::create([
            'user_id' => $event->user->id,
            'accion' => 'INICIO_SESION',
            'nombre_archivo' => null,
            'ruta_archivo' => null,
            'estado' => 'EXITOSO',
            'ip_conexion' => request()->ip(),
            'fecha_hora' => Carbon::now(),
        ]);

        // 2. Dejar una cookie testigo por 3 días (en minutos = 4320) para detectar si expira por inactividad
        Cookie::queue('tracking_user_id', $event->user->id, 4320);
    }
}
