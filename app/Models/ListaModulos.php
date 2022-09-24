<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaModulos extends Model
{
    //
    protected $fillable = [
        'nombre', 'administrador', 'analista', 'cliente'
    ];
}
