<?php

namespace Database\Seeders;

use App\Models\Genero;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    public function run(): void
    {
        $generos = [
            ['nom_genero' => 'Masculino'],
            ['nom_genero' => 'Femenino'],
            ['nom_genero' => 'Otro'],
        ];

        foreach ($generos as $genero) {
            Genero::firstOrCreate($genero);
        }
    }
}
