<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    //
    protected $fillable = [
        'id',
        'idSolicitudFk',
        'idUsuarioFk',
        'fechaPago',
        'estatusPago',
        'montoPagar',
        'montoPagado',
        'medioPago',
        'fechaPagado',
        'nroReferencia',
        'gastosCobranza',
        'interesMora',
        'montoNovado',
        'medioNovado',
        'fechaPagadoNovado',
        'concepto',
        'nroReferenciaNovado',
        'capital',
        'intereses',
        'plataforma',
        'aprobacionRapida',
        'iva',
        'gastosCobranzaSinIva',
        'ivaGastosCobranza',
        'saldoInicial',
        'interesMoraPendiente',
        'gastosCobranzaPendiente',
        'gastosCobranzaSinIvaPendiente',
        'ivaGastosCobranzaPendiente',
        'montoRestante',
        'interesMoraPagado',
        'gastosCobranzaPagado',
        'gastosCobranzaSinIvaPagado',
        'ivaGastosCobranzaPagado'

    ];

    protected $appends = [
        'Novacion'
    ];

    public function novacion()
    {
        return $this->hasOne(Repagos::class, 'idPagoFk');
    }

    public function getNovacionAttribute()
    {
        $novacion = $this->novacion()->where('concepto', 'Novacion')->first();

        if(!empty($novacion))
        {
            return $novacion;
        }else{
            return '';
        }

    }
}
