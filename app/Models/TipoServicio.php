<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table = 'tipo_servicio';
    
    protected $primaryKey = 'id_tipo_servicio';

    protected $fillable = [
        'nom_tipo_servicio',
        'est_tipo_servicio'
    ];

    protected $casts = [
        "est_tipo_servicio" => "boolean"
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_tipo_servicio', 'id_tipo_servicio');
    }
}
