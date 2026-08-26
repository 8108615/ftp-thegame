<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CarpetaController extends Controller
{
    use AuthorizesRequests;
    private function getBreadcrumbs($id)
    {
        $breadcrumbs = [];
        while ($id) {
            $carpeta = \App\Models\Carpeta::find($id);
            if (!$carpeta) break;
            $breadcrumbs[] = $carpeta;
            $id = $carpeta->parent_id;
        }
        return array_reverse($breadcrumbs);
    }


    public function index(Request $request, $id = null)
    {
        $this->sincronizarAutomaticamente(null);
        $userId = auth()->id();

        // 1. Sincronización: Ahora sincroniza el disco correcto automáticamente
        $this->sincronizarAutomaticamente($id);

        $carpetaActual = null;

        if ($id) {
            $carpetaActual = \App\Models\Carpeta::find($id);
            if (!$carpetaActual) abort(404, 'La carpeta no existe.');
            if (!$this->tieneAccesoACarpeta($carpetaActual, $userId)) {
                abort(403, 'No tienes permiso para ver esta carpeta.');
            }
        }

        $breadcrumbs = $id ? $this->getBreadcrumbs($id) : [];

        // 2. Lógica de consulta:
        if ($id) {
            // Dentro de una carpeta, filtramos por el padre
            $queryCarpetas = \App\Models\Carpeta::where('parent_id', $id);
            $queryArchivos = \App\Models\Archivo::where('carpeta_id', $id);
        } else {
            // EN LA RAÍZ:
            // Aquí mostramos todo lo que el usuario posee, sin importar el disco
            $queryCarpetas = \App\Models\Carpeta::where('user_id', $userId)->whereNull('parent_id');
            $queryArchivos = \App\Models\Archivo::where('user_id', $userId)->whereNull('carpeta_id');
        }

        // 3. Búsqueda (Se mantiene igual, funciona globalmente)
        if ($request->filled('search')) {
            $search = $request->search;
            $queryCarpetas->where('nombre', 'like', "%{$search}%");
            $queryArchivos->where('nombre', 'like', "%{$search}%");
        }

        $carpetas = $queryCarpetas->get();
        $archivos = $queryArchivos->get();

        // 4. Elementos compartidos
        $compartidoCarpetas = collect();
        $compartidoArchivos = collect();

        if (is_null($id) && !$request->filled('search')) {
            $compartidoCarpetas = \App\Models\Carpeta::whereHas('compartidos', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('user_id', '!=', $userId)->get();

            $compartidoArchivos = \App\Models\Archivo::whereHas('compartidos', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('user_id', '!=', $userId)->get();
        }

        $usuarios = \App\Models\User::where('id', '!=', $userId)->get();

        return view('admin.carpetas.index', compact(
            'carpetas', 'archivos', 'id', 'carpetaActual', 'usuarios',
            'compartidoCarpetas', 'compartidoArchivos', 'breadcrumbs'
        ));
    }

    private function sincronizarAutomaticamente($carpetaId)
    {
        // Si estamos en la raíz ($carpetaId es null), sincronizamos ambos discos
        if (!$carpetaId) {
            $discosARecorrer = ['LIGA_BOLIVIANA_d', 'COMPLETOS_f'];
            // Lista de carpetas del sistema que NUNCA debemos sincronizar
            $carpetasIgnoradas = ['$RECYCLE.BIN', 'System Volume Information', 'Recovery', 'System.sav'];
            
            foreach ($discosARecorrer as $diskName) {
                $disk = \Illuminate\Support\Facades\Storage::disk($diskName);
                $directoriosReales = $disk->directories(''); // Raíz del disco físico

                foreach ($directoriosReales as $dir) {
                    $nombreCarpeta = basename($dir);
                    
                    // Si la carpeta está en la lista negra o empieza con '$', la ignoramos
                    if (in_array($nombreCarpeta, $carpetasIgnoradas) || str_starts_with($nombreCarpeta, '$')) {
                        continue;
                    }
                    
                    // Verificamos si ya existe en la BD como carpeta raíz en este disco específico
                    $existe = \App\Models\Carpeta::where('nombre', $nombreCarpeta)
                        ->whereNull('parent_id')
                        ->where('disk', $diskName)
                        ->exists();

                    if (!$existe) {
                        \App\Models\Carpeta::create([
                            'nombre'    => $nombreCarpeta,
                            'parent_id' => null,
                            'disk'      => $diskName,
                            'user_id'   => auth()->id()
                        ]);
                    }
                }
            }
            return;
        }

        // --- RESTO DEL CÓDIGO PARA SUBCARPETAS ---
        $carpetaActual = \App\Models\Carpeta::find($carpetaId);
        $diskName = $carpetaActual ? $carpetaActual->disk : 'LIGA_BOLIVIANA_d';
        $disk = \Illuminate\Support\Facades\Storage::disk($diskName);
        $rutaRelativa = $carpetaActual->getPath();

        // 1. LIMPIEZA
        $archivosEnBD = \App\Models\Archivo::where('carpeta_id', $carpetaId)
            ->where('disk', $diskName)
            ->get();
        foreach ($archivosEnBD as $archivoBD) {
            if (!$disk->exists($archivoBD->ruta)) {
                $archivoBD->delete();
            }
        }

        // 2. SINCRONIZAR ARCHIVOS
        $archivosReales = $disk->files($rutaRelativa);
        foreach ($archivosReales as $ruta) {
            if (strpos(basename($ruta), '.') === false) continue;

            if (!\App\Models\Archivo::where('ruta', $ruta)->where('disk', $diskName)->exists()) {
                \App\Models\Archivo::create([
                    'nombre'     => basename($ruta),
                    'ruta'       => $ruta,
                    'disk'       => $diskName,
                    'mime_type'  => $disk->mimeType($ruta),
                    'carpeta_id' => $carpetaId,
                    'user_id'    => auth()->id()
                ]);
            }
        }

        // 3. SINCRONIZAR SUB-CARPETAS
        $directoriosReales = $disk->directories($rutaRelativa);
        foreach ($directoriosReales as $dir) {
            $nombreCarpeta = basename($dir);
            if (!\App\Models\Carpeta::where('nombre', $nombreCarpeta)
                                        ->where('parent_id', $carpetaId)
                                        ->where('disk', $diskName)
                                        ->exists()) {
                \App\Models\Carpeta::create([
                    'nombre'    => $nombreCarpeta,
                    'parent_id' => $carpetaId,
                    'disk'      => $diskName,
                    'user_id'   => auth()->id()
                ]);
            }
        }
    }

    private function tieneAccesoACarpeta($carpeta, $userId)
    {
        // A. ¿Es el dueño?
        if ($carpeta->user_id == $userId) return true;

        // B. ¿Está compartida directamente con este usuario?
        if ($carpeta->compartidos()->where('user_id', $userId)->exists()) return true;

        // C. Si no es dueño ni está compartida, subimos un nivel al padre
        if ($carpeta->parent_id) {
            $padre = \App\Models\Carpeta::find($carpeta->parent_id);
            if ($padre) {
                return $this->tieneAccesoACarpeta($padre, $userId); // Recursividad
            }
        }

        return false;
    }

    public function store(Request $request)
    {
        $this->authorize('crear_carpetas');

        // 1. Limpiamos el nombre para evitar caracteres inválidos en Windows
        $nombreLimpio = trim(preg_replace('#[\\\\/:*"<>|]#', '', $request->nombre));

        // 2. Validamos unicidad dentro del mismo nivel (parent_id)
        $request->merge(['nombre' => $nombreLimpio]);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:carpetas,nombre,NULL,id,parent_id,' . ($request->parent_id ?: 'NULL'),
            'disk'      => 'nullable|in:LIGA_BOLIVIANA_d,COMPLETOS_f',
        ]);

        // LÓGICA DE DISCO:
        // 1. Si estamos dentro de una carpeta, heredamos su disco.
        // 2. Si estamos en la raíz, usamos el disco que venga del formulario (request->disk).
        $padre = $request->parent_id ? \App\Models\Carpeta::find($request->parent_id) : null;
        $diskName = $padre ? $padre->disk : ($request->disk ?: 'LIGA_BOLIVIANA_d');

        // 3. Creamos en BD
        $carpeta = Carpeta::create([
            'nombre' => $nombreLimpio,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id ?: null,
            'disk' => $diskName
        ]);

        // 4. Creamos en el disco D usando la ruta jerárquica
        Storage::disk($diskName)->makeDirectory($carpeta->getPath());

        return back()->with('success', 'Carpeta creada correctamente en ' . $diskName);
    }

    public function uploadChunk(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $this->authorize('subir_archivos');

        $fileName = $request->input('resumableFilename');
        $chunkIndex = $request->input('resumableChunkNumber');
        $totalChunks = $request->input('resumableTotalChunks');
        $carpetaId = $request->input('carpeta_id');

        $tempPath = storage_path('app/temp/');
        if (!file_exists($tempPath)) mkdir($tempPath, 0777, true);

        $request->file('file')->move($tempPath, $fileName . '.part' . $chunkIndex);

        if ($chunkIndex == $totalChunks) {
            // 1. Unir los fragmentos
            $finalPath = $tempPath . $fileName;
            $out = fopen($finalPath, 'ab');
            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkFile = $tempPath . $fileName . '.part' . $i;
                if (file_exists($chunkFile)) {
                    $in = fopen($chunkFile, 'rb');
                    stream_copy_to_stream($in, $out);
                    fclose($in);
                    unlink($chunkFile);
                }
            }
            fclose($out);

            // 2. Determinar la carpeta y el disco
            $carpeta = ($carpetaId && $carpetaId != 0 && $carpetaId != 'null')
                        ? \App\Models\Carpeta::find($carpetaId)
                        : null;

            $rutaDestino = $carpeta ? ($carpeta->getPath() . '/' . $fileName) : $fileName;

            // Si hay carpeta, usamos su disco; si no, el default 'LIGA_BOLIVIANA_d'
            $diskName = $carpeta ? $carpeta->disk : 'LIGA_BOLIVIANA_d';

            // 3. Mover al disco (USANDO STREAMING)
            $disk = \Illuminate\Support\Facades\Storage::disk($diskName);
            $fileStream = fopen($finalPath, 'rb');

            // Intentamos subir al almacenamiento final
            if ($disk->put($rutaDestino, $fileStream)) {
                if (is_resource($fileStream)) fclose($fileStream);

                // 4. ELIMINAR TEMPORAL Y OBTENER TAMAÑO REAL DEL DISCO
                unlink($finalPath);

                // VERIFICACIÓN: Obtenemos el tamaño directamente del disco final, no del local
                $fileSize = $disk->size($rutaDestino);

                // 5. Registrar en BD
                \App\Models\Archivo::create([
                    'nombre'     => $fileName,
                    'ruta'       => $rutaDestino,
                    'disk'       => $diskName,
                    'mime_type'  => $request->file('file')->getClientMimeType(),
                    'carpeta_id' => $carpeta ? $carpeta->id : null,
                    'user_id'    => auth()->id(),
                    'size'       => $fileSize, // Usamos el tamaño verificado del disco
                ]);

                return response()->json(['message' => 'Archivo guardado correctamente']);
            } else {
                // Si falla la subida, cerramos el stream
                if (is_resource($fileStream)) fclose($fileStream);
                return response()->json(['message' => 'Error al subir al disco'], 500);
            }
        }
        return response()->json(['message' => 'Trozo recibido']);
    }

     /*public function destroy($id)
    {
        $this->authorize('eliminar_elementos');
        $userId = auth()->id();
        // Permitimos borrar si es el dueño O si tiene el permiso (ya validado por authorize)
        // Nota: Si quieres restringir más, podrías agregar una validación de "es dueño" aquí
        $item = Carpeta::findOrFail($id);
        // Opcional: Validar que el usuario tenga permiso sobre esta carpeta específica
        // si el sistema fuera más estricto. Por ahora, el 'authorize' global es suficiente.
        $item->delete();
        return back()->with('success', 'Carpeta eliminada correctamente.');
    }*/

    public function descargarMasivo(Request $request)
    {
        $archivosData = $request->input('archivos');
        $carpetasData = $request->input('carpetas');

        // 2. Limpiar los datos (convierte cualquier cosa en un array de IDs)
        $parsear = function($data) {
            if (empty($data)) return [];
            // Si es array, devolverlo tal cual
            if (is_array($data)) return $data;
            // Si es string (ej: "3,16" o "[3,16]"), limpiar caracteres basura
            $limpio = str_replace(['[', ']', '"', "'"], '', $data);
            return explode(',', $limpio);
        };

        $ids = $parsear($archivosData);
        $carpetaIds = $parsear($carpetasData);

        $userId = Auth::id();

        // 3. Consulta maestra (dueño o compartido)
        $todos = Archivo::whereIn('id', $ids)
            ->orWhereIn('carpeta_id', $carpetaIds)
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('compartidos', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->get();

        if ($todos->isEmpty()) {
            return back()->with('error', 'No tienes permisos sobre los archivos seleccionados.');
        }

        $zip = new ZipArchive;
        $zipFileName = 'descarga_' . time() . '.zip';
        $tempPath = storage_path('app/temp');
        $zipPath = $tempPath . '/' . $zipFileName;

        if (!file_exists($tempPath)) mkdir($tempPath, 0755, true);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($todos as $archivo) {
                $disk = \Illuminate\Support\Facades\Storage::disk($archivo->disk);

                if ($disk->exists($archivo->ruta)) {
                    // Obtenemos el contenido del archivo desde el disco correcto
                    $contenido = $disk->get($archivo->ruta);
                    // Lo agregamos al ZIP por nombre
                    $zip->addFromString($archivo->nombre, $contenido);
                }
            }
            $zip->close();
        }

        return file_exists($zipPath)
            ? response()->download($zipPath)->deleteFileAfterSend(true)
            : back()->with('error', 'No se pudo generar el archivo ZIP.');
    }

    public function renombrar(Request $request)
    {
        $this->authorize('editar_elementos');
        $request->validate([
            'nuevo_nombre' => 'required|string|max:255',
            'id' => 'required',
            'tipo' => 'required'
        ]);

        // Limpiamos el nombre
        $nuevoNombreLimpio = trim(preg_replace('#[\\\\/:*"<>|]#', '', $request->nuevo_nombre));

        if ($request->tipo === 'archivo') {
            $item = \App\Models\Archivo::findOrFail($request->id);
            $disk = \Illuminate\Support\Facades\Storage::disk($item->disk);

            $rutaAntigua = $item->ruta;
            $directorio = dirname($rutaAntigua);
            $rutaNueva = ($directorio === '.') ? $nuevoNombreLimpio : $directorio . '/' . $nuevoNombreLimpio;

            // Renombramos en el disco dinámico
            if ($disk->exists($rutaAntigua)) {
                $disk->move($rutaAntigua, $rutaNueva);
            }

            $item->update(['nombre' => $nuevoNombreLimpio, 'ruta' => $rutaNueva]);

        } else {
            $item = \App\Models\Carpeta::findOrFail($request->id);
            $disk = \Illuminate\Support\Facades\Storage::disk($item->disk);

            $rutaAntigua = $item->getPath();

            // 1. Actualizamos nombre en BD
            $item->nombre = $nuevoNombreLimpio;
            $item->save();

            $rutaNueva = $item->getPath();

            // 2. Renombramos en el disco dinámico
            if ($disk->exists($rutaAntigua)) {
                $disk->move($rutaAntigua, $rutaNueva);
            }

            // 3. Actualizar rutas de hijos (Recursivo o búsqueda)
            // Buscamos todos los archivos que contengan la ruta antigua en su ruta
            \App\Models\Archivo::where('carpeta_id', $item->id)
                ->where('disk', $item->disk)
                ->each(function ($archivo) use ($rutaAntigua, $rutaNueva) {
                    $archivo->ruta = str_replace($rutaAntigua, $rutaNueva, $archivo->ruta);
                    $archivo->save();
                });
        }

        return back()->with('success', 'Elemento renombrado correctamente.');
    }

    public function eliminar(Request $request)
    {
        $this->authorize('eliminar_elementos');
        $request->validate(['id' => 'required', 'tipo' => 'required']);

        if ($request->tipo === 'archivo') {
            $item = Archivo::where('user_id', auth()->id())->findOrFail($request->id);

            // Borramos usando el disco específico guardado en la BD
            $disk = \Illuminate\Support\Facades\Storage::disk($item->disk);

            if ($disk->exists($item->ruta)) {
                $disk->delete($item->ruta);
            }
            $item->delete();

        } else {
            $item = Carpeta::where('user_id', auth()->id())->findOrFail($request->id);

            // BORRADO RECURSIVO EN EL DISCO CORRECTO:
            // Obtenemos el disco desde la carpeta (esto garantiza que borramos en D o E según corresponda)
            $disk = \Illuminate\Support\Facades\Storage::disk($item->disk);

            $path = $item->getPath();

            if ($disk->exists($path)) {
                $disk->deleteDirectory($path);
            }

            $item->delete();
        }

        return back()->with('success', 'Elemento eliminado correctamente.');
    }

    public function mover(Request $request)
    {
        $this->authorize('editar_elementos');

        $request->validate([
            'id' => 'required|integer',
            'tipo' => 'required|in:archivo,carpeta',
            'carpeta_destino_id' => 'required'
        ]);

        $destinoId = ($request->carpeta_destino_id == 0 || $request->carpeta_destino_id == 'null') ? null : $request->carpeta_destino_id;
        $destino = $destinoId ? \App\Models\Carpeta::find($destinoId) : null;
        $discoDestino = $destino ? $destino->disk : 'LIGA_BOLIVIANA_d';

        // --- LÓGICA PARA ARCHIVOS ---
        if ($request->tipo === 'archivo') {
            $item = \App\Models\Archivo::findOrFail($request->id);

            $rutaAntigua = $item->ruta;
            $rutaNueva = $destino ? ($destino->getPath() . '/' . $item->nombre) : $item->nombre;

            // ¿Estamos cambiando de disco?
            if ($item->disk !== $discoDestino) {
                // Mover entre discos: Copiar -> Borrar Origen
                \Illuminate\Support\Facades\Storage::disk($discoDestino)->writeStream($rutaNueva, \Illuminate\Support\Facades\Storage::disk($item->disk)->readStream($rutaAntigua));
                \Illuminate\Support\Facades\Storage::disk($item->disk)->delete($rutaAntigua);
            } else {
                // Mover dentro del mismo disco
                \Illuminate\Support\Facades\Storage::disk($item->disk)->move($rutaAntigua, $rutaNueva);
            }

            $item->update(['carpeta_id' => $destinoId, 'ruta' => $rutaNueva, 'disk' => $discoDestino]);
            return back()->with('success', 'Archivo movido correctamente.');
        }

        // --- LÓGICA PARA CARPETAS ---
        else {
            $item = \App\Models\Carpeta::findOrFail($request->id);
            if ($item->id == $destinoId) return back()->with('error', 'No puedes mover una carpeta dentro de sí misma.');

            $rutaAntigua = $item->getPath();
            $item->parent_id = $destinoId;
            $item->disk = $discoDestino; // Actualizamos el disco de la carpeta
            $item->save();
            $rutaNueva = $item->getPath();

            // Mover carpeta en disco
            if ($item->disk !== $discoDestino) {
                // Nota: Mover directorios completos entre discos es complejo.
                // Si esto ocurre mucho, se recomienda advertir al usuario.
                \Illuminate\Support\Facades\Storage::disk($discoDestino)->makeDirectory($rutaNueva);
                // (La lógica recursiva de mover archivos internos debería ir aquí)
            } else {
                \Illuminate\Support\Facades\Storage::disk($item->disk)->move($rutaAntigua, $rutaNueva);
            }

            // Actualizar rutas de hijos
            $this->actualizarRutasHijos($item, $rutaAntigua, $rutaNueva, $discoDestino);

            return back()->with('success', 'Carpeta movida correctamente.');
        }
    }

    // Función auxiliar para actualizar rutas de forma recursiva
    private function actualizarRutasHijos($carpetaPadre, $rutaAntigua, $rutaNueva, $nuevoDisk)
    {
        // 1. Actualizar archivos en esta carpeta
        $archivos = \App\Models\Archivo::where('carpeta_id', $carpetaPadre->id)->get();
        foreach ($archivos as $archivo) {
            $archivo->ruta = str_replace($rutaAntigua, $rutaNueva, $archivo->ruta);
            $archivo->disk = $nuevoDisk; // <--- ACTUALIZAMOS EL DISCO
            $archivo->save();
        }

        // 2. Actualizar subcarpetas (recursividad)
        $subCarpetas = \App\Models\Carpeta::where('parent_id', $carpetaPadre->id)->get();
        foreach ($subCarpetas as $subCarpeta) {
            $subCarpeta->disk = $nuevoDisk; // <--- ACTUALIZAMOS EL DISCO DE LA SUBCARPETA
            $subCarpeta->save();

            // Llamada recursiva pasando el nuevo disco
            $this->actualizarRutasHijos($subCarpeta, $rutaAntigua, $rutaNueva, $nuevoDisk);
        }
    }

    public function compartir(Request $request)
    {
        $this->authorize('editar_elementos');

        // 1. Ajustamos la validación: 'user_id' ahora es un array y validamos cada elemento
        $request->validate([
            'user_id' => 'required|array',       // Esperamos un array
            'user_id.*' => 'exists:users,id',    // Cada ID dentro debe existir
            'id'        => 'required',
            'tipo'      => 'required|in:carpeta,archivo'
        ]);

        // 2. Lógica según el tipo (igual a la tuya)
        if ($request->tipo === 'carpeta') {
            $objeto = \App\Models\Carpeta::where('user_id', auth()->id())->findOrFail($request->id);
        } else {
            $objeto = \App\Models\Archivo::where('user_id', auth()->id())->findOrFail($request->id);
        }

        // 3. Filtramos el arreglo para evitar que el usuario se comparta a sí mismo
        // Eliminamos nuestro propio ID del arreglo si llegara a estar ahí
        $usuariosACompartir = array_diff($request->user_id, [auth()->id()]);

        if (empty($usuariosACompartir)) {
            return back()->with('error', 'No puedes compartir elementos contigo mismo.');
        }

        // 4. Guardamos la relación usando el arreglo limpio
        // syncWithoutDetaching acepta el array de IDs directamente
        $objeto->compartidos()->syncWithoutDetaching($usuariosACompartir);

        return back()->with('success', ucfirst($request->tipo) . ' compartido con éxito.');
    }

    public function gestionarAccesos(Request $request)
    {
        $this->authorize('editar_elementos');

        // 1. Validamos. Permitimos que user_id sea nulo (por si quieres quitar a todos)
        $request->validate([
            'id'        => 'required',
            'tipo'      => 'required|in:carpeta,archivo',
            'user_id'   => 'nullable|array',
            'user_id.*' => 'exists:users,id'
        ]);

        // 2. Buscamos el objeto
        if ($request->tipo === 'carpeta') {
            $objeto = \App\Models\Carpeta::where('user_id', auth()->id())->findOrFail($request->id);
        } else {
            $objeto = \App\Models\Archivo::where('user_id', auth()->id())->findOrFail($request->id);
        }

        // 3. Sincronizamos
        // Si envías [1, 5], el objeto quedará compartido solo con ellos.
        // Si envías [], se quitan todos los accesos.
        $objeto->compartidos()->sync($request->user_id ?? []);

        return back()->with('success', 'Accesos actualizados correctamente.');
    }


    public function descargarArchivo($id)
    {
        // 1. Buscamos el archivo
        $archivo = \App\Models\Archivo::find($id);

        if (!$archivo) {
            return back()->with('error', "No se encontró el archivo con ID $id.");
        }

        // 2. Determinamos el disco dinámicamente desde la BD.
        // Si tu columna en BD se llama de otra forma, cambia 'disk' por el nombre correcto.
        // Si algún archivo antiguo no tiene esta columna, ponemos 'LIGA_BOLIVIANA_d' como respaldo (fallback).
        $diskName = $archivo->disk ?? 'LIGA_BOLIVIANA_d';

        // 3. Verificamos que el disco configurado exista realmente en config/filesystems.php
        if (!config()->has("filesystems.disks.{$diskName}")) {
            dd("Error: El disco '$diskName' no está definido en config/filesystems.php");
        }

        $disk = \Illuminate\Support\Facades\Storage::disk($diskName);

        // 4. Diagnóstico: ¿Existe el archivo físico en ESE disco?
        if (!$disk->exists($archivo->ruta)) {
            dd("Error: El archivo físico no existe en el disco '$diskName'. Ruta buscada: " . $archivo->ruta);
        }

        // 5. Descarga exitosa
        return response()->file($disk->path($archivo->ruta), [
            'Content-Type' => $archivo->mime_type ?: 'video/mp4',
            'Content-Disposition' => 'attachment; filename="' . $archivo->nombre . '"',
        ]);
    }

    public function streamVideo($id)
    {
        $archivo = \App\Models\Archivo::find($id);
        if (!$archivo) abort(404);

        $disk = \Illuminate\Support\Facades\Storage::disk($archivo->disk);

        // 1. Verificamos existencia
        if (!$disk->exists($archivo->ruta)) {
            abort(404, "El video no existe en el disco: " . $archivo->disk);
        }

        // 2. Obtenemos la ruta absoluta física en el disco (más eficiente y seguro para saltos grandes)
        $filePath = $disk->path($archivo->ruta);
        $size = filesize($filePath);
        
        $start = 0;
        $end = $size - 1;

        // 3. Procesar el rango solicitado por el navegador
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = str_replace('bytes=', '', $_SERVER['HTTP_RANGE']);
            $rangeParts = explode('-', $range);
            
            $start = intval($rangeParts[0]);
            
            if (isset($rangeParts[1]) && is_numeric($rangeParts[1])) {
                $end = intval($rangeParts[1]);
            }
        }

        // Asegurar que los rangos sean válidos
        if ($start >= $size || $end >= $size || $start > $end) {
            return response()->stream(function () {}, 416, [
                'Content-Range' => "bytes */$size"
            ]);
        }

        $length = $end - $start + 1;

        // 4. Retornar respuesta optimizada usando lectura directa por chunks
        return response()->stream(function () use ($filePath, $start, $length) {
            $stream = fopen($filePath, 'rb');
            if ($stream === false) {
                return;
            }

            // Posicionamiento preciso del puntero de bytes
            fseek($stream, $start);

            $bytesSent = 0;
            $bufferSize = 512 * 1024; // 512 KB por buffer para evitar sobrecarga de memoria

            while (!feof($stream) && $bytesSent < $length) {
                $readLength = min($bufferSize, $length - $bytesSent);
                $buffer = fread($stream, $readLength);
                
                if ($buffer === false) {
                    break;
                }

                echo $buffer;
                flush();
                
                $bytesSent += strlen($buffer);
            }

            fclose($stream);
        }, 206, [
            'Content-Type' => $archivo->mime_type ?: 'video/mp4',
            'Content-Length' => $length,
            'Accept-Ranges' => 'bytes',
            'Content-Range' => "bytes $start-$end/$size",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
