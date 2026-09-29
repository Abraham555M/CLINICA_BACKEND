<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\ActualizarDependienteRequest;
use App\Http\Requests\Paciente\ActualizarMiPerfilRequest;
use App\Http\Requests\Paciente\ActualizarPacienteRequest;
use App\Http\Requests\Paciente\RegistrarDependienteRequest;
use App\Http\Requests\Paciente\RegistrarPacienteRequest;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PacienteController extends Controller
{
    public function selectPacientes()
    {
        $pacientes = Paciente::join('usuario', 'paciente.id_usuario', '=', 'usuario.id_usuario')
            ->where('usuario.est_usuario', User::ESTADO_ACTIVO)
            ->orderBy('usuario.nom_usuario', 'asc')
            ->orderBy('usuario.ape_usuario', 'asc')
            ->select([
                'paciente.id_paciente',
                'usuario.nom_usuario',
                'usuario.ape_usuario',
                'usuario.doc_usuario',
            ])
            ->get();

        return $this->successResponse($pacientes, 'Select de pacientes obtenido con éxito.');
    }

    public function listarPacientes(Request $request)
    {
        $query = Paciente::with(['usuario.genero'])
            ->join('usuario', 'paciente.id_usuario', '=', 'usuario.id_usuario')
            ->select('paciente.*');

        // Búsqueda por término (nombre, apellido, documento, teléfono)
        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('usuario.nom_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('usuario.ape_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('usuario.doc_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('paciente.tel_paciente', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtro por estado del usuario (0 = pendiente, 1 = activo, 2 = inactivo)
        if ($request->filled('est_usuario')) {
            $query->where('usuario.est_usuario', (int) $request->input('est_usuario'));
        }

        // Ordenamiento por defecto (alfabético por nombre y apellido)
        $query->orderBy('usuario.nom_usuario', 'asc')
              ->orderBy('usuario.ape_usuario', 'asc');

        // Retornar lista completa si se solicita explícitamente (?all=true)
        if ($request->boolean('all')) {
            $pacientes = $query->get();
            return $this->successResponse(
                PacienteResource::collection($pacientes),
                'Lista de pacientes obtenida con éxito.'
            );
        }

        // Paginación por defecto (10 por página o según ?per_page)
        $perPage = $request->integer('per_page', 10);
        $pacientes = $query->paginate($perPage);

        return $this->successResponse([
            'pacientes'  => PacienteResource::collection($pacientes),
            'paginacion' => [
                'total'        => $pacientes->total(),
                'per_page'     => $pacientes->perPage(),
                'current_page' => $pacientes->currentPage(),
                'last_page'    => $pacientes->lastPage(),
                'from'         => $pacientes->firstItem(),
                'to'           => $pacientes->lastItem(),
            ]
        ], 'Lista de pacientes obtenida con éxito.');
    }

    public function obtenerMiPerfil()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $paciente = $usuario->paciente;

        if (!$paciente) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de paciente asociado.', 404);
        }

        $paciente->load(['usuario.genero']);

        return $this->successResponse(
            new PacienteResource($paciente),
            'Perfil del paciente obtenido con éxito.'
        );
    }

    public function actualizarMiPerfil(ActualizarMiPerfilRequest $request)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $paciente = $usuario->paciente;

        if (!$paciente) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de paciente asociado.', 404);
        }

        $data = $request->validated();

        DB::transaction(function () use ($usuario, $paciente, $data) {
            $usuario->update([
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'ema_usuario' => $data['ema_usuario'],
            ]);

            $paciente->update([
                'tel_paciente'     => $data['tel_paciente'],
                'fch_nac_paciente' => $data['fch_nac_paciente'],
            ]);
        });

        $paciente->load(['usuario.genero']);

        return $this->successResponse(
            new PacienteResource($paciente),
            'Perfil actualizado con éxito.'
        );
    }

    public function registrarPaciente(RegistrarPacienteRequest $request)
    {
        $data = $request->validated();

        // 1. Obtener ID del Rol "Paciente" de forma segura
        $idRolPaciente = Rol::where('nom_rol', 'Paciente')->value('id_rol') ?? $data['id_rol'];

        $paciente = DB::transaction(function () use ($data, $idRolPaciente) {
            $usuario = User::create([
                'id_rol'      => $idRolPaciente,
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $data['ema_usuario'],
                'pas_usuario' => Hash::make($data['pas_usuario']),
                'est_usuario' => User::ESTADO_ACTIVO,
            ]);

            return Paciente::create([
                'id_usuario'             => $usuario->id_usuario,
                'tel_paciente'           => $data['tel_paciente'],
                'fch_nac_paciente'       => $data['fch_nac_paciente'],
                'sld_fav_paciente'       => $data['sld_fav_paciente'] ?? 0.00,
                'id_usuario_responsable' => auth()->id() ?? $usuario->id_usuario,
            ]);
        });

        $paciente->load(['usuario.genero']);

        return $this->successResponse(
            new PacienteResource($paciente),
            'Paciente registrado con éxito.',
            201
        );
    }

    public function obtenerPacientePorId($id_paciente){
        $paciente = Paciente::with(['usuario.genero'])
                            ->findOrFail($id_paciente);

        return $this->successResponse(new PacienteResource($paciente), 'Detalle del paciente obtenido con éxito.');
    }

    public function actualizarPaciente(ActualizarPacienteRequest $request, $id_paciente)
    {
        $paciente = Paciente::with('usuario')->findOrFail($id_paciente);
        $usuario = $paciente->usuario;

        $data = $request->validated();

        DB::transaction(function () use ($usuario, $paciente, $data) {
            $userData = [
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $data['ema_usuario'],
            ];

            if (isset($data['est_usuario'])) {
                $userData['est_usuario'] = $data['est_usuario'];
            }

            if (!empty($data['pas_usuario'])) {
                $userData['pas_usuario'] = Hash::make($data['pas_usuario']);
            }

            $usuario->update($userData);

            $pacienteData = [
                'tel_paciente'     => $data['tel_paciente'],
                'fch_nac_paciente' => $data['fch_nac_paciente'],
            ];

            if (array_key_exists('sld_fav_paciente', $data)) {
                $pacienteData['sld_fav_paciente'] = $data['sld_fav_paciente'];
            }

            if (array_key_exists('id_usuario_responsable', $data)) {
                $pacienteData['id_usuario_responsable'] = $data['id_usuario_responsable'] ?? $usuario->id_usuario;
            }

            $paciente->update($pacienteData);
        });

        $paciente->load(['usuario.genero']);

        return $this->successResponse(
            new PacienteResource($paciente),
            'Paciente actualizado con éxito.'
        );
    }

    private function obtenerDependientes($idUsuario)
    {
        return Paciente::with(['usuario.genero'])
            ->where('id_usuario_responsable', $idUsuario)
            ->where('id_usuario', '!=', $idUsuario) // Excluir al usuario titular
            ->get()
            ->map(function ($paciente) {
                $usuario = $paciente->usuario;

                return [
                    'id_paciente'      => $paciente->id_paciente,
                    'nombre_completo'  => trim(($usuario->nom_usuario ?? '') . ' ' . ($usuario->ape_usuario ?? '')),
                    'nom_usuario'      => $usuario->nom_usuario ?? null,
                    'ape_usuario'      => $usuario->ape_usuario ?? null,
                    'doc_usuario'      => $usuario->doc_usuario ?? null,
                    'tel_paciente'     => $paciente->tel_paciente,
                    'fch_nac_paciente' => $paciente->fch_nac_paciente?->format('Y-m-d'),
                    'edad'             => $paciente->fch_nac_paciente?->age, // Calculado por Carbon automáticamente
                    'id_genero'        => $usuario?->id_genero ?? null,
                    'genero'           => $usuario?->genero?->nom_genero ?? null,
                    'ema_usuario'      => $usuario?->ema_usuario ?? null,
                ];
            });
    }

    public function misDependientes()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $dependientes = $this->obtenerDependientes($usuario->id_usuario);

        return $this->successResponse($dependientes, 'Dependientes obtenidos con éxito.');
    }

    public function registrarDependiente(RegistrarDependienteRequest $request)
    {
        $titular = auth()->user();

        if (!$titular) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $data = $request->validated();
        $idRolPaciente = Rol::where('nom_rol', 'Paciente')->value('id_rol') ?? 4;

        // Generar un correo interno seguro si el dependiente (ej: menor) no tiene email propio
        $email = $data['ema_usuario'] ?? ('dep_' . $data['doc_usuario'] . '@clinica.local');

        // Contraseña aleatoria (el acceso lo gestiona el titular desde su cuenta)
        $password = Hash::make(Str::random(24));

        // Teléfono: usar el provisto o heredar del titular
        $telefono = $data['tel_paciente'] ?? ($titular->paciente?->tel_paciente ?? '000000000');

        $dependiente = DB::transaction(function () use ($data, $idRolPaciente, $email, $password, $telefono, $titular) {
            $usuario = User::create([
                'id_rol'      => $idRolPaciente,
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $email,
                'pas_usuario' => $password,
                'est_usuario' => User::ESTADO_ACTIVO,
            ]);

            $paciente = Paciente::create([
                'id_usuario'             => $usuario->id_usuario,
                'tel_paciente'           => $telefono,
                'fch_nac_paciente'       => $data['fch_nac_paciente'],
                'sld_fav_paciente'       => 0.00,
                'id_usuario_responsable' => $titular->id_usuario,
            ]);

            return $paciente;
        });

        $dependiente->load(['usuario.genero']);

        return $this->successResponse([
            'id_paciente'      => $dependiente->id_paciente,
            'nombre_completo'  => trim($dependiente->usuario->nom_usuario . ' ' . $dependiente->usuario->ape_usuario),
            'nom_usuario'      => $dependiente->usuario->nom_usuario,
            'ape_usuario'      => $dependiente->usuario->ape_usuario,
            'doc_usuario'      => $dependiente->usuario->doc_usuario,
            'tel_paciente'     => $dependiente->tel_paciente,
            'fch_nac_paciente' => $dependiente->fch_nac_paciente?->format('Y-m-d'),
            'edad'             => $dependiente->fch_nac_paciente?->age,
            'id_genero'        => $dependiente->usuario->id_genero,
            'genero'           => $dependiente->usuario?->genero?->nom_genero ?? null,
            'ema_usuario'      => $dependiente->usuario->ema_usuario,
        ], 'Dependiente registrado con éxito.', 201);
    }

    public function actualizarDependiente(ActualizarDependienteRequest $request, $id_paciente)
    {
        $titular = auth()->user();

        if (!$titular) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $paciente = Paciente::with('usuario')->find($id_paciente);

        if (!$paciente) {
            return $this->errorResponse('El paciente dependiente no fue encontrado.', 404);
        }

        // 1. Validar que el usuario autenticado sea el responsable asignado
        if ($paciente->id_usuario_responsable !== $titular->id_usuario) {
            return $this->errorResponse('No tienes autorización para editar los datos de este dependiente.', 403);
        }

        // 2. Prevenir que el titular se edite a sí mismo a través de esta ruta
        if ($paciente->id_usuario === $titular->id_usuario) {
            return $this->errorResponse('No puedes editar tu propio perfil desde la gestión de dependientes.', 422);
        }

        $data = $request->validated();
        $usuario = $paciente->usuario;

        DB::transaction(function () use ($usuario, $paciente, $data, $titular) {
            // Si el correo viene vacío, mantenemos o regeneramos el correo técnico con el documento actualizado
            $email = !empty($data['ema_usuario'])
                ? $data['ema_usuario']
                : ('dep_' . $data['doc_usuario'] . '@clinica.local');

            $usuario->update([
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $email,
            ]);

            // Actualización de datos clínicos del paciente
            $pacienteData = [
                'fch_nac_paciente' => $data['fch_nac_paciente'],
            ];

            if (array_key_exists('tel_paciente', $data)) {
                $pacienteData['tel_paciente'] = $data['tel_paciente'] ?? ($titular->paciente?->tel_paciente ?? '000000000');
            }

            $paciente->update($pacienteData);
        });

        $paciente->load(['usuario.genero']);

        return $this->successResponse([
            'id_paciente'      => $paciente->id_paciente,
            'nombre_completo'  => trim($paciente->usuario->nom_usuario . ' ' . $paciente->usuario->ape_usuario),
            'nom_usuario'      => $paciente->usuario->nom_usuario,
            'ape_usuario'      => $paciente->usuario->ape_usuario,
            'doc_usuario'      => $paciente->usuario->doc_usuario,
            'tel_paciente'     => $paciente->tel_paciente,
            'fch_nac_paciente' => $paciente->fch_nac_paciente?->format('Y-m-d'),
            'edad'             => $paciente->fch_nac_paciente?->age,
            'id_genero'        => $paciente->usuario->id_genero,
            'genero'           => $paciente->usuario?->genero?->nom_genero ?? null,
            'ema_usuario'      => $paciente->usuario->ema_usuario,
        ], 'Dependiente actualizado con éxito.');
    }

    public function desvincularDependiente($id_paciente)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $paciente = Paciente::with(['usuario', 'reservas', 'antecedentes'])->findOrFail($id_paciente);

        // Validar que el usuario autenticado sea quien tiene asignado a este dependiente
        if ($paciente->id_usuario_responsable !== $usuario->id_usuario) {
            return $this->errorResponse('No tienes autorización para desvincular a este paciente.', 403);
        }

        // Evitar que el titular se desvincule a sí mismo
        if ($paciente->id_usuario === $usuario->id_usuario) {
            return $this->errorResponse('No puedes desvincularte a ti mismo de tu cuenta.', 422);
        }

        $edad = $paciente->fch_nac_paciente?->age ?? 0;

        // 1. Si es menor de edad:
        if ($edad < 18) {
            // Si nunca tuvo citas ni antecedentes médicos, se puede eliminar limpiamente (error de registro)
            if ($paciente->reservas()->count() === 0 && $paciente->antecedentes()->count() === 0) {
                DB::transaction(function () use ($paciente) {
                    $usuarioDependiente = $paciente->usuario;
                    $paciente->delete();
                    $usuarioDependiente?->delete();
                });

                return $this->successResponse(null, 'El dependiente sin historial clínico fue eliminado de tu cuenta.');
            }

            // Si ya tiene historial clínico, no puede quedar huérfano sin apoderado
            return $this->errorResponse(
                'El paciente es menor de edad y cuenta con historial clínico registrado. No puede desvincularse sin asignar otro apoderado responsable. Contacte a la clínica.',
                422
            );
        }

        // 2. Si es mayor de edad:
        // Debe contar con un correo real para poder operar de forma autónoma
        if (str_ends_with($paciente->usuario->ema_usuario, '@clinica.local')) {
            return $this->errorResponse(
                'Para independizar a este paciente mayor de edad, primero actualice su perfil asignándole un correo electrónico real para que pueda acceder por su cuenta.',
                422
            );
        }

        // Se independiza: pasa a ser responsable de su propio expediente
        $paciente->update([
            'id_usuario_responsable' => $paciente->id_usuario,
        ]);

        return $this->successResponse(null, 'El paciente ha sido independizado con éxito. Ahora es responsable de su propia cuenta.');
    }
}
