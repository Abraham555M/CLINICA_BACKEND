<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioAtencion extends Model
{
    protected $table = 'horario_atencion';
    
    protected $primaryKey = 'id_horario_atencion';

    protected $fillable = [
        'dia_sem_horario_atencion',
        'hor_ini_horario_atencion',
        'hor_fin_horario_atencion',
        'est_horario_atencion',
        'id_doctor'
    ];

    protected $casts = [
        'est_horario_atencion' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }

    /**
     * Verifica si existen reservas futuras pendientes o confirmadas dentro de este horario.
     */
    public function tieneReservasPendientes(): bool
    {
        return Reserva::where('id_doctor', $this->id_doctor)
            ->whereIn('est_reserva', [Reserva::ESTADO_PENDIENTE, Reserva::ESTADO_CONFIRMADA])
            ->whereDate('fch_reserva', '>=', now()->toDateString())
            ->whereRaw('WEEKDAY(fch_reserva) + 1 = ?', [$this->dia_sem_horario_atencion])
            ->whereRaw('TIME(hor_reserva) >= ?', [$this->hor_ini_horario_atencion])
            ->whereRaw('TIME(hor_reserva) < ?', [$this->hor_fin_horario_atencion])
            ->exists();
    }

    public function puedeEliminarse(): bool
    {
        return !$this->tieneReservasPendientes();
    }

    /**
     * Valida si un rango de horario se solapa con otro horario activo del mismo doctor en la base de datos.
     */
    public static function existeSolapamiento(int $idDoctor, int $diaSemana, string $horaIni, string $horaFin, ?int $ignorarId = null): bool
    {
        $horaIni = substr($horaIni, 0, 5);
        $horaFin = substr($horaFin, 0, 5);

        $query = static::where('id_doctor', $idDoctor)
            ->where('dia_sem_horario_atencion', $diaSemana)
            ->where('est_horario_atencion', 1)
            ->where(function ($q) use ($horaIni, $horaFin) {
                $q->where('hor_ini_horario_atencion', '<', $horaFin)
                  ->where('hor_fin_horario_atencion', '>', $horaIni);
            });

        if ($ignorarId) {
            $query->where('id_horario_atencion', '!=', $ignorarId);
        }

        return $query->exists();
    }

    /**
     * Valida si existen solapamientos internos dentro de una lista de horarios recibidos en una petición.
     * Retorna el mensaje de error si hay cruce, o null si todos los turnos son compatibles.
     */
    public static function validarSolapamientoEnLista(array $horarios): ?string
    {
        $diasSemana = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
        ];

        $porDia = [];
        foreach ($horarios as $h) {
            $dia = (int) $h['dia_sem_horario_atencion'];
            $porDia[$dia][] = $h;
        }

        foreach ($porDia as $dia => $turnos) {
            $total = count($turnos);
            for ($i = 0; $i < $total; $i++) {
                for ($j = $i + 1; $j < $total; $j++) {
                    $iniA = substr($turnos[$i]['hor_ini_horario_atencion'], 0, 5);
                    $finA = substr($turnos[$i]['hor_fin_horario_atencion'], 0, 5);
                    $iniB = substr($turnos[$j]['hor_ini_horario_atencion'], 0, 5);
                    $finB = substr($turnos[$j]['hor_fin_horario_atencion'], 0, 5);

                    if ($iniA < $finB && $finA > $iniB) {
                        $nomDia = $diasSemana[$dia] ?? "Día $dia";
                        return "Existe cruce entre los turnos programados para el día $nomDia ($iniA-$finA con $iniB-$finB).";
                    }
                }
            }
        }

        return null;
    }
}
