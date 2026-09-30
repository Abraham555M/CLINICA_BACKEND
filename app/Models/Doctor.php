<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;
    
    protected $table = 'doctor';
    
    protected $primaryKey = 'id_doctor';

    protected $fillable = [
        'cop_num_doctor',
        'bio_doctor',
        'img_doctor',
        'id_usuario'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function horariosAtencion()
    {
        return $this->hasMany(HorarioAtencion::class, 'id_doctor', 'id_doctor');
    }

    public function horariosBloqueado()
    {
        return $this->hasMany(HorarioBloqueado::class, 'id_doctor', 'id_doctor');
    }

    public function especialidadDoctores()
    {
        return $this->hasMany(EspecialidadDoctor::class, 'id_doctor', 'id_doctor');
    }

    public function servicioDoctores()
    {
        return $this->hasMany(ServicioDoctor::class, 'id_doctor', 'id_doctor');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_doctor', 'id_doctor', 'id_servicio')
                    ->withTimestamps();
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_doctor', 'id_doctor');
    }

    // Métodos complementarios
    public function tieneHorarioActivo(): bool
    {
        return $this->horariosAtencion()
                    ->where('est_horario_atencion', true)
                    ->exists();
    }

    public function puedeEliminarse(): bool
    {
        return !$this->tieneHorarioActivo();
    }
}
