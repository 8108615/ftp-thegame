<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\HistoriaActividad;
use Carbon\Carbon;

class RegistrarCierreSesion
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
    public function handle(Logout $event): void
    {
        // Verificamos que haya un usuario autenticado al momento de salir
        if ($event->user) {
            HistoriaActividad::create([
                'user_id' => $event->user->id,
                'accion' => 'CIERRE_SESION',
                'nombre_archivo' => null,
                'ruta_archivo' => null,
                'estado' => 'EXITOSO',
                'ip_conexion' => request()->ip(),
                'fecha_hora' => Carbon::now(),
            ]);
        }
    }
}
