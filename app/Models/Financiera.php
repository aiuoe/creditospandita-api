<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financiera extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  
 

    protected $fillable = [
        'banco', 'tipoCuenta', 'nCuenta', 'ingresoTotalMensual', 'egresoTotalMensual',
        'ingresoTotalMensualHogar','egresoTotalMensualHogar','comoTePagan','situacionLaboral','actividad',
        'antiguedadLaboral','nombreEmpresa','telefonoEmpresa','usoCredito','idUserFk','otroIngreso','proviene','total_otro_ingr_mensual',
        'periodoPagoNomina','diasPago','tarjetasCredito','creditosBanco','otrasCuentas','tipoEmpresa','empresaConstituida','nit',
        'rut','nombreCargo','ciudadTrabajas','direccionEmpresa','sectorEconomico','tamanoEmpresa','fondoPension','bancoPension',
        'fuenteIngreso','cual','pagandoActual','deudaActual','tiempoDatacredito'


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
