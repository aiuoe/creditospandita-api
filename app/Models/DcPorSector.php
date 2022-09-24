<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcPorSector extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','cupoInicial','saldoActual','cuotaMensual', 'gastosFamiliares',
        'saldoMora', 'disponibleMensual', 'disponibleEndeudamiento','ingresoMensual'
    ];
}
