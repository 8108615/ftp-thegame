<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }

    public function carpetasCompartidas() {
        return $this->belongsToMany(Carpeta::class, 'carpeta_usuario', 'user_id', 'carpeta_id')
                    ->withPivot('permiso');
    }

    // AGREGA ESTA NUEVA RELACIÓN PARA ARCHIVOS
    public function archivosCompartidos() {
        return $this->belongsToMany(Archivo::class, 'archivo_user', 'user_id', 'archivo_id');
    }

}
