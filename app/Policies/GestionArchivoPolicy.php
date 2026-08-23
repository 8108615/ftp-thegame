<?php

namespace App\Policies;

use App\Models\User;

class GestionArchivoPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, $item)
    {
        // Solo permitir si el usuario es el dueño o tiene un rol con permiso de edición
        return $user->id === $item->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, $item)
    {
        // Solo el dueño o el admin puede eliminar
        return $user->id === $item->user_id || $user->hasRole('admin');
    }
}
