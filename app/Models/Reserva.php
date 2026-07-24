<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use SoftDeletes;

    protected $table = 'reserva';
    
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'fch_reserva',
        'hor_reserva',
        'tok_cancelacion',
        'fch_con_reserva',
        'fch_can_reserva',
        'id_paciente',
        'id_doctor',
        'id_servicio'
    ];

    protected $casts = [
        'fch_reserva' => 'date',
        'fch_con_reserva' => 'date',
        'fch_can_reserva' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_reserva', 'id_reserva');
    }
}
