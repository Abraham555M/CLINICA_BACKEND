<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpieza automática de cuentas de pacientes que no fueron activadas tras 3 días (diariamente a las 03:00 AM)
Schedule::command('pacientes:limpiar-inactivos --dias=3')->dailyAt('03:00');