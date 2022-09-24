<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  
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
        table {
          border-collapse: collapse;
        }

        table, th, td {
          border: 1px solid black;
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
<p><strong>CONTRATO DE CR&Eacute;DITO No. {{$solicitud->numero_credito}} CON CR&Eacute;DITOS PANDA S.A.S.</strong></p>
<p style="padding-left: 30px;"><strong>1. &iquest;QU&Eacute; OFRECEMOS EN CREDITOS PANDA?.</strong></p>
<p><strong>CREDITOS PANDA </strong>es una plataforma de soluciones financieras, por medio de la cual, nuestros clientes podr&aacute;n solicitar en cualquier momento prestamos en l&iacute;nea y digitales de libre destinaci&oacute;n, bajo monto y a corto plazo. Por medio de la plataforma los clientes podr&aacute;n solicitar cr&eacute;ditos por distintos montos, sin que exista obligaci&oacute;n de <strong>CR</strong><strong>EDITOS PANDA </strong>de aprobar el cr&eacute;dito solicitado, por lo que podr&aacute; rechazar el mismo o realizar una oferta al cliente con base en el an&aacute;lisis de cr&eacute;dito efectuado.</p>
<p>Los pr&eacute;stamos otorgados por <strong>CREDITOS PANDA</strong> bajo este contrato son aprobados y desembolsados por <strong>CREDITOS PANDA</strong> en pesos colombianos (COP) y son pagados por sus clientes en la misma moneda.</p>
<p>Tenemos dos opciones de cr&eacute;ditos, seg&uacute;n su plazo para que realices el pago de los mismos: PANDA MESES cuando el cr&eacute;dito que solicitas y gastos asociados, deban ser pagados entre dos (2) a doce (12) meses y PANDA D&Iacute;AS, cuando el cr&eacute;dito y gastos asociados, deban ser pagados entre ocho (8) a treinta y cinco (35) d&iacute;as. Los intereses que se generen en cualquier modalidad de cr&eacute;dito, se calculan sobre la base de un a&ntilde;o calendario de 360 d&iacute;as</p>
<p><strong>CREDITOS PANDA </strong>hace uso de plataformas tecnol&oacute;gicas agiles e innovadoras que facilitan a sus clientes el acceso a cr&eacute;ditos y ofrecen por ende un servicio adicional al simple pr&eacute;stamo de dinero.</p>
<p style="padding-left: 30px;"><strong>2. &iquest;QUI&Eacute;N ES EL ACREEDOR/PRESTAMISTA DE LOS CR&Eacute;DITOS?</strong></p>
<p><strong>CREDITOS PANDA</strong> es una empresa privada que otorga cr&eacute;ditos con sus recursos propios, que no realiza captaci&oacute;n masiva de dineros del p&uacute;blico, ni actividades de intermediaci&oacute;n bancaria ni otras actividades exclusivas de los establecimientos de cr&eacute;dito u otras instituciones financieras vigiladas en Colombia</p>
<p>La informaci&oacute;n legal de <strong>CREDITOS PANDA </strong>es la siguiente:</p>
<p><strong>Raz&oacute;n social: CREDITOS PANDA S.A.S</strong><strong><em>, </em></strong></p>
<p><strong>Nit: </strong>901.369.753-0</p>
<p><strong>Domicilio principal: </strong>Carrera 49 B. No 93-38 Bogot&aacute;, Colombia;</p>
<p>Correo electr&oacute;nico: <a href="mailto:info@creditospanda.com">info@creditospanda.com</a></p>
<p>Tel&eacute;fono de contacto: 3212403734</p>
<p style="padding-left: 30px;"><strong>3. Datos de nuestro cliente (deudor) </strong></p>
<p>Nombre del Deudor: {{$usuario->first_name}} {{$usuario->second_name}} {{$usuario->last_name}} {{$usuario->second_last_name}}</p>
<p>C&eacute;dula de Ciudadan&iacute;a: {{$usuario->n_document}}</p>
<p>Direcci&oacute;n: {{$basica->direccion}}</p>
<p>Tel&eacute;fono: {{$usuario->phone_number}}</p>
<p>Correo electr&oacute;nico: {{$usuario->email}}</p>
<p>Una vez recibes el desembolso del cr&eacute;dito solicitado y aceptado por tu parte, te conviertes en deudor de <strong>CREDITOS PANDA </strong>y adquieres la obligaci&oacute;n de pagar el cr&eacute;dito desembolsado, m&aacute;s sus intereses y otros gastos asociados en los plazos que aceptaste al momento de solicitar tu desembolso.</p>
<p style="padding-left: 30px;"><strong>4. OBJETO DEL ACUERDO </strong>&nbsp;</p>
<p><strong>CR&Eacute;DITOS PANDA, </strong>previo an&aacute;lisis y aprobaci&oacute;n del riesgo de cr&eacute;dito del DEUDOR se compromete a entregar en calidad de pr&eacute;stamo de consumo al <strong>DEUDOR</strong> el monto de dinero acordado, en los plazos y condiciones aceptados previamente por las partes.</p>
<p><strong>5. DEUDOR T&Eacute;RMINOS Y CONDICIONES DEL PR&Eacute;STAMO QUE SOLICITASTE Y ACEPTASTE.</strong></p>
<p><strong>CR&Eacute;DITOS PANDA, </strong>luego del an&aacute;lisis de cr&eacute;dito realizado, otorgar&aacute; en calidad de pr&eacute;stamo de consumo al <strong>DEUDOR, </strong>el siguiente cr&eacute;dito:</p>
<table border="1">
<tbody>
<tr>
<td width="150">
<p>Modalidad de pr&eacute;stamo</p>
</td>
<td width="250">
<p><strong>@if($solicitud->tipoCredito=='m')Panda Meses
    @elseif($solicitud->tipoCredito=='d')Panda D&iacute;as
    @endif</strong></p>
</td>
</tr>
<tr>
<td width="150">
<p>Monto del pr&eacute;stamo otorgado:</p>
</td>
<td width="250">
<p>$ {{$contra_oferta ? number_format($contra_oferta->montoAprobado) : number_format($solicitud->montoSolicitado)}}</p>
</td>
</tr>
<tr>
<td width="150">
<p>No. De Cuotas</p>
</td>
<td width="250">
<p>{{$contra_oferta ? $contra_oferta->plazo : $solicitud->plazo}}</p>
</td>
</tr>
<tr>
<td width="150">
<p>Periodicidad del pago de las cuotas:</p>
</td>
<td width="250">
<p> @if($solicitud->tipoCredito=='m')
@php
  $m = intval(date('m'));
  $d = date('d');
  $a = intval(date('Y'));
  $mi = 1;
@endphp
        @for($i=1; $i<=$solicitud->plazo; $i++)
        @php
        $mf = $m+$i
        @endphp
          @if($mf>12)
          @php
          $f = ($a+1).'-'.($mi++).'-'.($d);
          @endphp
          {{ date('d/m/Y', strtotime($f)) }},
          @else
          @php
          $f = ($a).'-'.($m+$i).'-'.($d);
          @endphp
          {{ date('d/m/Y', strtotime($f)) }},
          @endif
        @endfor
    @elseif($solicitud->tipoCredito=='d')
        @php
        $date = date("d-m-Y");
        $mod_date = strtotime($date."+ ".$solicitud->plazo." days"); 
        @endphp
        {{date("d-m-Y",$mod_date)}}
    @endif
</p>
</td>
</tr>
@if($solicitud->tipoCredito=='m')
<tr>
<td width="150">
<p>Valor de cada cuota</p>
</td>
<td width="250">

<p>$ {{number_format($cuota)}}</p>

</td>
</tr>
@endif
<tr>
<td width="150">
<p><strong>Valor total a pagar: </strong></p>
</td>
<td width="250">
<p><strong>$ {{$contra_oferta ? number_format($contra_oferta->totalPagar) : number_format($solicitud->totalPagar)}}</strong></p>
</td>
</tr>
<tr>
<td width="150">
<p>Inter&eacute;s remuneratorio:&nbsp;</p>
</td>
<td width="250">
<p>$ {{$contra_oferta ? number_format($contra_oferta->tasaInteres) : number_format($solicitud->tasaInteres)}}</p>
</td>
</tr>
<tr>
<td width="150">
<p>Tasa de Inter&eacute;s Fija</p>
</td>
<td width="250">
	@if($solicitud->tipoCredito=='m')
<p><strong>Panda Meses </strong>23% E.A. (Efectivo Anual)</p>
    @elseif($solicitud->tipoCredito=='d')
<p><strong>Panda Dias </strong>14% E.A. (Efectivo Anual)</p>
@endif
</td>
</tr>
<tr>
<td width="150">
<p>Tasa de inter&eacute;s moratoria</p>
</td>
<td width="250">
<p>La tasa moratoria m&aacute;xima permitida en Colombia para este tipo de cr&eacute;ditos. S&oacute;lo se cobrar&aacute;n si incumples el pago acordado.</p>
</td>
</tr>
<tr>
<td width="150">
<p>Tarifa Plataforma (opcional)</p>
</td>
<td width="250">
@if($solicitud->tipoCredito=='m') 
<p><strong>Panda Meses </strong>$ {{$contra_oferta ? number_format($contra_oferta->plataforma) : number_format($solicitud->plataforma)}}</p>
@elseif($solicitud->tipoCredito=='d')
<p><strong>Panda D&iacute;as </strong></p>
<p>$1.000 por cada d&iacute;a hasta el pago total de la cantidad otorgada. Es opcional, por lo tanto, esta no ser&aacute; cobrada si el DEUDOR decide presentar toda la documentaci&oacute;n necesaria para obtener el Pr&eacute;stamo en forma f&iacute;sico. La Tarifa de Plataforma ser&aacute; cobrada por el tiempo de uso real de la Plataforma.</p>
@endif
</td>
</tr>
<tr>
<td width="150">
<p>Tarifa Aprobaci&oacute;n R&aacute;pida (opcional)</p>
</td>
<td width="250">
@if($solicitud->tipoCredito=='m')     
<p><strong>Panda Meses</strong></p>
<p>${{$contra_oferta ? number_format($contra_oferta->aprobacionRapida) : number_format($solicitud->aprobacionRapida)}} del monto del pr&eacute;stamo aprobado</p>
@elseif($solicitud->tipoCredito=='d')
<p><strong>Panda D&iacute;as </strong></p>
<p>${{$contra_oferta ? number_format($contra_oferta->aprobacionRapida) : number_format($solicitud->aprobacionRapida)}} del monto del Pr&eacute;stamo aprobado.</p>
@endif
</td>
</tr>
<tr>
<td width="150">
<p>IVA</p>
</td>
<td width="250">
<p><strong>IVA: </strong>Se cobrar&aacute; el IVA establecido legalmente en relaci&oacute;n con la Tarifa de Plataforma y la Tarifa de Aprobaci&oacute;n R&aacute;pida en la Fecha de Pago del Pr&eacute;stamo.</p>
</td>
</tr>
</tbody>
</table>
<p style="padding-left: 30px;"><strong>6. TARIFAS Y OTROS SERVICIOS.</strong></p>
<p><strong><br />Tarifa Aprobaci&oacute;n R&aacute;pida (opcional): </strong>El <strong>DEUDOR </strong>tiene a su elecci&oacute;n el uso o no de la aprobaci&oacute;n r&aacute;pida de <strong>CR&Eacute;DITOS PANDA, </strong>no obstante, en caso de uso de la aprobaci&oacute;n r&aacute;pida el <strong>DEUDOR </strong>deber&aacute; sufragar los costos de utilizaci&oacute;n de la misma conforme al valor informado en los t&eacute;rminos y condiciones del pr&eacute;stamo se&ntilde;alados en este documento y se causar&aacute; al momento en el que el <strong>DEUDOR </strong>reciba el monto del pr&eacute;stamo otorgado, no obstante, por comodidad del <strong>DEUDOR, </strong>este monto ser&aacute; diferido en igual cantidad de cuotas mensuales a las acordadas para el pr&eacute;stamo. Pago diferido que desde ya acepta el <strong>DEUDOR </strong>que se realice.</p>
<p>En caso de pago anticipado del pr&eacute;stamo antes de la fecha de vencimiento, el <strong>DEUDOR </strong>deber&aacute; pagar a <strong>CR&Eacute;DITOS PANDA </strong>el monto restante que se deba de la tarifa de aprobaci&oacute;n R&aacute;pida.</p>
<p>El uso de la aprobaci&oacute;n r&aacute;pida de <strong>CR&Eacute;DITOS PANDA </strong>permitir&aacute; al <strong>DEUDOR:</strong></p>
<ol>
<li>Tener respuesta dentro de las 24 horas h&aacute;biles siguientes a su solicitud de cr&eacute;dito.</li>
<li>Hacer uso de una metodolog&iacute;a &aacute;gil en el estudio de la solicitud de cr&eacute;dito o de su eventual novaci&oacute;n presentada por el DEUDOR, metodolog&iacute;a que utiliza desarrollos tecnol&oacute;gicos especializados que aceleran el proceso y facilita no exigir documentos y garant&iacute;as adicionales tales como, pero sin limitarse a un codeudor con finca ra&iacute;z no hipotecada, certificado de tradici&oacute;n y libertad del inmueble del co-deudor y pagar&eacute; firmado por el codeudor debidamente notariado.</li>
</ol>
<p>Si decides no pagar la Tarifa de Aprobaci&oacute;n R&aacute;pida, pero si la tarifa de plataforma tu solicitud ser&aacute; revisada en un plazo de hasta 15 d&iacute;as h&aacute;biles, despu&eacute;s de ser recibidos via correo electr&oacute;nico los siguientes documentos: A. Certificado de tradici&oacute;n y libertad del inmueble del co-deudor, con fecha no mayor de 30 d&iacute;as a la fecha de presentaci&oacute;n del documento. B. certificaciones laborales del codeudor solidario &lt; 30 d&iacute;as que acredite m&iacute;nimo 6 meses de historia laboral en la empresa. C. Certificado de ingresos, expedido por contador acreditado. D. pagar&eacute; y carta de instrucciones firmados por el codeudor solidario debidamente notarizados.</p>
<p>Se entender&aacute; el cobro de la tarifa de aprobaci&oacute;n R&aacute;pida cuando el <strong>DEUDOR </strong>as&iacute; lo haya aceptado en el formulario de solicitud dentro de la p&aacute;gina web y la firma electr&oacute;nica del presente documento implica aceptaci&oacute;n del <strong>DEUDOR </strong>de los cargos de la tarifa aprobaci&oacute;n r&aacute;pida.</p>
<p><strong>Tarifa uso de la plataforma (opcional): </strong>El <strong>DEUDOR </strong>tiene a su elecci&oacute;n el uso o no de la plataforma tecnol&oacute;gica de <strong>CR&Eacute;DITOS PANDA, </strong>no obstante, en caso de uso de la plataforma el <strong>DEUDOR </strong>deber&aacute; sufragar los costos de utilizaci&oacute;n de la misma conforme al valor informado en los t&eacute;rminos y condiciones del pr&eacute;stamo, aclarando que la tarifa se calcular&aacute; el primer d&iacute;a h&aacute;bil de cada mes hasta el pago total del pr&eacute;stamo otorgado. Para conveniencia del DEUDOR esta tarifa ser&aacute; distribuida en pagos mensuales iguales hechos por el DEUDOR. En caso de pago anticipado del pr&eacute;stamo la tarifa se calcular&aacute; hasta el momento en que se us&oacute; la plataforma.</p>
<p>La tarifa no ser&aacute; cobrada en el caso que el <strong>DEUDOR</strong> decida no hacer uso de la misma y realizar todos los tr&aacute;mites de solicitud del cr&eacute;dito y pagos posteriores de forma f&iacute;sica.</p>
<p>El uso de la plataforma de <strong>CR&Eacute;DITOS PANDA </strong>permitir&aacute;:</p>
<ol style="list-style-type: upper-alpha;">
<li>Llenar el formulario de solicitud de cr&eacute;dito en l&iacute;nea</li>
<li>Acceso a un perfil en l&iacute;nea espec&iacute;ficamente designado para &eacute;ste en la p&aacute;gina web de <strong>CREDITOS PANDA</strong></li>
<li>Organizaci&oacute;n de informaci&oacute;n (capital, intereses, costos, etc.) del pr&eacute;stamo.</li>
<li>Firma digital y/o electr&oacute;nica de documentos.</li>
<li>Pagos Electr&oacute;nicos.</li>
<li>Seguimiento del progreso del pr&eacute;stamo del <strong>DEUDOR</strong> a trav&eacute;s de plataformas omni-canales (computador, tel&eacute;fono celular)</li>
<li>Consulta de pagos y saldos.</li>
<li>Recepci&oacute;n de comunicaciones en relaci&oacute;n con promociones y otras actividades de mercadeo;</li>
<li>Evitar impresi&oacute;n y env&iacute;o por correo certificado del formulario de solicitud de cr&eacute;dito y otros documentos detallados m&aacute;s adelante, y en caso de ser aprobada la solicitud el env&iacute;o por correo certificado de los siguientes documentos firmados y notarizados: contrato de mutuo, poder, pagar&eacute;, carta de instrucciones y autorizaci&oacute;n de descuento a tu empleador</li>
</ol>
<p>Si decides pagar la Tarifa de Aprobaci&oacute;n R&aacute;pida, pero no la Tarifa de Plataforma, tienes que enviar por correo certificado a nuestras oficinas la siguiente documentaci&oacute;n; A) formulario de solicitud de cr&eacute;dito firmado y notariado B) copia de tu c&eacute;dula ampliada, C) copia de tus servicios p&uacute;blicos D) extracto bancario &uacute;ltimos tres meses E) si eres independiente deber&aacute;s anexar F) certificaci&oacute;n de pagos a la seguridad social y G) copia de la matr&iacute;cula de tu veh&iacute;culo. ADICIONALMENTE si es aprobada tu solicitud deber&aacute;s imprimir y enviarnos H) el presente contrato de mutuo, I) carta de autorizaci&oacute;n de descuento, J) el poder, K) el pagar&eacute; y L) carta de instrucciones debidamente diligenciados y notarizados, a la direcci&oacute;n de nuestras oficinas que aparece en nuestra p&aacute;gina web WWW.CREDITOSPANDA.COM.</p>
<p>El solo uso de la plataforma de <strong>CR&Eacute;DITOS PANDA</strong> y la firma del presente documento a trav&eacute;s de la misma, implica aceptaci&oacute;n del <strong>DEUDOR </strong>de los cargos por uso de la plataforma.</p>
<p>Si decides aplicar SIN aceptar la Tarifa de Plataforma y la de Aprobaci&oacute;n R&aacute;pida, tendr&aacute;s que enviar por correo certificado los siguientes documentos</p>
<ol style="list-style-type: upper-alpha;">
<li>Certificado de tradici&oacute;n y libertad del inmueble del co-deudor, con fecha no mayor de 30 d&iacute;as a la fecha de presentaci&oacute;n del documento.</li>
<li>Certificaciones laborales del codeudor solidario &lt; 30 d&iacute;as que acredite m&iacute;nimo 6 meses de historia laboral en la empresa.</li>
<li>Certificado de ingresos, expedido por contador acreditado.</li>
<li>pagar&eacute; y carta de instrucciones firmados por el codeudor solidario debidamente notarizados.</li>
<li>Formulario de solicitud de cr&eacute;dito firmado y notarizado</li>
<li>copia de tu c&eacute;dula ampliada</li>
<li>copia de tus servicios p&uacute;blicos</li>
<li>extracto bancario &uacute;ltimos tres meses</li>
<li>Si eres independiente deber&aacute;s anexar: certificaci&oacute;n de pagos a la seguridad social y copia de la matr&iacute;cula de tu veh&iacute;culo.</li>
<li>ADICIONALMENTE si es aprobada tu solicitud deber&aacute;s imprimir y enviarnos</li>
<ul style="list-style-type: disc;">
<li>El presente contrato de mutuo</li>
<li>Carta de autorizaci&oacute;n de descuento</li>
<li>el poder</li>
<li>el pagar&eacute;</li>
<li>carta de instrucciones debidamente diligenciados y notarizados, a la direcci&oacute;n de nuestras oficinas que aparece en nuestra p&aacute;gina web WWW.CREDITOSPANDA.COM.</li>
</ul>
</ol>
<p>Despu&eacute;s de recibir toda la informaci&oacute;n requerida, la respuesta a la solicitud se dar&aacute; en un plazo de hasta 15 d&iacute;as h&aacute;biles.</p>
<p><strong>Novaci&oacute;n</strong>: La novaci&oacute;n (obtener m&aacute;s plazo para pagar el cr&eacute;dito) es un servicio solicitado por el <strong>DEUDOR</strong> de manera OPCIONAL, en el cual se incurre en costos asociados a la prestaci&oacute;n del servicio (Art&iacute;culo 1700 y 1701 del C&oacute;digo Civil Colombiano). Es un servicio opcional &uacute;nicamente para el &ldquo;prestamo de Panda D&iacute;as&rdquo;. EL <strong>DEUDOR </strong>podr&aacute; solicitarlo antes o despu&eacute;s de la fecha de vencimiento del cr&eacute;dito. Al momento de vencerse tendr&aacute; m&aacute;ximo 15 d&iacute;as para solicitar la Novaci&oacute;n. El d&iacute;a que el <strong>DEUDOR </strong>solicite la novaci&oacute;n deber&aacute; hacer el pago de: Intereses del cr&eacute;dito actual, tarifa uso de la plataforma y la aprobaci&oacute;n r&aacute;pida si solicit&oacute; estos servicios e IVA causado.</p>
<p>Cada Per&iacute;odo adicional generado por la novaci&oacute;n ser&aacute; de la misma duraci&oacute;n que el per&iacute;odo de vencimiento inicial y durante este per&iacute;odo se generar&aacute;n intereses remuneratorios seg&uacute;n la tasa pactada y las tarifas por aprobaci&oacute;n r&aacute;pida, uso de la plataforma y cualquier otra solicitada y aprobada.</p>
<p>Esta opci&oacute;n de novaci&oacute;n podr&aacute; no generar costo adicional en el caso de que el Deudor no acepte las tarifas de uso de la plataforma ni la tarifa de aprobaci&oacute;n r&aacute;pida. En este evento, el Deudor deber&aacute; hacer entrega por medios f&iacute;sicos, los documentos necesarios para analizar el cupo de cr&eacute;dito en esta novaci&oacute;n:</p>
<p>a)Certificado de tradici&oacute;n y libertad del inmueble del co-deudor, con fecha no mayor de 30 d&iacute;as a la fecha de presentaci&oacute;n del documento. b) Certificaciones laborales del codeudor solidario &lt; 30 d&iacute;as que acredite m&iacute;nimo 6 meses de historia laboral en la empresa. c) Certificado de ingresos, expedido por contador acreditado. d)pagar&eacute; y carta de instrucciones firmados por el codeudor solidario debidamente notarizados. e) Formulario de solicitud de cr&eacute;dito firmado y notarizado. f) copia de tu c&eacute;dula ampliada g) copia de tus servicios p&uacute;blicos h) extracto bancario &uacute;ltimos tres meses i) Si eres independiente deber&aacute;s anexar: certificaci&oacute;n de pagos a la seguridad social y copia de la matr&iacute;cula de tu veh&iacute;culo. j) ADICIONALMENTE si es aprobada tu solicitud deber&aacute;s imprimir y enviarnos: El presente contrato de mutuo, Carta de autorizaci&oacute;n de descuento, el poder, el pagar&eacute; y carta de instrucciones debidamente diligenciados y notarizados, a la direcci&oacute;n de nuestras oficinas que aparece en nuestra p&aacute;gina web <a href="WWW.CREDITOSPANDA.COM">WWW.CREDITOSPANDA.COM</a>.</p>
<p style="padding-left: 30px;"><strong>7. PROCEDIMIENTO DE SOLICITUD DEL PRESTAMO </strong></p>
<p style="padding-left: 30px;">7.1 Al solicitar un Pr&eacute;stamo, el DEUDOR debe primero seleccionar en la calculadora de la p&aacute;gina web de <a href="http://www.creditospanda.com/">creditospanda.com</a> los campos correspondientes al tipo de pr&eacute;stamo ya sea &ldquo;Panda D&iacute;as&rdquo; o &ldquo;Panda Meses&rdquo;, el monto y el per&iacute;odo de financiamiento. Al dar clic en &ldquo;solicitar un pr&eacute;stamo&rdquo; el <strong>DEUDOR </strong>ser&aacute; re direccionado al formulario de solicitud de pr&eacute;stamo donde podr&aacute; confirmar su aceptaci&oacute;n de este contrato, dar su aprobaci&oacute;n para el uso, tratamiento y almacenamiento de sus datos personales por parte de CREDITOS PANDA, as&iacute; como su consentimiento para autorizar a CREDITOS PANDA solicitar su informaci&oacute;n a operadores de informaci&oacute;n del PILA, y que estos provean la informaci&oacute;n a CREDITOS PANDA, aceptar la tarifa r&aacute;pida (opcional), la tarifa de uso de plataforma (opcional), pagar&eacute; y carta de instrucciones y el formato de autorizaci&oacute;n descuento por n&oacute;mina.</p>
<p style="padding-left: 30px;">7.2 El DEUDOR aplica para un Pr&eacute;stamo, diligenciando el formulario de Solicitud a trav&eacute;s de la P&aacute;gina Web o de forma f&iacute;sica, de acuerdo con las condiciones especificadas en este contrato. En la Solicitud, es necesario especificar en los campos correspondientes datos personales como nombres y apellido, n&uacute;mero de cedula, direcci&oacute;n, informaci&oacute;n de empleo (lugar de trabajo), ingresos y egresos mensuales, direcci&oacute;n de correo electr&oacute;nico, n&uacute;mero de tel&eacute;fono de contacto, n&uacute;mero de cuenta de desembolso del Pr&eacute;stamo, referencias personales y comerciales, fotograf&iacute;a del documento de identidad de la persona titular de la cuenta, cualquier otro documento o informaci&oacute;n que sea necesaria para la aprobaci&oacute;n del pr&eacute;stamo.</p>
<p style="padding-left: 30px;">7.3&nbsp;<strong>CR&Eacute;DITOS PANDA</strong> no ser&aacute; responsable en caso de cualquier error imputable al <strong>DEUDOR </strong>en los datos provistos por este. Si el <strong>DEUDOR</strong> presenta su Solicitud en la P&aacute;gina Web, la Solicitud debe completarse por el mismo DEUDOR. Si la Solicitud es presentada por tel&eacute;fono, los datos relevantes son dados por el <strong>DEUDOR</strong> a un empleado de <strong>CREDITOS PANDA</strong> que completar&aacute; el formulario de Solicitud a trav&eacute;s de la plataforma tecnol&oacute;gica autorizando previamente el <strong>DEUDOR</strong> la creaci&oacute;n de su perfil y el cobro de uso de la plataforma (opcional) y la tarifa de aprobaci&oacute;n r&aacute;pida (opcional).</p>
<p style="padding-left: 30px;">7.4<strong>&nbsp;CREDITOS PANDA</strong> verificar&aacute; la capacidad crediticia del DEUDOR, la afiliaci&oacute;n y pago de los aportes al sistema de seguridad social integral, tales como ingreso base de cotizaci&oacute;n y dem&aacute;s informaci&oacute;n relacionada con la situaci&oacute;n laboral y empleador y la legitimidad de los datos reportados con los operadores de informaci&oacute;n del PILA. Si se requiere informaci&oacute;n adicional <strong>CREDITOS PANDA</strong> se pondr&aacute; en contacto con el <strong>DEUDOR </strong>para completar la informaci&oacute;n necesaria y requerida para aprobar el pr&eacute;stamo.</p>
<p style="padding-left: 30px;">7.5 Si la solicitud del pr&eacute;stamo fue aprobada recibir&aacute; un correo electr&oacute;nico con los documentos legales necesarios para su revisi&oacute;n, aprobaci&oacute;n y posterior firma electr&oacute;nica. La suscripci&oacute;n y la confirmaci&oacute;n se realizar&aacute; sobre la base de un c&oacute;digo individual &uacute;nico recibido a trav&eacute;s de mensaje de texto y/o v&iacute;a correo electr&oacute;nico. </p>
<p style="padding-left: 30px;">7.6 Una vez que el DEUDOR haya firmado toda la documentaci&oacute;n electronicamente, el monto del Pr&eacute;stamo se transferir&aacute; a la cuenta bancaria registrada por el DEUDOR. Para todos los prop&oacute;sitos, la confirmaci&oacute;n del Contrato se entender&aacute; como una subscripci&oacute;n del mismo por el DEUDOR.</p>
<p style="padding-left: 30px;"><strong>8. EVALUACION DE LA CAPACIDAD DE ENDEUDAMIENTO DEL DEUDOR Y APROBACION DE LA APLICACI&Oacute;N. </strong></p>
<p style="padding-left: 30px;">8.1 Al recibir la Solicitud, <strong>CREDITOS PANDA</strong> evaluar&aacute; la capacidad crediticia del DEUDOR sobre la informaci&oacute;n real existente y la informaci&oacute;n obtenida del <strong>DEUDOR</strong>, verific&aacute;ndola en la base de datos de los Operadores de Informaci&oacute;n PILA, y a la que puede acceder de forma legal. <strong>CREDITOS PANDA</strong> si ve la necesidad contactara al DEUDOR para confirmar parte de la informaci&oacute;n. Si, como resultado del an&aacute;lisis, <strong>CREDITOS PANDA</strong> rechaza el otorgamiento del Pr&eacute;stamo, <strong>CREDITOS PANDA</strong> informar&aacute; al DEUDOR de forma inmediata y sin costo alguno sobre el resultado de la validaci&oacute;n.</p>
<p style="padding-left: 30px;">8.2<strong>&nbsp;CR&Eacute;DITOS PANDA</strong> no comparte informaci&oacute;n de clientes con terceras personas distintas a las autorizadas en el presente contrato y en su pol&iacute;tica de tratamiento de datos personales ni cobra comisiones o gastos por anticipado. No usara intermediarios para diligenciar, estudiar o agilizar la solicitud o desembolsar y procesar el cr&eacute;dito.</p>
<p style="padding-left: 30px;">8.3<strong>&nbsp;CREDITOS PANDA</strong> puede, a su criterio, solicitar informaci&oacute;n o documentos adicionales, tener una entrevista con el <strong>DEUDOR</strong>, pedir una copia del documento de identidad presentado por el <strong>DEUDOR </strong>as&iacute; como foto del DEUDOR para realizar procesos de identificaci&oacute;n. La entrega de informaci&oacute;n por parte del <strong>DEUDOR</strong> es voluntaria, sin embargo, la negativa del <strong>DEUDOR</strong> puede afectar la decisi&oacute;n de otorgar el pr&eacute;stamo.</p>
<p style="padding-left: 30px;">8.4 Al enviar la Solicitud, el DEUDOR autoriza a <strong>CREDITOS PANDA</strong> a obtener informaci&oacute;n comercial sobre el DEUDOR de los Operadores de Informaci&oacute;n y de los operadores de informaci&oacute;n del PILA.</p>
<p style="padding-left: 30px;">8.5 Para los fines del Pr&eacute;stamo que se otorgar&aacute; en virtud de este contrato, el <strong>DEUDOR</strong> declara que en el momento de realizar la Solicitud: El <strong>DEUDOR</strong> no tiene obligaciones econ&oacute;micas pendientes diferentes de aquellas reportadas en las centrales de riesgo e informaci&oacute;n financiera (Datacr&eacute;dito, CIFIN u otras similares), ni oligaciones trubutarias, tampoco tiene obligaciones con su actual o anterior empleador, ni con cualquier persona natural y todos los datos proporcionados en la Cuenta de Cliente, la Solicitud, as&iacute; como en el Contrato y en todos los documentos adicionales son completos y verdaderos. El <strong>DEUDOR </strong>no tiene procesos judiciales existentes o de arbitraje pendientes contra &eacute;l, y no hay razones que puedan llevar a dichos procedimientos.</p>
<p style="padding-left: 30px;">8.6 Si el <strong>DEUDOR</strong> proporciona datos falsos, seg&uacute;n cuando ocurra, <strong>CREDITOS PANDA</strong> puede negarse a firmar el Contrato u otorgar el Pr&eacute;stamo, o terminar el Contrato (si est&aacute; firmado) sin previo aviso y demandar el pago anticipado del Pr&eacute;stamo, junto con todos los honorarios, costos e intereses originados hasta la fecha de terminaci&oacute;n.</p>
<p style="padding-left: 30px;">8.7 En caso de que el <strong>DEUDOR</strong> haya elegido voluntariamente pagar la Tarifa de Aprobaci&oacute;n r&aacute;pida, la aprobaci&oacute;n puede darse en el instante o a m&aacute;s tardar dentro de las siguientes veinticuatro (24) horas habiles despu&eacute;s de enviar la Solicitud. Si la Solicitud no se aprueba dentro de ese l&iacute;mite de tiempo, el Contrato no se celebrar&aacute; entre <strong>CREDITOS PANDA</strong> y el <strong>DEUDOR</strong>. El horario de <strong>CREDITOS PANDA</strong> es de lunes a viernes, de 8:00 a.m. a 5:00 p.m. Si su Solicitud se envi&oacute; despu&eacute;s de este horario, se analizar&aacute; despu&eacute;s de las 10 a.m. del d&iacute;a h&aacute;bil siguiente.</p>
<p style="padding-left: 30px;">8.8<strong>&nbsp;CREDITOS PANDA</strong> tiene el derecho, de negarse a conceder el Pr&eacute;stamo mediante un aviso por escrito al DEUDOR enviado al n&uacute;mero de tel&eacute;fono (texto) o al correo electr&oacute;nico proporcionado por el DEUDOR o por rechazo t&aacute;cito. Se entender&aacute; rechazo t&aacute;cito el caso en el que <strong>CREDITOS PANDA</strong> no suministre una comunicaci&oacute;n de aprobaci&oacute;n del Pr&eacute;stamo al DEUDOR dentro del plazo detallado en el apartado arriba indicado.</p>
<p style="padding-left: 30px;">8.9 El contrato se subscribe electr&oacute;nicamente o en papel y las Partes consideran que la fecha del Contrato es el d&iacute;a en que se desembolsa el Pr&eacute;stamo a la cuenta bancaria del DEUDOR.</p>
<p style="padding-left: 30px;"><strong>9.&nbsp;DESEMBOLSO</strong></p>
<p>Aprobado el cr&eacute;dito <strong>CR&Eacute;DITOS PANDA </strong>desembolsar&aacute; el pr&eacute;stamo dentro de las 24 (veinticuatro) horas h&aacute;biles siguientes directamente en la cuenta bancaria indicada por el <strong>DEUDOR </strong>en el formulario de solicitud de pr&eacute;stamo y siempre y cuanto previamente el <strong>DEUDOR </strong>haya suscrito el pagar&eacute; contemplado en el anexo 1 de este contrato. <strong>CREDITOS PANDA</strong> enviara a la direcci&oacute;n de correo electr&oacute;nico los siguientes documentos: i) t&eacute;rminos y condiciones del servicio ii) poder para suscripci&oacute;n de pagar&eacute; y carta de instrucciones y autorizaci&oacute;n de descuento de libranza iii) factura (si hay lugar a ella)</p>
<p>No obstante, el otorgamiento del cr&eacute;dito en un t&eacute;rmino mayor no podr&aacute; constituirse en un incumplimiento de las obligaciones que se encuentran a cargo de <strong>CREDITOS PANDA</strong>. La celebraci&oacute;n del contrato se perfeccionar&aacute; una vez <strong>CREDITOS PANDA</strong> autorice y efect&uacute;e la transferencia bancaria a favor del <strong>DEUDOR. </strong></p>
<p><strong>CR&Eacute;DITOS PANDA </strong>cuenta con recursos limitados y aclara que no capta dinero del p&uacute;blico, en consecuencia, sus recursos son limitados y puede incurrir en restricciones de liquidez, en cuyo caso informar&aacute; de dicha situaci&oacute;n a sus clientes.</p>
<p><strong>CR&Eacute;DITOS PANDA </strong>&nbsp;en ning&uacute;n caso responde por da&ntilde;os o perjuicios en caso de no desembolso, quedando facultado incluso para abstenerse de desembolsar el pr&eacute;stamo cuando detecte que la informaci&oacute;n aportada por el <strong>DEUDOR </strong>es fraudulenta, err&oacute;nea o existen posibles causales de no pago despu&eacute;s de la solicitud, en todo caso, la aprobaci&oacute;n de un cr&eacute;dito, no significa para <strong>CR&Eacute;DITOS PANDA</strong>, la obligaci&oacute;n de hacer al <strong>DEUDOR</strong> pr&eacute;stamos, ni de otorgarle pr&oacute;rrogas de obligaciones vencidas y que hubieren sido contra&iacute;das antes o despu&eacute;s del presente acuerdo.</p>
<p>En caso de que el <strong>DEUDOR</strong> inscriba un n&uacute;mero de cuenta de la cual no es titular, el presente contrato se dar&aacute; por inexistente y, por consiguiente, no nacer&aacute;n obligaciones entre el <strong>DEUDOR </strong>y <strong>CR&Eacute;DITOS PANDA.</strong></p>
<p style="padding-left: 30px;"><strong>10. AUTORIZACIONES Y DECLARACIONES. </strong></p>
<p style="padding-left: 30px;">10.1 El <strong>DEUDOR</strong> certifica y declara que la informaci&oacute;n suministrada es exacta, veraz y verificable comprometi&eacute;ndose a actualizarla por el tiempo que use los servicios de <strong>CR&Eacute;DITOS PANDA. </strong>Cualquier inconsistencia entre los datos entregados y la realidad, bajo la premisa que, en tal caso, el <strong>DEUDOR</strong> se podr&aacute; convertir en autor de los delitos de estafa y falsedad en documentos, conforme a lo que disponen los art&iacute;culos 246, 289 y 296 del C&oacute;digo Penal Colombiano, y dem&aacute;s normas complementarias y accesorias.</p>
<p style="padding-left: 30px;">10.2 El <strong>DEUDOR</strong> autoriza a <strong>CR&Eacute;DITOS PANDA</strong> para realizar consultas, verificaciones, reportes, divulgaci&oacute;n y procesamiento de informaci&oacute;n sobre el comportamiento financiero del <strong>DEUDOR </strong>en las centrales de riesgo autorizadas.</p>
<p style="padding-left: 30px;">10.3 El <strong>DEUDOR</strong> acepta el procesamiento de sus datos personales incluidos en el Contrato, as&iacute; como informaci&oacute;n sobre la observancia de las obligaciones en virtud de este Contrato con el fin de analizar la liquidez del <strong>DEUDOR</strong>, por un per&iacute;odo no superior al tiempo que existan obligaciones vigentes entre el DEUDOR y CREDITOS PANDA.</p>
<p style="padding-left: 30px;">10.4 El <strong>DEUDOR</strong> se comprometes a notificar a <strong>CR&Eacute;DITOS PANDA</strong>, cualquier cambio en la informaci&oacute;n suministrada en el proceso de aplicaci&oacute;n o durante la vigencia de la relaci&oacute;n entre las partes.</p>
<p style="padding-left: 30px;">10.5 En atenci&oacute;n a lo dispuesto en los art&iacute;culos 8&deg; y 13 de la Ley 1581 de 2012, El <strong>DEUDOR</strong> autoriza a LOS OPERADORES DE INFORMACI&Oacute;N PILA para que le haga entrega a <strong>CR&Eacute;DITOS PANDA </strong>o a quien haga sus veces la informaci&oacute;n relativa a sus ingresos y aportes parafiscales. En atenci&oacute;n a lo anterior, mediante el presente contrato el <strong>DEUDOR </strong>otorga poder especial, amplio y suficiente a <strong>CR&Eacute;DITOS PANDA </strong>para recibir dicha informaci&oacute;n cuantas veces esta lo requiera y mientras el presente poder no se revoque por el <strong>DEUDOR</strong> mediante comunicaci&oacute;n escrita dirigida a <strong>CR&Eacute;DITOS PANDA</strong>.</p>
<p style="padding-left: 30px;">10.6 El <strong>DEUDOR</strong> autoriza expresamente a <strong>CR&Eacute;DITOS PANDA</strong> para que lo contacte con fines comerciales y/o promocionales ya sea sobre sus propios servicios y productos, o los de terceros con los que <strong>CR&Eacute;DITOS PANDA</strong> tenga relaciones comerciales o alianzas, a trav&eacute;s de correo electr&oacute;nico, tel&eacute;fono o cualquier otro medio conocido o por conocer. Igualmente, en caso de mora, autoriza a <strong>CR&Eacute;DITOS PANDA</strong> para que contacte por llamada o mensaje de texto o v&iacute;a correo electr&oacute;nico o cualquier otro medio conocido o por conocer a las personas que ha registrado como referencias comerciales o personales para facilitar su gesti&oacute;n de cobranza. Declara el<strong> DEUDOR</strong> que tiene autorizaci&oacute;n de dichas personas para entregar a <strong>CR&Eacute;DITOS PANDA</strong> sus datos de contacto.</p>
<p style="padding-left: 30px;">10.7 El <strong>DEUDOR</strong> declara que se le ha proporcionado toda la informaci&oacute;n requerida por la ley colombiana sobre los Productos ofrecidos por <strong>CR&Eacute;DITOS PANDA.</strong></p>
<p style="padding-left: 30px;">10.8 En el caso de un retraso en el pago de las cuotas adeudadas por el <strong>DEUDOR</strong>, este autoriza a <strong>CR&Eacute;DITOS PANDA </strong>o quien haga sus veces a contactar a su empleador con el fin de informarle el incumplimiento de las obligaciones que tiene con <strong>CR&Eacute;DITOS PANDA.</strong></p>
<p style="padding-left: 30px;">10.9 El <strong>DEUDOR</strong> autoriza a <strong>CR&Eacute;DITOS PANDA</strong> y/o sus aliados, filiales o matriz, cesionarios, compradores de cartera, tanto nacionales como extranjeras, a conservar, mantener, compartir, suministrar, remitir, comercializar e intercambiar entre s&iacute; toda informaci&oacute;n sobre las condiciones personales, econ&oacute;micas y/o comerciales, y el comportamiento crediticio del</p>
<p style="padding-left: 30px;">10.10 El <strong>DEUDOR </strong>declara que el origen de los recursos que maneja y llegara a manejar en desarrollo de cualquier relaci&oacute;n con <strong>CR&Eacute;DITOS PANDA </strong>o con quien represente sus derechos, provienen de actividades licitas. As&iacute; mismo el <strong>DEUDOR</strong> declara que los recursos solicitados y objeto del contrato ser&aacute;n usados para fines l&iacute;citos y de ninguna manera para la comisi&oacute;n de cualquiera de los actos establecidos como delitos de acuerdo con la legislaci&oacute;n penal.</p>
<p style="padding-left: 30px;">10.11<strong>&nbsp;CREDITOS PANDA</strong> tiene el derecho de suministrar informaci&oacute;n sobre las obligaciones del <strong>DEUDOR</strong> a los Operadores de Informaci&oacute;n, lo que podr&iacute;a afectar la calificaci&oacute;n crediticia del <strong>DEUDOR</strong>. La transferencia de datos del DEUDOR a los Operadores de Informaci&oacute;n se hace con base a un acuerdo escrito sobre la provisi&oacute;n de informaci&oacute;n comercial entre <strong>CREDITOS PANDA</strong> y los Operadores de Informaci&oacute;n.</p>
<p style="padding-left: 30px;">10.12 La provisi&oacute;n de los datos del <strong>DEUDOR</strong> a los Operadores de Informaci&oacute;n est&aacute; sujeta a la aprobaci&oacute;n del <strong>DEUDOR</strong> expresado en este contrato y en la autorizaci&oacute;n de consulta y reporte en bases de datos</p>
<p style="padding-left: 30px;">10.13<strong>&nbsp;EL DEUDOR</strong> reconoce que conoce de las obligaciones que pueden afectar su situaci&oacute;n financiera en relaci&oacute;n con la aprobaci&oacute;n y entrada en vigencia del presente Contrato; que el inter&eacute;s dispuesto por <strong>CR&Eacute;DITOS PANDA</strong> puede ser m&aacute;s alto que el ofrecido por otras compa&ntilde;&iacute;as de cr&eacute;dito al consumo y de microcr&eacute;dito, pero en su opini&oacute;n, est&aacute;n justificados y son consistentes con las condiciones del mercado; que <strong>CR&Eacute;DITOS PANDA</strong> le ha expuesto todas las obligaciones en virtud del Contrato y le ha proporcionado toda la informaci&oacute;n y los documentos exigidos por la ley y el Contrato antes de la entrada en vigencia del Contrato; que el <strong>DEUDOR</strong> no considera que los t&eacute;rminos del Contrato sean inapropiados.</p>
<p style="padding-left: 30px;">10.14<strong>&nbsp;EL DEUDOR</strong> autoriza a <strong>CR&Eacute;DITOS PANDA</strong> evaluar mi calificaci&oacute;n crediticia a trav&eacute;s de los Operadores de Informaci&oacute;n y el Banco de la Rep&uacute;blica, el Sistema de Registro Bancario sobre los datos comerciales procesados por esas instituciones.</p>
<p style="padding-left: 30px;"><strong>11. PRODUCTOS </strong></p>
<p>En la P&aacute;gina Web se ofrecen Productos que son una combinaci&oacute;n de varias opciones, montos de pr&eacute;stamos y otros t&eacute;rminos. <strong>CREDITOS PANDA</strong> puede cambiar los t&eacute;rminos del Producto en cualquier momento, incluso agregando o eliminando opciones individuales aplicables al Producto. Estos cambios no aplicar&aacute;n a los Contratos que ya se encuentren en ejecuci&oacute;n.</p>
<p>En la p&aacute;gina web se detalla la informaci&oacute;n sobre los t&eacute;rminos de cada producto ofrecido por <strong>CREDITOS PANDA</strong>. En el caso de cambios en los t&eacute;rminos del Producto, los t&eacute;rminos actuales en la fecha de otorgamiento del Pr&eacute;stamo se aplicar&aacute;n al Producto.</p>
<p>La Fecha de Pago del Pr&eacute;stamo es la fecha que figura en el Contrato excepto cuando la fecha que surge del Contrato se haya prorrogado por una solicitud de novacion del Pr&eacute;stamo a los que se aplicar&aacute; dicha extensi&oacute;n.</p>
<p><strong>CREDITOS PANDA</strong> puede, a su criterio, dar per&iacute;odos de gracia para ciertos Pr&eacute;stamos y para ciertos Productos, as&iacute; como reembolsos para pagos oportunos de las Cuotas de Pr&eacute;stamos.</p>
<p><strong>CREDITOS PANDA</strong> tiene el derecho de ofrecer promociones con intereses y tarifas m&aacute;s bajas a los que se encuentran vigentes al momento de la firma del contrato.</p>
<p style="padding-left: 30px;"><strong>12. GARANT&Iacute;AS</strong></p>
<p>Para garantizar el cumplimiento de todas y cada una de las obligaciones contra&iacute;das, el <strong>DEUDOR</strong>, adem&aacute;s de comprometer su responsabilidad personal, y sin perjuicio de los mecanismos legales que tiene <strong>CR&Eacute;DITOS PANDA </strong>para el cobro del pr&eacute;stamo, otorga en favor de <strong>CR&Eacute;DITOS PANDA </strong>o quien haga sus veces las siguientes garant&iacute;as.</p>
<p style="padding-left: 30px;">12.1 El <strong>DEUDOR </strong>otorgar&aacute; un poder a CREDITOS PANDA para que en su nombre suscriba uno o m&aacute;s pagar&eacute;s en blanco (Anexo 1) para instrumentar sus obligaciones contra&iacute;das con <strong>CR&Eacute;DITOS PANDA</strong>.</p>
<p style="padding-left: 30px;">12.2 El DEUDOR firmar&aacute; una Carta de autorizaci&oacute;n de descuento de salarios (Anexo 2) la cual deber&aacute; ser informada al empleador, para lo cual CREDITOS PANDA se encuentra tambi&eacute;n facultado como apoderado para realizar cualquier gesti&oacute;n exigida por el empleador</p>
<p style="padding-left: 30px;"><strong>13. FORMAS DE PAGO</strong></p>
<p style="padding-left: 30px;">13.1 El pago ser&aacute; realizado por el <strong>DEUDOR </strong>de conformidad al plan de amortizaci&oacute;n dado a conocer a trav&eacute;s de la plataforma de <strong>CR&Eacute;DITOS PANDA </strong>y el pago ser&aacute; en las fechas se&ntilde;aladas en los t&eacute;rminos y condiciones del pr&eacute;stamo mediante transferencia electr&oacute;nica, consignaci&oacute;n en una de las cuentas de <strong>CR&Eacute;DITOS PANDA</strong> publicada en la p&aacute;gina web u otros medios de pago y se&ntilde;aladas a continuaci&oacute;n:</p>
<p style="padding-left: 30px;"><strong>Banco:</strong> Banco de las Microfinanzas Bancamia.</p>
<p style="padding-left: 30px;"><strong>Tipo de cuenta: </strong>Ahorros</p>
<p style="padding-left: 30px;"><strong>A nombre de:</strong> Creditos Panda SAS</p>
<p style="padding-left: 30px;"><strong>NIT:</strong> 901.369.753-0</p>
<p style="padding-left: 30px;"><strong>Numero de cuenta: </strong>2110065594200011</p>
<p style="padding-left: 30px;">13.2 Todas las transferencias se consideran exitosas una vez que la transferencia se refleja en la cuenta bancaria o pasarela de pagos de <strong>CR&Eacute;DITOS PANDA, </strong>si <strong>CR&Eacute;DITOS PANDA </strong>encuentra que el pago realizado no es identificable, el pago no se considerar&aacute; v&aacute;lido hasta que sea identificado y el <strong>DEUDOR</strong> ser&aacute; responsable de todas las consecuencias del pago atrasado.</p>
<p style="padding-left: 30px;">13.3 Entiende el <strong>DEUDOR </strong>que todo pago que haga se aplicar&aacute; seg&uacute;n la siguiente prelaci&oacute;n:</p>
<ul>
<li>IVA</li>
<li>Intereses de mora pendientes de pago.</li>
<li>Gasto de cobranza si se generan</li>
<li>Tarifa uso plataforma si aplica</li>
<li>Tarifa aprobaci&oacute;n R&aacute;pida si aplica</li>
<li>Inter&eacute;s remuneratorio.</li>
<li>Capital del pr&eacute;stamo.</li>
</ul>
<p style="padding-left: 30px;">13.4<strong> Autorizaci&oacute;n d&eacute;bito autom&aacute;tico y control de cuenta bancaria: </strong>Mediante la aceptaci&oacute;n del presente documento el <strong>DEUDOR </strong>de forma irrevocable autoriza a <strong>CR&Eacute;DITOS PANDA </strong>para que a partir del primer d&iacute;a de mora en el pr&eacute;stamo puede este o quien lo represente realizar el d&eacute;bito autom&aacute;tico de la totalidad o parte del monto adeudado por los conceptos descrito en el punto 10.3 de este contrato, de la cuenta d&eacute;bito o tarjeta de cr&eacute;dito reportada por el <strong>DEUDOR </strong>al momento de la solicitud del pr&eacute;stamo y/o cualquier otra cuenta bancaria que llegue a conocer <strong>CR&Eacute;DITOS PANDA </strong>ya sea al momento de la aceptaci&oacute;n de las condiciones de utilizaci&oacute;n de los productos o servicios que ofrece <strong>CR&Eacute;DITOS PANDA </strong>o con posterioridad a trav&eacute;s de la plataforma tecnol&oacute;gica de <strong>CR&Eacute;DITOS PANDA </strong>o de correo electr&oacute;nico u otro medio de informaci&oacute;n.</p>
<p><strong>Cuenta inicial reportada por el</strong> <strong>DEUDOR {{$financiera->banco}} Nro. {{$financiera->nCuenta}}</strong></p>
<p>En caso de no poder realizar el debito autom&aacute;tico por falta de fondos, o en el caso de haber debitado &uacute;nicamente un pago parcial por fondos insuficientes que no alcancen a cubrir el total de la obligaci&oacute;n, el <strong>DEUDOR </strong>autoriza a <strong>CR&Eacute;DITOS PANDA </strong>a seguir intentando d&eacute;bitos autom&aacute;ticos a la cuenta, cuentas o tarjetas de cr&eacute;dito de las cuales el <strong>DEUDOR </strong>sea titular hasta el pago total de las obligaciones generadas a trav&eacute;s de este contrato.</p>
<p>En los t&eacute;rminos de la ley 1676 de 2013, el DEUDOR manifiesta que otorga una garant&iacute;a mobiliaria a favor de CREDITOS PANDA, consistente en el control de sus cuentas bancarias como se define en los art&iacute;culo 8&deg;, 33 y 34 de la referida ley, de forma que CREDITOS PANDA, ante el incumplimiento de cualquiera de las obligaciones a cargo del DEUDOR, podr&aacute; acudir ante cualquier establecimiento de cr&eacute;dito en que tenga cuentas de ahorro o corrientes para solicitar el control de la misma, bastando para ello la presentaci&oacute;n de estos t&eacute;rminos y condiciones y mi aceptaci&oacute;n electr&oacute;nica mediante mensajes de datos.</p>
<p>Con mi aceptaci&oacute;n electr&oacute;nica, otorgo poder a CREDITOS PANDA para que adelante ante cualquier establecimiento bancario, el registro de este acuerdo de control y pueda disponer de los saldos all&iacute; depositados hasta el monto total que le sea adeudado por mi parte por cualquier concepto (capital, intereses remuneratorios o moratorios, tarifas por servicios adicionales y gastos de cobranza entre otros). CREDITOS PANDA estar&aacute; autorizado para registrar este acuerdo de control ante el registro de garant&iacute;as mobiliarias, indicando all&iacute; como valor m&aacute;ximo de la garant&iacute;a mobiliaria el 120% del capital que me fue desembolsado.</p>
<p>Si por cualquier motivo los establecimientos de cr&eacute;dito solicitaran documentaci&oacute;n adicional o no aceptaran este acuerdo de control, CREDITOS PANDA estar&aacute; autorizado para solicitar mediante el mecanismo de ejecuci&oacute;n especial ante la C&aacute;mara de Comercio de Bogot&aacute; D.C., o cualquier Notar&iacute;a, para que estas autoridades conforme al procedimiento dispuesto en la ley 1676 de 2013 y sus decretos reglamentarios autoricen la apropiaci&oacute;n de los recursos depositados en las cuentas bancarias de mi titularidad. CREDITOS PANDA podr&aacute; consultar ante centrales de informaci&oacute;n y riesgo financiero (CIFIN y Datacr&eacute;dito u otras similares) la existencia de cuentas de ahorro y corrientes de mi titularidad para dar aplicaci&oacute;n a esta garant&iacute;a mobiliaria.</p>
<p style="padding-left: 30px;"><strong>14. PREPAGO DEL PRESTAMO</strong></p>
<p>El <strong>DEUDOR</strong> tiene derecho a realizar el pago anticipado total o parcial del pr&eacute;stamo adquirido conforme a este contrato, en consecuencia, el <strong>DEUDOR </strong>no estar&aacute; obligado a pagar inter&eacute;s que no se hayan causado al momento del pago, as&iacute; mismo, no se impondr&aacute;n ning&uacute;n tipo de sanciones debido al pago anticipado. Cabe aclarar que el <strong>DEUDOR </strong>en el caso de pago total o parcial del pr&eacute;stamo deber&aacute; pagar el monto del pr&eacute;stamo, junto con los inter&eacute;s y tarifas causados, pero no pagados a la fecha de pago del pr&eacute;stamo.</p>
<p>El DEUDOR se deber&aacute; comunicar con <strong>CREDITOS PANDA</strong> para que este le indique el monto exacto a pagar antes de la fecha de vencimiento del pr&eacute;stamo. <strong>CREDITOS PANDA </strong>no se responsabiliza en caso de que el <strong>DEUDOR </strong>pague un valor distinto por el concepto de pago por adelantado.</p>
<p style="padding-left: 30px;"><strong>15. GASTOS DE COBRANZA</strong></p>
<p>El<strong> DEUDOR </strong>entiende y acepta con la firma de este contrato de mutuo que pagar&aacute; a favor de <strong>CR&Eacute;DITOS PANDA </strong>los gastos de cobranza que se generen por incumplimiento de sus obligaciones de pago hasta un m&aacute;ximo del 30% del monto del total de las sumas debidas m&aacute;s los impuestos e intereses de mora generados por este concepto.</p>
<p>Los gastos de cobranza ocasionados por la gesti&oacute;n de cobranza prejudicial y/o judicial, ser&aacute;n liquidados siempre sobre el valor de los recaudos efectivos, y cobrados en el momento de realizarse el pago por EL <strong>DEUDOR</strong>, y estar&aacute; determinados por el m&eacute;todo de cobranza que se realice siempre que estos costos est&eacute;n debidamente soportados por los documentos de cobro que evidencien que la actividad fue desarrollada.</p>
<p><strong>EL PRESTATATRIO</strong> acepta adem&aacute;s que, todas las llamadas llevadas a cabo dentro del proceso de cobranza ser&aacute;n grabadas y las mismas ser&aacute;n conservadas junto con los registros que contengan la informaci&oacute;n de la persona que realiza el cobro, la fecha, hora, lugar del contacto y un resumen de lo hablado, que podr&aacute;n ser usadas como prueba de los compromisos adquiridos y de la calidad de la llamada dentro de los est&aacute;ndares sugeridos por el regulador nacional, para los procesos de cobranza extrajudicial. Estos registros deben conservarse por el tiempo m&iacute;nimo establecido en la ley. Los acuerdos de pago pactados dentro del proceso de cobranza prejur&iacute;dica, podr&aacute;n ser incorporados en una constancia del acuerdo de pago a trav&eacute;s de medio verificable legalmente, en el que consten las condiciones de dicho acuerdo.</p>
<p>Los gastos de cobranza ocasionados por la gesti&oacute;n de cobranza prejudicial y/o judicial, ser&aacute;n liquidados siempre sobre el valor de los recaudos efectivos, y cobrados en el momento de realizarse el pago por EL DEUDOR.</p>
<p style="padding-left: 30px;"><strong>16. USO DE INFORMACI&Oacute;N</strong></p>
<p>El <strong>DEUDOR</strong> faculta de manera expresa a <strong>CR&Eacute;DITOS PANDA</strong> para que los datos personales, documentaci&oacute;n, condiciones de oferta, contenido contractual, condiciones de ejecuci&oacute;n del contrato y en general cualquier informaci&oacute;n obtenida en virtud de la relaci&oacute;n comercial establecida con anterioridad, al mismo tiempo o con posterioridad a este contrato, as&iacute; como aquella que en lo sucesivo sea suministrada, pueda ser objeto de tratamiento, sistematizaci&oacute;n y/o sea compartida por parte del <strong>CR&Eacute;DITOS PANDA</strong> con sus filiales, aliados comerciales y las entidades que pertenecen o llegaren a tener relaciones comerciales con<strong> CR&Eacute;DITOS PANDA</strong> para efectos de que la misma sirva de soporte para la estructuraci&oacute;n de posibles relaciones comerciales y/o de servicios con dichas entidades. La informaci&oacute;n igualmente podr&aacute; ser utilizada para los fines propios de la ejecuci&oacute;n de este contrato, para fines estad&iacute;sticos, tributarios y de registros comerciales, corporativos y contables de<strong> CR&Eacute;DITOS PANDA</strong> y dem&aacute;s tratamientos contemplados en la pol&iacute;tica de protecci&oacute;n de datos de<strong> CR&Eacute;DITOS PANDA </strong>que se encuentra publicada en la p&aacute;gina web <a href="http://www.creditopanda.com">www.creditopanda.com</a> y que el El <strong>DEUDOR</strong> declara conocer. La presente autorizaci&oacute;n permanecer&aacute; vigente mientras est&eacute; vigente cualquier relaci&oacute;n contractual entre el DEUDOR y CR&Eacute;DITOS PANDA</p>
<p style="padding-left: 30px;"><strong>17. DERECHOS Y OBLIGACIONES DE </strong><strong>CR&Eacute;DITOS PANDA</strong></p>
<p>En virtud del presente contrato, <strong>CR&Eacute;DITOS PANDA</strong> se obliga para con el<strong> DEUDOR</strong> a</p>
<ol style="list-style-type: lower-alpha;">
<li>Suministrar al <strong>DEUDOR</strong> informaci&oacute;n precontractual y contractual relacionada con el Producto o Pr&eacute;stamo.</li>
<li>Desembolsar el Pr&eacute;stamo al <strong>DEUDOR</strong> en la cantidad, en la fecha y en los t&eacute;rminos acordados por las Partes de conformidad a los t&eacute;rminos y condiciones generales de solicitud de cr&eacute;dito.</li>
<li>Registrar de manera eficaz y precisa el pago de las Cuotas de Pr&eacute;stamo por parte del</li>
<li>Facilitar al <strong>DEUDOR</strong>, en cualquier momento durante la vigencia del Contrato, informaci&oacute;n completa y precisa sobre las Cuotas pagadas hasta la fecha.</li>
<li>Proveer al <strong>DEUDOR</strong> todos los documentos necesarios relacionados al servicio del Pr&eacute;stamo.</li>
</ol>
<p>En virtud del presente contrato, <strong>CR&Eacute;DITOS PANDA</strong> tiene derecho a:</p>
<ol style="list-style-type: lower-alpha;">
<li>Al cumplimiento absoluto de todas las obligaciones del <strong>DEUDOR</strong> en virtud del Contrato, y conceder o negar la solicitud del Pr&eacute;stamo si considera que se cumplen las condiciones para conceder o negar el mismo.</li>
<li>Ser informado directamente por el <strong>DEUDOR</strong> de las situaciones que puedan causar una demora o deterioro de la situaci&oacute;n financiera general del <strong>DEUDOR</strong>.</li>
<li>Solicitar que el <strong>DEUDOR</strong> suministre documentos o copias de los documentos necesarios para conceder el Pr&eacute;stamo, para evaluar su capacidad crediticia y para facilitar informaci&oacute;n sobre el servicio de Pr&eacute;stamo.</li>
<li>Ceder cuentas por cobrar, incluidos los montos no pagados y pagaderos, originarios del Contrato a terceros sin la aprobaci&oacute;n del <strong>DEUDOR</strong>. Se notificar&aacute; al <strong>DEUDOR</strong> de la cesi&oacute;n con el prop&oacute;sito de que realice los pagos al tercero cesionario;</li>
<li>Enviar, incluso a trav&eacute;s de terceros, toda la correspondencia para el <strong>DEUDOR</strong>, realizar llamadas telef&oacute;nicas, enviar correos electr&oacute;nicos, cartas, si el <strong>DEUDOR</strong> no cumple con alguna de las obligaciones pactadas en el Contrato y, por lo tanto, avisarle del incumplimiento, en cualquier caso, cumpliendo con la ley aplicable.</li>
</ol>
<p style="padding-left: 30px;"><strong>18. DERECHOS Y OBLIGACIONES DEL </strong><strong>DEUDOR</strong></p>
<p><strong>El DEUDOR </strong>en virtud del presente contrato, se obliga para con<strong> CR&Eacute;DITOS PANDA</strong> a:</p>
<ol style="list-style-type: lower-alpha;">
<li>Pagar todas las sumas debidas bajo el Pr&eacute;stamo, incluidos el monto principal, Intereses, servicios adicionales, gastos de cobranza y en general cualquier obligaci&oacute;n que se desprenda del pr&eacute;stamo otorgado.</li>
<li>Cumplir con las condiciones y fechas de pago para las Cuotas del pr&eacute;stamo contempladas en los t&eacute;rminos y condiciones del pr&eacute;stamo.</li>
<li>Notificar de inmediato a <strong>CR&Eacute;DITOS PANDA</strong> de cualquier cambio de situaci&oacute;n que cause demoras en el pago o que pueda da&ntilde;ar la situaci&oacute;n financiera del <strong>DEUDOR</strong> y cualquier cambio en la informaci&oacute;n dada en la Solicitud del pr&eacute;stamo, incluidos los cambios en los ingresos mensuales, la direcci&oacute;n de residencia y postal, lugar de trabajo, n&uacute;mero de tel&eacute;fono, procedimientos de ejecuci&oacute;n contra el <strong>DEUDOR</strong> o su c&oacute;nyuge, sanciones impuestas al <strong>DEUDOR</strong> o a su c&oacute;nyuge en procedimientos administrativos o penales, procedimientos para incautar activos que son propiedad del <strong>DEUDOR</strong> o de su c&oacute;nyuge y en general cualquier situaci&oacute;n que pueda afectar el pago oportuno de la obligaci&oacute;n contenida en este contrato.</li>
<li>Pagar todos los costos respectivos con el incumplimiento por parte del <strong>DEUDOR </strong>de las obligaciones establecidas en el Contrato incluidos, entre otros, los cargos por cobro extra-judicial o judicial del pr&eacute;stamo.</li>
</ol>
<p>En virtud del presente contrato, el <strong>DEUDOR </strong>tiene derecho a:</p>
<ol style="list-style-type: lower-alpha;">
<li>Realizar el pago parcial o total del pr&eacute;stamo antes de la fecha de pago. En caso de pago total del pr&eacute;stamo, el costo total del Pr&eacute;stamo ser&aacute; la suma del dinero prestado, m&aacute;s intereses causados hasta el momento del pago del Pr&eacute;stamo. El <strong>DEUDOR </strong>est&aacute; obligado a pagar la tarifa por el uso de la plataforma desde el momento de la concesi&oacute;n del pr&eacute;stamo y hasta el d&iacute;a del pago anticipado, entendiendo igualmente que deber&aacute; cancelar el total adeudado por el servicio de aprobaci&oacute;n R&aacute;pida en caso de que aplique dicho cobro.</li>
<li>Si el <strong>DEUDOR</strong> no ha tenido retrasos en los pagos y no ha incumplido las obligaciones en virtud del Contrato, el <strong>DEUDOR</strong> puede recibir un descuento al momento del prepago total de sus obligaciones. El descuento lo establecer&aacute; <strong>CREDITOS PANDA</strong> a su entera discreci&oacute;n.</li>
<li>C&oacute;digos de promoci&oacute;n: Estos c&oacute;digos de promoci&oacute;n incluir&aacute;n un c&oacute;digo que se usar&aacute; en el sitio web de <strong>CREDITOS PANDA</strong> para recibir un descuento sobre una o varias tarifas / intereses del Pr&eacute;stamo.</li>
</ol>
<p style="padding-left: 30px;"><strong>19. ACELERACI&Oacute;N DEL PR&Eacute;STAMO</strong></p>
<p><strong>CR&Eacute;DITOS PANDA</strong> podr&aacute; declarar insubsistente los plazos de pago contemplados en los t&eacute;rminos y condiciones del pr&eacute;stamo y exigir el pago inmediato de la totalidad del cr&eacute;dito, judicial y/o extrajudicialmente sobre la base de un aviso unilateral de <strong>CR&Eacute;DITOS PANDA</strong> al <strong>DEUDOR</strong> en los siguientes casos:</p>
<ul>
<li>Por incumplimiento de cualquiera de las obligaciones por parte del deudor</li>
<li>La mora o el simple retardo en uno de los pagos de las cuotas pactadas.</li>
<li>Si los bienes del deudor son embargados o perseguidos judicialmente por cualquier persona y en ejercicio de cualquier acci&oacute;n.</li>
<li>Si el (los) deudor(es) es (son) declarados en liquidaci&oacute;n forzosa, de car&aacute;cter comercial o administrativo admitidos a proceso concordatario o se adelanta un proceso de insolvencia de persona natural no comerciante.</li>
<li>Si las garant&iacute;as que se hayan otorgado para cubrir el cumplimiento de las obligaciones a cargo de los deudores y a favor del acreedor, resultaran insuficientes o se tornaren as&iacute; por deterioro o depreciaci&oacute;n, a juicio del acreedor, o si fueren perseguidas judicialmente por terceras personas.</li>
<li>Si el (los) deudor(es) dejaren de asegurar los bienes que fungen como garant&iacute;a a las obligaciones.</li>
<li>Si el Deudor fuere vinculado con actividades delictivas en Colombia o en el exterior, o incluido en listas restrictivas para la prevenci&oacute;n del lavado de activos y financiaci&oacute;n del terrorismo, sin necesidad de condena o decisi&oacute;n administrativa en firme.</li>
<li>Cuando se evidencie por cualquier medio que EL DEUDOR ha aportado informaci&oacute;n falsa o imprecisa a CREDITOS PANDA S.A.S, para el otorgamiento de los cr&eacute;ditos a mi favor.</li>
<li>Si el (los) deudor(es) enajenaren, a cualquier t&iacute;tulo y sin autorizaci&oacute;n del acreedor, los bienes que garantizan el cumplimiento de las obligaciones que por &eacute;ste pagar&eacute; asumen.</li>
</ul>
<p>El Pr&eacute;stamo se declara pagadero anticipadamente mediante una declaraci&oacute;n de <strong>CR&Eacute;DITOS PANDA</strong>, enviada al n&uacute;mero de tel&eacute;fono proporcionado (mensaje de texto) o al correo electr&oacute;nico dado en la Solicitud del <strong>DEUDOR.</strong></p>
<p>En caso de un retraso en el pago o la aceleraci&oacute;n del Pr&eacute;stamo, <strong>CREDITOS PANDA</strong> enviar&aacute; recordatorios al <strong>DEUDOR</strong> por correos electr&oacute;nicos, mensajes de texto, n&uacute;mero de tel&eacute;fono m&oacute;vil, direcci&oacute;n postal y as&iacute; como a la direcci&oacute;n de empleo del <strong>DEUDOR,</strong> conocida por <strong>CREDITOS PANDA</strong>, y en general por cualquier otro medio de comunicaci&oacute;n en cada caso cumpliendo con la ley aplicable.</p>
<p>Al firmar el Contrato, el <strong>DEUDOR</strong> autoriza la visita de un empleado de <strong>CREDITOS PANDA</strong> o quien haga sus veces a su lugar de trabajo o residencia del <strong>DEUDOR</strong> al igual que el env&iacute;o de mensajes de texto al n&uacute;mero de contacto, en caso de incumplimiento de las obligaciones que nazcan en conexi&oacute;n con el Contrato, en los t&eacute;rminos permitidos por la ley. En el caso de un retraso en el pago de las Cuotas, y siempre que el DEUDOR lo haya autorizado, <strong>CREDITOS PANDA</strong>, o a quien <strong>CREDITOS PANDA</strong> destine, podr&aacute; contactar al empleador del <strong>DEUDOR</strong> con el fin de avisar el incumplimiento de las obligaciones de &eacute;ste, a trav&eacute;s de llamadas por tel&eacute;fono o documentos escritos, dentro del horario h&aacute;bil, y &uacute;nicamente se informar&aacute;n los d&iacute;as de mora de la obligaci&oacute;n y el saldo debido por el <strong>DEUDOR</strong>.</p>
<p>El <strong>DEUDOR</strong> pagar&aacute; los Intereses por el pago tard&iacute;o (del capital y los intereses) desde la fecha del pago acelerado hasta el pago total de la deuda. En el caso del pago acelerado, el<strong> DEUDOR</strong> pagar&aacute; a <strong>CR&Eacute;DITOS PANDA</strong> todos los costos asociados al pr&eacute;stamo incluyendo los costos de cobranza generados en la recuperaci&oacute;n de la deuda. Las Partes admiten incondicionalmente que todos los costos extrajudiciales o judiciales afines con la recuperaci&oacute;n de deudas (incluidas visitas, llamadas telef&oacute;nicas, aranceles judiciales y otros costos) correr&aacute;n a cargo del <strong>DEUDOR.</strong></p>
<p style="padding-left: 30px;"><strong>20. TERMINACI&Oacute;N ANTICIPADA</strong></p>
<p>Ser&aacute;n justas causas para dar por terminado el contrato, las siguientes:</p>
<ol style="list-style-type: lower-alpha;">
<li>El prepago total del DEUDOR de las obligaciones que est&eacute;n vigentes con CR&Eacute;DITOS PANDA</li>
<li>Por parte de <strong>CR&Eacute;DITOS PANDA </strong>de forma inmediata si evidencia que el <strong>DEUDOR </strong>aporto y/o declaro informaci&oacute;n falsa, inexacta o enga&ntilde;osa.</li>
<li>Por parte de <strong>CR&Eacute;DITOS PANDA </strong>de forma inmediata si existe un retraso de m&aacute;s de 30 d&iacute;as por parte del <strong>DEUDOR</strong> en el pago de cualquiera de sus obligaciones a cargo en virtud de este contrato.</li>
<li>Por parte de <strong>CR&Eacute;DITOS PANDA </strong>de forma inmediata cuando este no desembolse el pr&eacute;stamo de conformidad a los t&eacute;rminos y condiciones generales de solicitud del pr&eacute;stamo.</li>
<li>Por parte de <strong>CR&Eacute;DITOS PANDA </strong>de forma inmediata cuando evidencie que una transacci&oacute;n relacionada con el contrato puede o est&aacute; relacionada con cualquier actividad conexa al lavado de activos o la financiaci&oacute;n del terrorismo de conformidad con la ley aplicable.</li>
</ol>
<p style="padding-left: 30px;"><strong>21. SARLAFT</strong></p>
<p>Las Partes declaran que sus negocios y los recursos utilizados para la ejecuci&oacute;n y pago de los Servicios contratados, no provienen ni se destinar&aacute;n al ejercicio de ninguna actividad il&iacute;cita, lavado de activos o financiaci&oacute;n del terrorismo. Asimismo, se comprometen a entregar toda la informaci&oacute;n que les sea solicitada para dar cumplimiento a las disposiciones relacionadas con la prevenci&oacute;n del lavado de activos y financiaci&oacute;n del terrorismo y declaran que la misma es veraz y verificable. Las Partes se obligan a realizar todas las actividades encaminadas a asegurar que todos sus socios, empleados, contratistas, administradores, clientes, proveedores y los recursos de &eacute;stos, no se encuentren relacionados o provengan de actividades il&iacute;citas; En todo caso, si durante el plazo de vigencia del presente contrato las Partes o alguno de sus socios, administradores, clientes, proveedores, contratistas o empleados llegar&aacute;n a resultar inmiscuidos en una investigaci&oacute;n de cualquier tipo como penal, administrativa, o de cualquier otra &iacute;ndole, relacionada con actividades il&iacute;citas, lavado de activos o financiaci&oacute;n del terrorismo, o fuesen incluidos en listas de control como las de la ONU, OFAC, etc., cualquiera de las Partes tiene derecho a terminar unilateralmente la relaci&oacute;n entre las Partes.</p>
<p style="padding-left: 30px;"><strong>22. EVENTOS DE FUERZA MAYOR</strong></p>
<p><strong>CR&Eacute;DITOS PANDA</strong> no es responsable ante el <strong>DEUDOR</strong> por ning&uacute;n retraso o incumplimiento, causado por fuerza mayor.</p>
<p>Fuerza mayor es cualquier evento imprevisto que suceda despu&eacute;s de firmar el contrato entre <strong>CR&Eacute;DITOS PANDA</strong> y el <strong>DEUDOR</strong>, y hace improbable que <strong>CR&Eacute;DITOS PANDA</strong> o el<strong> DEUDOR</strong> cumplan con sus obligaciones bajo este contrato, incluidas fallas t&eacute;cnicas en el software o hardware utilizado por <strong>CR&Eacute;DITOS PANDA</strong>, restricciones legales, administrativas o gubernamentales; desastres naturales; disturbios, levantamientos, confrontaciones, guerras, actos de terrorismo, terremotos u otras acciones destructivas de las fuerzas de la naturaleza; huelgas a nivel nacional. No obstante, la existencia de caso fortuito o fuerza mayor no ser&aacute;n motivo para que la parte afectada no pague oportunamente las cantidades o cumpla con las obligaciones consignadas en este contrato, previas a la aparici&oacute;n del caso fortuito o fuerza mayor.</p>
<p>En caso de fuerza mayor, <strong>CR&Eacute;DITOS PANDA</strong> y el <strong>DEUDOR</strong> tomar&aacute;n todos los pasos y medidas razonables para mitigar posibles p&eacute;rdidas y da&ntilde;os, y comunicar&aacute;n a la otra parte del contrato dentro de los 7 d&iacute;as siguientes despu&eacute;s de la ocurrencia de un evento de fuerza mayor.</p>
<p>La falta de fondos del <strong>DEUDOR</strong> o su disminuci&oacute;n en su capacidad de pago o la existencia de pandemias, p&eacute;rdida de empleo como consecuencia de las mismas o de decisiones de terceros o eventos que disminuyan la capacidad f&iacute;sica o mental del DEUDOR, no se considerar&aacute;n un evento de fuerza mayor.</p>
<p style="padding-left: 30px;"><strong>23. PODER IRREVOCABLE PARA SUSCRIBIR AUTORIZACIONES DE DESCUENTO, PAGARES Y CARTA DE INSTRUCCIONES. </strong></p>
<p>Mediante el presente aparte el <strong>DEUDOR </strong>otorga un mandato con representaci&oacute;n (poder especial e irrevocable) a favor de <strong>CR&Eacute;DITOS PANDA</strong> o quien represente sus inter&eacute;s para suscribir y firmar en nombre del<strong> DEUDOR </strong>uno o varios pagar&eacute;s mientras existan las obligaciones derivadas de este contrato, y para firmar en nombre del <strong>DEUDOR </strong>una o varias autorizaciones de descuento con el empleador presente o futuro del <strong>DEUDOR, </strong>su fondo de pensiones, o cuenta d&eacute;bito o cr&eacute;dito con su entidad financiera que garanticen el pago de las obligaciones adquiridas por el <strong>DEUDOR</strong> a trav&eacute;s de este contrato.</p>
<p style="padding-left: 30px;"><strong>24. OTRAS DISPOSICIONES</strong></p>
<p>El presente es un contrato a distancia generado conforme a la ley 527 de 1999 que regula los mensajes de datos, la firma digital y el comercio electr&oacute;nico en Colombia, en consecuencia, el <strong>DEUDOR </strong>acepta que a trav&eacute;s de los mecanismos electr&oacute;nicos y digitales utilizados en la plataforma de <strong>CR&Eacute;DITOS PANDA</strong>, suscribir&aacute; este contrato y por tanto no se puede oponerse a lo aqu&iacute; estipulado una vez lo acepte.</p>
<p>Al aceptar los t&eacute;rminos y condiciones del presente documento el <strong>DEUDOR </strong>declara que acepta de manera expresa e inequ&iacute;voca los t&eacute;rminos y condiciones del pr&eacute;stamo y dem&aacute;s condiciones previstas en este acuerdo. Por tanto, declara haber le&iacute;do, entendido y aceptado todo lo anterior.</p>
<p>Entienden las partes que el presente contrato es ley para las partes de conformidad con el art&iacute;culo 1602 del C&oacute;digo Civil, en consecuencia, declaran que las disposiciones del contrato y los t&eacute;rminos del pr&eacute;stamo solo pueden cambiarse con la aprobaci&oacute;n mutuo de las Partes, indicadas por escrito o en otro Medio duradero.</p>
<p>Con la suscripci&oacute;n de este documento, el <strong>DEUDOR </strong>acepta la cesi&oacute;n de la condici&oacute;n de acreedor de la obligaci&oacute;n que haga <strong>CR&Eacute;DITOS PANDA</strong> a favor de un tercero, de tal manera que la cesi&oacute;n se entender&aacute; que es oponible por el solo hecho de la notificaci&oacute;n que se realice por parte de <strong>CR&Eacute;DITOS PANDA</strong> o el cesionario por cualquiera de los medios de contacto que se indican en</p>
<p>el presente documento. El <strong>DEUDOR </strong>aceptas que dicha cesi&oacute;n conlleva compartir datos personales y crediticios seg&uacute;n se contempla en el punto 3.9 de este documento.</p>
<p>Todos los impuestos y derechos que se generen con ocasi&oacute;n de la celebraci&oacute;n del presente contrato ser&aacute;n de cargo exclusivo de EL <strong>DEUDOR</strong>.</p>
<p><strong>CREDITOS PANDA</strong> modificara el Contrato de acuerdo con las reformas o adopciones de nuevas leyes o instrucciones de las autoridades estatales, pertinentes a el Contrato. <strong>CREDITOS PANDA</strong> comunicar&aacute; al <strong>DEUDOR</strong> sobre los cambios en las disposiciones del Contrato antes de su fecha de vigencia enviando un correo electr&oacute;nico al correo del <strong>DEUDOR</strong> junto con el Contrato modificados en formato. <strong>CREDITOS PANDA</strong> indicar&aacute; las disposiciones modificadas y su nueva redacci&oacute;n, as&iacute; como la fecha de vigencia del Contrato enmendado. Una modificaci&oacute;n del Contrato ser&aacute; vinculante para el <strong>DEUDOR</strong> a menos que el <strong>DEUDOR</strong> finalice el Contrato en los t&eacute;rminos establecidos en el Contrato dentro de los 14 (catorce) d&iacute;as posteriores a la recepci&oacute;n de la notificaci&oacute;n de modificaci&oacute;n mencionada anteriormente.</p>
<p>Cualquier disputa que surja de o est&eacute; relacionada con el Contrato o cualquier reclamaci&oacute;n contra <strong>CREDITOS PANDA</strong> ser&aacute; resuelta de conformidad con la ley aplicable. En asuntos no regulados en este Contrato, se aplicar&aacute; la Ley Comercial.</p>
<p>Si alguna de las disposiciones del Contrato o su parte se considera inv&aacute;lida o inejecutable, las disposiciones restantes de este Contrato permanecer&aacute;n en plena vigencia y efecto.</p>
<p>El servicio de <strong>CREDITOS PANDA</strong> tiene costos asociados. <strong>CREDITOS PANDA</strong> reporta a las centrales de riesgo tu buen comportamiento crediticio, lo que te ayuda a tener una buena calificaci&oacute;n crediticia. Los incumplimientos en los pagos podr&iacute;an afectar tu historia crediticia. Tu atraso genera intereses de mora, gastos de cobranza y reporte en las centrales de riesgo.</p>
<p style="padding-left: 30px;"><strong>25. ENV&Iacute;O DE COMUNICACIONES</strong></p>
<p><strong>CR&Eacute;DITOS PANDA</strong> recibir&aacute; notificaciones en las siguientes direcciones</p>
<p>Direcci&oacute;n: Carrera 49B. No. 93-38</p>
<p>CEL: 3212403734</p>
<p>Direcci&oacute;n de Correo Electr&oacute;nico: <a href="mailto:info@creditospanda.com">info@creditospanda.com</a></p>
<p>Ciudad: Bogot&aacute;</p>
<p>El Deudor por su parte, recibir&aacute; notificaciones en las direcciones de contacto que para tal efecto haya informado a CR&Eacute;DITOS PANDA en su solicitud de desembolso.</p>
<p>La parte que cambiare su direcci&oacute;n, correo o n&uacute;mero de fax para la recepci&oacute;n de las comunicaciones que deban dirigirse en raz&oacute;n del presente acuerdo, deber&aacute; dar aviso por escrito a la otra PARTE con una antelaci&oacute;n no inferior a cinco (5) d&iacute;as h&aacute;biles.</p>
<p>El deudor con su firma electr&oacute;nica y aceptaci&oacute;n en la solicitud de cr&eacute;dito y desembolso, declara haber le&iacute;do y aceptar estos t&eacute;rminos y condiciones.</p>
<table border="1">
<tbody>
<tr>
<td width="250">
<p><strong>CR&Eacute;DITOS PANDA</strong></p>
<p>Nombre: CREDITOS PANDA SAS</p>
<p>NIT. 901.369.753-0</p>
</td>
<td width="250">
<p><strong>DEUDOR</strong></p>
<p>Nombre: {{$usuario->first_name}} {{$usuario->last_name}}</p>
<p>C.C. {{$usuario->n_document}}</p>
<p>Fecha y Hora: {{$fecha_actual}}</p>
@if($ip != "" && $codigo_sms != "")
<p>IP desde donde te conectas: {{$ip}}</p>
<p>C&oacute;digo enviado por Email: {{$codigo_sms}}</p>
<p>C&oacute;digo enviado por SMS: {{$codigo_sms}}</p>
@endif
</td>
</tr>
</tbody>
</table>
</body>
</html>

