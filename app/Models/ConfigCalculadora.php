<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigCalculadora extends Model
{
    protected $fillable = [
        'monto_minimo', 
        'monto_maximo', 
        'dias_minimo', 
        'dias_maximo', 
        'monto_restriccion',
        'dias_restriccion',
        'porcentaje_iva',
        'porcentaje_plataforma',
        'porcentaje_express',
        'porcentaje_express_dos',
        'porcentaje_express_tres',
        'monto_restriccion_tooltip',
        'tasa',
        'tipo'
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
