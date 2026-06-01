<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => mb_strtoupper(fake()->unique()->jobTitle(), 'UTF-8'),
            'guard_name' => 'web',
        ];
    }
}
