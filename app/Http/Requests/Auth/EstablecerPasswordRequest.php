<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EstablecerPasswordRequest extends FormRequest
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
        if ($this->has('ema_usuario')) {
            $this->merge([
                'ema_usuario' => strtolower(trim($this->ema_usuario)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token'       => ['required', 'string'],
            'ema_usuario' => ['required', 'string', 'email', 'max:200', 'exists:usuario,ema_usuario'],
            'pas_usuario' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'token.required'        => 'El token de activación es obligatorio.',
            'ema_usuario.required'  => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'     => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.exists'    => 'No se encontró un usuario registrado con este correo electrónico.',
            'pas_usuario.required'  => 'La nueva contraseña es obligatoria.',
            'pas_usuario.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'pas_usuario.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'token'       => 'token de activación',
            'ema_usuario' => 'correo electrónico',
            'pas_usuario' => 'contraseña',
        ];
    }
}
