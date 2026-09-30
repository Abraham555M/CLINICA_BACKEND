<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AntecedenteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_antecedente'      => $this->id_antecedente,
            'id_paciente'         => $this->id_paciente,
            'id_tipo_antecedente' => $this->id_tipo_antecedente,
            'id_consulta'         => $this->id_consulta,
            'des_antecedente'     => $this->des_antecedente,
            'fch_antecedente'     => $this->fch_antecedente?->format('Y-m-d H:i:s'),
            'est_antecedente'     => $this->est_antecedente,
            'tipo_antecedente'    => $this->whenLoaded('tipoAntecedente', function () {
                return [
                    'id_tipo_antecedente'  => $this->tipoAntecedente->id_tipo_antecedente,
                    'nom_tipo_antecedente' => $this->tipoAntecedente->nom_tipo_antecedente,
                ];
            }),
            'consulta'            => $this->whenLoaded('consulta', function () {
                return [
                    'id_consulta'  => $this->consulta->id_consulta,
                    'fch_consulta' => $this->consulta->fch_consulta?->format('Y-m-d'),
                    'mot_consulta' => $this->consulta->mot_consulta,
                ];
            }),
        ];
    }
}
