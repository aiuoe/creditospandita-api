<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Criteria\RequestCriteria;


class Pagoreferidor extends Model
{
    protected $fillable = [
        'idReferidor', 'idReferido', 'idSolicitud','comision','estatus','referencia','idRegistradorFk','registrador'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
