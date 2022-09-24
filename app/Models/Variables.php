<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variables extends Model
{
    protected $fillable = [
        'variable', 'ponderacion', 'puntosTotales','cantidadCategorias'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 
    ];
}
