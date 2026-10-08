<?php

namespace App\Http\Requests\Horario;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfigurarSemanaRequest extends FormRequest
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
        return [
            'id_doctor'                                  => ['required', 'integer', 'exists:doctor,id_doctor'],
            'horarios'                                   => ['present', 'array'],
            'horarios.*.dia_sem_horario_atencion'        => ['required', 'integer', 'between:1,7'],
            'horarios.*.hor_ini_horario_atencion'        => ['required', 'date_format:H:i'],
            'horarios.*.hor_fin_horario_atencion'        => [
                'required',
                'date_format:H:i',
                'after:horarios.*.hor_ini_horario_atencion',
            ],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id_doctor.required'                           => 'El doctor es obligatorio.',
            'id_doctor.exists'                             => 'El doctor seleccionado no existe.',
            'horarios.present'                             => 'El campo horarios debe estar presente.',
            'horarios.array'                               => 'Los horarios deben enviarse en formato de lista.',
            'horarios.*.dia_sem_horario_atencion.required' => 'El día de la semana es obligatorio para cada turno.',
            'horarios.*.dia_sem_horario_atencion.between'  => 'El día de la semana debe ser un valor entre 1 (Lunes) y 7 (Domingo).',
            'horarios.*.hor_ini_horario_atencion.required' => 'La hora de inicio es obligatoria para cada turno.',
            'horarios.*.hor_ini_horario_atencion.date_format' => 'La hora de inicio debe tener formato HH:mm (ejemplo: 09:00).',
            'horarios.*.hor_fin_horario_atencion.required' => 'La hora de fin es obligatoria para cada turno.',
            'horarios.*.hor_fin_horario_atencion.date_format' => 'La hora de fin debe tener formato HH:mm (ejemplo: 13:00).',
            'horarios.*.hor_fin_horario_atencion.after'    => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
