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


class UnDiaPago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'undia:pago';
        private $NAME_CONTROLLER = 'envio cron';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email a usuarios con a un dia de su pago proximo';

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
            ->get();
            $fecha_actual = date("Y-m-d");
            $fecha_ayer = date("Y-m-d",strtotime($fecha_actual."- 1 days"));
                foreach($creditos as $credito){
                    
                    $contenido=Correos::where('pertenece','pago a un dia')->first();
                    $contenido_hoy=Correos::where('pertenece','pago mismo dia')->first();
                    $solicitud = Calculadora::where('id',$credito->id_solicitud)->first();
                    $pago_proximo = Pagos::where('idSolicitudFk', $credito->id_solicitud)
                    ->where('estatusPago', 'pendiente')->first();
                    $contra_oferta = ContraOferta::where('idCalculadoraFk', $credito->id_solicitud)->orderBy('id','desc')->first();
                    $res = array();
                    $cuota=0;
                    $fecha_ayer_pago = date("Y-m-d",strtotime($pago_proximo->fechaPago."- 1 days"));
                    // echo $fecha_ayer_pago." ";
                    // echo $fecha_actual."\n";
                    if($fecha_ayer_pago == $fecha_actual){
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
                            $plazo_pago = $res->plazo." ".$Lplazo;
                            $monto_aprobado = "$".number_format($res->montoAprobado);
                            $monto_solicitado = "$".number_format($res->montoSolicitado);
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
                            $plazo_pago = $solicitud->plazo." ".$Lplazo;
                            $monto_aprobado = "$".number_format($solicitud->montoSolicitado);
                            $monto_solicitado = "$".number_format($solicitud->montoSolicitado);
                        }
                        $monto_pagar = "$".number_format($pago_proximo->montoPagar);
                        if($contenido->estatus=='activo'){
                            
                            $numerCredito=$credito->numero_credito;
                            // $expedicion=$credito->created_at;
                
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
                            "{{MontoPagar}}"
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
                                $montoPagar
                            );
                        
                            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                            $data = [
                                'Nombre' => $name,
                                'Email'=>$email,
                                'Apellido'=>$last_name,
                                'Cedula'=>$cedula,
                                'Contenido'=>$cntent2,
                            ];

                            Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name){
                                $msj->subject($name.', recuerda que el pago de tu préstamo es mañana');
                                $msj->to($email);
                                $msj->to('respaldos@creditospanda.com');
                                
                            });
                        }   
                    }else if($fecha_actual == $pago_proximo->fechaPago ){
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
                            $plazo_pago = $res->plazo." ".$Lplazo;
                            $monto_aprobado = "$".number_format($res->montoAprobado);
                            $monto_solicitado = "$".number_format($res->montoSolicitado);
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
                            $plazo_pago = $solicitud->plazo." ".$Lplazo;
                            $monto_aprobado = "$".number_format($solicitud->montoSolicitado);
                            $monto_solicitado = "$".number_format($solicitud->montoSolicitado);
                        }
                        $monto_pagar = "$".number_format($pago_proximo->montoPagar);
                        if($contenido_hoy->estatus=='activo'){
                            
                            $numerCredito=$credito->numero_credito;
                            // $expedicion=$credito->created_at;
                
                            $name=$credito->first_name;
                            $last_name=$credito->last_name;
                            $email=$credito->email;
                            $cedula=$credito->n_document;
                            $codigo= $credito->codigo;
                            $token=$credito->token_firma;
                            $pathToFile=$credito->documentoContrato;
                            $pathToFilePagare=$credito->documentoPagare;
                            $pathToFileCartaAutorizacion=$credito->documentoCarta;
                            $content=$contenido_hoy->contenido;
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
                            "{{MontoPagar}}"
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
                                $montoPagar
                            );
                        
                            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                            $data = [
                                'Nombre' => $name,
                                'Email'=>$email,
                                'Apellido'=>$last_name,
                                'Cedula'=>$cedula,
                                'Contenido'=>$cntent2,
                            ];

                            Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name){
                                $msj->subject($name.', recuerda que el pago de tu préstamo es hoy');
                                $msj->to($email);
                                
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
