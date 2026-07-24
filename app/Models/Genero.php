<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = 'genero';
    
    protected $primaryKey = 'id_genero';

    protected $fillable = [
        'nom_genero'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_genero', 'id_genero');
    }
}
