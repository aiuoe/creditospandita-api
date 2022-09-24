<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluacion;
use App\Models\User;
use App\Models\Correos;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\ContraOferta;
use App\Models\Financiera;
use App\Http\Requests\EvaluacionCreateRequest;
use App\Repositories\EvaluacionRepositoryEloquent;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\Route;
use App\Exports\ViewExport;
use App\Models\RecordHistory;
use PDFt;
use Maatwebsite\Excel\Facades\Excel;

class EvaluacionController extends Controller
{

        /**
     * @var $repository
     */
    protected $repository;

    private $NAME_CONTROLLER = 'EvaluacionController';

    public function __construct(EvaluacionRepositoryEloquent $repository)
    {

        $this->repository = $repository;
    }

    public function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer',
                'until'         =>  'nullable|date_format:Y-m-d',
                'since'         =>  'nullable|date_format:Y-m-d'
            ]);
        $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();
        // $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
        // $result = Evaluacion::where('estatus','preaprobado')->OrWhere('estatus','aprobado')->paginate( $per_page);
         $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk');
         $query->leftJoin('evaluacions', 'calculadoras.id', '=', 'evaluacions.idSolicitudFk')
        //  ->leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
         ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','calculadoras.created_at as fecha_solicitud',
         'evaluacions.*','evaluacions.estatus as estatus_evaluacion', 'evaluacions.id as id_evaluacion',
        'users.*','users.id as id_usuario');
            if(!empty(trim($request->search))){
                    if(strtolower(trim($request->search)) =='por firmar'){
                        $request->search='pendiente';
                    }else if(strtolower(trim($request->search)) =='por desembolsar'){
                        $request->search='firmado';
                    }else if(strtolower(trim($request->search)) =='minima'){
                        $request->search=0;
                    }else if(strtolower(trim($request->search)) =='maxima'){
                     $request->search=1;
                    }else if(strtolower(trim($request->search)) =='completa'){
                        $request->search=2;
                    }else{
                        $request->search=trim($request->search);
                    }

                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.estatus','abierto')
                        ->OrWhere('calculadoras.estatus','aprobado')
                        ->OrWhere('calculadoras.estatus','pagado')
                        ->OrWhere('calculadoras.estatus','novado')
                        ->OrWhere('calculadoras.estatus','pendiente de novacion')
                        ->OrWhere('calculadoras.estatus','moroso')
                        ->OrWhere('calculadoras.estatus','restructurado')
                        ->OrWhere('calculadoras.estatus','castigado');
                    });
                    // $query->where('evaluacions.estatus','!=','negado');
                    $query->where(function($query2) use ($request) {
                       $query2->OrWhere('calculadoras.ofertaEnviada',$request->search)
                       ->OrWhere('calculadoras.estatus','abierto')
                       ->OrWhere('calculadoras.estatus','aprobado')
                       ->OrWhere('calculadoras.estatus','pagado')
                        ->OrWhere('calculadoras.estatus','castigado')
                        ->OrWhere('calculadoras.estatus','novado')
                        ->OrWhere('calculadoras.estatus','pendiente de novacion')
                        ->OrWhere('calculadoras.estatus','moroso')
                        ->OrWhere('calculadoras.estatus','restructurado');

                    });


                    if($request->estatus){
                        if($request->estatus && trim($request->estatus)=='negado'){

                         $query->where(function($query2) use ($request) {
                             $query2->orWhere('evaluacions.estatus','negado verificacion selfie')
                             ->OrWhere('evaluacions.estatus','negado verificacion identidad')
                             ->OrWhere('evaluacions.estatus','negado archivos adicionales')
                             ->OrWhere('evaluacions.estatus','negado en la llamada')
                             ->OrWhere('evaluacions.estatus','negado en matriz de calculo')
                             ->OrWhere('evaluacions.estatus','negado en extractos bancarios')
                             ->OrWhere('evaluacions.estatus','negado')
                             ->OrWhere('evaluacions.estatus','negado en data credito');

                         });

                        }
                        if($request->estatus && trim($request->estatus)=='aprobado'){

                         $query->where(function($query2) use ($request) {
                             $query2->orWhere('evaluacions.estatus','aprobado');


                         });

                        }
                        if($request->estatus && trim($request->estatus)=='pendiente'){

                            //  $query->where('evaluacions.estatus','!=','negado verificacion selfie');
                            //  $query->where('evaluacions.estatus','!=','negado verificacion identidad');
                            //  $query->where('evaluacions.estatus','!=','negado archivos adicionales');
                            //  $query->where('evaluacions.estatus','!=','negado en la llamada');
                            //  $query->where('evaluacions.estatus','!=','negado en matriz de calculo');
                            //  $query->where('evaluacions.estatus','!=','negado en data credito');
                            //  $query->where('evaluacions.estatus','!=','aprobado');
                            //   $query->whereNull('evaluacions.estatus');

                            $query->where(function($query2) use ($request) {
                                $query2->orWhere('evaluacions.estatus','verificacion de identidad')
                                ->OrWhere('evaluacions.estatus','verificacion de datos adicionales')
                                ->OrWhere('evaluacions.estatus','verificacion de identidad')
                                ->OrWhere('evaluacions.estatus','Esperando revision data credito')
                                ->OrWhere('evaluacions.estatus','Esperando revision extractos bancarios')
                                ->OrWhere('evaluacions.estatus','verificacion de selfie')
                                ->OrWhere('evaluacions.estatus','')
                                ->OrWhere('evaluacions.estatus','esperando selfies nuevas')
                                ->OrWhere('evaluacions.estatus','esperando certificado bancario')
                                ->OrWhere('evaluacions.estatus','esperando certificacion laboral')
                                ->OrWhere('evaluacions.estatus','esperando extracto bancario')
                                ->OrWhere('evaluacions.estatus','pendiente')
                                ->OrWhere('evaluacions.estatus','esperando desprendible de nomina');




                            });




                        }

                        if($request->estatus && trim($request->estatus)=='no posee'){
                            $query->whereNull('evaluacions.estatus');
                        }
         }

                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('calculadoras.estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('evaluacions.estatus','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                    });
                    if($request->until && $request->since){
                        $d =  $request->since.' 00:00:00';
                        $h =  $request->until.' 23:00:00';
                        // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                        $query->where('calculadoras.created_at','>=', date("Y-m-d H:i:s", strtotime($d)));
                        $query->where('calculadoras.created_at','<=', date("Y-m-d H:i:s", strtotime($h)));
                    }


            }else{
                // $query->where('evaluacions.estatus','!=','negado');
                $query->where(function($query2) use ($request) {
                    $query2->orWhere('calculadoras.estatus','abierto')
                    ->OrWhere('calculadoras.estatus','aprobado')
                    ->OrWhere('calculadoras.estatus','pagado')
                    ->OrWhere('calculadoras.estatus','novado')
                        ->OrWhere('calculadoras.estatus','pendiente de novacion')
                        ->OrWhere('calculadoras.estatus','moroso')
                        ->OrWhere('calculadoras.estatus','restructurado')
                    ->OrWhere('calculadoras.estatus','castigado');
                });

                if($request->estatus){
                    if($request->estatus && trim($request->estatus)=='negado'){

                     $query->where(function($query2) use ($request) {
                         $query2->orWhere('evaluacions.estatus','negado verificacion selfie')
                         ->OrWhere('evaluacions.estatus','negado verificacion identidad')
                         ->OrWhere('evaluacions.estatus','negado archivos adicionales')
                         ->OrWhere('evaluacions.estatus','negado en la llamada')
                         ->OrWhere('evaluacions.estatus','negado en matriz de calculo')
                         ->OrWhere('evaluacions.estatus','negado en extractos bancarios')
                         ->OrWhere('evaluacions.estatus','negado')
                         ->OrWhere('evaluacions.estatus','negado en data credito');

                     });

                    }
                    if($request->estatus && trim($request->estatus)=='aprobado'){

                     $query->where(function($query2) use ($request) {
                         $query2->orWhere('evaluacions.estatus','aprobado');


                     });

                    }
                    if($request->estatus && trim($request->estatus)=='pendiente'){

                        // $query->where('evaluacions.estatus','!=','negado verificacion selfie');
                        //  $query->where('evaluacions.estatus','!=','negado verificacion identidad');
                        //  $query->where('evaluacions.estatus','!=','negado archivos adicionales');
                        //  $query->where('evaluacions.estatus','!=','negado en la llamada');
                        //  $query->where('evaluacions.estatus','!=','negado en matriz de calculo');
                        //  $query->where('evaluacions.estatus','!=','negado en data credito');
                        //  $query->where('evaluacions.estatus','!=','aprobado');
                        //  $query->whereNull('evaluacions.estatus');

                        $query->where(function($query2) use ($request) {
                            $query2->orWhere('evaluacions.estatus','verificacion de identidad')
                            ->OrWhere('evaluacions.estatus','verificacion de datos adicionales')
                            ->OrWhere('evaluacions.estatus','verificacion de identidad')
                            ->OrWhere('evaluacions.estatus','Esperando revision data credito')
                            ->OrWhere('evaluacions.estatus','Esperando revision extractos bancarios')
                            ->OrWhere('evaluacions.estatus','verificacion de selfie')
                            ->OrWhere('evaluacions.estatus','')
                            ->OrWhere('evaluacions.estatus','esperando selfies nuevas')
                            ->OrWhere('evaluacions.estatus','esperando certificado bancario')
                            ->OrWhere('evaluacions.estatus','esperando certificacion laboral')
                            ->OrWhere('evaluacions.estatus','esperando extracto bancario')
                            ->OrWhere('evaluacions.estatus','pendiente')
                            ->OrWhere('evaluacions.estatus','esperando desprendible de nomina');





                        });

                        // $query->where(function($query2) use ($request) {
                        //     $query2->orWhere('evaluacions.estatus','verificacion de identidad')
                        //     ->OrWhere('evaluacions.estatus','verificacion de datos adicionales')
                        //     ->OrWhere('evaluacions.estatus','verificacion de identidad')
                        //     ->OrWhere('evaluacions.estatus','Esperando revision data credito')
                        //     ->OrWhere('evaluacions.estatus','verificacion de selfie')
                        //     ->OrWhere('evaluacions.estatus','')
                        //     ->OrWhere('evaluacions.estatus','esperando selfies nuevas')
                        //     ->OrWhere('evaluacions.estatus','esperando certificado bancario')
                        //     ->OrWhere('evaluacions.estatus','esperando certificacion laboral')
                        //     ->OrWhere('evaluacions.estatus','esperando extracto bancario')
                        //     ->OrWhere('evaluacions.estatus','esperando desprendible de nomina');




                        // });




                    }
                    if($request->estatus && trim($request->estatus)=='no posee'){
                        $query->whereNull('evaluacions.estatus');
                    }
     }

                if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.created_at','>=', date("Y-m-d H:i:s", strtotime($d)));
                    $query->where('calculadoras.created_at','<=', date("Y-m-d H:i:s", strtotime($h)));
                }
            }

        //  ->with('user')
         $result = $query->orderBy('calculadoras.id','desc')->paginate($per_page);
            $response = $result;

        if($result->isEmpty()){
            $response = [
                'data' => [],
                'total' => 0,
                'msj' => 'No se encontraron registros.',
            ];
            return response()->json($response, 200);
        }
        return response()->json($response, 200);
    }catch (\Exception $e) {
        Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
        ], 500);
    }
    }

    public function get(Request $request){
        try{
            $result = Evaluacion::where('id','=',$request->id)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No se encontraron registros.',
                ], 200);
            }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function getTipo(Request $request){
        try{
            $result = Evaluacion::where('tipo','=',$request->tipo)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No se encontraron registros.',
                ], 200);
            }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function store(Request $request){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $req = json_decode($request->getContent(), true);
            $existe = Evaluacion::where("idSolicitudFk", $request->idSolicitudFk)->where("idUserFk",$request->idUserFk)->count();
            if($existe == 0){
               $result = Evaluacion::create([
                'idSolicitudFk'    => $request->idSolicitudFk,
                'idUserFk'     => $request->idUserFk,
                'estatus'    => $request->estatus

                ]);
            }else{
                $result = Evaluacion::where("idSolicitudFk", $request->idSolicitudFk)->where("idUserFk",$request->idUserFk)->first();
            }

            DB::commit(); // Guardamos la transaccion
            return response()->json($result,201);
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

    public function update(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->selfie  = $request->selfie;
            $config->comentario_selfie=$request->comentario_selfie;
            $config->identidad=$request->identidad;
            $config->comentario_identidad=$request->comentario_identidad;
            $config->adicionales=$request->adicionales;
            $config->llamada=$request->llamada;
            $config->comentario_llamada=$request->comentario_llamada;
            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function updateSelfie(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->selfie  = $request->selfie;
            $config->comentario_selfie=$request->comentario_selfie;

            $config->estatus=$request->estatus;

            $config->save();

            $usuarios=User::where('id',$config->idUserFk)->first();
            $contenido=Correos::where('pertenece','denegado')->first();

         if($contenido->estatus=='activo' && $request->selfie !='aprobado'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];

             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                $msj->from("no-reply@creditospanda.com","Créditos Panda");
                 $msj->subject($name.',préstamo denegado');
                 $msj->to($email);
              });
         }

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function updateBalance(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->balance  = $request->balance;
            if($request->balance=='aprobado' && trim($config->estatus)=='aprobado' ){

            }else{
                if(trim($config->estatus)=='verificacion de selfie'){
                    $config->estatus=$request->estatus;
                }else if($request->balance=='aprobado' && trim($config->estatus)=='negado en matriz de calculo'){
                    $config->estatus=$request->estatus;
                }

            }

            $config->save();

            if($config->balance=='rechazado' && $config->notificadoRechazado==0){

                $usuarios=User::where('id',$config->idUserFk)->first();
                $contenido=Correos::where('pertenece','denegado')->first();

             if($contenido->estatus=='activo'){

                 if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                     $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                         if($credito->tipoCredito=="m"){
                             $tipo="Panda meses";
                         }else{
                             $tipo="Panda dias";
                         }
                         $monto=$credito->totalPagar;
                         $numerCredito=$credito->numero_credito;
                         $expedicion=$credito->created_at;
                 }else{
                     $numerCredito='No posee';
                     $monto='No posee';
                     $expedicion='No posee';
                     $tipo='No posee';

                 }
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
                 "{{Ncredito}}",
                 "{{Monto}}",
                 "{{Expedicion}}",
                 "{{TipoCredito}}",

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

                 );

                 $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




                 $data = [
                     'Nombre' => $name,
                     'Email'=>$email,
                     'Apellido'=>$last_name,
                     'Cedula'=>$cedula,
                     'Contenido'=>$cntent2,

                     ];

                 Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                    $msj->from("no-reply@creditospanda.com","Créditos Panda");
                     $msj->subject($name.',préstamo denegado');
                     $msj->to($email);
                  });
             }



                $config->notificadoRechazado=1;
                $config->save();
            }

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function comentarioSelfie(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->usuarioResSelfie  = $request->usuario;
            $config->comentario_selfie=$request->comentario_selfie;
            $config->fechaComentSelfi=$request->fecha;


            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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
    public function comentarioIdentidad(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->usuarioResIdentidad  = $request->usuario;
            $config->comentario_identidad=$request->comentario_identidad;
            $config->fechaComentIdentidad=$request->fecha;


            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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
    public function comentarioAdicional(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->usuarioResAdicional  = $request->usuario;
            $config->comentario_adicionales=$request->comentario_adicionales;
            $config->fechaComentAdicional=$request->fecha;


            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function comentarioLamada(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->usuarioResLlamada  = $request->usuario;
            $config->comentario_llamada=$request->comentario_llamada;
            $config->fechaComentLlamada=$request->fecha;


            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function updateIdentidad(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->identidad  = $request->identidad;
            $config->comentario_identidad=$request->comentario_identidad;
            $config->estatus=$request->estatus;

            $config->save();
            if($config->identidad=='rechazado'){

                $usuarios=User::where('id',$config->idUserFk)->first();
                $contenido=Correos::where('pertenece','denegado')->first();

             if($contenido->estatus=='activo'){

                 if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                     $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                         if($credito->tipoCredito=="m"){
                             $tipo="Panda meses";
                         }else{
                             $tipo="Panda dias";
                         }
                         $monto=$credito->totalPagar;
                         $numerCredito=$credito->numero_credito;
                         $expedicion=$credito->created_at;
                 }else{
                     $numerCredito='No posee';
                     $monto='No posee';
                     $expedicion='No posee';
                     $tipo='No posee';

                 }
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
                 "{{Ncredito}}",
                 "{{Monto}}",
                 "{{Expedicion}}",
                 "{{TipoCredito}}",

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

                 );

                 $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




                 $data = [
                     'Nombre' => $name,
                     'Email'=>$email,
                     'Apellido'=>$last_name,
                     'Cedula'=>$cedula,
                     'Contenido'=>$cntent2,

                     ];

                 Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                    $msj->from("no-reply@creditospanda.com","Créditos Panda");
                     $msj->subject($name.',préstamo denegado');
                     $msj->to($email);
                  });
             }

            }

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function updateAdicionales(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->adicionales  = $request->adicionales;
            $config->comentario_adicionales=$request->comentario_adicionales;
            $config->estatus=$request->estatus;

            $config->save();
if($request->adicionales !='aprobado'){
                $usuarios=User::where('id',$config->idUserFk)->first();
                $contenido=Correos::where('pertenece','denegado')->first();

             if($contenido->estatus=='activo'){

                 if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                     $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                         if($credito->tipoCredito=="m"){
                             $tipo="Panda meses";
                         }else{
                             $tipo="Panda dias";
                         }
                         $monto=$credito->totalPagar;
                         $numerCredito=$credito->numero_credito;
                         $expedicion=$credito->created_at;
                 }else{
                     $numerCredito='No posee';
                     $monto='No posee';
                     $expedicion='No posee';
                     $tipo='No posee';

                 }
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
                 "{{Ncredito}}",
                 "{{Monto}}",
                 "{{Expedicion}}",
                 "{{TipoCredito}}",

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

                 );

                 $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




                 $data = [
                     'Nombre' => $name,
                     'Email'=>$email,
                     'Apellido'=>$last_name,
                     'Cedula'=>$cedula,
                     'Contenido'=>$cntent2,

                     ];

                 Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                    $msj->from("no-reply@creditospanda.com","Créditos Panda");
                     $msj->subject($name.',préstamo denegado');
                     $msj->to($email);
                  });
             }

            }

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function updateLlamada(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $config = Evaluacion::find($request->id);
            $config->llamada  = $request->llamada;
            $config->comentario_llamada=$request->comentario_llamada;
            $config->estatus=$request->estatus;

            $config->save();

            DB::commit(); // Guardamos la transaccion

            return response()->json($config,200);

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

    public function delete(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $config = Evaluacion::find($request->id);
            $config->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Evaluacion eliminada",200);
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

    public function showSolicitud(Request $request){
        try{
            $result = Calculadora::where('id','=',$request->id)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No se encontraron registros.',
                ], 200);
            }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }


    public function solicitarSelfie(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','requerir selfie')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
            $msjAdmin = '<p>Se solicito las selfies del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
            $infoAdmin =[
                'Contenido'=>$msjAdmin,
              ];
            Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                $msj->subject('Notificacion de solicitud de selfies');
                $msj->to('info@creditospanda.com');
             });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',falta un paso para que tu solicitud de préstamo sea revisada');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }
    public function solicitarAdicional(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','requerir Info adicional')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito documentos adicionales del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de adicionales');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',falta un paso para que tu solicitud de préstamo sea revisada');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }


    public function solicitarCertificado(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','Requerir Certificado Bancario')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito Certificado bancario del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de Certificado bancario');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',Requerimos tu certificado bancario');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }


    public function solicitarCertificadoLaboral(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','Requerir certificación laboral')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito Certificacion laboral del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de adicionales');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',Requerimos tu certificacion laboral');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function solicitarDesprendible(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','Requerir desprendible de nomina')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito Desprendible de nomina del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de adicionales');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',Requerimos tu desprendible de nomina');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function solicitarExtracto(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','Requerir extracto bancario')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito Extracto bancario del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de adicionales');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',Requerimos tu extracto bancario');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function solicitarVerificacion(Request $request){
        try{
            $result = User::where('id','=',$request->idUsuario)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No existe el usuario.',
                ], 200);
            }


            $usuarios=User::where('id',$request->idUsuario)->first();
            $contenido=Correos::where('pertenece','Verificacion Reportado')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];
                 $msjAdmin = '<p>Se solicito Verificacion Reportado del usuario: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                 $infoAdmin =[
                     'Contenido'=>$msjAdmin,
                   ];
                 Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                     $msj->subject('Notificacion de solicitud de adicionales');
                     $msj->to('info@creditospanda.com');
                  });
             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                 $msj->subject($name.',Requerimos tu verificacion de reportado');
                 $msj->to($email);
              });
         }

            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function obtenerDatos(Request $request){

        if($request->fechaNacimiento){
            // $pizza  = "porción1 porción2 porción3 porción4 porción5 porción6";
           $fecha = explode("-", $request->fechaNacimiento);
           $ano=$fecha[0];
           $mes=$fecha[1];
           $dia=$fecha[2];
           $fecha_completa=$dia.'-'.$mes.'-'.$ano;

        }


 //    	$request->nCedula="1032440675";
       // $fecha_completa="15-12-2008";
         $usuario=User::where('n_document',$request->nCedula)->first();
         // var_dump($usuario);
         $basica=Basica::where('idUserFk',$usuario->id)->first();
                      // NOMBRES DE  UN CIUDADANO

                    $data = array(
                       "documentType" => "CC",
                       "documentNumber" => $request->nCedula
                   );

                      $push=http_build_query($data);
                      $curl = curl_init();

                      curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.misdatos.com.co/api/co/consultarNombres",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $push,
                      CURLOPT_HTTPHEADER => array(
                          "Authorization: 0q1tgq7hdctxaqgapwmmue15foyemzmfh0ldr9uy110oft9n"
                      ),
                      ));

                      $nombreCiudadano = curl_exec($curl);

                      curl_close($curl);



         if(json_decode($nombreCiudadano)->statusCode=="404"){
               return response()->json([
                   'result' => [],
                   'estatus'=>0,
                   'msj' => 'No se encontraron registros.',
               ], 200);
           }else{

       //DOCUMENTOS EN TRAMITES

                    $data = array(
                       "documentType" => "CC",
                       "documentNumber" => $request->nCedula
                   );

                      $push=http_build_query($data);
                      $curl = curl_init();

                      curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.misdatos.com.co/api/co/registraduria/consultarDocumentoEnTramite",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $push,
                      CURLOPT_HTTPHEADER => array(
                          "Authorization: 0q1tgq7hdctxaqgapwmmue15foyemzmfh0ldr9uy110oft9n"
                      ),
                      ));

                      $documentosTramites = curl_exec($curl);


                      if(json_decode($documentosTramites)->data->documentoEnTramite==''){
                          $documentos=1;
                      }else{
                          $documento=0;
                      }



                   //// AFILIACIONES

                    $data = array(
                       "documentType" => "CC",
                       "documentNumber" => $request->nCedula,
                       "date" =>$fecha_completa
                   );


                      $push=http_build_query($data);
                      $curl = curl_init();

                      curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.misdatos.com.co/api/co/afiliaciones",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $push,
                      CURLOPT_HTTPHEADER => array(
                          "Authorization: 0q1tgq7hdctxaqgapwmmue15foyemzmfh0ldr9uy110oft9n"
                      ),
                      ));

                      $afiliacion = curl_exec($curl);

                      curl_close($curl);
                   //    echo $afiliacion;

                      if($basica->genero=="Femenino"){
                          $gen="F";
                      }else{
                          $gen="M";
                      }

                      if($gen==json_decode($afiliacion)->sexo){
                          $genero=1;
                      }else{
                          $genero=0;
                      }

                      if($basica->eps==json_decode($afiliacion)->eps->eps){
                          $eps=1;
                      }else{
                          $eps=0;
                      }

                      if(json_decode($afiliacion)->eps->regimen=="Contributivo"){
                          $regimen=1;
                      }else{
                          $regimen=0;
                      }

                        if(json_decode($afiliacion)->eps->estadoAfiliacion=="Activo"){
                          $estadoAfiliado=1;
                      }else{
                          $estadoAfiliado=0;
                      }

                        if(json_decode($afiliacion)->eps->tipoAfiliado=="COTIZANTE"){
                          $tipoAfiliado=1;
                      }else{
                          $tipoAfiliado=0;
                      }

                      if($genero==1 && $eps==1 && $regimen==1 && $estadoAfiliado==1 && $tipoAfiliado==1){
                          $estatus=1;
                      }else{
                          $estatus=0;
                      }


                      if(empty($documentosTramites) && empty($nombreCiudadano) && empty($afiliacion)){
                       return response()->json([
                           'result' => [],
                           'msj' => 'No se encontraron registros.',
                       ], 200);
                   }else{
                       return response()->json([
                           'result' => [
                               "estatus"=>$estatus,
                               "documentosTramites" => $documentosTramites,
                               "nombreCiudadano" => $nombreCiudadano,
                                "afiliacion" => $afiliacion,
                           ],
                           'msj' => 'Hay registro.',
                       ], 200);
                   }

       }


   }
   public function updateDataCredito(Request $request){
    try{
        DB::beginTransaction(); // Iniciar transaccion de la base de datos

        $config = Evaluacion::find($request->id);
        $config->data_credito  = $request->data_credito;
        $config->estatus=$request->estatus;

        $config->save();

        if($config->data_credito=='rechazado' && $config->notificadoRechazado==0){

            $usuarios=User::where('id',$config->idUserFk)->first();
            $contenido=Correos::where('pertenece','denegado')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];

             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                $msj->from("no-reply@creditospanda.com","Créditos Panda");
                 $msj->subject($name.',préstamo denegado');
                 $msj->to($email);
              });
         }



            $config->notificadoRechazado=1;
            $config->save();
        }

        DB::commit(); // Guardamos la transaccion

        return response()->json($config,200);

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

public function obtenerDatosKonivin(Request $request){
    if($request->fechaNacimiento){
        // $pizza  = "porción1 porción2 porción3 porción4 porción5 porción6";
    $fecha = explode("-", $request->fechaNacimiento);
    $ano=$fecha[0];
    $mes=$fecha[1];
    $dia=$fecha[2];
    $fecha_completa=$ano.'-'.$mes.'-'.$dia;

    }


    //    	$request->nCedula="1032440675";
    // $fecha_completa="15-12-2008";
    $usuario=User::where('n_document',$request->nCedula)->first();
    // var_dump($usuario);
    $basica=Basica::where('idUserFk',$usuario->id)->first();
    $evaluacion = Evaluacion::find($request->idEvaluacion);

    if($evaluacion->informacion_identidad){
        $rest =json_decode($evaluacion->informacion_identidad);

        $ruaf_ = json_decode($rest->result->ruaf);
        $ofac_ = json_decode($rest->result->ofac);
        $estadoCedula_ = json_decode($rest->result->estadoCedula);
        $antecedentes_ = json_decode($rest->result->antecedentes);

        if($ruaf_== false || $ruaf_->fuenteFallo == 'SI'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=46784765&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $RUAF = curl_exec($curl);

            curl_close($curl);
        }else{
            $RUAF = $rest->result->ruaf;
        }
        if($ofac_== false || $ofac_->fuenteFallo == 'SI'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=28600607&icf=01&thy=CO&klm=".$request->nCedula."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $OFAC = curl_exec($curl);

            curl_close($curl);
        }else{
            $OFAC = $rest->result->ofac;
        }
        if($estadoCedula_== false || $estadoCedula_->fuenteFallo == 'SI'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=91891024&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $estadoCedula = curl_exec($curl);

            curl_close($curl);
        }else{
            $estadoCedula = $rest->result->estadoCedula;
        }
        if($antecedentes_== false || $antecedentes_->fuenteFallo == 'SI'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=42156544&icf=01&thy=CO&klm=".$request->nCedula."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $antecedentes = curl_exec($curl);

            curl_close($curl);
        }else{
            $antecedentes = $rest->result->antecedentes;
        }

        $res = [
            'result' => [
                "ruaf"=>$RUAF,
                "ofac" => $OFAC,
                "estadoCedula" => $estadoCedula,
                "antecedentes" => $antecedentes,
            ],
            'msj' => 'Hay registro.',
        ];
        $evaluacion->informacion_identidad = json_encode($res);
        $evaluacion->save();
    }else{
        // RUAF
        $data = array(
            "documentType" => "CC",
            "documentNumber" => $request->nCedula
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=46784765&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $RUAF = curl_exec($curl);

        curl_close($curl);
        //OFAC
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=28600607&icf=01&thy=CO&klm=".$request->nCedula."",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $OFAC = curl_exec($curl);

        curl_close($curl);
        //ESTADO CEDULA
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=91891024&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $estadoCedula = curl_exec($curl);

        curl_close($curl);
        //ANTECEDENTES POLICIALES
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://test.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=cr3d1t0s2020&jor=42156544&icf=01&thy=CO&klm=".$request->nCedula."",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $antecedentes = curl_exec($curl);

        curl_close($curl);
        $res = [
            'result' => [
                "ruaf"=>$RUAF,
                "ofac" => $OFAC,
                "estadoCedula" => $estadoCedula,
                "antecedentes" => $antecedentes,
            ],
            'msj' => 'Hay registro.',
        ];
        $evaluacion->informacion_identidad = json_encode($res);
        $evaluacion->save();
    }
    return response()->json($res, 200);
}

public function estadosSolicitudes(Request $request){
    try{
        DB::beginTransaction(); // Iniciar transaccion de la base de datos

        $config = Evaluacion::find($request->id);
        $config->estatus  = $request->estatus;


        $config->save();



        DB::commit(); // Guardamos la transaccion

        return response()->json($config,200);

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
public function updateCalculos(Request $request){
    try{
        DB::beginTransaction(); // Iniciar transaccion de la base de datos

        $config = Evaluacion::find($request->id);
        $config->calculoIngreso  = json_encode($request->calculoIngreso);
        $config->gastoMonetario  = json_encode($request->gastoMonetario);

        $config->save();

        DB::commit(); // Guardamos la transaccion

        return response()->json("actualizacion de calculos completa",200);

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

public function exportExcel(Request $request)
{
    $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk');
    $query->leftJoin('evaluacions', 'calculadoras.id', '=', 'evaluacions.idSolicitudFk')
     ->leftJoin('basicas', 'basicas.idUserFk', '=', 'calculadoras.idUserFk')
     ->leftJoin('referencias', 'referencias.idUserFk', '=', 'calculadoras.idUserFk')
     ->leftJoin('financieras', 'financieras.idUserFk', '=', 'calculadoras.idUserFk')
     ->leftJoin('dc_analisis_ingresos', 'dc_analisis_ingresos.idEvaluacion', '=', 'evaluacions.id')
     ->leftJoin('dc_indicador_pagos', 'dc_indicador_pagos.idEvaluacion', '=', 'evaluacions.id')
     ->leftJoin('dc_por_sectors', 'dc_por_sectors.idEvaluacion', '=', 'evaluacions.id')
    ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','calculadoras.created_at as fecha_solicitud',
    'evaluacions.*','evaluacions.estatus as estatus_evaluacion', 'evaluacions.id as id_evaluacion',
    'users.*','users.id as id_usuario', 'basicas.*','referencias.*','financieras.*',
    'dc_analisis_ingresos.*','dc_indicador_pagos.*','dc_por_sectors.*');
    $query->where(function($query2) use ($request) {
        $query2->orWhere('calculadoras.estatus','abierto')
        ->OrWhere('calculadoras.estatus','aprobado')
        ->OrWhere('calculadoras.estatus','pagado')
        ->OrWhere('calculadoras.estatus','novado')
            ->OrWhere('calculadoras.estatus','pendiente de novacion')
            ->OrWhere('calculadoras.estatus','moroso')
            ->OrWhere('calculadoras.estatus','restructurado')
        ->OrWhere('calculadoras.estatus','castigado');
    });
    $result = $query->orderBy('calculadoras.id','desc')->get();
    foreach ($result as $key => $value) {
        $financiera = Financiera::where('idUserFk',$value->idUserFk)->first();
            if(trim($value->estatus_evaluacion)=='negado verificacion selfie'
                || trim($value->estatus_evaluacion)=='negado verificacion identidad'
                || trim($value->estatus_evaluacion)=='negado archivos adicionales'
                || trim($value->estatus_evaluacion)=='negado en la llamada'
                || trim($value->estatus_evaluacion)=='negado en matriz de calculo'
                || trim($value->estatus_evaluacion)=='negado en data credito'
                || trim($value->estatus_evaluacion)=='negado en extractos bancarios'

            ){

             $estatus_evaluacion_final = "negado";

            }else
            if(trim($value->estatus_evaluacion)=='aprobado'){

                $estatus_evaluacion_final = "aprobado";

            }else
            if(trim($value->estatus_evaluacion)=='verificacion de identidad'
                || trim($value->estatus_evaluacion)=='verificacion de datos adicionales'
                || trim($value->estatus_evaluacion)=='Esperando revision data credito'
                || trim($value->estatus_evaluacion)=='Esperando revision extracto bancarios'
                || trim($value->estatus_evaluacion)=='verificacion de selfie'
                || trim($value->estatus_evaluacion)==''
                || trim($value->estatus_evaluacion)=='esperando selfies nuevas'
                || trim($value->estatus_evaluacion)=='esperando certificado bancario'
                || trim($value->estatus_evaluacion)=='esperando certificacion laboral'
                || trim($value->estatus_evaluacion)=='esperando extracto bancario'
                || trim($value->estatus_evaluacion)=='verificacion de datos adicionales'
                || trim($value->estatus_evaluacion)=='esperando desprendible de nomina'
            ){
                $estatus_evaluacion_final = "pendiente";

            }else
            if(!$value->estatus_evaluacion || $value->estatus_evaluacion==NULL){
                $estatus_evaluacion_final = "no posee";
            }

            $calculo_ingreso = json_decode($value->calculoIngreso);
            $gasto_monetario = json_decode($value->gastoMonetario);

            $result[$key]['estatus_evaluacion_final']=$estatus_evaluacion_final;
            $result[$key]['calculo_ingreso']=$calculo_ingreso;
            $result[$key]['gasto_monetario']=$gasto_monetario;
    }
    $params =  [

        'evaluaciones'      =>  $result,
        'view'           => 'reports.excel.evaluaciones'
    ];
//   return response()->json($result, 200);
    return Excel::download(
      new ViewExport (
          $params
      ),
      'usersAdmin.xlsx'
  );


}
public function updateExtractoBancario(Request $request){
    try{
        DB::beginTransaction(); // Iniciar transaccion de la base de datos

        $config = Evaluacion::find($request->id);
        $config->extracto_bancario  = $request->extracto_bancario;
        $config->estatus=$request->estatus;

        $config->save();

        if($config->extracto_bancario=='rechazado' && $config->notificadoRechazado==0){

            $usuarios=User::where('id',$config->idUserFk)->first();
            $contenido=Correos::where('pertenece','denegado')->first();

         if($contenido->estatus=='activo'){

             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                     if($credito->tipoCredito=="m"){
                         $tipo="Panda meses";
                     }else{
                         $tipo="Panda dias";
                     }
                     $monto=$credito->totalPagar;
                     $numerCredito=$credito->numero_credito;
                     $expedicion=$credito->created_at;
             }else{
                 $numerCredito='No posee';
                 $monto='No posee';
                 $expedicion='No posee';
                 $tipo='No posee';

             }
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
             "{{Ncredito}}",
             "{{Monto}}",
             "{{Expedicion}}",
             "{{TipoCredito}}",

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

             );

             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




             $data = [
                 'Nombre' => $name,
                 'Email'=>$email,
                 'Apellido'=>$last_name,
                 'Cedula'=>$cedula,
                 'Contenido'=>$cntent2,

                 ];

             Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                $msj->from("no-reply@creditospanda.com","Créditos Panda");
                 $msj->subject($name.',préstamo denegado');
                 $msj->to($email);
              });
         }



            $config->notificadoRechazado=1;
            $config->save();
        }

        DB::commit(); // Guardamos la transaccion

        return response()->json($config,200);

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

    public function estadisticas(Request $request){
        set_time_limit(120);
        try {


        if($request->typeFilter === 'actualMonth' || $request->typeFilter === 'month'){

            //RESUMEN

            $aprobadosFormulario = Calculadora::whereIn('estatus', ['pendiente de novacion', 'aprobado','novado', 'moroso', 'restructurado', 'abierto'])
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();
            $negadoFormulario=Calculadora::where('estatus','negado')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();
            $incompletosFormulario=Calculadora::where('estatus','incompleto')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();
            $aprobadoInterno=Evaluacion::where('estatus','aprobado')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();
            $negadoInterno=Evaluacion::where('estatus','!=','aprobado')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();
            $desembolsados = Calculadora::whereNotNull('fechaDesembolso')
                ->whereIn('estatus', ['pagado', 'castigado', 'novado', 'moroso', 'restructurado', 'abierto'])
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();

            $faltaEvaluacion=calculadora::leftJoin('evaluacions', 'calculadoras.id', '=', 'evaluacions.idSolicitudFk')
                ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','calculadoras.created_at as fecha_solicitud','evaluacions.*','evaluacions.estatus as estatus_evaluacion')
                ->where('evaluacions.estatus',NULL)
                ->whereRaw("DATE_FORMAT(calculadoras.created_at, '%Y-%m') = ?", [$request->since])
                ->count();


            $totalVisitantes = RecordHistory::whereRaw("DATE_FORMAT(fecha_registro, '%Y-%m') = ?", [$request->since])->count();

            $users = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
                ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
                ->where('users.estatus','activo')->with('roles')
                ->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m') = ?", [$request->since])
                ->whereHas('roles',function($q){
                    $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
                })->get();
            $totalRegistrados = $users->count();
            $totalValidado = $users->where('estatus_credito', '!=','incompleto')->count();
            $totalIncompleto = $users->where('estatus_credito', 'incompleto')->count();

            $usuarios=User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m') = ?", [$request->since])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->get();
            $contador=0;

            foreach ($usuarios->whereIn('estatus_credito', ['pagado', 'castigado']) as $value) {
                $contarCreditos=Calculadora::where('idUserFk',$value->id)
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                    ->count();

                if($contarCreditos>1){
                    $contador=$contador+1;
                }
            }

            $creditosCerrados=Calculadora::where('estatus','pagado')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
                ->count();

            // $percentajeRetorno=$creditosCerrados/$contador*100;

            if($aprobadoInterno > 0){
                $percentajeAprobacion=$aprobadosFormulario/$aprobadoInterno*100;
            }else{
                $percentajeAprobacion=0;
            }

            //CREDITOS DESEMBOSADOS
            $desembolsadosPm = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'm')
                ->whereRaw("DATE_FORMAT(fechaDesembolso, '%Y-%m') = ?", [$request->since])
                ->count();
            $totalDesembolsadosPm = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'm')
                ->whereRaw("DATE_FORMAT(fechaDesembolso, '%Y-%m') = ?", [$request->since])
                ->get();
            $montoA = 0;
            foreach($totalDesembolsadosPm as $value){
                $co= ContraOferta::where('idCalculadoraFk',$value->id)->orderBy('id','desc')->first();
                if($value->ofertaEnviada == 2){
                    $montoA = $montoA + $value->montoSolicitado;
                }else{
                    $montoA = $montoA + $co->montoAprobado;
                }
            }
            $totalDesembolsadosPm = $montoA;
            $desembolsadosPd = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'd')
                ->whereRaw("DATE_FORMAT(fechaDesembolso, '%Y-%m') = ?", [$request->since])
                ->count();
            $totalDesembolsadosPd = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'd')
                ->whereRaw("DATE_FORMAT(fechaDesembolso, '%Y-%m') = ?", [$request->since])
                ->get();
            $montoA = 0;
            foreach($totalDesembolsadosPd as $value){
                $co= ContraOferta::where('idCalculadoraFk',$value->id)->orderBy('id','desc')->first();
                if($value->ofertaEnviada == 2){
                    $montoA = $montoA + $value->montoSolicitado;
                }else{
                    $montoA = $montoA + $co->montoAprobado;
                }
            }
            $totalDesembolsadosPd = $montoA;

            $totalCreditosDesembolsados = $desembolsadosPm + $desembolsadosPd;
            $totalCapitalCreditosDesembolsados = $totalDesembolsadosPm + $totalDesembolsadosPd;

            //CREDITOS ABIERTOS
            $this->request = $request;
            $creditosAbiertos = Calculadora::where('estatus', 'abierto')
            // ->where('tipoCredito', 'm')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
            ->with(['pagos' => function($query){
                return $query->where('estatusPago', 'pendiente')
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$this->request->since]);
            }])
            ->get();

            $capitalpxpM = 0;
            $interespxpM = 0;
            $interesMoraPendientepxpM = 0;
            $plataformapxpM = 0;
            $aprobacionpxpM = 0;
            $ivapxpM = 0;
            $gastosCobranzapxpM = 0;
            $ivaGastosCobranzapxpM = 0;

            $capitalpxpD = 0;
            $interespxpD = 0;
            $interesMoraPendientepxpD = 0;
            $plataformapxpD = 0;
            $aprobacionpxpD = 0;
            $ivapxpD = 0;
            $gastosCobranzapxpD = 0;
            $ivaGastosCobranzapxpD = 0;

            foreach ($creditosAbiertos as $credito) {
                foreach ($credito->pagos as $pago) {
                    if($credito->tipoCredito === 'm'){
                        $capitalpxpM = $capitalpxpM + $pago->capital;
                        $interespxpM = $interespxpM + $pago->intereses;
                        $plataformapxpM = $plataformapxpM + $pago->plataforma;
                        $aprobacionpxpM = $aprobacionpxpM + $pago->aprobacionRapida;
                        $ivapxpM = $ivapxpM + $pago->iva;
                        $interesMoraPendientepxpM = $interesMoraPendientepxpM + $pago->interesMoraPendiente;
                        $gastosCobranzapxpM = $gastosCobranzapxpM + $pago->gastosCobranza;
                        $ivaGastosCobranzapxpM = $ivaGastosCobranzapxpM + $pago->ivaGastosCobranza;
                    }

                    if($credito->tipoCredito === 'd'){
                        $capitalpxpD = $capitalpxpD + $pago->capital;
                        $interespxpD = $interespxpD + $pago->intereses;
                        $plataformapxpD = $plataformapxpD + $pago->plataforma;
                        $aprobacionpxpD = $aprobacionpxpD + $pago->aprobacionRapida;
                        $ivapxpD = $ivapxpD + $pago->iva;
                        $interesMoraPendientepxpD = $interesMoraPendientepxpD + $pago->interesMoraPendiente;
                        $gastosCobranzapxpD = $gastosCobranzapxpD + $pago->gastosCobranza;
                        $ivaGastosCobranzapxpD = $ivaGastosCobranzapxpD + $pago->ivaGastosCobranza;
                    }
                }
            }

            $totalPxpM = $capitalpxpM + $interespxpM + $plataformapxpM + $aprobacionpxpM + $ivapxpM + $interesMoraPendientepxpM + $gastosCobranzapxpM + $ivaGastosCobranzapxpM;
            $totalPxpD = $capitalpxpD + $interespxpD + $plataformapxpD + $aprobacionpxpD + $ivapxpD + $interesMoraPendientepxpM + $gastosCobranzapxpM + $ivaGastosCobranzapxpM;
            $totalPxp = $totalPxpM + $totalPxpD;

            $creditosAbiertosPm = $creditosAbiertos->where('tipoCredito', 'm')->count();
            $creditosAbiertosPd = $creditosAbiertos->where('tipoCredito', 'd')->count();

            //CREDITOS MOROSOS
            $creditosMorosos = Calculadora::where('estatus', 'moroso')
            // ->where('tipoCredito', 'm')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->since])
            ->with(['pagos' => function($query){
                return $query->where('estatusPago', 'pendiente')
                    /* ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$this->request->since]) */;
            }])
            ->get();

            $diasMora15Pm = 0;
            $diasMora30Pm = 0;
            $diasMora60Pm = 0;
            $diasMora90Pm = 0;
            $diasMora120Pm = 0;
            $diasMora180Pm = 0;
            $diasMora360Pm = 0;
            $diasMoraMayor360Pm = 0;
            $diasMoraPm = 0;

            $diasMora15Pd = 0;
            $diasMora30Pd = 0;
            $diasMora60Pd = 0;
            $diasMora90Pd = 0;
            $diasMora120Pd = 0;
            $diasMora180Pd = 0;
            $diasMora360Pd = 0;
            $diasMoraMayor360Pd = 0;
            $diasMoraPd = 0;

            $totalMora15Pd = 0;
            $totalMora30Pd = 0;
            $totalMora60Pd = 0;
            $totalMora90Pd = 0;
            $totalMora120Pd = 0;
            $totalMora180Pd = 0;
            $totalMora360Pd = 0;
            $totalMoraMayor360Pd = 0;
            $totalMora = 0;
            $totalMoraPm = 0;
            $totalMoraPd = 0;
            $totalDiasMora = 0;
            $totalCreditosMorososPm = $creditosMorosos->where('tipoCredito', 'm')->count();
            $totalCreditosMorososPd = $creditosMorosos->where('tipoCredito', 'd')->count();
            $totalCreditosMorosos = $totalCreditosMorososPm + $totalCreditosMorososPd;


            foreach ($creditosMorosos as $creditoMoroso) {
                foreach ($creditoMoroso->pagos as $pago) {
                    if($creditoMoroso->tipoCredito === 'm'){
                        $diasMoraPm = $diasMoraPm + $pago->diasMora;
                        if($pago->diasMora > 0 && $pago->diasMora <= 15 ){
                            $diasMora15Pm = $diasMora15Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 15 && $pago->diasMora <= 30 ){
                            $diasMora30Pm = $diasMora30Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 30 && $pago->diasMora <= 60 ){
                            $diasMora60Pm = $diasMora60Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 60 && $pago->diasMora <= 90 ){
                            $diasMora90Pm = $diasMora90Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 90 && $pago->diasMora <= 120 ){
                            $diasMora120Pm = $diasMora120Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 120 && $pago->diasMora <= 180 ){
                            $diasMora180Pm = $diasMora180Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 180 && $pago->diasMora <= 360 ){
                            $diasMora360Pm = $diasMora360Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 360 ){
                            $diasMoraMayor360Pm = $diasMoraMayor360Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                    }
                    if($creditoMoroso->tipoCredito === 'd'){
                        $diasMoraPd = $diasMoraPd + $pago->diasMora;
                        if($pago->diasMora > 0 && $pago->diasMora <= 15 ){
                            $diasMora15Pd = $diasMora15Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 15 && $pago->diasMora <= 30 ){
                            $diasMora30Pd = $diasMora30Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 30 && $pago->diasMora <= 60 ){
                            $diasMora60Pd = $diasMora60Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 60 && $pago->diasMora <= 90 ){
                            $diasMora90Pd = $diasMora90Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 90 && $pago->diasMora <= 120 ){
                            $diasMora120Pd = $diasMora120Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 120 && $pago->diasMora <= 180 ){
                            $diasMora180Pd = $diasMora180Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 180 && $pago->diasMora <= 360 ){
                            $diasMora360Pd = $diasMora360Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($pago->diasMora > 360 ){
                            $diasMoraMayor360Pd = $diasMoraMayor360Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                    }
                }
            }

            $diasMoraPm = $totalCreditosMorososPm > 0 ? $diasMoraPm / $creditosMorosos->where('tipoCredito', 'm')->count() : 0;
            $diasMoraPd = $totalCreditosMorososPd > 0 ? $diasMoraPd / $creditosMorosos->where('tipoCredito', 'd')->count() : 0;
            $totalDiasMora = $diasMoraPm + $diasMoraPd;

            $totalMora15 = $diasMora15Pm + $diasMora15Pd;
            $totalMora30 = $diasMora30Pm + $diasMora30Pd;
            $totalMora60 = $diasMora60Pm + $diasMora60Pd;
            $totalMora90 = $diasMora90Pm + $diasMora90Pd;
            $totalMora120 = $diasMora120Pm + $diasMora120Pd;
            $totalMora180 = $diasMora180Pm + $diasMora180Pd;
            $totalMora360 = $diasMora360Pm + $diasMora360Pd;
            $totalMoraMayor360 = $diasMoraMayor360Pm + $diasMoraMayor360Pd;

            $totalMoraPm = $diasMora15Pm + $diasMora30Pm + $diasMora60Pm + $diasMora90Pm + $diasMora120Pm + $diasMora180Pm + $diasMora360Pm + $diasMoraMayor360Pm;
            $totalMoraPd = $diasMora15Pd + $diasMora30Pd + $diasMora60Pd + $diasMora90Pd + $diasMora120Pd + $diasMora180Pd + $diasMora360Pd + $diasMoraMayor360Pd;

            $totalMora = $totalMoraPm + $totalMoraPd;
            $totalUsuarioPC=$usuarios->whereIn('estatus_credito', ['pagado', 'castigado'])->count();
        }else
        if($request->typeFilter == 'betweenDate'){

            $aprobadosFormulario = Calculadora::whereIn('estatus', ['pendiente de novacion', 'pagado', 'castigado', 'aprobado', 'novado', 'moroso', 'restructurado', 'abierto'])
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();
            $negadoFormulario=Calculadora::where('estatus','negado')
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();
            $incompletosFormulario=Calculadora::where('estatus','incompleto')
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();
            $aprobadoInterno=Evaluacion::where('estatus','aprobado')
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();
            $negadoInterno=Evaluacion::where('estatus','!=','aprobado')
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();
            $desembolsados = Calculadora::whereNotNull('fechaDesembolso')
                ->whereIn('estatus', ['pagado', 'castigado', 'novado', 'moroso', 'restructurado', 'abierto'])
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();

            $faltaEvaluacion=Calculadora::leftJoin('evaluacions', 'calculadoras.id', '=', 'evaluacions.idSolicitudFk')
                ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','calculadoras.created_at as fecha_solicitud','evaluacions.*','evaluacions.estatus as estatus_evaluacion')
                ->where('evaluacions.estatus',NULL)
                ->whereBetween('calculadoras.created_at', [$request->since, $request->until])
                ->count();

            // $usuarios=User::join('calculadoraas', 'users.id', '=', 'calculadoras.idUserFk')
            // ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            // ->where('users.estatus','activo')
            // ->with('roles')
            // ->whereHas('roles',function($qq){
            //     $qq->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            // })
            // ->whereBetween('users.created_at', [$request->since, $request->until]);



            // $contador=0;

            // foreach ($usuarios->whereIn('calculadoras.estatus', ['pagado', 'castigado'])->get() as $value) {
            //     $contarCreditos=Calculadora::where('idUserFk',$value->id)
            //         ->whereBetween('created_at', [$request->since, $request->until])
            //         ->count();

            //     if($contarCreditos>1){
            //         $contador=$contador+1;
            //     }
            // }

            $creditosCerrados=Calculadora::where('estatus','pagado')
                ->whereBetween('created_at', [$request->since, $request->until])
                ->count();

            // $percentajeRetorno=$creditosCerrados/$contador*100;

            if($aprobadoInterno > 0){
                $percentajeAprobacion=$aprobadosFormulario/$aprobadoInterno*100;
            }else{
                $percentajeAprobacion=0;
            }

            //CREDITOS DESEMBOSADOS
            $desembolsadosPm = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'm')
                ->whereBetween('fechaDesembolso', [$request->since, $request->until])
                ->count();
            $totalDesembolsadosPm = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'm')
                ->whereBetween('fechaDesembolso', [$request->since, $request->until])
                // ->get('montoSolicitado');
                ->get();
            $montoA = 0;
            foreach($totalDesembolsadosPm as $value){
                $co= ContraOferta::where('idCalculadoraFk',$value->id)->orderBy('id','desc')->first();
                if($value->ofertaEnviada == 2){
                    $montoA = $montoA + $value->montoSolicitado;
                }else{
                    $montoA = $montoA + $co->montoAprobado;
                }
            }
            $totalDesembolsadosPm = $montoA;
            $desembolsadosPd = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'd')
                ->whereBetween('fechaDesembolso', [$request->since, $request->until])
                ->count();
            $totalDesembolsadosPd = Calculadora::whereNotNull('fechaDesembolso')
                ->where('tipoCredito', 'd')
                ->whereBetween('fechaDesembolso', [$request->since, $request->until])
                // ->sum('montoSolicitado');
                ->get();
            $montoA = 0;
            foreach($totalDesembolsadosPd as $value){
                $co= ContraOferta::where('idCalculadoraFk',$value->id)->orderBy('id','desc')->first();
                if($value->ofertaEnviada == 2){
                    $montoA = $montoA + $value->montoSolicitado;
                }else{
                    $montoA = $montoA + $co->montoAprobado;
                }
            }
            $totalDesembolsadosPd = $montoA;

            $totalCreditosDesembolsados = $desembolsadosPm + $desembolsadosPd;
            $totalCapitalCreditosDesembolsados = $totalDesembolsadosPm + $totalDesembolsadosPd;

            //CREDITOS ABIERTOS
            $this->request = $request;
            $creditosAbiertos = Calculadora::where('estatus', 'abierto')
            // ->where('tipoCredito', 'm')
            ->whereBetween('created_at', [$request->since, $request->until])
            ->with(['pagos' => function($query){
                return $query->where('estatusPago', 'pendiente')
                    ->whereBetween('created_at', [$this->request->since, $this->request->until]);
            }])
            ->get();

            $capitalpxpM = 0;
            $interespxpM = 0;
            $interesMoraPendientepxpM = 0;
            $plataformapxpM = 0;
            $aprobacionpxpM = 0;
            $ivapxpM = 0;
            $gastosCobranzapxpM = 0;
            $ivaGastosCobranzapxpM = 0;

            $capitalpxpD = 0;
            $interespxpD = 0;
            $interesMoraPendientepxpD = 0;
            $plataformapxpD = 0;
            $aprobacionpxpD = 0;
            $ivapxpD = 0;
            $gastosCobranzapxpD = 0;
            $ivaGastosCobranzapxpD = 0;

            foreach ($creditosAbiertos as $credito) {
                foreach ($credito->pagos as $pago) {
                    if($credito->tipoCredito === 'm'){
                        $capitalpxpM = $capitalpxpM + $pago->capital;
                        $interespxpM = $interespxpM + $pago->intereses;
                        $plataformapxpM = $plataformapxpM + $pago->plataforma;
                        $aprobacionpxpM = $aprobacionpxpM + $pago->aprobacionRapida;
                        $ivapxpM = $ivapxpM + $pago->iva;
                        $interesMoraPendientepxpM = $interesMoraPendientepxpM + $pago->interesMoraPendiente;
                        $gastosCobranzapxpM = $gastosCobranzapxpM + $pago->gastosCobranza;
                        $ivaGastosCobranzapxpM = $ivaGastosCobranzapxpM + $pago->ivaGastosCobranza;
                    }

                    if($credito->tipoCredito === 'd'){
                        $capitalpxpD = $capitalpxpD + $pago->capital;
                        $interespxpD = $interespxpD + $pago->intereses;
                        $plataformapxpD = $plataformapxpD + $pago->plataforma;
                        $aprobacionpxpD = $aprobacionpxpD + $pago->aprobacionRapida;
                        $ivapxpD = $ivapxpD + $pago->iva;
                        $interesMoraPendientepxpD = $interesMoraPendientepxpD + $pago->interesMoraPendiente;
                        $gastosCobranzapxpD = $gastosCobranzapxpD + $pago->gastosCobranza;
                        $ivaGastosCobranzapxpD = $ivaGastosCobranzapxpD + $pago->ivaGastosCobranza;
                    }
                }
            }

            $totalPxpM = $capitalpxpM + $interespxpM + $plataformapxpM + $aprobacionpxpM + $ivapxpM + $interesMoraPendientepxpM + $gastosCobranzapxpM + $ivaGastosCobranzapxpM;
            $totalPxpD = $capitalpxpD + $interespxpD + $plataformapxpD + $aprobacionpxpD + $ivapxpD + $interesMoraPendientepxpM + $gastosCobranzapxpM + $ivaGastosCobranzapxpM;
            $totalPxp = $totalPxpM + $totalPxpD;

            $creditosAbiertosPm = $creditosAbiertos->where('tipoCredito', 'm')->count();
            $creditosAbiertosPd = $creditosAbiertos->where('tipoCredito', 'd')->count();

            //CREDITOS MOROSOS
            $creditosMorosos = Calculadora::where('estatus', 'moroso')
            // ->where('tipoCredito', 'm')
            ->whereBetween('created_at', [$request->since, $request->until])
            ->with(['pagos' => function($query){
                return $query->where('estatusPago', 'pendiente')
                /* ->whereBetween('created_at', [$this->request->since, $this->request->until]) */;
            }])
            ->get();

            $diasMora15Pm = 0;
            $diasMora30Pm = 0;
            $diasMora60Pm = 0;
            $diasMora90Pm = 0;
            $diasMora120Pm = 0;
            $diasMora180Pm = 0;
            $diasMora360Pm = 0;
            $diasMoraMayor360Pm = 0;
            $diasMoraPm = 0;

            $diasMora15Pd = 0;
            $diasMora30Pd = 0;
            $diasMora60Pd = 0;
            $diasMora90Pd = 0;
            $diasMora120Pd = 0;
            $diasMora180Pd = 0;
            $diasMora360Pd = 0;
            $diasMoraMayor360Pd = 0;
            $diasMoraPd = 0;

            $totalMora15Pd = 0;
            $totalMora30Pd = 0;
            $totalMora60Pd = 0;
            $totalMora90Pd = 0;
            $totalMora120Pd = 0;
            $totalMora180Pd = 0;
            $totalMora360Pd = 0;
            $totalMoraMayor360Pd = 0;
            $totalMora = 0;
            $totalMoraPm = 0;
            $totalMoraPd = 0;
            $totalDiasMora = 0;
            $totalCreditosMorososPm = $creditosMorosos->where('tipoCredito', 'm')->count();
            $totalCreditosMorososPd = $creditosMorosos->where('tipoCredito', 'd')->count();
            $totalCreditosMorosos = $totalCreditosMorososPm + $totalCreditosMorososPd;


            foreach ($creditosMorosos as $creditoMoroso) {

                if($creditoMoroso->tipoCredito === 'm'){
                    $diasMoraPm = $diasMoraPm + $creditoMoroso->diasMora;
                }else{
                    $diasMoraPd = $diasMoraPd + $creditoMoroso->diasMora;
                }
                foreach ($creditoMoroso->pagos as $pago) {
                    if($creditoMoroso->tipoCredito === 'm'){
                        if($creditoMoroso->diasMora > 0 && $creditoMoroso->diasMora <= 15 ){
                            $diasMora15Pm = $diasMora15Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 15 && $creditoMoroso->diasMora <= 30 ){
                            $diasMora30Pm = $diasMora30Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 30 && $creditoMoroso->diasMora <= 60 ){
                            $diasMora60Pm = $diasMora60Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 60 && $creditoMoroso->diasMora <= 90 ){
                            $diasMora90Pm = $diasMora90Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 90 && $creditoMoroso->diasMora <= 120 ){
                            $diasMora120Pm = $diasMora120Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 120 && $creditoMoroso->diasMora <= 180 ){
                            $diasMora180Pm = $diasMora180Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 180 && $creditoMoroso->diasMora <= 360 ){
                            $diasMora360Pm = $diasMora360Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 360 ){
                            $diasMoraMayor360Pm = $diasMoraMayor360Pm + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                    }
                    if($creditoMoroso->tipoCredito === 'd'){

                        if($creditoMoroso->diasMora > 0 && $creditoMoroso->diasMora <= 15 ){
                            $diasMora15Pd = $diasMora15Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 15 && $creditoMoroso->diasMora <= 30 ){
                            $diasMora30Pd = $diasMora30Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 30 && $creditoMoroso->diasMora <= 60 ){
                            $diasMora60Pd = $diasMora60Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 60 && $creditoMoroso->diasMora <= 90 ){
                            $diasMora90Pd = $diasMora90Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 90 && $creditoMoroso->diasMora <= 120 ){
                            $diasMora120Pd = $diasMora120Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 120 && $creditoMoroso->diasMora <= 180 ){
                            $diasMora180Pd = $diasMora180Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 180 && $creditoMoroso->diasMora <= 360 ){
                            $diasMora360Pd = $diasMora360Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                        if($creditoMoroso->diasMora > 360 ){
                            $diasMoraMayor360Pd = $diasMoraMayor360Pd + $pago->montoPagar + $pago->gastosCobranza + $pago->interesMora;
                        }
                    }
                }
            }

            $diasMoraPm = $totalCreditosMorososPm > 0 ? $diasMoraPm / $creditosMorosos->where('tipoCredito', 'm')->count() : 0;
            $diasMoraPd = $totalCreditosMorososPd > 0 ? $diasMoraPd / $creditosMorosos->where('tipoCredito', 'd')->count() : 0;
            $totalDiasMora = $diasMoraPm + $diasMoraPd;

            $totalMora15 = $diasMora15Pm + $diasMora15Pd;
            $totalMora30 = $diasMora30Pm + $diasMora30Pd;
            $totalMora60 = $diasMora60Pm + $diasMora60Pd;
            $totalMora90 = $diasMora90Pm + $diasMora90Pd;
            $totalMora120 = $diasMora120Pm + $diasMora120Pd;
            $totalMora180 = $diasMora180Pm + $diasMora180Pd;
            $totalMora360 = $diasMora360Pm + $diasMora360Pd;
            $totalMoraMayor360 = $diasMoraMayor360Pm + $diasMoraMayor360Pd;

            $totalMoraPm = $diasMora15Pm + $diasMora30Pm + $diasMora60Pm + $diasMora90Pm + $diasMora120Pm + $diasMora180Pm + $diasMora360Pm + $diasMoraMayor360Pm;
            $totalMoraPd = $diasMora15Pd + $diasMora30Pd + $diasMora60Pd + $diasMora90Pd + $diasMora120Pd + $diasMora180Pd + $diasMora360Pd + $diasMoraMayor360Pd;

            $totalMora = $totalMoraPm + $totalMoraPd;

            $totalVisitantes = RecordHistory::whereBetween('created_at', [$request->since, $request->until])->count();

            $users = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
                ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
                ->where('users.estatus','activo')->with('roles')
                ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
                ->whereHas('roles',function($q){
                    $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
                });

            $contador=0;
            // $qUserAll = $users;
            // $qUserR = $users;
            // $qUserV = $users;
            // $qUserI = $users;
            // $qUserT = $users;
            $userAll=User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->whereIn('calculadoras.estatus', ['pagado', 'castigado'])->get();

            $totalRegistrados = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->count();

            $totalValidado = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->where('calculadoras.estatus', '!=','incompleto')->count();

            $totalIncompleto = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->where('calculadoras.estatus', 'incompleto')->count();

            $totalUsuarioPC=User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
            ->where('users.estatus','activo')->with('roles')
            ->whereBetween('users.created_at', [date("Y-m-d H:i:s", strtotime($request->since.' 00:00:00')), date("Y-m-d H:i:s", strtotime($request->until.' 23:00:00'))])
            ->whereHas('roles',function($q){
                $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
            })->whereIn('calculadoras.estatus', ['pagado', 'castigado'])->count();

            foreach ($userAll as $value) {
                $contarCreditos=Calculadora::where('idUserFk',$value->id)
                    ->whereBetween('created_at', [$request->since, $request->until])
                    ->count();

                if($contarCreditos>1){
                    $contador=$contador+1;
                }
            }
        }

        return response()->json([
            //RESUMEN
            'aprobadosFormulario'=>$aprobadosFormulario,
            'negadoFormulario'  => $negadoFormulario,
            'incompletosFormulario'  => $incompletosFormulario,
            'aprobadoInterno'  => $aprobadoInterno,
            'negadoInterno'  => $negadoInterno,
            'faltaEvaluacion'  => $faltaEvaluacion,
            'percentajeAprobacion' => round($percentajeAprobacion),
            'clientesRetornan' => $contador,
            // 'TOTAL DE USUARIOS' => $usuarios->whereIn('estatus_credito', ['pagado', 'castigado'])->count(),
            // 'percentajeRetorno' =>round($percentajeAprobacion),
            'percentajeRetorno' => $contador > 0 && $totalUsuarioPC > 0 ? round(($contador * 100) /$totalUsuarioPC , 2) : 0,
            'desembolsados' => $desembolsados,
            'totalVisitantes' => $totalVisitantes,
            'totalRegistrados' => $totalRegistrados,
            'totalValidado' => $totalValidado,
            'totalIncompleto' => $totalIncompleto,

            //creditos desembolsados
            'creditosDesembolsadosPm' => $desembolsadosPm,
            'creditosDesembolsadosPd' => $desembolsadosPd,
            'capitalDesembolsadosPm' => $totalDesembolsadosPm,
            'capitalDesembolsadosPd' => $totalDesembolsadosPd,
            'totalCreditosDesembolsados' => $totalCreditosDesembolsados,
            'totalCapitalCreditosDesembolsados' => $totalCapitalCreditosDesembolsados,

            //creditos abiertos
            'creditosAbiertosPm' => $creditosAbiertosPm,
            'creditosAbiertosPd' => $creditosAbiertosPd,

            //meses
            'capitalpxpM' => $capitalpxpM,
            'interespxpM' => $interespxpM,
            'plataformapxpM' => $plataformapxpM,
            'aprobacionpxpM' => $aprobacionpxpM,
            'ivapxpM' => $ivapxpM,
            'interesMoraPendientepxpM' => $interesMoraPendientepxpM,
            'gastosCobranzapxpM' => $gastosCobranzapxpM,
            'ivaGastosCobranzapxpM' => $ivaGastosCobranzapxpM,

            //dias
            'capitalpxpD' => $capitalpxpD,
            'interespxpD' => $interespxpD,
            'plataformapxpD' => $plataformapxpD,
            'aprobacionpxpD' => $aprobacionpxpD,
            'ivapxpD' => $ivapxpD,
            'interesMoraPendientepxpD' => $interesMoraPendientepxpD,
            'gastosCobranzapxpD' => $gastosCobranzapxpD,
            'ivaGastosCobranzapxpD' => $ivaGastosCobranzapxpD,

            //totales pxp
            'totalInteresMoraPendientepxp' => $interesMoraPendientepxpD + $interesMoraPendientepxpM,
            'totalGastosCobranzapxp' => $gastosCobranzapxpD + $gastosCobranzapxpM,
            'totalIvaGastosCobranzapxp' => $ivaGastosCobranzapxpD + $ivaGastosCobranzapxpM,

            //totales pendiente por pagar
            'totalPxpM' => $totalPxpM,
            'totalPxpD' => $totalPxpD,

            'totalCreditosAbiertos' => $creditosAbiertosPm + $creditosAbiertosPd,
            'totalPxp' => $totalPxp,
            'totalCapital' => $capitalpxpM + $capitalpxpD,
            'totalInteres' => $interespxpM +  $interespxpD,
            'totalPlataforma' => $plataformapxpM + $plataformapxpD,
            'totalAprobacion' => $aprobacionpxpM + $aprobacionpxpD,
            'totalIva' => $ivapxpM + $ivapxpD,

            //CREDITOS MOROSOS
            'diasMora15Pm' => $diasMora15Pm,
            'diasMora30Pm' => $diasMora30Pm,
            'diasMora60Pm' => $diasMora60Pm,
            'diasMora90Pm' => $diasMora90Pm,
            'diasMora120Pm' => $diasMora120Pm,
            'diasMora180Pm' => $diasMora180Pm,
            'diasMora360Pm' => $diasMora360Pm,
            'diasMoraMayor360Pm' => $diasMoraMayor360Pm,
            'diasMoraPm' => $diasMoraPm,
            'diasMora15Pd' => $diasMora15Pd,
            'diasMora30Pd' => $diasMora30Pd,
            'diasMora60Pd' => $diasMora60Pd,
            'diasMora90Pd' => $diasMora90Pd,
            'diasMora120Pd' => $diasMora120Pd,
            'diasMora180Pd' => $diasMora180Pd,
            'diasMora360Pd' => $diasMora360Pd,
            'diasMoraMayor360Pd' => $diasMoraMayor360Pd,
            'diasMoraPd' => $diasMoraPd,

            'totalMora15Pd' => $totalMora15Pd,
            'totalMora30Pd' => $totalMora30Pd,
            'totalMora60Pd' => $totalMora60Pd,
            'totalMora90Pd' => $totalMora90Pd,
            'totalMora120Pd' => $totalMora120Pd,
            'totalMora180Pd' => $totalMora180Pd,
            'totalMora360Pd' => $totalMora360Pd,
            'totalMoraMayor360Pd' => $totalMoraMayor360Pd,
            'totalMora' => $totalMora,
            'totalMoraPm' => $totalMoraPm,
            'totalMoraPd' => $totalMoraPd,
            'totalDiasMora' => $totalDiasMora,
            'totalCreditosMorososPm' => $totalCreditosMorososPm,
            'totalCreditosMorososPd' => $totalCreditosMorososPd,
            'totalCreditosMorosos' => $totalCreditosMorosos,
            'totalMora15' => $totalMora15,
            'totalMora30' => $totalMora30,
            'totalMora60' => $totalMora60,
            'totalMora90' => $totalMora90,
            'totalMora120' => $totalMora120,
            'totalMora180' => $totalMora180,
            'totalMora360' => $totalMora360,
            'totalMoraMayor360' => $totalMoraMayor360,


        ]);
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de consoltar estadisticas.',
            ], 500);
        }

    }

    public function isEvaluationApproved(Request $request)
    {

        $evaluacion=Evaluacion::where("idSolicitudFk", $request->idSolicitud)
               ->where('idUserFk',$request->idUser)->first();

        $success = false;
        $status = 'waiting';
        if($request->type === 'email'){
            if(!is_null($evaluacion->email)){
                $success = true;
                $status = $evaluacion->email;
            }
        }
        if($request->type === 'scrapping'){
            if(!is_null($evaluacion->informacion_identidad)){
                $success = true;
                $status = $evaluacion->verifiquese;
            }
        }

        return response()->json([
            'success' => $success,
            'status' => $status
        ], 200);

    }

    public function manualApproveEmail(Request $request)
    {
        $evaluacion=Evaluacion::findOrFail($request->evaluation_id);

        if($request->status){
            $evaluacion->estatus = "pendiente";
            $evaluacion->email = "aprobado";
            $evaluacion->resultadoEmail = $request->result;
        }else{
            $evaluacion->estatus = "negado";
            $evaluacion->email = "negado";
        }

        $evaluacion->save();

        return response()->json([
            'success' => true,
            'message' => "Etapa de Evaluación actualizada"
        ], 200);
    }

    public function manualApproveVerifiquese(Request $request)
    {
        $evaluacion=Evaluacion::findOrFail($request->evaluation_id);
        $usuario=User::where('id',$request->idUser)->first();

        if($request->status){
            $evaluacion->estatus = "aprobado";

            $tokenResult = $usuario->createToken('Analizer Access Token');
            $token = $tokenResult->token;

            $token->save();
            $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $res = [
                'result' => [
                    "bdua" => $request->bdua,
                    "ofac" => $request->ofac,
                    "estadoCedula" => $request->estadoCedula,
                    "antecedentes" => $request->antecedentes,
                    "estatus" => "aprobado",
                    'tokenAnalizer' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $expires_at,
                ],
                'msj' => 'Hay registro.',
            ];
            $usuario->tokenAnalizer=$tokenResult->accessToken;
            $usuario->save();
            $evaluacion->tokenAnalizer=$tokenResult->accessToken;
            $evaluacion->informacion_identidad = json_encode($res);
            $evaluacion->verifiquese = "aprobado";
            $evaluacion->save();

            return response()->json([
                'success' => true,
                'message' => "Etapa de Evaluación actualizada"
            ], 200);

        }else{
            $evaluacion->estatus = "negado";
            $res = [
                'result' => [
                    "bdua"=>$request->bdua,
                    "ofac" => $request->ofac,
                    "estadoCedula" => $request->estadoCedula,
                    "antecedentes" => $request->antecedentes,
                    "estatus" => "negado",
                    'tokenAnalizer'=>null,
                    'token_type'    => null,
                    'expires_at'    => null,
                ],
                'msj' => 'Hay registro.',
            ];
            $evaluacion->informacion_identidad = json_encode($res);
            $evaluacion->verifiquese = "negado";
            $evaluacion->save();

            $solicitud=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$request->idUser)->find($request->idSolicitud);
            $solicitud->estatus = "negado";
            $solicitud->save();
            /**Correo denegado */
            $usuarios=User::where('id',$request->idUser)->first();
            $contenido=Correos::where('pertenece','denegado')->first();
            $contenido_invitacion=Correos::where('pertenece','Invitacion referir despues de ser negado')->first();
            if($contenido->estatus=='activo'){

            if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();

                if($credito->tipoCredito=="m"){
                    $tipo="Panda meses";
                }else{
                    $tipo="Panda dias";
                }
                $monto=$credito->totalPagar;
                $numerCredito=$credito->numero_credito;
                $expedicion=$credito->created_at;
            }else{
                $numerCredito='No posee';
                $monto='No posee';
                $expedicion='No posee';
                $tipo='No posee';

            }

            $name = $usuarios->first_name;
            $last_name = $usuarios->last_name;
            $email = $usuarios->email;
            $cedula = $usuarios->n_document;
            $content = $contenido->contenido;
            $contentInvitacion = $contenido_invitacion->contenido;


            $arregloBusqueda=array(
                "{{Nombre}}",
                "{{Apellido}}",
                "{{Email}}",
                "{{Cedula}}",
                "{{Ncredito}}",
                "{{Monto}}",
                "{{Expedicion}}",
                "{{TipoCredito}}",

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

            );

            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);
            $cntentInvitacion2=str_replace($arregloBusqueda,$arregloCambiar,$contentInvitacion);

            $data = [
                'Nombre' => $name,
                'Email'=>$email,
                'Apellido'=>$last_name,
                'Cedula'=>$cedula,
                'Contenido'=>$cntent2,
            ];
            $dataInvitacion = [
                'Nombre' => $name,
                'Email'=>$email,
                'Apellido'=>$last_name,
                'Cedula'=>$cedula,
                'Contenido'=>$cntentInvitacion2,
            ];

            Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
            $msj->from("no-reply@creditospanda.com","Créditos Panda");
                $msj->subject($name.', préstamo denegado');
                $msj->to($email);
            });
            Mail::send('Mail.solicitud',$dataInvitacion, function($msj) use ($email,$name){
                $msj->from("no-reply@creditospanda.com","Créditos Panda");
                $msj->subject($name.', gana dinero refiriendo amigos y conocidos');
                $msj->to($email);
            });

            return response()->json([
                'success' => true,
                'message' => "Etapa de Evaluación actualizada"
            ], 200);
        }

    }

}
}
