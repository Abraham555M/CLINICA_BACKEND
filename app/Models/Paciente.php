<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'paciente';
    
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'tel_paciente',
        'fch_nac_paciente',
        'sld_fav_paciente',
        'id_usuario',
        'id_usuario_responsable'
    ];

    protected $casts = [
        'fch_nac_paciente' => 'date',
        'sld_fav_paciente' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function usuarioResponsable()
    {
        return $this->belongsTo(User::class, 'id_usuario_responsable', 'id_usuario');
    }

    public function antecedentes()
    {
        return $this->hasMany(AntecedenteMedico::class, 'id_paciente', 'id_paciente');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_paciente', 'id_paciente');
    }
}
