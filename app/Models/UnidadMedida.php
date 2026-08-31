<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadMedida extends Model
{
    use SoftDeletes; 
    
    protected $table = 'unidad_medida';

    protected $primaryKey = 'id_unidad_medida';
    
    protected $fillable = [
        'nom_unidad_medida',
        'abr_unidad_medida',
        'est_unidad_medida'
    ];

    protected $casts = [
        "est_unidad_medida" => "boolean"
    ];

    public function medicamentos()
    {
        return $this->hasMany(Medicamento::class, 'id_unidad_medida', 'id_unidad_medida');
    }
}
