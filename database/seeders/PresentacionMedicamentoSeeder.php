<?php

namespace Database\Seeders;

use App\Models\PresentacionMedicamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresentacionMedicamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentaciones = [
            ['nom_presentacion' => 'Tableta / Comprimido', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Cápsula', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Jarabe', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Suspensión', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Solución Inyectable (Ampolla)', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Crema / Pomada', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Gel', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Gotas Oftálmicas / Oticas', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Inhalador / Aerosol', 'est_presentacion' => 1],
            ['nom_presentacion' => 'Supositorio', 'est_presentacion' => 1],
        ];

        foreach ($presentaciones as $presentacion) {
            PresentacionMedicamento::firstOrCreate($presentacion);
        }
    }
}
