<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de efectividad</title>
    <style>

        .table th{
          background-color: #ccc;
        }
        h1 {
          text-align: left;
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
    <table class="table">
        <tr>
            <td>
                <img style=" margin: 0px 15px 0px 0px;" src="{{ public_path('images/logo.png') }}" width="440" />
            </td>
            <td>
                @php

                @endphp
                <b>Historico de clientes</b>
            </td>
        </tr>
    </table>
    <br>
    Desde: {{$since->format('d-m-Y')}} Hasta: {{$until->format('d-m-Y')}}
    <br>
    <table class="table">
        <tr>
            <th>Cliente</th>
            <th>Fecha de cotización</th>

            <th>Desde</th>
            <th>Hasta</th>
            <th>Estatus</th>
            <th>Importe</th>
        </tr>
        @php

            $import = 0;
        @endphp
        @foreach ($clients as $i => $client)


          @foreach( $client->quotations as $quotation )
            @php
              $import += $quotation->total;
            @endphp
            <tr>
                <td>{{$client->name}}</td>
                <td>{{ $quotation->created_at->format('d-m-Y')}}</td>

                <td>{{($quotation->start_date) ? $quotation->start_date->format('d-m-Y') : ''}}</td>
                <td>{{($quotation->end_date) ? $quotation->end_date->format('d-m-Y') : ''}}</td>
                <td>{{$quotation->status->name}}</td>
                <td class="text-right">$ {{number_format($quotation->total,2,',','.')}}</td>
            </tr>
          @endforeach


        @endforeach
        <tr>
            <th colspan="5">
                Totales
            </th>
            <th class="text-right">
                $ {{number_format($import,2,',','.')}}
            </th>

        </tr>
    </table>
    <br>
    {{date('d-m-Y h:i A')}}

</body>
</html>
<?php
