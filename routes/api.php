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

Route::prefix('/auth')->group(function(){
    Route::post("/login", [AuthController::class, 'login']);
    Route::post("/establecer-password", [AuthController::class, 'establecerPassword']);
    Route::post("/recuperar-password", [AuthController::class, 'recuperarPassword']);
    Route::post("/restablecer-password", [AuthController::class, 'restablecerPassword']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post("/logout", [AuthController::class, 'logout']);
    });
});

Route::prefix('/medicamento')->group(function(){
    Route::get("/listar-medicamentos", [MedicamentoController::class, 'listarMedicamentos']);
    Route::post("/registrar-medicamento", [MedicamentoController::class, 'registrarMedicamento']);
    Route::put("/actualizar-medicamento/{id_medicamento}", [MedicamentoController::class, 'actualizarMedicamento']);
    Route::delete("/eliminar-medicamento/{id_medicamento}", [MedicamentoController::class, 'eliminarMedicamento']);
    Route::get("/select-medicamentos", [MedicamentoController::class, 'selectMedicamentos']);
});

Route::prefix('/servicio')->group(function(){
    Route::get("/select-servicios", [ServicioController::class, 'selectServicios']);
    Route::get("/listar-servicios", [ServicioController::class, 'listarServicios']);
    Route::get("/obtener-servicio/{id_servicio}", [ServicioController::class, 'obtenerServicio']);
    Route::post("/registrar-servicio", [ServicioController::class, 'registrarServicio']);
    Route::put("/actualizar-servicio/{id_servicio}", [ServicioController::class, 'actualizarServicio']);
    Route::delete("/eliminar-servicio/{id_servicio}", [ServicioController::class, 'eliminarServicio']);
});

Route::prefix('/configuracion')->group(function(){
    Route::get("/select-rol", [ConfiguracionController::class, 'selectRol']);
    Route::get("/select-genero", [ConfiguracionController::class, 'selectGenero']);
    Route::get("/select-tipo-antecedente", [ConfiguracionController::class, 'selectTipoAntecedente']);
    Route::get("/select-presentacion-medicamento", [ConfiguracionController::class, 'selectPresentacionMedicamento']);
    Route::get("/select-tipo-servicio", [ConfiguracionController::class, 'selectTipoServicio']);
    Route::get("/select-unidad-medida", [ConfiguracionController::class, 'selectUnidadMedida']);
});
