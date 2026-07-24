<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdontrogramaRegistro extends Model
{
    protected $table = 'odontrograma_registro';
    
    protected $primaryKey = 'id_odontograma_registro';

    protected $fillable = [
        'fch_registro_odontograma',
        'obs_registro_odontograma',
        'id_paciente',
        'id_pieza_dental',
        'id_condicion_dental',
        'id_consulta',
        'id_doctor'
    ];

    public function paciente(){
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function piezaDental(){
        return $this->belongsTo(PiezaDental::class, 'id_pieza_dental', 'id_pieza_dental');
    }

    public function condicionDental(){
        return $this->belongsTo(CondicionDental::class, 'id_condicion_dental', 'id_condicion_dental');
    }

    public function consulta(){
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
}
