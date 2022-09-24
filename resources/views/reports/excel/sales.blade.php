<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<body>
    <table>
        <tr>
            <td></td>
            <td>
                <img src="{{ public_path('images/logo.png') }}" width="240" />
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td></td>
            <td>
                <br>
                <b>Reporte de efectividad</b>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>Desde: {{$since->format('d-m-Y')}} Hasta: {{$until->format('d-m-Y')}}</td>

        </tr>
    </table>
    <table>
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
                    <td class="text-right">{{$client->import}}</td>

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
                    {{$import}}
                </th>

            </tr>
        </table>

    <br>
    {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
