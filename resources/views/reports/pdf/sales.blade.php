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
                        <b>Reporte de ventas</b>
                    </td>
                </tr>
            </table>
            <br>
            Desde: {{$since->format('d-m-Y')}} Hasta: {{$until->format('d-m-Y')}}
    <table class="table">
        <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th>Ventas</th>
            <th>Importe (Pesos)</th>
        </tr>
        @php
            $sales = 0;
            $import = 0;
        @endphp
        @foreach ($data as $i => $client)
        @php
            $sales += $client->sales;
            $import += $client->import;
        @endphp
            <tr>
                <td class="text-center">{{$i+1}}</td>
                <td>{{$client->name}}</td>
                <td class="text-right">{{$client->sales}}</td>
                <td class="text-right">$ {{$client->import}}</td>

            </tr>
        @endforeach
        <tr>
            <th colspan="2">
                Totales
            </th>
            <th class="text-right">
                {{$sales}}
            </th>
            <th class="text-right">
                $ {{$import}}
            </th>

        </tr>
    </table>
    <br>
    {{date('d-m-Y h:i A')}}

</body>
</html>

