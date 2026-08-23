<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Admin extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $config = Ajuste::query()->first();

        $stats = [
            'configuracion' => $config ? 1 : 0,
            'roles'    => $user->can('Ver listado de roles') ? Role::count() : 0,
            'usuarios' => $user->can('Ver listado de usuarios') ? User::count() : 0,
            'carpetas' => $user->can('Ver listado de carpetas') ? Carpeta::count() : 0,
            
        ];

        return view('admin.index', compact('config', 'stats'));
    }
}
