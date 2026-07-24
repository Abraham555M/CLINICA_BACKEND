<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecetaDetalle extends Model
{
    use SoftDeletes;

    protected $table = 'receta_detalle';
    
    protected $primaryKey = 'id_receta_detalle';

    protected $fillable = [
        'dos_med_receta_detalle',
        'fre_med_receta_detalle',
        'dur_med_receta_detalle',
        'id_medicamento',
        'id_receta'
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'id_receta', 'id_receta');
    }
}
