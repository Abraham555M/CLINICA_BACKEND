<?php

namespace App\Http\Requests\Horario;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarDisponibilidadRequest extends FormRequest
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
            'id_doctor'   => ['required', 'integer', 'exists:doctor,id_doctor'],
            'id_servicio' => ['required', 'integer', 'exists:servicio,id_servicio'],
            'fecha'       => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id_doctor.required'        => 'El doctor es obligatorio.',
            'id_doctor.exists'          => 'El doctor seleccionado no existe.',
            'id_servicio.required'      => 'El servicio odontológico es obligatorio.',
            'id_servicio.exists'        => 'El servicio seleccionado no existe.',
            'fecha.required'            => 'La fecha de consulta es obligatoria.',
            'fecha.date'                => 'La fecha no tiene un formato válido.',
            'fecha.date_format'         => 'La fecha debe tener el formato YYYY-MM-DD (ejemplo: 2026-10-15).',
            'fecha.after_or_equal'      => 'La fecha de consulta debe ser hoy o una fecha futura.',
        ];
    }
}
