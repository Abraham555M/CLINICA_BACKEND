<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidad';
    
    protected $primaryKey = 'id_especialidad';

    protected $fillable = [
        'nom_especialidad',
        'est_especialidad'
    ];

    protected $casts = [
        'est_especialidad' => 'boolean'
    ];

    public function especialidadDoctores()
    {
        return $this->hasMany(EspecialidadDoctor::class, 'id_especialidad', 'id_especialidad');
    }
}
