<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;




class Basica extends Model
{
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  
    protected $fillable = [
        'direccion', 'ciudad', 'tipoVivienda', 'tienmpoVivienda', 'conquienVives',
        'estrato','genero','fechaNacimiento','estadoCivil','personasaCargo',
        'nCedula','fechaExpedicionCedula','anversoCedula','reversoCedula','selfi',
        'nHijos','tipoPlanMovil','nivelEstudio','estadoEstudio','vehiculo','placa',
        'centralRiesgo','idUserFk','nroPersonasDependenEconomicamente',
        'cotizasSeguridadSocial',
        'tipoAfiliacion',
        'eps',
        'entidadReportado',
        'cualEntidadReportado',
        'valorMora',
        'tiempoReportado',
        'comoEnterasteNosotros',
        'estadoReportado',
        'motivoReportado',
        'certificacion'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 
    ];
}
