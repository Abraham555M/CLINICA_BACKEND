<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Consulta las reservas futuras activas (Pendientes o Confirmadas) de este doctor.
     */
    public function reservasFuturasActivas()
    {
        return $this->reservas()
            ->with('servicio')
            ->whereIn('est_reserva', [Reserva::ESTADO_PENDIENTE, Reserva::ESTADO_CONFIRMADA])
            ->whereDate('fch_reserva', '>=', now()->toDateString());
    }

    /**
     * Valida que todas las citas futuras activas del doctor quepan dentro de los horarios propuestos.
     * Retorna el mensaje de conflicto si alguna cita queda desamparada, o null si todo está cubierto.
     */
    public function obtenerConflictoConReservasFuturas(array $horariosPropuestos): ?string
    {
        $reservasFuturas = $this->reservasFuturasActivas()->get();

        foreach ($reservasFuturas as $reserva) {
            $fchStr = $reserva->fch_reserva instanceof \Carbon\CarbonInterface
                ? $reserva->fch_reserva->format('Y-m-d')
                : substr((string) $reserva->fch_reserva, 0, 10);

            $diaReserva = Carbon::parse($fchStr)->dayOfWeekIso;
            $horaIniReserva = Carbon::parse($reserva->hor_reserva)->format('H:i');
            $duracion = (int) ($reserva->servicio?->dur_min_servicio ?? 30);
            $horaFinReserva = Carbon::parse($reserva->hor_reserva)->addMinutes($duracion)->format('H:i');

            $encaja = false;
            foreach ($horariosPropuestos as $h) {
                if ((int) $h['dia_sem_horario_atencion'] === $diaReserva) {
                    $hIni = substr($h['hor_ini_horario_atencion'], 0, 5);
                    $hFin = substr($h['hor_fin_horario_atencion'], 0, 5);
                    if ($hIni <= $horaIniReserva && $hFin >= $horaFinReserva) {
                        $encaja = true;
                        break;
                    }
                }
            }

            if (!$encaja) {
                return "No se puede actualizar el horario: el doctor tiene citas futuras activas el $fchStr a las $horaIniReserva que quedarían fuera del nuevo cronograma.";
            }
        }

        return null;
    }
}
