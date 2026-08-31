<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Definimos los permisos exactos que coinciden con tus rutas
        $permisosPorModulo = [
            'Roles' => ['Ver listado de roles',
                        'Guardar rol',
                        'Actualizar rol',
                        'Eliminar rol', 'Editar permisos de rol'],
            'Usuarios' => ['Ver listado de usuarios',
                            'Guardar usuario',
                            'Actualizar usuario',
                            'Eliminar usuario'],
            'Ajustes' => ['Ver formulario de ajustes',
                            'Editar ajustes'],
            'Archivos' => ['Ver listado de carpetas',
                            'Crear carpeta',
                            'Subir archivos',
                            'Descargar archivos',
                            'Editar elementos',
                            'Eliminar elementos',
                            'Compartir elementos'
                        ],
            'Historial' => [
                'Ver historial de actividad'
            ]
            
        ];

        // 1. Crear los permisos
        foreach ($permisosPorModulo as $modulo => $lista) {
            foreach ($lista as $nombrePermiso) {
                Permission::firstOrCreate(['name' => $nombrePermiso, 'guard_name' => 'web']);
            }
        }

        // 2. Asignar todos los permisos a los roles Totales
        $rolesTotales = ['SUPER ADMIN', 'ADMINISTRADOR'];
        $todosLosPermisos = Permission::all();

        foreach ($rolesTotales as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->first();
            if ($rol) {
                $rol->syncPermissions($todosLosPermisos);
            }
        }
    }
}
