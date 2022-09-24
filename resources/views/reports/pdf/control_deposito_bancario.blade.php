<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Bogot&aacute; D.C., <strong>{{$today}}.</strong></p>
    <p>Se&ntilde;ores.</p>
    <p><strong> {{ $userBasica->financiera->banco }} </strong></p>
    <p>Ciudad.</p>
    <p>&nbsp;</p>
    <p>REF: DERECHO DE PETICI&Oacute;N EN INTER&Eacute;S PARTICULAR &ndash; SOLICITUD DE CONTROL DE CUENTA BANCARIA SEG&Uacute;N LEY 1676 DE 2013.</p>
    <p>&nbsp;</p>
    <p>Respetados Se&ntilde;ores:</p>
    <p>&nbsp;</p>
    <p>Por medio de la presente, actuando en mi calidad de Representante Legal de la sociedad <strong>CR&Eacute;DITOS PANDA S.A.S</strong>, sociedad con domicilio principal en la ciudad de Bogot&aacute; D.C., identificada <strong>con Nit.901.369-753-0</strong>, me permito informarles que el se&ntilde;or <strong>{{ $fullName }}</strong>, mayor de edad identificado con c&eacute;dula de ciudadan&iacute;a <strong>No.{{ $document }}</strong>, otorg&oacute; a favor de CR&Eacute;DITOS PANDA S.A.S, una autorizaci&oacute;n de d&eacute;bito y garant&iacute;a mobiliaria sobre los dineros depositados en sus cuentas bancarias para garantizar el pago oportuno de los cr&eacute;ditos que le fueran otorgados por nuestra compa&ntilde;&iacute;a.</p>
    <p>&nbsp;</p>
    <p>Conforme al contrato de cr&eacute;dito suscrito electr&oacute;nicamente con el se&ntilde;or <strong>{{ $fullName }}</strong>, cliente de <strong> {{ $userBasica->financiera->banco }} </strong>, este nos autoriz&oacute; en el numeral 13.4 a solicitar ante Ustedes el d&eacute;bito autom&aacute;tico de cualquier suma que tuviera depositada en sus cuentas bancarias.</p>
    <p>&nbsp;</p>
    <p>Por lo anterior, de conformidad a las autorizaciones expresas impartidas por el se&ntilde;or&nbsp; <strong>{{ $fullName }}</strong>, solicitamos a <strong>{{ $userBasica->financiera->banco }}</strong>., se proceda de la siguiente manera:</p>
    <p>&nbsp;</p>
    <ol>
    <li>Se realice el d&eacute;bito autom&aacute;tico de una suma equivalente a<strong> ${{number_format($saldoDia)}}</strong> con cargo a cualquiera de los dep&oacute;sitos que se encuentren a nombre del se&ntilde;or <strong>{{$fullName}}</strong> en sus cuentas bancarias, en particular la cuenta de {{$userBasica->financiera->tipoCuenta}} {{$userBasica->financiera->nCuenta}} para pagar la obligaci&oacute;n que &eacute;l adquiri&oacute; con nosotros.</li>
    {{-- </ul> --}}
    <p>&nbsp;</p>
    <p>En el evento de que el se&ntilde;or <strong>{{$fullName}} </strong>tenga un saldo menor al aqu&iacute; se&ntilde;alado, se solicita que conforme a lo estipulado en el contrato y aprobado por &eacute;l, se realice la transferencia del monto disponible para realizar abonos parciales a nuestra acreencia.</p>
    <p>&nbsp;</p>
    {{-- <ul> --}}
    <li>La transferencia deber&aacute; ser realizada a nuestra cuenta de ahorros 2110065594200011 del Banco de las Microfinanzas Bancam&iacute;a a nombre de CR&Eacute;DITOS PANDA S.A.S.</li>
    {{-- </ul> --}}
    <p>&nbsp;</p>
    {{-- <ol> --}}
    <li>De no considerar procedente la realizaci&oacute;n del d&eacute;bito aqu&iacute; solicitado, solicitamos se indiquen las razones expresas y concretas por las cuales se desconoce la autorizaci&oacute;n de debito impartida por el se&ntilde;or <strong>{{$fullName}} </strong>y firmada electr&oacute;nicamente lo que garantiza su identidad en el proceso de aceptaci&oacute;n de las condiciones pactadas con nosotros.</li>
    </ol>
    <p>&nbsp;</p>
    <p>Como soporte de nuestras peticiones, adjuntamos los siguientes documentos:</p>
    <p>&nbsp;</p>
    <ol>
    <li>Certificado de existencia y representaci&oacute;n legal de CR&Eacute;DITOS PANDA S.A.S, que acredita mi condici&oacute;n de representante legal.</li>
    <p>&nbsp;</p>
    <li>Contrato de cr&eacute;dito <strong>{{$nCredito}}</strong> suscrito electr&oacute;nicamente por el se&ntilde;or <strong>{{$fullName}}</strong> y en el que autoriza la realizaci&oacute;n del d&eacute;bito correspondiente, en su numeral 13.4.</li>
    <p>&nbsp;</p>
    <li>Folio electr&oacute;nico No.{{$folioElectronico}} expedido por Confec&aacute;maras donde se acredita el formulario de ejecuci&oacute;n de la garant&iacute;a mobiliaria otorgada por <strong>{{$fullName}}</strong>, teniendo en consideraci&oacute;n que la autorizaci&oacute;n de d&eacute;bito autom&aacute;tico es una garant&iacute;a mobiliaria seg&uacute;n la definici&oacute;n contemplada en el art&iacute;culo 3&deg; de la ley 1676 de 2013.</li>
    </ol>
    <p>&nbsp;</p>
    <p>Para efectos de la respuesta de esta petici&oacute;n, manifiesto que recibiremos notificaciones en el correo electr&oacute;nico <a href="mailto:info@creditospanda.com"><strong>info</strong><strong>@creditospanda.com</strong></a> <strong>&nbsp;</strong></p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>Sin otro particular,</p>
    <p>&nbsp;</p>
    <p>&nbsp;<img src="{{ public_path('images/firma.png') }}" alt="asd" width="139" height="82" /></p>
    <p>Menajem Mendel Srugo Posternak</p>
    <p>C.C.No. 1126018831</p>
    <p>Representante Legal.</p>
    <p>CR&Eacute;DITOS PANDA S.A.S.</p>
</body>
</html>
