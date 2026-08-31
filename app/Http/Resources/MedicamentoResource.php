<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_medicamento'  => $this->id_medicamento,
            'nom_medicamento' => $this->nom_medicamento,
            'con_medicamento' => $this->con_medicamento,
            'est_medicamento' => $this->est_medicamento,
            'id_presentacion' => $this->id_presentacion,
            'presentacion'    => $this->whenLoaded('presentacion', function () {
                return [
                    'id_presentacion'  => $this->presentacion->id_presentacion,
                    'nom_presentacion' => $this->presentacion->nom_presentacion,
                    'est_presentacion' => $this->presentacion->est_presentacion,
                ];
            }),
            'unidad_medida'    => $this->whenLoaded('unidad_medida', function () {
                return [
                    'id_unidad_medida'  => $this->unidad_medida->id_unidad_medida,
                    'abr_unidad_medida' => $this->unidad_medida->abr_unidad_medida,
                    'est_unidad_medida' => $this->unidad_medida->est_unidad_medida,
                ];
            }),
            /*
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            */
        ];
    }
}