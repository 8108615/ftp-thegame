@extends('layouts.admin')

@push('styles')
    <style>
        .input-group .input-group-text {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
        }

        .input-group .input-group-text i {
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <h3>Ajustes del sistema</h3>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Configuracion general</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ajustes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-lg-8 col-12">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="nombre">Nombre</label><b> (*)</b>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <input id="nombre" type="text" class="form-control"
                                                value="{{ old('nombre', $configuracion->nombre ?? '') }}" name="nombre"
                                                placeholder="Escriba aqui..." required>
                                        </div>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label><b> (*)</b>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input id="email" type="email" class="form-control"
                                                value="{{ old('email', $configuracion->email ?? '') }}" name="email"
                                                placeholder="correo@dominio.com" required>
                                        </div>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="telefono">Telefono</label><b> (*)</b>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input id="telefono" type="text" class="form-control"
                                                value="{{ old('telefono', $configuracion->telefono ?? '') }}"
                                                name="telefono" placeholder="Escriba aqui..." required>
                                        </div>
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="divisa">Divisa</label><b> (*)</b>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                                            <select id="divisa" class="form-select" name="divisa" required>
                                                <option value="">Seleccione una divisa</option>
                                                @foreach ($divisas ?? [] as $codigo => $divisa)
                                                    <option value="{{ $codigo }}"
                                                        {{ old('divisa', $configuracion->divisa ?? '') === $codigo ? 'selected' : '' }}>
                                                        {{ $codigo }} - {{ $divisa['name'] ?? $codigo }}
                                                        @if (!empty($divisa['symbol']))
                                                            ({{ $divisa['symbol'] }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('divisa')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="direccion">Direccion</label><b> (*)</b>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input id="direccion" type="text" class="form-control"
                                                value="{{ old('direccion', $configuracion->direccion ?? '') }}"
                                                name="direccion" placeholder="Escriba aqui..." required>
                                        </div>
                                        @error('direccion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="descripcion">Descripcion</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                            <textarea id="descripcion" class="form-control" name="descripcion" rows="3" placeholder="Descripcion opcional">{{ old('descripcion', $configuracion->descripcion ?? '') }}</textarea>
                                        </div>
                                        @error('descripcion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="web">Sitio web</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                            <input id="web" type="url" class="form-control"
                                                value="{{ old('web', $configuracion->web ?? '') }}" name="web"
                                                placeholder="https://tu-sitio.com">
                                        </div>
                                        @error('web')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="card border">
                                <div class="card-header">
                                    <h5 class="mb-0">Logo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="logo">Cargar logo</label>
                                        <input id="logo" type="file" class="form-control" name="logo"
                                            accept="image/*">
                                        @error('logo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mt-3 text-center">
                                        @php
                                            $logoActual = old(
                                                'logo_preview_url',
                                                isset($configuracion->logo)
                                                    ? asset('storage/' . $configuracion->logo)
                                                    : '',
                                            );
                                        @endphp
                                        <img id="logoPreview" src="{{ $logoActual }}" alt="Previsualizacion del logo"
                                            style="max-width: 100%; max-height: 240px; border: 1px dashed #dce7f1; border-radius: 8px; padding: 8px; background: #f7fbff; {{ $logoActual ? '' : 'display:none;' }}">
                                        <p id="logoPlaceholder"
                                            class="text-muted mb-0 {{ $logoActual ? 'd-none' : '' }}">
                                            Aqui se mostrara la previsualizacion del logo.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle"></i> Guardar ajustes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function() {
            const inputLogo = document.getElementById('logo');
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');

            if (!inputLogo || !preview || !placeholder) {
                return;
            }

            inputLogo.addEventListener('change', function(event) {
                const file = event.target.files && event.target.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'inline-block';
                    placeholder.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
@endpush
