<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\HistoriaActividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

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

        // Borramos la cookie testigo porque el cierre fue voluntario
        Cookie::queue(Cookie::forget('tracking_user_id'));
    }
}
