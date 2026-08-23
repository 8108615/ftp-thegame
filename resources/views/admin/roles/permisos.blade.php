@extends('layouts.admin')

@section('content')
    <div class="relative mb-6 w-full">
        <h3>Permisos del Rol: {{ $rol->name }}</h3>
        <p class="text-slate-500 dark:text-neutral-400">Gestion de permisos para el rol seleccionado.</p>
        <hr>
    </div>

    <form action="{{ route('admin.roles.update_permisos', $rol->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach ($permisos as $modulo => $grupoPermisos)
                        <div class="col-md-3 mb-4">
                            <h4 class="mb-3 text-base font-semibold">{{ $modulo }}</h4>
                            <div class="space-y-2">
                                @foreach ($grupoPermisos as $permiso)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permisos[]"
                                               value="{{ $permiso->name }}" id="permiso_{{ $permiso->id }}"
                                               @checked($rol->hasPermissionTo($permiso->name))>
                                        <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar permisos
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
@endsection
