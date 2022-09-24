<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    protected $fillable = [
        'comentario', 'archivo', 'evaluacionFk','tab','usuario','idSolicitudFk'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         
    ];
}
