<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoNotificacion extends Model
{
    protected $table = 'tipo_notificacion';
    
    protected $primaryKey = 'id_tipo_notificacion';

    protected $fillable = [
        'nom_tipo_notificacion',
        'est_tipo_notificacion'
    ];

    protected $casts = [
        "est_tipo_notificacion" => "boolean"
    ];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_tipo_notificacion', 'id_tipo_notificacion');
    }
}
