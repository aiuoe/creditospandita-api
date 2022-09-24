<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    protected $fillable = [
        'titulo', 'descripcion'
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
