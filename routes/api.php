<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\MedicamentoController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\ServicioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/medicamento')->group(function(){
    Route::get("/listar-medicamentos", [MedicamentoController::class, 'listarMedicamentos']);
    Route::post("/registrar-medicamento", [MedicamentoController::class, 'registrarMedicamento']);
    Route::put("/actualizar-medicamento/{id_medicamento}", [MedicamentoController::class, 'actualizarMedicamento']);
    Route::delete("/eliminar-medicamento/{id_medicamento}", [MedicamentoController::class, 'eliminarMedicamento']);
    Route::get("/select-medicamentos", [MedicamentoController::class, 'selectMedicamentos']);
});