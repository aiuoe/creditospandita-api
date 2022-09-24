<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class testimonio extends Model
{

    protected $fillable = [
        'nombres', 'contenido', 'descripcionCorta','imagen', 
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
