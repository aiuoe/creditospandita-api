<?php

namespace App\Http\Controllers;
use App\Models\Basica;
use App\Models\User;
use App\Models\Referencias;
use App\Models\Financiera;
use App\Models\Adicional;
use App\Models\Evaluacion;
use Exception;
use App\Models\Calculadora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserCreateRequest;
use App\Repositories\UserRepositoryEloquent;
use Image;
use Illuminate\Support\Facades\Auth;


class BasicaController extends Controller
{

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

    private $NAME_CONTROLLER = 'BasicaController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
            $usuario=User::where('id',$request->id)->with('roles')->first();
            $users = Basica::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
            where('idUserFk','=',$request->id)
            ->first();
            $financiera =Financiera::where('idUserFk','=',$request->id)
            ->first();
            $referencia =Referencias::where('idUserFk','=',$request->id)
            ->first();
            $adicional =Adicional::where('idUserFk','=',$request->id)
            ->get();
            $solicitud =Calculadora::where('idUserFk','=',$request->id)->orderBy('id', 'DESC')
            ->get();
            // if($users->isEmpty()){
            //     return response()->json([
            //         'msj' => 'No se encontraron registros.',
            //     ], 200);
            // }
            return response()->json([

                'basica'          => $users,
                'usuario'          => $usuario,
                'financiera'          => $financiera,
                'referencia'          => $referencia,
                'adicional'          => $adicional,
                'solicitud'          => $solicitud,

            ]);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function informacionCompletaPorEmail(Request $request){
        try{

            $request->validate([
                'email'         => 'required',
                'password'      => 'required|string',

            ]);

            $credentials = [
                'email'    => $request['email'],
                'password' => $request['password'],
            ];
            if (!User::where('email',$request['email'])->exists()) {

                return response()->json([
                    'message' => 'Correo electronico incorrecto o no esta registrado. Vuelva a intentarlo o haz click en registrate',
                ], 401);
            }
            if (!Auth::attempt($credentials)) {

                return response()->json([
                    'message' => 'Correo electronico y/o contraseña incorrecta. Vuelva a intentarlo o haz clic en olvido su contraseña',
                ], 401);
            }

            $usuario=User::where('email',$request['email'])->with('roles')->first();
            $basica = Basica::where('idUserFk','=',$usuario->id)->first();
            $financiera =Financiera::where('idUserFk','=',$usuario->id)->first();
            $referencia =Referencias::where('idUserFk','=',$usuario->id)->first();
            $solicitud =Calculadora::where('idUserFk','=',$usuario->id)->orderby('id','DESC')->first();
            $evaluacion=Evaluacion::where("idSolicitudFk", $solicitud['id'])->where('idUserFk',$usuario->id)->first();
            return response()->json([

                'basica'    => $basica,
                'usuario'   => $usuario,
                'financiera'    => $financiera,
                'referencia'    => $referencia,
                'solicitud' => $solicitud,
                'evaluacion'    =>  $evaluacion

            ]);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function create(Request $request){
        try{


            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            if($request->cc_anverso!='' && !empty($request->cc_anverso) && $request->cc_anverso!='null'){
                $n=time().'_'.$request->nombre_anverso;
                $resized_image = Image::make($request->cc_anverso)->stream('jpg', 60);
Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure

                $nombreAnverso=$n;
            }else{
                $nombreAnverso='';
            }
            if($request->cc_reverso!='' && !empty($request->cc_reverso) && $request->cc_reverso!='null'){
                $n=time().'_'.$request->nombre_reverso;
                $resized_image = Image::make($request->cc_reverso)->stream('jpg', 60);
Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure




                $nombreReverso=$n;
            }else{
                $nombreReverso='';
            }
            if($request->selfie!='' && !empty($request->selfie) && $request->selfie!='null'){
                $n=time().'_'.$request->nombre_selfie;

                $resized_image = Image::make($request->selfie)->stream('jpg', 60);
Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure


                $nombreSelfie=$n;
            }else{
                $nombreSelfie='';
            }
            $user = Basica::create([
                'direccion'    => $request->direccion,
                'ciudad'     => $request->ciudad,
                'tipoVivienda'  => $request->vivienda,
                'tienmpoVivienda'  => $request->tiempo_vivienda,
                'conquienVives'     => $request->con_quien_vives,
                'estrato'  => $request->estrato,
                'genero'  => $request->genero,
                'fechaNacimiento' => $request->fecha_nacimiento,
                'estadoCivil' => $request->estado_civil,
                'personasaCargo' => $request->personas_cargo,
                'nCedula'    => $request->n_documento,
                'fechaExpedicionCedula'     => $request->fecha_expedicion_doc,
                'anversoCedula'  => $nombreAnverso,
                'reversoCedula'  => $nombreReverso,
                'selfi'     => $nombreSelfie,
                'nHijos'  => $request->nro_hijos,
                'tipoPlanMovil'  => $request->tipo_plan_movil,
                'nivelEstudio' => $request->nivel_estudios,
                'estadoEstudio' => $request->estado_estudios,
                'vehiculo'    => $request->vehiculo_propio,
                'placa'     => $request->nro_placa,
                'centralRiesgo' => $request->reportado,
                'idUserFk'     => $request->id,
                'nroPersonasDependenEconomicamente' => $request->nroPersonasDependenEconomicamente,
                'cotizasSeguridadSocial'=> $request->cotizasSeguridadSocial,
                'tipoAfiliacion'=> $request->tipoAfiliacion,
                'eps'=> $request->eps,
                'entidadReportado'=> $request->entidadReportado,
                'cualEntidadReportado'=> $request->cualEntidadReportado,
                'valorMora'=> $request->valorMora,
                'tiempoReportado'=> $request->tiempoReportado,
                'estadoReportado'=>$request->estadoReportado,
                'motivoReportado'=>$request->motivoReportado,
                'comoEnterasteNosotros'=> $request->comoEnterasteNosotros,
                // 'borrower_id_Fk' => $rquest->borrower
            ]);
            DB::commit(); // Guardamos la transaccion
            return response()->json($user,201);
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

     // Modificar usuarios
     function update(Request $request){
        try{

           DB::beginTransaction(); // Iniciar transaccion de la base de datos
           if(Basica::where('idUserFk',$request->id)->exists()){

           $user = Basica::where('idUserFk',$request->id)->first();

           if($request->cc_anverso!='' && $request->cc_anverso!=null){
            $n=time().'_'.$request->nombre_anverso;
            $resized_image = Image::make($request->cc_anverso)->stream('jpg', 60);

Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure

$user->anversoCedula=$n;
        }else{
            $user->anversoCedula=$user->anversoCedula;
        }
        if($request->cc_reverso!=''){

            $n=time().'_'.$request->nombre_reverso;
            $resized_image = Image::make($request->cc_reverso)->stream('jpg', 60);

Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure




$user->reversoCedula=$n;
        }else{
            $user->reversoCedula=$user->reversoCedula;
        }
        if($request->selfie!=''){
            $n=time().'_'.$request->nombre_selfie;

            $resized_image = Image::make($request->selfie)->stream('jpg', 60);

            Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure

        $user->selfi=$n;
        }else{
            $user->selfi=$user->selfi;
        }




           $user->direccion  = $request->direccion;
           $user->ciudad = $request->ciudad;
           $user->tipoVivienda=$request->tipoVivienda;
           $user->tienmpoVivienda = $request->tienmpoVivienda;
           $user->conquienVives = $request->conquienVives;
           $user->estrato = $request->estrato;
           $user->genero = $request->genero;
           $user->fechaNacimiento = $request->fechaNacimiento;
           $user->estadoCivil = $request->estadoCivil;
           $user->personasaCargo  = $request->personasaCargo;
           $user->nCedula = $request->nCedula;
           $user->fechaExpedicionCedula=$request->fechaExpedicionCedula;
           $user->nHijos = $request->nHijos;
           $user->tipoPlanMovil = $request->tipoPlanMovil;
           $user->nivelEstudio = $request->nivelEstudio;
           $user->estadoEstudio = $request->estadoEstudio;
           $user->vehiculo = $request->vehiculo;
           $user->placa = $request->placa;
           $user->centralRiesgo = $request->centralRiesgo;
           $user->nroPersonasDependenEconomicamente = $request->nroPersonasDependenEconomicamente;
           $user->cotizasSeguridadSocial= $request->cotizasSeguridadSocial;
           $user->tipoAfiliacion= $request->tipoAfiliacion;
           $user->eps= $request->eps;
           $user->entidadReportado= $request->entidadReportado;
           $user->cualEntidadReportado= $request->cualEntidadReportado;
           $user->valorMora= $request->valorMora;
           $user->tiempoReportado= $request->tiempoReportado;
           $user->estadoReportado=$request->estadoReportado;
           $user->motivoReportado=$request->motivoReportado;
           $user->comoEnterasteNosotros= $request->comoEnterasteNosotros;


           $user->save();
           if(($request->selfie!='' && $request->selfie!=null) || ($request->cc_reverso!='' && $request->cc_reverso!=null) || ($request->cc_anverso!='' && $request->cc_anverso!=null)){
            $usuario = User::find($request->id);
                $msjAdmin = '<p>Se actualizaron fotos del usuario: <br> Nombre: '.$usuario->first_name.' '.$usuario->last_name.'<br> Cedula: '.$usuario->n_document.'<br> Email: '.$usuario->email.'</p>';
                $infoAdmin =[
                    'Contenido'=>$msjAdmin,
                ];
                Mail::send('Mail.plantilla',$infoAdmin, function($msj){
                    $msj->subject('Notificacion de actualizacion de fotos');
                    $msj->to('info@creditospanda.com');
                });
           }
           }else{
            if($request->cc_anverso!='' && $request->cc_anverso!=null){
                $n=time().'_'.$request->nombre_anverso;
                $resized_image = Image::make($request->cc_anverso)->stream('jpg', 60);
                Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure
                $anversoCedula=$n;
            }else{
                $anversoCedula='';
            }
            if($request->cc_reverso!='' && $request->cc_reverso!=''){
                $n=time().'_'.$request->nombre_reverso;
                $resized_image = Image::make($request->cc_reverso)->stream('jpg', 60);
                Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure
                $reversoCedula=$n;
            }else{
                $reversoCedula='';
            }
            if($request->selfie!='' && $request->selfie!=null){
                $n=time().'_'.$request->nombre_selfie;
                $resized_image = Image::make($request->selfie)->stream('jpg', 60);
                Storage::disk('local')->put('\\public\\'.$n, $resized_image); // check return for success and failure
                $selfi=$n;
            }else{
                $selfi='';
            }

            $user = Basica::create([
                'direccion'    => $request->direccion,
                'ciudad'     => $request->ciudad,
                'tipoVivienda'  => $request->tipoVivienda,
                'tienmpoVivienda'  => $request->tienmpoVivienda,
                'conquienVives'     => $request->conquienVives,
                'estrato'  => $request->estrato,
                'genero'  => $request->genero,
                'fechaNacimiento' => $request->fechaNacimiento,
                'estadoCivil' => $request->estadoCivil,
                'personasaCargo' => $request->personasaCargo,
                'nCedula'    => $request->nCedula,
                'fechaExpedicionCedula'     => $request->fechaExpedicionCedula,
                'nHijos'  => $request->nHijos,
                'tipoPlanMovil'  => $request->tipoPlanMovil,
                'nivelEstudio' => $request->nivelEstudio,
                'estadoEstudio' => $request->estadoEstudio,
                'vehiculo'    => $request->vehiculo,
                'placa'     => $request->placa,
                'centralRiesgo' => $request->centralRiesgo,
                'anversoCedula'     => $anversoCedula,
                'reversoCedula'     => $reversoCedula,
                'selfi'     => $selfi,
                'idUserFk'     => $request->id,
                'nroPersonasDependenEconomicamente' => $request->nroPersonasDependenEconomicamente,
                'cotizasSeguridadSocial'=> $request->cotizasSeguridadSocial,
                'tipoAfiliacion'=> $request->tipoAfiliacion,
                'eps'=> $request->eps,
                'entidadReportado'=> $request->entidadReportado,
                'cualEntidadReportado'=> $request->cualEntidadReportado,
                'valorMora'=> $request->valorMora,
                'tiempoReportado'=> $request->tiempoReportado,
                'estadoReportado'=> $request->estadoReportado,
                'motivoReportado'=> $request->motivoReportado,
                'comoEnterasteNosotros'=> $request->comoEnterasteNosotros,
            ]);

           }

           DB::commit(); // Guardamos la transaccion

           return response()->json('Actualizado correctamente',200);
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

     // Eliminar usuarios
     function delete($idUser){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $user = Basica::find($idUser);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Usuario eliminado",200);
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
}
