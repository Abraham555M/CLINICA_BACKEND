<?php

use App\Http\Controllers\Api\AntecedenteMedicoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HorarioBloqueadoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\MedicamentoController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\ServicioController;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function(){
    Route::post("/login", [AuthController::class, 'login']);
    Route::post("/registro-paciente", [AuthController::class, 'registroPaciente'])->middleware('throttle:10,1');
    Route::post("/reenviar-activacion", [AuthController::class, 'reenviarActivacion'])->middleware('throttle:5,1');
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

Route::prefix('/paciente')->group(function(){
    Route::get("/listar-pacientes", [PacienteController::class, 'listarPacientes']);
    Route::get("/select-pacientes", [PacienteController::class, 'selectPacientes']);
    Route::post("/registrar-paciente", [PacienteController::class, 'registrarPaciente']);
    Route::get("/obtener-paciente/{id_paciente}", [PacienteController::class, 'obtenerPacientePorId']);
    Route::put("/actualizar-paciente/{id_paciente}", [PacienteController::class, 'actualizarPaciente']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::get("/obtener-mi-perfil", [PacienteController::class, 'obtenerMiPerfil']);
        Route::put("/actualizar-mi-perfil", [PacienteController::class, 'actualizarMiPerfil']);
        Route::get("/mis-dependientes", [PacienteController::class, 'misDependientes']);
        Route::post("/registrar-dependiente", [PacienteController::class, 'registrarDependiente']);
        Route::put("/actualizar-dependiente/{id_paciente}", [PacienteController::class, 'actualizarDependiente']);
        Route::patch("/desvincular-dependiente/{id_paciente}", [PacienteController::class, 'desvincularDependiente']);
    });
});

Route::middleware('auth:sanctum')->prefix('/antecedente')->group(function(){
    Route::get("/mis-antecedentes", [AntecedenteMedicoController::class, 'misAntecedentes']);
    Route::get("/listar-por-paciente/{id_paciente}", [AntecedenteMedicoController::class, 'listarPorPaciente']);
    Route::get("/obtener-antecedente/{id_antecedente}", [AntecedenteMedicoController::class, 'obtenerAntecedente']);
    Route::post("/registrar-antecedente", [AntecedenteMedicoController::class, 'registrarAntecedente']);
    Route::put("/actualizar-antecedente/{id_antecedente}", [AntecedenteMedicoController::class, 'actualizarAntecedente']);
    Route::delete("/eliminar-antecedente/{id_antecedente}", [AntecedenteMedicoController::class, 'eliminarAntecedente']);
});

Route::prefix('/doctor')->group(function(){
    Route::get("/listar-doctores", [DoctorController::class, 'listarDoctores']);
    Route::get("/select-doctores", [DoctorController::class, 'selectDoctores']);
    Route::get("/obtener-doctor/{id_doctor}", [DoctorController::class, 'obtenerDoctorPorId']);
    Route::post("/registrar-doctor", [DoctorController::class, 'registrarDoctor']);
    Route::put("/actualizar-doctor/{id_doctor}", [DoctorController::class, 'actualizarDoctor']);
    Route::delete("/eliminar-doctor/{id_doctor}", [DoctorController::class, 'eliminarDoctor']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::get("/obtener-mi-perfil", [DoctorController::class, 'obtenerMiPerfil']);
    });
});

Route::prefix('/horario')->group(function(){
    Route::get("/disponibilidad", [HorarioController::class, 'obtenerDisponibilidad']);
    Route::get("/listar-horarios", [HorarioController::class, 'listarHorarios']);
    Route::get("/listar-horarios-por-doctor/{id_doctor}", [HorarioController::class, 'listarHorarioPorDoctor']);
    Route::get("/dias-laborables/{id_doctor}", [HorarioController::class, 'diasLaborablesDoctor']);
    Route::get("/obtener-horario/{id_horario}", [HorarioController::class, 'obtenerHorarioPorID']);
    Route::post("/registrar-horario", [HorarioController::class, 'registrarHorarioDoctor']);
    Route::put("/actualizar-horario/{id_horario_atencion}", [HorarioController::class, 'actualizarHorarioDoctor']);
    Route::patch("/cambiar-estado/{id_horario}", [HorarioController::class, 'cambiarEstadoHorario']);
    Route::delete("/eliminar-horario-doctor/{id_horario}", [HorarioController::class, 'eliminarHorario']);
    Route::post("/registrar-semana", [HorarioController::class, 'registrarSemanaDoctor']);
    Route::post("/configurar-semana", [HorarioController::class, 'registrarSemanaDoctor']);
    
    Route::middleware('auth:sanctum')->group(function(){
        Route::get("/mis-horarios", [HorarioController::class, 'misHorarios']);
    });
});

Route::prefix('/horario-bloqueado')->group(function(){
    Route::get("/listar-por-doctor/{id_doctor}", [HorarioBloqueadoController::class, 'listarBloqueosPorDoctor']);
    Route::post("/registrar-bloqueo-horario", [HorarioBloqueadoController::class, 'registrarBloqueoHorario']);
    Route::delete("/eliminar-bloqueo/{id_bloqueo}", [HorarioBloqueadoController::class, 'eliminarHorarioBloqueo']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get("/mis-horarios-bloqueados", [HorarioBloqueadoController::class, 'misHorariosBloqueados']);
    });
});

Route::prefix('/reserva')->group(function(){
    Route::get("/listar-reservas", [ReservaController::class, 'listarReservas']);
    Route::post("/registrar-reserva", [ReservaController::class, 'registrarReserva']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::get("/mis-reservas", [ReservaController::class, 'misReservas']);
        Route::put("/reprogramar-reserva/{id_reserva}", [ReservaController::class, 'reprogramarReserva']);
        Route::patch("/cancelar-reserva/{id_reserva}", [ReservaController::class, 'cancelarReserva']);
    });
});

Route::middleware('auth:sanctum')->prefix('/dashboard')->group(function(){
    Route::get("/paciente", [DashboardController::class, 'dashboardPaciente']);
});