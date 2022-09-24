<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de pago</title>
    <style>


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
        caption{
            background-color: #003366;
            color:white;
            font-weight: bold;
        }
      </style>
</head>
<body>

    <table class="table">
        <tr>
            <td>
                <img style=" margin: 0px 15px 0px 0px;" src="{{ public_path('images/logo.png') }}" width="440" />
            </td>
            <th class="text-center">
                Reporte de pago
            </th>
        </tr>
    </table>
    <br>
    <table class="table table-striped table-bordered">
        <caption>Datos del cliente</caption>
        <tr>
            @if(!empty($payment->client->parent))
            <th style="width:50%;">
                    Cliente:
            </th>
            <td>
                {{$payment->client->parent->name}}
            </td>
            @endif
            <th style="width:50%;">
                @if(!empty($payment->client->parent))
                Sucursal:
                @else
                Cliente:
                @endif

            </th>
            <td>
                {{$payment->client->name}}
            </td>
        </tr>
        <tr>
            <th>
                RFC:
            </th>
            <td>
                {{$payment->client->rfc}}
            </td>
        </tr>
        <tr>
            <th>
                Email:
            </th>
            <td>
                {{$payment->client->email}}
            </td>
        </tr>
        <tr>
            <th>
                Código Postal:
            </th>
            <td>
                {{$payment->client->zip_code}}
            </td>
        </tr>
    </table>
    <br>

    <table class="table">
        <caption>Datos de la Cotización</caption>
        <tr>
            <th style="width: 50%;">Folio:</th>
            <td>{{$payment->quotation->folio}}</td>
        </tr>
        <tr>
            <th>Fecha de Cotización:</th>
            <td>{{$payment->quotation->created_at->format('d-m-Y')}}</td>
        </tr>
        <tr>
            <th>Cotizado por:</th>
            <td>{{$payment->quotation->created_by->first_name.' '.$payment->quotation->created_by->last_name}}</td>
        </tr>
        <tr>
            <th>Tipo de moneda:</th>
            <td>{{$payment->quotation->currency->name}}</td>
        </tr>
        <tr>
            <th>Total:</th>
            <td>{{number_format($payment->quotation->total,2,'.',',')}}</td>
        </tr>
    </table>
    <br>
    <table class="table">
        <caption>
            Datos del Pago
        </caption>
        <tr>
                <th style="width:50%;">
                Nro Folio:
            </th>
            <td>
                {{$payment->folio_number}}
            </td>
        </tr>
        <tr>
            <th>
                Fecha del pago:
            </th>
            <td>
                {{$payment->date_payment}}
            </td>
        </tr>
        <tr>
            <th>
                Concepto:
            </th>
            <td>
                {{$payment->concept}}
            </td>
        </tr>
        <tr>
            <th>
                Observasión:
            </th>
            <td>
                {{$payment->observation}}
            </td>
        </tr>
        <tr>
            <th>
                Método de Pago:
            </th>
            <td>
                {{$payment->payment_method->name}}
            </td>
        </tr>
        <tr>
            <th>
                Tipo de moneda:
            </th>
            <td>
                {{$payment->currency->name}}
            </td>
        </tr>
        <tr>
            <th>
                Importe:
            </th>
            <td>
                {{number_format($payment->import,2,'.',',')}} $
            </td>
        </tr>
    </table>


    Fecha de impresión: {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
