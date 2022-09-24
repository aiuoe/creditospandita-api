<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcAlertas extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','fuente','fecha','novedad', 'descripcion'
    ];
}
