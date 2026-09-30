<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntecedenteMedico extends Model
{
    use SoftDeletes, Auditable; 

    protected $table = 'antecedente_medico';
    
    protected $primaryKey = 'id_antecedente';

    protected $fillable = [
        'des_antecedente',
        'fch_antecedente',
        'est_antecedente',
        'id_paciente',
        'id_tipo_antecedente',
        'id_consulta',
    ];

    protected $casts = [
        'fch_antecedente' => 'datetime',
        'est_antecedente' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function tipoAntecedente()
    {
        return $this->belongsTo(TipoAntecedente::class, 'id_tipo_antecedente', 'id_tipo_antecedente');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}