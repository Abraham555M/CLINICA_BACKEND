<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Horario\BloquearHorarioRequest;
use App\Http\Resources\HorarioBloqueadoResource;
use App\Models\Doctor;
use App\Models\HorarioBloqueado;
use App\Services\BloqueoHorarioService;
use Illuminate\Http\Request;

class HorarioBloqueadoController extends Controller
{
    /**
     * Registrar unas horas en especifico: 
     *      - fch_blq_horario_bloqueado
     *      - hor_ini_horario_bloqueado 
     *      - hor_fin_horario_bloqueado
     *      - es_dia_completo (false)
     * Registrar un día completo:
     *      - fch_blq_horario_bloqueado  
     *      - es_dia_completo (true)
     * Registrar un rango de fechas (N registros): 
     *      - fch_blq_horario_bloqueado
     *      - fch_fin_bloqueado
     *      - es_dia_completo (true)
     */
    public function registrarBloqueoHorario(
        BloquearHorarioRequest $request,
        BloqueoHorarioService $bloqueoHorarioService
    ) {
        $resultado = $bloqueoHorarioService->registrarBloqueo(
            $request->validated(),
            $request->boolean('es_dia_completo')
        );

        if (!$resultado['ok']) {
            return $this->errorResponse($resultado['message'], $resultado['status']);
        }

        $esRango = $resultado['es_rango'];
        $registros = $resultado['data'];

        $resultadoResource = (!$esRango && count($registros) === 1)
            ? new HorarioBloqueadoResource($registros[0])
            : HorarioBloqueadoResource::collection($registros);

        return $this->successResponse($resultadoResource, $resultado['message'], $resultado['status']);
    }

    public function listarBloqueosPorDoctor(Request $request, $id_doctor)
    {
        $doctor = Doctor::find($id_doctor);

        if (!$doctor) {
            return $this->errorResponse("Doctor no encontrado.", 404);
        }

        $query = HorarioBloqueado::with('doctor.usuario')
            ->where('id_doctor', $id_doctor);

        if ($request->filled('desde')) {
            $query->whereDate('fch_blq_horario_bloqueado', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fch_blq_horario_bloqueado', '<=', $request->input('hasta'));
        }

        $bloqueos = $query->orderBy('fch_blq_horario_bloqueado', 'desc')
            ->orderBy('hor_ini_horario_bloqueado', 'asc')
            ->get();

        return $this->successResponse(
            HorarioBloqueadoResource::collection($bloqueos),
            "Lista de horarios bloqueados del doctor obtenida con éxito."
        );
    }

    public function eliminarHorarioBloqueo($id_bloqueo)
    {
        $bloqueo = HorarioBloqueado::find($id_bloqueo);

        if (!$bloqueo) {
            return $this->errorResponse("Horario bloqueado no encontrado.", 404);
        }

        $bloqueo->delete();

        return $this->successResponse(null, "Bloqueo de horario eliminado con éxito.");
    }

    public function misHorariosBloqueados(Request $request)
    {
        $usuario = $request->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $doctor = $usuario->doctor;

        if (!$doctor) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de doctor asociado.', 404);
        }

        $query = HorarioBloqueado::with('doctor.usuario')
            ->where('id_doctor', $doctor->id_doctor);

        if ($request->filled('desde')) {
            $query->whereDate('fch_blq_horario_bloqueado', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fch_blq_horario_bloqueado', '<=', $request->input('hasta'));
        }

        $bloqueos = $query->orderBy('fch_blq_horario_bloqueado', 'desc')
            ->orderBy('hor_ini_horario_bloqueado', 'asc')
            ->get();

        return $this->successResponse(
            HorarioBloqueadoResource::collection($bloqueos),
            "Mis horarios bloqueados obtenidos con éxito."
        );
    }
}