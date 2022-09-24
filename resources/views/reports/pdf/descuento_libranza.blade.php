<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Se&ntilde;ores</p>
    <p>Departamento de Recursos Humanos</p>
    <p>{{$userBasica->financiera->nombreEmpresa}}</p>
    <p>&nbsp;</p>
    {{-- <p>&nbsp;</p> --}}
    <p>Somos CR&Eacute;DITO PANDA S.A.S, sociedad comercial identificada con Nit.901.369.753-0, debidamente registrados en el RUNEOL como operadores de libranza, seg&uacute;n n&uacute;mero &uacute;nico de reconocimiento 90136975300006048.</p>
    <p>&nbsp;</p>
    <p>Por este medio, nos permitimos notificarle que su empleado(a) <b>{{$fullName}}</b>, identificado(a) con c&eacute;dula de ciudadan&iacute;a No <b>{{ $document }}</b>, &nbsp;tiene vigente con nosotros un cr&eacute;dito por saldo total a la fecha de <strong> ${{ number_format($saldoDia) }} </strong>. Para efectos del pago de este cr&eacute;dito, {{ $fullName }} otorg&oacute; a nuestro favor la autorizaci&oacute;n de descuento directo (libranza) de su n&oacute;mina, la cual consta en mensaje de datos firmado electr&oacute;nicamente, soporte que adjuntamos en este correo y que es plenamente valido para efectuar los descuentos seg&uacute;n lo ha dispuesto la Superintendencia de Sociedades en oficio No.220-016062 del 05 de marzo de 2019 y el Ministerio de Trabajo en concepto radicado No.02EE2019410600000049118. En esta autorizaci&oacute;n como se puede observar, {{ $fullName }} autoriz&oacute; el descuento total del capital prestado, m&aacute;s los intereses y otros gastos causados que se causaran.
    <br />
    <br />
    Por lo tanto, teniendo en cuenta que &nbsp;{{ $fullName }} ha entrado en mora de su cr&eacute;dito, solicitamos a Ustedes proceder con el descuento total de <strong> ${{ number_format($saldoDia) }} </strong>, que corresponde al monto total adeudado a la fecha de esta comunicaci&oacute;n, y consignarla en la cuenta de ahorros No. 2110065594200011 del Banco Banco de las Microfinanzas Bancamia que se encuentra a nombre de CR&Eacute;DITO PANDA S.A.S con Nit.901.369.753-0.&nbsp; En el evento de que este monto supere el l&iacute;mite m&aacute;ximo de descuento permitido por la legislaci&oacute;n vigente, solicitamos realizar el descuento mensual del monto que sea posible realizar hasta tanto se complete el pago total de la deuda.</p>
    <p>&nbsp;</p>
    <p>En caso de requerir el diligenciamiento de alg&uacute;n documento adicional o convenio de libranza, estaremos atentos a su respuesta por este medio para proceder y coordinar lo que necesiten.</p>
    <p>&nbsp;</p>
    {{-- <p>&nbsp;</p> --}}
    <p>Alejandra Puentes</p>
    <p>Departamento de Cobranza. &nbsp;</p>
    <p><a href="mailto:info@creditospanda.com">info@creditospanda.com</a></p>
    <p>Cel: 3212403734</p>
    <p>CR&Eacute;DITOS PANDA S.A.S</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    {{-- <p>&nbsp;</p>
    <p>&nbsp;</p> --}}
</body>
</html>
