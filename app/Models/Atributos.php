<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Criteria\RequestCriteria;

class Atributos extends Model
{
    protected $fillable = [
        'variable', 'categoria', 'ponderacion'
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
