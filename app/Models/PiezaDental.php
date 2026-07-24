<?php

namespace App\Models;

use App\Enums\TipoPiezaDental;
use Illuminate\Database\Eloquent\Model;

class PiezaDental extends Model
{
    protected $table = 'pieza_dental';
    
    protected $primaryKey = 'id_pieza_dental';

    protected $fillable = [
        'num_pieza_dental',
        'nom_pieza_dental',
        'tip_det_pieza_dental',
        'est_pieza_dental'
    ];

    protected $casts = [
        'tip_det_pieza_dental' => TipoPiezaDental::class,
        'est_pieza_dental' => 'boolean'
    ];

    public function odontogramaRegistros(){
        return $this->hasMany(OdontrogramaRegistro::class, 'id_pieza_dental', 'id_pieza_dental');
    }
}
