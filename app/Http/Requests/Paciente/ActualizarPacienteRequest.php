<?php

namespace App\Http\Requests\Paciente;

use App\Models\Paciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarPacienteRequest extends FormRequest
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
            // Datos de usuario
            'id_genero'    => ['required', 'exists:genero,id_genero'],
            'nom_usuario'  => ['required', 'string', 'max:200'],
            'ape_usuario'  => ['required', 'string', 'max:200'],
            'doc_usuario'  => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9A-Za-z]+$/',
                Rule::unique('usuario', 'doc_usuario')->ignore($idUsuario, 'id_usuario')
            ],
            'ema_usuario'  => [
                'required',
                'email',
                'max:200',
                Rule::unique('usuario', 'ema_usuario')->ignore($idUsuario, 'id_usuario')
            ],
            'pas_usuario'  => ['nullable', 'string', 'min:8'],
            'est_usuario'  => ['nullable', 'integer', 'in:0,1,2'],

            // Datos de Paciente
            'tel_paciente' => [
                'required',
                'string',
                'size:9',
                'regex:/^9\d{8}$/',
                Rule::unique('paciente', 'tel_paciente')->ignore($idPaciente, 'id_paciente')
            ],
            'fch_nac_paciente'       => ['required', 'date', 'before:today'],
            'sld_fav_paciente'       => ['nullable', 'numeric', 'min:0'],
            'id_usuario_responsable' => ['nullable', 'exists:usuario,id_usuario'],
        ];
    }

    public function messages(): array
    {
        return [
            // Datos de usuario
            'id_genero.required'       => 'El género es obligatorio.',
            'id_genero.exists'         => 'El género seleccionado no es válido.',

            'nom_usuario.required'     => 'El nombre es obligatorio.',
            'nom_usuario.string'       => 'El nombre debe ser un texto válido.',
            'nom_usuario.max'          => 'El nombre no debe exceder los 200 caracteres.',

            'ape_usuario.required'     => 'El apellido es obligatorio.',
            'ape_usuario.string'       => 'El apellido debe ser un texto válido.',
            'ape_usuario.max'          => 'El apellido no debe exceder los 200 caracteres.',

            'doc_usuario.required'     => 'El documento es obligatorio.',
            'doc_usuario.string'       => 'El documento debe ser un texto válido.',
            'doc_usuario.size'         => 'El documento debe tener exactamente 8 caracteres.',
            'doc_usuario.regex'        => 'El documento solo debe contener letras y números.',
            'doc_usuario.unique'       => 'Este documento ya se encuentra registrado por otro usuario.',

            'ema_usuario.required'     => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'        => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.max'          => 'El correo electrónico no debe exceder los 200 caracteres.',
            'ema_usuario.unique'       => 'Este correo electrónico ya se encuentra registrado por otro usuario.',

            'pas_usuario.string'       => 'La contraseña debe ser un texto válido.',
            'pas_usuario.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'est_usuario.integer'      => 'El estado del usuario debe ser un número entero.',
            'est_usuario.in'           => 'El estado del usuario no es válido. Debe ser 0 (Pendiente), 1 (Activo) o 2 (Inactivo).',

            // Datos de Paciente
            'tel_paciente.required'    => 'El teléfono es obligatorio.',
            'tel_paciente.string'      => 'El teléfono debe ser un texto válido.',
            'tel_paciente.size'        => 'El teléfono debe tener exactamente 9 dígitos.',
            'tel_paciente.regex'       => 'El teléfono debe iniciar con 9 y tener 9 dígitos numéricos.',
            'tel_paciente.unique'      => 'Este teléfono ya se encuentra registrado por otro paciente.',

            'fch_nac_paciente.required' => 'La fecha de nacimiento es obligatoria.',
            'fch_nac_paciente.date'     => 'La fecha de nacimiento no tiene un formato válido.',
            'fch_nac_paciente.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',

            'sld_fav_paciente.numeric'  => 'El saldo a favor debe ser un valor numérico.',
            'sld_fav_paciente.min'      => 'El saldo a favor no puede ser negativo.',

            'id_usuario_responsable.exists' => 'El usuario responsable seleccionado no es válido.',
        ];
    }
}
