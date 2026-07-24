<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAntecedente extends Model
{
    protected $table = 'tipo_antecedente';
    
    protected $primaryKey = 'id_tipo_antecedente';

    protected $fillable = [
        'nom_tipo_antecedente'
    ];

    public function antecedenteMedicos()
    {
        return $this->hasMany(AntecedenteMedico::class, 'id_tipo_antecedente', 'id_tipo_antecedente');
    }
}
