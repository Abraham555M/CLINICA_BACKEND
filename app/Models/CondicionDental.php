<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionDental extends Model
{
    protected $table = 'condicion_dental';
    
    protected $primaryKey = 'id_condicion_dental';

    protected $fillable = [
        'nom_condicion_dental',
        'cod_col_condicion_dental',
        'est_condicion_dental'        
    ];

    protected $casts = [
        'est_condicion_dental' => 'boolean'
    ];
}
