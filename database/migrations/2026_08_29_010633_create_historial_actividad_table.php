<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con el usuario
            $table->string('accion'); // Ej: 'INICIO_SESION', 'DESCARGAS', 'CERRAR SESION'
            $table->string('nombre_archivo')->nullable(); // Nombre del archivo descargado
            $table->string('ruta_archivo')->nullable(); // Ruta o enlace del archivo
            $table->string('estado')->default('EXITOSO'); // 'EXITOSO' o 'FALLIDO'
            $table->string('ip_conexion')->nullable(); // IP desde donde se conectó
            $table->timestamp('fecha_hora'); // Fecha y hora exacta del evento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_actividads');
    }
};
