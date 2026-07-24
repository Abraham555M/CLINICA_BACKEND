<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioBloqueado extends Model
{
    protected $table = 'horario_bloqueado';

    protected $primaryKey = 'id_horario_bloqueado';

    protected $fillable = [
        'fch_blq_horario_bloqueado',
        'hor_ini_horario_bloqueado',
        'hor_fin_horario_bloqueado',
        'mot_horario_bloqueado',
        'id_doctor'
    ];

    protected $casts = [
        'fch_blq_horario_bloqueado' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
}
