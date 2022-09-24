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
            <td class="text-center">
                <b>PASAJERO</b>
            </td>
        </tr>
    </table>
    <br>

    <table class="table">
        <tr>
            <th colspan="2" class="text-center">
                Datos del pasajero
            </th>
        </tr>
        <tr>
            <td width="30%">
                Nombres:
            </td>
            <td>
                {{$passenger->first_name}}
            </td>
        </tr>
        <tr>
            <td>
                Apellidos:
            </td>
            <td>
                {{$passenger->last_name}}
            </td>
        </tr>
        <tr>
            <td>
                Número premier:
            </td>
            <td>
                {{$passenger->premier_number}}
            </td>
        </tr>
        <tr>
            <td>
                Fecha de nacimiento:
            </td>
            <td>
                {{$passenger->birthdate->format('d-m-Y')}}
            </td>
        </tr>
        <tr>
            <td>
                Email:
            </td>
            <td>
                {{$passenger->email}}
            </td>
        </tr>
        <tr>
            <td>
                Teléfono 1:
            </td>
            <td>
                {{$passenger->phone_1}}
            </td>
        </tr>
        <tr>
            <td>
                Teléfono 2:
            </td>
            <td>
                {{$passenger->phone_2}}
            </td>
        </tr>
    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="2" class="text-center">
                Dirección
            </th>
        </tr>

        <tr>
            <td width="30%">
                País:
            </td>
            <td>
                {{$passenger->country->name}}
            </td>
        </tr>
        <tr>
            <td>
                Ciudad:
            </td>
            <td>
                {{$passenger->city}}
            </td>
        </tr>
        <tr>
            <td>
                Calle:
            </td>
            <td>
                {{$passenger->street}}
            </td>
        </tr>
        <tr>
            <td>
                Colonia:
            </td>
            <td>
                {{$passenger->colony}}
            </td>
        </tr>
        <tr>
            <td>
                Código postal:
            </td>
            <td>
                {{$passenger->zip_code}}
            </td>
        </tr>
    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="6" class="text-center">Contactos de emergencia</th>
        </tr>
        <tr>
            <th class="text-center">Nº</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Teléfono 1</th>
            <th>Teléfono 2</th>
        </tr>
        @foreach ($passenger->emergency_contacts as $i => $contact)
            <tr>
                <td class="text-center">{{$i+1}}</td>
                <td>{{$contact->first_name}}</td>
                <td>{{$contact->last_name}}</td>
                <td>{{$contact->email}}</td>
                <td>{{$contact->phone_1}}</td>
                <td>{{$contact->phone_2}}</td>
            </tr>
        @endforeach


    </table>
    <br>
    <table class="table">
        <tr>
            <th colspan="8" class="text-center">Nacionalidades / Documentos</th>
        </tr>
        <tr>
            <th class="text-center">Nº</th>
            <th>Nacionalidad</th>
            <th>Pasaporte</th>
            <th>Fecha Vto.</th>
            <th>Visa</th>
            <th>Fecha Vto.</th>
            <th>INE</th>
            <th>CURP</th>
        </tr>
        @foreach ($passenger->nationalities as $i => $nationality)

            <tr>
                <td class="text-center">{{$i+1}}</td>
                <td>{{$nationality->country->name}}</td>
                <td>{{$nationality->passport}}</td>
                <td>{{$nationality->passport_expired_date}}</td>
                <td>{{$nationality->visa}}</td>
                <td>{{$nationality->visa_expired_date}}</td>
                <td>{{$nationality->ine}}</td>
                <td>{{$nationality->curp}}</td>
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
                {{$passenger->observations}}
            </td>
        </tr>
    </table>
    Fecha de impresión: {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
