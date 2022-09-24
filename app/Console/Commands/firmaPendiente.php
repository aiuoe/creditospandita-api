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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class FirmaPendiente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firma:pendiente';
        private $NAME_CONTROLLER = 'envio cron';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email a usuarios con firma de contrato no realizada';

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
            leftJoin('codigos_validaciones', 'calculadoras.id', '=', 'codigos_validaciones.idSolicitudFk')
            ->leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('calculadoras.id as id_solicitud','calculadoras.*','users.*','codigos_validaciones.*')
            ->where('calculadoras.estatus','aprobado')
            ->where('calculadoras.estatus_firma','pendiente')
            ->where('codigos_validaciones.valido',1)
            ->where('calculadoras.notificadoFirma','<=',3)
            ->get();
                foreach($creditos as $credito){
                    $contenido=Correos::where('pertenece','Contrato')->first();
                    $solicitud = Calculadora::where('id',$credito->id_solicitud)->first();
                    $contra_oferta = ContraOferta::where('idCalculadoraFk', $credito->id_solicitud)->orderBy('id','desc')->first();
                    $res = array();
                    $cuota=0;
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
                        "{{MontoSolicitado}}"
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
                            $monto_solicitado
                        );
                      
                        $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                        $data = [
                            'Nombre' => $name,
                            'Email'=>$email,
                            'Apellido'=>$last_name,
                            'Cedula'=>$cedula,
                            'Contenido'=>$cntent2,
                        ];

                        Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$pathToFile,$pathToFilePagare,$pathToFileCartaAutorizacion){
                            $msj->subject($name.', recuerda que esta pendiente firmar los documentos para el desembolso');
                            $msj->to($email);
                            if($pathToFile){
                                $msj->attach($pathToFile); 
                            }
                            if($pathToFilePagare){
                                $msj->attach($pathToFilePagare);  
                            }
                            if($pathToFileCartaAutorizacion){
                                $msj->attach($pathToFileCartaAutorizacion);   
                            }
                            
                         });
            }   

                        $calAct=Calculadora::find($credito->id_solicitud);
                        $calAct->notificadoFirma=$calAct->notificadoFirma+1;
                        $calAct->save();

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
