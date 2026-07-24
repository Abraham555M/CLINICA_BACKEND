<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'dat_notificacion',
        'est_lei_notificacion',
        'fch_lei_notificacion',
        'id_usuario',
        'id_tipo_notificacion'
    ];

    protected $casts = [
        'fch_lei_notificacion' => 'dateTime',
        'est_lei_notificacion' => 'boolean'
    ];

    public function tipoNotificacion()
    {
        return $this->belongsTo(TipoNotificacion::class, 'id_tipo_notificacion', 'id_tipo_notificacion');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
