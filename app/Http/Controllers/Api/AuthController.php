<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\EstablecerPasswordRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Http\Requests\Auth\RegistroPacienteRequest;
use App\Http\Requests\Auth\RestablecerPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Paciente;
use App\Models\Rol;
use App\Models\User;
use App\Notifications\PacienteBienvenidaNotification;
use App\Notifications\RecuperarPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function registroPaciente(RegistroPacienteRequest $request)
    {
        $data = $request->validated();

        $idRolPaciente = Rol::where('nom_rol', 'Paciente')->value('id_rol');

        if (!$idRolPaciente) {
            return $this->errorResponse('El rol Paciente no se encuentra configurado en el sistema.', 500);
        }

        $plainToken = Str::random(64);

        DB::transaction(function () use ($data, $idRolPaciente, $plainToken) {
            $usuario = User::create([
                'id_rol'      => $idRolPaciente,
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $data['ema_usuario'],
                'pas_usuario' => Hash::make(Str::random(32)), // Clave aleatoria temporal
                'est_usuario' => User::ESTADO_PENDIENTE,     // 0 = Inactivo hasta que establezca contraseña por correo
            ]);

            Paciente::create([
                'id_usuario'             => $usuario->id_usuario,
                'tel_paciente'           => $data['tel_paciente'],
                'fch_nac_paciente'       => $data['fch_nac_paciente'],
                'sld_fav_paciente'       => 0.00,
                'id_usuario_responsable' => $usuario->id_usuario,
            ]);

            // Guardar token de activacion en password_reset_tokens
            DB::table('password_reset_tokens')->where('ema_usuario', $usuario->ema_usuario)->delete();
            DB::table('password_reset_tokens')->insert([
                'ema_usuario' => $usuario->ema_usuario,
                'token'       => Hash::make($plainToken),
                'created_at'  => now(),
            ]);

            // Enviar correo con el enlace de activacion
            $usuario->notify(new PacienteBienvenidaNotification($plainToken));
        });

        return $this->successResponse(
            null,
            'Registro completado con éxito. Hemos enviado un correo con un enlace de activación para que establezcas tu contraseña.',
            201
        );
    }

    public function reenviarActivacion(Request $request)
    {
        $request->validate([
            'ema_usuario' => 'required|email|exists:usuario,ema_usuario',
        ], [
            'ema_usuario.required' => 'El correo electrónico es obligatorio.',
            'ema_usuario.email'    => 'El correo electrónico no es válido.',
            'ema_usuario.exists'   => 'No encontramos ningún usuario registrado con este correo.',
        ]);

        $usuario = User::where('ema_usuario', strtolower(trim($request->ema_usuario)))->first();

        if ($usuario->est_usuario === User::ESTADO_ACTIVO) {
            return $this->errorResponse('Esta cuenta ya se encuentra activa. Puedes iniciar sesión directamente.', 400);
        }

        if ($usuario->est_usuario === User::ESTADO_INACTIVO) {
            return $this->errorResponse('Esta cuenta se encuentra inactiva o suspendida. Contacte a la administración de la clínica.', 403);
        }

        $plainToken = Str::random(64);

        DB::table('password_reset_tokens')->where('ema_usuario', $usuario->ema_usuario)->delete();
        DB::table('password_reset_tokens')->insert([
            'ema_usuario' => $usuario->ema_usuario,
            'token'       => Hash::make($plainToken),
            'created_at'  => now(),
        ]);

        $usuario->notify(new PacienteBienvenidaNotification($plainToken));

        return $this->successResponse(
            null,
            'Se ha enviado un nuevo enlace de activación a tu correo electrónico.'
        );
    }

    public function login(AuthRequest $request)
    {
        $data = $request->validated();

        if (str_ends_with($data['ema_usuario'], '@clinica.local')) {
            return $this->errorResponse('Esta cuenta corresponde a un dependiente y debe ser gestionada desde la sesión del titular responsable.', 403);
        }

        $usuario = User::with(['rol', 'genero', 'doctor', 'paciente'])
            ->where('ema_usuario', $data['ema_usuario'])
            ->first();

        if (!$usuario || !Hash::check($data['pas_usuario'], $usuario->pas_usuario)) {
            return $this->errorResponse('Las credenciales proporcionadas son incorrectas.', 401);
        }

        if ($usuario->est_usuario === User::ESTADO_PENDIENTE) {
            return $this->errorResponse('Su cuenta se encuentra pendiente de activación. Por favor, revise su correo o solicite un nuevo enlace de activación.', 403);
        }

        if ($usuario->est_usuario === User::ESTADO_INACTIVO) {
            return $this->errorResponse('Su cuenta se encuentra inactiva o suspendida. Comuníquese con la administración de la clínica.', 403);
        }

        // El token expira en 24 horas (puedes ajustar el tiempo con addHours, addDays, etc.)
        $token = $usuario->createToken('auth', ['*'], now()->addHours(24))->plainTextToken;

        return $this->successResponse([
            'token'   => $token,
            'usuario' => new UserResource($usuario),
        ], 'Inicio de sesión exitoso.');
    }

    public function establecerPassword(EstablecerPasswordRequest $request)
    {
        $data = $request->validated();

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('ema_usuario', $data['ema_usuario'])
            ->first();

        if (!$tokenRecord || !Hash::check($data['token'], $tokenRecord->token)) {
            return $this->errorResponse('El enlace o token de activación no es válido o ya fue utilizado.', 400);
        }

        // Verificar expiración del token (24 horas)
        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addHours(24)->isPast()) {
            DB::table('password_reset_tokens')->where('ema_usuario', $data['ema_usuario'])->delete();
            return $this->errorResponse('El enlace de activación ha expirado. Solicite un nuevo enlace al administrador.', 400);
        }

        $usuario = User::where('ema_usuario', $data['ema_usuario'])->first();

        if (!$usuario) {
            return $this->errorResponse('No se encontró el usuario asociado a este correo.', 404);
        }

        $usuario->update([
            'pas_usuario' => Hash::make($data['pas_usuario']),
            'est_usuario' => User::ESTADO_ACTIVO, 
        ]);

        // Eliminar token tras su uso
        DB::table('password_reset_tokens')->where('ema_usuario', $data['ema_usuario'])->delete();

        return $this->successResponse(
            null,
            'Contraseña establecida y cuenta activada con éxito. Ya puedes iniciar sesión.'
        );
    }

    public function recuperarPassword(RecuperarPasswordRequest $request)
    {
        $data = $request->validated();

        $usuario = User::where('ema_usuario', $data['ema_usuario'])->first();

        if (!$usuario) {
            return $this->errorResponse('No se encontró el usuario asociado a este correo.', 404);
        }

        if ($usuario->est_usuario === User::ESTADO_PENDIENTE) {
            return $this->errorResponse('Su cuenta aún no ha sido activada. Debe completar primero la activación enviada a su correo.', 403);
        }

        if ($usuario->est_usuario === User::ESTADO_INACTIVO) {
            return $this->errorResponse('Su cuenta se encuentra inactiva o suspendida. Contacte al administrador.', 403);
        }

        $plainToken = Str::random(64);

        // Guardar token en password_reset_tokens
        DB::table('password_reset_tokens')->where('ema_usuario', $usuario->ema_usuario)->delete();
        DB::table('password_reset_tokens')->insert([
            'ema_usuario' => $usuario->ema_usuario,
            'token'       => Hash::make($plainToken),
            'created_at'  => now(),
        ]);

        // Enviar correo con el enlace de restablecimiento
        $usuario->notify(new RecuperarPasswordNotification($plainToken));

        return $this->successResponse(
            [
                'token' => $plainToken,
            ],
            'Se ha enviado un enlace de recuperación a tu correo electrónico.'
        );
    }

    public function restablecerPassword(RestablecerPasswordRequest $request)
    {
        $data = $request->validated();

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('ema_usuario', $data['ema_usuario'])
            ->first();

        if (!$tokenRecord || !Hash::check($data['token'], $tokenRecord->token)) {
            return $this->errorResponse('El enlace o token de recuperación no es válido o ya fue utilizado.', 400);
        }

        // Verificar expiración del token (2 horas)
        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addHours(2)->isPast()) {
            DB::table('password_reset_tokens')->where('ema_usuario', $data['ema_usuario'])->delete();
            return $this->errorResponse('El enlace de recuperación ha expirado. Por favor, solicite uno nuevo.', 400);
        }

        $usuario = User::where('ema_usuario', $data['ema_usuario'])->first();

        if (!$usuario) {
            return $this->errorResponse('No se encontró el usuario asociado a este correo.', 404);
        }

        $usuario->update([
            'pas_usuario' => Hash::make($data['pas_usuario']),
        ]);

        // Eliminar token tras su uso
        DB::table('password_reset_tokens')->where('ema_usuario', $data['ema_usuario'])->delete();

        return $this->successResponse(
            null,
            'Contraseña restablecida con éxito. Ya puedes iniciar sesión con tu nueva contraseña.'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Sesión cerrada con éxito.');
    }
}