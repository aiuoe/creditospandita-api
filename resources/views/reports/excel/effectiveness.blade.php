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

    <table style="border:1px solid black;" border="1">
        <tr>
            <th><b>Nº</b></th>
            <th><b>Usuario</b></th>
            <th><b>Propuestas</b></th>
            <th><b>Aprobadas</b></th>
            <th><b>Efectividad</b></th>

        </tr>
        @php
            $propoals = 0;
            $approved = 0;
        @endphp
        @foreach ($data as $i => $user)
        @php
            $propoals += $user->propoals;
            $approved += $user->propoals_approved;
        @endphp
            <tr>
                <td class="text-center">{{$i+1}}</td>
                <td>{{$user->first_name.' '.$user->last_name}}</td>
                <td class="text-right">{{$user->propoals}}</td>
                <td class="text-right">{{$user->propoals_approved}}</td>
                <td class="text-right">{{round($user->effectiveness,2)}} %</td>

            </tr>
        @endforeach
        <tr>
            <th colspan="2">
                Totales
            </th>
            <th class="text-right">
                {{$propoals}}
            </th>
            <th class="text-right">
                {{$approved}}
            </th>
            <th class="text-right">

                {{ ($propoals>0) ? ($approved*100)/$propoals : 0  }} %
            </th>
        </tr>
    </table>
    <br>
    {{date('d-m-Y h:i A')}}
</body>
</html>
<?php
