<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Admin extends Controller
{
    public function index()
    {
        $config = Ajuste::query()->first();

        $stats = [
            'configuracion' => $config ? 1 : 0,
            'roles' => Role::count(),
            'usuarios' => User::count(),

        ];
        return view('admin.index', compact('config', 'stats'));
    }
}
