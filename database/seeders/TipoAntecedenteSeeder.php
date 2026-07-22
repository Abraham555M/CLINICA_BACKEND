<?php

namespace Database\Seeders;

use App\Models\TipoAntecedente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoAntecedenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nom_tipo_antecedente' => 'Alergia'],               // Ej: Penicilina, Anestesia, Látex
            ['nom_tipo_antecedente' => 'Enfermedad Crónica'],    // Ej: Diabetes, Hipertensión
            ['nom_tipo_antecedente' => 'Hábito'],                 // Ej: Tabaquismo, Bruxismo
            ['nom_tipo_antecedente' => 'Medicación Actual'],     // Ej: Anticoagulantes
            ['nom_tipo_antecedente' => 'Cirugía Previas'],       // Ej: Cirugía maxilofacial
        ];

        foreach ($tipos as $tipo) {
            TipoAntecedente::firstOrCreate($tipo);
        }
    }
}
