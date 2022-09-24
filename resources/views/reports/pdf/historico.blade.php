<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historico crediticio</title>
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
                        <h1><b>HISTORIA CREDITICIA CON CREDITOS PANDA</b></h1>
                    </td>
                   
                </tr>
                <tr>
                <td>
                <p>Certificamos el estado de los créditos abajo relacionados a <b>{{$usuario->first_name}} {{$usuario->last_name}}</b>
                identificado(a) con cédula de ciudadanía No <b>{{$usuario->n_document}}</b>
                , originados por la COMPAÑÍA DE CREDITOS PANDA  SAS.
                </p><br>
                <p>Se expide a solicitud del interesado el día <b> {{date('d-m-Y h:i A')}}</b></p>
                </td>
                </tr>
            </table>
            <br>
<table style="width: 100%;">
<tr style="background-color: #ebc121;color:white">
  <th># CRÉDITO</th>
  <th>VALOR</th>
  <th>FECHA</th>
  <th>ESTADO</th>
</tr>
@foreach($solicitud as $solicituds)
<tr>
  <td>{{$solicituds->numero_credito}}</td> 
  @if($solicituds->ofertaEnviada == 2)
  <td>$ {{(number_format($solicituds->montoSolicitado))}}</td>
  @elseif($solicituds->ofertaEnviada != 2)
  <td>$ {{($solicituds->ContraOferta != '' ? number_format($solicituds->ContraOferta->montoAprobado) : 0)}}</td> 
  @endif
  @if($solicituds->estatus !='pagado' && $solicituds->estatus !='castigado' && $solicituds->ProximoPago !='')
  <td>{{$solicituds->ProximoPago->fechaPago}}</td> 
  @elseif($solicituds->estatus =='pagado')
  <td>{{$solicituds->UltimoPago->fecha}}</td> 
  @elseif($solicituds->estatus =='castigado')
  <td>{{$solicituds->PagoParcial->created_at}}</td>
  @else
  <td></td>
  @endif
  <td>{{$solicituds->estatus}}</td>    
</tr>
@endforeach
</table>
            <!-- <div style="text-align: center;"><h1><b>HISTORIA CREDITICIA CON CREDITOS PANDA</b></h1></div> -->

    <br><br><br><br><br><br><br><br><br><br>
    <table style="border-top:1px solid black;width:100%">
    <tr>
    <td>
    <p>DERECHO A ENMIENDA DE ERRORES ART. 880 DE CÓDIGO DE COMERCIO</p>
    <p>Cordialmente,</p>
    <p>Atención al Cliente</p>
    <p>Departamento de Cartera</p>
    <p>CREDITOS PANDA SAS</p>
    </td>
    </tr>
    </table>
    
</body>
</html>
<?php
