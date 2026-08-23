@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <h3>Gestionar Permisos para: <strong>{{ $role->name }}</strong></h3>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.roles.updatePermissions', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Seleccione los permisos permitidos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($allPermissions as $permission)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                            {{ str_replace('_', ' ', strtoupper($permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
