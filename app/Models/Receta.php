<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receta extends Model
{
    use SoftDeletes;
    
    protected $table = 'receta';
    
    protected $primaryKey = 'id_receta';

    protected $fillable = [
        'fch_receta',
        'ind_receta',
        'est_receta',
        'id_consulta'
    ];

    protected $casts = [
        'fch_receta' => 'date',
        'est_receta' => 'boolean',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }

    public function recetaDetalles()
    {
        return $this->hasMany(RecetaDetalle::class, 'id_receta', 'id_receta');
    }
}
