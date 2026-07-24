<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecialidadDoctor extends Model
{
    protected $table = 'especialidad_doctor';
    
    protected $primaryKey = 'id_especialidad_doctor';

    protected $fillable = [
        'id_doctor',
        'id_especialidad'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }
}
