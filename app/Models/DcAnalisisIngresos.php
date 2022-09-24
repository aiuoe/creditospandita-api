<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcAnalisisIngresos extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','situacionLaboral','ingresoVsCuota','ingresoEstimado'
    ];
}
