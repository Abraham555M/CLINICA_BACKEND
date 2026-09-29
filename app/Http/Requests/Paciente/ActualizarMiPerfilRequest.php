<?php

namespace App\Http\Requests\Paciente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarMiPerfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $idUsuario = auth()->id();
        $paciente = auth()->user()?->paciente;
        $idPaciente = $paciente?->id_paciente;

        return [
            // Datos de usuario permitidos para edición
            'id_genero'    => ['required', 'exists:genero,id_genero'],
            'nom_usuario'  => ['required', 'string', 'max:200'],
            'ape_usuario'  => ['required', 'string', 'max:200'],
            'ema_usuario'  => [
                'required',
                'email',
                'max:200',
                Rule::unique('usuario', 'ema_usuario')->ignore($idUsuario, 'id_usuario')
            ],

            // Datos de paciente permitidos para edición
            'tel_paciente' => [
                'required',
                'string',
                'size:9',
                'regex:/^9\d{8}$/',
                Rule::unique('paciente', 'tel_paciente')->ignore($idPaciente, 'id_paciente')
            ],
            'fch_nac_paciente' => [
                'required',
                'date',
                'before:today'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Datos de usuario
            'id_genero.required'    => 'El género es obligatorio.',
            'id_genero.exists'      => 'El género seleccionado no es válido.',

            'nom_usuario.required'  => 'El nombre es obligatorio.',
            'nom_usuario.string'    => 'El nombre debe ser un texto válido.',
            'nom_usuario.max'       => 'El nombre no debe exceder los 200 caracteres.',

            'ape_usuario.required'  => 'El apellido es obligatorio.',
            'ape_usuario.string'    => 'El apellido debe ser un texto válido.',
            'ape_usuario.max'       => 'El apellido no debe exceder los 200 caracteres.',

            'ema_usuario.required'  => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'     => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.max'       => 'El correo electrónico no debe exceder los 200 caracteres.',
            'ema_usuario.unique'    => 'Este correo electrónico ya se encuentra registrado por otro usuario.',

            // Datos de paciente
            'tel_paciente.required' => 'El teléfono es obligatorio.',
            'tel_paciente.string'   => 'El teléfono debe ser un texto válido.',
            'tel_paciente.size'     => 'El teléfono debe tener exactamente 9 dígitos.',
            'tel_paciente.regex'    => 'El teléfono debe iniciar con 9 y tener 9 dígitos numéricos.',
            'tel_paciente.unique'   => 'Este teléfono ya se encuentra registrado por otro paciente.',

            'fch_nac_paciente.required' => 'La fecha de nacimiento es obligatoria.',
            'fch_nac_paciente.date'     => 'La fecha de nacimiento no tiene un formato válido.',
            'fch_nac_paciente.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}

