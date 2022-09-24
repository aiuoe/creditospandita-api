<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcIndicadorPago extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','bajo','medio','alto','puntajeBajo','puntajeMedio','puntajeAlto'
    ];
}
