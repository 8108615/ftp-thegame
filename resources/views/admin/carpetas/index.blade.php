@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="breadcrumb-container">
                @if ($id)
                    <a href="{{ $carpetaActual->parent_id ? route('admin.carpetas.index', $carpetaActual->parent_id) : route('admin.carpetas.index') }}"
                        class="btn btn-sm btn-outline-info me-2">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                @endif


                <h5 class="mb-0 d-flex align-items-center">
                    <a href="{{ route('admin.carpetas.index') }}" class="breadcrumb-item-link">
                        <i class="bi bi-house-door"></i> Mis Archivos
                    </a>


                    @foreach ($breadcrumbs as $breadcrumb)
                        <span class="breadcrumb-separator mx-2">/</span>
                        <a href="{{ route('admin.carpetas.index', $breadcrumb->id) }}" class="breadcrumb-item-link">
                            {{ $breadcrumb->nombre }}
                        </a>
                    @endforeach


                </h5>
            </div>


            <div class="d-flex align-items-center gap-2">
                @can('Descargar archivos')
                    <button id="downloadSelectedBtn" class="btn btn-success d-none">
                        <i class="bi bi-download"></i> Descargar Seleccionados
                    </button>
                @endcan
            </div>
        </div>

        <di class="card bg-transparent  border-0 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-0 mt-2">Gestión de archivos</h5>

                {{-- Contenedor de botones y progreso --}}
                <div class="d-flex flex-column align-items-end" style="width: 300px;">
                    <div class="d-flex gap-2">
                        @can('Crear carpeta')
                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalCarpeta">
                                <i class="bi bi-folder-plus"></i> Nueva Carpeta
                            </button>
                        @endcan
                        @can('Subir archivos')
                            <button type="button" id="btn-subir-custom" class="btn btn-sm btn-primary">
                                <i class="bi bi-cloud-arrow-up"></i> Subir Archivo
                            </button>
                            <input type="file" id="input-archivo-oculto" hidden>
                        @endcan
                    </div>

                    {{-- Contenedor de progreso alineado a los botones --}}
                    <div id="file-list-container" class="mt-2 w-100">
                        </div>
                </div>
            </div>


            {{-- Contenedor de progreso (aparecerá solo al subir) --}}
            <div id="progreso-container" class="mt-3" style="display:none;">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <form action="{{ route('admin.carpetas.index', $id ?? '') }}" method="GET" class="d-flex gap-2"
                style="max-width: 450px;">
                <input type="text" name="search"
                    class="form-control form-control-sm bg-dark text-white border-secondary"
                    placeholder="Buscar archivos o carpetas..." value="{{ request('search') }}">

                <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <i class="bi bi-search"></i> Buscar
                </button>

                @if (request('search'))
                    <a href="{{ route('admin.carpetas.index', $id ?? '') }}" class="btn btn-sm btn-secondary"
                        title="Limpiar búsqueda">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="row mb-5">
        @foreach ($carpetas as $carpeta)
            <div class="col-md-2 text-center position-relative">
                <div class="position-absolute" style="top: 0px; left: 10px; z-index: 10;">
                    <input type="checkbox" class="carpeta-check" value="{{ $carpeta->id }}">
                </div>
                @can('Ver listado de carpetas')
                    <a href="{{ route('admin.carpetas.index', $carpeta->id) }}" style="text-decoration:none;">
                        <i class="bi bi-folder-fill" style="font-size: 3rem; color: #f1c40f;"></i>
                        <p class="fw-bold text-white mt-2">{{ $carpeta->nombre }}</p>
                    </a>
                @endcan


                <div class="dropdown position-absolute" style="top: 0; right: 20px;">

                    <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i>
                    </button>


                    <ul class="dropdown-menu dropdown-menu-dark">
                        @can('Editar elementos')
                            <li><a class="dropdown-item" href="#"
                                    onclick="abrirModalRenombrar({{ $carpeta->id }}, 'carpeta', '{{ $carpeta->nombre }}')">Renombrar</a>
                            </li>
                        @endcan

                        @can('Editar elementos')
                            <li>
                                <a class="dropdown-item" href="#"
                                    onclick="abrirModalMover({{ $carpeta->id }}, 'carpeta')">Mover a...
                                </a>
                            </li>
                        @endcan

                        @can('Compartir elementos')
                            @if ($carpeta->compartidos->isNotEmpty())
                                <li>
                                    <a class="dropdown-item text-warning" href="#"
                                        onclick='abrirModalDejarDeCompartir({{ $carpeta->id }}, "carpeta", @json($carpeta->compartidos))'>
                                        Gestionar accesos
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="abrirModalCompartir({{ $carpeta->id }}, 'carpeta')">Compartir Carpeta</a>
                                </li>
                            @endif
                        @endcan

                        @can('Eliminar elementos')
                            <li>

                                <form action="{{ route('admin.archivos.eliminar') }}" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar esta carpeta y todo su contenido?');">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $carpeta->id }}">
                                    <input type="hidden" name="tipo" value="carpeta">
                                    <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                </form>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        @foreach ($archivos as $archivo)
            <div class="col-md-3 mb-4">
                <div class="card h-100 position-relative" style="background: #1e1e2d; border: 1px solid #333;">
                    <div class="position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                        <input type="checkbox" class="archivo-check" value="{{ $archivo->id }}">
                    </div>
                    @can('Descargar archivos')
                        <a href="{{ route('archivo.download', $archivo->id) }}" class="position-absolute"
                            style="top: 10px; right: 40px; z-index: 10; color: #28a745; font-size: 1.5rem;">
                            <i class="bi bi-download"></i>
                        </a>
                    @endcan

                    <div class="position-absolute" style="top: 5px; right: 5px; z-index: 10;">
                        <div class="dropdown">

                            <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown"><i
                                    class="bi bi-three-dots-vertical" style="font-size: 1.5rem;"></i></button>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                @can('Editar elementos')
                                    <li><a class="dropdown-item" href="#"
                                            onclick="abrirModalRenombrar({{ $archivo->id }}, 'archivo', '{{ $archivo->nombre }}')">Renombrar</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            onclick="abrirModalMover({{ $archivo->id }}, 'archivo')">Mover a...</a>
                                    </li>
                                @endcan
                                @can('Compartir elementos')
                                    <li>
                                        @if ($archivo->compartidos->isNotEmpty())
                                            {{-- Si ya tiene compartidos, permitir gestionar --}}
                                            <a class="dropdown-item text-warning" href="#"
                                                onclick='abrirModalDejarDeCompartir({{ $archivo->id }}, "archivo", @json($archivo->compartidos))'>
                                                Gestionar accesos
                                            </a>
                                        @else
                                            {{-- Si NO tiene, mostrar la opción de compartir por primera vez --}}
                                            <a class="dropdown-item" href="#"
                                                onclick="abrirModalCompartir({{ $archivo->id }}, 'archivo')">
                                                Compartir Archivo
                                            </a>
                                        @endif
                                    </li>
                                @endcan

                                @can('Eliminar elementos')
                                    <li>
                                        <form id="form-eliminar-{{ $archivo->id }}"
                                            action="{{ route('admin.archivos.eliminar') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $archivo->id }}">
                                            <input type="hidden" name="tipo" value="archivo">
                                            <button type="button" class="dropdown-item text-danger"
                                                onclick="confirmarEliminar({{ $archivo->id }})">Eliminar</button>
                                        </form>
                                    </li>
                                @endcan

                            </ul>

                        </div>
                    </div>
                    <div class="card-body text-center d-flex flex-column align-items-center mt-4">
                        @php
                            $mime = $archivo->mime_type;
                            $ext = strtolower(pathinfo($archivo->nombre, PATHINFO_EXTENSION));
                            // Asegurar que detecte extensiones comunes si el mime_type es genérico
                            if ($ext === 'mp4') {
                                $mime = 'video/mp4';
                            }
                        @endphp

                        @if (str_contains($mime, 'video'))
                            <div style="width: 100%; height: 150px; background: #000; display: flex; align-items: center; justify-content: center; border-radius: 5px; position: relative;">
                                <i class="bi bi-play-circle" style="font-size: 3rem; color: #fff; cursor: pointer; position: absolute;"
                                onclick="this.style.display='none'; this.nextElementSibling.style.display='block'; this.nextElementSibling.play();"></i>

                                <video width="100%" height="100%" controls style="object-fit: contain; display: none;" preload="none">
                                    <source src="{{ route('archivo.download', $archivo->id) }}" type="{{ $mime }}">
                                </video>
                            </div>
                        @elseif(str_contains($mime, 'image'))
                            <img src="{{ route('archivo.download', $archivo->id) }}" class="img-fluid"
                                style="height: 150px; object-fit: contain;" loading="lazy">

                        @else

                            <div
                                style="height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                @if (in_array($ext, ['doc', 'docx']))
                                    <i class="bi bi-file-earmark-word" style="font-size: 3rem; color: #2b5797;"></i>
                                @elseif(in_array($ext, ['pdf']))
                                    <i class="bi bi-file-earmark-pdf" style="font-size: 3rem; color: #d40f0f;"></i>
                                @elseif(in_array($ext, ['zip', 'rar', '7z']))
                                    <i class="bi bi-file-earmark-zip" style="font-size: 3rem; color: #f1c40f;"></i>
                                @else
                                    <i class="bi bi-file-earmark" style="font-size: 3rem; color: #fff;"></i>
                                @endif
                                <span class="badge bg-secondary mt-2">{{ strtoupper($ext) }}</span>
                            </div>
                        @endif

                        <p class="mt-2 text-white"
                            style="font-size: 0.85rem; word-break: break-all; height: 40px; overflow: hidden;"
                            title="{{ $archivo->nombre }}">
                            {{ $archivo->nombre }}
                        </p>
                        <small class="text-muted d-block" style="margin-top: -10px; margin-bottom: 5px;">
                            {{ $archivo->tamano }}
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (($compartidoCarpetas ?? collect())->isNotEmpty() || ($compartidoArchivos ?? collect())->isNotEmpty())
        <div class="row mb-5">
            {{-- Sección de Carpetas Compartidas --}}

            @foreach ($compartidoCarpetas ?? [] as $carpeta)
                <div class="col-md-2 text-center">
                    <a href="{{ route('admin.carpetas.index', $carpeta->id) }}" style="text-decoration:none;">
                        <i class="bi bi-folder-fill" style="font-size: 3rem; color: #d0db34;"></i>
                        <p class="fw-bold text-white mt-2">{{ $carpeta->nombre }}</p>
                    </a>

                </div>
            @endforeach



            {{-- Sección de Archivos Compartidos --}}
            @foreach ($compartidoArchivos ?? [] as $archivo)
                {{-- 1. Validación de seguridad para evitar "Trying to get property of non-object" --}}
                @if($archivo && isset($archivo->id))
                    <div class="col-md-3 mb-4">
                        <div class="card h-100" style="background: #4f502c; border: 1px solid #34495e;">
                            <div class="card-body text-center d-flex flex-column align-items-center">
                                @php
                                    // 2. Uso de operador de fusión de null (??) para evitar errores si está vacío
                                    $mime = $archivo->mime_type ?? 'application/octet-stream';
                                @endphp

                                {{-- Lógica de previsualización --}}
                                @if (str_contains($mime, 'video'))
                                    <div style="width: 100%; height: 150px; background: #000; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                        <video width="100%" height="150" controls preload="metadata">
                                            {{-- 3. Aquí usamos la ruta de stream, y NO la de descarga --}}
                                            <source src="{{ url('/admin/archivos/stream/' . $archivo->id) }}" type="video/mp4">
                                            Tu navegador no soporta video.
                                        </video>
                                    </div>
                                @elseif(str_contains($mime, 'image'))
                                    <img src="{{ url($archivo->ruta) }}" class="img-fluid" style="height: 150px; object-fit: contain;">
                                @else
                                    <i class="bi bi-file-earmark" style="font-size: 3rem; color: #fff;"></i>
                                @endif

                                <p class="text-white mt-2" style="font-size: 0.85rem; word-break: break-all;">
                                    {{ $archivo->nombre ?? 'Archivo sin nombre' }}
                                </p>
                                <small class="text-info d-block mb-2">De: {{ $archivo->user->name ?? 'Usuario' }}</small>

                                {{-- BOTÓN DE DESCARGA (Este sigue apuntando a download, está bien aquí) --}}
                                <a href="{{ route('archivo.download', $archivo->id) }}" class="btn btn-sm btn-outline-light mt-auto">
                                    <i class="bi bi-download"></i> Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="modal fade" id="modalCarpeta" tabindex="-1">

        <div class="modal-dialog">
            <form action="{{ route('admin.carpetas.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $id ?? '' }}">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Carpeta</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la carpeta"
                        required>

                    <div class="mt-3">
                        <label>Seleccionar Unidad:</label>
                        <select name="disk" class="form-control" required>
                            <option value="LIGA_BOLIVIANA_d">Unidad D (ENTELGOL)</option>
                            <option value="COMPLETOS_f">Unidad F (PARTIDOS CLEAN)</option>
                            <option value="COMPLETOS_f">Unidad F (PROGRAMAS)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>

    </div>

    <div class="modal fade" id="modalRenombrar" tabindex="-1">

        <div class="modal-dialog">
            <form action="{{ route('admin.archivos.renombrar') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="renombrar_id">
                <input type="hidden" name="tipo" id="renombrar_tipo">
                <div class="modal-header">
                    <h5 class="modal-title">Renombrar</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="nuevo_nombre" id="nuevo_nombre" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>

    </div>

    <div class="modal fade" id="modalMover" tabindex="-1">

        <div class="modal-dialog">
            <form action="{{ route('admin.archivos.mover') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="mover_archivo_id">
                <input type="hidden" name="tipo" id="mover_tipo">
                <div class="modal-header">
                    <h5 class="modal-title">Mover elemento a:</h5>
                </div>
                <div class="modal-body">
                    <select name="carpeta_destino_id" class="form-select">
                        <option value="0">Raíz (Mis Archivos)</option>
                        @foreach (\App\Models\Carpeta::where('user_id', auth()->id())->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirmar Movimiento</button>
                </div>
            </form>
        </div>

    </div>

    <div class="modal fade" id="modalCompartir" tabindex="-1" aria-labelledby="modalCompartirLabel"
        aria-hidden="true">

        <div class="modal-dialog">
            <form action="{{ route('admin.carpetas.compartir') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="compartir_id">
                <input type="hidden" name="tipo" id="compartir_tipo">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCompartirLabel">Compartir elemento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Seleccionar usuarios:</label>
                        <select id="select-usuarios" name="user_id[]" class="form-select" multiple="multiple"
                            style="width: 100%;">
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Compartir</button>
                </div>
            </form>
        </div>

    </div>

    <div class="modal fade" id="modalDejarDeCompartir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.carpetas.gestionarAccesos') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="dejar_id">
                <input type="hidden" name="tipo" id="dejar_tipo">

                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="bi bi-person-gear"></i> Gestionar accesos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <label class="form-label fw-bold">Usuarios con acceso:</label>
                    <p class="text-muted small">Selecciona nuevos usuarios para añadir, o elimina a los existentes para
                        revocar.</p>

                    <select id="select-dejar-compartir" name="user_id[]" class="form-select" multiple="multiple"
                        style="width: 100%;">
                        @foreach (\App\Models\User::where('id', '!=', auth()->id())->get() as $usuario)
                            <option value="{{ $usuario->id }}">
                                {{ $usuario->name }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>


        <form id="formDescargaMasiva" action="{{ route('admin.archivos.descargarMasivo') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="archivos" id="inputArchivos">
            <input type="hidden" name="carpetas" id="inputCarpetas">
        </form>



    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
       $(document).ready(function() {
        let r;

        // 1. Función para configurar los eventos de Resumable
        function configurarEventosResumable(instancia) {
            instancia.on('fileAdded', function (file) {
                let fileHtml = `
                <div class="d-flex align-items-center mb-3 p-2 bg-dark rounded border border-secondary" id="file-${file.uniqueIdentifier}">
                    <div class="me-3"><i class="bi bi-file-earmark-text" style="font-size: 1.5rem; color: #435ebe;"></i></div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-white">${file.fileName}</small>
                            <small class="text-muted" id="progress-text-${file.uniqueIdentifier}">0%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar-${file.uniqueIdentifier}" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="ms-3" id="status-icon-${file.uniqueIdentifier}">
                        <i class="bi bi-clock text-warning"></i>
                    </div>
                </div>`;
                document.getElementById('file-list-container').insertAdjacentHTML('beforeend', fileHtml);
                instancia.upload();
            });

            instancia.on('fileProgress', function (file) {
                let progress = Math.floor(file.progress() * 100);
                let bar = document.getElementById(`progress-bar-${file.uniqueIdentifier}`);
                let text = document.getElementById(`progress-text-${file.uniqueIdentifier}`);
                if (bar) bar.style.width = progress + '%';
                if (text) text.innerText = progress + '%';
            });

            instancia.on('fileSuccess', function (file) {
                let statusIcon = document.getElementById(`status-icon-${file.uniqueIdentifier}`);
                if (statusIcon) statusIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';

                // Cambiamos el texto de progreso a 100%
                let text = document.getElementById(`progress-text-${file.uniqueIdentifier}`);
                if (text) text.innerText = '100%';

                // Mostrar alerta de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Subida completa!',
                    text: 'El archivo se ha subido correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Recargar la página automáticamente después de que la alerta termine
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });

            instancia.on('fileError', function (file, message) {
                let statusIcon = document.getElementById(`status-icon-${file.uniqueIdentifier}`);
                if (statusIcon) statusIcon.innerHTML = '<i class="bi bi-x-circle-fill text-danger" title="Error"></i>';
                Swal.fire('Error', 'Hubo un problema al subir: ' + file.fileName, 'error');
            });
        }

        // 2. Lógica del botón de subir
        const btnSubir = document.getElementById('btn-subir-custom');
        const inputArchivo = document.getElementById('input-archivo-oculto');

        btnSubir.addEventListener('click', () => inputArchivo.click());

        inputArchivo.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                document.getElementById('file-list-container').innerHTML = '';

                r = new Resumable({
                    target: '{{ route('admin.carpetas.upload') }}',
                    simultaneousUploads: 3,
                    chunkSize: 1 * 1024 * 1024,
                    testChunks: false,
                    forceChunkSize: true,
                    query: { carpeta_id: '{{ $id ?? 0 }}', _token: '{{ csrf_token() }}' }
                });

                // Aplicamos los eventos a la nueva instancia
                configurarEventosResumable(r);

                r.addFiles(this.files);
                r.upload();
            }
        });

            // 2. Configuración de Select2 (Ahora está correctamente dentro del document.ready)
            $('#select-usuarios').select2({
                dropdownParent: $('#modalCompartir'),
                placeholder: "Selecciona uno o más usuarios",
                allowClear: true,
                width: '100%'
            });

            $('#select-dejar-compartir').select2({
                dropdownParent: $('#modalDejarDeCompartir'),
                placeholder: "Selecciona usuarios...",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        }); // <-- Este es el cierre correcto del $(document).ready



        // --- FUNCIONES GLOBALES (Fuera del document.ready) ---
        function abrirModalRenombrar(id, tipo, nombreActual) {
            document.getElementById('renombrar_id').value = id;
            document.getElementById('renombrar_tipo').value = tipo;
            document.getElementById('nuevo_nombre').value = nombreActual;
            new bootstrap.Modal(document.getElementById('modalRenombrar')).show();
        }

        function abrirModalMover(id, tipo) {
            document.getElementById('mover_archivo_id').value = id;
            document.getElementById('mover_tipo').value = tipo;
            new bootstrap.Modal(document.getElementById('modalMover')).show();
        }

        function abrirModalCompartir(id, tipo) {
            document.getElementById('compartir_id').value = id;
            document.getElementById('compartir_tipo').value = tipo;
            new bootstrap.Modal(document.getElementById('modalCompartir')).show();
        }

        function abrirModalDejarDeCompartir(id, tipo, compartidos) {
            document.getElementById('dejar_id').value = id;
            document.getElementById('dejar_tipo').value = tipo;
            const select = $('#select-dejar-compartir');
            select.empty();
            const todosLosUsuarios = @json(\App\Models\User::where('id', '!=', auth()->id())->get());
            todosLosUsuarios.forEach(u => {
                let esta = compartidos.some(c => c.id === u.id);
                select.append(new Option(u.name + ' (' + u.email + ')', u.id, esta, esta));
            });
            select.trigger('change');
            $('#modalDejarDeCompartir').modal('show');
        }
        $(document).on('change', '.archivo-check, .carpeta-check', function() {
            let haySeleccionados = $('.archivo-check:checked, .carpeta-check:checked').length > 0;
            if (haySeleccionados) {
                $('#downloadSelectedBtn').removeClass('d-none');
            } else {
                $('#downloadSelectedBtn').addClass('d-none');
            }
        });

        $('#downloadSelectedBtn').click(function() {
            let archivos = [];
            let carpetas = [];

            $('.archivo-check:checked').each(function() { archivos.push($(this).val()); });
            $('.carpeta-check:checked').each(function() { carpetas.push($(this).val()); });

            // AQUÍ ESTÁ EL CAMBIO CRUCIAL:
            // .join(',') convierte [3, 16] en el texto plano "3,16"
            $('#inputArchivos').val(archivos.join(','));
            $('#inputCarpetas').val(carpetas.join(','));

            // Enviamos
            $('#formDescargaMasiva').submit();

            // Limpiamos
            setTimeout(() => {
                $('.archivo-check, .carpeta-check').prop('checked', false);
                $('#downloadSelectedBtn').addClass('d-none');
            }, 500);
        });

        function confirmarEliminar(id) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¿Deseas Eliminar este archivo?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminarlo",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('form-eliminar-' + id).submit();
            });
        }
    </script>
@endpush
@endsection
