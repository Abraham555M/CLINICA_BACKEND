<?php

namespace App\Http\Requests\Horario;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BloquearHorarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $esDiaCompleto = $this->boolean('es_dia_completo');

        return [
            'id_doctor'                 => ['required', 'integer', 'exists:doctor,id_doctor'],
            'fch_blq_horario_bloqueado' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'fch_fin_bloqueado'         => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:fch_blq_horario_bloqueado'],
            'es_dia_completo'           => ['nullable', 'boolean'],
            'hor_ini_horario_bloqueado' => [
                $esDiaCompleto ? 'nullable' : 'required',
                'date_format:H:i',
            ],
            'hor_fin_horario_bloqueado' => [
                $esDiaCompleto ? 'nullable' : 'required',
                'date_format:H:i',
                $esDiaCompleto ? 'nullable' : 'after:hor_ini_horario_bloqueado',
            ],
            'mot_horario_bloqueado'     => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id_doctor.required'                      => 'El doctor es obligatorio.',
            'id_doctor.exists'                        => 'El doctor seleccionado no existe.',
            'fch_blq_horario_bloqueado.required'      => 'La fecha de bloqueo (o fecha de inicio) es obligatoria.',
            'fch_blq_horario_bloqueado.date'          => 'La fecha de bloqueo no tiene un formato de fecha válido.',
            'fch_blq_horario_bloqueado.date_format'   => 'La fecha de bloqueo debe tener el formato YYYY-MM-DD (ejemplo: 2026-09-15).',
            'fch_blq_horario_bloqueado.after_or_equal'=> 'La fecha de bloqueo debe ser hoy o una fecha futura.',
            'fch_fin_bloqueado.date'                  => 'La fecha de fin no tiene un formato de fecha válido.',
            'fch_fin_bloqueado.date_format'           => 'La fecha de fin debe tener el formato YYYY-MM-DD (ejemplo: 2026-09-20).',
            'fch_fin_bloqueado.after_or_equal'        => 'La fecha de fin debe ser igual o posterior a la fecha de inicio del bloqueo.',
            'hor_ini_horario_bloqueado.required'      => 'La hora de inicio del bloqueo es obligatoria cuando no es día completo.',
            'hor_ini_horario_bloqueado.date_format'   => 'La hora de inicio debe tener el formato HH:mm (ejemplo: 08:00).',
            'hor_fin_horario_bloqueado.required'      => 'La hora de fin del bloqueo es obligatoria cuando no es día completo.',
            'hor_fin_horario_bloqueado.date_format'   => 'La hora de fin debe tener el formato HH:mm (ejemplo: 12:00).',
            'hor_fin_horario_bloqueado.after'         => 'La hora de fin debe ser posterior a la hora de inicio.',
            'mot_horario_bloqueado.max'               => 'El motivo del bloqueo no puede superar los 500 caracteres.',
        ];
    }
}

