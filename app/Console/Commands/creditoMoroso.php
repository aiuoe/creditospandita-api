<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\User;
use App\Models\Correos;
use App\Models\Calculadora;
use App\Models\ContraOferta;
use App\Models\Basica;
use App\Models\Financiera;
use App\Models\Referencias;
use App\Models\Pagos;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Models\ConfigCalculadora;


class CreditoMoroso extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credito:moroso';
        private $NAME_CONTROLLER = 'envio cron credito moroso';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email a usuarios con mora';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //


         try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $creditos = Calculadora::
            leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('calculadoras.id as id_solicitud','users.*','calculadoras.*')
            ->where('calculadoras.estatus','abierto')
            ->OrWhere('calculadoras.estatus','moroso')
            ->OrWhere('calculadoras.estatus','novado')
            ->get();
            $fecha_actual = date("Y-m-d");
            $contenido="";
            $fecha_ayer = date("Y-m-d",strtotime($fecha_actual."- 1 days"));
                foreach($creditos as $credito){
                    $contenido="";
                    // $contenido=Correos::where('pertenece','pago a un dia')->first();
                    // $contenido_hoy=Correos::where('pertenece','pago mismo dia')->first();
                    $solicitud = Calculadora::where('id',$credito->id_solicitud)->first();
                    $pago_proximo = Pagos::where('idSolicitudFk', $credito->id_solicitud)
                    ->where('estatusPago', 'pendiente')->first();
                    $contra_oferta = ContraOferta::where('idCalculadoraFk', $credito->id_solicitud)->orderBy('id','desc')->first();
                    $basica = Basica::where('idUserFk', $credito->idUserFk)->first();
                    $pagado = DB::table("repagos")->where('idSolicitudFk',$credito->id_solicitud)
                    ->where('concepto','!=','Novacion')
                    ->get()
                    ->sum("montoRepagar");
                    $pagos = Pagos::where('idSolicitudFk', $credito->id_solicitud)->get();
                    $res = array();
                    $cuota=0;
                    $diasMora=0;
                    $fecha_ayer_pago = date("Y-m-d",strtotime($pago_proximo->fechaPago."- 1 days"));
                    if($pago_proximo->fechaPago >= $fecha_actual){
                       $diasMora= 0; 
                    }else{
                    $date1 = new DateTime($pago_proximo->fechaPago);
                    $date2 = new DateTime($fecha_actual);
                    $diff = $date1->diff($date2);
                    // will output 2 days
                        $diasMora= $diff->days;
                    }
                    // echo $credito->id." ";
                    // echo $diasMora."\n";
                    
                    if($diasMora > 0 && ($credito->estatus=="abierto" || $credito->estatus=="novado") && $credito->estatusIntereses==1){
                        $solicitud->estatus = "moroso";
                        $solicitud->estatusAnterior = "abierto";
                        $solicitud->save();
                    }
                    if($credito->estatus=="abierto" || $credito->estatus=="novado"){
                        $solicitud->diasMora = 0;
                        $solicitud->save();  
                    }else{
                        $solicitud->diasMora = $diasMora;
                        $solicitud->save();  
                    }
                     
                    if($credito->tipoCredito == 'd'){
                        if($solicitud->estatus == "novado"){
                            $date1 = new DateTime($solicitud->fechaNovado);
                            $date2 = new DateTime($fecha_actual);
                            $diff = $date1->diff($date2);
                            // will output 2 days
                                $dvc= $diff->days;
                        // dvc = Moment().diff($solicitud->fechaNovado,'days')
                        }else{
                            $date1 = new DateTime($solicitud->fechaDesembolso);
                            $date2 = new DateTime($fecha_actual);
                            $diff = $date1->diff($date2);
                            // will output 2 days
                            $dvc= $diff->days;
                        // dvc = Moment().diff($solicitud->fechaDesembolso,'days')
                        }
                        if($dvc<=0){
                            $diasVanCredito = 1;
                        }else{
                            $diasVanCredito = $dvc;
                        }
                    }
                    
                    $asunto ='';
                    $interesMora =0;
                    $gastosCobranza = 0;
                    $interesMoratorio = pow(1+(22/100), (1/360))-1;
                    $montoInvertido=0;
                    $totalP =0;
                    $suma_novacion=0;
                    if($diasMora == 1){
                        if($credito->tipoCredito == 'm'){
                            $contenido=Correos::where('pertenece','un dia de mora panda meses')->first();
                        }else{
                            $contenido=Correos::where('pertenece','un dia de mora panda dias')->first();
                        }
                        $asunto = 'tu crédito esta vencido y próximo a entrar en mora';
                    }else if($diasMora == 3){
                        $asunto = 'tu crédito con Créditos Panda esta vencido, vas a ser reportado negativamente CC ( '.$credito->numero_credito.' )';
                        $contenido=Correos::where('pertenece','tercer dia de mora')->first();
                    }else if($diasMora >=4 && $diasMora <=22){
                        $asunto = 'realiza el pago y evita REPORTES NEGATIVOS ante centrales.';
                        $contenido=Correos::where('pertenece','cuatro a veintidos dias de mora')->first();
                    }else if($diasMora == 23){
                        $asunto = 'has sido reportado NEGATIVAMENTE ante DataCrédito.';
                        $contenido=Correos::where('pertenece','dia veintitres de mora')->first();
                    }
                        if($solicitud->ofertaEnviada != 2){
                            $res = $contra_oferta;
                            $cuota=round($contra_oferta->totalPagar/$contra_oferta->plazo);
                            if($credito->tipoCredito=="m"){
                                $tipo="Panda meses";
                                $Lplazo = "Meses";
                            }else{
                                $tipo="Panda dias";
                                $Lplazo = "Dias";
                            }
                            $monto="$".number_format($res->totalPagar);
                            $numerCredito=$credito->numero_credito;
                            $expedicion=$credito->created_at;
                            $plazo_pago = $res->plazo;
                            $montoInvertido=$res->montoAprobado;
                            $totalP =$res->totalPagar;
                            $monto_aprobado = "$".number_format($res->montoAprobado);
                            $monto_solicitado = "$".number_format($res->montoSolicitado);
                            $tasa_interes = "$".number_format($res->tasaInteres);
                            $plataforma = "$".number_format($res->plataforma);
                            $aprobacion_rapida = "$".number_format($res->aprobacionRapida);
                            $iva = "$".number_format($res->iva);
                            $suma_novacion = $res->tasaInteres+$res->plataforma+$res->aprobacionRapida+$res->iva;
                            $total_pagar = "$".number_format($suma_novacion);
                        }else{
                            $cuota=round($solicitud->totalPagar/$solicitud->plazo);
                            if($credito->tipoCredito=="m"){
                                $tipo="Panda meses";
                                $Lplazo = "Meses";
                            }else{
                                $tipo="Panda dias";
                                $Lplazo = "Dias";
                            }
                            $monto="$".number_format($solicitud->totalPagar);
                            $numerCredito=$credito->numero_credito;
                            $expedicion=$credito->created_at;
                            $plazo_pago = $solicitud->plazo;
                            $montoInvertido=$solicitud->montoSolicitado;
                            $totalP =$solicitud->totalPagar;
                            $monto_aprobado = "$".number_format($solicitud->montoSolicitado);
                            $monto_solicitado = "$".number_format($solicitud->montoSolicitado);
                            $tasa_interes = "$".number_format($solicitud->tasaInteres);
                            $plataforma = "$".number_format($solicitud->plataforma);
                            $aprobacion_rapida = "$".number_format($solicitud->aprobacionRapida);
                            $iva = "$".number_format($solicitud->iva);
                            $suma_novacion = $solicitud->tasaInteres+$solicitud->plataforma+$solicitud->aprobacionRapida+$solicitud->iva;
                            $total_pagar = "$".number_format($suma_novacion);
                        }
                        $interesMoraSuma =0;
                        if($diasMora > 1){
                           
                            foreach ($pagos as $key => $value) {
                                $pag = Pagos::find($value->id);
                                $month = date("m", strtotime($value->fechaPago));
                                $year = date("Y", strtotime($value->fechaPago));
                                $endOfMonth = date("d",(mktime(0,0,0,$month+1,1,$year)-1));
                                $dmp = 0;
                                
                                if($value->estatusPago !="pagado"){
                                    if($value->fechaPago < $fecha_actual){
                                        $date1 = new DateTime($value->fechaPago);
                                        $date2 = new DateTime($fecha_actual);
                                        $diff = $date1->diff($date2);
                                        // will output 2 days
                                        $dmp= $diff->days;

                                        if($dmp<1){
                                            $dmp = 0;  
                                        }else if($dmp>$endOfMonth){
                                            $dmp = $endOfMonth;
                                        }
                                        if($dmp>0){
                                            $im = 0;
                                            $gc=0;
                                            $ivagc =0;
                                            $im = $montoInvertido*pow((1+$interesMoratorio),$dmp);
                                            $interesMora = ($im-$montoInvertido);
                                            
                                            $pag->interesMora=$interesMora;

                                            if($credito->tipoCredito == 'm'){
                                                $gc = ($cuota*30)/100;
                                                $ivagc = ($gc*19)/100;
                                                $gastosCobranzaSinIva = $gc;
                                                $ivaGastosCobranza = $ivagc;
                                                $gastosCobranza = $gc+$ivagc;
                                                if($diasMora < 30){
                                                    $gastosCobranza = ($gastosCobranza/30)*$dmp;
                                                    $gastosCobranzaSinIva = ($gc/30)*$dmp;
                                                    $ivaGastosCobranza = ($ivagc/30)*$dmp;
                                                }
                                                $pag->gastosCobranza = $gastosCobranza;
                                                $pag->ivaGastosCobranza = $ivaGastosCobranza;
                                                $pag->gastosCobranzaSinIva = $gastosCobranzaSinIva;
                                            
                                            }else{
                                                $gc = ($totalP*30)/100;
                                                $ivagc = ($gc*19)/100;
                                                $gastosCobranza = $gc+$ivagc;
                                                $gastosCobranzaSinIva = $gc;
                                                $ivaGastosCobranza = $ivagc;
                                                
                                                if($diasMora < 60){
                                                    $gastosCobranza = ($gastosCobranza/60)*$diasMora;
                                                    $gastosCobranzaSinIva = ($gc/60)*$diasMora;
                                                    $ivaGastosCobranza = ($ivaGastosCobranza/60)*$diasMora;
                                                }
                                                $pag->gastosCobranza = $gastosCobranza;
                                                $pag->ivaGastosCobranza = $ivaGastosCobranza;
                                                $pag->gastosCobranzaSinIva = $gastosCobranzaSinIva;
                                                
                                            }
                                        }
                                    }
                                    
                                    $pag->diasMora = $dmp;
                                    $pag->save();
                                }
                                
                            }
                            // $pago_proximo->diasMora = $diasMora;
                            // $pago_proximo->gastosCobranza = $gastosCobranza;
                            // $pago_proximo->gastosCobranzaSinIva = $gc;
                            // $pago_proximo->ivaGastosCobranza = $ivagc;
                            // $pago_proximo->interesMora = $interesMora;
                            // $pago_proximo->save(); 
                        }

                        if($diasMora == 1){
                            $monto_pagar = "$".number_format($pago_proximo->montoPagar);  
                        }else if($diasMora >=3){
                            $t=0;
                            $tt=0;
                            if($credito->tipoCredito=="m"){
                                $t = round($cuota+$interesMora+$gastosCobranza);
                                // $tt = round($t-$pagado);
                                $monto_pagar = "$".number_format($t);
                            }else{
                                $t = round($totalP+$interesMora+$gastosCobranza);
                                $tt = round($t-$pagado);
                                $monto_pagar = "$".number_format($tt);  
                            }
                            
                        }
                        
                    
                        if($contenido && $contenido->estatus=='activo'){
                            Log::error($credito->id.': '.$totalP.': '.$interesMora.': '.$gastosCobranza);
                            $numerCredito=$credito->numero_credito;
                            // $expedicion=$credito->created_at;
                            $telefono = $credito->phone_number;
                            $name=$credito->first_name;
                            $last_name=$credito->last_name;
                            $email=$credito->email;
                            $cedula=$credito->n_document;
                            $codigo= $credito->codigo;
                            $token=$credito->token_firma;
                            $pathToFile=$credito->documentoContrato;
                            $pathToFilePagare=$credito->documentoPagare;
                            $pathToFileCartaAutorizacion=$credito->documentoCarta;
                            $content=$contenido->contenido;
                            $fechaPago = $pago_proximo->fechaPago;
                            $montoPagar = $monto_pagar;
                            

                            $arregloBusqueda=array(
                            "{{Nombre}}",
                            "{{Apellido}}",
                            "{{Email}}",
                            "{{Cedula}}",
                            "{{Ncredito}}",
                            "{{Monto}}",
                            "{{Expedicion}}",
                            "{{TipoCredito}}",
                            "{{Cod_Firma}}",
                            "{{Token}}",
                            "{{PlazoPago}}",
                            "{{MontoAprobado}}",
                            "{{MontoSolicitado}}",
                            "{{FechaPago}}",
                            "{{MontoPagar}}",
                            "{{TasaInteres}}",
                            "{{Plataforma}}",
                            "{{AprobacionRapida}}",
                            "{{IVA}}",
                            "{{TotalPagar}}",
                            '{{FechaActual}}',
                            "{{Direccion}}",
                            "{{Ciudad}}"
                            );
                            $arregloCambiar=array(
                                $name,
                                $last_name,
                                $email,
                                $cedula,
                                $numerCredito,
                                $monto,
                                $expedicion,
                                $tipo,
                                $codigo,
                                $token,
                                $plazo_pago,
                                $monto_aprobado,
                                $monto_solicitado,
                                $fechaPago,
                                $montoPagar,
                                $tasa_interes,
                                $plataforma,
                                $aprobacion_rapida,
                                $iva,
                                $total_pagar,
                                $fecha_actual,
                                $basica->direccion,
                                $basica->ciudad
                            );
                        
                            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                            $data = [
                                'Nombre' => $name,
                                'Email'=>$email,
                                'Apellido'=>$last_name,
                                'Cedula'=>$cedula,
                                'Contenido'=>$cntent2,
                            ];
                            Log::error("credito moroso".$credito->id.':'.$diasMora.':'.$pago_proximo->fechaPago);
                            if($diasMora == 1){
                                $msjAdmin = '<p>El credito entro en mora:<br> Nro de Credito: '.$numerCredito.' <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.' <br> Monto solicitado: '.$monto_solicitado.' <br> Monto aprobado: '.$monto_aprobado.'<br> Total a pagar: '.$monto_pagar.'<br> Email: '.$email.'<br> Telefono: '.$telefono.'</p>';
                                $infoAdmin =[
                                    'Contenido'=>$msjAdmin,
                                ];
                                Mail::send('Mail.plantilla',$infoAdmin, function($msj) {
                                    $msj->subject('Notificacion de credito en mora');
                                    $msj->to('info@creditospanda.com');
                                });
                            }
                            if($diasMora >= 1){
                                Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$asunto){
                                    $msj->subject($name.', '.$asunto);
                                    $msj->to($email);
                                    $msj->to('respaldos@creditospanda.com');
                                    
                                });
                            }
                        }   
                    }

            DB::commit(); // Guardamos la transaccion
       
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error inesperado.',
            ], 500);
        }
       

    }
}
