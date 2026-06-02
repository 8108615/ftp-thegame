@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Mis Carpetas</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCarpetaModal">
                <i class="bi bi-folder-plus"></i> Nueva Carpeta
            </button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Subir video (hasta 6GB)</h5>
            <div id="dropzone" class="border p-4 text-center rounded" style="border: 2px dashed #ccc; transition: 0.3s;">
                <button id="browseButton" class="btn btn-primary">
                    <i class="bi bi-cloud-upload"></i> Seleccionar video
                </button>
                <p id="status" class="mt-2 text-muted">Arrastra el archivo aquí o haz clic en seleccionar</p>

                <div class="progress mt-3 d-none" id="progressWrapper" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
        <script>
            let dropzone = document.getElementById('dropzone');

            // 1. PREVENIR COMPORTAMIENTO NATIVO
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            dropzone.addEventListener('dragover', () => dropzone.style.borderColor = '#0d6efd');
            dropzone.addEventListener('dragleave', () => dropzone.style.borderColor = '#ccc');
            dropzone.addEventListener('drop', () => dropzone.style.borderColor = '#ccc');

            // 2. CONFIGURACIÓN DE RESUMABLE
            let r = new Resumable({
                target: '{{ route('admin.carpetas.upload') }}',
                chunkSize: 1 * 1024 * 1024, // Reducido a 1MB (Más pequeño = más estable ante cortes)
                simultaneousUploads: 1,      // ¡CRÍTICO! Sube solo un trozo a la vez para no saturar Apache
                maxChunkRetries: 5,          // Reintentar si un trozo falla
                chunkRetryInterval: 2000,    // Esperar 2 segundos antes de reintentar
                testChunks: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                query: { carpeta_id: 1 }
            });

            r.assignBrowse(document.getElementById('browseButton'));
            r.assignDrop(dropzone);

            // 3. MANEJO DE EVENTOS
            r.on('fileAdded', function(file) {
                document.getElementById('progressWrapper').classList.remove('d-none');
                r.upload();
            });

            r.on('progress', function() {
                let pct = Math.floor(r.progress() * 100);
                document.getElementById('progressBar').style.width = pct + '%';
                document.getElementById('status').innerHTML = pct + '% completado';
            });

            r.on('fileSuccess', function(file, message) {
                document.getElementById('status').innerHTML = "¡Archivo subido correctamente!";
                alert("Subida completada con éxito");
            });

            r.on('fileError', function(file, message) {
                document.getElementById('status').innerHTML = "Error al subir.";
                console.error("Error:", message);
                alert("Hubo un error al subir el archivo. Revisa la consola (F12).");
            });
        </script>
    @endpush
@endsection
