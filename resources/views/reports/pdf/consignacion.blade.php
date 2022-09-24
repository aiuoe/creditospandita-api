<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recibo de consignacion</title>
    <style>

        .table th{
          background-color: #ccc;
        }
        h1 {
          text-align: center;
          font-size: 17px;

        }
        .text-success{
          color: #00c853 !important;
          border-color: #000 !important ;
        }
        .text-good{
          color: #ffeb3b !important;
          border-color: #000 !important ;
        }
        .text-regular{
          color: #ff9800 !important;
          border-color: #000 !important ;
        }
        .text-bad{
          color: #f44336 !important;
          border-color: #000 !important ;
        }

        .success{
          background-color: #00c853;
        }
        .good{
          background-color: #ffeb3b;
        }
        .regular{
          background-color: #ff9800;
        }
        .bad{
          background-color: #f44336;
        }

        .text-center{
            text-align: center !important;
        }

        .text-right{

          text-align: right !important;
        }

        .left{
          text-align: left;
        }

        .table {
          font-family: Arial, Helvetica, sans-serif;
          border-collapse: collapse;
          width: 100%;
          font-size: 14px;
        }

        .table td,
        .table th {
          border: 1px solid;
          padding: 3px;
        }
        thead { display: table-header-group }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid }
        .table th {

        }

        .table tr:hover {
          background-color: #ddd;
        }

        .table th {
          padding-top: 3px;
          padding-bottom: 3px;

          text-align: left;
        }

        .table td {
          text-align: left;
          padding: 3px;
        }

        .page-break {
          page-break-after: always;
        }

        .title{
          font-weight: bold;
          background-color: #ccc;
          text-align: center !important;
          border: 2px solid #000;
          padding: 0.5em;
          border-radius: 3px !important;
          margin-bottom: 1em;
        }
      </style>
</head>
<body>
        <table class="" style="width: 100%;">
                <tr>
                    <td>
                        <img style=" margin: 0px 15px 0px 0px;" src="{{ public_path('images/logo1.png') }}" width="300" />
                    </td>
                </tr>
                <tr class="text-center">
                 
                    <td class="text-center" colspan="2">
                        <h1><b>RECIBO DE CONSIGNACIÓN</b></h1>
                    </td>
                   
                </tr>
                <tr>
                <td>
               
                </td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;">
            <tr>
                <td>Nit: 901.369.753-0</td>
            <td></td>
                <td colspan="4" style="text-align:right">
                    Código crédito: {{$solicitud->numero_credito}}
                </td>
            </tr>
            <tr>
                <td>Cra. 49b No. 93-38</td>
                <td></td>
                <td colspan="4" style="text-align:right">
                    Fecha inicio: {{$solicitud->fechaDesembolso}}
                </td>
            </tr>
            <tr>
                <td>Bogotá</td>
                <td></td>
                <td colspan="4" style="text-align:right">
                    Fecha vencimiento: {{$pagoProximo->fechaPago}}
                </td>
            </tr>
          
            <tr>
                <td>Telf: +57 3212403734</td>
                
                <td></td>
         
                <td colspan="4" style="text-align:right">
                    Documento: {{$solicitud->n_document}}
                </td>
            </tr>
            <tr>
            <td>Email: info@creditospanda.com</td>
                <td></td>
             
                <td colspan="4" style="text-align:right">
                    Nombres: {{$solicitud->first_name}} {{$solicitud->second_name}} {{$solicitud->last_name}} {{$solicitud->second_last_name}}
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
     
                <td colspan="4" style="text-align:right">
                    Celular: {{$solicitud->phone_number}} 
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            
                <td colspan="4" style="text-align:right">
                    Correo: {{$solicitud->email}} 
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            
                <td colspan="4" style="text-align:right">
                    Dirección: {{$solicitud->direccion}} 
                </td>
            </tr>
            </table><br><br>
<table style="width: 100%;">
<tr style="background-color: #ebc121;color:white;font-size:13px">
  <th>PRESTAMO</th>
  <th>INTERES</th>
  <th>MORA</th>
  <th>PLATAFORMA</th>
  <th>APRO. RAPIDA</th>
  <th>COBRANZA</th>
  <th>IVA</th>
  <th>NOVACIÓN</th>

</tr>
@if($solicitud->tipoCredito=='d' && $solicitud->estatus_credito!='pendiente de novacion')
<tr >

    <td>$ {{$contraOferta ? number_format($contraOferta->montoAprobado) : number_format($solicitud->montoSolicitado)   }}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->tasaInteres) : number_format($solicitud->tasaInteres)   }}</td>
    <td>$ {{number_format($pagoProximo->interesMora)}}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->plataforma) : number_format($solicitud->plataforma)   }}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->aprobacionRapida) : number_format($solicitud->aprobacionRapida)   }}</td>
    <td>$ {{number_format($pagoProximo->gastosCobranza)}}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->iva) : number_format($solicitud->iva)   }}</td>
    <td>$ {{number_format($contraOferta->montoNovado)}}</td>
</tr>
@endif
@if($solicitud->tipoCredito=='d' && $solicitud->estatus_credito=='pendiente de novacion')

  @php
    $tasa_n = $contraOferta ? $contraOferta->tasaInteres : $solicitud->tasaInteres;
    $plata_n= $contraOferta ? $contraOferta->plataforma : $solicitud->plataforma;
    $apro_n= $contraOferta ? $contraOferta->aprobacionRapida : $solicitud->aprobacionRapida;
    $iva_n= $contraOferta ? $contraOferta->iva : $solicitud->iva;
    $totalNovacion = $tasa_n+$plata_n+$apro_n+$iva_n;
  @endphp
<tr >

    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ 0</td>
    <td>$ {{number_format($totalNovacion)}}</td>
</tr>

@endif
@if($solicitud->tipoCredito=='m')
<tr >
    <td>$ {{number_format(round($amortizacionPagar[2]))  }}</td>
    <td>$ {{ number_format(round($amortizacionPagar[1]))  }}</td>
    <td>$ {{number_format($pagoProximo->interesMora)}}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->plataforma/$solicitud->plazo) : number_format($solicitud->plataforma/$solicitud->plazo)   }}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->aprobacionRapida/$solicitud->plazo) : number_format($solicitud->aprobacionRapida/$solicitud->plazo)   }}</td>
    <td>$ {{number_format($pagoProximo->gastosCobranza)}}</td>
    <td>$ {{$contraOferta ? number_format($contraOferta->iva/$solicitud->plazo) : number_format($solicitud->iva/$solicitud->plazo)   }}</td>
    <td>$ 0</td>
</tr>
 @php

 $plataform= $contraOferta ? $contraOferta->plataforma/$solicitud->plazo : $solicitud->plataforma/$solicitud->plazo;

 $apro= $contraOferta ? $contraOferta->aprobacionRapida/$solicitud->plazo : $solicitud->aprobacionRapida/$solicitud->plazo;

 $iva= $contraOferta ? $contraOferta->iva/$solicitud->plazo : $solicitud->iva/$solicitud->plazo;
 $abonadoCapital =round($amortizacionPagar[2]);
 $interesess=round($amortizacionPagar[1]);
 @endphp
@endif
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
<td></td>
    </tr>
    <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
<td></td>
    </tr>
    <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
<td></td>
    </tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
@if($solicitud->tipoCredito=='d' && $solicitud->estatus_credito!='pendiente de novacion')
    <td style="font-size:20px"><b>${{number_format($contraOferta->montoAprobado+$contraOferta->tasaInteres+$contraOferta->plataforma+$contraOferta->aprobacionRapida+$contraOferta->iva+$pagoProximo->interesMora+$pagoProximo->gastosCobranza) }}</b></td>
@endif
@if($solicitud->tipoCredito=='d' && $solicitud->estatus_credito=='pendiente de novacion')
    <td style="font-size:20px"><b>${{number_format($totalNovacion) }}</b></td>
@endif
@if($solicitud->tipoCredito=='m')
    <td style="font-size:20px"><b>${{ number_format($abonadoCapital+$interesess+$plataform+$apro+$iva+$pagoProximo->interesMora+$pagoProximo->gastosCobranza) }}</b></td>
@endif
</tr>


</table>
            <!-- <div style="text-align: center;"><h1><b>HISTORIA CREDITICIA CON CREDITOS PANDA</b></h1></div> -->

 
    <table style="background:#f5f7fa;width:100%">
    <tr>
    <td>
    Datos del banco: Banco de las Microfinanzas Bancamia
    </td>
    </tr>
    <tr>
    <td>
    Tipo de cuenta: Ahorros
    </td>
    </tr>
    <tr>
    <td>
    A nombre de: Creditos Panda SAS. NIT: 901.369.753-0.
    </td>
    </tr>
    <tr>
    <td>
    Numero de cuenta: 2110065594200011.
    </td>
    </tr>
    </table>
    
</body>
</html>
<?php
