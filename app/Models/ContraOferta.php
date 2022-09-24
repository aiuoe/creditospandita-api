<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ContraOferta extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

  
    protected $fillable = [
        'montoSolicitado', 'plazo', 'tasaInteres', 'subtotal', 'plataforma',
        'aprobacionRapida','iva','totalPagar','tipoCredito','idUserFk', 'numero_credito','puntaje_total',
        'idCalculadoraFk','estatus','montoAprobado','fechaAprobado'
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
        
    ];


    public function calculadora()
    {
        return $this->hasOne(Calculadora::class, 'id');
    }

    public function getSolicitudAttribute()
    {
        $contra_oferta = $this->calculadora()->orderBy('created_at','desc')->first();

        if(!empty($contra_oferta))
        {
            return $contra_oferta;
        }else{
            return '';
        }

    }
}
