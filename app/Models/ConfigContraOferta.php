<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigContraOferta extends Model
{
    protected $fillable = [
        'monto_maximo','monto_minimo','tipo_credito'
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
