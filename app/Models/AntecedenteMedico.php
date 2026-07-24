<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntecedenteMedico extends Model
{
    use SoftDeletes; 

    protected $table = 'antecedente_medico';
    
    protected $primaryKey = 'id_antecedente';

    protected $fillable = [
        'des_antecedente',
        'fch_antecedente',
        'est_antecedente',
        'id_paciente',
        'id_tipo_antecedente'
    ];

    protected $casts = [
        'fch_antecedente' => 'datetime',
        'est_antecedente' => 'boolean',
    ];

    // Relación: un antecedente pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    // Relación: un antecedente pertenece a un tipo de antecedente
    public function tipoAntecedente()
    {
        return $this->belongsTo(TipoAntecedente::class, 'id_tipo_antecedente', 'id_tipo_antecedente');
    }
}
