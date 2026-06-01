@extends('layouts.admin')

@push('styles')
    <style>
        .modal .input-group .input-group-text {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal .input-group .input-group-text i {
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Usuarios</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-circle"></i> Nuevo usuario
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de usuarios registrados</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe nombre o correo">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.users.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $users->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $users->firstItem() + $loop->index }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ optional($user->roles->first())->name ?? 'Sin rol' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal-{{ $user->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteUserModal-{{ $user->id }}"
                                                    {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No hay usuarios
                                                registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($users->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de
                                    {{ $users->total() }}
                                    registros
                                </small>
                                <div>
                                    {{ $users->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label for="create-name">Nombre (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="name" id="create-name" class="form-control"
                                value="{{ old('name') }}" placeholder="Nombre del usuario" required>
                        </div>
                        @if (session('open_modal') === 'createUserModal')
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="create-email">Correo (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" id="create-email" class="form-control"
                                value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                        </div>
                        @if (session('open_modal') === 'createUserModal')
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="create-role">Rol (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                            <select name="role_id" id="create-role" class="form-select" required>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (session('open_modal') === 'createUserModal')
                            @error('role_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="create-password">Contrasena (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" id="create-password" class="form-control"
                                placeholder="Minimo 8 caracteres" required>
                        </div>
                        @if (session('open_modal') === 'createUserModal')
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="create-password-confirmation">Confirmar contrasena (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" id="create-password-confirmation"
                                class="form-control" placeholder="Repita la contrasena" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($users as $user)
        @php
            $currentRoleId = optional($user->roles->first())->id;
        @endphp

        <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="edit-name-{{ $user->id }}">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" id="edit-name-{{ $user->id }}"
                                    class="form-control" required
                                    value="{{ session('open_modal') === 'editUserModal-' . $user->id ? old('name', $user->name) : $user->name }}">
                            </div>
                            @if (session('open_modal') === 'editUserModal-' . $user->id)
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-email-{{ $user->id }}">Correo (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="edit-email-{{ $user->id }}"
                                    class="form-control" required
                                    value="{{ session('open_modal') === 'editUserModal-' . $user->id ? old('email', $user->email) : $user->email }}">
                            </div>
                            @if (session('open_modal') === 'editUserModal-' . $user->id)
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-role-{{ $user->id }}">Rol (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                <select name="role_id" id="edit-role-{{ $user->id }}" class="form-select" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ (session('open_modal') === 'editUserModal-' . $user->id ? old('role_id', $currentRoleId) : $currentRoleId) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (session('open_modal') === 'editUserModal-' . $user->id)
                                @error('role_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-password-{{ $user->id }}">Contrasena (opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" id="edit-password-{{ $user->id }}"
                                    class="form-control" placeholder="Dejar vacio para mantener actual">
                            </div>
                            @if (session('open_modal') === 'editUserModal-' . $user->id)
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="edit-password-confirmation-{{ $user->id }}">Confirmar contrasena</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" name="password_confirmation"
                                    id="edit-password-confirmation-{{ $user->id }}" class="form-control"
                                    placeholder="Repita la contrasena nueva">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Esta seguro de eliminar el usuario <strong>{{ $user->name }}</strong>?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger"
                            {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }}>
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        (function() {
            const openModalId = @json(session('open_modal'));
            if (!openModalId) {
                return;
            }

            const modalElement = document.getElementById(openModalId);
            if (!modalElement || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })();
    </script>
@endpush
