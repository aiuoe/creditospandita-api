<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<body>
            <table class="table">
                    <tr>
                        <th>Estatus de evaluacion</th>
                        <th>Fecha de solicitud</th>
                        <th>Nº crédito</th>
                        <th>Estatus crédito</th>
                        <th>Tipo de credito</th> 
                        <th>Monto solicitado</th>
                        <th>Plazo</th> 
                        <th>Puntos panda</th>
                        <th>Total ingresos</th> 
                        <th>Total egresos aproximados</th>
                        <th>Total deudas financieras</th>
                        <th>Disponible endeudamiento</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Telefono</th>
                        <th>Correo</th>
                        <th>C.C</th>
                        <th>Direccion</th>
                        <th>Ciudad</th>
                        <th>Tipo de vivienda</th>
                        <th>Tiempo en vivienda</th>
                        <th>Con quien vives</th>
                        <th>Estrato</th>
                        <th>Genero</th>
                        <th>Fecha de nacimiento</th>
                        <th>Estado civil</th>
                        <th>Personas a cargo</th>
                        <th>Cuantas personas dependen de ti economicamente</th>
                        <th>Fecha de expedicion de cedula</th>
                        <th>Numero de hijos</th>
                        <th>Tipo de plan movil</th>
                        <th>Nivel de estudio</th>
                        <th>Estado de estudio</th>
                        <th>Vehiculo</th>
                        <th>Placa</th>
                        <th>Central de riesgo</th>
                        <th>¿Cotizas a Seguridad Social?</th>
                        <th>Tipo de afiliacion</th>
                        <th>EPS</th>
                        <th>Entidad que te ha reportado</th>
                        <th>Cual entidad que te ha reportado</th>
                        <th>Valor en mora</th>
                        <th>Tiempo reportado</th>
                        <th>Como te enteraste de nosotros</th>
                        <th>Referencia personal nombres</th>
                        <th>Referencia personal apellidos</th>
                        <th>Referencia personal ciudad</th>
                        <th>Referencia personal telefono</th>
                        <th>Referencia personal correo</th>
                        <th>Referencia personal relacion</th>
                        <th>Referencia familiar nombres</th>
                        <th>Referencia familiar apellidos</th>
                        <th>Referencia familiar ciudad</th>
                        <th>Referencia familiar telefono</th>
                        <th>Referencia familiar correo</th>
                        <th>Referencia familiar relacion</th>
                        <th>Banco</th>
                        <th>Tipo cuenta</th>
                        <th>Numero de cuenta</th>
                        <th>Ingreso total mensual</th>
                        <th>Egreso total mensual</th> 
                        <th>Otro ingreso</th> 
                        <th>Proviene</th> 
                        <th>Total de otro ingreso mensual</th>      
                        <th>Como te pagan</th>
                        <th>Periodo pago nomina</th> 
                        <th>Dias pago</th>
                        <th>Tarjetas credito</th>
                        <th>Creditos banco</th>
                        <th>Otras cuentas</th>
                        <th>Situacion laboral</th>
                        <th>Actividad</th>
                        <th>Antiguedad laboral</th>   
                        <th>Nombre empresa</th>    
                        <th>Telefono empresa</th> 
                        <th>Tipo empresa</th>
                        <th>Empresa constituida</th>
                        <th>NIT</th>
                        <th>RUT</th>
                        <th>Nombre cargo</th> 
                        <th>Ciudad trabajas</th>
                        <th>Direccion empresa</th>
                        <th>Sector economico</th>
                        <th>Tamano empresa</th>
                        <th>Fondo pension</th>
                        <th>Banco pension</th>
                        <th>Fuente ingreso</th>
                        <th>Cual</th> 
                        <th>Uso credito</th>
                        <th>Ingreso data credito</th>
                        <th>Indicador de Pago Bajo</th> 
                        <th>Puntaje</th>
                        <th>Indicador de Pago Medio</th> 
                        <th>Puntaje</th>
                        <th>Indicador de Pago Alto</th> 
                        <th>Puntaje</th>
                        <th>Cupo Inicial CSM por sector</th>      
                        <th>Saldo Actual CSM por sector</th>
                        <th>Cuota Mensual CSM por sector</th>
                        <th>Gastos Familiares CSM por sector</th>
                        <th>Saldo Mora CSM por sector</th>
                        <th>Disponible Mensual CSM por sector</th>
                        <th>Disponible Endeudamiento CSM por sector</th>
                        <th>Ingreso Mensual CSM por sector</th>
                    
                    </tr>
                    @foreach ($evaluaciones as $i => $evaluacion)
                    <tr>
                        <td>{{ ($evaluacion->estatus_evaluacion_final) }}</td>
                        <td>{{ ($evaluacion->fecha_solicitud) }}</td>
                        <td>{{$evaluacion->numero_credito}}</td>
                        <td>{{$evaluacion->estatus_credito}}</td>
                        <td>{{$evaluacion->tipoCredito}}</td>
                        <td>$ {{ number_format($evaluacion->montoSolicitado)}}</td>
                        <td>{{$evaluacion->plazo}}</td>
                        <td>{{$evaluacion->puntaje_total}}</td>
                        <td>$ {{number_format($evaluacion->ingresoTotalMensual)}}</td>
                        <td>$ {{ $evaluacion->gasto_monetario ? number_format($evaluacion->gasto_monetario->totalEgresosPAE):0}}</td>
                        <td>$ {{ $evaluacion->pagandoActual && $evaluacion->pagandoActual != '' ? $evaluacion->pagandoActual:0}}</td>
                        <td>$ {{ $evaluacion->gasto_monetario ? number_format($evaluacion->gasto_monetario->diponibleEndeudamiento):0}}</td>
                        <td>{{ $evaluacion->first_name }} {{ $evaluacion->second_name }}</td>
                        <td>{{ $evaluacion->last_name }} {{ $evaluacion->second_last_name }}</td>
                        <td>{{ ($evaluacion->phone_number) }}</td>
                        <td>{{ ($evaluacion->email) }}</td>
                        <td>{{ $evaluacion->n_document }}</td>
                        <td>{{ $evaluacion->direccion }}</td>
                        <td>{{ $evaluacion->ciudad }}</td>
                        <td>{{ $evaluacion->tipoVivienda }}</td>
                        <td>{{ $evaluacion->tienmpoVivienda }}</td>
                        <td>{{ $evaluacion->conquienVives }}</td>
                        <td>{{ $evaluacion->estrato }}</td>
                        <td>{{ $evaluacion->genero }}</td>
                        <td>{{ $evaluacion->fechaNacimiento }}</td>
                        <td>{{ $evaluacion->estadoCivil }}</td>
                        <td>{{ $evaluacion->personasaCargo }}</td>
                        <td>{{ $evaluacion->nroPersonasDependenEconomicamente }}</td>
                        <td>{{ $evaluacion->fechaExpedicionCedula }}</td>
                        <td>{{ $evaluacion->nHijos }}</td>
                        <td>{{ $evaluacion->tipoPlanMovil }}</td>
                        <td>{{ $evaluacion->nivelEstudio }}</td>
                        <td>{{ $evaluacion->estadoEstudio }}</td>
                        <td>{{ $evaluacion->vehiculo }}</td>
                        <td>{{ $evaluacion->placa }}</td>
                        <td>{{ $evaluacion->centralRiesgo }}</td>
                        <td>{{ $evaluacion->cotizasSeguridadSocial }}</td>
                        <td>{{ $evaluacion->tipoAfiliacion }}</td>
                        <td>{{ $evaluacion->eps }}</td>
                        <td>{{ $evaluacion->entidadReportado }}</td>
                        <td>{{ $evaluacion->cualEntidadReportado }}</td>
                        <td>{{ $evaluacion->valorMora }}</td>
                        <td>{{ $evaluacion->tiempoReportado }}</td>
                        <td>{{ $evaluacion->comoEnterasteNosotros }}</td>
                        <td>{{ $evaluacion->ReferenciaPersonalNombres }}</td>
                        <td>{{ $evaluacion->ReferenciaPersonalApellidos }}</td>
                        <td>{{ $evaluacion->ReferenciaPersonalCiudadFk }}</td>
                        <td>{{ $evaluacion->ReferenciaPersonalTelefono }}</td>
                        <td>{{ $evaluacion->emailp }}</td>
                        <td>{{ $evaluacion->relacionp }}</td>
                        <td>{{ $evaluacion->ReferenciaFamiliarNombres }}</td>
                        <td>{{ $evaluacion->ReferenciaFamiliarApellidos }}</td>
                        <td>{{ $evaluacion->ReferenciaFamiliarCiudadFk }}</td>
                        <td>{{ $evaluacion->ReferenciaFamiliarTelefono }}</td>
                        <td>{{ $evaluacion->emailf }}</td>
                        <td>{{ $evaluacion->relacionf }}</td>
                        <td>{{$evaluacion->banco}}</td>
                        <td>{{$evaluacion->tipoCuenta}}</td>
                        <td>{{$evaluacion->nCuenta}}</td>
                        <td>${{number_format($evaluacion->ingresoTotalMensual)}}</td>
                        <td>{{$evaluacion->egresoTotalMensual}}</td>    
                        <td>{{$evaluacion->otroIngreso}}</td> 
                        <td>{{$evaluacion->proviene}}</td> 
                        <td>{{$evaluacion->total_otro_ingr_mensual}}</td>   
                        <td>{{$evaluacion->comoTePagan}}</td>
                        <td>{{$evaluacion->periodoPagoNomina}}</td> 
                        <td>{{$evaluacion->diasPago}}</td>
                        <td>{{$evaluacion->tarjetasCredito}}</td>
                        <td>{{$evaluacion->creditosBanco}}</td>
                        <td>{{$evaluacion->otrasCuentas}}</td>
                        <td>{{$evaluacion->situacionLaboral}}</td>
                        <td>{{$evaluacion->actividad}}</td>
                        <td>{{$evaluacion->antiguedadLaboral}}</td>   
                        <td>{{$evaluacion->nombreEmpresa}}</td>    
                        <td>{{$evaluacion->telefonoEmpresa}}</td> 
                        <td>{{$evaluacion->tipoEmpresa}}</td>
                        <td>{{$evaluacion->empresaConstituida}}</td>
                        <td>{{$evaluacion->nit}}</td>
                        <td>{{$evaluacion->rut}}</td>
                        <td>{{$evaluacion->nombreCargo}}</td> 
                        <td>{{$evaluacion->ciudadTrabajas}}</td>
                        <td>{{$evaluacion->direccionEmpresa}}</td>
                        <td>{{$evaluacion->sectorEconomico}}</td>
                        <td>{{$evaluacion->tamanoEmpresa}}</td>
                        <td>{{$evaluacion->fondoPension}}</td>
                        <td>{{$evaluacion->bancoPension}}</td>
                        <td>{{$evaluacion->fuenteIngreso}}</td>
                        <td>{{$evaluacion->cual}}</td>
                        <td>{{$evaluacion->usoCredito}}</td> 
                        <td>$ {{ $evaluacion->ingresoEstimado ? number_format($evaluacion->ingresoEstimado) : 0}}</td>
                        <td>{{$evaluacion->bajo ? 'Si' : 'No'}}</td> 
                        <td>{{$evaluacion->puntajeBajo}}</td>
                        <td>{{$evaluacion->medio ? 'Si' : 'No'}}</td> 
                        <td>{{$evaluacion->puntajeMedio}}</td>
                        <td>{{$evaluacion->alto ? 'Si' : 'No'}}</td> 
                        <td>{{$evaluacion->puntajeAlto}}</td> 
                        <td>{{$evaluacion->cupoInicial ? number_format($evaluacion->cupoInicial) : 0}}</td> 
                        <td>{{$evaluacion->saldoActual ? number_format($evaluacion->saldoActual) : 0}}</td> 
                        <td>{{$evaluacion->cuotaMensual ? number_format($evaluacion->cuotaMensual) : 0}}</td> 
                        <td>{{$evaluacion->gastosFamiliares ? number_format($evaluacion->gastosFamiliares) : 0}}</td> 
                        <td>{{$evaluacion->saldoMora ? number_format($evaluacion->saldoMora) : 0}}</td> 
                        <td>{{$evaluacion->disponibleMensual ? number_format($evaluacion->disponibleMensual) : 0}}</td> 
                        <td>{{$evaluacion->disponibleEndeudamiento ? number_format($evaluacion->disponibleEndeudamiento) : 0}}</td> 
                        <td>{{$evaluacion->ingresoMensual ? number_format($evaluacion->ingresoMensual) : 0}}</td> 
                        
                    </tr>
                    @endforeach
                </table>
    <br>
    {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
