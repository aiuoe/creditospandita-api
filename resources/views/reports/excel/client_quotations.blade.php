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
                        <th>Nº crédito</th>
                        <th>Estatus crédito</th>
                        <th>Estatus contraoferta</th>
                        <th>Fecha de registro</th>
                        <th>Tipo de credito</th>
                        <th>Monto solicitado</th>
                        <th>Total a pagar</th>
                        <th>Plazo</th>
                        <th>Puntos panda</th>



                    </tr>
                    @foreach ($usuarios as $i => $usuario)
                    <tr>
                        <td>{{ $usuario->first_name }} {{ $usuario->second_name }}</td>
                        <td>{{ $usuario->last_name }} {{ $usuario->second_last_name }}</td>
                        <td>{{ ($usuario->phone_number) }}</td>
                        <td>{{ ($usuario->email) }}</td>
                        <td>{{ $usuario->n_document }}</td>
                        <td>{{ $usuario->direccion }}</td>
                        <td>{{ $usuario->ciudad }}</td>
                        <td>{{ $usuario->tipoVivienda }}</td>
                        <td>{{ $usuario->tienmpoVivienda }}</td>
                        <td>{{ $usuario->conquienVives }}</td>
                        <td>{{ $usuario->estrato }}</td>
                        <td>{{ $usuario->genero }}</td>
                        <td>{{ $usuario->fechaNacimiento }}</td>
                        <td>{{ $usuario->estadoCivil }}</td>
                        <td>{{ $usuario->personasaCargo }}</td>
                        <td>{{ $usuario->nroPersonasDependenEconomicamente }}</td>
                        <td>{{ $usuario->fechaExpedicionCedula }}</td>
                        <td>{{ $usuario->nHijos }}</td>
                        <td>{{ $usuario->tipoPlanMovil }}</td>
                        <td>{{ $usuario->nivelEstudio }}</td>
                        <td>{{ $usuario->estadoEstudio }}</td>
                        <td>{{ $usuario->vehiculo }}</td>
                        <td>{{ $usuario->placa }}</td>
                        <td>{{ $usuario->centralRiesgo }}</td>
                        <td>{{ $usuario->cotizasSeguridadSocial }}</td>
                        <td>{{ $usuario->tipoAfiliacion }}</td>
                        <td>{{ $usuario->eps }}</td>
                        <td>{{ $usuario->entidadReportado }}</td>
                        <td>{{ $usuario->cualEntidadReportado }}</td>
                        <td>{{ $usuario->valorMora }}</td>
                        <td>{{ $usuario->tiempoReportado }}</td>
                        <td>{{ $usuario->comoEnterasteNosotros }}</td>
                        <td>{{ $usuario->ReferenciaPersonalNombres }}</td>
                        <td>{{ $usuario->ReferenciaPersonalApellidos }}</td>
                        <td>{{ $usuario->ReferenciaPersonalCiudadFk }}</td>
                        <td>{{ $usuario->ReferenciaPersonalTelefono }}</td>
                        <td>{{ $usuario->emailp }}</td>
                        <td>{{ $usuario->relacionp }}</td>
                        <td>{{ $usuario->ReferenciaFamiliarNombres }}</td>
                        <td>{{ $usuario->ReferenciaFamiliarApellidos }}</td>
                        <td>{{ $usuario->ReferenciaFamiliarCiudadFk }}</td>
                        <td>{{ $usuario->ReferenciaFamiliarTelefono }}</td>
                        <td>{{ $usuario->emailf }}</td>
                        <td>{{ $usuario->relacionf }}</td>
                        <td>{{$usuario->banco}}</td>
                        <td>{{$usuario->tipoCuenta}}</td>
                        <td>{{$usuario->nCuenta}}</td>
                        <td>{{$usuario->ingresoTotalMensual}}</td>
                        <td>{{$usuario->egresoTotalMensual}}</td>
                        <td>{{$usuario->otroIngreso}}</td>
                        <td>{{$usuario->proviene}}</td>
                        <td>{{$usuario->total_otro_ingr_mensual}}</td>
                        <td>{{$usuario->comoTePagan}}</td>
                        <td>{{$usuario->periodoPagoNomina}}</td>
                        <td>{{$usuario->diasPago}}</td>
                        <td>{{$usuario->tarjetasCredito}}</td>
                        <td>{{$usuario->creditosBanco}}</td>
                        <td>{{$usuario->otrasCuentas}}</td>
                        <td>{{$usuario->situacionLaboral}}</td>
                        <td>{{$usuario->actividad}}</td>
                        <td>{{$usuario->antiguedadLaboral}}</td>
                        <td>{{$usuario->nombreEmpresa}}</td>
                        <td>{{$usuario->telefonoEmpresa}}</td>
                        <td>{{$usuario->tipoEmpresa}}</td>
                        <td>{{$usuario->empresaConstituida}}</td>
                        <td>{{$usuario->nit}}</td>
                        <td>{{$usuario->rut}}</td>
                        <td>{{$usuario->nombreCargo}}</td>
                        <td>{{$usuario->ciudadTrabajas}}</td>
                        <td>{{$usuario->direccionEmpresa}}</td>
                        <td>{{$usuario->sectorEconomico}}</td>
                        <td>{{$usuario->tamanoEmpresa}}</td>
                        <td>{{$usuario->fondoPension}}</td>
                        <td>{{$usuario->bancoPension}}</td>
                        <td>{{$usuario->fuenteIngreso}}</td>
                        <td>{{$usuario->cual}}</td>
                        <td>{{$usuario->usoCredito}}</td>
                        <td>{{$usuario->numero_credito}}</td>
                        <td>{{$usuario->estatus_credito}}</td>
                        <td>{{$usuario->estatus_contraOferta}}</td>
                        <td>{{$usuario->created_at}}</td>
                        <td>{{$usuario->tipoCredito}}</td>
                        <td>$ {{ number_format($usuario->montoSolicitado)}}</td>
                        <td>$ {{ number_format($usuario->totalPagar) }}</td>
                        <td>{{$usuario->plazo}}</td>
                        <td>{{$usuario->puntaje_total}}</td>
                    </tr>
                    @endforeach
                </table>
    <br>
    {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
