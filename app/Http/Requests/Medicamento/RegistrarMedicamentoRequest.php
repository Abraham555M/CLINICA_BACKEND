<?php

namespace App\Http\Requests\Medicamento;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarMedicamentoRequest extends FormRequest
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
            'nom_medicamento' => [
                'required',
                'string',
                'max:200',
                // Ignora los registros eliminados con SoftDeletes
                Rule::unique('medicamento', 'nom_medicamento')->withoutTrashed(),
            ],
            'con_medicamento' => ['required', 'string'],
            'est_medicamento' => ['nullable', 'boolean'],
            'id_presentacion' => [
                'required',
                'integer',
                // Valida que el ID exista en la tabla presentaciones y no esté eliminado
                Rule::exists('presentacion_medicamento', 'id_presentacion'),
            ],
            'id_unidad_medida' => [
                'required',
                'integer',
                // Valida que el ID exista en la tabla presentaciones y no esté eliminado
                Rule::exists('unidad_medida', 'id_unidad_medida'),
            ],
        ];
    }

    // Mensajes personalizados en español
    public function messages(): array
    {
        return [
            'nom_medicamento.required'  => 'El nombre del medicamento es obligatorio.',
            'nom_medicamento.unique'    => 'Ya existe un medicamento registrado con este nombre.',
            'est_medicamento.required'  => 'Debe especificar el estado del medicamento.',
            'est_medicamento.boolean'   => 'El estado del medicamento debe ser activo o inactivo.',
            'id_presentacion.exists'    => 'La presentación seleccionada no es válida o no existe.',
            'id_unidad_medida.exists'   => 'La unidad de medida seleccionada no es válida o no existe.',
        ];
    }
}
