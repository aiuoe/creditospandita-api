<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $fillable = [
        'selfie', 
        'comentario_selfie', 
        'identidad', 
        'comentario_identidad', 
        'adicionales',
        'comentario_adicionales',
        'llamada',
        'comentario_llamada',

        'data_credito',

        'usuarioResSelfie',
        'fechaComentSelfi',
        'usuarioResIdentidad',
        'fechaComentIdentidad',
        'usuarioResAdicional',
        'fechaComentAdicional',
        'usuarioResLlamada',
        'fechaComentLlamada',

        'idSolicitudFk',
        'idUserFk',
        'estatus',
        'balance',
        'notificadoRechazado',
        'informacion_identidad',
        'gastoMonetario',
        'calculoIngreso',
        'extracto_bancario',
        'telefono',
        'filtro',
        'resultadoFiltro',
        'email',
        'resultadoEmail',
        'scrapping',
        'resultadoScrapping',
        'tokenAnalizer',
        'verifiquese',
        'analizer',
        'resultadoAnalizer',
        'resultadoTelefono'

    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 
    ];

    protected $appends = [
        'Evaluacion'
    ];


    public function calculadora()
    {
        return $this->hasOne(Calculadora::class, 'id');
    }

    public function getEvaluacionAttribute()
    {
        $quotation = $this->calculadora()->orderBy('created_at','desc')->first();

        if(!empty($quotation))
        {
            return $quotation;
        }else{
            return '';
        }

    }


}
