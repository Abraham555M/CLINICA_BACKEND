<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['nom_usuario', 'ape_usuario', 'ema_usuario', 'doc_usuario', 'pas_usuario', 'est_usuario', 'id_rol', 'id_genero'])]
#[Hidden(['pas_usuario', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    // Constantes de estado del usuario
    public const ESTADO_PENDIENTE = 0; // Pendiente de activación de contraseña/correo
    public const ESTADO_ACTIVO    = 1; // Usuario activo y operativo
    public const ESTADO_INACTIVO  = 2; // Inactivado o suspendido administrativamente

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'est_usuario' => 'integer',
        ];
    }

    /**
     * Functiones predefinidas.
     */
    public function isActivo(): bool
    {
        return $this->est_usuario === self::ESTADO_ACTIVO;
    }

    public function isPendiente(): bool
    {
        return $this->est_usuario === self::ESTADO_PENDIENTE;
    }

    public function isInactivo(): bool
    {
        return $this->est_usuario === self::ESTADO_INACTIVO;
    }

    public function getAuthPassword()
    {
        return $this->pas_usuario;
    }

    public function getAuthPasswordName()
    {
        return 'pas_usuario';
    }

    /**
     * Relaciones.
     */

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero', 'id_genero');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'id_usuario', 'id_usuario');
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'id_usuario', 'id_usuario');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Define el correo destino para las notificaciones.
     */
    public function routeNotificationForMail($notification = null): string
    {
        // Si es un dependiente con correo generado del sistema, notificar al titular responsable
        if (str_ends_with($this->ema_usuario, '@clinica.local') && $this->paciente?->usuarioResponsable) {
            return $this->paciente->usuarioResponsable->ema_usuario;
        }

        return $this->ema_usuario;
    }
}
