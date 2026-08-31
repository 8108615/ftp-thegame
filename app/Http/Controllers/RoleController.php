<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Solo los SUPER ADMIN pueden tocar las rutas de permisos
        $this->middleware('role:SUPER ADMIN')->only(['editPermissions', 'updatePermissions']);
    }
    public function index()
    {
        $this->authorize('Ver listado de roles');
        $search = trim((string) request('search', ''));

        $roles = Role::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.roles.index', compact('roles', 'search'));
    }

    public function editPermissions($id)
    {
        $this->authorize('Editar permisos de rol');

        $rol = Role::findOrFail($id);
        // Si prefieres usar la vista moderna 'permisos', la llamamos directamente aquí también:
        $permisos = Permission::all()->groupBy(function ($permiso) {
            if (stripos($permiso->name, 'Ajustes') !== false) return 'Ajustes';
            if (stripos($permiso->name, 'rol') !== false) return 'Roles';
            if (stripos($permiso->name, 'usuario') !== false) return 'Usuarios';
            if (stripos($permiso->name, 'historial') !== false) return 'Historial';
            return 'Archivos';
        });

        return view('admin.roles.permisos', compact('rol', 'permisos'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $this->authorize('Editar permisos de rol');
        $rol = Role::findOrFail($id);
        
        // Sincronizamos usando $request->permisos para estandarizar con la vista
        $rol->syncPermissions($request->permisos ?? []);

        return redirect()->route('admin.roles.index')
            ->with('mensaje', 'Permisos actualizados correctamente.')
            ->with('icono', 'success');
    }

    public function permisos(string $id)
    {
        $rol = Role::findOrFail($id);

        $permisos = Permission::all()->groupBy(function ($permiso) {
            if (stripos($permiso->name, 'Ajustes') !== false) return 'Ajustes';
            if (stripos($permiso->name, 'rol') !== false) return 'Roles';
            if (stripos($permiso->name, 'usuario') !== false) return 'Usuarios';
            if (stripos($permiso->name, 'historial') !== false) return 'Historial';
            return 'Archivos';
        });

        return view('admin.roles.permisos', compact('rol', 'permisos'));
    }

    public function updatePermisos(Request $request, string $id)
    {
        $rol = Role::findOrFail($id);
        $rol->syncPermissions($request->permisos ?? []);

        return redirect()->route('admin.roles.index')
            ->with('mensaje', 'Permisos actualizados correctamente.')
            ->with('icono', 'success');
    }

    public function store(Request $request)
    {
        $this->authorize('Guardar rol');

        $request->merge([
            'name' => mb_strtoupper(trim((string) $request->input('name')), 'UTF-8'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.roles.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createRoleModal');
        }

        $rol = new Role();
        $rol->name = $request->input('name');
        $rol->guard_name = 'web';
        $rol->save();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $this->authorize('Actualizar rol');

        $role = Role::query()->findOrFail($id);

        $request->merge([
            'name' => mb_strtoupper(trim((string) $request->input('name')), 'UTF-8'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.roles.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editRoleModal-' . $role->id);
        }

        $role->name = $request->input('name');
        $role->save();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $this->authorize('Eliminar rol');

        $role = Role::query()->findOrFail($id);

        // Protección: No permitir eliminar el rol SUPER ADMIN
        if ($role->name === 'SUPER ADMIN') {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'No es posible eliminar el rol de SUPER ADMIN.');
        }
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }


}
