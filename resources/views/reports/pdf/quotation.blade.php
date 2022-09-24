<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de efectividad</title>
    <style>

        .table th{
          background-color: #003366;
          color:white;
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
          border: 1px solid #003366;
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
                <b>COTIZACIÓN # {{$quotation->folio}}</b>
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;">
        <tr>
            <td>
                <b>Cliente:</b> {{$quotation->client->name}}
                <br>
                <b>RUC:</b> {{$quotation->client->ruc}}
                <br>
                <b>Email:</b> {{$quotation->client->email}}
                <br>
                <b>Teléfonos:</b> {{$quotation->client->mobile.' '.$quotation->client->phone}}

            </td>
            <td>
                    <b>Fecha de cotización: </b> {{$quotation->created_at->format('d-m-Y')}}
                    <br>
                    <b>Estatus:</b> {{$quotation->status->name}}
                    <br>
                    <b>Cotizado por:</b> {{$quotation->created_by->first_name.' '.$quotation->created_by->last_name}}
                    <br>
                    <b>Tipo de Moneda:</b> {{$quotation->currency->name}}
            </td>
        </tr>
    </table>
    <table class="table">
        <tr>
            <th>Personas</th>
            <th>Cantidad</th>
        </tr>
        <tr>
            <td>Adultos</td>
            <td>{{$quotation->quantity_adults}}</td>
        </tr>
        <tr>
            <td>Niños</td>
            <td>{{$quotation->quantity_childrens}}</td>
        </tr>
        <tr>
            <td>Adultos mayores</td>
            <td>{{$quotation->quantity_elderly}}</td>
        </tr>
    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="5" class="text-center" style="border-bottom-color: white;">
                Destinos cotizados
            </th>
        </tr>
        <tr>
            <th style="width: 30px;">Nº</th>
            <th>Destino</th>
            <th>Descripción</th>
            <th>Fecha de inicio</th>
            <th>Fecha de fin</th>
        </tr>
        @foreach ($quotation->destinations as $i => $destination)

        <tr>
            <td>{{($i+1)}}</td>
            <td>{{ $destination->name }}</td>
            <td>{{ $destination->description }}</td>
            <td>{{ $destination->start_date->format('d-m-Y') }}</td>
            <td>{{ $destination->end_date->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center" style="border-bottom-color: white;">Servicios</th>
        </tr>
        <tr>
            <th style="width: 30px;">Nº</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th style="width: 80px;">Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
        @php
            $n = 1;
            $total = 0;
        @endphp
        @foreach ($quotation->details as $i => $detail)

        @if( ($detail->confirm && $detail->service->operator_commission == false) || ($show_all && $detail->service->operator_commission == false) )
        <tr>
            <td>{{($n++)}}</td>
            <td>{{$detail->service->name}}</td>
            <td>{{$detail->description}}</td>
            <td class="text-right">{{ number_format($detail->quantity,2,',','.')}}</td>
            <td class="text-right">$ {{ number_format($detail->price,2,',','.')}}</td>
            <td class="text-right">$ {{ number_format($detail->import,2,',','.')}}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td class="text-right" colspan="5">
                Total
            </td>
            <td class="text-right">
                $ {{ number_format($quotation->total,2,',','.') }}
            </td>
        </tr>


    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center" style="border-bottom-color: white;">Pasajeros</th>
        </tr>
        <tr>
            <th>Nombres y Apellidos</th>
            <th>Nacionalidad</th>
            <th>Ine</th>
            <th>Curp</th>
            <th>Pasaporte</th>
            <th>Visa</th>
        </tr>
        @foreach ($quotation->passengers as $passenger)
        <tr>
            <td>
                {{ $passenger->first_name.' '.$passenger->last_name }}
            </td>
            <td>
                {{ $passenger->nationalities[0]->country->name }}
            </td>
            <td>
                {{ $passenger->nationalities[0]->ine }}
            </td>
            <td>
                {{ $passenger->nationalities[0]->curp }}
            </td>
            <td>
                {{ $passenger->nationalities[0]->passport }}
            </td>
            <td>
                {{ $passenger->nationalities[0]->visa }}
            </td>
        </tr>
        @endforeach
    </table>
    <br>
    <table class="table">
        <tr>
            <th class="text-center">Observaciones</th>
        </tr>
        <tr>
            <td style="padding:25px; min-height:200px;">
                {{$quotation->observations}}
            </td>
        </tr>
    </table>
    Fecha de impresión: {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
