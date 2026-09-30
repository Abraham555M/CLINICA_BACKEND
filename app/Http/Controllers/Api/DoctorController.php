<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\ActualizarDoctorRequest;
use App\Http\Requests\Doctor\RegistrarDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Models\Rol;
use App\Models\User;
use App\Notifications\DoctorBienvenidaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    public function selectDoctores(Request $request)
    {
        $query = Doctor::join('usuario', 'doctor.id_usuario', '=', 'usuario.id_usuario')
            ->where('usuario.est_usuario', User::ESTADO_ACTIVO);

        if ($request->filled('id_servicio')) {
            $query->whereHas('servicios', function ($q) use ($request) {
                $q->where('servicio.id_servicio', $request->input('id_servicio'));
            });
        }

        $doctores = $query->orderBy('usuario.nom_usuario', 'asc')
            ->orderBy('usuario.ape_usuario', 'asc')
            ->select([
                'doctor.id_doctor',
                'usuario.nom_usuario',
                'usuario.ape_usuario',
                'usuario.doc_usuario',
                'doctor.cop_num_doctor',
            ])
            ->get();

        return $this->successResponse($doctores, 'Select de doctores obtenido con éxito.');
    }

    public function listarDoctores(Request $request)
    {
        $query = Doctor::with(['usuario.genero', 'servicios'])
            ->join('usuario', 'doctor.id_usuario', '=', 'usuario.id_usuario')
            ->select('doctor.*');

        // Búsqueda por término (nombre, apellido, documento, correo, COP)
        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('usuario.nom_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('usuario.ape_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('usuario.doc_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('usuario.ema_usuario', 'LIKE', "%{$buscar}%")
                  ->orWhere('doctor.cop_num_doctor', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtro por servicio odontológico
        if ($request->filled('id_servicio')) {
            $query->whereHas('servicios', function ($q) use ($request) {
                $q->where('servicio.id_servicio', $request->input('id_servicio'));
            });
        }

        // Filtro por estado del usuario (0 = pendiente, 1 = activo, 2 = inactivo)
        if ($request->filled('est_usuario')) {
            $query->where('usuario.est_usuario', (int) $request->input('est_usuario'));
        }

        // Filtro por género
        if ($request->filled('id_genero')) {
            $query->where('usuario.id_genero', (int) $request->input('id_genero'));
        }

        // Ordenamiento por defecto (alfabético por nombre y apellido)
        $query->orderBy('usuario.nom_usuario', 'asc')
              ->orderBy('usuario.ape_usuario', 'asc');

        // Retornar lista completa si se solicita explícitamente (?all=true)
        if ($request->boolean('all')) {
            $doctores = $query->get();
            return $this->successResponse(
                DoctorResource::collection($doctores),
                'Lista de doctores obtenida con éxito.'
            );
        }

        // Paginación por defecto (10 por página o según ?per_page)
        $perPage = $request->integer('per_page', 10);
        $doctores = $query->paginate($perPage);

        return $this->successResponse([
            'doctores'   => DoctorResource::collection($doctores),
            'paginacion' => [
                'total'        => $doctores->total(),
                'per_page'     => $doctores->perPage(),
                'current_page' => $doctores->currentPage(),
                'last_page'    => $doctores->lastPage(),
                'from'         => $doctores->firstItem(),
                'to'           => $doctores->lastItem(),
            ]
        ], 'Lista de doctores obtenida con éxito.');
    }
    
    public function obtenerDoctorPorId($id_doctor)
    {
        $doctor = Doctor::with(['usuario.genero', 'servicios'])
            ->findOrFail($id_doctor);

        return $this->successResponse(new DoctorResource($doctor), 'Detalle del doctor obtenido con éxito.');
    }

    public function registrarDoctor(RegistrarDoctorRequest $request)
    {
        $data = $request->validated();
        
        $idRolDoctor = Rol::where('nom_rol', 'Doctor')->value('id_rol');

        if (!$idRolDoctor) {
            return $this->errorResponse('El rol Doctor no se encuentra configurado en el sistema.', 500);
        }

        $plainToken = Str::random(64);

        $doctor = DB::transaction(function () use ($data, $request, $idRolDoctor, $plainToken) {
            $usuario = User::create([
                'id_rol'      => $idRolDoctor,
                'id_genero'   => $data['id_genero'],
                'nom_usuario' => $data['nom_usuario'],
                'ape_usuario' => $data['ape_usuario'],
                'doc_usuario' => $data['doc_usuario'],
                'ema_usuario' => $data['ema_usuario'],
                'pas_usuario' => Hash::make(Str::random(32)), // Contraseña temporal aleatoria
                'est_usuario' => User::ESTADO_PENDIENTE,      // 0 = Inactivo hasta que cree su contraseña
            ]);

            $imgPath = null;
            if ($request->hasFile('img_doctor')) {
                $imgPath = $request->file('img_doctor')->store('doctores', 'public');
            }

            $doctor = Doctor::create([
                'id_usuario'     => $usuario->id_usuario,
                'cop_num_doctor' => $data['cop_num_doctor'],
                'bio_doctor'     => $data['bio_doctor'] ?? null,
                'img_doctor'     => $imgPath,
            ]);

            if (!empty($data['ids_servicio'])) {
                $doctor->servicios()->attach($data['ids_servicio']);
            }

            // Registrar token de activación en password_reset_tokens
            DB::table('password_reset_tokens')->where('ema_usuario', $usuario->ema_usuario)->delete();
            DB::table('password_reset_tokens')->insert([
                'ema_usuario' => $usuario->ema_usuario,
                'token'       => Hash::make($plainToken),
                'created_at'  => now(),
            ]);

            // Enviar correo de invitación con el token
            $usuario->notify(new DoctorBienvenidaNotification($plainToken));

            return $doctor;
        });

        $doctor->load(['usuario.genero', 'servicios']);

        return $this->successResponse(
            new DoctorResource($doctor),
            'Doctor registrado con éxito. Se envió un correo de activación para que establezca su contraseña.',
            201
        );
    }

    public function actualizarDoctor(ActualizarDoctorRequest $request, $id_doctor)
    {
        $doctor = Doctor::with('usuario')->findOrFail($id_doctor);
        $usuario = $doctor->usuario;

        $data = $request->validated();

        DB::transaction(function () use ($doctor, $usuario, $data, $request) {
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

            $usuario->update($userData);

            $doctorData = [
                'cop_num_doctor' => $data['cop_num_doctor'],
                'bio_doctor'     => $data['bio_doctor'] ?? null,
            ];

            if ($request->hasFile('img_doctor')) {
                // Eliminar foto anterior si existe
                if ($doctor->img_doctor && Storage::disk('public')->exists($doctor->img_doctor)) {
                    Storage::disk('public')->delete($doctor->img_doctor);
                }
                $doctorData['img_doctor'] = $request->file('img_doctor')->store('doctores', 'public');
            }

            $doctor->update($doctorData);

            if (isset($data['ids_servicio'])) {
                $doctor->servicios()->sync($data['ids_servicio']);
            }
        });

        $doctor->load(['usuario.genero', 'servicios']);

        return $this->successResponse(
            new DoctorResource($doctor),
            'Doctor actualizado con éxito.'
        );
    }

    public function eliminarDoctor($id_doctor)
    {
        $doctor = Doctor::findOrFail($id_doctor);

        if (!$doctor->puedeEliminarse()) {
            return $this->errorResponse(
                'No se puede eliminar el doctor porque tiene horarios o reservas activas.', 409
            );
        }
        $doctor->delete();

        return $this->successResponse(null, 'Doctor eliminado con éxito.');
    }

    public function obtenerMiPerfil()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return $this->errorResponse('Usuario no autenticado.', 401);
        }

        $doctor = $usuario->doctor;

        if (!$doctor) {
            return $this->errorResponse('El usuario autenticado no cuenta con un perfil de doctor asociado.', 404);
        }

        $doctor->load(['usuario.genero', 'servicios']);

        return $this->successResponse(
            new DoctorResource($doctor),
            'Perfil del doctor obtenido con éxito.'
        );
    }
}