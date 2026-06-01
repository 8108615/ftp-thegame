@extends('layouts.admin')

@push('styles')
    <style>
        .modal .input-group .input-group-text {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
        }

        .modal .input-group .input-group-text i {
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Roles</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="bi bi-plus-circle"></i> Nuevo rol
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de roles registrados</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.roles.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar rol</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe el nombre del rol">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-success py-2 mb-3" role="alert">
                                Se encontraron {{ $roles->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $rol)
                                        <tr>
                                            <td>{{ $roles->firstItem() + $loop->index }}</td>
                                            <td>{{ $rol->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editRoleModal-{{ $rol->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteRoleModal-{{ $rol->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No hay roles registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($roles->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $roles->firstItem() }} a {{ $roles->lastItem() }} de
                                    {{ $roles->total() }}
                                    registros
                                </small>
                                <div>
                                    {{ $roles->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear rol</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre del rol (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Nombre del rol" required>
                        </div>
                        @if (session('open_modal') === 'createRoleModal')
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($roles as $rol)
        <div class="modal fade" id="editRoleModal-{{ $rol->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.roles.update', $rol->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar rol</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name-{{ $rol->id }}">Nombre del rol (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" name="name" id="name-{{ $rol->id }}" class="form-control"
                                    value="{{ session('open_modal') === 'editRoleModal-' . $rol->id ? old('name', $rol->name) : $rol->name }}"
                                    placeholder="Nombre del rol" required>
                            </div>
                            @if (session('open_modal') === 'editRoleModal-' . $rol->id)
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteRoleModal-{{ $rol->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.roles.destroy', $rol->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar rol</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">¿Está seguro de eliminar el rol <strong>{{ $rol->name }}</strong>?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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
