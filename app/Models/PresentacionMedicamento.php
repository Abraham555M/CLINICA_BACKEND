<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentacionMedicamento extends Model
{
    protected $table = 'presentacion_medicamento';
    
    protected $primaryKey = 'id_presentacion';

    protected $fillable = [
        'nom_presentacion',
        'est_presentacion'
    ];

    protected $casts = [
        'est_presentacion' => 'boolean',
    ];

    public function medicamentos()
    {
        return $this->hasMany(Medicamento::class, 'id_presentacion', 'id_presentacion');
    }
}
