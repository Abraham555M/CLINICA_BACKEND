<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Horario\ActualizarHorarioRequest;
use App\Http\Requests\Horario\ConfigurarSemanaRequest;
use App\Http\Requests\Horario\ConsultarDisponibilidadRequest;
use App\Http\Requests\Horario\RegistrarHorarioRequest;
use App\Http\Resources\HorarioResource;
use App\Models\Doctor;
use App\Models\HorarioAtencion;
use App\Models\HorarioBloqueado;
use App\Services\DisponibilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{
    public function registrarHorarioDoctor(RegistrarHorarioRequest $request)
    {
        // 1. Obtener datos validados
        $data = $request->validated();

        // 2. Validar que no exista choque o solapamiento con otro horario activo del mismo doctor
        if (HorarioAtencion::existeSolapamiento($data['id_doctor'], $data['dia_sem_horario_atencion'], $data['hor_ini_horario_atencion'], $data['hor_fin_horario_atencion'])) {
            return $this->errorResponse("El rango de horario ingresado se cruza con otro horario de atención ya registrado para este doctor.", 422);
        }

        // 3. Crear el horario
        $horario = HorarioAtencion::create($data);
        $horario->load('doctor.usuario');
        
        // 4. Retornar respuesta estructurada
        return $this->successResponse(
            new HorarioResource($horario), 
            "Horario de atención registrado correctamente.", 
            201
        );
    }

    public function listarHorarios()
    {
        $horarios = HorarioAtencion::with('doctor.usuario')
            ->orderBy('id_doctor')
            ->orderBy('dia_sem_horario_atencion')
            ->orderBy('hor_ini_horario_atencion')
            ->get();

        return $this->successResponse(
            HorarioResource::collection($horarios),
            "Lista de horarios de atención obtenida con éxito."
        );
    }

    public function listarHorarioPorDoctor($id_doctor)
    {
        $doctor = Doctor::find($id_doctor);

        if (!$doctor) {
            return $this->errorResponse("Doctor no encontrado.", 404);
        }

        $horarios = HorarioAtencion::with('doctor.usuario')
            ->where('id_doctor', $id_doctor)
            ->orderBy('dia_sem_horario_atencion', 'asc')
            ->orderBy('hor_ini_horario_atencion', 'asc')
            ->get();
            
        return $this->successResponse(
            HorarioResource::collection($horarios), 
            "Horarios de atención del doctor obtenidos con éxito."
        ); 
    }

    public function diasLaborablesDoctor(Request $request, $id_doctor)
    {
        $doctor = Doctor::with('usuario')->find($id_doctor);

        if (!$doctor) {
            return $this->errorResponse("Doctor no encontrado.", 404);
        }

        // Obtener los horarios de atención activos del doctor
        $horarios = HorarioAtencion::where('id_doctor', $id_doctor)
            ->where('est_horario_atencion', 1)
            ->orderBy('dia_sem_horario_atencion', 'asc')
            ->orderBy('hor_ini_horario_atencion', 'asc')
            ->get();

        // Días de la semana únicos en los que atiende (1 = Lunes, ..., 7 = Domingo)
        $diasSemanaActivos = $horarios->pluck('dia_sem_horario_atencion')
            ->map(fn($dia) => (int) $dia)
            ->unique()
            ->values();

        // Consulta de fechas bloqueadas
        $queryBloqueos = HorarioBloqueado::where('id_doctor', $id_doctor);

        if ($request->filled('desde')) {
            $queryBloqueos->whereDate('fch_blq_horario_bloqueado', '>=', $request->input('desde'));
        } elseif ($request->filled('mes') && $request->filled('anio')) {
            $queryBloqueos->whereMonth('fch_blq_horario_bloqueado', $request->input('mes'))
                          ->whereYear('fch_blq_horario_bloqueado', $request->input('anio'));
        } else {
            // Por defecto, fechas bloqueadas desde hoy en adelante
            $queryBloqueos->whereDate('fch_blq_horario_bloqueado', '>=', now()->toDateString());
        }

        if ($request->filled('hasta')) {
            $queryBloqueos->whereDate('fch_blq_horario_bloqueado', '<=', $request->input('hasta'));
        }

        $fechasBloqueadas = $queryBloqueos->orderBy('fch_blq_horario_bloqueado', 'asc')
            ->pluck('fch_blq_horario_bloqueado')
            ->map(function ($fch) {
                return $fch instanceof \Carbon\CarbonInterface
                    ? $fch->format('Y-m-d')
                    : substr((string) $fch, 0, 10);
            })
            ->unique()
            ->values();

        return $this->successResponse([
            'id_doctor'           => (int) $doctor->id_doctor,
            'nom_doctor'          => trim($doctor->usuario?->nom_usuario . ' ' . $doctor->usuario?->ape_usuario),
            'dias_semana_activos' => $diasSemanaActivos,
            'fechas_bloqueadas'   => $fechasBloqueadas,
        ], 'Días laborables y bloqueos del doctor obtenidos con éxito.');
    }

    public function actualizarHorarioDoctor(ActualizarHorarioRequest $request, $id_horario_atencion)
    {
        $horario = HorarioAtencion::find($id_horario_atencion);

        if (!$horario) {
            return $this->errorResponse("Horario de atención no encontrado.", 404);
        }

        $data = $request->validated();
        $estado = $data['est_horario_atencion'] ?? $horario->est_horario_atencion;

        // 1. Validar choque o solapamiento con otro horario activo del mismo doctor (excluyendo el registro actual)
        if ($estado && HorarioAtencion::existeSolapamiento($data['id_doctor'], $data['dia_sem_horario_atencion'], $data['hor_ini_horario_atencion'], $data['hor_fin_horario_atencion'], $horario->id_horario_atencion)) {
            return $this->errorResponse("El rango de horario ingresado se cruza con otro horario de atención ya registrado para este doctor.", 422);
        }

        // 2. Proteger citas futuras existentes si se desactiva o se acorta el horario
        $otrosHorarios = $horario->doctor->horariosAtencion()
            ->where('est_horario_atencion', 1)
            ->where('id_horario_atencion', '!=', $horario->id_horario_atencion)
            ->get(['dia_sem_horario_atencion', 'hor_ini_horario_atencion', 'hor_fin_horario_atencion'])
            ->toArray();

        if ($estado) {
            $otrosHorarios[] = $data;
        }

        $conflicto = $horario->doctor->obtenerConflictoConReservasFuturas($otrosHorarios);
        if ($conflicto) {
            return $this->errorResponse($conflicto, 409);
        }

        $horario->update($data);
        $horario->load('doctor.usuario');

        return $this->successResponse(
            new HorarioResource($horario),
            "Horario de atención actualizado correctamente."
        );
    }

    public function cambiarEstadoHorario($id_horario)
    {
        $horario = HorarioAtencion::with('doctor')->find($id_horario);

        if (!$horario) {
            return $this->errorResponse("Horario de atención no encontrado.", 404);
        }

        // Si se va a desactivar, validar que ninguna reserva futura activa quede sin cobertura
        if ($horario->est_horario_atencion) {
            $otrosHorarios = $horario->doctor->horariosAtencion()
                ->where('est_horario_atencion', 1)
                ->where('id_horario_atencion', '!=', $horario->id_horario_atencion)
                ->get(['dia_sem_horario_atencion', 'hor_ini_horario_atencion', 'hor_fin_horario_atencion'])
                ->toArray();

            $conflicto = $horario->doctor->obtenerConflictoConReservasFuturas($otrosHorarios);
            if ($conflicto) {
                return $this->errorResponse(
                    "No se puede desactivar este horario porque existen citas futuras activas que quedarían fuera del cronograma.",
                    409
                );
            }
        }

        $horario->est_horario_atencion = !$horario->est_horario_atencion;
        $horario->save();
        $horario->load('doctor.usuario');

        $mensaje = $horario->est_horario_atencion ? 'Horario activado con éxito.' : 'Horario desactivado con éxito.';

        return $this->successResponse(
            new HorarioResource($horario),
            $mensaje
        );
    }

    public function eliminarHorario($id_horario)
    {
        $horario = HorarioAtencion::findOrFail($id_horario);

        if (!$horario->puedeEliminarse()) {
            return $this->errorResponse(
                'No se puede eliminar el horario porque cuenta con reservas pendientes o confirmadas.', 409
            );
        }

        $horario->delete();
        
        return $this->successResponse(null, 'Horario de atención eliminado con éxito.');
    }

    public function obtenerHorarioPorID($id_horario)
    {
        $horario = HorarioAtencion::with('doctor.usuario')->findOrFail($id_horario); 

        return $this->successResponse(
            new HorarioResource($horario),
            "Horario $id_horario obtenido con éxito."
        );
    }

    public function obtenerDisponibilidad(
        ConsultarDisponibilidadRequest $request,
        DisponibilidadService $disponibilidadService
    ) {
        $resultado = $disponibilidadService->calcularDisponibilidad(
            $request->integer('id_doctor'),
            $request->integer('id_servicio'),
            $request->input('fecha')
        );

        if (!$resultado['ok']) {
            return $this->errorResponse($resultado['message'], $resultado['status']);
        }

        return $this->successResponse($resultado['data'], $resultado['message'], $resultado['status']);
    }

    public function registrarSemanaDoctor(ConfigurarSemanaRequest $request)
    {
        $data = $request->validated();
        $idDoctor = (int) $data['id_doctor'];
        $horariosNuevos = $data['horarios'];

        // 1. Validar solapamientos internos entre los turnos enviados en la petición
        $errorSolapamiento = HorarioAtencion::validarSolapamientoEnLista($horariosNuevos);
        if ($errorSolapamiento) {
            return $this->errorResponse($errorSolapamiento, 422);
        }

        // 2. Proteger citas futuras existentes para que no queden fuera del nuevo cronograma
        $doctor = Doctor::find($idDoctor);
        if (!$doctor) {
            return $this->errorResponse("Doctor no encontrado.", 404);
        }

        $errorCitas = $doctor->obtenerConflictoConReservasFuturas($horariosNuevos);
        if ($errorCitas) {
            return $this->errorResponse($errorCitas, 409);
        }

        // 3. Reemplazar los horarios bajo transacción
        DB::transaction(function () use ($idDoctor, $horariosNuevos) {
            HorarioAtencion::where('id_doctor', $idDoctor)->delete();

            foreach ($horariosNuevos as $h) {
                HorarioAtencion::create([
                    'id_doctor'                => $idDoctor,
                    'dia_sem_horario_atencion' => $h['dia_sem_horario_atencion'],
                    'hor_ini_horario_atencion' => $h['hor_ini_horario_atencion'],
                    'hor_fin_horario_atencion' => $h['hor_fin_horario_atencion'],
                    'est_horario_atencion'     => 1,
                ]);
            }
        });

        $horarios = HorarioAtencion::with('doctor.usuario')
            ->where('id_doctor', $idDoctor)
            ->orderBy('dia_sem_horario_atencion', 'asc')
            ->orderBy('hor_ini_horario_atencion', 'asc')
            ->get();

        return $this->successResponse(
            HorarioResource::collection($horarios),
            "Semana de atención configurada con éxito."
        );
    }

    public function misHorarios(Request $request)
    {        
        $usuario = $request->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $doctor = $usuario->doctor;

        if (!$doctor) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de doctor asociado.', 404);
        }

        $horarios = HorarioAtencion::with('doctor.usuario')
            ->where('id_doctor', $doctor->id_doctor)
            ->orderBy('dia_sem_horario_atencion', 'asc')
            ->orderBy('hor_ini_horario_atencion', 'asc')
            ->get();

        return $this->successResponse(
            HorarioResource::collection($horarios),
            "Mis horarios de atención obtenidos con éxito."
        );
    }
}