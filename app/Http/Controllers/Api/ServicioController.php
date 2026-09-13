<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Servicio\ActualizarServiceRequest;
use App\Http\Requests\Servicio\RegistrarServiceRequest;
use App\Http\Resources\ServicioResource;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function selectServicios()
    {
        $servicios = Servicio::select('id_servicio', 'nom_servicio')
            ->where('est_servicio', 1)
            ->orderBy('nom_servicio', 'asc')
            ->get();

        return $this->successResponse($servicios, 'Select de servicios obtenido con éxito.');
    }

    public function listarServicios()
    {
        $servicios = Servicio::with('tipoServicio')
            ->orderBy('id_servicio', 'desc')
            ->get();

        return $this->successResponse(
            ServicioResource::collection($servicios),
            'Lista de servicios obtenida con éxito.'
        );
    }

    public function obtenerServicio($id_servicio)
    {
        $servicio = Servicio::with('tipoServicio')->findOrFail($id_servicio);

        return $this->successResponse(
            new ServicioResource($servicio),
            'Detalle del servicio obtenido con éxito.'
        );
    }

    public function registrarServicio(RegistrarServiceRequest $request)
    {
        $servicio = Servicio::create($request->validated());
        $servicio->load('tipoServicio');

        return $this->successResponse(
            new ServicioResource($servicio),
            'Servicio registrado con éxito.',
            201
        );
    }

    public function actualizarServicio(ActualizarServiceRequest $request, $id_servicio)
    {
        $servicio = Servicio::findOrFail($id_servicio);
        $servicio->update($request->validated());
        $servicio->load('tipoServicio');

        return $this->successResponse(
            new ServicioResource($servicio),
            'Servicio actualizado con éxito.'
        );
    }

    public function eliminarServicio($id_servicio)
    {
        $servicio = Servicio::findOrFail($id_servicio);

        if ($servicio->asignadoADoctor()) {
            return $this->errorResponse(
                'No se puede eliminar el servicio porque se encuentra asignado a uno o más doctores.', 409
            );
        }

        if ($servicio->tieneReservas()) {
            return $this->errorResponse(
                'No se puede eliminar el servicio porque tiene citas registradas.', 409
            );
        }

        $servicio->delete();
        return $this->successResponse(null, 'Servicio eliminado con éxito.');
    }
}
