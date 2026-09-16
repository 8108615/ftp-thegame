<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AjusteSeeder::class,
        ]);

        // 1. Obtenemos el rol de SUPER ADMIN
        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'SUPER ADMIN',
            'guard_name' => 'web',
        ]);

        // 2. Definimos el listado de los 4 usuarios administradores
        $superAdmins = [
            [
                'name' => 'Erick Morales',
                'email' => 'erick@gmail.com',
                'password' => bcrypt('12345678'),
            ],
            [
                'name' => 'Edison Chavez',
                'email' => 'echavez@thegamesrl.com',
                'password' => bcrypt('12345678'),
            ],
            [
                'name' => 'Daniel Uria',
                'email' => 'duria@thegamesrl.com',
                'password' => bcrypt('12345678'),
            ],
            [
                'name' => 'Jose Maria Quiroga',
                'email' => 'jquiroga@thegamesrl.com',
                'password' => bcrypt('12345678'),
            ],
        ];

        // 3. Recorremos el arreglo para crearlos y asignarles el rol de SUPER ADMIN a todos
        foreach ($superAdmins as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'avatar' => null
                ]
            );

            // Asignamos el rol de SUPER ADMIN (esto asegura que si el usuario ya existía, igual mantenga su rol actualizado)
            $user->syncRoles([$superAdminRole->name]);
        }
    }
}