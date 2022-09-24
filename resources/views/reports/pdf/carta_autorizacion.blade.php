<!DOCTYPE html>
<html lang="en">
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
        table, th, td {
          border: 1px solid black;
        }
      </style>
</head>
<body>
<p><strong>AUTORIZACI&Oacute;N DE DESCUENTO POR NOMINA</strong></p>
<p>En virtud de la ley 1527 de 2012 de Libranzas y aquellas que la sustituyan o modifiquen, yo como <strong>DEUDOR</strong>autorizo expresa e irrevocablementea CREDITOS PANDA SAS, sociedad identificada con Nit.901.369.753-0, o cualquiera de sus cesionarios a cualquier t&iacute;tulo, a informar a mi empleador actual o futuro o entidad que administre mi pensi&oacute;n la presente libranza y descontar de mi salario, bonificaciones, prestaciones sociales, pensi&oacute;n o de cualquier suma de dinero que se vaya generando a mi favor, el valor del capital adeudado equivalente a $ {{$contra_oferta ? number_format($contra_oferta->totalPagar) : number_format($solicitud->totalPagar)}} y gastos adeudados, m&aacute;s el valor de los intereses de mora que se causen hasta la fecha que <strong>CREDITOS PANDA SAS o sus cesionarios</strong>reciban el pago total, as&iacute; como los gastos de cobro judicial o extrajudicial de la deuda.</p>
<p>Igualmente autorizo expresa e irrevocablemente adescontar las cuotas que debo pagarle a <strong>CREDITOS PANDA SAS</strong> durante el tiempo que permanezca en vacaciones o licencia. En caso de que finalice mi relaci&oacute;n laboral por cualquier causa, autorizo para que, de los valores resultantes de mi liquidaci&oacute;n de contrato por concepto de salario, se descuenten las cuotas restantes para pagar el saldo de mi obligaci&oacute;n hasta el monto disponible y sean giradas a CREDITOS PANDA SAS o sus cesionariosy se descuenten de las prestaciones sociales, salarios, indemnizaciones, pensi&oacute;n etc., a que tengo derecho. As&iacute; mismo como firmante de este formato de autorizaci&oacute;n de descuento por nomina como <strong>DEUDOR</strong> autorizo expresamente al pagador de la empresa y/o a los fondos de cesant&iacute;as y pensiones, para que, en el caso de realizarse la liquidaci&oacute;n definitiva o parcial de &eacute;stas, de la suma que resulte, sea descontada y girada directamente a <strong>CREDITOS PANDA</strong> el saldo a mi cargo.</p>
<p>Para estos efectos, y con el fin de registro de la cuota mensual a descontar y saldo total de mi deuda ante mi empleador y/o fondos de administradores de pensi&oacute;n, declaro suficiente la certificaci&oacute;n emitida por<strong>CREDITOS PANDA SAS</strong> indique sobre el saldo debido a mi cargo y el valor de la cuota respectivas y la fecha de pago de la misma. De encontrarse vencido el cr&eacute;dito, autorizo para que <strong>CREDITOS PANDA SAS</strong>, informe al empleador y/o fondo administrador de pensiones, que se deber&aacute; incrementar la cuota a descontar por conceptos como intereses moratorios, gastos de cobranza entre otros debidamente certificados por CREDITOS PANDAS SAS. Como <strong>DEUDOR</strong> acepto por lo tanto adelantar las gestiones y coordinar lo que haya lugar con la pagadur&iacute;a de la empresa (o entidad) de la cual devengo mi salario o pensi&oacute;n.</p>
<p>La presente autorizaci&oacute;n es firmada utilizando mecanismos electr&oacute;nicos, por lo que reconozco como DEUDOR que CREDITOS PANDA SAS podr&aacute; presentar en este mismo formato la presente autorizaci&oacute;n, reconociendo plena validez a su contenido.</p>
<p>En el evento de que mi empresa pagadora o entidad administradora de mis fondos de pensiones, requiera que esta autorizaci&oacute;n conste por escrito f&iacute;sico o que se diligencie cualquier otro documento adicional a la misma para efectuar el descuento de libranza que he autorizado, declaro que por este mismo documento electr&oacute;nico otorgo poder especial de car&aacute;cter irrevocable a favor de CREDITOS PANDA SAS sociedad identificada con Nit.901.369.753-0, o cualquiera de sus cesionarios a cualquier t&iacute;tulo, para que en mi nombre y representaci&oacute;n adelante todos los tr&aacute;mites necesarios ante mi empleador o entidad administradora de fondos de pensiones, para que se lleve a cabo el descuento autorizado, estando CREDITOS PANDA SAS autorizado para suscribir cualquier documento f&iacute;sico o electr&oacute;nico que le sea exigido para tal fin, inclusive para que suscriba por mi cuenta y representaci&oacute;n una autorizaci&oacute;n de descuento f&iacute;sica en los t&eacute;rminos aqu&iacute; se&ntilde;alados, incluyendo en la misma el valor concreto a descontar. El poder que por este documento electr&oacute;nico se otorga permanecer&aacute; vigente mientras existan obligaciones o saldos pendientes a mi cargo y a favor de CREDITOS PANDA S.A.S.</p>
<table border="1">
<tbody>
<tr>
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

