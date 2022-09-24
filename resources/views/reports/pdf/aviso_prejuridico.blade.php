<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p><strong>{{$today}}</strong><strong>, Bogot</strong><strong>&aacute;</strong><strong>. </strong></p>
    <p>Se&ntilde;or(a)</p>
    <p><strong>{{$fullName}}</strong><strong>, </strong></p>
    <p>Ciudad.</p>
    <p><strong>&nbsp;</strong></p>
    <p><strong>REF: AVISO DE COBRO PREJUR&Iacute;DICO &ndash; VALOR ADEUDADO ${{ number_format($saldoDia) }} &nbsp;</strong></p>
    <p><strong>&nbsp;</strong></p>
    <p>A la fecha, nos hemos comunicado comunicado con Usted por los distintos medios de contacto que tenemos disponibles para que realice el pago de la deuda que tiene a su cargo con nosotros, sin embargo hasta la fecha, Usted no ha honrado este compromiso.</p>
    <p>Por lo tanto, teniendo en cuenta su incumplimiento y que no ha atendido nuestros requerimientos para realizar el pago, le informamos que hemos decidido adelantar las siguientes acciones en su contra.</p>
    <ul>
    <li>Efectuar los reportes de su incumplimiento a las Centrales de Cr&eacute;dito e informaci&oacute;n financiera (Datacr&eacute;dito, Transunion y otras), lo cual afectar&aacute; negativamente su puntaje crediticio y le impedir&aacute; acceder a otras oportunidades de cr&eacute;dito.</li>
    </ul>
    <p>&nbsp;</p>
    <ul>
    <li>Efectuaremos el d&eacute;bito autom&aacute;tico del total del cr&eacute;dito de sus cuentas bancarias, descontando directamente el dinero disponible hasta que la deuda sea cancelada en su totalidad. Esto significa que cualquier sueldo o dinero que por otro concepto ingrese a su cuenta bancaria, ser&aacute; descontado a nuestro favor hasta pagar el saldo total de la deuda.</li>
    </ul>
    <p>&nbsp;</p>
    <ul>
    <li>Conforme a la ley 1527 de 2012, informaremos a su empleador sobre la mora de su obligaci&oacute;n total, con el fin de que se realice el descuento directo v&iacute;a libranza de su salario, primas y otras prestaciones sociales, seg&uacute;n la autorizaci&oacute;n otorgada. Acorde a esta ley, si el empleador no realiza el descuento, se convertir&aacute; solidariamente responsable por las sumas dejadas de descontar.</li>
    </ul>
    <p>&nbsp;</p>
    <ul>
    <li>Iniciar acciones judiciales, para que los Jueces de la Rep&uacute;blica ordenen el embargo de sus cuentas bancarias asi como el embargo de su salario, con el fin de obtener el pago de nuestra acreencia.</li>
    </ul>
    <p>&nbsp;</p>
    <ul>
    <li>Embargaremos y remataremos en p&uacute;blica subasta sus bienes con el fin de obtener el pago de la deuda, proceso que generar&aacute; gastos de cobranza judicial, incrementando su deuda a&uacute;n m&aacute;s, proceso que la misma ley nos faculta para adelantar en el art&iacute;culo 599 y siguientes del C&oacute;digo General del Proceso.</li>
    </ul>
    <p>&nbsp;</p>
    <p>Adelantaremos todas estas acciones cumpliendo los requisitos legales para ellas y aclaramos, que se adelantar&aacute;n de forma simult&aacute;nea, es decir, se dar&aacute; inicio a todas las acciones y se mantendr&aacute;n vigentes hasta tanto se realice el pago del saldo del cr&eacute;dito a su cargo. S&oacute;lo cuando se pague la totalidad de la deuda, procederemos a levantar estas medidas.</p>
    <p>Le recordamos que su obligaci&oacute;n inicialmente adquirida por un capital de<strong> ${{number_format($montoDesembolso)}},</strong> actualmente tiene el siguiente estado de cuenta:</p>
    <p>&nbsp;</p>
    <table style="height: 200px;" border="1" width="468" cellspacing="0" cellpadding="0">
    <tbody>
    <tr style="height: 20px;">
    <td style="width: 226px; height: 20px;">
    <p><strong>Concepto</strong></p>
    </td>
    <td style="width: 226px; height: 20px;">
    <p><strong>Valor adeudado</strong></p>
    </td>
    </tr>
    <tr style="height: 17px;">
    <td style="width: 226px; height: 17px;">
    <p>Saldo de Capital.</p>
    </td>
    <td style="width: 226px; height: 17px;">
    <p>${{number_format($montos->capital)}}</p>
    </td>
    </tr>
    <tr style="height: 4px;">
    <td style="width: 226px; height: 4px;">
    <p>Intereses</p>
    </td>
    <td style="width: 226px; height: 4px;">
    <p>${{number_format($montos->intereses)}}</p>
    </td>
    </tr>
    <tr style="height: 17px;">
    <td style="width: 226px; height: 17px;">
    <p>Intereses moratorios</p>
    </td>
    <td style="width: 226px; height: 17px;">
    <p>${{number_format($montos->interesesMoratorio)}}</p>
    </td>
    </tr>
    <tr style="height: 13px;">
    <td style="width: 226px; height: 13px;">
    <p>Tarifa de plataforma.</p>
    </td>
    <td style="width: 226px; height: 13px;">
    <p>${{number_format($montos->plataforma)}}</p>
    </td>
    </tr>
    <tr style="height: 27px;">
    <td style="width: 226px; height: 27px;">
    <p>Tarifa aprobacion rapida</p>
    </td>
    <td style="width: 226px; height: 27px;">
    <p>${{number_format($montos->aprobacionRapida)}}</p>
    </td>
    </tr>
    <tr style="height: 44px;">
    <td style="width: 226px; height: 44px;">
    <p>Gastos de cobranza.</p>
    </td>
    <td style="width: 226px; height: 44px;">
    <p>${{number_format($montos->gastosCobranza)}}</p>
    </td>
    </tr>
    <tr style="height: 33px;">
    <td style="width: 226px; height: 33px;">
    <p>IVA Gastos de cobranza</p>
    </td>
    <td style="width: 226px; height: 33px;">
    <p>${{number_format($montos->ivaGastosCobranza)}}</p>
    </td>
    </tr>
    <tr style="height: 26px;">
    <td style="width: 226px; height: 26px;">
    <p>IVA</p>
    </td>
    <td style="width: 226px; height: 26px;">
    <p>${{number_format($montos->iva)}}</p>
    </td>
    </tr>
    <tr style="height: 25px;">
    <td style="width: 226px; height: 25px;">
    <p><strong>Total</strong></p>
    </td>
    <td style="width: 226px; height: 25px;">
    <p>${{number_format($montos->total)}}</p>
    </td>
    </tr>
    </tbody>
    </table>
    <p>&nbsp;</p>
    <p>Esta cifra, seguir&aacute; incrementando en la medida que su incumplimiento contin&uacute;e, por lo que le requerimos para comunicarse al<strong> 3212403734</strong> , con el fin de solucionar su deuda y evitar mayores perjuicios en su contra.</p>
    <p><strong>Sin otro particular, esperamos su respuesta en un plazo no superior a tres (3) d&iacute;as calendario, pues de lo contrario, entenderemos que no existe inter&eacute;s de llegar a un acuerdo para el pago, e iniciaremos las acciones mencionadas.</strong></p>
    <p>Departamento de Cartera</p>
    <p>CR&Eacute;DITOS PANDA S.A.S.</p>
</body>
</html>
