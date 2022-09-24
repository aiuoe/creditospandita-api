<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtractosBancariosCreditos extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','fecha','empresa','ingresoPrestamo','cuotaCredito'
    ];
}
