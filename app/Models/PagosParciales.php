<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagosParciales extends Model
{
    //
    protected $fillable = [
        'id',
        'idSolicitudFk',
        'idUsuarioFk',
        'capital',
        'intereses',
        'interesesMora',
        'plataforma',
        'aprobacionRapida',
        'gastosCobranza',
        'iva',
        'totalNoPago',
        'fecha',
        'nroReferencia',
        'concepto',
        'medioPago'
    ];
}
