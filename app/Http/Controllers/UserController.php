<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('Ver listado de usuarios'); // Sincronizado

        $search = trim((string) request('search', ''));

        $users = User::query()
            ->with('roles:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $rolesQuery = Role::query()->orderBy('name');

        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            $rolesQuery->where('name', '!=', 'SUPER ADMIN');
        }

        $roles = $rolesQuery->get(['id', 'name']);

        return view('admin.users.index', compact('users', 'roles', 'search'));
    }

    public function store(Request $request)
    {
        $this->authorize('Guardar usuario'); // Sincronizado

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createUserModal');
        }

        $role = Role::findOrFail((int) $request->input('role_id'));

        if ($role->name === 'SUPER ADMIN' && !auth()->user()->hasRole('SUPER ADMIN')) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['role_id' => 'No tienes permiso para asignar el rol de SUPER ADMIN.'])
                ->withInput();
        }

        $user = User::query()->create([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'password' => $request->input('password'),
        ]);

        // Lógica del Avatar
        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            $user->save();
        }

        $user->syncRoles([$role->name]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $this->authorize('Actualizar usuario'); // Sincronizado

        $user = User::query()->findOrFail($id);

        if ($user->hasRole('SUPER ADMIN')) {
            $currentRole = $user->roles->first();
            if ($currentRole) {
                $request->merge(['role_id' => $currentRole->id]);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editUserModal-' . $user->id);
        }

        $role = Role::findOrFail((int) $request->input('role_id'));

        if ($role->name === 'SUPER ADMIN' && !auth()->user()->hasRole('SUPER ADMIN') && !$user->hasRole('SUPER ADMIN')) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['role_id' => 'No tienes permiso para asignar el rol de SUPER ADMIN.'])
                ->withInput();
        }

        $payload = [
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ];

        if (filled($request->input('password'))) {
            $payload['password'] = $request->input('password');
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $payload['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($payload);

        if (!$user->hasRole('SUPER ADMIN') || auth()->user()->hasRole('SUPER ADMIN')) {
            $user->syncRoles([$role->name]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $this->authorize('Eliminar usuario'); // Sincronizado
        $user = User::query()->findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($user->hasRole('SUPER ADMIN')) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No es posible eliminar a un usuario con rol de SUPER ADMIN.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
