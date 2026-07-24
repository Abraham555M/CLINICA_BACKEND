<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioAtencion extends Model
{
    protected $table = 'horario_atencion';
    
    protected $primaryKey = 'id_horario_atencion';

    protected $fillable = [
        'dia_sem_horario_atencion',
        'hor_ini_horario_atencion',
        'hor_fin_horario_atencion',
        'est_horario_atencion',
        'id_doctor'
    ];

    protected $casts = [
        'est_horario_atencion' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
}
