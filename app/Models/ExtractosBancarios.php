<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtractosBancarios extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','saldoAnterior','totalAbonos','totalCargos','saldoActual',
        'saldoPromedio','salario','diasPago','nombreEmpresa','tipoContrato','antiguedadLaboral',
        'nombreCargo','valorTotalMensualCreditosActuales','tipoPagoNomina'
    ];
}
