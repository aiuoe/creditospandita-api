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
use App\Models\CodigosValidaciones;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class FirmaExpirada extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firma:expirada';
        private $NAME_CONTROLLER = 'envio cron firma expirada';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalidar codigos de firma despues de cumplir 72h sin uso';

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

            $codigos = CodigosValidaciones::where('valido',1)->get();
            $fecha_actual = date("Y-m-d");
            // var_dump($codigos);
                foreach($codigos as $codigo){
                    $fecha_codigo_72 = date("Y-m-d",strtotime($codigo->created_at."+ 3 days"));
                    // echo $codigo->created_at.' ';
                    // echo $fecha_codigo_72.' ';
                    $fecha_codigo = $codigo->created_at;

                    if($fecha_actual >= $fecha_codigo_72){
                        $cod =  CodigosValidaciones::find($codigo->id);
                        $cod->valido = 0;
                        $cod->save();
                    }
                }
                // echo $fecha_actual.' ';
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
