<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\HorarioAtencion;
use App\Models\HorarioBloqueado;
use App\Models\Reserva;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DisponibilidadService
{
    protected array $nombresDiasSemana = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    /**
     * Calcula los slots y la disponibilidad de un doctor para un servicio y fecha específicos.
     *
     * @param int $idDoctor
     * @param int $idServicio
     * @param string $fecha Cadena en formato Y-m-d
     * @return array [
     *    'ok'      => bool,
     *    'status'  => int,
     *    'message' => string,
     *    'data'    => array|null
     * ]
     */
    public function calcularDisponibilidad(int $idDoctor, int $idServicio, string $fecha): array
    {
        // 1. Validar existencia y estado del doctor
        $doctor = Doctor::with('usuario')->find($idDoctor);
        if (!$doctor || $doctor->usuario?->est_usuario !== User::ESTADO_ACTIVO) {
            return [
                'ok'      => false,
                'status'  => 404,
                'message' => 'El doctor no existe o se encuentra inactivo.',
                'data'    => null,
            ];
        }

        // 2. Validar existencia y estado del servicio
        $servicio = Servicio::find($idServicio);
        if (!$servicio || !$servicio->est_servicio) {
            return [
                'ok'      => false,
                'status'  => 404,
                'message' => 'El servicio odontológico no existe o no se encuentra activo.',
                'data'    => null,
            ];
        }

        // 3. Validar que el doctor brinde dicho servicio
        $ofreceServicio = $doctor->servicios()->where('servicio.id_servicio', $idServicio)->exists();
        if (!$ofreceServicio) {
            return [
                'ok'      => false,
                'status'  => 422,
                'message' => 'El doctor seleccionado no brinda el servicio especificado.',
                'data'    => null,
            ];
        }

        $fechaCarbon = Carbon::parse($fecha);
        $diaSemana = $fechaCarbon->dayOfWeekIso; // 1: Lunes, ..., 7: Domingo
        $nomDia = $this->nombresDiasSemana[$diaSemana] ?? '';

        // 4. Obtener horarios de atención configurados y activos para el día
        $horariosAtencion = HorarioAtencion::where('id_doctor', $idDoctor)
            ->where('dia_sem_horario_atencion', $diaSemana)
            ->where('est_horario_atencion', 1)
            ->orderBy('hor_ini_horario_atencion', 'asc')
            ->get();

        $duracionMinutos = (int) ($servicio->dur_min_servicio ?? 30);
        if ($duracionMinutos <= 0) {
            $duracionMinutos = 30;
        }

        $nomDoctor = trim(($doctor->usuario?->nom_usuario ?? '') . ' ' . ($doctor->usuario?->ape_usuario ?? ''));

        // Si el doctor no atiende este día de la semana
        if ($horariosAtencion->isEmpty()) {
            return [
                'ok'      => true,
                'status'  => 200,
                'message' => "El doctor no cuenta con horario de atención para los días {$nomDia}.",
                'data'    => [
                    'id_doctor'         => $doctor->id_doctor,
                    'nom_doctor'        => $nomDoctor,
                    'id_servicio'       => $servicio->id_servicio,
                    'nom_servicio'      => $servicio->nom_servicio,
                    'dur_min_servicio'  => $duracionMinutos,
                    'fecha'             => $fecha,
                    'nom_dia'           => $nomDia,
                    'slots_disponibles' => [],
                ],
            ];
        }

        // 5. Cargar bloqueos y reservas del día
        $bloqueos = HorarioBloqueado::where('id_doctor', $idDoctor)
            ->whereDate('fch_blq_horario_bloqueado', $fecha)
            ->get();

        $reservas = Reserva::with('servicio')
            ->where('id_doctor', $idDoctor)
            ->whereDate('fch_reserva', $fecha)
            ->whereNotIn('est_reserva', [Reserva::ESTADO_CANCELADA, Reserva::ESTADO_EXPIRADA])
            ->get();

        // 6. Generar slots libres
        $slotsDisponibles = $this->generarSlots(
            $horariosAtencion,
            $fechaCarbon,
            $duracionMinutos,
            $bloqueos,
            $reservas
        );

        return [
            'ok'      => true,
            'status'  => 200,
            'message' => 'Disponibilidad y slots calculados con éxito.',
            'data'    => [
                'id_doctor'         => $doctor->id_doctor,
                'nom_doctor'        => $nomDoctor,
                'id_servicio'       => $servicio->id_servicio,
                'nom_servicio'      => $servicio->nom_servicio,
                'dur_min_servicio'  => $duracionMinutos,
                'fecha'             => $fecha,
                'nom_dia'           => $nomDia,
                'slots_disponibles' => $slotsDisponibles,
            ],
        ];
    }

    /**
     * Itera los turnos del doctor y produce los slots que no tienen conflicto con bloqueos ni citas.
     */
    protected function generarSlots(
        Collection $horariosAtencion,
        Carbon $fechaCarbon,
        int $duracionMinutos,
        Collection $bloqueos,
        Collection $reservas
    ): array {
        $fechaStr = $fechaCarbon->toDateString();
        $esHoy = $fechaCarbon->isToday();
        $horaActualStr = now()->format('H:i');

        // Intervalo de cuadrícula (máximo 30 minutos o la duración si es menor)
        $intervaloMinutos = min(30, $duracionMinutos);
        if ($intervaloMinutos <= 0) {
            $intervaloMinutos = 30;
        }

        $slotsDisponibles = [];

        foreach ($horariosAtencion as $horario) {
            $inicioTurno = Carbon::parse($fechaStr . ' ' . $horario->hor_ini_horario_atencion);
            $finTurno    = Carbon::parse($fechaStr . ' ' . $horario->hor_fin_horario_atencion);

            $slotInicio = clone $inicioTurno;

            while ($slotInicio->copy()->addMinutes($duracionMinutos)->lte($finTurno)) {
                $slotFin = $slotInicio->copy()->addMinutes($duracionMinutos);
                $slotHoraStr = $slotInicio->format('H:i');

                // Si es hoy, omitir horas que ya pasaron
                if ($esHoy && $slotHoraStr <= $horaActualStr) {
                    $slotInicio->addMinutes($intervaloMinutos);
                    continue;
                }

                // Omitir si choca con bloqueo o con reserva existente
                if (
                    !$this->chocaConBloqueos($slotInicio, $slotFin, $fechaStr, $bloqueos) &&
                    !$this->chocaConReservas($slotInicio, $slotFin, $reservas)
                ) {
                    if (!in_array($slotHoraStr, $slotsDisponibles, true)) {
                        $slotsDisponibles[] = $slotHoraStr;
                    }
                }

                $slotInicio->addMinutes($intervaloMinutos);
            }
        }

        return $slotsDisponibles;
    }

    /**
     * Comprueba si un slot se solapa con algún horario bloqueado del doctor.
     */
    protected function chocaConBloqueos(Carbon $slotInicio, Carbon $slotFin, string $fechaStr, Collection $bloqueos): bool
    {
        foreach ($bloqueos as $bloqueo) {
            $blqInicio = Carbon::parse($fechaStr . ' ' . $bloqueo->hor_ini_horario_bloqueado);
            $blqFin    = Carbon::parse($fechaStr . ' ' . $bloqueo->hor_fin_horario_bloqueado);

            // Se cruzan si slotInicio < blqFin && slotFin > blqInicio
            if ($slotInicio->lt($blqFin) && $slotFin->gt($blqInicio)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Comprueba si un slot se solapa con alguna reserva ya existente.
     */
    protected function chocaConReservas(Carbon $slotInicio, Carbon $slotFin, Collection $reservas): bool
    {
        foreach ($reservas as $reserva) {
            $resFechaStr = $reserva->fch_reserva instanceof \Carbon\CarbonInterface
                ? $reserva->fch_reserva->format('Y-m-d')
                : substr((string) $reserva->fch_reserva, 0, 10);

            $resInicio = Carbon::parse($resFechaStr . ' ' . $reserva->hor_reserva);
            $resDuracion = (int) ($reserva->servicio?->dur_min_servicio ?? 30);
            $resFin = $resInicio->copy()->addMinutes($resDuracion);

            // Se cruzan si slotInicio < resFin && slotFin > resInicio
            if ($slotInicio->lt($resFin) && $slotFin->gt($resInicio)) {
                return true;
            }
        }

        return false;
    }
}
