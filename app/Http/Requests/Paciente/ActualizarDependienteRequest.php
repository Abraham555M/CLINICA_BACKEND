<?php

namespace App\Http\Requests\Paciente;

use App\Models\Paciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarDependienteRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $idPaciente = $this->route('id_paciente');
        $paciente = Paciente::find($idPaciente);
        $idUsuario = $paciente?->id_usuario;

        return [
            // Datos personales del dependiente
            'id_genero'        => ['required', 'integer', Rule::exists('genero', 'id_genero')],
            'nom_usuario'      => ['required', 'string', 'max:200'],
            'ape_usuario'      => ['required', 'string', 'max:200'],
            'doc_usuario'      => [
                'required',
                'string',
                'max:20',
                Rule::unique('usuario', 'doc_usuario')->ignore($idUsuario, 'id_usuario'),
            ],
            'fch_nac_paciente' => ['required', 'date', 'before:today'],
            
            // Opcionales
            'tel_paciente'     => ['nullable', 'string', 'size:9', 'regex:/^9\d{8}$/'],
            'ema_usuario'      => [
                'nullable',
                'email',
                'max:200',
                Rule::unique('usuario', 'ema_usuario')->ignore($idUsuario, 'id_usuario'),
            ],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'id_genero.required'        => 'El género del dependiente es obligatorio.',
            'id_genero.exists'          => 'El género seleccionado no es válido.',

            'nom_usuario.required'      => 'El nombre es obligatorio.',
            'nom_usuario.string'        => 'El nombre debe ser un texto válido.',
            'nom_usuario.max'           => 'El nombre no debe exceder los 200 caracteres.',

            'ape_usuario.required'      => 'El apellido es obligatorio.',
            'ape_usuario.string'        => 'El apellido debe ser un texto válido.',
            'ape_usuario.max'           => 'El apellido no debe exceder los 200 caracteres.',

            'doc_usuario.required'      => 'El documento del dependiente es obligatorio.',
            'doc_usuario.string'        => 'El documento debe ser un texto válido.',
            'doc_usuario.max'           => 'El documento no debe exceder los 20 caracteres.',
            'doc_usuario.unique'        => 'Ya existe un usuario registrado con este documento.',

            'fch_nac_paciente.required' => 'La fecha de nacimiento es obligatoria.',
            'fch_nac_paciente.date'     => 'La fecha de nacimiento debe ser una fecha válida.',
            'fch_nac_paciente.before'   => 'La fecha de nacimiento debe ser anterior al día de hoy.',

            'tel_paciente.string'       => 'El teléfono debe ser un texto válido.',
            'tel_paciente.size'         => 'El teléfono debe tener exactamente 9 dígitos.',
            'tel_paciente.regex'        => 'El teléfono debe iniciar con 9 y tener 9 dígitos numéricos.',

            'ema_usuario.email'         => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.max'           => 'El correo electrónico no debe exceder los 200 caracteres.',
            'ema_usuario.unique'        => 'Ya existe un usuario registrado con este correo electrónico.',
        ];
    }
}
