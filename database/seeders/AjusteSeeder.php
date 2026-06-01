<?php

namespace Database\Seeders;

use App\Models\Ajuste;
use Illuminate\Database\Seeder;

class AjusteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ajuste::query()->firstOrCreate(
            ['email' => 'thegame@gmail.com'],
            [
                'nombre' => 'Ftp The Game',
                'descripcion' => 'Configuracion inicial del sistema',
                'direccion' => 'Doble via la guardia, calle Eucalipto, Nro. 60',
                'telefono' => '76658531',
                'divisa' => 'BOB',
                'logo' => 'ajustes/ChsdWiwBPXA9DFr2KthhYgtMMf6aWVVCRFP4oOQT.jpg',
                'web' => 'https://thegame.srl',
            ]
        );
    }
}
