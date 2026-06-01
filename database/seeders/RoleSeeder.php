<?php

namespace Database\Seeders;

use Database\Factories\RoleFactory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Role::create(['name' => 'SUPER ADMIN', 'guard_name' => 'web']);
        Role::create(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'EDITOR', 'guard_name' => 'web']);
        
    }
}
