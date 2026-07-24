<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicamento extends Model
{
    use SoftDeletes;

    protected $table = 'medicamento';
    
    protected $primaryKey = 'id_medicamento';

    protected $fillable = [
        'nom_medicamento',
        'con_medicamento',
        'est_medicamento',
        'id_presentacion'
    ];

    protected $casts = [
        'est_medicamento' => 'boolean'
    ];

    public function presentacion()
    {
        return $this->belongsTo(PresentacionMedicamento::class, 'id_presentacion', 'id_presentacion');
    }

    public function recetaDetalles()
    {
        return $this->hasMany(RecetaDetalle::class, 'id_medicamento', 'id_medicamento');
    }
}
