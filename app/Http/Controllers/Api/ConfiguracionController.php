<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use App\Models\PresentacionMedicamento;
use App\Models\Rol;
use App\Models\TipoAntecedente;
use App\Models\TipoServicio;
use App\Models\UnidadMedida;

class ConfiguracionController extends Controller
{
    public function selectRol(){
        $rol = Rol::select("id_rol", "nom_rol")
                    ->get();
        return $this->successResponse($rol, "Lista de roles en el sistema"); 
    }
    
    public function selectGenero(){
        $genero = Genero::select("id_genero", "nom_genero")
                    ->get();
        return $this->successResponse($genero, "Lista de generos en el sistema"); 
    }

    public function selectTipoAntecedente(){
        $tipoAntecedente = TipoAntecedente::select("id_tipo_antecedente", "nom_tipo_antecedente")
                    ->get();
        return $this->successResponse($tipoAntecedente, "Lista de tipos de antecedentes en el sistema"); 
    }

    public function selectPresentacionMedicamento(){
        $presentacionMedicamento = PresentacionMedicamento::select("id_presentacion", "nom_presentacion")
                    ->where("est_presentacion", 1)
                    ->get();
        return $this->successResponse($presentacionMedicamento, "Lista de presentaciones de medicamento en el sistema"); 
    }

    public function selectTipoServicio(){
        $tipoServicio = TipoServicio::select("id_tipo_servicio", "nom_tipo_servicio")
                    ->where("est_tipo_servicio", 1)
                    ->get();
        return $this->successResponse($tipoServicio, "Lista de tipos de servicio en el sistema"); 
    }

    public function selectUnidadMedida(){
        $unidadMedida = UnidadMedida::select("id_unidad_medida", "nom_unidad_medida")
                    ->where("est_unidad_medida", 1)
                    ->get();
        return $this->successResponse($unidadMedida, "Lista de unidades de medida en el sistema");
    }
}
