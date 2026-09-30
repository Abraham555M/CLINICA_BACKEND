<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Antecedente\ActualizarAntecedenteRequest;
use App\Http\Requests\Antecedente\RegistrarAntecedenteRequest;
use App\Http\Resources\AntecedenteResource;
use App\Models\AntecedenteMedico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AntecedenteMedicoController extends Controller
{
    public function listarPorPaciente(Request $request, $id_paciente): JsonResponse
    {
        $query = AntecedenteMedico::with(['tipoAntecedente', 'consulta'])
            ->where('id_paciente', $id_paciente);

        if (!$request->boolean('todos', false)) {
            $query->where('est_antecedente', 1);
        }

        $antecedentes = $query->orderBy('id_tipo_antecedente', 'asc')
            ->orderBy('id_antecedente', 'desc')
            ->get();

        return $this->successResponse(
            AntecedenteResource::collection($antecedentes),
            'Antecedentes médicos del paciente obtenidos con éxito.'
        );
    }

    public function obtenerAntecedente($id_antecedente): JsonResponse
    {
        $antecedente = AntecedenteMedico::with(['tipoAntecedente', 'consulta'])
            ->findOrFail($id_antecedente);

        return $this->successResponse(
            new AntecedenteResource($antecedente),
            'Detalle del antecedente médico obtenido con éxito.'
        );
    }

    public function registrarAntecedente(RegistrarAntecedenteRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['fch_antecedente'])) {
            $data['fch_antecedente'] = now();
        }

        if (!isset($data['est_antecedente'])) {
            $data['est_antecedente'] = 1;
        }

        $antecedente = AntecedenteMedico::create($data);
        $antecedente->load(['tipoAntecedente', 'consulta']);

        return $this->successResponse(
            new AntecedenteResource($antecedente),
            'Antecedente médico registrado con éxito.',
            201
        );
    }

    public function actualizarAntecedente(ActualizarAntecedenteRequest $request, $id_antecedente): JsonResponse
    {
        $antecedente = AntecedenteMedico::findOrFail($id_antecedente);
        $antecedente->update($request->validated());
        $antecedente->load(['tipoAntecedente', 'consulta']);

        return $this->successResponse(
            new AntecedenteResource($antecedente),
            'Antecedente médico actualizado con éxito.'
        );
    }

    public function eliminarAntecedente($id_antecedente): JsonResponse
    {
        $antecedente = AntecedenteMedico::findOrFail($id_antecedente);
        $antecedente->delete();

        return $this->successResponse(
            null,
            'Antecedente médico eliminado con éxito.'
        );
    }

    public function misAntecedentes(Request $request): JsonResponse
    {
        $usuario = auth()->user();

        if (!$usuario || !$usuario->paciente) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de paciente asociado.', 404);
        }

        $query = AntecedenteMedico::with(['tipoAntecedente', 'consulta'])
            ->where('id_paciente', $usuario->paciente->id_paciente);

        if (!$request->boolean('todos', false)) {
            $query->where('est_antecedente', 1);
        }

        $antecedentes = $query->orderBy('id_tipo_antecedente', 'asc')
            ->orderBy('id_antecedente', 'desc')
            ->get();

        return $this->successResponse(
            AntecedenteResource::collection($antecedentes),
            'Tus antecedentes médicos han sido obtenidos con éxito.'
        );
    }
}