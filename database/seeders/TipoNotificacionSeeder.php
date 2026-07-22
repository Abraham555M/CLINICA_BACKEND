<?php

namespace Database\Seeders;

use App\Models\Notificacion;
use App\Models\TipoNotificacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoNotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notificaciones = [
            ['nom_tipo_notificacion' => 'Recordatorio de Cita', 'est_tipo_notificacion' => 1],
            ['nom_tipo_notificacion' => 'Confirmación de Cita', 'est_tipo_notificacion' => 1],
            ['nom_tipo_notificacion' => 'Cancelación de Cita', 'est_tipo_notificacion' => 1],
            ['nom_tipo_notificacion' => 'Reprogramación de Cita', 'est_tipo_notificacion' => 1],
            ['nom_tipo_notificacion' => 'Aviso de Pago', 'est_tipo_notificacion' => 1],
        ];

        foreach ($notificaciones as $notificacion) {
            TipoNotificacion::firstOrCreate($notificacion);
        }
    }
}
