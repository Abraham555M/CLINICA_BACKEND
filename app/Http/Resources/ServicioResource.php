<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id_servicio" => $this->id_servicio,
            "nom_servicio" => $this->nom_servicio,
            "des_servicio" => $this->des_servicio,
            "dur_min_servicio" => $this->dur_min_servicio,
            "prc_servicio" => $this->prc_servicio,
            "est_servicio" => $this->est_servicio,
            "id_tipo_servicio" => $this->id_tipo_servicio,
            'tipo_servicio' => $this->whenLoaded('tipoServicio', function () {
                return [
                    'id_tipo_servicio'  => $this->tipoServicio->id_tipo_servicio,
                    'nom_tipo_servicio' => $this->tipoServicio->nom_tipo_servicio
                ];
            }),
        ];
    }
}

