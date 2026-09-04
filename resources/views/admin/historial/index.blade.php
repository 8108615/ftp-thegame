@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Título y Descripción -->
    <div class="row mb-3 align-items-center">
        <div class="col-12">
            <h2>Historial de Actividad del Sistema</h2>
            <p class="text-muted">Registro detallado de inicios de sesión, cierres y descargas de archivos.</p>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros Profesional -->
    <div class="card shadow-sm border-0 bg-dark mb-3">
        <div class="card-body py-3">
            <form id="searchForm" method="GET" action="{{ route('admin.historial.index') }}" class="row g-3 align-items-center justify-content-between">

                <!-- Buscador General (Alineado a la Izquierda) -->
                <div class="col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-dark text-light border-secondary shadow-none" id="searchInput" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar usuario, email o archivo..." autocomplete="off">
                    </div>
                </div>

                <!-- Filtros de Fecha y Botones (Alineados a la Derecha) -->
                <div class="col-md-7 col-lg-8">
                    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-secondary border-secondary text-light" title="Desde">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" class="form-control bg-dark text-light border-secondary shadow-none" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                        </div>

                        <span class="text-muted d-none d-lg-inline">-</span>

                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-secondary border-secondary text-light" title="Hasta">
                                <i class="bi bi-calendar-check"></i>
                            </span>
                            <input type="date" class="form-control bg-dark text-light border-secondary shadow-none" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>

                        @if(request('buscar') || request('fecha_inicio') || request('fecha_fin'))
                            <a href="{{ route('admin.historial.index') }}" class="btn btn-outline-secondary btn-sm px-2 shadow-sm" title="Limpiar filtros">
                                <i class="bi bi-arrow-counterclockwise"></i> Restablecer
                            </a>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Contenedor y Tabla con el Diseño Original -->
    <div class="card shadow-sm" id="resultadoTabla">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Detalle / Archivo</th>
                            <th>Estado</th>
                            <th>IP Conexión</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiales as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <strong>{{ $item->user->name ?? 'Usuario desconocido' }}</strong>
                                    <br><small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                </td>
                                <td>
                                    @if ($item->accion == 'INICIO_SESION')
                                        <span class="badge bg-success">Inicio de Sesión</span>
                                    @elseif ($item->accion == 'CIERRE_SESION')
                                        <span class="badge bg-secondary">Cierre de Sesión</span>
                                    @else
                                        <span class="badge bg-primary">{{ $item->accion }}</span>
                                    @endif
                                </td>

                                <!-- Columna 4: Detalle / Archivo -->
                                <td>
                                    @if ($item->accion == 'INICIO_SESION')
                                        <span class="text-success fw-semibold"><i class="bi bi-box-arrow-in-right me-1"></i> Inicio de Sesión</span>
                                    @elseif ($item->accion == 'CIERRE_SESION')
                                        <span class="text-secondary fw-semibold"><i class="bi bi-box-arrow-right me-1"></i> Cierre de Sesión</span>
                                    @else
                                        @php
                                            $rutaCompleta = $item->ruta_archivo ?? '';
                                            $partes = explode('/', $rutaCompleta);
                                            $nombreArchivo = array_pop($partes);
                                            $estructuraCarpetas = implode(' / ', $partes);
                                        @endphp

                                        <div class="d-flex flex-column">
                                            @if(!empty($estructuraCarpetas))
                                                <span class="text-info small">
                                                    <i class="bi bi-folder-fill me-1"></i> {{ $estructuraCarpetas }}
                                                </span>
                                            @endif
                                            <span class="fw-bold text-light">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i> {{ $nombreArchivo ?: 'N/A' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if($item->estado == 'EXITOSO')
                                        <span class="text-success fw-bold">Exitoso</span>
                                    @elseif($item->estado == 'PROCESANDO DESCARGA')
                                        <span class="text-warning fw-bold">Procesando Descarga</span>
                                    @elseif($item->estado == 'CANCELADO')
                                        <span class="text-info fw-bold">Cancelado</span>
                                    @else
                                        <span class="text-danger fw-bold">Fallido</span>
                                    @endif
                                </td>
                                <td><code>{{ $item->ip_conexion ?? 'N/A' }}</code></td>
                                <td>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No hay registros en el historial todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($historiales->count() > 0)
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3 px-4 pb-3">
                    <small class="text-muted">
                        Mostrando {{ $historiales->firstItem() }} a {{ $historiales->lastItem() }} de {{ $historiales->total() }} registros
                    </small>
                    <div>
                        {{ $historiales->links('vendor.pagination.bootstrap-5-no-summary') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);

            let query = searchInput.value;
            let fechaInicio = document.getElementById('fecha_inicio').value;
            let fechaFin = document.getElementById('fecha_fin').value;

            timeout = setTimeout(function() {
                let url = `{{ route('admin.historial.index') }}?buscar=${encodeURIComponent(query)}&fecha_inicio=${encodeURIComponent(fechaInicio)}&fecha_fin=${encodeURIComponent(fechaFin)}`;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let nuevaTabla = doc.getElementById('resultadoTabla');

                    if (nuevaTabla) {
                        document.getElementById('resultadoTabla').innerHTML = nuevaTabla.innerHTML;
                    }

                    const inputActual = document.getElementById('searchInput');
                    inputActual.focus();
                    let val = inputActual.value;
                    inputActual.value = '';
                    inputActual.value = val;
                })
                .catch(error => console.error('Error:', error));
            }, 400);
        });
    });
</script>
@endpush
