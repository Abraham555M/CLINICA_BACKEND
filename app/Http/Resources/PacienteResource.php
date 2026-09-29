<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PacienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_paciente'             => $this->id_paciente,
            'id_usuario'              => $this->id_usuario,
            'id_usuario_responsable'  => $this->id_usuario_responsable,
            'tel_paciente'            => $this->tel_paciente,
            'fch_nac_paciente'        => $this->fch_nac_paciente?->format('Y-m-d'),
            'sld_fav_paciente'        => $this->sld_fav_paciente,
            'usuario'                 => $this->whenLoaded('usuario', function () {
                return [
                    'id_usuario'  => $this->usuario->id_usuario,
                    'nom_usuario' => $this->usuario->nom_usuario,
                    'ape_usuario' => $this->usuario->ape_usuario,
                    'doc_usuario' => $this->usuario->doc_usuario,
                    'ema_usuario' => $this->usuario->ema_usuario,
                    'est_usuario' => $this->usuario->est_usuario,
                    'genero'      => $this->when($this->usuario->relationLoaded('genero'), function () {
                        return [
                            'id_genero'  => $this->usuario->genero->id_genero ?? null,
                            'nom_genero' => $this->usuario->genero->nom_genero ?? null,
                        ];
                    }),
                ];
            }),
            'antecedentes'            => AntecedenteResource::collection($this->whenLoaded('antecedentes')),
        ];
    }
}
