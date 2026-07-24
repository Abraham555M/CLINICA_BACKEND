<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consulta';
    
    protected $primaryKey = 'id_consulta';

    protected $fillable = [
        'fch_consulta',
        'mot_consulta',
        'dig_consulta', 
        'tra_consulta',
        'obs_consulta',
        'est_consulta',
        'id_reserva'
    ];

    protected $casts = [
        'fch_consulta' => 'date',
        'est_consulta' => 'boolean'
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoClinico::class, 'id_consulta', 'id_consulta');
    }

    public function receta()
    {
        return $this->hasOne(Receta::class, 'id_consulta', 'id_consulta');
    }
}
