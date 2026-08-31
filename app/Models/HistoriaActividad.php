<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaActividad extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla en español
    protected $table = 'historial_actividad';

    // Definimos los campos que se pueden llenar masivamente
    protected $fillable = [
        'user_id',
        'accion',
        'nombre_archivo',
        'ruta_archivo',
        'estado',
        'ip_conexion',
        'fecha_hora',
    ];

    // Relación con el modelo User (para saber de qué usuario es el registro)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
