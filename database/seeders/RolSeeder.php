<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nom_rol' => 'Administrador'],
            ['nom_rol' => 'Doctor'],
            ['nom_rol' => 'Recepcionista'],
            ['nom_rol' => 'Paciente'],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate($rol);
        }
    }
}
