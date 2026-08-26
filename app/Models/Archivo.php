<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = ['nombre', 'ruta', 'mime_type', 'carpeta_id', 'user_id', 'disk', 'size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function compartidos()
    {
        return $this->belongsToMany(User::class, 'archivo_user', 'archivo_id', 'user_id');
    }

    public function getTamanoAttribute()
    {
        // 1. Obtenemos el nombre del disco guardado en la BD (LIGA_BOLIVIANA_d o COMPLETOS_f)
        $diskName = $this->disk ?: 'LIGA_BOLIVIANA_d';

        // 2. Usamos el Storage de Laravel para obtener la ruta física real
        // Esto funciona aunque el disco sea D: o E:
        $path = \Illuminate\Support\Facades\Storage::disk($diskName)->path($this->ruta);

        // 3. Verificamos si existe en el disco que corresponda
        if (file_exists($path)) {
            $bytes = filesize($path);

            if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
            if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
            if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
            return $bytes . ' bytes';
        }

        return 'N/A';
    }
}
