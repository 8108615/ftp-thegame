<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Models\HistoriaActividad;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin';

    /**
     * Este método se ejecuta automáticamente cuando el usuario coloca bien sus credenciales.
     */
    protected function authenticated(Request $request, $user)
    {
        // =========================================================================
        // SUGERENCIA (Comentada): Si en el futuro deseas que los SUPER ADMIN puedan 
        // iniciar sesión en múltiples navegadores/dispositivos sin que el sistema los bote,
        // puedes descomentar la siguiente línea de condición:
        // =========================================================================
        if (!$user->hasRole('SUPER ADMIN')) {

            $cacheKey = 'user_logged_' . $user->id;

            // 1. Verificamos si ya existe una sesión activa registrada en caché para este usuario
            if (Cache::has($cacheKey)) {
                // Cerramos la sesión que acaba de intentar entrar
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirigimos al login con un mensaje de error
                return redirect()->route('login')
                    ->with('error', 'Este usuario ya tiene una sesión activa en otro Dispositivo o Navegador.');
            }

            // 2. Si no está activo en otro lado, guardamos en caché que este usuario está conectado
            Cache::put($cacheKey, true, now()->addMinutes(1440));

        } // <-- Cierre del if de la sugerencia para SUPER ADMIN
    }

    public function logout(Request $request)
    {
        // Limpiamos el caché de sesión activa al cerrar sesión
        if (Auth::check()) {
            Cache::forget('user_logged_' . Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        // Verificamos si existe la cookie testigo pero ya no hay sesión activa
        if (request()->hasCookie('tracking_user_id')) {
            $userId = request()->cookie('tracking_user_id');

            // Registramos en el historial que la sesión expiró por inactividad
            HistoriaActividad::create([
                'user_id' => $userId,
                'accion' => 'SESION_EXPIRADA',
                'nombre_archivo' => null,
                'ruta_archivo' => null,
                'estado' => 'EXITOSO',
                'ip_conexion' => request()->ip(),
                'fecha_hora' => Carbon::now(),
            ]);

            // Limpiamos la cookie para que no se registre dos veces
            Cookie::queue(Cookie::forget('tracking_user_id'));
            
            // Opcional: puedes retornar con un mensaje flash a la vista si lo deseas
            session()->flash('warning', 'Tu sesión ha expirado por inactividad.');
        }

        return view('auth.login'); // O la vista de login que estés utilizando
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}