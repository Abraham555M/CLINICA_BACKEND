<?php

namespace App\Models;

use App\Enums\TipoArchivoClinico;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchivoClinico extends Model
{
    use SoftDeletes; 

    protected $table = 'archivo_clinico';
    
    protected $primaryKey = 'id_archivo_clinico';

    protected $fillable = [
        'nom_archivo',
        'url_archivo',
        'tip_archivo',
        'fch_crg_archivo',
        'id_consulta'
    ];

    protected $casts = [
        'tip_archivo' => TipoArchivoClinico::class,
        'fch_crg_archivo' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
