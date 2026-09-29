<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_usuario'  => $this->id_usuario,
            'nom_usuario' => $this->nom_usuario,
            'ape_usuario' => $this->ape_usuario,
            'ema_usuario' => $this->ema_usuario,
            'doc_usuario' => $this->doc_usuario,
            'rol'         => $this->whenLoaded('rol', function () {
                return [
                    'id_rol'  => $this->rol->id_rol,
                    'nom_rol' => $this->rol->nom_rol,
                ];
            }),
            'id_doctor'   => $this->doctor?->id_doctor,
            'id_paciente' => $this->paciente?->id_paciente,
        ];
    }
}
