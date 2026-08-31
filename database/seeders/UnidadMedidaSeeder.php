<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            [
                'nom_unidad_medida' => 'Miligramo',
                'abr_unidad_medida' => 'mg',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Gramo',
                'abr_unidad_medida' => 'g',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Microgramo',
                'abr_unidad_medida' => 'mcg',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Mililitro',
                'abr_unidad_medida' => 'ml',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Litro',
                'abr_unidad_medida' => 'l',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Unidad Internacional',
                'abr_unidad_medida' => 'UI',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Porcentaje',
                'abr_unidad_medida' => '%',
                'est_unidad_medida' => 1,
            ],
            [
                'nom_unidad_medida' => 'Miligramo por Mililitro',
                'abr_unidad_medida' => 'mg/ml',
                'est_unidad_medida' => 1,
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate($unidad);
        }
    }
}
