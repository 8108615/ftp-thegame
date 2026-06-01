<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetaController extends Controller
{
    /**
     * Listar carpetas raíz del usuario
     */
    public function index()
    {
        $carpetas = Carpeta::where('user_id', Auth::id())
                           ->whereNull('parent_id')
                           ->get();

        return view('admin.carpetas.index', compact('carpetas'));
    }

    /**
     * Guardar una nueva carpeta
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        Carpeta::create([
            'nombre'    => $request->nombre,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id ?? null
        ]);

        return back()->with('success', 'Carpeta creada correctamente.');
    }

    /**
     * Manejar la subida de archivos grandes por fragmentos (Chunks)
     */
    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileName = $request->input('file_name');
        $carpetaId = $request->input('carpeta_id');

        // Carpeta temporal única: storage/app/temp/user_id/nombre_archivo/
        $tempDir = storage_path('app/temp/' . Auth::id() . '/' . $fileName);

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Guardamos el fragmento (chunk) dentro de esa carpeta
        $file->move($tempDir, 'part_' . $chunkIndex);

        // Verificamos si es el último fragmento para ensamblar
        if ($chunkIndex == $totalChunks - 1) {
            $finalDir = storage_path('app/uploads/' . $carpetaId);
            if (!file_exists($finalDir)) {
                mkdir($finalDir, 0777, true);
            }

            $finalPath = $finalDir . '/' . $fileName;
            $out = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $partPath = $tempDir . '/part_' . $i;
                fwrite($out, file_get_contents($partPath));
                unlink($partPath); // Borrar fragmento
            }
            fclose($out);
            rmdir($tempDir); // Borrar carpeta temporal del archivo

            return response()->json(['message' => 'Archivo subido y ensamblado con éxito']);
        }

        return response()->json(['message' => 'Fragmento ' . $chunkIndex . ' recibido']);
    }
}
