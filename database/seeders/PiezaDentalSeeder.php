<?php

namespace Database\Seeders;

use App\Enums\TipoPiezaDental;
use App\Models\PiezaDental;
use Illuminate\Database\Seeder;

class PiezaDentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $piezas = [
            // Cuadrante 1 (Superior Derecho)
            ['num_pieza_dental' => 18, 'nom_pieza_dental' => 'Tercer Molar Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 17, 'nom_pieza_dental' => 'Segundo Molar Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 16, 'nom_pieza_dental' => 'Primer Molar Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 15, 'nom_pieza_dental' => 'Segundo Premolar Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 14, 'nom_pieza_dental' => 'Primer Premolar Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 13, 'nom_pieza_dental' => 'Canino Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::CANINO->value],
            ['num_pieza_dental' => 12, 'nom_pieza_dental' => 'Incisivo Lateral Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 11, 'nom_pieza_dental' => 'Incisivo Central Superior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],

            // Cuadrante 2 (Superior Izquierdo)
            ['num_pieza_dental' => 21, 'nom_pieza_dental' => 'Incisivo Central Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 22, 'nom_pieza_dental' => 'Incisivo Lateral Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 23, 'nom_pieza_dental' => 'Canino Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::CANINO->value],
            ['num_pieza_dental' => 24, 'nom_pieza_dental' => 'Primer Premolar Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 25, 'nom_pieza_dental' => 'Segundo Premolar Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 26, 'nom_pieza_dental' => 'Primer Molar Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 27, 'nom_pieza_dental' => 'Segundo Molar Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 28, 'nom_pieza_dental' => 'Tercer Molar Superior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],

            // Cuadrante 3 (Inferior Izquierdo)
            ['num_pieza_dental' => 38, 'nom_pieza_dental' => 'Tercer Molar Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 37, 'nom_pieza_dental' => 'Segundo Molar Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 36, 'nom_pieza_dental' => 'Primer Molar Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 35, 'nom_pieza_dental' => 'Segundo Premolar Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 34, 'nom_pieza_dental' => 'Primer Premolar Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 33, 'nom_pieza_dental' => 'Canino Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::CANINO->value],
            ['num_pieza_dental' => 32, 'nom_pieza_dental' => 'Incisivo Lateral Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 31, 'nom_pieza_dental' => 'Incisivo Central Inferior Izquierdo', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],

            // Cuadrante 4 (Inferior Derecho)
            ['num_pieza_dental' => 41, 'nom_pieza_dental' => 'Incisivo Central Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 42, 'nom_pieza_dental' => 'Incisivo Lateral Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::INCISIVO->value],
            ['num_pieza_dental' => 43, 'nom_pieza_dental' => 'Canino Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::CANINO->value],
            ['num_pieza_dental' => 44, 'nom_pieza_dental' => 'Primer Premolar Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 45, 'nom_pieza_dental' => 'Segundo Premolar Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::PREMOLAR->value],
            ['num_pieza_dental' => 46, 'nom_pieza_dental' => 'Primer Molar Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 47, 'nom_pieza_dental' => 'Segundo Molar Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
            ['num_pieza_dental' => 48, 'nom_pieza_dental' => 'Tercer Molar Inferior Derecho', 'tip_det_pieza_dental' => TipoPiezaDental::MOLAR->value],
        ];

        foreach ($piezas as $pieza) {
            PiezaDental::firstOrCreate(
                ['num_pieza_dental' => $pieza['num_pieza_dental']],
                $pieza
            );
        }
    }
}