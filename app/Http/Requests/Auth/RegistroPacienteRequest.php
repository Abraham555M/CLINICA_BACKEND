<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistroPacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom_usuario'  => $this->has('nom_usuario') ? trim($this->nom_usuario) : null,
            'ape_usuario'  => $this->has('ape_usuario') ? trim($this->ape_usuario) : null,
            'doc_usuario'  => $this->has('doc_usuario') ? trim($this->doc_usuario) : null,
            'ema_usuario'  => $this->has('ema_usuario') ? strtolower(trim($this->ema_usuario)) : null,
            'tel_paciente' => $this->has('tel_paciente') ? trim($this->tel_paciente) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Datos de usuario
            'id_genero'        => ['required', 'exists:genero,id_genero'],
            'nom_usuario'      => ['required', 'string', 'max:200'],
            'ape_usuario'      => ['required', 'string', 'max:200'],
            'doc_usuario'      => ['required', 'string', 'size:8', 'regex:/^[0-9A-Za-z]+$/', 'unique:usuario,doc_usuario'],
            'ema_usuario'      => ['required', 'string', 'email', 'max:200', 'unique:usuario,ema_usuario'],

            // Datos de paciente
            'tel_paciente'     => ['required', 'string', 'size:9', 'regex:/^9\d{8}$/', 'unique:paciente,tel_paciente'],
            'fch_nac_paciente' => ['required', 'date', 'before:today', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            // Datos de usuario
            'id_genero.required'               => 'El género es obligatorio.',
            'id_genero.exists'                 => 'El género seleccionado no es válido.',

            'nom_usuario.required'             => 'El nombre es obligatorio.',
            'nom_usuario.string'               => 'El nombre debe ser un texto válido.',
            'nom_usuario.max'                  => 'El nombre no debe exceder los 200 caracteres.',

            'ape_usuario.required'             => 'El apellido es obligatorio.',
            'ape_usuario.string'               => 'El apellido debe ser un texto válido.',
            'ape_usuario.max'                  => 'El apellido no debe exceder los 200 caracteres.',

            'doc_usuario.required'             => 'El documento de identidad es obligatorio.',
            'doc_usuario.string'               => 'El documento debe ser un texto válido.',
            'doc_usuario.size'                 => 'El documento debe tener 8 dígitos.',
            'doc_usuario.regex'                => 'El documento solo debe contener letras y números.',
            'doc_usuario.unique'               => 'Este documento ya se encuentra registrado.',

            'ema_usuario.required'             => 'El correo electrónico es obligatorio.',
            'ema_usuario.string'               => 'El correo electrónico debe ser un texto válido.',
            'ema_usuario.email'                => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.max'                  => 'El correo electrónico no debe exceder los 200 caracteres.',
            'ema_usuario.unique'               => 'Este correo electrónico ya se encuentra registrado.',

            // Datos de paciente
            'tel_paciente.required'            => 'El teléfono es obligatorio.',
            'tel_paciente.string'              => 'El teléfono debe ser un texto válido.',
            'tel_paciente.size'                => 'El teléfono debe tener exactamente 9 dígitos.',
            'tel_paciente.regex'               => 'El teléfono debe iniciar con 9 y tener 9 dígitos numéricos.',
            'tel_paciente.unique'              => 'Este número de teléfono ya se encuentra registrado.',

            'fch_nac_paciente.required'        => 'La fecha de nacimiento es obligatoria.',
            'fch_nac_paciente.date'            => 'La fecha de nacimiento no tiene un formato válido.',
            'fch_nac_paciente.before'          => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fch_nac_paciente.before_or_equal' => 'El paciente debe ser mayor de 18 años para registrarse de forma independiente.',
        ];
    }
}