<?php

namespace App\Http\Requests\Servicio;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class ActualizarServiceRequest extends FormRequest
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
        $idServicio = $this->route('id_servicio');

        return [
            'nom_servicio' => [
                'required',
                'string',
                'max:200',
                Rule::unique('servicio', 'nom_servicio')
                    ->ignore($idServicio, 'id_servicio')
                    ->withoutTrashed(),
            ],
            'des_servicio'     => ['nullable', 'string', 'max:200'],
            'dur_min_servicio' => ['required', 'integer', 'min:1', 'max:480'],
            'prc_servicio'     => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'est_servicio'     => ['required', 'boolean'],
            'id_tipo_servicio' => [
                'required',
                'integer',
                Rule::exists('tipo_servicio', 'id_tipo_servicio'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_servicio.required' => 'El nombre del servicio es obligatorio.',
            'nom_servicio.string'   => 'El nombre del servicio debe ser un texto válido.',
            'nom_servicio.max'      => 'El nombre del servicio no debe exceder los :max caracteres.',
            'nom_servicio.unique'   => 'Ya existe otro servicio registrado con ese nombre.',

            'des_servicio.string'   => 'La descripción debe ser un texto válido.',
            'des_servicio.max'      => 'La descripción no debe exceder los :max caracteres.',

            'dur_min_servicio.required' => 'La duración del servicio es obligatoria.',
            'dur_min_servicio.integer'  => 'La duración debe ser un número entero.',
            'dur_min_servicio.min'      => 'La duración debe ser de al menos :min minuto.',
            'dur_min_servicio.max'      => 'La duración no debe exceder los :max minutos.',

            'prc_servicio.required' => 'El precio del servicio es obligatorio.',
            'prc_servicio.numeric'  => 'El precio debe ser un valor numérico.',
            'prc_servicio.min'      => 'El precio no puede ser negativo.',
            'prc_servicio.decimal'  => 'El precio debe tener hasta 2 decimales.',

            'est_servicio.required' => 'Debe especificar el estado del servicio.',
            'est_servicio.boolean'  => 'El estado del servicio debe ser verdadero o falso.',

            'id_tipo_servicio.required' => 'El tipo de servicio es obligatorio.',
            'id_tipo_servicio.integer'  => 'El tipo de servicio debe ser un valor numérico válido.',
            'id_tipo_servicio.exists'   => 'El tipo de servicio seleccionado no existe.',
        ];
    }
}
