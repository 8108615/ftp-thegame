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
            @can('Guardar usuario')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bi bi-plus-circle"></i> Nuevo usuario
                </button>
            @endcan
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
                                        <th style="width: 80px;">Foto</th>
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
                                            <td>
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Foto" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ optional($user->roles->first())->name ?? 'Sin rol' }}</td>
                                            <td>
                                                @can('Actualizar usuario')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal-{{ $user->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                @endcan

                                                @can('Eliminar usuario')

                                                    @php
                                                        // Verificamos si es el mismo usuario logueado O si el usuario en la fila es Super Admin
                                                        $isSelf = (int) $user->id === (int) auth()->id();
                                                        $isSuperAdmin = $user->hasRole('Super Admin');
                                                        $isDisabled = $isSelf || $isSuperAdmin;
                                                    @endphp

                                                    <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        data-bs-toggle="{{ $isDisabled ? '' : 'modal' }}"
                                                        data-bs-target="#deleteUserModal-{{ $user->id }}"
                                                        {{ $isDisabled ? 'disabled' : '' }}
                                                        title="{{ $isSuperAdmin ? 'No se puede eliminar al Super Admin' : ($isSelf ? 'No puedes eliminarte a ti mismo' : '') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endcan
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
            <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
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
                    <div class="form-group mb-2">
                        <label for="avatar">Foto de perfil</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-image-fill"></i></span>
                            <input type="file" name="avatar" id="avatar-input" class="form-control" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <img id="avatar-preview" src="#" alt="Vista previa" class="mt-2 rounded-circle" style="display:none; width: 100px; height: 100px; object-fit: cover;">
                        @error('avatar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                <form class="modal-content" method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
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

                            @php
                                // Verificamos si el usuario tiene el rol de SUPER ADMIN
                                $isTargetSuperAdmin = $user->hasRole('SUPER ADMIN');

                                // Obtenemos el ID del rol SUPER ADMIN directamente de la colección de roles
                                $superAdminRole = $roles->firstWhere('name', 'SUPER ADMIN');
                                $superAdminRoleId = $superAdminRole ? $superAdminRole->id : null;

                                // Definimos qué ID de rol debe estar seleccionado (si es super admin, forzamos su ID)
                                $selectedRoleId = $isTargetSuperAdmin ? $superAdminRoleId : $currentRoleId;
                            @endphp

                            <select name="role_id" id="edit-role-{{ $user->id }}" class="form-select" required {{ $isTargetSuperAdmin ? 'disabled' : '' }}>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ (string) old('role_id', session('open_modal') === 'editUserModal-' . $user->id ? old('role_id') : $selectedRoleId) === (string) $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Input oculto para que viaje el ID del rol ya que el select está disabled --}}
                            @if($isTargetSuperAdmin && $superAdminRoleId)
                                <input type="hidden" name="role_id" value="{{ $superAdminRoleId }}">
                            @endif
                        </div>

                        @if($isTargetSuperAdmin)
                            <small class="text-muted">El rol de SUPER ADMIN no se puede modificar.</small>
                        @endif

                        @if (session('open_modal') === 'editUserModal-' . $user->id)
                            @error('role_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                        <div class="form-group mb-2">
                            <label for="edit-password-{{ $user->id }}">Contraseña (opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" id="edit-password-{{ $user->id }}"
                                    class="form-control" placeholder="Dejar vacío para mantener actual">
                            </div>
                            @if (session('open_modal') === 'editUserModal-' . $user->id)
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-password-confirmation-{{ $user->id }}">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" name="password_confirmation"
                                    id="edit-password-confirmation-{{ $user->id }}" class="form-control"
                                    placeholder="Repita la contraseña nueva">
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="avatar-input-{{ $user->id }}">Foto de perfil</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image-fill"></i></span>
                                <input type="file" name="avatar" id="avatar-input-{{ $user->id }}"
                                    class="form-control" accept="image/*" onchange="previewImage(event, '{{ $user->id }}')">
                            </div>
                            <img id="avatar-preview-{{ $user->id }}"
                                src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '#' }}"
                                alt="Vista previa"
                                class="mt-2 rounded-circle"
                                style="{{ $user->avatar ? 'display:block;' : 'display:none;' }} width: 100px; height: 100px; object-fit: cover;">
                            @error('avatar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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

        function previewImage(event, userId = '') {
            const suffix = userId ? `-${userId}` : '';
            const preview = document.getElementById(`avatar-preview${suffix}`);

            // Verificamos que existan archivos antes de intentar leerlos
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();

                reader.onload = function() {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(event.target.files[0]);
            }
        }

    </script>
@endpush
