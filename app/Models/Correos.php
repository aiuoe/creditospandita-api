<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correos extends Model
{



    protected $fillable = [
        'id', 'asunto','contenido','pertenece','estatus'
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

