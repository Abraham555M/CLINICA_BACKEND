<?php

namespace Database\Seeders;

use App\Models\CondicionDental;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CondicionDentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $condiciones = [
            ['nom_condicion_dental' => 'Sano', 'cod_col_condicion_dental' => '#FFFFFF'],
            ['nom_condicion_dental' => 'Caries', 'cod_col_condicion_dental' => '#FF0000'],
            ['nom_condicion_dental' => 'Obturado / Restaurado', 'cod_col_condicion_dental' => '#0000FF'],
            ['nom_condicion_dental' => 'Ausente / Extraído', 'cod_col_condicion_dental' => '#000000'],
            ['nom_condicion_dental' => 'Sellador', 'cod_col_condicion_dental' => '#00FF00'],
            ['nom_condicion_dental' => 'Tratamiento de Conducto (Endodoncia)', 'cod_col_condicion_dental' => '#FFA500'],
            ['nom_condicion_dental' => 'Corona', 'cod_col_condicion_dental' => '#800080'],
            ['nom_condicion_dental' => 'Implante', 'cod_col_condicion_dental' => '#808080'],
            ['nom_condicion_dental' => 'Prótesis Fija', 'cod_col_condicion_dental' => '#A52A2A'],
            ['nom_condicion_dental' => 'Extracción Indicada', 'cod_col_condicion_dental' => '#FFC0CB'],
        ];

        foreach ($condiciones as $condicion) {
            CondicionDental::firstOrCreate($condicion);
        }
    }
}
