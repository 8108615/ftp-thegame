<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carpeta extends Model
{
    protected $fillable = ['nombre', 'user_id', 'parent_id', 'disk'];

    // Relación: Una carpeta pertenece a un usuario
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relación: Una carpeta puede tener subcarpetas
    public function subcarpetas(): HasMany {
        return $this->hasMany(Carpeta::class, 'parent_id');
    }

    public function compartidos() {
        // Si tus columnas en la tabla 'carpeta_usuario' se llaman distinto, cámbialo aquí
        return $this->belongsToMany(User::class, 'carpeta_usuario', 'carpeta_id', 'user_id')
                    ->withPivot('permiso');
    }

    public function getPath()
    {
        $path = $this->nombre;

        if ($this->parent_id) {
            $parent = \App\Models\Carpeta::find($this->parent_id);
            if ($parent) {
                return $parent->getPath() . '/' . $path;
            }
        }
        return $path;
    }


}
