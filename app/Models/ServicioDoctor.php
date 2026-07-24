<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioDoctor extends Model
{
    protected $table = 'servicio_doctor';
    
    protected $primaryKey = 'id_servicio_doctor';

    protected $fillable = [
        'id_servicio',
        'id_doctor'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
}
