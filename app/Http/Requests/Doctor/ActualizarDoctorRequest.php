<?php

namespace App\Http\Requests\Doctor;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private ?Doctor $doctorModel = null;

    /**
     * Preparar datos y validar existencia del doctor antes de las reglas.
     */
    protected function prepareForValidation(): void
    {
        $idDoctor = $this->route('id_doctor');
        $this->doctorModel = Doctor::findOrFail($idDoctor);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $doctor = $this->doctorModel ?? Doctor::findOrFail($this->route('id_doctor'));
        $idDoctor = $doctor->id_doctor;
        $idUsuario = $doctor->id_usuario;

        return [
            // Datos de usuario
            'id_genero'      => ['required', 'exists:genero,id_genero'],
            'nom_usuario'    => ['required', 'string', 'max:200'],
            'ape_usuario'    => ['required', 'string', 'max:200'],
            'doc_usuario'    => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Za-z]+$/',
                Rule::unique('usuario', 'doc_usuario')->ignore($idUsuario, 'id_usuario')
            ],
            'ema_usuario'    => [
                'required',
                'email',
                'max:200',
                Rule::unique('usuario', 'ema_usuario')->ignore($idUsuario, 'id_usuario')
            ],
            'est_usuario'    => ['nullable', 'integer', 'in:0,1,2'],

            // Datos de doctor
            'cop_num_doctor' => [
                'required',
                'string',
                'max:20',
                Rule::unique('doctor', 'cop_num_doctor')->ignore($idDoctor, 'id_doctor')
            ],
            'bio_doctor'     => ['nullable', 'string', 'max:2000'],
            'img_doctor'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Datos del servicio
            'ids_servicio'   => ['nullable', 'array'],
            'ids_servicio.*' => [
                'integer',
                Rule::exists('servicio', 'id_servicio')
                    ->where('est_servicio', 1)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'id_genero.required'      => 'El género es obligatorio.',
            'id_genero.exists'        => 'El género seleccionado no existe.',
            'nom_usuario.required'    => 'El nombre es obligatorio.',
            'ape_usuario.required'    => 'Los apellidos son obligatorios.',
            'doc_usuario.required'    => 'El documento de identidad es obligatorio.',
            'doc_usuario.unique'      => 'El documento de identidad ya se encuentra registrado.',
            'ema_usuario.required'    => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'       => 'El formato del correo electrónico no es válido.',
            'ema_usuario.unique'      => 'El correo electrónico ya se encuentra registrado.',
            'cop_num_doctor.required' => 'El número de COP del doctor es obligatorio.',
            'cop_num_doctor.unique'   => 'El número de COP ingresado ya se encuentra registrado.',
            'img_doctor.image'        => 'La foto debe ser un archivo de imagen válido.',
            'img_doctor.mimes'        => 'La foto debe ser de formato JPG, JPEG o PNG.',
            'img_doctor.max'          => 'La foto no debe pesar más de 2MB.',
            'ids_servicio.array'      => 'Los servicios deben ser proporcionados en una lista.',
            'ids_servicio.*.exists'   => 'Uno o más servicios seleccionados no existen o no se encuentran activos.',
        ];
    }
}
