<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Calculadora extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'montoSolicitado', 'plazo', 'tasaInteres', 'subtotal', 'plataforma',
        'aprobacionRapida','iva','totalPagar','tipoCredito','idUserFk',
        'numero_credito','puntaje_total','ofertaEnviada','fechaDocEnviado','fechaDocFirma',
        'fechaDesembolso','estatusIntereses','fechaNovado','factura','fechaUltimoPago','estatusAnterior','fechaPendienteNovacion','diasMora',
        'codCampaign'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'update_at',
    ];

    protected $appends = [
        'Evaluacion',
        'ContraOferta',
        'ProximoPago',
        'UltimoPago',
        'MontoPagado',
        'PagoParcial',
        'Pagoreferidor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUserFk');
    }


    public function evaluacion()
    {
        return $this->hasOne(Evaluacion::class, 'idSolicitudFk');
    }

    public function pagoReferidor()
    {
        return $this->hasOne(Pagoreferidor::class, 'idSolicitud');
    }

    public function getEvaluacionAttribute()
    {
        $usuario = $this->evaluacion()->first();

        if(!empty($usuario))
        {
            return $usuario;
        }else{
            return '';
        }

    }



    public function contra_oferta()
    {
        return $this->hasOne(ContraOferta::class, 'idCalculadoraFk');
    }

    public function getContraOfertaAttribute()
    {
        $contra_oferta = $this->contra_oferta()->orderBy("id","desc")->first();

        if(!empty($contra_oferta))
        {
            return $contra_oferta;
        }else{
            return '';
        }

    }

    public function pago_proximo()
    {
        return $this->hasOne(Pagos::class, 'idSolicitudFk');
    }

    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'idSolicitudFk');
    }

    public function hitorial_contactos()
    {
        return $this->hasMany(ContactHistory::class, 'idSolicitudFk');
    }

    public function historial_ultimo_contacto()
    {
        return $this->hasMany(ContactHistory::class, 'idSolicitudFk');
    }

    public function scopeGetHistorialUltimoContacto($query)
    {
        return $query->with(['historial_ultimo_contacto' => function($query2){
            return $query2->orderBy('fechaPtp', 'DESC')->first();
        }]);
    }

    public function getProximoPagoAttribute()
    {
        $proximo_pago = $this->pago_proximo()->where('estatusPago', 'pendiente')->first();

        if(!empty($proximo_pago))
        {
            return $proximo_pago;
        }else{
            return '';
        }

    }

    public function getpagoReferidorAttribute()
    {
        $pagoReferidor = $this->pagoReferidor()->first();

        if(!empty($pagoReferidor))
        {
            return $pagoReferidor;
        }else{
            return '';
        }

    }

    public function monto_pagado()
    {
        return $this->hasOne(Pagos::class, 'idSolicitudFk');
    }

    public function getMontoPagadoAttribute()
    {
        $monto_pagado = $this->monto_pagado()->get()->sum("montoPagado");

        if(!empty($monto_pagado))
        {
            return $monto_pagado;
        }else{
            return 0;
        }

    }

    public function pago_ultimo()
    {
        return $this->hasOne(Repagos::class, 'idSolicitudFk');
    }

    public function getUltimoPagoAttribute()
    {
        $ultimo_pago = $this->pago_ultimo()->orderBy('id', 'desc')->first();

        if(!empty($ultimo_pago))
        {
            return $ultimo_pago;
        }else{
            return '';
        }

    }

    public function pago_parcial()
    {
        return $this->hasOne(PagosParciales::class, 'idSolicitudFk');
    }

    public function getPagoParcialAttribute()
    {
        $parcial_pago = $this->pago_parcial()->first();

        if(!empty($parcial_pago))
        {
            return $parcial_pago;
        }else{
            return '';
        }

    }

}
