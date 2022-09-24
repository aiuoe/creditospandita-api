<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adicional extends Model
{
    protected $fillable = [
        'nombre', 'idUserFk','tipo','nombreDocumento'
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
