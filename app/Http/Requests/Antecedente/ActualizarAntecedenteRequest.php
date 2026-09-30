<?php

namespace App\Http\Requests\Antecedente;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarAntecedenteRequest extends FormRequest
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
            'id_tipo_antecedente' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('tipo_antecedente', 'id_tipo_antecedente'),
            ],
            'id_consulta' => [
                'nullable',
                'integer',
                Rule::exists('consulta', 'id_consulta'),
            ],
            'des_antecedente' => ['sometimes', 'required', 'string', 'max:1000'],
            'fch_antecedente' => ['nullable', 'date', 'before_or_equal:today'],
            'est_antecedente' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Mensajes de validación personalizados.
     */
    public function messages(): array
    {
        return [
            'id_tipo_antecedente.required' => 'El tipo de antecedente es obligatorio.',
            'id_tipo_antecedente.integer'  => 'El identificador del tipo de antecedente debe ser un número entero.',
            'id_tipo_antecedente.exists'   => 'El tipo de antecedente seleccionado no existe.',

            'id_consulta.integer' => 'El identificador de la consulta debe ser un número entero.',
            'id_consulta.exists'  => 'La consulta seleccionada no existe.',

            'des_antecedente.required' => 'La descripción del antecedente es obligatoria.',
            'des_antecedente.string'   => 'La descripción del antecedente debe ser un texto válido.',
            'des_antecedente.max'      => 'La descripción no debe exceder los :max caracteres.',

            'fch_antecedente.date'            => 'La fecha del antecedente debe tener un formato de fecha válido.',
            'fch_antecedente.before_or_equal' => 'La fecha del antecedente no puede ser una fecha futura.',

            'est_antecedente.boolean' => 'El estado del antecedente debe ser verdadero o falso.',
        ];
    }
}

