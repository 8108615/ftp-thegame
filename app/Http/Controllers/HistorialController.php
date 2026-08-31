<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HistoriaActividad;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('Ver historial de actividad');
        // Creamos la consulta base ordenada del más reciente al más antiguo con la relación del usuario
        $query = HistoriaActividad::with('user')->orderBy('fecha_hora', 'desc');

        // Filtrar por texto de búsqueda (nombre, email o ruta/archivo)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                // Buscar en los campos de la tabla relacionada 'user' (nombre o email)
                $q->orWhereHas('user', function($userQuery) use ($busqueda) {
                    $userQuery->where('name', 'LIKE', "%{$busqueda}%")
                            ->orWhere('email', 'LIKE', "%{$busqueda}%");
                })
                // O buscar en la ruta del archivo de la propia tabla historial
                ->orWhere('ruta_archivo', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtrar por fecha de inicio si se envió
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_inicio);
        }

        // Filtrar por fecha de fin si se envió
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_fin);
        }

        // Ejecutamos la paginación y retenemos los parámetros de la URL
        $historiales = $query->paginate(10)->appends($request->query());

        return view('admin.historial.index', compact('historiales'));
    }
}
