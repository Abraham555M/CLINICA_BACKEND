<?php

namespace Database\Seeders;

use App\Models\TipoServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposServicios = [
            ['nom_tipo_servicio' => 'Odontología General', 'est_tipo_servicio' => 1],  // Limpiezas, curaciones
            ['nom_tipo_servicio' => 'Ortodoncia', 'est_tipo_servicio' => 1],           // Brackets, alineadores
            ['nom_tipo_servicio' => 'Endodoncia', 'est_tipo_servicio' => 1],           // Tratamientos de conducto
            ['nom_tipo_servicio' => 'Odontopediatría', 'est_tipo_servicio' => 1],      // Atención a niños
            ['nom_tipo_servicio' => 'Rehabilitación Oral', 'est_tipo_servicio' => 1],  // Prótesis, coronas
            ['nom_tipo_servicio' => 'Cirugía BUCAL', 'est_tipo_servicio' => 1],        // Extracción de muelas del juicio
            ['nom_tipo_servicio' => 'Estética Dental', 'est_tipo_servicio' => 1],      // Blanqueamiento, carillas
        ];

        foreach ($tiposServicios as $tipo) {
            TipoServicio::firstOrCreate($tipo);
        }
    }
}
