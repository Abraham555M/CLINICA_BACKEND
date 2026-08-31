<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicamento\ActualizarMedicamentoRequest;
use App\Http\Requests\Medicamento\RegistrarMedicamentoRequest;
use App\Http\Resources\MedicamentoResource;
use App\Models\Medicamento;

class MedicamentoController extends Controller
{
    public function selectMedicamentos(){
        $medicamentos = Medicamento::select('id_medicamento', 'nom_medicamento')
                                    ->where('est_medicamento', 1)
                                    ->get(); 

        return $this->successResponse($medicamentos, 'Select de medicamentos obtenido con éxito'); 
    }
    
    public function listarMedicamentos()
    {
        $medicamentos = Medicamento::with(['presentacion', 'unidad_medida'])
                                    ->orderBy('est_medicamento', 'desc')
                                    ->get();

        return $this->successResponse(
            MedicamentoResource::collection($medicamentos),
            'Lista de medicamentos obtenida con éxito.'
        );
    }

    public function registrarMedicamento(RegistrarMedicamentoRequest $request)
    {
        $medicamento = Medicamento::create($request->validated());
        $medicamento->load(['presentacion', 'unidad_medida']);

        return $this->successResponse(
            new MedicamentoResource($medicamento),
            'Medicamento registrado con éxito.', 201
        );
    }

    public function actualizarMedicamento(ActualizarMedicamentoRequest $request, $id_medicamento)
    {
        $medicamento = Medicamento::findOrFail($id_medicamento);
        $medicamento->update($request->validated());
        $medicamento->load(['presentacion', 'unidad_medida']);

        return $this->successResponse(
            new MedicamentoResource($medicamento),
            'Medicamento actualizado con éxito.'
        );
    }

    public function eliminarMedicamento($id_medicamento)
    {
        $medicamento = Medicamento::findOrFail($id_medicamento);

        // Validar si el medicamento ya pertenece a alguna receta médica
        if (!$medicamento->puedeEliminarse()) {
            return $this->errorResponse(
                'No se puede eliminar el medicamento porque se encuentra activo en recetas',
                409
            );
        }

        $medicamento->delete();
        return $this->successResponse(null, 'Medicamento eliminado con éxito.');
    }
}