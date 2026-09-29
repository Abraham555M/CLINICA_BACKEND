<?php

namespace App\Console\Commands;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LimpiarPacientesNoActivados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pacientes:limpiar-inactivos {--dias=3 : Cantidad de días de antigüedad permitidos antes de eliminar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina de forma segura las cuentas de pacientes que no completaron su activación por correo y no tienen registros clínicos asociados';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dias = (int) $this->option('dias');
        if ($dias < 1) {
            $dias = 3;
        }

        $fechaLimite = now()->subDays($dias);

        $this->info("Buscando pacientes no activados registrados antes de: {$fechaLimite->toDateTimeString()} ({$dias} días)...");

        // 1. Filtrar SOLO usuarios con rol Paciente, en estado PENDIENTE DE ACTIVACIÓN (0) y creados antes de la fecha límite
        $usuariosCandidatos = User::where('est_usuario', User::ESTADO_PENDIENTE)
            ->whereHas('rol', fn($q) => $q->where('nom_rol', 'Paciente'))
            ->where('created_at', '<', $fechaLimite)
            ->whereDoesntHave('paciente.reservas') // Seguridad: No deben tener reservas/citas
            ->whereDoesntHave('paciente.antecedentes') // Seguridad: No deben tener antecedentes médicos
            ->with(['paciente'])
            ->get();

        $eliminados = 0;

        foreach ($usuariosCandidatos as $usuario) {
            // Seguridad adicional: Verificar que no sea responsable de otros pacientes dependientes
            $tieneDependientes = Paciente::where('id_usuario_responsable', $usuario->id_usuario)
                ->where('id_usuario', '!=', $usuario->id_usuario)
                ->exists();

            if ($tieneDependientes) {
                $this->warn("Omitiendo usuario ID {$usuario->id_usuario} ({$usuario->ema_usuario}) porque tiene dependientes asignados.");
                continue;
            }

            DB::transaction(function () use ($usuario) {
                DB::table('password_reset_tokens') // Eliminar token de activación pendiente si existe
                    ->where('ema_usuario', $usuario->ema_usuario)
                    ->delete();

                $usuario->paciente?->delete(); // Eliminar perfil de paciente si existe

                $usuario->delete(); // Eliminar usuario
            });

            $eliminados++;
        }

        $mensaje = "Proceso finalizado. Se eliminaron {$eliminados} cuenta(s) de pacientes pendientes de activación con más de {$dias} días de abandono.";
        $this->info($mensaje);
        Log::info("[Scheduler] {$mensaje}");

        return Command::SUCCESS;
    }
}