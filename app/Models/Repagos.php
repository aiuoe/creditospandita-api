<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repagos extends Model
{
    //
    protected $fillable = [
        'id','montoRepagar','metodoRepago',
        'fecha','nroReferencia','concepto','idSolicitudFk',
        'idUsuarioFk','idPagoFk','interesMora','gastosCobranza','totalPagar'
    ];
}
