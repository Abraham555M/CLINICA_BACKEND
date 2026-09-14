<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecuperarPasswordRequest extends FormRequest
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
            'ema_usuario' => ['required', 'string', 'email', 'max:200', 'exists:usuario,ema_usuario'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'ema_usuario.required' => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'    => 'El correo electrónico no tiene un formato válido.',
            'ema_usuario.exists'   => 'No existe ninguna cuenta asociada a este correo electrónico.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'ema_usuario' => 'correo electrónico',
        ];
    }
}

