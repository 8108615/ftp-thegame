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
        $this->authorize('ver_roles');
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
        $this->authorize('editar_roles');

        $role = Role::findOrFail($id);
        $allPermissions = Permission::all();
        return view('admin.roles.permissions', compact('role', 'allPermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $this->authorize('editar_roles');
        $role = Role::findOrFail($id);
        // syncPermissions elimina los permisos antiguos y asigna solo los nuevos recibidos
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', 'Permisos actualizados correctamente.');
    }

    public function permisos(string $id)
    {
        $rol = Role::findOrFail($id);

        // Obtenemos todos los permisos y los agrupamos para mostrarlos bonito
        $permisos = Permission::all()->groupBy(function ($permiso) {
            if (stripos($permiso->name, 'Ajustes') !== false) return 'Ajustes';
            if (stripos($permiso->name, 'rol') !== false) return 'Roles';
            if (stripos($permiso->name, 'usuario') !== false) return 'Usuarios';
            return 'Archivos'; // Todo lo relacionado a carpetas/archivos cae aquí
        });

        return view('admin.roles.permisos', compact('rol', 'permisos'));
    }

    public function updatePermisos(Request $request, string $id)
    {
        $rol = Role::findOrFail($id);
        // Sincroniza los permisos seleccionados en los checkboxes
        $rol->syncPermissions($request->permisos);

        return redirect()->route('admin.roles.index')
            ->with('mensaje', 'Permisos actualizados correctamente')
            ->with('icono', 'success');
    }

    public function store(Request $request)
    {
        $this->authorize('crear_roles');

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
        $this->authorize('editar_roles');

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
        $this->authorize('eliminar_roles');

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
