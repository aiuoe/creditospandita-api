<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\User;
use App\Models\Correos;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Financiera;
use App\Models\Referencias;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class IncompleteUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomplete:info';
        private $NAME_CONTROLLER = 'envio cron informacion incompleta';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email a usuarios con informacion incompleta';

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

            $users = User::where('notificado',0)->get();
                foreach($users as $user){
                    if(!Basica::where('idUserFk',$user['id'])->exists() || 
                    !Referencias::where('idUserFk',$user['id'])->exists() ||
                    !Financiera::where('idUserFk',$user['id'])->exists()){


                        $usuarios=User::where('email',$user->email)->first();
                        $contenido=Correos::where('pertenece','incompleto')->first();
                        $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
            if($contenido && $contenido->estatus=='activo'){
                        // echo($contenido);
                        // if($credito->tipoCredito=="m"){
                        //     $tipo="Panda meses";
                        // }else{
                        //     $tipo="Panda dias";
                        // }
                        // $monto=$credito->totalPagar;
                        // $numerCredito=$credito->numero_credito;
                        // $expedicion=$credito->created_at;
            
                        $name=$usuarios->first_name;
                        $last_name=$usuarios->last_name;
                        $email=$usuarios->email;
                        $cedula=$usuarios->n_document;
                        $content=$contenido->contenido;
            
                  
            
                        $arregloBusqueda=array(
                        "{{Nombre}}",
                        "{{Apellido}}",
                        "{{Email}}",
                        "{{Cedula}}",
                        );
                        $arregloCambiar=array(
                            $name,
                            $last_name,
                            $email,
                            $cedula
                        );
                      
                        $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);
                        
            
                  
                   
                        $data = [
                            'Nombre' => $name,
                            'Email'=>$email,
                            'Apellido'=>$last_name,
                            'Cedula'=>$cedula,
                            'Contenido'=>$cntent2,
            
                            ];

                            
                            if($user['notificadoCantidad'] < 10 && $user['notificado']==0){
                                Mail::send('Mail.infoImcompleta',$data, function($msj) use ($email,$name){
                                    $msj->subject($name.',Solicitud incompleta');
                                    $msj->to($email);
                                    // $msj->to('respaldos@creditospanda.com');

                                });
                            }
            }   

                        $userAct=User::find($user['id']);
                        // echo $userAct->notificadoCantidad;
                        if($userAct->notificadoCantidad < 10){
                            Log::error("Correo incompleto".":".$email.":".$usuarios->notificadoCantidad);
                            $userAct->notificadoCantidad = $userAct->notificadoCantidad+1;
                        }else{
                            $userAct->notificado=1;
                        }
                        $userAct->save();
                    }else{
                        $userAct=User::find($user['id']);
                        $userAct->notificado=1;
                        $userAct->save();
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
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
       

    }
}
