<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetaController extends Controller
{
    public function index()
    {
        $carpetas = Carpeta::where('user_id', Auth::id())
                           ->whereNull('parent_id')
                           ->get();

        return view('admin.carpetas.index', compact('carpetas'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        Carpeta::create([
            'nombre'    => $request->nombre,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id ?? null
        ]);

        return back()->with('success', 'Carpeta creada correctamente.');
    }

    public function uploadChunk(Request $request)
    {
        $fileName = $request->input('resumableFilename');
        $chunkIndex = $request->input('resumableChunkNumber');
        $totalChunks = $request->input('resumableTotalChunks');

        // 1. Usamos una carpeta temporal en el storage de Laravel para mayor seguridad
        $tempPath = storage_path('app/temp/');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        // 2. Guardar el trozo actual
        $request->file('file')->move($tempPath, $fileName . '.part' . $chunkIndex);

        // 3. Verificar si es el último trozo
        if ($chunkIndex == $totalChunks) {

            set_time_limit(0);
            ini_set('memory_limit', '2048M');

            // Definimos la ruta final en public/uploads
            $finalPath = public_path('uploads/' . $fileName);
            $out = fopen($finalPath, 'ab');

            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkFile = $tempPath . $fileName . '.part' . $i;

                if (file_exists($chunkFile)) {
                    $in = fopen($chunkFile, 'rb');
                    stream_copy_to_stream($in, $out, 1024 * 1024);
                    fclose($in);
                    unlink($chunkFile); // Borramos el trozo
                }
            }
            fclose($out);

            return response()->json(['message' => 'Archivo ensamblado correctamente en public/uploads']);
        }

        return response()->json(['message' => 'Trozo recibido']);
    }
}
