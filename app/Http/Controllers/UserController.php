<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Repositories\UserRepositoryEloquent;
use DB;
use App\Models\User;
use App\Models\Correos;
use App\Models\Repagos;
use App\Models\Modulos;
use App\Models\Basica;
use App\Models\Pagoreferidor;
use Illuminate\Support\Facades\Log;
use App\Models\Financiera;
use App\Models\Referencias;
use App\Models\Calculadora;
use App\Models\ConfigCalculadora;
use App\Models\Evaluacion;
use App\Models\CodigosValidaciones;
use App\Models\ContraOferta;
use App\Models\desembolso;
use App\Models\Pagos;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Support\Facades\Route;
use App\Exports\ViewExport;
use Carbon\Carbon;
use PDFt;
use Maatwebsite\Excel\Facades\Excel;
use App\Criteria\RoleCriteria;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    private $NAME_CONTROLLER = 'UserController';
    public $url;
    public $auth_code;
    public $public_key;
    public $branch_id;

    public $data, $headers;

    private $send_headers;
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * @var $responseCode
     */
    protected $responseCode = 200;

    public function __construct(UserRepositoryEloquent $repository)
    {
        $this->url = env("URI_LOANDISK",null);
        $this->auth_code = env("AUTH_CODE_LOANDISK",null);
        $this->public_key = env("PUBLIC_KEY_LOANDISK",null);
        $this->branch_id = env("BRANCH_ID_LOANDISK",null);
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);

        $per_page = (!empty($request->per_page)) ? $request->per_page : User::count();
        $resp = User::join('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
        ->select('users.*','calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.numero_credito')
        ->where('users.estatus','activo')->with('roles')
        ->with('calculadora')
        ->with('calculadora.evaluacion')
        ->whereHas('roles',function($q){
            $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');
        });
        if(!empty(trim($request->search))){
            $resp->where(function($query2) use ($request) {
                $query2->orWhere('calculadoras.numero_credito', 'like', '%'.$request->search.'%')
                ->orWhere('users.first_name','like','%'.$request->search.'%')
                ->orWhere('calculadoras.estatus','like','%'.$request->search.'%')
                ->orWhere('users.email','like','%'.$request->search.'%')
                ->orWhere('users.n_document','like','%'.$request->search.'%')
                ->orWhere('users.last_name','like','%'.$request->search.'%')
                ->orWhere('users.phone_number','like','%'.$request->search.'%');
            });
            if($request->until && $request->since){
                $d =  $request->since.' 00:00:00';
                $h =  $request->until.' 23:00:00';

                $resp->where('users.created_at','>=', date("Y-m-d H:i:s", strtotime($d)));
                $resp->where('users.created_at','<=', date("Y-m-d H:i:s", strtotime($h)));
            }
        }else{
            if($request->until && $request->since){
                $d =  $request->since.' 00:00:00';
                $h =  $request->until.' 23:00:00';

                $resp->where('calculadoras.created_at','>=', date("Y-m-d H:i:s", strtotime($d)));
                $resp->where('calculadoras.created_at','<=', date("Y-m-d H:i:s", strtotime($h)));
            }
        }

        if($request->status_evaluation === "pendiente_manual"){
            $resp->whereHas('calculadora', function($query2){
                return $query2->whereHas('evaluacion', function($query3){
                    return $query3->where('estatus', "pendiente manual");
                });
            });
        }else if($request->status_evaluation === "pendiente_email"){
            $resp->whereHas('calculadora', function($query2){
                return $query2->whereHas('evaluacion', function($query3){
                    return $query3->where('estatus', 'pendiente manual')->where('email', 'pendiente manual');
                });
            });
        }else if($request->status_evaluation === "pendiente_verifiquese"){
            $resp->whereHas('calculadora', function($query2){
                return $query2->whereHas('evaluacion', function($query3){
                    return $query3->where('estatus', 'pendiente manual')->where('verifiquese', 'pendiente manual');
                });
            });
        }else{
            if($request->status_evaluation !== "all"){
                $resp->where('calculadoras.estatus', $request->status_evaluation);
            }
        }


        $respon = $resp->orderBy('users.id','desc')->paginate( $per_page);
        return response()->json($respon,$this->responseCode);
    }

    public function allAdmin(Request $request)
    {
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);

        $per_page = (!empty($request->per_page)) ? $request->per_page : $this->repository->count();
        $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
        $resp = $this->repository->where('estatus','activo')->orderBy('users.id','desc')->with('roles')->whereHas('roles',function($q){
            $q->where('name', 'Administrador');})->paginate( $per_page);

        return response()->json($resp,$this->responseCode);
    }
    public function allReferidos(Request $request)
    {
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);

        $per_page = (!empty($request->per_page)) ? $request->per_page : $this->repository->count();
        $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
        $resp = $this->repository->where('estatus','activo')->orderBy('users.id','desc')->with('roles')->whereHas('roles',function($q){
            $q->where('name', 'Referido')->orWhere('name', 'ReferidoCliente');})->paginate( $per_page);

        return response()->json($resp,$this->responseCode);
    }
    public function misReferidos(Request $request,$id)
    {
        $u = $this->repository->find($id);

        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);

        $per_page = (!empty($request->per_page)) ? $request->per_page : $this->repository->count();
        $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
        $resp = $this->repository
        ->leftJoin('referencias', 'users.id', '=', 'referencias.idUserFk')


        ->select('referencias.id as idReferencia','users.*','referencias.QuienRecomendo')

        ->where('users.estatus','activo')->where('referencias.QuienRecomendo',$u->email)->orderBy('id','desc')->paginate( $per_page);
        if($resp){
        foreach($resp as $referidos){
            // echo'aqui van'.$referidos->FirstSolicitud->estatus;
            if($referidos->FirstSolicitud->estatus=='pagado'){
                if(!Pagoreferidor::where('idSolicitud',$referidos->FirstSolicitud->id)->exists()){
                    $query=Repagos::where('idSolicitudFk',$referidos->FirstSolicitud->id);
                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('concepto','Pago total saldo al dia moroso')
                        ->OrWhere('concepto','Pago cuota mensual morosa');
                    });
                    $resul = $query->get();
                    // die($resul);
                    if(count($resul)>0)
                    {
                        $modulos = Pagoreferidor::create([
                            'idReferidor'    => $id,
                            'idReferido' => $referidos->FirstSolicitud->idUserFk,
                            'idSolicitud' => $referidos->FirstSolicitud->id,
                            'comision' => 'Esta comision no se puede pagar ya que el credito se presenta en mora',

                        ]);
                    }else{

                        $modulos = Pagoreferidor::create([
                            'idReferidor'    => $id,
                            'idReferido' => $referidos->FirstSolicitud->idUserFk,
                            'idSolicitud' => $referidos->FirstSolicitud->id,
                            'comision' => '10000',

                        ]);

                    }
                }
            }

        }
        }
        return response()->json($resp,$this->responseCode);
    }
    public function getListanegra(Request $request)
    {
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);

        $per_page = (!empty($request->per_page)) ? $request->per_page : $this->repository->count();
        $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
        $resp = $this->repository->where('estatus','lista negra')->with('roles')->paginate( $per_page);

        return response()->json($resp,$this->responseCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        DB::beginTransaction();
        $user = [];
        try{


            $user = $this->repository->create(
                $request->all()
            );

            $user->roles()->sync( $request->roles );
            $modulos = Modulos::create([
                'modulos'    => $request->modulos,
                'idUserFk' => $user->id
            ]);
            $message = 'Registro Exitoso!';
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }

        return response()->json([
            'message'   =>  $message,
            'data'      =>  $user->load('roles'),
        ],$this->responseCode);
    }

    public function estadisticasReferido(UserCreateRequest $request)
    {
        DB::beginTransaction();

        try{
            $totalPagado=0;
            $totalPendiente=0;
            $gananciasPagadas=Pagoreferidor::where('idReferidor',$request->id)->where('estatus','pagado')->get();
            foreach($gananciasPagadas as $pago){
                $totalPagado+=$pago->comision;
            }
            $gananciasPendiente=Pagoreferidor::where('idReferidor',$request->id)->where('estatus','pendiente')->get();
            foreach($gananciasPendiente as $pendiente){
                $totalPendiente+=$pendiente->comision;
            }

            $u = $this->repository->find($request->id);

            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer',
                'search'        =>  'nullable|string',
                'orderBy'       =>  'nullable|string',
                'sortBy'        =>  'nullable|in:desc,asc',
                'until'         =>  'nullable|date_format:Y-m-d',
                'since'         =>  'nullable|date_format:Y-m-d'
            ]);

            $per_page = (!empty($request->per_page)) ? $request->per_page : $this->repository->count();
            $this->repository->pushCriteria(app('App\Criteria\RoleCriteria'));
            $resp = $this->repository
            ->leftJoin('referencias', 'users.id', '=', 'referencias.idUserFk')
            // ->leftJoin('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')->orderBy('calculadoras.id','desc')
            ->select('referencias.id as idReferencia','users.*','referencias.QuienRecomendo')

            ->where('users.estatus','activo')->where('referencias.QuienRecomendo',$u->email)->count();

            $message = 'Consulta Exitosa!';
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }

        return response()->json([
            'totalPagado'   =>  $totalPagado,
            'totalPendiente' => $totalPendiente,
            'totalReferidos' => $resp

        ],$this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = [];
        try{
            $user = $this->repository->with(['roles'])->find($id);

        }catch(\Exception $e){
            $this->responseCode = 404;
        }
        return response()->json($user,$this->responseCode);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserCreateRequest $request, $id)
    {
        $user = [];
        DB::beginTransaction();
        try{
            $user = $this->repository->find($id);
            $user->fill( $request->all() );
            $user->save();

            $user->roles()->sync($request->roles);

            $message = 'Registro Actualizado!';
            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 404;
        }

        return response()->json([
            'data'      =>  $user->load('roles'),
            'message'   =>  $message,
        ],$this->responseCode);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        DB::beginTransaction();
        try{
            $user = User::find($request['id']);
            $user->email =$request['email'];
            $user->first_name =$request['first_name'];
            $user->second_name =$request['second_name'];
            $user->last_name =$request['last_name'];
            $user->second_last_name =$request['second_last_name'];
            $user->n_document =$request['n_document'];
            $user->phone_number =$request['phone_number'];
            // $user->fill( $request->all() );
            $user->save();

            $modulos = Modulos::where('idUserFk', $request['id'])->first();
            $modulos->modulos = $request['modulos'];
            $modulos->save();

            $user->roles()->sync($request['roles']);

            $message = 'Registro Actualizado!';
            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 404;
        }

        return response()->json([
            'data'      =>  $user->load('roles'),
            'message'   =>  $message,
        ],$this->responseCode);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user = $this->repository->find($id);
            $user->delete();
            if(Calculadora::where('idUserFk',$user->id)->exists()){

                $calculadora=Calculadora::where('idUserFk',$user->id)->first();
                $calculadora->delete();
            }
            if(Evaluacion::where('idUserFk',$user->id)->exists()){

                $evaluacion=Evaluacion::where('idUserFk',$user->id)->first();
                $evaluacion->delete();
            }
            if(Referencias::where('idUserFk',$user->id)->exists()){

                $referencia=Referencias::where('idUserFk',$user->id)->first();
                $referencia->delete();
            }
            if(Financiera::where('idUserFk',$user->id)->exists()){

                $financiera=Financiera::where('idUserFk',$user->id)->first();
                $financiera->delete();
            }
            if(Basica::where('idUserFk',$user->id)->exists()){

                $basica=Basica::where('idUserFk',$user->id)->first();
                $basica->delete();
            }

            $message = 'Registro Eliminado!';
        }catch(\Exception $e){
            $message = 'Recurso no encontrado';
            $this->responseCode = 404;
        }

        return response()->json([
            'message'   =>  $message,
        ],$this->responseCode);

    }

    public function sendPassword(Request $request)
    {
        if(User::where('email',$request->email)->exists()){
            $user = User::where('email',$request->email)->first();

            // $token=Hash::make(str_random(30));
              $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                  $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
            $user->token_password=$token;
            $user->save();


            $usuarios=User::where('email',$user->email)->first();
            $contenido=Correos::where('pertenece','password')->first();

if($contenido && $contenido->estatus=='activo'){
            // echo($contenido);
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
            "{{Token}}",
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
                $token,
            );

            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




            $data = [
                'Nombre' => $name,
                'Email'=>$email,
                'Apellido'=>$last_name,
                'Cedula'=>$cedula,
                'Contenido'=>$cntent2,

                ];

            Mail::send('Mail.password',$data, function($msj) use ($email,$name){
                $msj->subject($name.',recupera tu contraseña');
                $msj->to($email);
             });
}

            // $email=$request->email;
            // $data = [
            //     'Nombre' => $user->first_name,
            //     'Token' => $token,
            //     ];
            //     $name=$user->first_name;
            // Mail::send('Mail.password',$data, function($msj) use ($email,$name){
            //     $msj->subject($name.', recupera tu contraseña   ');
            //     $msj->to($email);
            //  });

             $message = 'Email enviado correctamente';
             $this->responseCode = 200;
        return response()->json([
            'message'   =>  $message,
        ],$this->responseCode);
    }else{
        $message = 'Email no registrado';
             $this->responseCode = 404;
        return response()->json([
            'message'   =>  $message,
        ],$this->responseCode);
    }

    }
    public function actualizarPassword(Request $request)
    {
        if(User::where('token_password',$request->token_password)->exists()){
            $user = User::where('token_password',$request->token_password)->first();

            $token='';
            $user->token_password='';
            $user->password=$request->password;
            $user->save();



                 $message = 'Actualizado correctamente';
                 $this->responseCode = 200;
            return response()->json([
                'message'   =>  $message,
            ],$this->responseCode);
        }else{

                        $message = 'Link caducado';
                 $this->responseCode = 401;
            return response()->json([
                'message'   =>  $message,
            ],$this->responseCode);

        }
    }

    public function storeUserBasica(Request $request)
    {
        $arreglo=json_decode($request->getContent(), true);
        DB::beginTransaction();

        try{



            // var_dump($arreglo['solicitud']);
            if(User::where('email',$arreglo['registro']['user_email'])->exists()){
                return response()->json([
                    'message' => 'Este email ya esta registrado.',
                ], 500);
            }
            if(User::where('n_document',$arreglo['registro']['n_documento'])->exists()){
                return response()->json([
                    'message' => 'Esta cedula ya esta registrada.',
                ], 500);
            }
            if(User::where('phone_number',$arreglo['registro']['telfono_celul'])->exists()){
                return response()->json([
                    'message' => 'Esta telefono ya esta registrado.',
                ], 500);
            }

            // $user = [];
                  $user = $this->repository->create([
                    'email' => $arreglo['registro']['user_email'],
                    'first_name' => $arreglo['registro']['first_name'],
                    'second_name' => $arreglo['registro']['second_name'],
                    'last_name' => $arreglo['registro']['primer_apelli'],
                    'second_last_name' => $arreglo['registro']['segundo_apell'],
                    'n_document' => $arreglo['registro']['n_documento'],
                    'phone_number' => $arreglo['registro']['telfono_celul'],
                    'password' => $arreglo['registro']['user_pass'],
                    'ip' => $arreglo['registro']['ip'],
                    'coordenadas' => $arreglo['registro']['coordenadas'],
                    'tipoDocumento' => $arreglo['registro']['tipo_de_docum'],
                    ]);

           $arreglito= [2];

            $user->roles()->sync($arreglito);



            $calculadora = Calculadora::create([
                'montoSolicitado'    => $arreglo['solicitud']['m_solicitado'],
                'plazo'     => $arreglo['solicitud']['c_mensual'],
                'tasaInteres'  => $arreglo['solicitud']['tasa'],
                'subtotal'  => $arreglo['solicitud']['subtotal'],
                'plataforma'     => $arreglo['solicitud']['plataforma'],
                'aprobacionRapida'  => $arreglo['solicitud']['aprobacion'],
                'iva'  => $arreglo['solicitud']['iva'],
                'totalPagar' => $arreglo['solicitud']['total_pagar'],
                'tipoCredito' => $arreglo['solicitud']['tipo'],
                'idUserFk' => $user->id,
                'codCampaign'=>$arreglo['solicitud']['cod_campaign']


            ]);
            // $post_array = array();
            // $post_array['borrower_unique_number'] = $user->id;
            // $post_array['borrower_firstname'] = $arreglo['registro']['first_name'];
            // $post_array['borrower_lastname'] = $arreglo['registro']['primer_apelli'];
            // $post_array['borrower_country'] = 'CO';
            // $post_array['borrower_email'] = $arreglo['registro']['user_email'];
            // $post_array['borrower_mobile'] = $arreglo['registro']['telfono_celul'];
            // $post_array['borrower_mobile'] = $arreglo['registro']['telfono_celul'];

            $solAct= Calculadora::find($calculadora->id);
            $len = strlen($calculadora->id);
            $codigo= "CP";
            if($len == 1){
                $cod = $codigo.'00'.$calculadora->id;
            }else if($len == 2){
                $cod = $codigo.'0'.$calculadora->id;
            }else if($len >= 3){
                $cod = $codigo.$calculadora->id;
            }
            $solAct->numero_credito = $cod;
            $solAct->save();
            // $arreglo=json_decode($request->getContent(), true);
            // $response=$this->call('borrower', 'POST', $post_array);

            // $data_array = json_decode($this->data, TRUE);
            // $borrower_id = $data_array['response']['borrower_id'];

            // $userAct = $this->repository->find($user->id);
            // $userAct->borrower_id_Fk=$borrower_id;
            // $userAct->save();

//             $usuarios=User::where('email',$user->email)->first();
//             $contenido=Correos::where('pertenece','bienvenida')->first();
//             $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
// if($contenido->estatus=='activo'){
//             // echo($contenido);
//              if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
//                 $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



//                     if($credito->tipoCredito=="m"){
//                         $tipo="Panda meses";
//                     }else{
//                         $tipo="Panda dias";
//                     }
//                     $monto=$credito->totalPagar;
//                     $numerCredito=$credito->numero_credito;
//                     $expedicion=$credito->created_at;
//             }else{
//                 $numerCredito='No posee';
//                 $monto='No posee';
//                 $expedicion='No posee';
//                 $tipo='No posee';

//             }

//             $name=$usuarios->first_name;
//             $last_name=$usuarios->last_name;
//             $email=$usuarios->email;
//             $cedula=$usuarios->n_document;
//             $content=$contenido->contenido;



//             $arregloBusqueda=array(
//             "{{Nombre}}",
//             "{{Apellido}}",
//             "{{Email}}",
//             "{{Cedula}}",
//             "{{Ncredito}}",
//             "{{Monto}}",
//             "{{Expedicion}}",
//             "{{TipoCredito}}",
//             );
//             $arregloCambiar=array(
//                 $name,
//                 $last_name,
//                 $email,
//                 $cedula,
//                 $numerCredito,
//                 $monto,
//                 $expedicion,
//                 $tipo,
//             );

//             $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




//             $data = [
//                 'Nombre' => $name,
//                 'Email'=>$email,
//                 'Apellido'=>$last_name,
//                 'Cedula'=>$cedula,
//                 'Contenido'=>$cntent2,

//                 ];

//             Mail::send('Mail.bienvenida',$data, function($msj) use ($email,$name){
//                 $msj->subject($name.',Bienvenido a Créditos Panda');
//                 $msj->to($email);
//              });
// }
            $message = 'Registro Exitoso!';
            DB::commit();
            return response()->json([

                'user'          => $user,
                // 'response'          => $response,
                // 'data'          => $this->data,
                // 'headers'          => $this->headers,
                'message' => $message

            ]);

        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }


    }


    public function consultaUsuario(Request $request)
    {
        $arreglo=json_decode($request->getContent(), true);
        DB::beginTransaction();

        try{



            // var_dump($arreglo['solicitud']);
            if(User::where('email',$arreglo['registro']['user_email'])->exists()){
                return response()->json([
                    'message' => 'Este email ya esta registrado.',
                ], 500);
            }else
            if(User::where('n_document',$arreglo['registro']['n_documento'])->exists()){
                return response()->json([
                    'message' => 'Esta cedula ya esta registrada.',
                ], 500);
            }else
            if(User::where('phone_number',$arreglo['registro']['telfono_celul'])->exists()){
                return response()->json([
                    'message' => 'Esta telefono ya esta registrado.',
                ], 500);
            }else{
                return response()->json([
                    'message' => 'No existe ninguno',
                ], 200);
            }



            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }


    }

    public function consultaSolicitud(Request $request)
    {
        $arreglo=json_decode($request->getContent(), true);
        DB::beginTransaction();

        try{



            // var_dump($arreglo['solicitud']);
            if(Calculadora::where('idUserFk',$request->id)->exists()){
                return response()->json([
                    'message' => 'Posee solicitudes.',
                ], 200);
            }else{
                return response()->json([
                    'message' => 'No posee solicitudes.',
                ], 400);
            }



            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }


    }

    public function informacionCompleta(Request $request)
    {
        $arreglo=json_decode($request->getContent(), true);
        DB::beginTransaction();

        try{



            // var_dump($arreglo['solicitud']);
            // var_dump($arreglo['solicitud']);
            if(!Basica::where('idUserFk',$request->id)->exists()){
                return response()->json([
                    'message' => 'No posee la informacion basica.',
                ], 400);
            }else
            if(!Referencias::where('idUserFk',$request->id)->exists()){
                return response()->json([
                    'message' => 'No posee referencias.',
                ], 400);
            }else
            if(!Financiera::where('idUserFk',$request->id)->exists()){
                return response()->json([
                    'message' => 'No posee informacion financiera',
                ], 400);
            }else{
                return response()->json([
                    'message' => 'Posee toda la informacion',
                ], 200);
            }



            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $message = $e->getMessage();
            $this->responseCode = 500;
        }


    }

    public function call ($endpoint, $type, $data = null, $from = null, $count= null, $branch_check = true){

        $this->send_headers = array('Content-Type: application/json',
            'Authorization: Basic ' . $this->auth_code
          );

          $curl_options = array(
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FRESH_CONNECT => TRUE,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTPHEADER => $this->send_headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
          );

          #other related to type and data
          switch($type) {
            case 'GET':
              $data_url =
              (!empty($data) ? '/' . $data : '') .
              (!empty($from) ? '/from/' . $from : '').
              (!empty($count) ? '/count/' . $count : '');
              break;

            case 'POST':
              $data_url = '';
              $data = json_encode($data);
              $curl_options[CURLOPT_POST] = true;
              $curl_options[CURLOPT_POSTFIELDS] = $data;
              break;

            case 'PUT':
              $data_url = '';
              $data = json_encode($data);
              $curl_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
              $curl_options[CURLOPT_POSTFIELDS] = $data;
              break;
            case 'DELETE':
              $data_url = (!empty($data) ? '/' . $data : '');
              $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
              break;
          }

          #url to connect to
          $_url = $this->url . "/" . $this->public_key . "/" . ($branch_check ? $this->branch_id : 'ma') . "/" . $endpoint. $data_url;

          #initialize the connection
          $ch = curl_init();


          $curl_options[CURLOPT_URL] = $_url;
          curl_setopt_array($ch, $curl_options);

          // Send the request
          $result = curl_exec($ch);
          $error = curl_errno($ch);
          $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
          $header = substr($result, 0, $header_size);
          $body = substr($result, $header_size);

          //
          $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
          $header = substr($result, 0, $header_size);
          $body = substr($result, $header_size);

          curl_close($ch);

          $this->headers = $header;
          $this->data = $body;

          #Mix headers & body
          return true;
      }


      public function blackList(Request $request)
      {

          DB::beginTransaction();
          try{

            // var_dump($request->id);
              $user = User::find($request->id);
            //   $user->estatus='lista negra';
            $user->estatus=$request->estatus;
              $user->save();



              $message = 'Enviado a la lista negra!';
              DB::commit();

          }catch(\Exception $e){
              DB::rollback();
              $message = $e->getMessage();
              $this->responseCode = 404;
          }

          return response()->json([
              'message'   =>  $message,
          ],$this->responseCode);
      }

//    public function creditos(){
//     if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()

//    }

   public function creditos(Request $request){
    try{
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer'
        ]);
    $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::where('idUserFk', $request->idUser)->count();
    $result = Calculadora::where('idUserFk', $request->id)->paginate($per_page);
         $response = $result;

    if($result->isEmpty()){
        $response = [
            'historico' => [],
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

public function emailReferido(Request $request){
    try{
$usuario= User::where('codigoReferidor',$request->codigo)->first();
         $response = $usuario->email;

    // if($usuario->isEmpty()){
    //     $response = [

    //         'msj' => 'No se encontraron registros.',
    //     ];
    //     return response()->json($response, 200);
    // }
    return response()->json($response, 200);
}catch (\Exception $e) {
    Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
    return response()->json([
        'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
    ], 500);
}

}

      public function addUserRefer(Request $request)
      {
          $arreglo=json_decode($request->getContent(), true);
          DB::beginTransaction();

          try{

              if(User::where('email',$request->user_email)->exists()){
                  return response()->json([
                      'message' => 'Este email ya esta registrado.',
                  ], 500);
              }
              if(User::where('n_document',$request->n_documento)->exists()){
                  return response()->json([
                      'message' => 'Esta cedula ya esta registrada.',
                  ], 500);
              }
              if(User::where('phone_number',$request->telfono_celul)->exists()){
                  return response()->json([
                      'message' => 'Esta telefono ya esta registrado.',
                  ], 500);
              }

              $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
              $codigo = mt_rand(100000, 999999);

              // $user = [];
                    $user = $this->repository->create([
                        'email' => $request->user_email,
                        'first_name' => $request->first_name,
                        'second_name' => $request->second_name,
                        'last_name' => $request->primer_apelli,
                        'second_last_name' => $request->segundo_apell,
                        'n_document' => $request->n_documento,
                        'phone_number' => $request->telfono_celul,
                        'password' => $request->user_pass,
                        'notificado' => 1,
                        'codigoReferidor'=>$codigo,
                        'tipoDocumento' => $request->tipo_de_docum
                    ]);

                    $userFinanciera = Financiera::create([
                        'banco'    => $request->banco,
                        'tipoCuenta'     => $request->tipo_cuenta,
                        'nCuenta'  => $request->numero_cuenta,
                        'idUserFk' => $user->id,
                    ]);


                    if($request->cc_anverso!='' && $request->cc_anverso!=null){
                        $n=time().'_'.$request->nombre_anverso;
                        if($request->tipoAnverso=='imagen'){

                            $resized_image = Image::make($request->cc_anverso)->stream('jpg', 60);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure

                            $nombreAnverso=$n;
                        }else{
                            $resized_image = base64_decode($request->cc_anverso);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image);
                            $nombreAnverso=$n;
                        }
                    }else{
                        $nombreAnverso='';
                    }

                    if($request->cc_reverso!='' && $request->cc_reverso!=null){
                        $n=time().'_'.$request->nombre_reverso;
                        if($request->tipoReverso=="imagen"){
                            $resized_image = Image::make($request->cc_reverso)->stream('jpg', 60);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure

                            $nombreReverso=$n;
                        }else{
                            $resized_image = base64_decode($request->cc_reverso);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image);
                            $nombreReverso=$n;
                        }
                    }else{
                        $nombreReverso='';
                    }

                    if($request->selfie!='' && $request->selfie!=null){
                        $n=time().'_'.$request->nombre_selfie;
                        if($request->tipoSelfie=="imagen"){
                            $resized_image = Image::make($request->selfie)->stream('jpg', 60);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure
                            $nombreSelfie=$n;
                        }else{
                            $resized_image = base64_decode($request->selfie);
                            Storage::disk('local')->put('\\public\\'.$n, $resized_image);
                            $nombreSelfie=$n;
                        }
                    }else{
                        $nombreSelfie='';
                    }

                    $userBasica = Basica::create([
                        'ciudad'     => $request->ciudad,
                        'fechaNacimiento' => $request->fecha_nacimiento,
                        'nCedula'    => $request->n_documento,
                        'anversoCedula'  => $nombreAnverso,
                        'reversoCedula'  => $nombreReverso,
                        'certificacion'     => $nombreSelfie,
                        'idUserFk' => $user->id,
                    ]);

             $arreglito= [3];

              $user->roles()->sync($arreglito);

              $usuarios=User::where('email',$user->email)->first();
              $contenido=Correos::where('pertenece','referidor')->first();
              $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
  if($contenido && $contenido->estatus=='activo'){
              // echo($contenido);
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
            $msjAdmin = '<p>Se registro un usuario referidor: <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
            $infoAdmin =[
                'Contenido'=>$msjAdmin,
              ];
            Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                $msj->subject('Notificacion de registro de referidor');
                $msj->to('info@creditospanda.com');
             });
              Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name){
                  $msj->subject($name.',premiamos tu lealtad');
                  $msj->to($email);
               });

  }

              $message = 'Registro Exitoso!';
              DB::commit();
              return response()->json([

                  'user' => $user,
                  'basica' => $userBasica,
                  'financiera' => $userFinanciera,
                  'message' => $message

              ]);

          }catch(\Exception $e){
              DB::rollback();
              $message = $e->getMessage();
              $this->responseCode = 500;
          }


      }

      function invitar(Request $request){

        $usuarios=User::where('id',$request->id)->first();
        $contenido=Correos::where('pertenece','invitaciones referido')->first();
        $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
                // echo($contenido);
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
                $codigo=$usuarios->codigoReferidor;
                $content=$contenido->contenido;
                $para=$request->emailp;



                $arregloBusqueda=array(
                "{{Nombre}}",
                "{{Apellido}}",
                "{{Email}}",
                "{{Cedula}}",
                "{{Ncredito}}",
                "{{Monto}}",
                "{{Expedicion}}",
                "{{TipoCredito}}",
                "{{Codigo}}",
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
                    $para
                );

                $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




                $data = [
                    'Nombre' => $name,
                    'Email'=>$email,
                    'Apellido'=>$last_name,
                    'Cedula'=>$cedula,
                    'Contenido'=>$cntent2,

                    ];
$para2=explode(",",$para);
$enviart=array();
foreach ($para2 as  $parato) {


	array_push ($enviart,trim($parato));
}



                Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$para2,$parato,$enviart){
                    $msj->subject($name.',Creditos panda');
                    $msj->to($enviart);
                 });


      }

      public function export(Request $request, $id)
      {
          $calculadora=Calculadora::find($id);
          $usuario = $this->repository
          ->find($calculadora->idUserFk);
          $financiera=Financiera::where('idUserFk',$calculadora->idUserFk)->first();
          $basica=Basica::where('idUserFk',$calculadora->idUserFk)->first();
          $referencia=Referencias::where('idUserFk',$calculadora->idUserFk)->first();

          $cuota=round($calculadora->totalPagar/$calculadora->plazo);
          $params =  [
              'usuario'      =>  $usuario,
              'financiera'      =>  $financiera,
              'basica'      =>  $basica,
              'referencia'      =>  $referencia,
              'solicitud'      =>  $calculadora,
              'cuota' => $cuota,
              'ip' => "",
              'fecha_actual' => "",
              'codigo_sms' => "",
              'contra_oferta' => array(),
              'view'           => 'reports.pdf.passenger'
          ];
          $pathToFile = storage_path('app/public/contratos/').$calculadora->numero_credito.'_'.time().'.pdf';
          $pdf = \PDFt::loadView('reports.pdf.passenger', $params)->save($pathToFile);

          $params =  [
            'usuario'      =>  $usuario,
            'financiera'      =>  $financiera,
            'basica'      =>  $basica,
            'referencia'      =>  $referencia,
            'solicitud'      =>  $calculadora,
            'contra_oferta' => array(),
            'cuota' => $cuota,
            'ip' => "",
            'fecha_actual' => "",
            'codigo_sms' => "",
            'view'           => 'reports.pdf.pagare'
        ];
        $pathToFilePagare = storage_path('app/public/contratos/').$calculadora->numero_credito.'_pagare_'.time().'.pdf';
        $pdfPagare = \PDFt::loadView('reports.pdf.pagare', $params)->save($pathToFilePagare);

        $params =  [
            'usuario'      =>  $usuario,
            'financiera'      =>  $financiera,
            'basica'      =>  $basica,
            'referencia'      =>  $referencia,
            'solicitud'      =>  $calculadora,
            'contra_oferta' => array(),
            'cuota' => $cuota,
            'ip' => "",
            'fecha_actual' => "",
            'codigo_sms' => "",
            'view'           => 'reports.pdf.carta_autorizacion'
        ];
        $pathToFileCartaAutorizacion = storage_path('app/public/contratos/').$calculadora->numero_credito.'_carta_autorizacion_'.time().'.pdf';
        $pdfCartaAutorizacion = \PDFt::loadView('reports.pdf.carta_autorizacion', $params)->save($pathToFileCartaAutorizacion);
        //   return \PDFt::loadView('reports.pdf.passenger', $params)->download('nombre-archivo.pdf');

        if($pdf){
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
            $codigo = mt_rand(100000, 999999);
            $usuario->token_firma=$token;
            $usuario->save();
            $codigoSolicitud=CodigosValidaciones::where('idUserFk',$usuario->id)
                  ->where('idSolicitudFk',$calculadora->id)
                  ->update(['valido' => 0]);
            $codigoV = CodigosValidaciones::create([
            'codigo'    => $codigo,
            'idUserFk'     => $calculadora->idUserFk,
            'idSolicitudFk'  => $calculadora->id,
            'token_firma' => $token
            ]);
            $usuarios=User::where('id',$calculadora->idUserFk)->first();
            $contenido=Correos::where('pertenece','Contrato')->first();
            $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
                // echo($contenido);
                 if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                    $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                    if($credito->tipoCredito=="m"){
                        $tipo="Panda meses";
                        $Lplazo = "Meses";
                    }else{
                        $tipo="Panda dias";
                        $Lplazo = "Dias";
                    }
                    $monto="$".number_format($credito->totalPagar);
                    $numerCredito=$credito->numero_credito;
                    $expedicion=$credito->created_at;
                    $plazo_pago = $credito->plazo." ".$Lplazo;
                    $monto_aprobado = "$".number_format($credito->montoSolicitado);
                    $monto_solicitado = "$".number_format($credito->montoSolicitado);

            }else{
                $numerCredito='No posee';
                $monto='0';
                $expedicion='No posee';
                $tipo='No posee';
                $plazo_pago = "0";
                $monto_aprobado = "0";
                $monto_solicitado = "0";

            }

                $name=$usuarios->first_name;
                $last_name=$usuarios->last_name;
                $email=$usuarios->email;
                $cedula=$usuarios->n_document;
                $content=$contenido->contenido;
                $para=$request->emailp;

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
$para2=explode(",",$para);
$enviart=array();
foreach ($para2 as  $parato) {


	array_push ($enviart,trim($parato));
}

                Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$para2,$parato,$enviart,$pathToFile,$pathToFilePagare,$pathToFileCartaAutorizacion){
                    $msj->subject($name.', tu préstamo ha sido aprobado');
                    $msj->to($email);
                    $msj->attach($pathToFile);
                    $msj->attach($pathToFilePagare);
                    $msj->attach($pathToFileCartaAutorizacion);
                 });
                 $m = "Su codigo para firma de contrato en Creditos Panda es : ".$codigo;
                 $data = array(
                     "number" => "+573212403734",
                     "message" => $m
                 );

                //     $push=http_build_query($data);
                //     $curl = curl_init();

                //     curl_setopt_array($curl, array(
                //     CURLOPT_URL => "https://api.misdatos.com.co/api/co/send/sms",
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => "",
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 0,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => "POST",
                //     CURLOPT_POSTFIELDS => $push,
                //     CURLOPT_HTTPHEADER => array(
                //         "Authorization: 0q1tgq7hdctxaqgapwmmue15foyemzmfh0ldr9uy110oft9n"
                //     ),
                //     ));

                //     $contenido = curl_exec($curl);

                //     curl_close($curl);
                }
                $calculadora->ofertaEnviada=2;
                $calculadora->documentoCarta = $pathToFileCartaAutorizacion;
                $calculadora->documentoPagare = $pathToFilePagare;
                $calculadora->documentoContrato = $pathToFile;
                $calculadora->fechaDocEnviado = date('d-m-Y h:i A');
                $calculadora->save();
        $response = [
            'msj' => 'Enviado con exito.',
        ];
        return response()->json($response, 200);


      }

      function exportExcel(Request $request)
      {
          set_time_limit(300);
          $usuario = $this->repository
          ->leftJoin('financieras', 'users.id', '=', 'financieras.idUserFk')
          ->leftJoin('basicas', 'users.id', '=', 'basicas.idUserFk')
          ->leftJoin('referencias', 'users.id', '=', 'referencias.idUserFk')
          ->leftJoin('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
          ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','users.*','basicas.*','financieras.*','referencias.*')
          ->with('roles')->whereHas('roles',function($q){
            $q->where('name', 'Cliente')->orWhere('name', 'ReferidoCliente');})
            // ->limit(500)
            // ->offset($request->offset)
            ->orderBy('users.id','desc')
          ->get();
            // print($usuario);

            foreach ($usuario as $key => $value) {
                $co = ContraOferta::where('idCalculadoraFk', $value->id_solicitud)->orderBy('id','desc')->first();
                if( $co && $value->ofertaEnviada != 2){
                    $usuario[$key]['co'] = $co;
                    $usuario[$key]['totalPagar'] = $co['totalPagar'];

                }

            }
          $params =  [

              'usuarios'      =>  $usuario,
              'view'           => 'reports.excel.client_quotations'
          ];
        // return response()->json($usuario, 200);
          return Excel::download(
            new ViewExport (
                $params
            ),
            'usersAdmin.xlsx'
        );


      }
      public function exportExcelAdmin(Request $request)
      {
          $usuario = $this->repository
          ->leftJoin('financieras', 'users.id', '=', 'financieras.idUserFk')
          ->leftJoin('basicas', 'users.id', '=', 'basicas.idUserFk')
          ->leftJoin('referencias', 'users.id', '=', 'referencias.idUserFk')
          ->leftJoin('calculadoras', 'users.id', '=', 'calculadoras.idUserFk')
          ->with('roles')->whereHas('roles',function($q){
            $q->where('name', 'Administrador');})
          ->get();

        //   $financiera=Financiera::where('idUserFk',$id)->first();
        //   $basica=Basica::where('idUserFk',$id)->first();
        //   $referencia=Referencias::where('idUserFk',$id)->first();
        //   $calculadora=Calculadora::where('idUserFk',$id)->first();
        //   $cuota=round($calculadora->totalPagar/$calculadora->plazo);
          $params =  [

              'usuarios'      =>  $usuario,
              'view'           => 'reports.excel.client_quotations'
          ];
            // var_dump($usuario);
          return Excel::download(
            new ViewExport (
                $params
            ),
            'usersAdmin.xlsx'
        );


      }


      public function exportPDF(Request $request, $id)
      {
          $usuario = $this->repository
          ->find($id);

          $financiera=Financiera::where('idUserFk',$id)->first();
          $basica=Basica::where('idUserFk',$id)->first();
          $referencia=Referencias::where('idUserFk',$id)->first();
          $calculadora=Calculadora::where('idUserFk',$id)->get();
        //   return $calculadora;
        //   $contra_oferta = ContraOferta::where('idCalculadoraFk', $calculadora->id)->orderBy('id','desc')->first();
        Log::error('historico usuario=> '.$id);
          $params =  [

              'usuario'      =>  $usuario,
              'financiera'      =>  $financiera,
              'basica'      =>  $basica,
              'referencia'      =>  $referencia,
              'solicitud'      =>  $calculadora,
              'view'           => 'reports.pdf.historico'
          ];

          return \PDFt::loadView('reports.pdf.historico', $params)->download('nombre-archivo.pdf');
                //   $pdf = PDF::loadView('reports.pdf.passenger', $params)
                //   ->setOption('margin-top', 16)
                //   ->setOption('margin-bottom', 16)
                //   ->setOption('margin-right', 16)
                //   ->setOption('margin-left', 16);
                //   return $pdf->inline('componente.pdf');


      }
      public function exportConsignacion(Request $request, $id)
      {


        if(!Calculadora::where('id','=',$request->id)->exists()){
            return response()->json([
                'solicitud'=>null,
                'pagos'=>null,
                'contra_oferta'=> null,
                'pago_proximo' => null,
                'pagado'=> null,
                'coutas_pagadas'=>null,
                'message' => 'no existe'
            ], 500);
        }
        $query =Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
        ->leftJoin('basicas', 'users.id', '=', 'basicas.idUserFk')
        ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','users.*','basicas.*');
        $query
        ->where('calculadoras.id',$request->id);
        $solicitud = $query->first();


        $pagos = Pagos::where('idSolicitudFk', $request->id)->get();
        $pago_proximo = Pagos::where('idSolicitudFk', $request->id)
        ->where('estatusPago', 'pendiente')
        ->first();
        $pagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
        ->where('estatusPago', 'pagado')
        ->get()
        ->sum("montoPagado");
        $cuotas_pagadas = Pagos::where('idSolicitudFk', $request->id)
        ->where('estatusPago', 'pagado')
        ->count();
        $contra_oferta = ContraOferta::where('idCalculadoraFk', $request->id)->orderBy('id','desc')->first();
        // return response()->json([
        //     'solicitud'=> $solicitud,
        //     'pagos' => $pagos,
        //     'contra_oferta'=> $contra_oferta,
        //     'pago_proximo' => $pago_proximo,
        //     'pagado'=> $pagado,
        //     'coutas_pagadas'=>$cuotas_pagadas,
        //     'message'=>'Consulta correcta'

        // ]);

        if($solicitud->tipoCredito=='m'){
                    $configCal = ConfigCalculadora::where('tipo','=',1)->first();
                    $porPlat = $configCal->porcentaje_plataforma;
                    $porIva = $configCal->porcentaje_iva;
                    $tasa = $configCal->tasa;
                    $porExpUno = $configCal->porcentaje_express;
                    $porExpDos = $configCal->porcentaje_express_dos;
                    $porExpTres = $configCal->porcentaje_express_tres;

                    $montoSolicitado=$solicitud->ofertaEnviada != 2 ? $contra_oferta->montoAprobado : $solicitud->montoSolicitado;
                    $plazoMeses=$solicitud->plazo;
                    $plataforma=round($montoSolicitado)*($porPlat/100);

                      $cuotas=$plazoMeses;
                      $monto=$montoSolicitado;
                      $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );

                      $valor_de_cuota =$t_interes;
                      $saldo_al_capital =$montoSolicitado;
                      $interesos;
                      $abono_al_capital;
                      $items = array();
                      $sum=0;
                      $resta=1000;

                      for ($i=0; $i <$plazoMeses; $i++) {
                          $interesos = $saldo_al_capital * $tasa;
                          $abono_al_capital = $valor_de_cuota - $interesos;
                          $saldo_al_capital -= $abono_al_capital;
                          $numero = $i + 1;

                          $interesos = $interesos;
                          $abono_al_capital = $abono_al_capital;
                          $saldo_al_capital = $saldo_al_capital;

                          $item = [$numero, $interesos, $abono_al_capital, $valor_de_cuota, round($saldo_al_capital,2)];
                          array_push($items, $item);
                          $sum+=$interesos;
                      }

                      $taxInTotal=$sum;
                    //   $homeForm.controls['tasa'].setValue($taxInTotal.toFixed(0));
                      $subtotal=round($montoSolicitado+$taxInTotal);

                      foreach($pagos as $key=> $valor){
                          if($valor->id==$pago_proximo->id){
                              $keydelArreglo=$key;
                          }
                      }



                      $amortizacion=$items;
                      $amortizacionPagar=$amortizacion[$keydelArreglo];
            }else{
                $amortizacion=[];
                $amortizacionPagar='';
            }


          $params =  [

              'solicitud'      =>  $solicitud,
              'pagoProximo'      =>  $pago_proximo,
              'contraOferta'      =>  $contra_oferta,
              'amortizacion' => $amortizacion,
             'amortizacionPagar' => $amortizacionPagar,
              'view'           => 'reports.pdf.passenger'
          ];

     // return $params;

          return \PDFt::loadView('reports.pdf.consignacion', $params)->download('nombre-archivo.pdf');
                //   $pdf = PDF::loadView('reports.pdf.passenger', $params)
                //   ->setOption('margin-top', 16)
                //   ->setOption('margin-bottom', 16)
                //   ->setOption('margin-right', 16)
                //   ->setOption('margin-left', 16);
                //   return $pdf->inline('componente.pdf');


      }
      public function consultaFirmar(Request $request, $tocken)
      {
        $solicitud = [];
        $arreglo=json_decode($request->getContent(), true);
          DB::beginTransaction();
        try{
            $codigo=CodigosValidaciones::where('token_firma',$tocken)->first();
            if($codigo){
                $solicitud = Calculadora::where('id',$codigo->idSolicitudFk)->first();
                if($solicitud){
                    if($codigo->enviado == 0){
                        $contenido=Correos::where('pertenece','codigo firma')->first();
                        $usuarios=User::where('id',$codigo->idUserFk)->first();
                        $name=$usuarios->first_name;
                        $last_name=$usuarios->last_name;
                        $email=$usuarios->email;
                        $cedula=$usuarios->n_document;
                        $content=$contenido->contenido;
                        $para=$request->emailp;
                        $arregloBusqueda=array(
                            "{{Nombre}}",
                            "{{Apellido}}",
                            "{{Email}}",
                            "{{Cedula}}",
                            "{{Cod_Firma}}",
                            );
                            $arregloCambiar=array(
                                $name,
                                $last_name,
                                $email,
                                $cedula,
                                $codigo->codigo
                            );

                            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                            $data = [
                                'Nombre' => $name,
                                'Email'=>$email,
                                'Apellido'=>$last_name,
                                'Cedula'=>$cedula,
                                'Contenido'=>$cntent2,

                                ];
                            $para2=explode(",",$para);
                            $enviart=array();
                            foreach ($para2 as  $parato) {
                            array_push ($enviart,trim($parato));
                            }

                            Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$para2,$parato,$enviart){
                                $msj->subject($name.',ingresa el código único para firmar el contrato');
                                $msj->to($email);
                            });
                            $linkFirma = "http://creditospanda.com/contrato/".$tocken;
                            $m = "Al digitar este código estas firmando electrónicamente los documentos para tu préstamo con Créditos Panda. Ingresa este código ( ".$codigo->codigo." ) en el siguiente link ".$linkFirma;
                            $data = array(
                                "messages" => [
                                    array(
                                        "from"=> "Creditos Panda",
                                        "destinations"=> [
                                            array(
                                                "to" =>'57'.$usuarios->phone_number
                                            )
                                        ],
                                        "text"=> $m,
                                        "flash"=> false
                                    )
                                ]
                            );
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://lzzkp5.api.infobip.com/sms/2/text/advanced",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS =>json_encode($data),
                                CURLOPT_HTTPHEADER => array(
                                    "Authorization: App cb9dfcd5a20ae4a16297809ddcaa9438-8b9d4893-7a2f-486e-af98-de86a0bbb3f9",
                                    "Content-Type: application/json",
                                    "Accept: application/json"
                                ),
                            ));

                            $response = curl_exec($curl);

                            curl_close($curl);
                            $codigoAct=CodigosValidaciones::find($codigo->id);
                            $codigoAct->enviado = true;
                            $codigoAct->save();
                            DB::commit();
                    }
                }
            }

        }catch(\Exception $e){
            $this->responseCode = 404;
        }
        return response()->json($solicitud,$this->responseCode);

      }

      public function firmar(Request $request, $tocken)
      {
        $response = [];
        $numeros = [
            0 => 'CERO',
            1 => 'UNO',
            2 => 'DOS',
            3 => 'TRES',
            4 => 'CUATRO',
            5 => 'CINCO',
            6 => 'SEIS',
            7 => 'SIETE',
            8 => 'OCHO',
            9 => 'NUEVE',
            10 => 'DIEZ',
            11 => 'ONCE',
            12 => 'DOCE',
            13 => 'TRECE',
            14 => 'CATORCE',
            15 => 'QUINCE',
            16 => 'DIECISEIS',
            17 => 'DIECISIETE',
            18 => 'DIECIOCHO',
            19 => 'DIECINUEVE',
            20 => 'VEINTE',
            21 => 'VEINTIUNO',
            22 => 'VEINTIDOS',
            23 => 'VEINTITRES',
            24 => 'VEINTICUATRO',
            25 => 'VEINTICINCO',
            26 => 'VEINTISEIS',
            27 => 'VEINTISIETE',
            28 => 'VEINTIOCHO',
            29 => 'VEINTINUEVE',
            30 => 'TREINTA',
            31 => 'TREINTIUNO'
        ];

        $meses=[
            0=>'',
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE',
        ];
        try{
            $codigoExiste=CodigosValidaciones::where('token_firma',$tocken)
            ->where('codigo',$request->codigo)->exists();
            if($codigoExiste){
                $codigo=CodigosValidaciones::where('token_firma',$tocken)
                ->where('codigo',$request->codigo)->first();
                $solicitud = Calculadora::where('id',$codigo->idSolicitudFk)->first();
                $contra_oferta = ContraOferta::where('idCalculadoraFk', $codigo->idSolicitudFk)->orderBy('id','desc')->first();
                if($codigo->valido){
                    $codigo->valido = 0;
                    $codigo->save();
                    $solicitud->estatus_firma = 'firmado';

                    $solicitud->save();
                    $contenido=Correos::where('pertenece','firma exitosa')->first();
                    $usuarios=User::where('id',$codigo->idUserFk)->first();
                    $financiera=Financiera::where('idUserFk',$solicitud->idUserFk)->first();
                    $basica=Basica::where('idUserFk',$solicitud->idUserFk)->first();
                    $referencia=Referencias::where('idUserFk',$solicitud->idUserFk)->first();
                    $name=$usuarios->first_name;
                    $last_name=$usuarios->last_name;
                    $email=$usuarios->email;
                    $cedula=$usuarios->n_document;
                    $content=$contenido->contenido;
                    $para=$request->emailp;
                    $res = array();
                    $fechita=date('d-m-Y h:i A');
                    $cuota=0;
                    if($solicitud->ofertaEnviada != 2){
                        $res = $contra_oferta;
                        $cuota=round($contra_oferta->totalPagar/$contra_oferta->plazo);
                    }else{
                        $cuota=round($solicitud->totalPagar/$solicitud->plazo);
                    }

                    $params =  [
                        'usuario'      =>  $usuarios,
                        'financiera'      =>  $financiera,
                        'basica'      =>  $basica,
                        'referencia'      =>  $referencia,
                        'solicitud'      =>  $solicitud,
                        'contra_oferta' => $res,
                        'cuota' => $cuota,
                        'ip' => $request->ip,
                        'fecha_actual' => $fechita,
                        'codigo_sms' => $request->codigo,
                        'view'           => 'reports.pdf.passenger'
                    ];
                    $pathToFile = storage_path('app/public/contratos/').$solicitud->numero_credito.'_'.time().'.pdf';
                    $pdf = \PDFt::loadView('reports.pdf.passenger', $params)->save($pathToFile);

                    $params =  [
                        'usuario'      =>  $usuarios,
                        'financiera'      =>  $financiera,
                        'basica'      =>  $basica,
                        'referencia'      =>  $referencia,
                        'solicitud'      =>  $solicitud,
                        'contra_oferta' => $res,
                        'cuota' => $cuota,
                        'ip' => $request->ip,
                        'fecha_actual' => $fechita,
                        'codigo_sms' => $request->codigo,
                        'meses' => $meses,
                        'numeros' => $numeros,
                        'view'           => 'reports.pdf.pagare'
                    ];
                    $pathToFilePagare = storage_path('app/public/contratos/').$solicitud->numero_credito.'_pagare_'.time().'.pdf';
                    $pdfPagare = \PDFt::loadView('reports.pdf.pagare', $params)->save($pathToFilePagare);

                    $params =  [
                        'usuario'      =>  $usuarios,
                        'financiera'      =>  $financiera,
                        'basica'      =>  $basica,
                        'referencia'      =>  $referencia,
                        'solicitud'      =>  $solicitud,
                        'contra_oferta' => $res,
                        'cuota' => $cuota,
                        'ip' => $request->ip,
                        'fecha_actual' => $fechita,
                        'codigo_sms' => $request->codigo,
                        'view'           => 'reports.pdf.carta_autorizacion'
                    ];
                    $pathToFileCartaAutorizacion = storage_path('app/public/contratos/').$solicitud->numero_credito.'_carta_autorizacion_'.time().'.pdf';
                    $pdfCartaAutorizacion = \PDFt::loadView('reports.pdf.carta_autorizacion', $params)->save($pathToFileCartaAutorizacion);

                    $arregloBusqueda=array(
                        "{{Nombre}}",
                        "{{Apellido}}",
                        "{{Email}}",
                        "{{Cedula}}",
                        "{{Cod_Firma}}",
                        );
                        $arregloCambiar=array(
                            $name,
                            $last_name,
                            $email,
                            $cedula,
                            $codigo->codigo
                        );

                        $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);

                        $data = [
                            'Nombre' => $name,
                            'Email'=>$email,
                            'Apellido'=>$last_name,
                            'Cedula'=>$cedula,
                            'Contenido'=>$cntent2,

                            ];
                        $para2=explode(",",$para);
                        $enviart=array();
                        foreach ($para2 as  $parato) {
                           array_push ($enviart,trim($parato));
                        }

                            Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$para2,$parato,$enviart,$pathToFile,$pathToFilePagare,$pathToFileCartaAutorizacion){
                                                $msj->subject($name.', tu contrato ha sido firmado exitosamente');
                                                $msj->to($email);
                                                $msj->attach($pathToFile);
                                                $msj->attach($pathToFilePagare);
                                                $msj->attach($pathToFileCartaAutorizacion);
                                            });
                        $solicitud->documentoCarta = $pathToFileCartaAutorizacion;
                        $solicitud->documentoPagare = $pathToFilePagare;
                        $solicitud->documentoContrato = $pathToFile;
                        $solicitud->fechaDocFirma= $fechita;
                        $solicitud->codigoFirma= $request->codigo;
                        $solicitud->save();
                        $msjAdmin = '<p>Se firmo el contrato del credito: <br> Nro de credito: '.$solicitud->numero_credito.'<br> <br> Nombre: '.$usuarios->first_name.' '.$usuarios->last_name.'<br> Cedula: '.$usuarios->n_document.'<br> Email: '.$usuarios->email.'</p>';
                        $infoAdmin =[
                            'Contenido'=>$msjAdmin,
                        ];
                        Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                            $msj->subject('Notificacion de contrato firmado electrónicamente');
                            $msj->to('info@creditospanda.com');
                        });
                    $response=[
                        'message' => 'Tu crédito será desembolsado en las próximas 24 horas hábiles.',
                    ];
                }else{
                    $this->responseCode = 404;
                    $response=[
                        'message' => 'El código único expiro ya que se pasó el tiempo límite para firmar o ya fue usado. Por favor comuníquese con Créditos Panda para generar los códigos nuevamente.',
                    ];
                }
            }else{
                $this->responseCode = 404;
                $response=[
                    'message' => 'Ingreso erróneamente el código único enviado por SMS o enviado al correo electrónico. Tiene tres intentos más, verifique bien antes de hacer clic nuevamente en el botón firmar.',
                ];
            }
            DB::commit();
        }catch(\Exception $e){
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            $this->responseCode=404;
            $response=[
                'message' => 'Ha ocurrido un error al tratar de firmar el contrato.',
            ];
        }
        return response()->json($response,$this->responseCode);

      }

      public function consultarCodigoActivo(Request $request)
      {
          if(CodigosValidaciones::where('idUserFk',$request->id)->where('valido',1)->exists()){
            $response=[
                'codigo' => 1,
            ];
            return response()->json($response);
          }else{
            $response=[
                'codigo' => 0,
            ];
            return response()->json($response);
          }




      }

    function download(Request $request,$id,$doc)
    {
        $solicitud = Calculadora::find($id);
        if($doc == 1){
           $file= $solicitud->documentoContrato;
        }else if($doc == 2){
            $file= $solicitud->documentoCarta;
        }else if($doc == 3){
            $file= $solicitud->documentoPagare;
        }


        $headers = array(
                  'Content-Type'=> 'application/pdf',
                );
        // return \PDFt::file
        return response()->download($file, 'filename.pdf', $headers);
        // return Storage::download('private/'.$file);
    }

    function downloadFactura(Request $request,$id)
    {
        $solicitud = Calculadora::find($id);
        $file= $solicitud->factura;
        $headers = array(
                  'Content-Type: application/pdf',
                );
        // return \PDFt::file
        return response()->download($file, 'filename.pdf', $headers);
        // return Storage::download('private/'.$file);
    }

    public function desembolsoReferido(Request $request){
        $pagoReferidor2= Pagoreferidor::where('idSolicitud',$request->idSolicitudFk)->first();


        $pagoReferidor=Pagoreferidor::find($pagoReferidor2->id);
        $pagoReferidor->estatus='pagado';
        $pagoReferidor->referencia=$request->referencia;
        $pagoReferidor->registrador=$request->registrador;
        $pagoReferidor->idRegistradorFk=$request->idRegistradorFk;
        $pagoReferidor->save();

        $usuarios=User::where('id',$pagoReferidor->idReferidor)->first();
        $contenido=Correos::where('pertenece','pagoReferidor')->first();

if($contenido && $contenido->estatus=='activo'){
        // echo($contenido);
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

        Mail::send('Mail.password',$data, function($msj) use ($email,$name){
            $msj->subject($name.',tu comisión ha sido pagada ');
            $msj->to($email);
         });
}

        $response=[
            'msj' => 'Actualizado',
        ];
        return response()->json($response);
    }

    public function estatusNovacion(Request $request){
        $actualizar=Calculadora::find($request->idSolicitud);
        if($actualizar->tipoCredito == 'd'){
            $fecha_actual = date("Y-m-d H:i:s");
            $actualizar->estatusAnterior=$actualizar->estatus;
            $actualizar->fechaPendienteNovacion = $fecha_actual;
            $actualizar->estatus='pendiente de novacion';
            $actualizar->save();
        }
        $response=[
            'msj' => 'Actualizado',
        ];
        return response()->json($response);
    }

    public function detallePagoReferidor(Request $request){
        try{
            $pago_referidor = Pagoreferidor::find($request->id);
            $usuario_referidor = User::find($pago_referidor->idReferidor);
            $financiera_referidor = Financiera::where('idUserFk',$usuario_referidor->id)->first();
            $response=[
                'pago_referidor'=>$pago_referidor,
                'usuario_referidor'=>$usuario_referidor,
                'financiera_referidor'=>$financiera_referidor,
                'msj' => 'Correcto',
            ];
            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }
    public function enviarCampana(Request $request){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://tracker2.doaffiliate.net/api/creditospanda-com?type=".$request->type."&lead=".$request->leadId."&v=".$request->visitor,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*"
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return response()->json($response,200);
    }

    public function desactivarEnvio(Request $request){
        try{
            if (User::where('email',$request->email)->exists()) {
                $userAct=User::where("email",$request->email)->first();
                $userAct->notificado=1;
                $userAct->save();
                return response()->json([
                    'message' => 'Envio de email desactivado.',
                ],200);
            }else{
                return response()->json([
                    'message' => 'Email no encontrado.',
                ],200);
            }


        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de actualizar.',
            ], 500);
        }
    }
    public function actualizarFirebase(Request $request){
        try{
            if (User::where('id',$request->idUser)->exists()) {
                $userAct=User::where("id",$request->idUser)->first();
                $userAct->tokenFb=$request->token;
                $userAct->save();
                return response()->json([
                    'message' => 'Token actualizado.',
                ],200);
            }else{
                return response()->json([
                    'message' => 'Token no actualizado.',
                ],200);
            }


        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de actualizar.',
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datosUsuarioToken(Request $request)
    {

        try{
            if ($request->user()) {
                $user=$request->user();
                $basica=Basica::where('idUserFk',$user->id)->first();
                return response()->json([
                    'first_name' => $user->first_name,
                    'second_name' => $user->second_name,
                    'last_name' => $user->last_name,
                    'second_last_name' => $user->second_last_name,
                    'n_document' => $user->n_document,
                    'birth_date'=> $basica->fechaNacimiento,
                    'expedition_date'=>$basica->fechaExpedicionCedula,
                    'gender' => $basica->genero,
                    'error'=>false
                ],200);
            }else{
                return response()->json([
                    'error'=>true,
                    'message' => 'Invalid token.',
                ],201);
            }

        }catch(\Exception $e){
            $this->responseCode = 404;
            return response()->json([
                'error'=>true,
                'message' => 'Unexpected error.',
            ],404);
        }

    }
}
