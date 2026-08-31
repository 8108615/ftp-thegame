<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\HistoriaActividad;
use Carbon\Carbon;

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
        // Guardamos el registro en nuestra tabla historial_actividad
        HistoriaActividad::create([
            'user_id' => $event->user->id,
            'accion' => 'INICIO_SESION',
            'nombre_archivo' => null,
            'ruta_archivo' => null,
            'estado' => 'EXITOSO',
            'ip_conexion' => request()->ip(), // Captura la IP desde donde ingresó
            'fecha_hora' => Carbon::now(),     // Fecha y hora exacta
        ]);
    }
}
