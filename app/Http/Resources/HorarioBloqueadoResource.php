<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HorarioBloqueadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_horario_bloqueado'      => $this->id_horario_bloqueado,
            'id_doctor'                 => $this->id_doctor,
            'fch_blq_horario_bloqueado' => $this->fch_blq_horario_bloqueado instanceof \Carbon\CarbonInterface 
                                            ? $this->fch_blq_horario_bloqueado->format('Y-m-d') 
                                            : $this->fch_blq_horario_bloqueado,
            'hor_ini_horario_bloqueado' => is_string($this->hor_ini_horario_bloqueado) 
                                            ? substr($this->hor_ini_horario_bloqueado, 0, 5) 
                                            : $this->hor_ini_horario_bloqueado,
            'hor_fin_horario_bloqueado' => is_string($this->hor_fin_horario_bloqueado) 
                                            ? substr($this->hor_fin_horario_bloqueado, 0, 5) 
                                            : $this->hor_fin_horario_bloqueado,
            'mot_horario_bloqueado'     => $this->mot_horario_bloqueado,
            'doctor'                    => $this->whenLoaded('doctor', function () {
                return [
                    'id_doctor'      => $this->doctor->id_doctor,
                    'cop_num_doctor' => $this->doctor->cop_num_doctor,
                    'nom_doctor'     => $this->doctor->usuario?->nom_usuario,
                    'ape_doctor'     => $this->doctor->usuario?->ape_usuario,
                ];
            }),
        ];
    }
}

