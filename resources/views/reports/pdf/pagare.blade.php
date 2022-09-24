<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>        
        table, th, td {
          border: 1px solid black;
        }
    </style>
</head>
<body>
<p style="text-align: center;">&nbsp;<strong>PODER ESPECIAL PARA FIRMA DE PAGAR&Eacute;S</strong></p>
<p>En mi calidad de DEUDOR, manifiesto que otorgo poder especial irrevocable a favor de CREDITOS PANDA SAS, sociedad identificada con Nit.901.369.753-0, o cualquiera de sus cesionarios a cualquier t&iacute;tulo, para suscribir y firmar en mi nombre y representaci&oacute;n uno o varios pagar&eacute;s y sus cartas de instrucciones cuando sean necesarias, mientras est&eacute;n vigentes obligaciones adquiridas por mi cuenta y a favor de CREDITOS PANDA SAS.</p>
<p>Como poderdante, manifiesto que no existe conflicto de inter&eacute;s ni impedimento de ninguna clase para que CREDITOS PANDA S.A.S, como apoderado, diligencie a su favor los pagar&eacute;s que instrumenten mis obligaciones crediticias con ellos.</p>
<p>El poder que por este documento electr&oacute;nico otorgo a CREDITOS PANDA SAS, le faculta para diligenciar en mi nombre uno o m&aacute;s pagar&eacute;s, a su elecci&oacute;n, en los siguientes t&eacute;rminos:</p>
<p>&nbsp;</p>
<p><strong>Pagar&eacute; No</strong><strong>: {{$solicitud->numero_credito}}</strong></p>
<p><strong>VALOR</strong><strong>: ${{$contra_oferta ? number_format($contra_oferta->totalPagar) : number_format($solicitud->totalPagar)}} PESOS M/CTE </strong></p>
<p><strong>PLAZO DEL CREDITO:</strong><strong> {{$contra_oferta ? $contra_oferta->plazo : $solicitud->plazo}} @if($solicitud->tipoCredito=='m')Meses 
    @elseif($solicitud->tipoCredito=='d')Dias
    @endif</strong></p>
<p>&nbsp;</p>
<p>EL DEUDOR <strong><u>_______________________</u> </strong>domiciliado en la ciudad de _____________, identificado con C&eacute;dula de Ciudadan&iacute;a No ________ actuando en nombre y cuenta propia en adelante EL DEUDOR, declaro:</p>
<p><strong>PRIMERO: OBJETO.</strong> Que como DEUDOR pagar&eacute; de forma incondicional en dinero efectivo, a la orden de <strong>CR&Eacute;DITOS PANDA S.A.S. </strong>identificada con Nit.901.369.753-0 o a quien represente sus derechos, la suma de (____________________________________)<strong> PESOS M/CTE ($________________)</strong>, m&aacute;s los intereses se&ntilde;alados en la disposici&oacute;n tercera de este documento<strong> PESOS M/CTE ($________________)</strong>.</p>
<p><strong>SEGUNDO. VENCIMIENTO.</strong> Que pagar&eacute; la suma indicada en la cl&aacute;usula anterior en la ciudad de __________ el d&iacute;a ______ del mes de _______ del a&ntilde;o ________.</p>
<p><strong>TERCERO: INTERESES DE MORA.</strong> En caso de incumplimiento o simple retardo en el cumplimiento de las obligaciones a mi cargo, pagar&eacute; a favor de CREDITOS PANDA S.A.S., intereses moratorios que ser&aacute;n liquidados a la tasa m&aacute;xima legal vigente se&ntilde;alada por la Superintendencia Bancaria.</p>
<p>&nbsp;</p>
<p><strong>CUARTO</strong>: <strong>CL&Aacute;USULA ACELERATORIA</strong>. El tenedor leg&iacute;timo de este pagar&eacute;, podr&aacute; declarar insubsistente los plazos de est&aacute; obligaci&oacute;n y exigir el pago inmediato de la totalidad de las obligaciones a mi cargo, judicial o extrajudicialmente en los siguientes casos:</p>
<ul>
<li>Por incumplimiento de cualquiera de las obligaciones por parte del deudor</li>
<li>La mora o el simple retardo en uno de los pagos de las cuotas pactadas.</li>
<li>Si los bienes del deudor son embargados o perseguidos judicialmente por cualquier persona y en ejercicio de cualquier acci&oacute;n<strong>. </strong></li>
<li>Si el (los) deudor(es) es (son) declarados en liquidaci&oacute;n forzosa, de car&aacute;cter comercial o administrativo admitidos a proceso concordatario o se adelanta un proceso de insolvencia de persona natural no comerciante.</li>
<li>Si las garant&iacute;as que se hayan otorgado para cubrir el cumplimiento de las obligaciones a cargo de los deudores y a favor del acreedor, resultaran insuficientes o se tornaren as&iacute; por deterioro o depreciaci&oacute;n, a juicio del acreedor, o si fueren perseguidas judicialmente por terceras personas.</li>
<li>Si el (los) deudor(es) dejaren de asegurar los bienes que fungen como garant&iacute;a a las obligaciones.</li>
<li>Si el Deudor fuere vinculado con actividades delictivas en Colombia o en el exterior, o incluido en listas restrictivas para la prevenci&oacute;n del lavado de activos y financiaci&oacute;n del terrorismo, sin necesidad de condena o decisi&oacute;n administrativa en firme.</li>
<li>Cuando se evidencie por cualquier medio que EL DEUDOR ha aportado informaci&oacute;n falsa o imprecisa a CREDITOS PANDA S.A.S, para el otorgamiento de los cr&eacute;ditos a mi favor.</li>
<li>Si el (los) deudor(es) enajenaren, a cualquier t&iacute;tulo y sin autorizaci&oacute;n del acreedor, los bienes que garantizan el cumplimiento de las obligaciones que por &eacute;ste pagar&eacute; asumen.</li>
</ul>
<p>&nbsp;</p>
<p><strong>QUINTA: IMPUESTOS Y GASTOS DE COBRANZA:</strong> Los gastos originados por concepto de impuestos u otra carga tributaria y los dem&aacute;s en que incurran por la ejecuci&oacute;n del presente t&iacute;tulo, tales como honorarios de abogado, honorarios de cobranza extrajudicial y judicial correr&aacute;n a cargo del deudor, sus avalistas y dem&aacute;s garantes. Para efectos legales, se excusa el aviso de rechazo, el protesto y la presentaci&oacute;n para el pago del presente Pagar&eacute;. Se autoriza expresamente para que en el caso de incumplimiento de las obligaciones, sea reportado mi nombre y la informaci&oacute;n relativa a mi incumplimiento a las centrales de informaci&oacute;n financiera destinadas a este fin (DATACR&Eacute;DITO, CIFIN o cualquier otra similar).</p>
@if($fecha_actual != "")
@php
$d = intval(date('d', strtotime($fecha_actual) ));
$m = intval(date('m', strtotime($fecha_actual) ));
$y = intval(date('y', strtotime($fecha_actual) ));
$dn = $numeros[$d];
$mn = $meses[$m];
$yn = $numeros[$y];
@endphp
<p>Se otorga este pagar&eacute; a los {{($dn)}} ({{date('d', strtotime($fecha_actual))}}) d&iacute;as del mes de {{($mn)}} del a&ntilde;o dos mil {{($yn)}} ({{date('Y', strtotime($fecha_actual))}}).</p>
@else
<p>Se otorga este pagar&eacute; a los __________ (___) d&iacute;as del mes de __________ del a&ntilde;o dos mil catorce (20__).</p>
@endif
<p>&nbsp;</p>
<p><strong>DEUDOR: {{$usuario->first_name}} {{$usuario->last_name}}</strong></p>
<p><strong>C.C. No. {{$usuario->n_document}}</strong></p>
<p><strong>Firma por apoderado CREDITOS PANDA S.A.S</strong></p>
<p>&nbsp;</p>
<p>Los espacios en blanco del texto previamente establecido, ser&aacute;n diligenciados en su totalidad por CREDITOS PANDA S.A.S, siguiendo para ello las siguientes instrucciones:</p>
<ol style="list-style-type: upper-alpha;">
<li>La cuant&iacute;a ser&aacute; igual al monto de todas las sumas que por cualquier concepto que EL DEUDOR le est&eacute;(n) debiendo a CREDITOS PANDA S.A.S, o sus cesionarios o por el valor de una o algunas de tales obligaciones, a elecci&oacute;n de CREDITOS PANDA S.A.S, o sus cesionarios, incluyendo sin limitarse al valor de capital, intereses, comisiones, dep&oacute;sitos, cargos, sanciones, multas o cualquier otra suma a cargo del DEUDOR. Los pagos se realizar&aacute;n libre de grav&aacute;menes, impuestos o tasas, los cuales ser&aacute;n asumidos por el (los) Deudor(es).</li>
<li>Los intereses corrientes ser&aacute;n los acordados entre el DEUDOR y CREDITOS PANDA S.A.S para cada obligaci&oacute;n y para los intereses de mora, se aplicar&aacute; la tasa moratoria m&aacute;s alta permitida en Colombia seg&uacute;n certificaci&oacute;n expedida por la Superintendencia Financiera de Colombia o cualquier autoridad a la que le sea asignada dicha funci&oacute;n.</li>
<li>La fecha de vencimiento de las obligaciones se incorporen en el pagar&eacute; ser&aacute; la del d&iacute;a en que el t&iacute;tulo sea llenado</li>
<li>El lugar de pago de las obligaciones ser&aacute; la ciudad de Bogot&aacute; D.C., o cualquier otra indicada por CREDITOS PANDA S.A.S. La fecha de creaci&oacute;n del t&iacute;tulo ser&aacute; la de su diligenciamiento. El lugar de creaci&oacute;n ser&aacute; aquel en que se entreg&oacute; el documento con espacios en blanco o el de cumplimiento de las obligaciones, tambi&eacute;n a elecci&oacute;n de <strong>CR&Eacute;DITOS PANDA S.A.S.</strong></li>
</ol>
<p>El pagar&eacute; as&iacute; llenado ser&aacute; exigible inmediatamente y prestar&aacute; m&eacute;rito ejecutivo sin m&aacute;s requerimientos.</p>
<p>Las anteriores son las instrucciones que CREDITOS PANDA S.A.S como apoderado deber&aacute; acatar en el diligenciamiento de uno o m&aacute;s pagar&eacute;s que instrumenten las obligaciones a mi cargo y a favor de CR&Eacute;DITOS PANDA S.A.S.</p>
<p>Ese poder se otorga de forma irrevocable a favor de CREDITOS PANDA S.A.S., permanecer&aacute; vigente mientras existan obligaciones a mi cargo y a favor de CREDITOS PANDA S.A.S, y es firmado de forma electr&oacute;nica, por lo cual reconozco plena validez a los mensajes de datos que contienen estas instrucciones y el poder conferido para que CREDITOS PANDA S.A.S suscriba en mi nombre uno o m&aacute;s pagar&eacute;s para su cobro prejudicial o judicial a su criterio.</p>
<p><strong>DEUDOR: {{$usuario->first_name}} {{$usuario->last_name}}</strong></p>
<p><strong>C.C. No. {{$usuario->n_document}}</strong></p>
<p><strong>Firma por apoderado CREDITOS PANDA S.A.S</strong></p>
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

