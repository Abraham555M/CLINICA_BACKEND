<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_doctor'      => $this->id_doctor,
            'cop_num_doctor' => $this->cop_num_doctor,
            'bio_doctor'     => $this->bio_doctor,
            'img_doctor'     => $this->img_doctor ? asset('storage/' . $this->img_doctor) : null,
            'id_usuario'     => $this->id_usuario,
            'usuario'        => $this->whenLoaded('usuario', function () {
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
            'servicios'      => $this->whenLoaded('servicios', function () {
                return $this->servicios->map(function ($servicio) {
                    return [
                        'id_servicio'      => $servicio->id_servicio,
                        'nom_servicio'     => $servicio->nom_servicio,
                        'prc_servicio'     => $servicio->prc_servicio,
                        'dur_min_servicio' => $servicio->dur_min_servicio,
                    ];
                });
            }),
        ];
    }
}