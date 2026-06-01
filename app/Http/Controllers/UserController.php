<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $users = User::query()
            ->with('roles:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.users.index', compact('users', 'roles', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createUserModal');
        }

        $user = User::query()->create([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'password' => $request->input('password'),
        ]);

        $role = Role::query()->findOrFail((int) $request->input('role_id'));
        $user->syncRoles([$role->name]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $user = User::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editUserModal-' . $user->id);
        }

        $payload = [
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ];

        if (filled($request->input('password'))) {
            $payload['password'] = $request->input('password');
        }

        $user->update($payload);

        $role = Role::query()->findOrFail((int) $request->input('role_id'));
        $user->syncRoles([$role->name]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $user = User::query()->findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
