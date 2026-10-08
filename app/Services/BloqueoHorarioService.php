<?php

namespace App\Services;

use App\Models\HorarioAtencion;
use App\Models\HorarioBloqueado;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BloqueoHorarioService
{
    protected array $diasSemana = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    /**
     * Procesa, valida y registra uno o más bloqueos de horario para un doctor.
     *
     * @param array $data Datos validados del FormRequest
     * @param bool $esDiaCompleto Indica si se bloquea toda la jornada del día
     * @return array [
     *    'ok'       => bool,
     *    'status'   => int,
     *    'message'  => string,
     *    'data'     => array|null,
     *    'es_rango' => bool
     * ]
     */
    public function registrarBloqueo(array $data, bool $esDiaCompleto): array
    {
        $idDoctor  = (int) $data['id_doctor'];
        $fchInicio = Carbon::parse($data['fch_blq_horario_bloqueado']);
        $fchFin    = !empty($data['fch_fin_bloqueado']) ? Carbon::parse($data['fch_fin_bloqueado']) : (clone $fchInicio);
        $motivo    = $data['mot_horario_bloqueado'] ?? null;
        $esRango   = $fchInicio->ne($fchFin);

        // 1. Obtener horarios de atención activos del doctor
        $horariosDoctor = HorarioAtencion::where('id_doctor', $idDoctor)
            ->where('est_horario_atencion', 1)
            ->get();

        if ($horariosDoctor->isEmpty()) {
            return [
                'ok'       => false,
                'status'   => 422,
                'message'  => 'El doctor no cuenta con ningún horario de atención activo configurado.',
                'data'     => null,
                'es_rango' => $esRango,
            ];
        }

        // 2. Determinar las jornadas y horas a bloquear en el rango
        $resultadoJornadas = $this->determinarBloqueosPorCrear(
            $fchInicio,
            $fchFin,
            $horariosDoctor,
            $esDiaCompleto,
            $esRango,
            $data
        );

        if (!$resultadoJornadas['ok']) {
            return [
                'ok'       => false,
                'status'   => $resultadoJornadas['status'],
                'message'  => $resultadoJornadas['message'],
                'data'     => null,
                'es_rango' => $esRango,
            ];
        }

        $bloqueosPorCrear = $resultadoJornadas['bloqueos'];

        // 3. Validar solapamiento con bloqueos previos ya existentes
        $errorSolapamiento = $this->validarSolapamientoConBloqueosExistentes($idDoctor, $bloqueosPorCrear);
        if ($errorSolapamiento) {
            return [
                'ok'       => false,
                'status'   => 422,
                'message'  => $errorSolapamiento,
                'data'     => null,
                'es_rango' => $esRango,
            ];
        }

        // 4. Validar conflicto con citas pendientes o confirmadas
        $errorCitas = $this->validarConflictoConCitasExistentes($idDoctor, $bloqueosPorCrear);
        if ($errorCitas) {
            return [
                'ok'       => false,
                'status'   => 409,
                'message'  => $errorCitas,
                'data'     => null,
                'es_rango' => $esRango,
            ];
        }

        // 5. Persistir los registros bajo una transacción atómica
        $registrosCreados = $this->crearBloqueosEnTransaccion($idDoctor, $bloqueosPorCrear, $motivo);

        $mensaje = count($registrosCreados) === 1
            ? "Horario bloqueado con éxito para el día {$registrosCreados[0]->fch_blq_horario_bloqueado->format('Y-m-d')}."
            : "Se bloquearon con éxito " . count($registrosCreados) . " jornadas de atención del doctor.";

        return [
            'ok'       => true,
            'status'   => 201,
            'message'  => $mensaje,
            'data'     => $registrosCreados,
            'es_rango' => $esRango,
        ];
    }

    /**
     * Recorre el rango de fechas y construye la lista de fechas y horas a bloquear.
     */
    protected function determinarBloqueosPorCrear(
        Carbon $fchInicio,
        Carbon $fchFin,
        Collection $horariosDoctor,
        bool $esDiaCompleto,
        bool $esRango,
        array $data
    ): array {
        $bloqueosPorCrear = [];
        $currentDate = clone $fchInicio;

        while ($currentDate->lte($fchFin)) {
            $fechaStr  = $currentDate->format('Y-m-d');
            $diaSemana = $currentDate->dayOfWeekIso;
            $nomDia    = $this->diasSemana[$diaSemana] ?? '';

            $turnosDia = $horariosDoctor->where('dia_sem_horario_atencion', $diaSemana);

            // Si el doctor no atiende este día
            if ($turnosDia->isEmpty()) {
                if (!$esRango) {
                    return [
                        'ok'      => false,
                        'status'  => 422,
                        'message' => "El doctor no cuenta con un horario de atención activo programado para el día $nomDia ($fechaStr).",
                    ];
                }
                // En rango continuo (ej. vacaciones), se omiten días no laborables del doctor
                $currentDate->addDay();
                continue;
            }

            if ($esDiaCompleto) {
                $horIni = $turnosDia->min('hor_ini_horario_atencion');
                $horFin = $turnosDia->max('hor_fin_horario_atencion');
            } else {
                $horIni = $data['hor_ini_horario_bloqueado'];
                $horFin = $data['hor_fin_horario_bloqueado'];

                $coincideConTurno = $turnosDia->contains(function ($h) use ($horIni, $horFin) {
                    return $h->hor_ini_horario_atencion < $horFin && $h->hor_fin_horario_atencion > $horIni;
                });

                if (!$coincideConTurno) {
                    if (!$esRango) {
                        return [
                            'ok'      => false,
                            'status'  => 422,
                            'message' => "El rango de horas a bloquear está fuera del horario de atención del doctor para el día $nomDia ($fechaStr).",
                        ];
                    }
                    $currentDate->addDay();
                    continue;
                }
            }

            $bloqueosPorCrear[] = [
                'fecha'    => $fechaStr,
                'hora_ini' => substr($horIni, 0, 5),
                'hora_fin' => substr($horFin, 0, 5),
                'nom_dia'  => $nomDia,
            ];

            $currentDate->addDay();
        }

        if (empty($bloqueosPorCrear)) {
            return [
                'ok'      => false,
                'status'  => 422,
                'message' => 'No se encontraron jornadas laborables del doctor para bloquear en el rango seleccionado.',
            ];
        }

        return [
            'ok'       => true,
            'bloqueos' => $bloqueosPorCrear,
        ];
    }

    /**
     * Valida si alguno de los bloqueos a crear choca con bloqueos ya existentes en la BD.
     */
    protected function validarSolapamientoConBloqueosExistentes(int $idDoctor, array $bloqueosPorCrear): ?string
    {
        foreach ($bloqueosPorCrear as $item) {
            if (HorarioBloqueado::existeSolapamiento($idDoctor, $item['fecha'], $item['hora_ini'], $item['hora_fin'])) {
                return "Ya existe un horario bloqueado para este doctor que se cruza con las horas seleccionadas en la fecha {$item['fecha']}.";
            }
        }

        return null;
    }

    /**
     * Valida si los bloqueos entran en conflicto con citas pendientes o confirmadas.
     */
    protected function validarConflictoConCitasExistentes(int $idDoctor, array $bloqueosPorCrear): ?string
    {
        $fechasABloquear = array_column($bloqueosPorCrear, 'fecha');

        $reservasEnRango = Reserva::with('servicio')
            ->where('id_doctor', $idDoctor)
            ->whereIn('fch_reserva', $fechasABloquear)
            ->whereIn('est_reserva', [Reserva::ESTADO_PENDIENTE, Reserva::ESTADO_CONFIRMADA])
            ->get();

        $citasEnConflicto = [];
        foreach ($bloqueosPorCrear as $item) {
            $bloqueoIni = Carbon::parse($item['fecha'] . ' ' . $item['hora_ini']);
            $bloqueoFin = Carbon::parse($item['fecha'] . ' ' . $item['hora_fin']);

            foreach ($reservasEnRango as $reserva) {
                $fchStr = $reserva->fch_reserva instanceof \Carbon\CarbonInterface
                    ? $reserva->fch_reserva->format('Y-m-d')
                    : substr((string) $reserva->fch_reserva, 0, 10);

                if ($fchStr !== $item['fecha']) {
                    continue;
                }

                $resIni = Carbon::parse($fchStr . ' ' . $reserva->hor_reserva);
                $duracion = (int) ($reserva->servicio?->dur_min_servicio ?? 30);
                $resFin = (clone $resIni)->addMinutes($duracion);

                // Cruce de intervalos: (A_ini < B_fin) && (A_fin > B_ini)
                if ($resIni->lt($bloqueoFin) && $resFin->gt($bloqueoIni)) {
                    $citasEnConflicto[] = "{$fchStr} a las {$resIni->format('H:i')}";
                }
            }
        }

        if (!empty($citasEnConflicto)) {
            $listaConflicto = implode(', ', array_unique($citasEnConflicto));
            return "No se puede bloquear este periodo: existen citas pendientes o confirmadas en las siguientes fechas/horas: [{$listaConflicto}]. Debe cancelarlas o reprogramarlas antes.";
        }

        return null;
    }

    /**
     * Inserta los bloqueos en base de datos de forma transaccional y precarga relaciones.
     */
    protected function crearBloqueosEnTransaccion(int $idDoctor, array $bloqueosPorCrear, ?string $motivo): array
    {
        $registrosCreados = DB::transaction(function () use ($idDoctor, $bloqueosPorCrear, $motivo) {
            $creados = [];
            foreach ($bloqueosPorCrear as $item) {
                $creados[] = HorarioBloqueado::create([
                    'id_doctor'                 => $idDoctor,
                    'fch_blq_horario_bloqueado' => $item['fecha'],
                    'hor_ini_horario_bloqueado' => $item['hora_ini'],
                    'hor_fin_horario_bloqueado' => $item['hora_fin'],
                    'mot_horario_bloqueado'     => $motivo,
                ]);
            }
            return $creados;
        });

        foreach ($registrosCreados as $registro) {
            $registro->load('doctor.usuario');
        }

        return $registrosCreados;
    }
}
