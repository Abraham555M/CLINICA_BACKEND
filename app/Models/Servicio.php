<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use SoftDeletes;

    protected $table = 'servicio';
    
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nom_servicio',
        'des_servicio',
        'dur_min_servicio',
        'prc_servicio',
        'est_servicio',
        'id_tipo_servicio'
    ];

    protected $casts = [
        'est_servicio' => 'boolean',
        'prc_servicio' => 'decimal:2',
    ];

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'id_tipo_servicio', 'id_tipo_servicio');
    }

    public function servicioDoctores()
    {
        return $this->hasMany(ServicioDoctor::class, 'id_servicio', 'id_servicio');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_servicio', 'id_servicio');
    }
}
