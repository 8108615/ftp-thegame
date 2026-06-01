<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
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

    public function store(Request $request)
    {
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
        $role = Role::query()->findOrFail($id);
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
