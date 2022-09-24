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


class PendienteNovacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pendiente:novacion';
        private $NAME_CONTROLLER = 'envio cron pendiente novacion';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Acualizar creditos a usuarios con creditos en estatus pendiente de novacion';

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
            ->where('calculadoras.estatus','pendiente de novacion')
            ->get();
            $fecha_actual = date("Y-m-d H:i:s");
            $contenido="";
            // $fecha_mnn = date("Y-m-d H:i:s",strtotime($fecha_actual."+ 1 days"));
                foreach($creditos as $credito){
                    $fecha_mnn = date("Y-m-d H:i:s",strtotime($credito->fechaPendienteNovacion."+ 1 days"));
                    // $contenido=Correos::where('pertenece','pago a un dia')->first();
                    // $contenido_hoy=Correos::where('pertenece','pago mismo dia')->first();
                    $solicitud = Calculadora::where('id',$credito->id_solicitud)->first();
                    
                    // echo $credito->id." ";
                    // echo $fecha_actual." ";
                    // echo $fecha_mnn."\n";
                    if($fecha_actual >= $fecha_mnn){
                        // echo "act";
                            $solicitud->estatus = $solicitud->estatusAnterior;
                            $solicitud->save();
                        
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
