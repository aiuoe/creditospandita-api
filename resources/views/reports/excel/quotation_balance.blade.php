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
            <td colspan="20">
                <img style=" margin: 0px 15px 0px 0px;" src="{{ public_path('images/logo.png') }}" width="440" />
            </td>

        </tr>
    </table>
    <br>
    <table class="table">
        <tr>
            <td colspan="6">
                <b>COTIZACIÓN # {{$quotation->folio}}</b>
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;">
        <tr>
            <td colspan="6">
                <b>Cliente:</b> {{$quotation->client->name}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <b>RFC:</b> {{$quotation->client->rfc}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <b>Email:</b> {{$quotation->client->email}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <b>Teléfonos:</b> {{$quotation->client->mobile.' '.$quotation->client->phone}}
            </td>
        </tr>

        <tr>
            <td colspan="6">
                <b>Fecha de cotización: </b> {{$quotation->created_at->format('d-m-Y')}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <b>Estatus:</b> {{$quotation->status->name}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                    <b>Cotizado por:</b> {{$quotation->created_by->first_name.' '.$quotation->created_by->last_name}}
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <b>Tipo de Moneda:</b> {{$quotation->currency->name}}
            </td>
        </tr>
    </table>


    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center" style="border-bottom-color: white;">
                <b>Servicios</b>
            </th>
        </tr>
        <tr>
            <th class="text-center">Nº</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th >Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
        @php
            $n = 1;
            $total = 0;
        @endphp
        @foreach ($quotation->details as $i => $detail)

        @if( ($detail->confirm && $detail->service->operator_commission == false))
        <tr>
            <td class="text-center">{{($n++)}}</td>
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
                <b>Total</b>
            </td>
            <td class="text-right">
                $ {{ number_format($quotation->total,2,',','.') }}
            </td>
        </tr>


    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center" style="border-bottom-color: white;">
                <b>Pagos</b>
            </th>
        </tr>
        <tr>
            <th class="text-center">Nº</th>
            <th>Método de pago</th>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Nro. Folio</th>
            <th>Importe</th>
        </tr>
        @foreach ($quotation->payments as $i => $payment)
            <tr>
                <td class="text-center">{{$i+1}}</td>
                <td>{{$payment->payment_method->name}}</td>
                <td>{{$payment->date_payment->format('d-m-Y')}}</td>
                <td>{{$payment->concept}}</td>
                <td>{{$payment->folio_number}}</td>
                <td class="text-right">{{number_format($payment->import,2,'.',',')}} $</td>

            </tr>
        @endforeach
        <tr>
            <td colspan="5" class="text-right">
                <b>Total</b>
            </td>
            <td class="text-right">{{number_format($quotation->payments->sum('import'),2,'.',',')}} $</td>
        </tr>
    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center" style="border-bottom-color: white;">Balance</th>
        </tr>
        <tr>
           <td><b>Total cotizado</b></td>
           <td class="text-right">{{ number_format($quotation->total,2,',','.') }} $</td>
        </tr>
        <tr>
           <td><b>Total pagos</b></td>
           <td class="text-right">{{number_format($quotation->payments->sum('import'),2,'.',',')}} $</td>
        </tr>
        <tr>
            <td><b>Saldo</b></td>
            <td class="text-right">
                {{number_format(($quotation->total-$quotation->payments->sum('import')),2,',','.')}} $
            </td>
        </tr>
    </table>


    Fecha de impresión: {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
