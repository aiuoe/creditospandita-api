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
                        <th>Documento</th>
                        <th>Direccion</th>
                        <th>Ciudad</th>
                        <th>Nro de credito</th>
                        <th>Tipo de credito</th>
                        <th>Plazo</th>
                        <th>Credito aprobado</th>
                        <th>Monto aprobado</th>
                        <th>Total a pagar</th>
                        <th>Pagado</th>
                        <th>Interses de mora</th>
                        <th>Gastos de cobranza</th>
                        <th>Pendiente por pagar</th>
                        <th>Total pagado novacion</th>
                        <th>Cuota</th>
                        <th>Fecha de desembolso</th>
                        <th>Fecha de novacion</th>
                        <th>Fecha de pago</th>
                        <!-- <th>Fecha de pago de la próxima cuota</th> -->
                        <!-- <th>Saldo al vencimiento</th>
                        <th>Saldo al dia</th> -->
                        <!-- <th>Días/Meses al vencimiento</th> -->
                        <th>Dias de mora</th>
                        <th>Estatus del credito</th>

                    
                    </tr>
                    @foreach ($creditos as $i => $credito)
                    <tr>
                        <td>{{ $credito->first_name }} {{ $credito->second_name }}</td>
                        <td>{{ $credito->last_name }} {{ $credito->second_last_name }}</td>
                        <td>{{ ($credito->phone_number) }}</td>
                        <td>{{ ($credito->email) }}</td>
                        <td>{{ $credito->n_document }}</td>
                        <td>{{ $credito->direccion }}</td>
                        <td>{{ $credito->ciudad }}</td>
                        <td>{{ $credito->numero_credito }}</td>
                        <td>{{ $credito->tipoCredito == 'm' ? 'Panda Meses' : 'Panda Dias' }}</td>
                        <td>{{ $credito->plazo }} {{ $credito->tipoCredito == 'm' ? 'Meses' : 'Dias' }}</td>
                        @if($credito->ofertaEnviada == 2)
                        <td>Solicitud Original</td>
                        @elseif($credito->ofertaEnviada == 1)
                        <td>Oferta Maxima</td>
                        @elseif($credito->ofertaEnviada == 0)
                        <td>Oferta Minima</td>
                        @endif
                        <td>${{ number_format($credito->montoInvertido) }}</td>
                        <td>${{ number_format($credito->totalPagarInicial) }}</td>
                        <td>${{ $credito->estatus_credito =='castigado' ? number_format($credito->pagos_parciales->totalNoPago) : number_format($credito->pagado) }}</td>
                        <td>${{ $credito->estatus_credito =='castigado' ? number_format($credito->pagos_parciales->interesesMora) : number_format($credito->interes_mora) }}</td>
                        <td>${{ $credito->estatus_credito =='castigado' ? number_format($credito->pagos_parciales->gastosCobranza) : number_format($credito->gastos_cobranza) }}</td>
                        <td>${{ number_format($credito->totalPagar) }}</td>
                        <td>${{ number_format($credito->pagado_novacion) }}</td>
                        <td>${{ $credito->tipoCredito == 'm' ? number_format($credito->cuota) : '' }}</td>
                        <td>{{ $credito->fechaDesembolso }}</td>
                        <td>{{ $credito->fechaNovado }}</td>
                        <td>{{ $credito->fecha_pago }}</td>
                        <!-- <td>{{ $credito->tipoCredito == 'm' ? $credito->pago_proximo->fechaPago : '' }}</td> -->
                        <!-- <td>${{ number_format($credito->totalPagar) }}</td>
                        <td>${{ number_format($credito->totalPagar) }}</td> -->
                        <!-- <td>{{ $credito->diasMesesVencimiento}}</td>-->
                        <td>{{ $credito->estatus_credito =='castigado' ? $credito->pagos_parciales->diasMora : $credito->diasMora}}</td> 
                        <td>{{ $credito->estatus_credito}}</td>
                    </tr>
                    @endforeach
                </table>
    <br>
    {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
