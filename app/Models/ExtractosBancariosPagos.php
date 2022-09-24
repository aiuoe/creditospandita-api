<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtractosBancariosPagos extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','fecha','concepto','valorIngreso','totalMensual',
        'promedio'
    ];
}
