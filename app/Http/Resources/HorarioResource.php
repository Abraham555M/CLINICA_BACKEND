<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HorarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return [
            'id_horario_atencion'      => $this->id_horario_atencion,
            'id_doctor'                => $this->id_doctor,
            'dia_sem_horario_atencion' => (int) $this->dia_sem_horario_atencion,
            'nom_dia_semana'           => $diasSemana[(int) $this->dia_sem_horario_atencion] ?? null,
            'hor_ini_horario_atencion' => is_string($this->hor_ini_horario_atencion) ? substr($this->hor_ini_horario_atencion, 0, 5) : $this->hor_ini_horario_atencion,
            'hor_fin_horario_atencion' => is_string($this->hor_fin_horario_atencion) ? substr($this->hor_fin_horario_atencion, 0, 5) : $this->hor_fin_horario_atencion,
            'est_horario_atencion'     => (bool) $this->est_horario_atencion,
            'doctor'                   => $this->whenLoaded('doctor', function () {
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
