<?php

namespace App\Http\Requests\Horario;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarHorarioRequest extends FormRequest
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
            'id_doctor'                => ['required', 'integer', 'exists:doctor,id_doctor'],
            'dia_sem_horario_atencion' => ['required', 'integer', 'between:1,7'], // 1: Lunes, ..., 7: Domingo
            'hor_ini_horario_atencion' => ['required', 'date_format:H:i'],       // Ejemplo: "08:00"
            'hor_fin_horario_atencion' => ['required', 'date_format:H:i', 'after:hor_ini_horario_atencion'], // Ejemplo: "12:00"
            'est_horario_atencion'     => ['required', 'boolean'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id_doctor.required'                => 'El doctor es obligatorio.',
            'id_doctor.exists'                  => 'El doctor seleccionado no existe.',
            'dia_sem_horario_atencion.required' => 'El día de la semana es obligatorio.',
            'dia_sem_horario_atencion.between'  => 'El día de la semana debe estar entre 1 (Lunes) y 7 (Domingo).',
            'hor_ini_horario_atencion.required' => 'La hora de inicio es obligatoria.',
            'hor_ini_horario_atencion.date_format' => 'La hora de inicio debe tener el formato HH:mm (ejemplo: 08:00).',
            'hor_fin_horario_atencion.required' => 'La hora de fin es obligatoria.',
            'hor_fin_horario_atencion.date_format' => 'La hora de fin debe tener el formato HH:mm (ejemplo: 12:00).',
            'hor_fin_horario_atencion.after'    => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
