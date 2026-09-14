<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\EstablecerPasswordRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Http\Requests\Auth\RestablecerPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\RecuperarPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $data = $request->validated();

        $usuario = User::with(['rol', 'genero', 'doctor', 'paciente'])
            ->where('ema_usuario', $data['ema_usuario'])
            ->first();

        if (!$usuario || !Hash::check($data['pas_usuario'], $usuario->pas_usuario)) {
            return $this->errorResponse('Las credenciales proporcionadas son incorrectas.', 401);
        }

        if (!$usuario->est_usuario) {
            return $this->errorResponse('Su cuenta se encuentra inactiva o pendiente de activación. Revise su correo o contacte al administrador.', 403);
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
            'est_usuario' => 1,
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

        if (!$usuario->est_usuario) {
            return $this->errorResponse('Su cuenta se encuentra inactiva. Contacte al administrador.', 403);
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
