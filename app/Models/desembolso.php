<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class desembolso extends Model
{

    protected $fillable = [
        'nombres', 'ncedula', 'email','nombreBanco','tipoCuenta','ncuenta',
        'monto','metodo','idUserFk','registrador','comentario','idRegistradorFk','idSolicitudFk'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
