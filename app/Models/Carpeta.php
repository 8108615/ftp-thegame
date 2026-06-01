<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carpeta extends Model
{
    protected $fillable = ['nombre', 'user_id', 'parent_id'];

    // Relación: Una carpeta pertenece a un usuario
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relación: Una carpeta puede tener subcarpetas
    public function subcarpetas(): HasMany {
        return $this->hasMany(Carpeta::class, 'parent_id');
    }
}
