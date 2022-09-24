<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\ConfigCalculadora;
use App\Models\ConfigContraOferta;
use App\Models\Basica;
use App\Models\Country;
use App\Models\Filtrado;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Variables;
use App\Models\Atributos;
use App\Models\Parascore;
use App\Models\ContraOferta;
use App\Models\Correos;
use App\Models\Evaluacion;
use App\Models\desembolso;
use App\Models\Pagos;
use App\Models\Repagos;
use App\Models\Cupones;
use App\Models\PagosParciales;
use App\Models\CodigosValidaciones;
use App\Models\CannonMensualAlojamiento;
use Exception;
use Illuminate\Http\Request;
use App\Repositories\CalculadoraRepositoryEloquent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Requests\UserCreateRequest;
use App\Repositories\UserRepositoryEloquent;
use App\Exports\ViewExport;
use PDFt;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use App\Models\Notificaciones;

use Illuminate\Support\Facades\Auth;

class FinancieraController extends Controller
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


    private $NAME_CONTROLLER = 'FinancieraController';

    public function __construct(UserRepositoryEloquent $repository)
    {
        $this->url = env("URI_LOANDISK",null);
        $this->auth_code = env("AUTH_CODE_LOANDISK",null);
        $this->public_key = env("PUBLIC_KEY_LOANDISK",null);
        $this->branch_id = env("BRANCH_ID_LOANDISK",null);

        $this->repository = $repository;
    }
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
            $users = Financiera::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
             where('idUserFk','=',$request->id)
             ->get();
            if($users->isEmpty()){
                return response()->json([
                    'msj' => 'No se encontraron registros.',
                ], 200);
            }
            return response()->json($users);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function create(Request $request){
        try{

            $arreglo=json_decode($request->getContent(), true);
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $userFinanciera = Financiera::create([
                'banco'    => $arreglo['info_bancaria']['banco'],
                'tipoCuenta'     => $arreglo['info_bancaria']['tipo_cuenta'],
                'nCuenta'  => $arreglo['info_bancaria']['numero_cuenta'],
                'ingresoTotalMensual'  => $arreglo['info_bancaria']['ing_mensuales'],
                'egresoTotalMensual'     => $arreglo['info_bancaria']['egr_mensuales'],
                'ingresoTotalMensualHogar'  => $arreglo['info_bancaria']['ing_hogar'],
                'egresoTotalMensualHogar'  => $arreglo['info_bancaria']['egr_hogar'],
                'comoTePagan' => $arreglo['info_bancaria']['como_te_pagan'],
                'situacionLaboral' => $arreglo['info_bancaria']['sit_laboral'],
                'actividad' => $arreglo['info_bancaria']['actividad'],
                'antiguedadLaboral'    => $arreglo['info_bancaria']['antiguedad'],
                'nombreEmpresa'     => $arreglo['info_bancaria']['nombre_empresa'],
                'telefonoEmpresa'  => $arreglo['info_bancaria']['tel_empresa'],
                'usoCredito'  => $arreglo['info_bancaria']['uso_credito'],
                'otroIngreso'  => $arreglo['info_bancaria']['otroIngreso'],
                'proviene'  => $arreglo['info_bancaria']['proviene'],
                'total_otro_ingr_mensual'  => $arreglo['info_bancaria']['total_otro_ingr_mensual'],
                'idUserFk'     => $arreglo['info_bancaria']['id'],
                'periodoPagoNomina'  => $arreglo['info_bancaria']['periodoPagoNomina'],
                'diasPago' => $arreglo['info_bancaria']['diasPago'],
                'tarjetasCredito'=>$arreglo['info_bancaria']['tarjetasCredito'],
                'creditosBanco' => $arreglo['info_bancaria']['creditosBanco'],
                'otrasCuentas' => $arreglo['info_bancaria']['otrasCuentas'],
                'tipoEmpresa' => $arreglo['info_bancaria']['tipoEmpresa'],
                'empresaConstituida' => $arreglo['info_bancaria']['empresaConstituida'],
                'nit' => $arreglo['info_bancaria']['nit'],
                'rut' => $arreglo['info_bancaria']['rut'],
                'nombreCargo'  => $arreglo['info_bancaria']['nombreCargo'],
                'ciudadTrabajas' => $arreglo['info_bancaria']['ciudadTrabajas'],
                'direccionEmpresa'=>$arreglo['info_bancaria']['direccionEmpresa'],
                'sectorEconomico' => $arreglo['info_bancaria']['sectorEconomico'],
                'tamanoEmpresa' => $arreglo['info_bancaria']['tamanoEmpresa'],
                'fondoPension'=>$arreglo['info_bancaria']['fondoPension'],
                'bancoPension' => $arreglo['info_bancaria']['bancoPension'],
                'fuenteIngreso' => $arreglo['info_bancaria']['fuenteIngreso'],
                'cual' => $arreglo['info_bancaria']['cual'],
                'deudaActual' => $arreglo['info_bancaria']['deudaActual'],
                'pagandoActual' => $arreglo['info_bancaria']['pagandoActual']

            ]);
            $user= User::where('id',$arreglo['info_bancaria']['id'])->first();
            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;
            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addDays(1);
            }

            $token->save();


            $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();











            // $name=$user->first_name;
            // $email=$user->email;
            // $data = [
            //     'Nombre' => $name,

            //     ];
            // Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
            //     $msj->subject($name.', tu solicitud de crédito ha sido recibida y está siendo procesada    ');
            //     $msj->to($email);
            //  });



            //  $solicitudMonto=Calculadora::where('idUserFk',$arreglo['info_bancaria']['id'])->where('loandisk',0)->first();
            //  if($solicitudMonto->montoSolicitado<='1249999'){
            //      $producto='60233';
            //  }
            //  if($solicitudMonto->montoSolicitado >'1249999' && $solicitudMonto->montoSolicitado<='1749999'){
            //     $producto='60234';
            //  }
            //  if($solicitudMonto->montoSolicitado >'1749999' && $solicitudMonto->montoSolicitado<='3000000'){
            //     $producto='60232';
            //  }
            //  if($solicitudMonto->tipoCredito=='d'){
            //      $tipoCredit='Day';
            //      $tipot='Days';
            //      $esquema=6;
            //  }
            //  if($solicitudMonto->tipoCredito=='m'){
            //     $tipoCredit='Month';
            //     $tipot='Months';
            //     $esquema=3;
            // }
            if(!Basica::where('idUserFk',$arreglo['info_bancaria']['id'])->exists()){

                return response()->json([
                    'message' => 'No posee informacion basica.',
                ], 400);
            }
            if(!Referencias::where('idUserFk',$arreglo['info_bancaria']['id'])->exists()){
                return response()->json([
                    'message' => 'No posee informacion referencia.',
                ], 400);
            }
            //  $post_array = array();
            //  $post_array['loan_product_id'] = $producto; // Change to your loan product id
            //  $post_array['borrower_id'] = $user->borrower_id_Fk;
            //  $post_array['loan_application_id'] = $userFinanciera->id;
            //  $post_array['loan_disbursed_by_id'] = '46056'; // Change to your loan disbursed by id
            //  $post_array['loan_principal_amount'] = $solicitudMonto->montoSolicitado;
            //  $post_array['loan_released_date'] = date('d/m/Y');
            //  $post_array['loan_interest_method'] = 'compound_interest';
            //  $post_array['loan_interest_type'] = 'percentage';
            //  $post_array['loan_interest_period'] = $tipoCredit;

            //  $post_array['loan_duration_period'] = $tipot;
            //  $post_array['loan_interest_type'] = 'percentage';
            //  $post_array['loan_duration'] = $solicitudMonto->plazo;
            //  $post_array['loan_payment_scheme_id'] = $esquema;
            //  $post_array['loan_num_of_repayments'] = $solicitudMonto->plazo;
            //  $post_array['loan_status_id'] = '1';
            //  $post_array['loan_decimal_places'] = 'round_off_to_two_decimal';


            //  $response=$this->call('loan', 'POST', $post_array);

            //  $solicitudMonto->loandisk=1;
            //  $solicitudMonto->save();

            DB::commit(); // Guardamos la transaccion
            return response()->json([

                'access_token'  => $tokenResult->accessToken,

                'token_type'    => 'Bearer',
                'user'          => $user,
                // 'credito' => $this->data,
                'time'          => now(),
                'expires_at'    => $expires_at
            ]);
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
            $analisis = [];
           if(Financiera::where('idUserFk',$request->id)->exists()){
            $userFinanciera = Financiera::where('idUserFk',$request->id)->first();
            $userFinanciera->banco  = $request->banco;
            $userFinanciera->tipoCuenta = $request->tipoCuenta;
            $userFinanciera->nCuenta=$request->nCuenta;
            $userFinanciera->ingresoTotalMensual = $request->ingresoTotalMensual;
            $userFinanciera->egresoTotalMensual = $request->egresoTotalMensual;
            $userFinanciera->ingresoTotalMensualHogar = $request->ingresoTotalMensualHogar;
            $userFinanciera->egresoTotalMensualHogar = $request->egresoTotalMensualHogar;
            $userFinanciera->comoTePagan = $request->comoTePagan;
            $userFinanciera->situacionLaboral = $request->situacionLaboral;
            $userFinanciera->actividad  = $request->actividad;
            $userFinanciera->antiguedadLaboral = $request->antiguedadLaboral;
            $userFinanciera->nombreEmpresa=$request->nombreEmpresa;
            $userFinanciera->telefonoEmpresa = $request->telefonoEmpresa;
            $userFinanciera->usoCredito = $request->usoCredito;
            $userFinanciera->otroIngreso=$request->otroIngreso;
            $userFinanciera->proviene = $request->proviene;
            $userFinanciera->total_otro_ingr_mensual = $request->total_otro_ingr_mensual;

            $userFinanciera->periodoPagoNomina  = $request->periodoPagoNomina;
            $userFinanciera->diasPago = $request->diasPago;
            $userFinanciera->tarjetasCredito=$request->tarjetasCredito;
            $userFinanciera->creditosBanco = $request->creditosBanco;
            $userFinanciera->otrasCuentas = $request->otrasCuentas;
            $userFinanciera->tipoEmpresa = $request->tipoEmpresa;
            $userFinanciera->empresaConstituida = $request->empresaConstituida;
            $userFinanciera->nit = $request->nit;
            $userFinanciera->rut = $request->rut;
            $userFinanciera->nombreCargo  = $request->nombreCargo;
            $userFinanciera->ciudadTrabajas = $request->ciudadTrabajas;
            $userFinanciera->direccionEmpresa=$request->direccionEmpresa;
            $userFinanciera->sectorEconomico = $request->sectorEconomico;
            $userFinanciera->tamanoEmpresa = $request->tamanoEmpresa;
            $userFinanciera->fondoPension=$request->fondoPension;
            $userFinanciera->bancoPension = $request->bancoPension;
            $userFinanciera->fuenteIngreso = $request->fuenteIngreso;
            $userFinanciera->cual = $request->cual;
            $userFinanciera->deudaActual = $request->deudaActual;
            $userFinanciera->pagandoActual = $request->pagandoActual;


            $userFinanciera->save();
           }else{

            $userFinanciera = Financiera::create([
                'banco'    => $request->banco,
                'tipoCuenta'     => $request->tipoCuenta,
                'nCuenta'  => $request->nCuenta,
                'ingresoTotalMensual'  => $request->ingresoTotalMensual,
                'egresoTotalMensual'     => $request->egresoTotalMensual,
                'ingresoTotalMensualHogar'  => $request->ingresoTotalMensualHogar,
                'egresoTotalMensualHogar'  => $request->egresoTotalMensualHogar,
                'comoTePagan' => $request->comoTePagan,
                'situacionLaboral' => $request->situacionLaboral,
                'actividad' => $request->actividad,
                'antiguedadLaboral'    => $request->antiguedadLaboral,
                'nombreEmpresa'     => $request->nombreEmpresa,
                'telefonoEmpresa'  => $request->telefonoEmpresa,
                'usoCredito'  => $request->usoCredito,
                'otroIngreso'  => $request->otroIngreso,
                'proviene'  => $request->proviene,
                'total_otro_ingr_mensual'  => $request->total_otro_ingr_mensual,
                'idUserFk'     => $request->id,
                'periodoPagoNomina'  => $request->periodoPagoNomina,
                'diasPago' => $request->diasPago,
                'tarjetasCredito'=>$request->tarjetasCredito,
                'creditosBanco' => $request->creditosBanco,
                'otrasCuentas' => $request->otrasCuentas,
                'tipoEmpresa' => $request->tipoEmpresa,
                'empresaConstituida' => $request->empresaConstituida,
                'nit' => $request->nit,
                'rut' => $request->rut,
                'nombreCargo'  => $request->nombreCargo,
                'ciudadTrabajas' => $request->ciudadTrabajas,
                'direccionEmpresa'=>$request->direccionEmpresa,
                'sectorEconomico' => $request->sectorEconomico,
                'tamanoEmpresa' => $request->tamanoEmpresa,
                'fondoPension'=>$request->fondoPension,
                'bancoPension' => $request->bancoPension,
                'fuenteIngreso' => $request->fuenteIngreso,
                'cual' => $request->cual,
                'deudaActual' => $request->deudaActual,
                'pagandoActual' => $request->pagandoActual


            ]);

            $solicitudMonto=Calculadora::where('idUserFk',$request->id)->first();
        //     if($solicitudMonto->montoSolicitado<='1249999'){
        //         $producto='60233';
        //         $aprobacionEx1='30';
        //     }else{
        //         $aprobacionEx1='0';
        //     }
        //     if($solicitudMonto->montoSolicitado >'1249999' && $solicitudMonto->montoSolicitado<='1749999'){
        //        $producto='60234';
        //        $aprobacionEx2='27.5';
        //     }else{
        //         $aprobacionEx2='0';
        //     }
        //     if($solicitudMonto->montoSolicitado >'1749999' && $solicitudMonto->montoSolicitado<='3000000'){
        //        $producto='60232';
        //        $aprobacionEx3='25';
        //     }else{
        //         $aprobacionEx3='0';
        //     }
        //     if($solicitudMonto->tipoCredito=='d'){
        //         $tipoCredit='Day';
        //         $tipot='Days';
        //         $esquema=6;
        //     }
        //     if($solicitudMonto->tipoCredito=='m'){
        //        $tipoCredit='Month';
        //        $tipot='Months';
        //        $esquema=3;
        //    }
           if(!Basica::where('idUserFk',$request->id)->exists()){
               return response()->json([
                   'message' => 'No posee informacion basica.',
               ], 400);
           }
           if(!Referencias::where('idUserFk',$request->id)->exists()){
               return response()->json([
                   'message' => 'No posee informacion basica.',
               ], 400);
           }
           $user= User::where('id',$request->id)->first();
            // $post_array = array();
            // $post_array['loan_product_id'] = $producto; // Change to your loan product id
            // $post_array['borrower_id'] = $user->borrower_id_Fk;
            // $post_array['loan_application_id'] = $userFinanciera->id;
            // $post_array['loan_disbursed_by_id'] = '46056'; // Change to your loan disbursed by id
            // $post_array['loan_principal_amount'] = $solicitudMonto->montoSolicitado;
            // $post_array['loan_released_date'] = date('d/m/Y');
            // $post_array['loan_interest_method'] = 'compound_interest';
            // $post_array['loan_interest_type'] = 'percentage';
            // $post_array['loan_iva'] = '19';
            // $post_array['loan_interest_period'] = $tipoCredit;
            // $post_array['loan_fee_id_3741'] = $aprobacionEx1;
            // $post_array['loan_fee_id_3742'] = $aprobacionEx2;
            // $post_array['loan_fee_id_3743'] = $aprobacionEx3;
            // $post_array['loan_duration_period'] = $tipot;
            // $post_array['loan_interest_type'] = 'percentage';
            // $post_array['loan_duration'] = $solicitudMonto->plazo;
            // $post_array['loan_payment_scheme_id'] = $esquema;
            // $post_array['loan_num_of_repayments'] = $solicitudMonto->plazo;
            // $post_array['loan_status_id'] = '1';
            // $post_array['loan_decimal_places'] = 'round_off_to_two_decimal';


            // $response=$this->call('loan', 'POST', $post_array);

            // if($response){
            //     $solicitudMonto->loandisk=1;
            //     $solicitudMonto->save();

            // }else{
            //     return response()->json($response,400);
            // }
            // $d = json_encode();
            $analisis = self::analisis($request->id);

           }
           DB::commit(); // Guardamos la transaccion
           if(Financiera::where('idUserFk',$request->id)->exists()){
            return response()->json([


                'user'          => $userFinanciera,
                'analisis'          => $analisis

            ]);
           }else{
            return response()->json([


                'user'          => $userFinanciera,
                'analisis'          => $analisis

            ]);
           }
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
            $user = Financiera::find($idUser);
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


    function createData(Request $request){
        try{
            $arreglo=json_decode($request->getContent(), true);

                // var_dump($arreglo['solicitud']);
                if(User::where('email',$arreglo['registro']['user_email'])->exists()){
                    return response()->json([
                        'message' => 'Este email ya esta registrado.',
                    ], 500);
                }

                DB::beginTransaction(); // Iniciar transaccion de la base de datos

                $user = [];
                  $user = $this->repository->create([
                    'email' => $arreglo['registro']['user_email'],
                    'first_name' => $arreglo['registro']['first_name'],
                    'second_name' => $arreglo['registro']['second_name'],
                    'last_name' => $arreglo['registro']['primer_apelli'],
                    'second_last_name' => $arreglo['registro']['segundo_apell'],
                    'n_document' => $arreglo['registro']['n_documento'],
                    'phone_number' => $arreglo['registro']['telfono_celul'],
                    'password' => $arreglo['registro']['user_pass'],
                     ]);

           $arreglito= [1];

            $user->roles()->sync($arreglito);



                // $user = User::create([

                // ]);


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
                    'idUserFk' => $user->id


                ]);

                if($arreglo['datos_basicos']['cc_anverso']!='' && $arreglo['datos_basicos']['cc_anverso']!=null){
                    $n=time().'_'.$arreglo['datos_basicos']['nombre_anverso'];
                    Storage::disk('local')->put('\\public\\'.$n, base64_decode($arreglo['datos_basicos']['cc_anverso']));
                    $nombreAnverso=$n;
                }else{
                    $nombreAnverso='';
                }
                if($arreglo['datos_basicos']['cc_reverso']!='' && $arreglo['datos_basicos']['cc_reverso']!=null){
                    $n=time().'_'.$arreglo['datos_basicos']['nombre_reverso'];
                    Storage::disk('local')->put('\\public\\'.$n, base64_decode($arreglo['datos_basicos']['cc_reverso']));
                    $nombreReverso=$n;
                }else{
                    $nombreReverso='';
                }
                if($arreglo['datos_basicos']['selfie']!='' && $arreglo['datos_basicos']['selfie']!=null){
                    $n=time().'_'.$arreglo['datos_basicos']['nombre_selfie'];
                    Storage::disk('local')->put('\\public\\'.$n, base64_decode($arreglo['datos_basicos']['selfie']));
                    $nombreSelfie=$n;
                }else{
                    $nombreSelfie='';
                }

                $basica = Basica::create([
                    'direccion'    => $arreglo['datos_basicos']['direccion'],
                    'ciudad'     => $arreglo['datos_basicos']['ciudad'],
                    'tipoVivienda'  => $arreglo['datos_basicos']['vivienda'],
                    'tienmpoVivienda'  => $arreglo['datos_basicos']['tiempo_vivienda'],
                    'conquienVives'     => $arreglo['datos_basicos']['con_quien_vives'],
                    'estrato'  => $arreglo['datos_basicos']['estrato'],
                    'genero'  => $arreglo['datos_basicos']['genero'],
                    'fechaNacimiento' => $arreglo['datos_basicos']['fecha_nacimiento'],
                    'estadoCivil' => $arreglo['datos_basicos']['estado_civil'],
                    'personasaCargo' => $arreglo['datos_basicos']['personas_cargo'],
                    'nCedula'    => $arreglo['registro']['n_documento'],
                    'fechaExpedicionCedula'     => $arreglo['datos_basicos']['fecha_expedicion_doc'],
                    'anversoCedula'  => $nombreAnverso,
                    'reversoCedula'  => $nombreReverso,
                    'selfi'     => $nombreSelfie,
                    'nHijos'  => $arreglo['datos_basicos']['nro_hijos'],
                    'tipoPlanMovil'  => $arreglo['datos_basicos']['tipo_plan_movil'],
                    'nivelEstudio' => $arreglo['datos_basicos']['nivel_estudios'],
                    'estadoEstudio' => $arreglo['datos_basicos']['estado_estudios'],
                    'vehiculo'    => $arreglo['datos_basicos']['vehiculo_propio'],
                    'placa'     => $arreglo['datos_basicos']['nro_placa'],
                    'centralRiesgo' => $arreglo['datos_basicos']['reportado'],
                    'idUserFk'     => $user->id
                ]);
                $financiera = Financiera::create([
                    'banco'    => $arreglo['info_bancaria']['banco'],
                    'tipoCuenta'     => $arreglo['info_bancaria']['tipo_cuenta'],
                    'nCuenta'  => $arreglo['info_bancaria']['numero_cuenta'],
                    'ingresoTotalMensual'  => $arreglo['info_bancaria']['ing_mensuales'],
                    'egresoTotalMensual'     => $arreglo['info_bancaria']['egr_mensuales'],
                    'ingresoTotalMensualHogar'  => $arreglo['info_bancaria']['ing_hogar'],
                    'egresoTotalMensualHogar'  => $arreglo['info_bancaria']['egr_hogar'],
                    'comoTePagan' => $arreglo['info_bancaria']['como_te_pagan'],
                    'situacionLaboral' => $arreglo['info_bancaria']['sit_laboral'],
                    'actividad' => $arreglo['info_bancaria']['actividad'],
                    'antiguedadLaboral'    => $arreglo['info_bancaria']['antiguedad'],
                    'nombreEmpresa'     => $arreglo['info_bancaria']['nombre_empresa'],
                    'telefonoEmpresa'  => $arreglo['info_bancaria']['tel_empresa'],
                    'usoCredito'  => $arreglo['info_bancaria']['uso_credito'],
                    'otroIngreso'  => $arreglo['info_bancaria']['otroIngreso'],
                    'proviene'  => $arreglo['info_bancaria']['proviene'],
                    'total_otro_ingr_mensual'  => $arreglo['info_bancaria']['total_otro_ingr_mensual'],
                    'idUserFk'     => $user->id


                ]);

                $referencia = Referencias::create([

                    'ReferenciaPersonalNombres'    => $arreglo['referencias']['nombre_personal'],
                    'ReferenciaPersonalApellidos'     => $arreglo['referencias']['apellido_personal'],
                    'ReferenciaPersonalCiudadFk'  => $arreglo['referencias']['ciudad_personal'],
                    'ReferenciaPersonalTelefono'  => $arreglo['referencias']['tlfn_personal'],
                    'ReferenciaFamiliarNombres'     => $arreglo['referencias']['nombre_familiar'],
                    'ReferenciaFamiliarApellidos'  => $arreglo['referencias']['apellido_familiar'],
                    'ReferenciaFamiliarCiudadFk'  => $arreglo['referencias']['ciudad_familiar'],
                    'ReferenciaFamiliarTelefono' => $arreglo['referencias']['tlfn_familiar'],
                    'QuienRecomendo' => $arreglo['referencias']['recomendo'],
                    'iduserFk' => $user->id

                ]);

                $tokenResult = $user->createToken('Personal Access Token');

                $token = $tokenResult->token;
                if ($request->remember_me) {
                    $token->expires_at = Carbon::now()->addDays(1);
                }

                $token->save();


                $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();


                $name=$arreglo['registro']['first_name'];
                $email=$arreglo['registro']['user_email'];
                $data = [
                    'Nombre' => $name,

                    ];
                // Mail::send('Mail.password',$data, function($msj) use ($email,$name){
                //     $msj->subject($name.', tu solicitud de crédito ha sido recibida y está siendo procesada    ');
                //     $msj->to($email);
                //  });


             DB::commit(); // Guardamos la transaccion
             return response()->json([

                'access_token'  => $tokenResult->accessToken,

                'token_type'    => 'Bearer',
                'user'          => $user,
                'time'          => now(),
                'expires_at'    => $expires_at
            ]);
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


    public function showToken($id)
    {
    //   var_dump($request->id);
        try{
            if(!User::where('users.id','=',$id)->exists()){
                return response()->json([
                    'message' => 'no existe',
                ], 500);
            }
            $user = User::where('users.id','=',$id)
            ->first();

            $user2 = $this->repository->with(['roles'])->find($id);
            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;
            // if ($request->remember_me) {
            //     $token->expires_at = Carbon::now()->addDays(1);
            // }

            $token->save();


            $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();

            return response()->json([

                'access_token'  => $tokenResult->accessToken,

                'token_type'    => 'Bearer',
                'user'          => $user2,
                'time'          => now(),
                'expires_at'    => $expires_at
            ]);

        }catch(\Exception $e){
            $this->responseCode = 400;
        }

    }


    function createPrestamo(Request $request){
        $arreglo=json_decode($request->getContent(), true);
        $fecha_actual = date("Y-m-d");
        try{
        if(CodigosValidaciones::where('idUserFk',$arreglo['solicitud']['idUserFk'])->where('valido',1)->exists()){
            return response()->json([
                   'message' => 'Usted ya tiene un crédito aprobado con nosotros y todavía no ha firmado el contrato electrónicamente. Si ya pasaron más de 72 horas el código único expiro. Por favor generar un nuevo código en la pestaña Firmar contrato.',
               ], 500);
         }

         if(Evaluacion::where('idUserFk',$arreglo['solicitud']['idUserFk'])->where('estatus','!=','aprobado')->exists()){
            return response()->json([
                   'message' => 'No puede solicitar un crédito nuevo ya que tiene una solicitud en proceso',
               ], 500);
         }
           if(Calculadora::where('idUserFk',$arreglo['solicitud']['idUserFk'])->where('estatus','!=','pagado')->where('estatus','!=','castigado')->where('estatus','!=','negado')->exists()){
            return response()->json([
                   'message' => 'Actualmente tienes un credito abierto una vez pago en su totalidad podras realizar una nueva solicitud de credito',
               ], 500);
         }
        $solicitudNegada = Calculadora::where('idUserFk',$arreglo['solicitud']['idUserFk'])
                            ->where('estatus','negado')->orderBy("id","desc")->first();
        if($solicitudNegada){
            $fecha_tres_meses = date("Y-m-d",strtotime($solicitudNegada->created_at."+ 3 month"));
            if($fecha_actual < $fecha_tres_meses){
                return response()->json([
                    'message' => 'Revisa si tu información ha cambiado antes de enviar una Nueva Solicitud. Recuerda revisar cada una de las pestañas de "Mi perfil", es importante que sepas que no puedes solicitar un crédito nuevo si tu solicitud anterior fue negada hace menos de 90 días.',
                ], 500);
            }
        }
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
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
                'idUserFk' => $arreglo['solicitud']['idUserFk']

            ]);
            $solicitudMonto=Calculadora::where('idUserFk',$arreglo['solicitud']['idUserFk'])->where('loandisk',0)->first();

           if(!Basica::where('idUserFk',$arreglo['solicitud']['idUserFk'])->exists()){
               return response()->json([
                   'message' => 'No posee informacion basica.',
               ], 400);
           }
           if(!Referencias::where('idUserFk',$arreglo['solicitud']['idUserFk'])->exists()){
               return response()->json([
                   'message' => 'No posee informacion basica.',
               ], 400);
           }
           $user= User::where('id',$arreglo['solicitud']['idUserFk'])->with('roles')->first();
           $resp = $this->repository->where('id',$arreglo['solicitud']['idUserFk'])->whereHas('roles',function($q){
            $q->where('name', 'Referido');})->first();

           $arreglito= [4];
            if($resp){
               $resp->roles()->sync($arreglito);
            }
$messag = "";
if($calculadora){

    $len = strlen($calculadora->id);
            $codigo= "CP";
            if($len == 1){
                $cod = $codigo.'00'.$calculadora->id;
            }else if($len == 2){
                $cod = $codigo.'0'.$calculadora->id;
            }else if($len >= 3){
                $cod = $codigo.$calculadora->id;
            }
    $calculadora->numero_credito = $cod;
    $calculadora->loandisk=1;
    $calculadora->save();
    $analisis = self::analisis($arreglo['solicitud']['idUserFk'],$calculadora->id);


    $usuarios=User::where('id',$arreglo['solicitud']['idUserFk'])->first();
    $contenido=Correos::where('pertenece','solicitud')->first();
    if($analisis['estatus_solicitud'] == "negado"){
        $messag = 'Revisa si tu información ha cambiado antes de enviar una Nueva Solicitud. Recuerda revisar cada una de las pestañas de "Mi perfil", es importante que sepas que no puedes solicitar un crédito nuevo si tu solicitud anterior fue negada hace menos de 90 días.';
    }else if($analisis['estatus_solicitud'] == "aprobado"){
        $messag = "Actualmente tienes un credito abierto una vez pago en su totalidad podras realizar una nueva solicitud de credito";
    }
}else{
    return response()->json('error al mandar la solcitud',400);
}

DB::commit(); // Guardamos la transaccion


            return response()->json([
                'calculadora' => $calculadora,
                'analisis'=> $analisis,
                'message' =>$messag
            ],201);

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

         function export(){
            $file='solicitudFisica.pdf';
            $pathtoFile = public_path().'/export/'.$file;


            return response()->download($pathtoFile);
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


          public function analisis($id, $idSolicitud = 0){
            try{
                $usuario=User::where('id',$id)->first();
                $basica=Basica::where('idUserfk',$id)->first();
                $referencia=Referencias::where('idUserfk',$id)->first();
                $financiera=Financiera::where('idUserfk',$id)->first();
                $atributos=Atributos::all();
                $variables=Variables::all();
                $scoreNegado=Parascore::where('caso','negado')->first();
                $scoreAprovado=Parascore::where('caso','aprobado')->first();
                $scorePreaprovado=Parascore::where('caso','preaprobado')->first();
                $balance_inicial = DB::table('variables')->where('status',0)->sum('puntosTotales');
                $estatus_solicitud ='aprobado';
                $evaluacion_telefono = "aprobado";
                $resultado_evaluacion_telefono = "";
                $evaluacion_filtro = "aprobado";
                $evaluacion_matriz= "aprobado";
                $evaluacion_caso1 = "aprobado";
                $evaluacion_caso2 = "aprobado";
                $evaluacion_caso3 = "aprobado";
                $evaluacion_caso4 = "aprobado";
                $evaluacion_caso5 = "aprobado";
                $evaluacion_caso6 = "aprobado";
                $evaluacion_caso7 = "aprobado";
                $evaluacion_caso8 = "aprobado";
                $evaluacion_caso9 = "aprobado";
                $evaluacion_caso14 = "aprobado";
                $evaluacion_caso16 = "aprobado";
                $evaluacion_caso17 = "aprobado";
                $evaluacion_tiempoDatacredito ="aprobado";
                //     if($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                //     $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                //     $financiera->situacionLaboral=='Empleado/a por servicios'||
                //     $financiera->situacionLaboral=='Empleado/a obra labor'||
                //     $financiera->situacionLaboral=='Empleado/a temporal'){
                //     $balance_faltante= Variables::where('variable', 'Empleado')
                //             ->first('puntosTotales');
                //     $balance_faltante= Variables::where('variable', 'Empleado')
                //             ->first('puntosTotales');
                // }else if($financiera->situacionLaboral=='Independiente'){
                //     $balance_faltante= Variables::where('variable', 'Independiente')
                //     ->first('puntosTotales');
                // }else if($financiera->situacionLaboral=='Desempleado' || $financiera->situacionLaboral=='Estudiante'){
                //     $balance_faltante= Variables::where('variable', 'Desempleado')
                //     ->first('puntosTotales');
                // }else if($financiera->situacionLaboral=='Pensionado'){
                //     $balance_faltante= Variables::where('variable', 'Jubilado/Pensionado')
                //     ->first('puntosTotales');
                // }
                //  $balance_total=$balance_inicial+$balance_faltante->puntosTotales;

                $suma_basica=0;
                 if($basica->genero=='Masculino'){
                     $atributo=Atributos::where('variable', 'Genero')->where('categoria','Masculino')->first();
                     $variable= Variables::where('variable', 'Genero')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                    //  var_dump(floatval((($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0)));
                 }else if($basica->genero=='Femenino'){
                    $atributo=Atributos::where('variable', 'Genero')->where('categoria','Femenino')->first();
                    $variable= Variables::where('variable', 'Genero')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                // $fecha_nacimiento=$basica->fechaNacimiento;
                //  $edad=time()-time($fecha_nacimiento);
                if($basica->fechaNacimiento != "Invalid date"){
                    $edad = Carbon::parse($basica->fechaNacimiento)->age;
                 }else{
                     $edad =0;
                 }
                if($edad<=23){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Jóven')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                } else if($edad>23 && $edad<=32){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Jóven')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>32 && $edad<=50){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Maduro')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>50 && $edad<=59){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Adulto Mayor')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($edad>59 ){
                    $atributo=Atributos::where('variable', 'Fecha de Nacimiento')->where('categoria','Senior')->first();
                    $variable= Variables::where('variable', 'Fecha de Nacimiento')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                $ciudad=Country::where('name',$basica->ciudad)->first();

                if($ciudad->zonaGeografica == 'AMAZONIA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','AMAZONIA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'ANDINA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','ANDINA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($ciudad->zonaGeografica == 'ORINOQUIA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','ORINOQUIA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'CARIBE'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','CARIBE')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'BOGOTA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','BOGOTA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($ciudad->zonaGeografica == 'PACIFICA'){
                    $atributo=Atributos::where('variable', 'Selecciona tu Ciudad')->where('categoria','PACIFICA')->first();
                    $variable= Variables::where('variable', 'Selecciona tu Ciudad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                // if(!empty($usuario->phone_number)){
                //     $atributo=Atributos::where('variable','Teléfono 1')->where('categoria','Celular personal')->first();
                //     $variable= Variables::where('variable', 'Teléfono 1')->first();
                //      $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);


                // }

                if($basica->tipoVivienda == 'Rentada'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Rentada')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tipoVivienda == 'Propia'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Propia')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tipoVivienda == 'Familiar'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Familiar')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tipoVivienda == 'Hipotecada'){
                    $atributo=Atributos::where('variable', 'Tipo de vivienda')->where('categoria','Hipotecada')->first();
                    $variable= Variables::where('variable', 'Tipo de vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->tienmpoVivienda == 'Menos de 1 año'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','Menos de 1 año')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '1 a 2 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','1 a 2 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tienmpoVivienda == '2 a 4 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','2 a 4 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '4 a 5 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','4 a 5 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == 'Más de 5 años'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','Más de 5 años')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '2 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','2 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '4 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','4 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tienmpoVivienda == '6 meses'){
                    $atributo=Atributos::where('variable', 'Tiempo en esta vivienda')->where('categoria','6 meses')->first();
                    $variable= Variables::where('variable', 'Tiempo en esta vivienda')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->estrato == '1'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 1')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estrato == '2'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 2')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estrato == '3'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 3')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estrato == '4'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 4')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->estrato == '5'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 5')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estrato == '6'){
                    $atributo=Atributos::where('variable', 'Estrato')->where('categoria','Estrato 6')->first();
                    $variable= Variables::where('variable', 'Estrato')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->estadoCivil == 'Soltero'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Soltero/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->estadoCivil == 'Casado'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Casado/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estadoCivil == 'Unión Libre'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','En unión libre')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estadoCivil == 'Divorciado'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Divorciado/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->estadoCivil == 'Viudo'){
                    $atributo=Atributos::where('variable', 'Estado civil')->where('categoria','Viudo/a')->first();
                    $variable= Variables::where('variable', 'Estado civil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->personasaCargo == 'Ninguna'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','Ninguna')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->personasaCargo == 'Una Persona'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','1 Persona')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->personasaCargo == 'Dos Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','2 Personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->personasaCargo == 'Tres Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','3 Personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->personasaCargo == 'Mas de Tres Personas'){
                    $atributo=Atributos::where('variable', 'Cuantas personas dependen de ti economicamente')->where('categoria','Mas de 3 personas')->first();
                    $variable= Variables::where('variable', 'Cuantas personas dependen de ti economicamente')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->conquienVives == 'Solo'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Solo')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->conquienVives == 'Padres y/o Hermanos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Padres y/o hermanos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->conquienVives == 'Esposo(a) y/o Pareja'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Esposo/a o pareja')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->conquienVives == 'Esposo(a) y/o Pareja con hijos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Esposo/a o pareja con hijos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->conquienVives == 'Unicamente Hijos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Únicamente hijos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->conquienVives == 'Amigos'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Amigos')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->conquienVives == 'Otro'){
                    $atributo=Atributos::where('variable', '¿Con quién vives?')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', '¿Con quién vives?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->tipoPlanMovil == 'Pospago'){
                    $atributo=Atributos::where('variable','Tipo de plan móvil')->where('categoria','Pospago')->first();
                    $variable= Variables::where('variable', 'Tipo de plan móvil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->tipoPlanMovil == 'Prepago'){
                    $atributo=Atributos::where('variable','Tipo de plan móvil')->where('categoria','Prepago')->first();
                    $variable= Variables::where('variable', 'Tipo de plan móvil')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($basica->nivelEstudio == 'Ninguno'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Ninguno')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Básico' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Básico en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Básico' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Básico finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->nivelEstudio == 'Bachiller' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Bachiller en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Bachiller' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Bachiller finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Técnico' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Técnico en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Técnico' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Técnico finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Tecnólogo' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Tecnólogo en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Tecnólogo' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Tecnólogo finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Pregrado' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Pregrado en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Pregrado' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Pregrado finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Postgrado' && $basica->estadoEstudio == 'En curso'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Posgrado en curso')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->nivelEstudio == 'Postgrado' && $basica->estadoEstudio == 'Finalizado'){
                    $atributo=Atributos::where('variable','Nivel de estudios')->where('categoria','Posgrado finalizado')->first();
                    $variable= Variables::where('variable', 'Nivel de estudios')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->vehiculo == 'Ninguno'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Ninguno')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->vehiculo == 'Carro'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Carro')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->vehiculo == 'Moto'){
                    $atributo=Atributos::where('variable','¿Tienes vehículo propio?')->where('categoria','Moto')->first();
                    $variable= Variables::where('variable', '¿Tienes vehículo propio?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($basica->centralRiesgo == 'Si'){
                    $atributo=Atributos::where('variable','¿Reportado en data crédito?')->where('categoria','Si')->first();
                    $variable= Variables::where('variable', '¿Reportado en data crédito?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if($basica->centralRiesgo == 'No'){
                    $atributo=Atributos::where('variable','¿Reportado en data crédito?')->where('categoria','No')->first();
                    $variable= Variables::where('variable', '¿Reportado en data crédito?')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if(trim($financiera->situacionLaboral)=='Empleado/a termino indefinido'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a termino indefinido')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a termino fijo renovable')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Empleado/a por servicios'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a por servicios')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Empleado/a obra labor'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a obra labor')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Empleado/a temporal'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Empleado/a temporal')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Independiente'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Independiente')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Estudiante'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Estudiante')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else if(trim($financiera->situacionLaboral)=='Pensionado'){
                    $atributo=Atributos::where('variable','Situación Laboral')->where('categoria','Pensionado')->first();

                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }


                if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Administrativas (jefes, coordinadores, asistentes, auxiliar y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Administrativas (jefes.coordinadores.asistentes.auxiliares y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Gerenciales (gerentes, subgerentes, directores, y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Gerenciales (gerentes.subgerentes.directores y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Operativas y de servicio (supervisores, maquinistas, operarios, analistas y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Operativas y de servicio(supervisores.maquinistas.operarios.analistas y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Ventas y Mercadeo (vendedores, agentes y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Ventas y mercadeo(vendedores.agentes y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Técnicas (ingeniería, investigación y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Vigilancia y seguridad'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Vigilancia y seguridad')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Taxista'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Taxista')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Conductor/transportador'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Conductor/transportador')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Secretariales'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Secretariales')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Areas de apoyo (sistemas, contabilidad, auditoria, revisoria y similares)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Áreas de apoyo(sistemas.contabilidad.auditoria.revosoria y similares)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Policia'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Policía')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Militar'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Militar')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Cajero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Cajero')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if(($financiera->situacionLaboral=='Empleado/a termino indefinido' ||
                    $financiera->situacionLaboral=='Empleado/a termino fijo renovable'||
                    $financiera->situacionLaboral=='Empleado/a por servicios '||
                    $financiera->situacionLaboral=='Empleado/a obra labor'||
                    $financiera->situacionLaboral=='Empleado/a temporal') && $financiera->actividad == 'Otro'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Otro')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }





                if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Abogado'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Abogado ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'actividades Agropecuarias'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Actividades agropecuarias')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Ama de casa'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Ama de casa')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Artistas'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Artistas ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Cocineros'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Cocineros')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Contador/Revisor'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Contador/revisor ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Empresario pyme'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Empresario pyme')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Ingeniero/Geologo/Arquitectos'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','ingeniero/geologo/arquitecto')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Medico/Profesionales del sector salud'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Medico/profesional sector salud')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Mensajero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Mensajero ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Microempresario'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Microempresario ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Operarios de maquinaria'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Operarios de maquinaria')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Peluquero'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Peluquero')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Plomero/Albañil'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Plomero/albañil')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Profesional o tecnico en informatica (software)'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Profesional o tecnico en informatica (software)')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Profesores/Docentes'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Profesores/docentes')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Secretarias'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Secretarias ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Servicios de vigilancia/Policias/Militares'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Servicios de vigilancia/Policias/militares')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Taxista/Transportador/Conductor'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Taxistas/ transportador/conductor')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Tecnicos o tecnologos en otras areas'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Tecnicos o tecnologos en otras areas ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Vendedores/Visitadores'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Vendedores/visitadores')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Otro'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','otros ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }else  if($financiera->situacionLaboral == 'Independiente' && $financiera->actividad == 'Estetica'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Estetica ')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($financiera->situacionLaboral == 'Desempleado' || $financiera->situacionLaboral=='Estudiante'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Desempleado')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                     $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);
                }

                if($financiera->situacionLaboral == 'Pensionado'){
                    $atributo=Atributos::where('variable','Actividad')->where('categoria','Jubilado / Pensionado')->first();
                    $variable= Variables::where('variable', 'Actividad')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($financiera->antiguedadLaboral == 'menos de 2 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','Menos de 2 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '3 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','3 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '4 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','4 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '5 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','5 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '6 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','6 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '7 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','7 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '8 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','8 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '9 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','9 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }
                else
                if($financiera->antiguedadLaboral == '10 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','10 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '11 meses'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','11 meses')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == '1 Año'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','1 año')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == 'Dos Años'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','2 años')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else
                if($financiera->antiguedadLaboral == 'Tres Años o más'){
                    $atributo=Atributos::where('variable','Antiguedad laboral')->where('categoria','3 años o mas')->first();
                    $variable= Variables::where('variable', 'Antiguedad laboral')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }


                if($financiera->ingresoTotalMensual <= 684600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','1-$684,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 684600 && $financiera->ingresoTotalMensual <= 877800){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$684,600-$877,800')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 877800 && $financiera->ingresoTotalMensual <= 1040600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$877,800-$1,040,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 1040600 && $financiera->ingresoTotalMensual <= 1233600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,040,600-$1,233,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 1233600 && $financiera->ingresoTotalMensual <= 1462400){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,233,600-$1,462,400')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 1462400 && $financiera->ingresoTotalMensual <= 1733600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,462,400-$1,733,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 1733600 && $financiera->ingresoTotalMensual <= 2055100){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$1,733,600-$2,055,100')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 2055100 && $financiera->ingresoTotalMensual <= 2436200){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,055,100-$2,436,200')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 2436200 && $financiera->ingresoTotalMensual <= 2888000){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,436,200-$2,888,000')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 2888000 && $financiera->ingresoTotalMensual <= 3423600){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$2,888,000-$3,423,600')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->ingresoTotalMensual > 3423600 && $financiera->ingresoTotalMensual <= 99999999){
                    $atributo=Atributos::where('variable','Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->where('categoria','$3,423,600-$99,999,999')->first();
                    $variable= Variables::where('variable', 'Ingresos totales mensuales (si aplica,incluir subsidio de transporte)')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }


                if($financiera->otroIngreso =='Si'){

                    if($financiera->proviene =='Arriendo'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Rentas')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }else if($financiera->proviene =='Salario Pareja'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Salario pareja')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }else if($financiera->proviene =='Actividad Comercial Extra'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Actividad comercial extra')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }else if($financiera->proviene =='Otro'){
                        $atributo=Atributos::where('variable','Otros ingresos 1')->where('categoria','Otro')->first();
                        $variable= Variables::where('variable', 'Otros ingresos 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }


                  $totalotroIngreso=$financiera->total_otro_ingr_mensual;

                }else{
                    $totalotroIngreso=0;
                }

                if(!empty($financiera->total_otro_ingr_mensual)){
                    if($financiera->tipoCuenta =='Ahorros'){
                        $atributo=Atributos::where('variable','Tipo de Cuenta bancaria 1')->where('categoria','Cuenta de ahorro')->first();
                        $variable= Variables::where('variable', 'Tipo de Cuenta bancaria 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }else if($financiera->tipoCuenta =='Corriente'){
                        $atributo=Atributos::where('variable','Tipo de Cuenta bancaria 1')->where('categoria','Cuenta Corriente')->first();
                        $variable= Variables::where('variable', 'Tipo de Cuenta bancaria 1')->first();
                        $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                    }
                }

                if(!empty($referencia->ReferenciaPersonalNombres)){
                    $atributo=Atributos::where('variable','Referencia 1 - Nombre:')->where('categoria','(Todas las categorías)')->first();
                    $variable= Variables::where('variable', 'Referencia 1 - Nombre:')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if(!empty($referencia->ReferenciaFamiliarNombres)){
                    $atributo=Atributos::where('variable','Referencia 2 - Nombre:')->where('categoria','(Todas las categorías)')->first();
                    $variable= Variables::where('variable', 'Referencia 2 - Nombre:')->first();
                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($basica->cotizasSeguridadSocial == "Si"){
                    $atributo=Atributos::where('variable','¿Cotizas a  Seguridad Social?')->where('categoria','Si')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->cotizasSeguridadSocial == "No" ){
                    $atributo=Atributos::where('variable','¿Cotizas a  Seguridad Social?')->where('categoria','No')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($basica->entidadReportado == "Bancos o entidades de financiamiento"){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Bancos o entidades de financiamiento')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->entidadReportado == "Empresa de Telecomunicaciones" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Empresa de Telecomunicaciones')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->entidadReportado == "Otros" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Otros')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($basica->estadoReportado == "castigada"){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Castigada')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->estadoReportado == "deuda pagada" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','deuda paga')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->estadoReportado == "reestructurada" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','Reestructurada')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->estadoReportado == "no paga" ){
                    $atributo=Atributos::where('variable','Entidad que te ha reportado')->where('categoria','No paga')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($basica->tiempoReportado == "1 mes"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','1 mes')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "2 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','2 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "3 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','3 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "4 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','4 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "5 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','5 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "6 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','6 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "7 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','7 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "8 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','8 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "9 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','9 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "10 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','10 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "11 meses"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','11 meses')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($basica->tiempoReportado == "mas de 1 ano"){
                    $atributo=Atributos::where('variable','Tiempo reportado')->where('categoria','Mas de 1 año')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($financiera->creditosBanco == "Si"){
                    $atributo=Atributos::where('variable','Tienes o has tenido una obligación (crédito) con una entidad financiera ?')->where('categoria','Si')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->creditosBanco == "No"){
                    $atributo=Atributos::where('variable','Tienes o has tenido una obligación (crédito) con una entidad financiera ?')->where('categoria','No')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($financiera->otrasCuentas == "Si"){
                    $atributo=Atributos::where('variable','Tienes otras cuentas de ahorro o corriente?')->where('categoria','Si')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->otrasCuentas == "No"){
                    $atributo=Atributos::where('variable','Tienes otras cuentas de ahorro o corriente?')->where('categoria','No')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if($financiera->tarjetasCredito == "Si"){
                    $atributo=Atributos::where('variable','Tienes tarjeta de credito')->where('categoria','Si')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if($financiera->tarjetasCredito == "No"){
                    $atributo=Atributos::where('variable','Tienes tarjeta de credito')->where('categoria','No')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                if(trim($basica->tipoAfiliacion) == "Cotizante"){
                    $atributo=Atributos::where('variable','Tipo de afiliacion')->where('categoria','Cotizante')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }else if(trim($basica->tipoAfiliacion) == "Beneficiario"){
                    $atributo=Atributos::where('variable','Tipo de afiliacion')->where('categoria','Beneficiario')->first();

                    $suma_basica+=(($atributo && $atributo->ponderacion) ? $atributo->ponderacion : 0);

                }

                $solicitudCount=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->count();
                $solicitudAPCount=Calculadora::where('estatus', 'aprobado')->where('idUserFk',$id)->count();

                $total_total=$suma_basica*100/953;
                if(round($total_total,2) >= $scoreNegado->desde && round($total_total,2) <= $scoreNegado->hasta){
                    // $estatus_solicitud = $scoreNegado->caso;
                }else if(round($total_total,2) >= $scorePreaprovado->desde && round($total_total,2) <= $scorePreaprovado->hasta){
                    // $estatus_solicitud = $scorePreaprovado->caso;
                }else if(round($total_total,2) >= $scoreAprovado->desde && round($total_total,2) <= $scoreAprovado->hasta){
                    // $estatus_solicitud = $scoreAprovado->caso;
                }
                $contra_oferta = false;

                // verificacion de numero telefonico contra operadoras
                $usuarioTelefono = \DB::table('users')
                ->where('users.id',$id)
                ->select('users.phone_number as phone_number')
                ->first();
                if($usuarioTelefono == null){
                    //si el usuario no tiene telefono
                    $estatus_solicitud ='negado';
                    $evaluacion_telefono = 'negado';
                    $resultado_evaluacion_telefono = 'El usuario no tiene telefono';
                }else{
                    if(strlen($usuarioTelefono->phone_number) == 10){
                        $prefijo = substr($usuarioTelefono->phone_number, 0, 3);
                        $telefonoCorrecto = \DB::table('operadoras')
                        ->where('operadoras.prefijo',$prefijo)
                        ->first();
                        if($telefonoCorrecto == null){
                            //Si el prefijo del telefono no esta entre los registrados
                            $estatus_solicitud ='negado';
                            $evaluacion_telefono = 'negado';
                            $resultado_evaluacion_telefono = 'El prefijo del telefono no esta entre los registrados';
                        }
                    }else{
                        //si el telefono mide menos de 10 caracteres
                        $estatus_solicitud ='negado';
                        $evaluacion_telefono = 'negado';
                        $resultado_evaluacion_telefono = 'El telefono mide menos de 10 caracteres';
                    }
                }

                if($financiera->tiempoDatacredito > 4){
                    $estatus_solicitud ='negado';
                    $evaluacion_filtro = "negado";
                    $evaluacion_tiempoDatacredito = 'negado';
                }


                if($solicitudCount > 0 ){

                    if($idSolicitud > 0){
                        $solicitud=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->find($idSolicitud);
                    }else{
                        $solicitud=Calculadora::where('estatus', 'incompleto')->where('idUserFk',$id)->first();
                    }
                 /** Inicio del analisis de filtros */
                    $caso1=Filtrado::where('id',1)->first();
                    $caso2=Filtrado::where('id',2)->first();
                    $caso3=Filtrado::where('id',3)->first();
                    $caso4=Filtrado::where('id',5)->first();
                    $caso5=Filtrado::where('id',6)->first();
                    $caso6=Filtrado::where('id',7)->first();
                    $caso7=Filtrado::where('id',8)->first();
                    $caso8=Filtrado::where('id',9)->first();
                    $caso9=Filtrado::where('id',10)->first();
                    $caso14=Filtrado::where('id',14)->first();
                    $caso16=Filtrado::where('id',16)->first();
                    $caso17=Filtrado::where('id',17)->first();
                    $totalingreso=$financiera->ingresoTotalMensual-$financiera->egresoTotalMensual;
                    $c5 = 0;
                    $c6 = 0;
                    $c14=0;
                    $aL = 0;
                    $tr = 0;
    				if($caso5->valor == "menos de 2 meses"){
                  		$c5 = 1;
                  	}else if($caso5->valor == "3 meses"){
                  		$c5 = 3;
                  	}else if($caso5->valor == "4 meses"){
                  		$c5 = 4;
                  	}else if($caso5->valor == "5 meses"){
                  		$c5 = 5;
                  	}else if($caso5->valor == "6 meses"){
                  		$c5 = 6;
                  	}else if($caso5->valor == "7 meses"){
                  		$c5 = 7;
                  	}else if($caso5->valor == "8 meses"){
                  		$c5 = 8;
                  	}else if($caso5->valor == "9 meses"){
                  		$c5 = 9;
                  	}else if($caso5->valor == "10 meses"){
                  		$c5 = 10;
                  	}else if($caso5->valor == "11 meses"){
                  		$c5 = 11;
                  	}else if($caso5->valor == "12 meses"){
                  		$c5 = 12;
                  	}else if($caso5->valor == "1 Año"){
                        $c5 = 12;
                    }else if($caso5->valor == "2 Año"){
                  		$c5 = 24;
                  	}else if($caso5->valor == "Tres Años o más"){
                  		$c5 = 48;
                  	}

                  	if($caso6->valor == "menos de 2 meses"){
                  		$c6 = 1;
                  	}else if($caso6->valor == "3 meses"){
                  		$c6 = 3;
                  	}else if($caso6->valor == "4 meses"){
                  		$c6 = 4;
                  	}else if($caso6->valor == "5 meses"){
                  		$c6 = 5;
                  	}else if($caso6->valor == "6 meses"){
                  		$c6 = 6;
                  	}else if($caso6->valor == "7 meses"){
                  		$c6 = 7;
                  	}else if($caso6->valor == "8 meses"){
                  		$c6 = 8;
                  	}else if($caso6->valor == "9 meses"){
                  		$c6 = 9;
                  	}else if($caso6->valor == "10 meses"){
                  		$c6 = 10;
                  	}else if($caso6->valor == "11 meses"){
                  		$c6 = 11;
                  	}else if($caso6->valor == "12 meses"){
                  		$c6 = 12;
                  	}else if($caso6->valor == "1 Año"){
                        $c6 = 12;
                    }else if($caso6->valor == "2 Año"){
                  		$c6 = 24;
                  	}else if($caso6->valor == "Tres Años o más"){
                  		$c6 = 48;
                  	}

                    if($caso14->valor == "1 mes"){
                        $c14 = 1;
                    }else  if($caso14->valor == "2 meses"){
                        $c14 = 2;
                    }else if($caso14->valor == "3 meses"){
                        $c14 = 3;
                    }else if($caso14->valor == "4 meses"){
                        $c14 = 4;
                    }else if($caso14->valor == "5 meses"){
                        $c14 = 5;
                    }else if($caso14->valor == "6 meses"){
                        $c14 = 6;
                    }else if($caso14->valor == "7 meses"){
                        $c14 = 7;
                    }else if($caso14->valor == "8 meses"){
                        $c14 = 8;
                    }else if($caso14->valor == "9 meses"){
                        $c14 = 9;
                    }else if($caso14->valor == "10 meses"){
                        $c14 = 10;
                    }else if($caso14->valor == "11 meses"){
                        $c14 = 11;
                    }else if($caso14->valor == "12 meses"){
                        $c14 = 12;
                    }else if($caso14->valor == "mas de 1 ano"){
                        $c14 = 12;
                    }

                    if($basica->tiempoReportado == "1 mes"){
                        $tr = 1;
                    }else  if($basica->tiempoReportado == "2 meses"){
                        $tr = 2;
                    }else if($basica->tiempoReportado == "3 meses"){
                        $tr = 3;
                    }else if($basica->tiempoReportado == "4 meses"){
                        $tr = 4;
                    }else if($basica->tiempoReportado == "5 meses"){
                        $tr = 5;
                    }else if($basica->tiempoReportado == "6 meses"){
                        $tr = 6;
                    }else if($basica->tiempoReportado == "7 meses"){
                        $tr = 7;
                    }else if($basica->tiempoReportado == "8 meses"){
                        $tr = 8;
                    }else if($basica->tiempoReportado == "9 meses"){
                        $tr = 9;
                    }else if($basica->tiempoReportado == "10 meses"){
                        $tr = 10;
                    }else if($basica->tiempoReportado == "11 meses"){
                        $tr = 11;
                    }else if($basica->tiempoReportado == "12 meses"){
                        $tr = 12;
                    }else if($basica->tiempoReportado == "mas de 1 ano"){
                        $tr = 12;
                    }

	                if($financiera->antiguedadLaboral == "menos de 2 meses"){
                  		$aL = 1;
                  	}else if($financiera->antiguedadLaboral == "3 meses"){
                  		$aL = 3;
                  	}else if($financiera->antiguedadLaboral == "4 meses"){
                  		$aL = 4;
                  	}else if($financiera->antiguedadLaboral == "5 meses"){
                  		$aL = 5;
                  	}else if($financiera->antiguedadLaboral == "6 meses"){
                  		$aL = 6;
                  	}else if($financiera->antiguedadLaboral == "7 meses"){
                  		$aL = 7;
                  	}else if($financiera->antiguedadLaboral == "8 meses"){
                  		$aL = 8;
                  	}else if($financiera->antiguedadLaboral == "9 meses"){
                  		$aL = 9;
                  	}else if($financiera->antiguedadLaboral == "10 meses"){
                  		$aL = 10;
                  	}else if($financiera->antiguedadLaboral == "11 meses"){
                  		$aL = 11;
                  	}else if($financiera->antiguedadLaboral == "12 meses"){
                        $aL = 12;
                    }else if($financiera->antiguedadLaboral == "1 Año"){
                  		$aL = 12;
                  	}else if($financiera->antiguedadLaboral == "Dos Años"){
                  		$aL = 24;
                  	}else if($financiera->antiguedadLaboral == "Tres Años o más"){
                  		$aL = 48;
                  	}

                    if(trim($caso1->signo)=='<'){

             	 if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                  trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                  trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                  trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual < $caso1->valor ){

                        $estatus_solicitud ='negado';
                        $evaluacion_filtro = "negado";
                        $evaluacion_caso1 = "negado";

                       }

             }else if(trim($caso1->signo)=='>'){


				 if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                 trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                 trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                 trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual>$caso1->valor ){



				                        $estatus_solicitud ='negado';
                                        $evaluacion_filtro = "negado";
                                        $evaluacion_caso1 = "negado";

				                       }

             }else if(trim($caso1->signo)=='<='){

             	 if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                  trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                  trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                  trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual<=$caso1->valor ){



				                        $estatus_solicitud ='negado';
                                        $evaluacion_caso1 = "negado";
                                        $evaluacion_filtro = "negado";
				                       }
             }else if(trim($caso1->signo)=='>='){

             	 if( (trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                  trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                  trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                  trim($financiera->situacionLaboral)=='Empleado/a temporal') && $financiera->ingresoTotalMensual>=$caso1->valor ){



				                        $estatus_solicitud ='negado';
                                        $evaluacion_filtro = "negado";
                                        $evaluacion_caso1 = "negado";

				                       }
             }
             if(trim($caso2->signo)=='<'){
					   if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual < $caso2->valor ){


					                        $estatus_solicitud ='negado';
                                            $evaluacion_filtro = "negado";
                                            $evaluacion_caso2 = "negado";

					                       }

                   }else if(trim($caso2->signo)=='>'){
                   		 if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual > $caso2->valor ){

					                        $estatus_solicitud ='negado';
                                            $evaluacion_filtro = "negado";
                                            $evaluacion_caso2 = "negado";

					                       }
                   }else if($caso2->signo=='<='){

                   		 if(($financiera->situacionLaboral == 'Independiente' || $financiera->situacionLaboral=='Empleado/a por servicios') && $financiera->ingresoTotalMensual <= $caso2->valor ){

					                        $estatus_solicitud ='negado';
                                            $evaluacion_filtro = "negado";
                                            $evaluacion_caso2 = "negado";

					                       }
                   }else if(trim($caso2->signo)=='>='){
						 if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && $financiera->ingresoTotalMensual <= $caso2->valor ){

										                        $estatus_solicitud ='negado';
                                                                $evaluacion_filtro = "negado";
										                        $evaluacion_caso2 = "negado";
										                       }

                   }

                    if($caso3->valor==$basica->cotizasSeguridadSocial){



                        $estatus_solicitud ='negado';
                        $evaluacion_filtro = "negado";
                        $evaluacion_caso3 = "negado";

                       }else if(trim($basica->cotizasSeguridadSocial)=='Si' && trim($basica->tipoAfiliacion)=="Beneficiario"){


                        $estatus_solicitud ='negado';
                        $evaluacion_filtro = "negado";
                        $evaluacion_caso3 = "negado";

                        }

                        if($caso4->valor==$basica->estrato){



                        $estatus_solicitud ='negado';
                        $evaluacion_filtro = "negado";
                        $evaluacion_caso4 = "negado";

                       }

                       if(trim($caso5->signo) =='>'){
                            if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                            trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                            trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                            trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL>$c5)){


                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso5 = "negado";

                            }
                        }else if(trim($caso5->signo) =='<'){
                            if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                            trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                            trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                            trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL<$c5)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso5 = "negado";

                            }
                        }else if(trim($caso5->signo) =='>='){
                            if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                            trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                            trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                            trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL>=$c5)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso5 = "negado";

                            }
                        }else if(trim($caso5->signo) =='<='){
                            if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                            trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                            trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                            trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL<=$c5)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso5 = "negado";

                            }
                        }else if(trim($caso5->signo) =='=='){
                            if((trim($financiera->situacionLaboral)=='Empleado/a termino indefinido' ||
                            trim($financiera->situacionLaboral)=='Empleado/a termino fijo renovable'||
                            trim($financiera->situacionLaboral)=='Empleado/a obra labor'||
                            trim($financiera->situacionLaboral)=='Empleado/a temporal') && ($aL==$c5)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso5 = "negado";

                            }
                        }
                        if(trim($caso6->signo) =='>'){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL>$c6)){


                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso6 = "negado";

                            }
                        }else if(trim($caso6->signo) =='<'){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL<$c6)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso6 = "negado";

                            }
                        }else if(trim($caso6->signo) =='>='){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL>=$c6)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso6 = "negado";
                                $evaluacion_caso6 = "negado";

                            }
                        }else if(trim($caso6->signo) =='<='){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL<=$c6)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso6 = "negado";

                            }
                        }else if(trim($caso6->signo) =='=='){
                            if((trim($financiera->situacionLaboral) == 'Independiente' || trim($financiera->situacionLaboral)=='Empleado/a por servicios') && ($aL==$c6)){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso6 = "negado";

                            }
                        }


                          if(trim($caso14->signo) =='>'){

                                      if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr>$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso14 = "negado";

                               }

                        }else if(trim($caso14->signo) =='<'){


                                      if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr<$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso14 = "negado";

                               }

                        }else if(trim($caso14->signo) =='<='){


                                      if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr<=$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso14 = "negado";

                               }

                        }else if(trim($caso14->signo) =='>='){



                                      if(trim($basica->centralRiesgo)=='Si' && (trim($basica->estadoReportado)=='castigada' || (trim($basica->estadoReportado)=='deuda pagada' && ($tr>=$c14) ) || trim($basica->estadoReportado)=='reestructurada' || trim($basica->estadoReportado)=='no paga')){

                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso14 = "negado";

                               }

                        }


                        if(!empty($caso8->valor) && $caso8->valor != ""){
                            $explode = explode("|", $caso8->valor);
                            foreach ($explode as $key => $value) {
                               if(trim($financiera->situacionLaboral) == trim($value)){
                                    $estatus_solicitud ='negado';
                                    $evaluacion_filtro = "negado";
                                    $evaluacion_caso8 = "negado";
                                    break;
                                }
                            }

                        }


                       if(trim($financiera->situacionLaboral) =='Estudiante' && $financiera->fuenteIngreso == $caso9->valor){

                        $estatus_solicitud ='negado';
                        $evaluacion_filtro = "negado";
                        $evaluacion_caso9 = "negado";
                       }

                    //    if(trim($financiera->situacionLaboral) =='Estudiante' ||
                    //    trim($financiera->situacionLaboral) =='Pensionado' ||
                    //    trim($financiera->situacionLaboral) == 'Independiente' ||
                    //    trim($financiera->situacionLaboral)=='Empleado/a por servicios'){

                    //         $estatus_solicitud ='negado';

                    //    }
                    if(!empty($caso16->valor) && $caso16->valor != ""){
                        $explodeusoCredito = explode("|", $caso16->valor);
                        foreach ($explodeusoCredito as $key => $value) {
                           if(trim($financiera->usoCredito) == trim($value)){
                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso1 = "negado";
                                break;
                            }
                        }

                    }
                    if(!empty($caso17->valor) && $caso17->valor != ""){
                        $explodecomoTePagan = explode("|", $caso17->valor);
                        foreach ($explodecomoTePagan as $key => $value) {
                           if(trim($financiera->comoTePagan) == trim($value)){
                                $estatus_solicitud ='negado';
                                $evaluacion_filtro = "negado";
                                $evaluacion_caso17 = "negado";
                                break;
                            }
                        }

                    }
                    $arrFiltro =array(
                        array(
                            "nombre"=>$caso1->variable,
                            "estatus"=>$evaluacion_caso1,
                            "respuesta"=>$financiera->situacionLaboral." | ".$financiera->ingresoTotalMensual
                        ),
                        array(
                            "nombre"=>$caso2->variable,
                            "estatus"=>$evaluacion_caso2,
                            "respuesta"=>$financiera->situacionLaboral." | ".$financiera->ingresoTotalMensual
                        ),
                        array(
                            "nombre"=>$caso3->variable,
                            "estatus"=>$evaluacion_caso3,
                            "respuesta"=>$basica->cotizasSeguridadSocial." | ".$basica->tipoAfiliacion
                        ),
                        array(
                            "nombre"=>$caso4->variable,
                            "estatus"=>$evaluacion_caso4,
                            "respuesta"=>$basica->estrato
                        ),
                        array(
                            "nombre"=>$caso5->variable,
                            "estatus"=>$evaluacion_caso5,
                            "respuesta"=>$financiera->situacionLaboral." | ".$financiera->antiguedadLaboral
                        ),
                        array(
                            "nombre"=>$caso6->variable,
                            "estatus"=>$evaluacion_caso6,
                            "respuesta"=>$financiera->situacionLaboral." | ".$financiera->antiguedadLaboral
                        ),
                        array(
                            "nombre"=>"Tiempo en Data credito",
                            "estatus"=>$evaluacion_tiempoDatacredito,
                            "respuesta"=>$financiera->tiempoDatacredito
                        ),
                        array(
                            "nombre"=>$caso8->variable,
                            "estatus"=>$evaluacion_caso8,
                            "respuesta"=>$financiera->situacionLaboral
                        ),
                        array(
                            "nombre"=>$caso9->variable,
                            "estatus"=>$evaluacion_caso9,
                            "respuesta"=>$financiera->situacionLaboral." | ".$financiera->fuenteIngreso
                        ),
                        array(
                            "nombre"=>$caso14->variable,
                            "estatus"=>$evaluacion_caso14,
                            "respuesta"=>$basica->centralRiesgo." | ".$basica->estadoReportado." | ".$basica->tiempoReportado
                        ),
                        array(
                            "nombre"=>$caso16->variable,
                            "estatus"=>$evaluacion_caso16,
                            "respuesta"=>$financiera->usoCredito
                        ),
                        array(
                            "nombre"=>$caso17->variable,
                            "estatus"=>$evaluacion_caso17,
                            "respuesta"=>$financiera->comoTePagan
                        )
                    );
                    /** Fin del analisis de filtros */

                    /** analisis de matriz de calculo */
                       $resultMatriz = self::matriz($id);
                       if($resultMatriz["resultado"] == false){
                        $estatus_solicitud = "negado";
                        $evaluacion_matriz = "rechazado";
                       }


                    /** actualizar estatus de la solicitud */
                    if($estatus_solicitud == "negado"){
                       $solicitud->estatus = $estatus_solicitud;
                    }else{
                        $solicitud->estatus = "pendiente";
                    }

                    $solicitud->puntaje_total = round($total_total,2);
                    $solicitud->save();
                    /** crear evaluacion donde se guardara paso a paso los analisis*/
                    $existe = Evaluacion::where("idSolicitudFk", $solicitud->id)->where("idUserFk",$id)->count();

                    if($existe == 0){
                    $resultEvalu = Evaluacion::create([
                        'idSolicitudFk'    => $solicitud->id,
                        'idUserFk'     => $id,
                        'estatus'    => $estatus_solicitud == 'negado' ? 'negado' : 'pendiente' ,
                        'gastoMonetario'=> $resultMatriz['calculoGastosString'],
                        'calculoIngreso'=> $resultMatriz['calculoString'],
                        'telefono' => $evaluacion_telefono,
                        'resultadoTelefono' => $resultado_evaluacion_telefono,
                        'filtro' => $evaluacion_filtro,
                        'balance'=> $evaluacion_matriz,
                        'resultadoFiltro' => json_encode($arrFiltro)
                        ]);
                    }else{
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $solicitud->id)
                        ->where('evaluacions.idUserFk',$id)
                        ->update([
                            'estatus'    => $estatus_solicitud == 'negado' ? 'negado' : 'pendiente',
                            'gastoMonetario'=> $resultMatriz['calculoGastosString'],
                            'calculoIngreso'=> $resultMatriz['calculoString'],
                            'telefono' => $evaluacion_telefono,
                            'resultadoTelefono' => $resultado_evaluacion_telefono,
                            'filtro' => $evaluacion_filtro,
                            'balance'=> $evaluacion_matriz,
                            'resultadoFiltro' => json_encode($arrFiltro)
                        ]);
                        // $result = Evaluacion::where("idSolicitudFk", $request->idSolicitudFk)->where("idUserFk",$request->idUserFk)->first();
                    }
                    Log::error('Analisis=> '.$solicitud->id.':'.$estatus_solicitud);
                    if($estatus_solicitud != 'negado'){
                        // $contra_oferta = self::crearContraOferta($solicitud->id);
                            $usuarios=User::where('id',$usuario->id)->first();
                            $contenido=Correos::where('pertenece','solicitud')->first();

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
                            $msjAdmin = '<p>Se registro un credito aprobado: <br> Nro de Credito: '.$numerCredito.' <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                            $infoAdmin =[
                                'Contenido'=>$msjAdmin,
                                ];
                            Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                                $msj->subject('Notificacion de credito aprobado');
                                $msj->to('info@creditospanda.com');
                                });
                            Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                                $msj->subject($name.',tu solicitud de crédito ha sido recibida y está siendo procesada');
                                $msj->to($email);
                             });
                        }


                        $contenido232=Correos::where('pertenece','Verificacion Reportado')->first();

                        if($contenido232 && $contenido232->estatus=='activo'){
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
                            $content=$contenido232->contenido;

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
                            $msjAdmin = '<p>Se solicito verificacion de reportado: <br> Nro de Credito: '.$numerCredito.' <br> Nombre: '.$name.' '.$last_name.'<br> Cedula: '.$cedula.'<br> Email: '.$email.'</p>';
                            $infoAdmin =[
                                'Contenido'=>$msjAdmin,
                                ];
                            Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                                $msj->subject('Notificacion de verificacion de reportado');
                                $msj->to('info@creditospanda.com');
                                });
                            Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name){
                                $msj->from("evaluacion@creditospanda.com","Créditos Panda");
                                $msj->subject($name.', estas a un paso de recibir tu dinero envianos los siguientes documentos');
                                $msj->to($email);
                             });
                        }
                    }else{

                        $usuarios=User::where('id',$usuario->id)->first();
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
                         $name=$usuarios->first_name;
                         $last_name=$usuarios->last_name;
                         $email=$usuarios->email;
                         $cedula=$usuarios->n_document;
                         $content=$contenido->contenido;
                         $contentInvitacion=$contenido_invitacion->contenido;


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
                     }

                    }

                }

                $usuarios=User::where('id',$usuario->id)->first();
                $arreglito= [4];
                $usuarios->roles()->sync($arreglito);
                $usuarios->save();

                $res = [
                    'total' => $suma_basica,
                    'total_en_porcentaje' =>round($total_total,2),
                    'estatus_solicitud' => $estatus_solicitud,
                    'balance_inicial' =>$balance_inicial,
                    'basica'  => $basica,
                    'referencia'    => $referencia,
                    'financiera'          => $financiera,
                    'atributos'          => $atributos,
                    'variables'    => $variables,
                    'solicitud' => $solicitud
                ];
                return $res;

                // return response()->json([
                //     'total' => $suma_basica,
                //     'total_en_porcentaje' =>round($total_total,2),
                //     'estatus_solicitud' => $estatus_solicitud,
                //     'balance_inicial' =>$balance_inicial,
                //     'balance_faltante' =>$balance_faltante,
                //     'basica'  => $basica,
                //     'referencia'    => $referencia,
                //     'financiera'          => $financiera,
                //     'atributos'          => $atributos,
                //     'variables'    => $variables
                // ]);
            }catch (\Exception $e) {
                if($e instanceof ValidationException) {
                    // return response()->json($e->errors(),402);
                    return $e->errors();
                }
                DB::rollback(); // Retrocedemos la transaccion
                Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                $me = [
                    'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                ];
                return $me;
                // return response()->json([
                //     'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                // ], 500);
            }
        }

        function empezarAnalisis(Request $request){
            try{
                // set_time_limit(240);
                $res = [];
                if(trim($request->inicioAnalisis) == "nuevo"){
                    \DB::table('financieras')
                    ->where('financieras.idUserFk',$request->id)
                    ->update([
                        'tiempoDataCredito'=>$request->tiempoReporteMeses
                    ]);
                    $res = self::analisis($request->id);
                        /** analisis correo */
                        $res["error"]=false;
                        $res["msj_error"]="";
                        $res["tipo_error"]="";
                    if($res['estatus_solicitud'] == 'aprobado'){
                        $resMail= self::controlMailInterno($request->id,$res['solicitud']['id']);
                        $res["analisis_email"] = $resMail;
                        if($resMail['estatus'] == "aprobado"){
                            $resScrapping= self::analisisWebScrappingInterno($request->id,$res['solicitud']['id']);
                            $res["analisis_scrapping"] = $resScrapping;

                            if($resScrapping['estatus'] == "aprobado"){
                                $res["analisis_scrapping"] = $resScrapping;
                                $res['tokenAnalizer'] = $resScrapping['tokenAnalizer'];
                            }else if($resScrapping['estatus'] == "negado"){
                                $res['estatus_solicitud'] = "negado";
                            }else{
                                $resVerifiquese= self::analisisKonivinInterno($request->id,$res['solicitud']['id']);
                                $res["analisis_verifiquese"] = $resVerifiquese;
                                if($resVerifiquese['estatus'] == "aprobado"){
                                    $res['estatus_solicitud'] = "aprobado";
                                    $res['tokenAnalizer'] = $resVerifiquese['result']['tokenAnalizer'];
                                }else if($resVerifiquese['estatus'] == "negado"){
                                    $res['estatus_solicitud'] = "negado";
                                }else{
                                    $res["error"]=true;
                                    $res["tipo_error"]="verifiquese";
                                    $res["msj_error"]="Se esta presentado un error en el sistema. Por  favor comunicarse con el equipo de creditos panda al whatsapp 3212403734 y escribir REF 003.";
                                } 
                            }
                        }else if($resMail['estatus'] == "negado"){
                                $res['estatus_solicitud'] = "negado";
                        }else{
                                $res["error"]=true;
                                $res["tipo_error"]="email";
                                $res["msj_error"]="Se esta presentado un error en el sistema. Por  favor comunicarse con el equipo de creditos panda al whatsapp 3212403734 y escribir REF 001.";
                        }
                    }
                }else if(trim($request->inicioAnalisis) == "email"){
                    $res['estatus_solicitud'] = 'aprobado';
                    $res["error"]=false;
                    $res["msj_error"]="";
                    $res["tipo_error"]="";
                    $resMail= self::controlMailInterno($request->id,$request->idSolicitud);
                        $res["analisis_email"] = $resMail;
                        if($resMail['estatus'] == "aprobado"){
                            $resScrapping= self::analisisWebScrappingInterno($request->id,$request->idSolicitud);
                            $res["analisis_scrapping"] = $resScrapping;

                            if($resScrapping['estatus'] == "aprobado"){
                                $res["analisis_scrapping"] = $resScrapping;
                                $res['tokenAnalizer'] = $resScrapping['tokenAnalizer'];
                            }else if($resScrapping['estatus'] == "negado"){
                                $res['estatus_solicitud'] = "negado";
                            }else{
                                $resVerifiquese= self::analisisKonivinInterno($request->id,$request->idSolicitud);
                                $res["analisis_verifiquese"] = $resVerifiquese;
                                if($resVerifiquese['estatus'] == "aprobado"){
                                    $res['estatus_solicitud'] = "aprobado";
                                    $res['tokenAnalizer'] = $resVerifiquese['result']['tokenAnalizer'];
                                }else if($resVerifiquese['estatus'] == "negado"){
                                    $res['estatus_solicitud'] = "negado";
                                }else{
                                    $res["error"]=true;
                                    $res["tipo_error"]="verifiquese";
                                    $res["msj_error"]="Se esta presentado un error en el sistema. Por  favor comunicarse con el equipo de creditos panda al whatsapp 3212403734 y escribir REF 003.";
                                } 
                            }
                        }else if($resMail['estatus'] == "negado"){
                                $res['estatus_solicitud'] = "negado";
                        }else{
                                $res["error"]=true;
                                $res["tipo_error"]="email";
                                $res["msj_error"]="Se esta presentado un error en el sistema. Por  favor comunicarse con el equipo de creditos panda al whatsapp 3212403734 y escribir REF 001.";
                        }

                }else if(trim($request->inicioAnalisis) == "verifiquese"){
                    $res['estatus_solicitud'] = 'aprobado';
                    $res["error"]=false;
                    $res["msj_error"]="";
                    $res["tipo_error"]="";
                    $resScrapping= self::analisisWebScrappingInterno($request->id,$request->idSolicitud);
                    $res["analisis_scrapping"] = $resScrapping;

                    if($resScrapping['estatus'] == "aprobado"){
                        $res["analisis_scrapping"] = $resScrapping;
                        $res['tokenAnalizer'] = $resScrapping['tokenAnalizer'];
                    }else if($resScrapping['estatus'] == "negado"){
                        $res['estatus_solicitud'] = "negado";
                    }else{
                        $resVerifiquese= self::analisisKonivinInterno($request->id,$request->idSolicitud);
                        $res["analisis_verifiquese"] = $resVerifiquese;
                        if($resVerifiquese['estatus'] == "aprobado"){
                            $res['estatus_solicitud'] = "aprobado";
                            $res['tokenAnalizer'] = $resVerifiquese['result']['tokenAnalizer'];
                        }else if($resVerifiquese['estatus'] == "negado"){
                            $res['estatus_solicitud'] = "negado";
                        }else{
                            $res["error"]=true;
                            $res["tipo_error"]="verifiquese";
                            $res["msj_error"]="Se esta presentado un error en el sistema. Por  favor comunicarse con el equipo de creditos panda al whatsapp 3212403734 y escribir REF 003.";
                        } 
                    }  

                }
                    /**fin analisis correo */
                return response()->json($res);
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
        function createContraOferta(Request $request){
            try{
                $res = self::crearContraOferta($request->idSolicitud, $request->tipo, $request->tipo_monto);
                $calculadora=Calculadora::where('id',$request->idSolicitud)->first();
                $usuario =User::where('id',$calculadora->idUserFk)->first();
                $financiera=Financiera::where('idUserFk',$calculadora->idUserFk)->first();
                $basica=Basica::where('idUserFk',$calculadora->idUserFk)->first();
                $referencia=Referencias::where('idUserFk',$calculadora->idUserFk)->first();


                $cuota=round($res->totalPagar/$res->plazo);
                $params =  [
                    'usuario'      =>  $usuario,
                    'financiera'      =>  $financiera,
                    'basica'      =>  $basica,
                    'referencia'      =>  $referencia,
                    'solicitud'      =>  $calculadora,
                    'contra_oferta' => $res,
                    'cuota' => $cuota,
                    'ip' => "",
                    'fecha_actual' => "",
                    'codigo_sms' => "",
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
                    'contra_oferta' => $res,
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
                    'contra_oferta' => $res,
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
                  'idUserFk'     => $usuario->id,
                  'idSolicitudFk'  => $calculadora->id,
                  'token_firma' => $token
                  ]);
                  $usuarios=User::where('id',$usuario->id)->first();
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
                              $monto="$".number_format($res->totalPagar);
                              $numerCredito=$credito->numero_credito;
                              $expedicion=$credito->created_at;
                              $plazo_pago = $res->plazo." ".$Lplazo;
                              $monto_aprobado = "$".number_format($res->montoAprobado);
                              $monto_solicitado = "$".number_format($res->montoSolicitado);

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
$fechita=date('d-m-Y h:i A');
                      if($request->tipo_monto==0){
                        $credito->ofertaEnviada=0;
                        $credito->documentoCarta = $pathToFileCartaAutorizacion;
                        $credito->documentoPagare = $pathToFilePagare;
                        $credito->documentoContrato = $pathToFile;
                        $credito->fechaDocEnviado = $fechita;
                        $credito->save();
                    }else{
                        $credito->ofertaEnviada=1;
                        $credito->documentoCarta = $pathToFileCartaAutorizacion;
                        $credito->documentoPagare = $pathToFilePagare;
                        $credito->documentoContrato = $pathToFile;
                        $credito->fechaDocEnviado = $fechita;
                        $credito->save();
                    }

                return response()->json($res);
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
        public function crearContraOferta($idSolicitud, $tipo, $tipo_monto){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $configCal = ConfigCalculadora::where('tipo','=',$tipo)->first();
                $configContraOferta = ConfigContraOferta::where('tipo_credito','=',$tipo)->first();
                $solicitud=Calculadora::find($idSolicitud);
                if($tipo == 2){
                    $porPlat = $configCal->porcentaje_plataforma;
                    $porIva = $configCal->porcentaje_iva;
                    $tasa = $configCal->tasa;
                    $porExp = $configCal->porcentaje_express;
                    $plazoDias=$solicitud->plazo;
                    $montoSolicitadoDias=$tipo_monto == 0 ? $configContraOferta->monto_minimo : $configContraOferta->monto_maximo;
                    $tasitaNueva=(pow((1+($tasa/100)),($plazoDias/360))-1);
                    $t_interesDias=$tasitaNueva*$montoSolicitadoDias;

                    $subtotalDias=($montoSolicitadoDias)+($t_interesDias);

                    $plataformaDias=$porPlat*$plazoDias;
                    $ap_expressDias=($montoSolicitadoDias*$porExp)/100;
                    $ivaDias=($plataformaDias+$ap_expressDias)*$porIva/100;
                    $totalDias=$subtotalDias+$plataformaDias+$ap_expressDias+$ivaDias;

                    $contra_oferta = ContraOferta::create([
                        'montoSolicitado'    => $solicitud->montoSolicitado,
                        'montoAprobado'    => round($montoSolicitadoDias),
                        'plazo'     => $plazoDias,
                        'tasaInteres'  => round($t_interesDias),
                        'subtotal'  => round($subtotalDias),
                        'plataforma'     => round($plataformaDias),
                        'aprobacionRapida'  => round($ap_expressDias),
                        'iva'  => round($ivaDias),
                        'totalPagar' =>round($totalDias),
                        'tipoCredito' => 'd',
                        'idUserFk' => $solicitud->idUserFk,
                        'idCalculadoraFk' => $solicitud->id,
                        'numero_credito' => $solicitud->numero_credito,
                        'puntaje_total' => $solicitud->puntaje_total,
                        'estatus' =>'rechazado'
                    ]);
                    $solicitud->estatus_contraOferta  = 'rechazado';
                    $solicitud->save();
                }else{
                    $porPlat = $configCal->porcentaje_plataforma;
                    $porIva = $configCal->porcentaje_iva;
                    $tasa = $configCal->tasa;
                    $porExpUno = $configCal->porcentaje_express;
                    $porExpDos = $configCal->porcentaje_express_dos;
                    $porExpTres = $configCal->porcentaje_express_tres;
                    $montoSolicitado=$tipo_monto == 0 ? $configContraOferta->monto_minimo : $configContraOferta->monto_maximo;
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

                      if($montoSolicitado<=1200000){
                        $ap_express=round($montoSolicitado)*$porExpUno/100;
                        $plataforma=round($plataforma)*round($plazoMeses);
                        $iva=(round($plataforma)+round($ap_express))*$porIva/100;
                        $total=round($subtotal)+round($plataforma)+round($ap_express)+round($iva);
                        $cuotaMensual=round(round($total)/round($plazoMeses));

                      }else
                      if($montoSolicitado>1200000 && $montoSolicitado<=1700000){
                        $ap_express=round($montoSolicitado)*$porExpDos/100;

                        $plataforma=round($plataforma)*round($plazoMeses);
                        $iva=(round($plataforma)+round($ap_express))*$porIva/100;
                        $total=round($subtotal)+round($plataforma)+round($ap_express)+round($iva);
                        $cuotaMensual=round(round($total)/round($plazoMeses));

                      }else
                      if($montoSolicitado>=1700001){
                        $ap_express=round($montoSolicitado)*$porExpTres/100;
                        $plataforma=round($plataforma)*round($plazoMeses);
                        $plata=round($plataforma)*round($plazoMeses);
                        $iva=(round($plataforma)+round($ap_express))*$porIva/100;
                        $total=round($subtotal)+round($plataforma)+round($ap_express)+round($iva);
                        $cuotaMensual=round(round($total)/round($plazoMeses));

                      }

                      $contra_oferta = ContraOferta::create([
                        'montoSolicitado'    => $solicitud->montoSolicitado,
                        'montoAprobado'    => round($montoSolicitado),
                        'plazo'     => $plazoMeses,
                        'tasaInteres'  => round($taxInTotal),
                        'subtotal'  => round($subtotal),
                        'plataforma'     => round($plataforma),
                        'aprobacionRapida'  => round($ap_express),
                        'iva'  => round($iva),
                        'totalPagar' =>round($total),
                        'tipoCredito' => 'm',
                        'idUserFk' => $solicitud->idUserFk,
                        'idCalculadoraFk' => $solicitud->id,
                        'numero_credito' => $solicitud->numero_credito,
                        'puntaje_total' => $solicitud->puntaje_total,
                        'estatus' =>'rechazado'
                    ]);

                    $solicitud->estatus_contraOferta  = 'rechazado';
                    $solicitud->save();
                }
                DB::commit(); // Guardar transaccion de la base de datos
                return $contra_oferta;
            }catch (\Exception $e) {
                if($e instanceof ValidationException) {
                    // return response()->json($e->errors(),402);
                    return $e->errors();
                }
                DB::rollback(); // Retrocedemos la transaccion
                Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                $me = [
                    'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                ];
                return $me;
                // return response()->json([
                //     'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
                // ], 500);
            }

        }

        function getAllCreditos(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
                ->leftJoin('evaluacions', 'evaluacions.idSolicitudFk', '=', 'calculadoras.id')
            ->select('calculadoras.id as id_solicitud','calculadoras.created_at as fecha_solicitud','evaluacions.estatus as evaluacion_estatus','users.*','evaluacions.*','calculadoras.*');


            if(!empty($request->search)){
                if(strtolower($request->search) =='por firmar'){
                     $request->search='pendiente';

                    }else if(strtolower($request->search) =='por desembolsar'){
                        $request->search='firmado';
                    }else{
                        $request->search=$request->search;
                    }
                 $query->where('calculadoras.estatus','aprobado');
                 $query->where('evaluacions.selfie','!=','rechazado');
                $query->where('evaluacions.identidad','!=','rechazado');

                $query->where('evaluacions.balance','!=','rechazado');
                $query->where('evaluacions.data_credito','!=','rechazado');
                // $query->where(function($query2) use ($request) {
                //     $query2->orWhere('calculadoras.estatus','novado')
                //     ->OrWhere('calculadoras.estatus','aprobado');
                // });

                //  $query->Where('numero_credito','LIKE','%'.$request->search.'%');
                //  $query->orWhere('users.first_name','LIKE','%'.$request->search.'%');
                //  $query->orWhere('users.last_name','LIKE','%'.$request->search.'%');

                     $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('calculadoras.estatus','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
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
                // $query->where(function($query2) use ($request) {
                //     $query2->orWhere('calculadoras.estatus','novado')
                //     ->OrWhere('calculadoras.estatus','aprobado');
                // });
               $query->where('calculadoras.estatus','aprobado');
               $query->where('evaluacions.selfie','!=','rechazado');
               $query->where('evaluacions.identidad','!=','rechazado');

               $query->where('evaluacions.balance','!=','rechazado');
               $query->where('evaluacions.data_credito','!=','rechazado');
               if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.created_at','>=', date("Y-m-d H:i:s", strtotime($d)));
                    $query->where('calculadoras.created_at','<=', date("Y-m-d H:i:s", strtotime($h)));
                }
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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

        function getAllCreditosAbiertos(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')

            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');


            if(!empty($request->search)){
                if(strtolower($request->search) =='por firmar'){
                     $request->search='pendiente';

                    }else if(strtolower($request->search) =='por desembolsar'){
                        $request->search='firmado';
                    }else{
                        $request->search=$request->search;
                    }
                    if($request->estatus){
                        $query->where('calculadoras.estatus',$request->estatus);
                        if(($request->estatus == "moroso" || $request->estatus == "pendiente de novacion") && $request->desdeMora !='' && $request->hastaMora !=''){
                            $query->where('calculadoras.diasMora','>=', $request->desdeMora);
                            $query->where('calculadoras.diasMora','<=', $request->hastaMora);
                        }
                    }else{
                       $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.estatus','novado')
                        ->OrWhere('calculadoras.estatus','pendiente de novacion')
                        ->OrWhere('calculadoras.estatus','moroso')
                        ->OrWhere('calculadoras.estatus','restructurado')
                        ->OrWhere('calculadoras.estatus','abierto');
                    });
                    }

                     $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('calculadoras.estatus','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                });
                if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaDesembolso','>=', $request->since);
                    $query->where('calculadoras.fechaDesembolso','<=', $request->until);
                }

            }else{
            //    $query->where('calculadoras.estatus','abierto');
            if($request->estatus){
                $query->where('calculadoras.estatus',$request->estatus);
                if(($request->estatus == "moroso" || $request->estatus == "pendiente de novacion") && $request->desdeMora !='' && $request->hastaMora !=''){
                    $query->where('calculadoras.diasMora','>=', $request->desdeMora);
                    $query->where('calculadoras.diasMora','<=', $request->hastaMora);
                }
            }else{
               $query->where(function($query2) use ($request) {
                $query2->orWhere('calculadoras.estatus','novado')
                ->OrWhere('calculadoras.estatus','pendiente de novacion')
                ->OrWhere('calculadoras.estatus','moroso')
                ->OrWhere('calculadoras.estatus','restructurado')
                ->OrWhere('calculadoras.estatus','abierto');
            });
            }

               if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaDesembolso','>=', $request->since);
                    $query->where('calculadoras.fechaDesembolso','<=', $request->until);
                }
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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

        function getCreditosMorosos(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
                    ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');


                $query->where('calculadoras.estatus', "moroso");

                if(!empty($request->search)){
                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                    });
                }

                if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaDesembolso','>=', $request->since);
                    $query->where('calculadoras.fechaDesembolso','<=', $request->until);
                }

                if($request->desdeMora !='' && $request->hastaMora !=''){
                    $query->where('calculadoras.diasMora','>=', $request->desdeMora);
                    $query->where('calculadoras.diasMora','<=', $request->hastaMora);
                }

                $query->with(['pagos', 'hitorial_contactos.colaborador']);

                $query->with(['historial_ultimo_contacto' => function($query2){
                    return $query2->orderBy('fechaPtp', 'DESC');
                }]);

                $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
                        'msj' => 'No se encontraron registros.',
                    ], 200);
                }
                return response()->json($response);
            }catch (\Exception $e) {
                Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                return response()->json([
                    'message' => 'Ha ocurrido un error al tratar de buscar los datos.',
                ], 500);
            }
        }

        public function generateDescuentoLibranzaDocument(Request $request){

            $userBasica = User::where('id',$request->userId)->with('financiera')->first();
            $request['userBasica'] = $userBasica;

            return \PDFt::loadView('reports.pdf.descuento_libranza', $request)->download('nombre-archivo.pdf');

        }

        public function generateControlBancoDocument(Request $request){

            $userBasica = User::where('id',$request->userId)->with('financiera')->first();
            $request['userBasica'] = $userBasica;
            $request['today'] = date('d/m/Y');

            return \PDFt::loadView('reports.pdf.control_deposito_bancario', $request)->download('nombre-archivo.pdf');

        }

        public function generateAvisoPrejuridicoDocument(Request $request){

            $userBasica = User::where('id',$request->userId)->with('financiera')->first();
            $request['userBasica'] = $userBasica;
            $request['today'] = date('d/m/Y');
            $request['montos'] = json_decode($request->montos);

            return \PDFt::loadView('reports.pdf.aviso_prejuridico', $request)->download('nombre-archivo.pdf');

        }

        function getAllCreditosCerrados(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')

            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');


            if(!empty($request->search)){
                if(strtolower($request->search) =='por firmar'){
                     $request->search='pendiente';

                    }else if(strtolower($request->search) =='por desembolsar'){
                        $request->search='firmado';
                    }else{
                        $request->search=$request->search;
                    }

                    // $query->where('calculadoras.estatus','abierto');
                    if($request->estatus){
                        $query->where('calculadoras.estatus',$request->estatus);
                    }else{
                        $query->where(function($query2) use ($request) {
                            $query2->orWhere('calculadoras.estatus','pagado')
                            ->OrWhere('calculadoras.estatus','castigado');
                        });
                    }

                     $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('calculadoras.estatus','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                });
                if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaDesembolso','>=', $request->since);
                    $query->where('calculadoras.fechaDesembolso','<=', $request->until);
                }

            }else{
            //    $query->where('calculadoras.estatus','abierto');
                if($request->estatus){
                    $query->where('calculadoras.estatus',$request->estatus);
                }else{
                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.estatus','pagado')
                        ->OrWhere('calculadoras.estatus','castigado');
                    });
                }
               if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaDesembolso','>=', $request->since);
                    $query->where('calculadoras.fechaDesembolso','<=', $request->until);
                }
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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


        function getAllCreditosAbiertosPorUsuario(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')

            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');


            if(!empty($request->search)){
                if(strtolower($request->search) =='por firmar'){
                     $request->search='pendiente';

                    }else if(strtolower($request->search) =='por desembolsar'){
                        $request->search='firmado';
                    }else{
                        $request->search=$request->search;
                    }
                    $query->where('calculadoras.idUserFk',$request->idUser);
                    // $query->where('calculadoras.estatus','abierto');
                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.estatus','novado')
                        ->OrWhere('calculadoras.estatus','pendiente de novacion')
                        ->OrWhere('calculadoras.estatus','moroso')
                        ->OrWhere('calculadoras.estatus','restructurado')
                        ->OrWhere('calculadoras.estatus','abierto');
                    });

                     $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                });

            }else{
                $query->where('calculadoras.idUserFk',$request->idUser);
            //    $query->where('calculadoras.estatus','abierto');
                $query->where(function($query2) use ($request) {
                    $query2->orWhere('calculadoras.estatus','novado')
                    ->OrWhere('calculadoras.estatus','pendiente de novacion')
                    ->OrWhere('calculadoras.estatus','moroso')
                    ->OrWhere('calculadoras.estatus','restructurado')
                    ->OrWhere('calculadoras.estatus','abierto');
                });
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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
        function getAllCreditosPagadosPorUsuario(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')

            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.created_at as fecha_solicitud','users.*','calculadoras.*');


            if(!empty($request->search)){
                    $query->where('calculadoras.idUserFk',$request->idUser);
                    // $query->where('calculadoras.estatus','abierto');
                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('calculadoras.estatus','pagado')
                        ->OrWhere('calculadoras.estatus','castigado');
                    });

                    $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('calculadoras.estatus','like','%'.$request->search.'%');
                    });

                    if($request->until && $request->since){
                        $d =  $request->since.' 00:00:00';
                        $h =  $request->until.' 23:00:00';
                        // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                        $query->where('calculadoras.fechaUltimoPago','>=', $request->since);
                        $query->where('calculadoras.fechaUltimoPago','<=', $request->until);
                    }

            }else{
                $query->where('calculadoras.idUserFk',$request->idUser);
            //    $query->where('calculadoras.estatus','abierto');
                $query->where(function($query2) use ($request) {
                    $query2->orWhere('calculadoras.estatus','pagado')
                    ->OrWhere('calculadoras.estatus','castigado');
                });
                if($request->until && $request->since){
                    $d =  $request->since.' 00:00:00';
                    $h =  $request->until.' 23:00:00';
                    // $query->whereBetween('calculadoras.created_ats', [$request->since,$request->until]);
                    $query->where('calculadoras.fechaUltimoPago','>=', $request->since);
                    $query->where('calculadoras.fechaUltimoPago','<=', $request->until);
                }
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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

        function getAllCreditosAbiertosUsuario(Request $request){
            try{
                $request->validate([
                    'per_page'      =>  'nullable|integer',
                    'page'          =>  'nullable|integer'
                ]);
                $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::count();

                $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
                ->leftJoin('evaluacions', 'calculadoras.id', '=', 'evaluacions.idSolicitudFk')

            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito', 'evaluacions.estatus as estatus_evaluacion','calculadoras.created_at as fecha_solicitud','users.*','calculadoras.*');


            if(!empty($request->search)){
                if(strtolower($request->search) =='por firmar'){
                     $request->search='pendiente';

                    }else if(strtolower($request->search) =='por desembolsar'){
                        $request->search='firmado';
                    }else{
                        $request->search=$request->search;
                    }

                     $query->where('calculadoras.idUserFk',$request->idUser);

                     $query->Where('calculadoras.estatus','!=','pagado');
                     $query->Where('calculadoras.estatus','!=','castigado');
                     $query->Where('calculadoras.estatus','!=','novado');
                    $query->Where('calculadoras.estatus','!=','moroso');
                    $query->Where('calculadoras.estatus','!=','restructurado');
                    $query->Where('calculadoras.estatus','!=','pendiente de novacion');

                    $query->Where('evaluacions.estatus','!=','negado verificacion selfie');
                    $query->Where('evaluacions.estatus','!=','negado verificacion identidad');
                    $query->Where('evaluacions.estatus','!=','negado archivos adicionales');
                    $query->Where('evaluacions.estatus','!=','negado en la llamada');
                    $query->Where('evaluacions.estatus','!=','negado en matriz de calculo');
                    $query->Where('evaluacions.estatus','!=','negado en extractos bancarios');
                    $query->Where('evaluacions.estatus','!=','negado en data credito');
                    // $query->where(function($query2) use ($request) {
                    //     $query2->orWhere('calculadoras.estatus','!=','abierto')
                    //     ->OrWhere('calculadoras.estatus','!=','pagado')
                    //     ->OrWhere('calculadoras.estatus','!=','castigado');
                    // });

                     $query->where(function($query2) use ($request) {
                        $query2->orWhere('numero_credito', 'like', '%'.$request->search.'%')
                        ->orWhere('users.first_name','like','%'.$request->search.'%')
                        ->orWhere('estatus_firma','like','%'.$request->search.'%')
                        ->orWhere('users.last_name','like','%'.$request->search.'%');
                });

            }else{
                $query->where('calculadoras.idUserFk',$request->idUser);
                // $query->Where('calculadoras.estatus','!=','aprobado');
                $query->Where('calculadoras.estatus','!=','abierto');
                $query->Where('calculadoras.estatus','!=','pagado');
                $query->Where('calculadoras.estatus','!=','castigado');
                $query->Where('calculadoras.estatus','!=','novado');
                $query->Where('calculadoras.estatus','!=','moroso');
                $query->Where('calculadoras.estatus','!=','restructurado');
                $query->Where('calculadoras.estatus','!=','pendiente de novacion');

                $query->Where('evaluacions.estatus','!=','negado verificacion selfie');
                $query->Where('evaluacions.estatus','!=','negado verificacion identidad');
                $query->Where('evaluacions.estatus','!=','negado archivos adicionales');
                $query->Where('evaluacions.estatus','!=','negado en la llamada');
                $query->Where('evaluacions.estatus','!=','negado en matriz de calculo');
                $query->Where('evaluacions.estatus','!=','negado en extractos bancarios');
                $query->Where('evaluacions.estatus','!=','negado en data credito');
                // $query->where(function($query2) use ($request) {
                //     $query2->orWhere('calculadoras.estatus','!=','abierto')
                //     ->OrWhere('calculadoras.estatus','!=','pagado')
                //     ->OrWhere('calculadoras.estatus','!=','castigado');
                // });
            }


                 $result=$query->orderBy('calculadoras.id','desc')->paginate($per_page);

                $response = $result;

                if($result->isEmpty()){
                    return response()->json([
                        'data' => [],
                        'total' => 0,
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


   function desembolso(Request $request){
            try{


                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $solicitud=Calculadora::find($request->idSolicitudFk);
                $co= ContraOferta::where('idCalculadoraFk',$request->idSolicitudFk)->orderBy('id','desc')->first();
                if($solicitud->ofertaEnviada == 2){
                    $montoA = $solicitud->montoSolicitado;
                    $totalA = $solicitud->totalPagar;
                    $plataforma = $solicitud->plataforma;
                    $aprobacion = $solicitud->aprobacionRapida;
                    $iva = $solicitud->iva;
                    $tasaInteres=$solicitud->tasaInteres;
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                    $plataforma = $co->plataforma;
                    $aprobacion = $co->aprobacionRapida;
                    $iva = $co->iva;
                    $tasaInteres=$co->tasaInteres;
                }
                $user = desembolso::create([
                    'nombres'    => $request->nombres,
                    'ncedula'     => $request->ncedula,
                    'email'  => $request->email,
                    'nombreBanco'  => $request->nombreBanco,
                    'tipoCuenta'  => $request->tipoCuenta,
                    'ncuenta'  => $request->ncuenta,
                    'monto'  => $montoA,
                    'metodo'  => $request->metodo,
                    'idUserFk'  => $request->idUserFk,
                    'registrador'  => $request->registrador,
                    'comentario'  => $request->comentario,
                    'idRegistradorFk'  => $request->idRegistradorFk,
                    'idSolicitudFk'  => $request->idSolicitudFk
                ]);
                $fechaActual = date('Y-m-d');
                $actualizarEstatus=Calculadora::find($request->idSolicitudFk);
                $actualizarEstatus->estatus='abierto';
                $actualizarEstatus->fechaDesembolso = date('Y-m-d');
                $actualizarEstatus->save();
                $solicitud=Calculadora::find($request->idSolicitudFk);
                if($solicitud->tipoCredito == 'm'){
                    $configCal = ConfigCalculadora::where('tipo','=',1)->first();
                    $tasa = $configCal->tasa;
                    $cuotas=$solicitud->plazo;
                    $monto=$montoA;
                    $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );
                    $valor_de_cuota =$t_interes;
                    $saldo_al_capital =$solicitud->montoSolicitado;
                    $interesos;
                    $abono_al_capital;
                    $items = array();
                    $sum=0;
                    $resta=1000;

                    for ($i=0; $i <$solicitud->plazo; $i++) {
                        $interesos = $saldo_al_capital * $tasa;
                        $abono_al_capital = $valor_de_cuota - $interesos;
                        $saldo_al_capital -= $abono_al_capital;
                        $numero = $i + 1;

                        $interesos = $interesos;
                        $abono_al_capital = $abono_al_capital;
                        $saldo_al_capital = $saldo_al_capital;
                        $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$numero." month"));

                        $pago = Pagos::create([
                            'idSolicitudFk' => $request->idSolicitudFk,
                            'idUsuarioFk' =>$request->idUserFk,
                            'fechaPago' => $fecha,
                            'montoPagar'=>round($totalA/$solicitud->plazo),
                            'capital' => round($abono_al_capital),
                            'intereses'=>round($interesos),
                            'plataforma'=>round($plataforma/$solicitud->plazo),
                            'aprobacionRapida'=>round($aprobacion/$solicitud->plazo),
                            'iva'=>round($iva/$solicitud->plazo),
                            'saldoInicial'=>round($saldo_al_capital)

                        ]);
                    }
                }else{
                    $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$solicitud->plazo." days"));
                    $pago = Pagos::create([
                        'idSolicitudFk' => $request->idSolicitudFk,
                        'idUsuarioFk' =>$solicitud->idUserFk,
                        'fechaPago' => $fecha,
                        'montoPagar'=>$totalA,
                        'capital' => $montoA,
                        'intereses'=>round($tasaInteres),
                        'plataforma'=>round($plataforma),
                        'aprobacionRapida'=>round($aprobacion),
                        'iva'=>round($iva),
                        'saldoInicial'=>round($montoA)
                    ]);
                }


                $usuarios=User::where('id',$request->idUserFk)->first();
                $contenido=Correos::where('pertenece','desembolso')->first();

    if($contenido->estatus=='activo'){
                // echo($contenido);
                if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                    $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                        if($credito->tipoCredito=="m"){
                            $tipo="Panda meses";
                        }else{
                            $tipo="Panda dias";
                        }
                        $monto=$totalA;
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

          $monto232=number_format($montoA);

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
                    $monto232,
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
                    $msj->subject($name.',el dinero ha sido desembolsado a tu cuenta');
                    $msj->to($email);
                    $msj->to('respaldos@creditospanda.com');
                 });
    }



                // Mail::send('Mail.contacto',$data, function($msj) use ($email,$name){
                //     $msj->subject($name.', hemos recibido tu mensaje');
                //     $msj->to($email);
                //  });

                DB::commit(); // Guardamos la transaccion


                return response()->json('Mensaje enviado correctamente!',201);
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

    function getDetalleCA(Request $request){
        try{
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
            // $query->where('calculadoras.estatus','abierto')
            $query->where(function($query2) use ($request) {
                $query2->orWhere('calculadoras.estatus','novado')
                ->OrWhere('calculadoras.estatus','pendiente de novacion')
                ->OrWhere('calculadoras.estatus','moroso')
                ->OrWhere('calculadoras.estatus','restructurado')
                ->OrWhere('calculadoras.estatus','abierto');
            })
            ->where('calculadoras.id',$request->id);
            $solicitud = $query->first();
            $pagos = Pagos::
            select('pagos.id as id_pago','pagos.*')
            ->where('pagos.idSolicitudFk', $request->id)
            ->orderBy("pagos.id","asc")
            ->get();
            $pagos_re = Repagos::where('idSolicitudFk', $request->id)->get();
            $pago_proximo = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pendiente')
            ->first();
            $pagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago','=','pagado')
            ->where(function($query2) use ($request) {
                $query2->orWhere('concepto','!=','Novacion')
                ->orWhereNull('concepto');
            })

            ->get()
            ->sum("montoPagar");
            $pagado_todo = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->where(function($query2) use ($request) {
                $query2->orWhere('concepto','!=','Novacion')
                ->orWhereNull('concepto');
            })
            // ->where('concepto','!=','Novacion')
            ->get()
            ->sum("montoPagado");
            $pagado_novacion = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('concepto','Novacion')
            ->get()
            ->sum("montoPagar");
            $cuotas_pagadas = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pagado')
            ->count();
            $suma_cuota = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("montoPagar");
            $suma_capital = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("capital");
            $suma_intereses = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("intereses");
            $suma_interesMora = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("interesMora");
            $suma_plataforma = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("plataforma");
            $suma_aprobacionRapida = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("aprobacionRapida");
            $suma_gastosCobranza = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("gastosCobranza");
            $suma_gastosCobranzaSinIva = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("gastosCobranzaSinIva");
            $suma_ivaGastosCobranza = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("ivaGastosCobranza");
            $suma_iva = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->where('estatusPago', 'pagado')
            ->sum("iva");
            $suma_montoPagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("montoPagado");
            $pago_ultimo = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pagado')
            ->orderBy('id','desc')
            ->first();
            $suma_interesMoraFull = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("interesMora");
            $suma_gastosCobranzaFull = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("gastosCobranza");
            $contra_oferta = ContraOferta::where('idCalculadoraFk', $request->id)->orderBy('id','desc')->first();
            return response()->json([
                'solicitud'=> $solicitud,
                'pagos' => $pagos,
                'contra_oferta'=> $contra_oferta,
                'pago_proximo' => $pago_proximo,
                'repagos' => $pagos_re,
                'pagado'=> $pagado,
                'pagado_todo'=> $pagado_todo,
                'coutas_pagadas'=>$cuotas_pagadas,
                'pagado_novacion'=>$pagado_novacion,
                'suma_cuota'=>$suma_cuota,
                'suma_capital'=>$suma_capital,
                'suma_intereses'=>$suma_intereses,
                'suma_interesMora'=>$suma_interesMora,
                'suma_plataforma'=>$suma_plataforma,
                'suma_aprobacionRapida'=>$suma_aprobacionRapida,
                'suma_gastosCobranza'=>$suma_gastosCobranza,
                'suma_iva'=>$suma_iva,
                'suma_montoPagado'=>$suma_montoPagado,
                'pago_ultimo'=>$pago_ultimo,
                'suma_interesMoraFull'=>$suma_interesMoraFull,
                'suma_gastosCobranzaFull'=>$suma_gastosCobranzaFull,
                'suma_gastosCobranzaSinIva'=>$suma_gastosCobranzaSinIva,
                'suma_ivaGastosCobranza'=>$suma_ivaGastosCobranza,
                'message'=>'Consulta correcta'

            ]);

        }catch(\Exception $e){
            $this->responseCode = 400;
        }
    }
    function getDetalleCC(Request $request){
        try{
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
            // $query->where('calculadoras.estatus','abierto')
            $query->where(function($query2) use ($request) {
                $query2->orWhere('calculadoras.estatus','pagado')
                ->OrWhere('calculadoras.estatus','castigado');
            })
            ->where('calculadoras.id',$request->id);
            $solicitud = $query->first();
            $pagos = Pagos::
            select('pagos.id as id_pago','pagos.*')
            ->where('pagos.idSolicitudFk', $request->id)
            ->orderBy("pagos.id","asc")
            ->get();
            $pagos_re = Repagos::where('idSolicitudFk', $request->id)->get();
            $pagos_parciales = PagosParciales::where('idSolicitudFk', $request->id)->first();
            $pago_proximo = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pendiente')
            ->orderBy('id','desc')
            ->first();
            $pagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago','=','pagado')
            ->where(function($query2) use ($request) {
                $query2->orWhere('concepto','!=','Novacion')
                ->orWhereNull('concepto');
            })

            ->get()
            ->sum("montoPagar");
            $pagado_todo = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->where(function($query2) use ($request) {
                $query2->orWhere('concepto','!=','Novacion')
                ->orWhereNull('concepto');
            })
            // ->where('concepto','!=','Novacion')
            ->get()
            ->sum("montoPagado");
            $cuotas_pagadas = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pagado')
            ->count();
            $pagado_novacion = DB::table("repagos")->where('idSolicitudFk',$request->id)
            ->where('concepto','Novacion')
            ->get()
            ->sum("montoRepagar");
            $contra_oferta = ContraOferta::where('idCalculadoraFk', $request->id)->orderBy('id','desc')->first();
            $pagado_intereses = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("interesesMora");
            $pagado_gastos = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("gastosCobranza");
            $dias_mora = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->get()
            ->sum("diasMora");
            $suma_cuota = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("montoPagar");
            $suma_capital = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("capital");
            $suma_intereses = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("intereses");
            $suma_interesMora = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("interesMora");
            $suma_plataforma = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("plataforma");
            $suma_aprobacionRapida = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("aprobacionRapida");
            $suma_gastosCobranza = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("gastosCobranza");
            $suma_gastosCobranzaSinIva = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("gastosCobranzaSinIva");
            $suma_ivaGastosCobranza = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("ivaGastosCobranza");
            $suma_iva = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("iva");
            $suma_montoPagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("montoPagado");
            $pago_ultimo = Pagos::where('idSolicitudFk', $request->id)
            ->where('estatusPago', 'pagado')
            ->orderBy('id','desc')
            ->first();
            $suma_interesMoraFull = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("interesMora");
            $suma_gastosCobranzaFull = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("gastosCobranza");
            return response()->json([
                'solicitud'=> $solicitud,
                'pagos' => $pagos,
                'contra_oferta'=> $contra_oferta,
                'pago_proximo' => $pago_proximo,
                'repagos' => $pagos_re,
                'pagado'=> $pagado,
                'pagado_todo'=> $pagado_todo,
                'coutas_pagadas'=>$cuotas_pagadas,
                'pagado_novacion'=>$pagado_novacion,
                'pargos_parciales'=>$pagos_parciales,
                'pagado_intereses'=>$pagado_intereses,
                'pagado_gastos'=>$pagado_gastos,
                'dias_mora'=>$dias_mora,
                'suma_cuota'=>$suma_cuota,
                'suma_capital'=>$suma_capital,
                'suma_intereses'=>$suma_intereses,
                'suma_interesMora'=>$suma_interesMora,
                'suma_plataforma'=>$suma_plataforma,
                'suma_aprobacionRapida'=>$suma_aprobacionRapida,
                'suma_gastosCobranza'=>$suma_gastosCobranza,
                'suma_iva'=>$suma_iva,
                'suma_montoPagado'=>$suma_montoPagado,
                'pago_ultimo'=>$pago_ultimo,
                'suma_interesMoraFull'=>$suma_interesMoraFull,
                'suma_gastosCobranzaFull'=>$suma_gastosCobranzaFull,
                'suma_gastosCobranzaSinIva'=>$suma_gastosCobranzaSinIva,
                'suma_ivaGastosCobranza'=>$suma_ivaGastosCobranza,
                'message'=>'Consulta correcta'

            ]);

        }catch(\Exception $e){
            $this->responseCode = 400;
        }
    }
    function getDesembolso(Request $request){
        try{
            if(!desembolso::where('idSolicitudFk','=',$request->id)->exists()){
                return response()->json([
                    'desembolso'=>null,
                    'message' => 'no existe'
                ], 500);
            }
            $desembolso = desembolso::where('idSolicitudFk', $request->id)->orderBy('id','desc')->first();
            return response()->json([
                'desembolso'=> $desembolso,
                'message'=>'Consulta correcta'

            ]);

        }catch(\Exception $e){
            $this->responseCode = 400;
        }
    }

    function realizarPago(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $contenido=array();
            $pago = Pagos::find($request->idPago);
            $solicitud = Calculadora::find($request->idSolicitud);
            $co= ContraOferta::where('idCalculadoraFk',$pago->idSolicitudFk)->orderBy('id','desc')->first();
            $montoA = 0;
            $totalA = 0;
            if($solicitud->tipoCredito == "d"){
                if($solicitud->ofertaEnviada == 2){
                    $montoA = $solicitud->montoSolicitado;
                    $totalA = $solicitud->totalPagar;
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                }
            }

            $fecha_actual = date("Y-m-d");
            $fecha_pago = date("Y-m-d",strtotime($request->fechaPagado."+ ".$solicitud->plazo." days"));
            if($request->concepto == "Novacion"){
                $repago = Repagos::create([
                    'montoRepagar'    => $request->montoPagado,
                    'interesMora'    => $request->interesMora,
                    'gastosCobranza'    => $request->gastosCobranza,
                    'totalPagar'    => $request->montoTotalPagar,
                    'metodoRepago'     => $request->medioPago,
                    'fecha'  => $request->fechaPagado,
                    'nroReferencia'  => $request->nroReferencia,
                    'concepto'  => $request->concepto,
                    'idUsuarioFk'  => $solicitud->idUserFk,
                    'idSolicitudFk'  => $request->idSolicitud,
                    'idPagoFk'  => $request->idPago,
                    'diasMora' => $request->diasMora ? $request->diasMora : 0
                ]);
                $solicitud->estatus = "novado";
                $solicitud->fechaNovado = $fecha_actual;
                $solicitud->diasMora=0;
                $solicitud->save();
                $pago->fechaPago = date("Y-m-d",strtotime($request->fechaPagado."+ ".$solicitud->plazo." days"));
                $pago->montoNovado = $request->montoPagado;
                $pago->medioNovado = $request->medioPago;
                $pago->fechaPagadoNovado = $request->fechaPagado;
                $pago->concepto = $request->concepto;
                $pago->nroReferenciaNovado = $request->nroReferencia;
                $pago->save();
                $asunto = 'tu préstamo ha sido novado exitosamente';
                $contenido=Correos::where('pertenece','pago realizado concepto novacion')->first();

            }else if($request->concepto == "Pago total credito" || $request->concepto == "Pago ultima cuota" || $request->concepto == "Pago total saldo al dia moroso"){
                $repago = Repagos::create([
                    'montoRepagar'    => $request->montoPagado,
                    'interesMora'    => $request->interesMora,
                    'gastosCobranza'    => $request->gastosCobranza,
                    'totalPagar'    => $request->montoTotalPagar,
                    'metodoRepago'     => $request->medioPago,
                    'fecha'  => $request->fechaPagado,
                    'nroReferencia'  => $request->nroReferencia,
                    'concepto'  => $request->concepto,
                    'idUsuarioFk'  => $solicitud->idUserFk,
                    'idSolicitudFk'  => $request->idSolicitud,
                    'idPagoFk'  => $request->idPago,
                    'diasMora' => $request->diasMora ? $request->diasMora : 0
                ]);
                $pago->montoPagado = $request->montoPagado;
                $pago->estatusPago = 'pagado';
                $pago->medioPago = $request->medioPago;
                $pago->fechaPagado = $request->fechaPagado;
                $pago->nroReferencia = $request->nroReferencia;
                $pago->concepto = $request->concepto;
                $pago->save();
                // if($request->factura!=''){
                //     $nombre=$solicitud->numero_credito.'_factura_'.time().'.pdf';
                //     $ruta_factura = storage_path('app/public/contratos/').$nombre;
                //     Storage::disk('local')->put('\\public\\contratos\\'.$nombre, base64_decode($request->factura));
                //     $solicitud->factura = $ruta_factura;
                // }
                $solicitud->estatus = "pagado";
                $solicitud->fechaUltimoPago = $request->fechaPagado;
                $solicitud->diasMora=0;
                $solicitud->save();
                $asunto = 'tu préstamo ha sido pagado en su totalidad';
                $contenido=Correos::where('pertenece','pago realizado concepto total')->first();
            }else if($request->concepto == "Pago cuota mensual morosa"){
                if($request->pagosAct){
                    $pagosAct = json_decode($request->pagosAct);
                    foreach ($pagosAct as $key => $value) {
                        $repago = Repagos::create([
                            'montoRepagar'    => $value->montoPagado,
                            'interesMora'    => $value->interesMora,
                            'gastosCobranza'    => $value->gastosCobranza,
                            'totalPagar'    => $request->montoTotalPagar,
                            'metodoRepago'     => $request->medioPago,
                            'fecha'  => $request->fechaPagado,
                            'nroReferencia'  => $request->nroReferencia,
                            'concepto'  => $request->concepto,
                            'idUsuarioFk'  => $solicitud->idUserFk,
                            'idSolicitudFk'  => $request->idSolicitud,
                            'idPagoFk'  => $value->id_pago,
                            'diasMora' => $value->diasMora ? $value->diasMora : 0
                        ]);
                        $pag = Pagos::find($value->id_pago);
                        $pag->montoPagado = $value->montoPagado;
                        $pag->estatusPago = 'pagado';
                        $pag->medioPago = $request->medioPago;
                        $pag->fechaPagado = $request->fechaPagado;
                        $pag->nroReferencia = $request->nroReferencia;
                        $pag->concepto = $request->concepto;
                        $pag->save();
                    }
                    $solicitud->estatus = "abierto";
                    $solicitud->diasMora=0;
                    $solicitud->save();
                    $asunto = 'tu préstamo ha sido pagado';
                    $contenido=Correos::where('pertenece','pago realizado concepto cuota')->first();
                }
            }else if($request->concepto == "Pago incompleto"){
                if($request->pagosAct){
                    $pagosAct = json_decode($request->pagosAct);
                    foreach ($pagosAct as $key => $value) {
                        if($value->estatusPago == "pagado"){
                        $repago = Repagos::create([
                            'montoRepagar'    => $value->montoPagado,
                            'interesMora'    => $value->interesMora,
                            'gastosCobranza'    => $value->gastosCobranza,
                            'totalPagar'    => $request->montoTotalPagar,
                            'metodoRepago'     => $request->medioPago,
                            'fecha'  => $request->fechaPagado,
                            'nroReferencia'  => $request->nroReferencia,
                            'concepto'  => $request->concepto,
                            'idUsuarioFk'  => $solicitud->idUserFk,
                            'idSolicitudFk'  => $request->idSolicitud,
                            'idPagoFk'  => $value->id_pago,
                            'diasMora' => $value->diasMora ? $value->diasMora : 0
                        ]);
                        }
                        $pag = Pagos::find($value->id_pago);
                        if($value->actCutota){
                            $pag->montoPagar =$value->montoPagar-($value->gastosCobranzaSinIva+$value->ivaGastosCobranza)-$value->interesMora;
                            $pag->capital=$value->capital;
                            $pag->intereses=$value->intereses;
                            $pag->plataforma=$value->plataforma;
                            $pag->aprobacionRapida=$value->aprobacionRapida;
                            $pag->iva=$value->iva;
                            if($value->estatusPago == "pagado"){
                                $pag->interesMora=$value->interesMora;
                                $pag->gastosCobranzaSinIva=$value->gastosCobranzaSinIva;
                                $pag->ivaGastosCobranza=$value->ivaGastosCobranza;
                                $pag->gastosCobranza=$value->gastosCobranzaSinIva+$value->ivaGastosCobranza;
                            }else{
                                $pag->interesMoraPendiente=$value->interesMora;
                                $pag->gastosCobranzaPendiente=$value->gastosCobranzaSinIva+$value->ivaGastosCobranza;
                                $pag->gastosCobranzaSinIvaPendiente=$value->gastosCobranzaSinIva;
                                $pag->ivaGastosCobranzaPendiente=$value->ivaGastosCobranza;
                            }

                        }

                            $pag->montoPagado = $value->montoPagado;
                        if($value->estatusPago == "pagado"){
                            $pag->estatusPago = $value->estatusPago;
                            $pag->medioPago = $request->medioPago;
                            $pag->fechaPagado = $request->fechaPagado;
                            $pag->nroReferencia = $request->nroReferencia;
                            $pag->concepto = $request->concepto;
                        }
                        $pag->save();
                    }
                    if($solicitud->tipoCredito == "d"){
                        $fec_mas=date("Y-m-d",strtotime($request->fechaPagado."+ 30 days"));
                        $pagoNew = json_decode($request->pagoNew);
                        $pago = Pagos::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUsuarioFk' =>$solicitud->idUserFk,
                            'fechaPago' => $fec_mas,
                            'montoPagar'=>$pagoNew->montoPagar-($pagoNew->gastosCobranzaSinIva+$pagoNew->ivaGastosCobranza)-$pagoNew->interesMora,
                            'capital' => $pagoNew->capital,
                            'intereses'=>$pagoNew->intereses,
                            'plataforma'=>$pagoNew->plataforma,
                            'aprobacionRapida'=>$pagoNew->aprobacionRapida,
                            'iva'=>$pagoNew->iva,
                            'interesMora'=>$pagoNew->interesMora,
                            'gastosCobranzaSinIva'=>$pagoNew->gastosCobranzaSinIva,
                            'ivaGastosCobranza'=>$pagoNew->ivaGastosCobranza,
                            'gastosCobranza'=> $pagoNew->gastosCobranzaSinIva+$pagoNew->ivaGastosCobranza,
                            'interesMoraPendiente'=>$pagoNew->interesMora,
                            'gastosCobranzaPendiente'=>$pagoNew->gastosCobranzaSinIva+$pagoNew->ivaGastosCobranza,
                            'gastosCobranzaSinIvaPendiente'=>$pagoNew->gastosCobranzaSinIva,
                            'ivaGastosCobranzaPendiente'=>$pagoNew->ivaGastosCobranza,
                        ]);
                    }
                    if($request->concepto == "Pago anticipado"){
                        $solicitud->estatus = "pagado";
                        $solicitud->fechaUltimoPago = $request->fechaPagado;
                        $solicitud->diasMora=0;
                        $solicitud->save();
                        $asunto = 'tu préstamo ha sido pagado en su totalidad';
                        $contenido=Correos::where('pertenece','pago realizado concepto total')->first();
                    }else{
                        $asunto = 'tu préstamo ha sido pagado';
                        $contenido=Correos::where('pertenece','pago realizado concepto cuota')->first();
                    }

                }

            }else if($request->concepto == "Pago extra"){
                if($request->pagosAct){
                    $pagosAct = json_decode($request->pagosAct);
                    foreach ($pagosAct as $key => $value) {
                        $repago = Repagos::create([
                            'montoRepagar'    => $value->montoPagado,
                            'interesMora'    => $value->interesMora,
                            'gastosCobranza'    => $value->gastosCobranza,
                            'totalPagar'    => $request->montoTotalPagar,
                            'metodoRepago'     => $request->medioPago,
                            'fecha'  => $request->fechaPagado,
                            'nroReferencia'  => $request->nroReferencia,
                            'concepto'  => $request->concepto,
                            'idUsuarioFk'  => $solicitud->idUserFk,
                            'idSolicitudFk'  => $request->idSolicitud,
                            'idPagoFk'  => $value->id_pago,
                            'diasMora' => $value->diasMora ? $value->diasMora : 0
                        ]);
                        $pag = Pagos::find($value->id_pago);
                        if($value->actCutota){
                        $pag->montoPagar =$value->montoPagar-($value->gastosCobranzaSinIva+$value->ivaGastosCobranza)-$value->interesMora;
                        $pag->capital=$value->capital;
                        $pag->intereses=$value->intereses;
                        $pag->interesMora=$value->interesMora;
                        $pag->plataforma=$value->plataforma;
                        $pag->aprobacionRapida=$value->aprobacionRapida;
                        $pag->gastosCobranzaSinIva=$value->gastosCobranzaSinIva;
                        $pag->ivaGastosCobranza=$value->ivaGastosCobranza;
                        $pag->gastosCobranza=$value->gastosCobranzaSinIva+$value->ivaGastosCobranza;
                        $pag->iva=$value->iva;
                        $pag->interesMoraPagado=$value->interesMoraPagado;
                        $pag->gastosCobranzaPagado=$value->gastosCobranzaSinIvaPagado+$value->ivaGastosCobranzaPagado;
                        $pag->gastosCobranzaSinIvaPagado=$value->gastosCobranzaSinIvaPagado;
                        $pag->ivaGastosCobranzaPagado=$value->ivaGastosCobranzaPagado;
                        }
                        $pag->montoPagado = $value->montoPagado;
                        if($value->estatusPago == "pagado"){
                        $pag->estatusPago = $value->estatusPago;
                        $pag->medioPago = $request->medioPago;
                        $pag->fechaPagado = $request->fechaPagado;
                        $pag->nroReferencia = $request->nroReferencia;
                        $pag->concepto = $request->concepto;
                        }
                        $pag->save();
                    }
                    $solicitud->estatus = "abierto";
                    $solicitud->diasMora=0;
                    $solicitud->save();
                    $asunto = 'tu préstamo ha sido pagado';
                    $contenido=Correos::where('pertenece','pago realizado concepto cuota')->first();
                }

            }else if($request->concepto == "Pago anticipado"){
                if($solicitud->tipoCredito == "d"){
                    $repago = Repagos::create([
                        'montoRepagar'    => $request->montoPagado,
                        'interesMora'    => $request->interesMora,
                        'gastosCobranza'    => $request->gastosCobranza,
                        'totalPagar'    => $request->montoTotalPagar,
                        'metodoRepago'     => $request->medioPago,
                        'fecha'  => $request->fechaPagado,
                        'nroReferencia'  => $request->nroReferencia,
                        'concepto'  => $request->concepto,
                        'idUsuarioFk'  => $solicitud->idUserFk,
                        'idSolicitudFk'  => $request->idSolicitud,
                        'idPagoFk'  => $request->idPago,
                        'diasMora' => $request->diasMora ? $request->diasMora : 0
                    ]);
                    $pagoAnticipadoDias = json_decode($request->pagoAnticipadoDias);
                        $pagoNew = Pagos::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUsuarioFk' =>$solicitud->idUserFk,
                            'fechaPago' => $pago->fechaPago,
                            'montoPagar'=> ($pago->montoPagar-$pagoAnticipadoDias->montoPagar),
                            'capital' => ($pago->capital-$pagoAnticipadoDias->capital),
                            'intereses'=> ($pago->intereses-$pagoAnticipadoDias->intereses),
                            'plataforma'=> ($pago->plataforma-$pagoAnticipadoDias->plataforma),
                            'aprobacionRapida'=> ($pago->aprobacionRapida-$pagoAnticipadoDias->aprobacionRapida),
                            'iva'=> ($pago->iva-$pagoAnticipadoDias->iva),
                            'interesMora'=>($pago->interesMora-$pagoAnticipadoDias->interesMora),
                            'gastosCobranzaSinIva'=>($pago->gastosCobranzaSinIva-$pagoAnticipadoDias->gastosCobranzaSinIva),
                            'ivaGastosCobranza'=>($pago->ivaGastosCobranza-$pagoAnticipadoDias->ivaGastosCobranza),
                            'gastosCobranza'=> ($pago->gastosCobranzaSinIva+$pago->ivaGastosCobranza)-($pagoAnticipadoDias->gastosCobranzaSinIva+$pagoAnticipadoDias->ivaGastosCobranza),

                        ]);
                        $pago->montoPagar=$pagoAnticipadoDias->montoPagar;
                        $pago->capital=$pagoAnticipadoDias->capital;
                        $pago->intereses=$pagoAnticipadoDias->intereses;
                        $pago->plataforma=$pagoAnticipadoDias->plataforma;
                        $pago->aprobacionRapida=$pagoAnticipadoDias->aprobacionRapida;
                        $pago->iva=$pagoAnticipadoDias->iva;
                        $pago->interesMora=$pagoAnticipadoDias->interesMora;
                        $pago->gastosCobranzaSinIva=$pagoAnticipadoDias->gastosCobranzaSinIva;
                        $pago->ivaGastosCobranza=$pagoAnticipadoDias->ivaGastosCobranza;
                        $pago->gastosCobranza=($pagoAnticipadoDias->gastosCobranzaSinIva+$pagoAnticipadoDias->ivaGastosCobranza);
                        $pago->montoRestante=$pagoAnticipadoDias->diferenciaPago;
                        $pago->montoPagado=$pagoAnticipadoDias->montoPagado;
                        $pago->estatusPago = 'pagado';
                        $pago->medioPago = $request->medioPago;
                        $pago->fechaPagado = $request->fechaPagado;
                        $pago->nroReferencia = $request->nroReferencia;
                        $pago->concepto = $request->concepto;
                        $pago->save();
                }else{
                    $repago = Repagos::create([
                        'montoRepagar'    => $request->montoPagado,
                        'interesMora'    => $request->interesMora,
                        'gastosCobranza'    => $request->gastosCobranza,
                        'totalPagar'    => $request->montoTotalPagar,
                        'metodoRepago'     => $request->medioPago,
                        'fecha'  => $request->fechaPagado,
                        'nroReferencia'  => $request->nroReferencia,
                        'concepto'  => $request->concepto,
                        'idUsuarioFk'  => $solicitud->idUserFk,
                        'idSolicitudFk'  => $request->idSolicitud,
                        'idPagoFk'  => $request->idPago,
                        'diasMora' => $request->diasMora ? $request->diasMora : 0
                    ]);
                    $pagoAnticipadoMeses = json_decode($request->pagoAnticipadoMeses);
                    $pago->montoPagar=$pagoAnticipadoMeses->montoPagar;
                    $pago->capital=$pagoAnticipadoMeses->capital;
                    $pago->intereses=$pagoAnticipadoMeses->intereses;
                    $pago->plataforma=$pagoAnticipadoMeses->plataforma;
                    $pago->aprobacionRapida=$pagoAnticipadoMeses->aprobacionRapida;
                    $pago->iva=$pagoAnticipadoMeses->iva;
                    $pago->interesMora=$pagoAnticipadoMeses->interesMora;
                    $pago->gastosCobranzaSinIva=$pagoAnticipadoMeses->gastosCobranzaSinIva;
                    $pago->ivaGastosCobranza=$pagoAnticipadoMeses->ivaGastosCobranza;
                    $pago->gastosCobranza=($pagoAnticipadoMeses->gastosCobranzaSinIva+$pagoAnticipadoMeses->ivaGastosCobranza);
                    $pago->montoRestante=$pagoAnticipadoMeses->diferenciaPago;
                    $pago->montoPagado=$pagoAnticipadoMeses->montoPagado;
                    $pago->estatusPago = 'pagado';
                    $pago->medioPago = $request->medioPago;
                    $pago->fechaPagado = $request->fechaPagado;
                    $pago->nroReferencia = $request->nroReferencia;
                    $pago->concepto = $request->concepto;
                    $pago->save();

                    $elimPagos= Pagos::where('idSolicitudFk',$request->idSolicitud)->where('estatusPago','pendiente')->get();
                    if(count($elimPagos)>0){
                        foreach ($elimPagos as $key => $value) {
                            $pagoElim = Pagos::find($value->id);
                            $pagoElim->delete();
                        }
                    }
                }
                $solicitud->estatus = "pagado";
                $solicitud->fechaUltimoPago = $request->fechaPagado;
                $solicitud->diasMora=0;
                $solicitud->save();
                $asunto = 'tu préstamo ha sido pagado en su totalidad';
                $contenido=Correos::where('pertenece','pago realizado concepto total')->first();
            }else{
                $repago = Repagos::create([
                    'montoRepagar'    => $request->montoPagado,
                    'interesMora'    => $request->interesMora,
                    'gastosCobranza'    => $request->gastosCobranza,
                    'totalPagar'    => $request->montoTotalPagar,
                    'metodoRepago'     => $request->medioPago,
                    'fecha'  => $request->fechaPagado,
                    'nroReferencia'  => $request->nroReferencia,
                    'concepto'  => $request->concepto,
                    'idUsuarioFk'  => $solicitud->idUserFk,
                    'idSolicitudFk'  => $request->idSolicitud,
                    'idPagoFk'  => $request->idPago,
                    'diasMora' => $request->diasMora ? $request->diasMora : 0
                ]);
                $pago->montoPagado = $request->montoPagado;
                $pago->estatusPago = 'pagado';
                $pago->medioPago = $request->medioPago;
                $pago->fechaPagado = $request->fechaPagado;
                $pago->nroReferencia = $request->nroReferencia;
                $pago->concepto = $request->concepto;
                $pago->save();
                $solicitud->estatus = "abierto";
                $solicitud->diasMora=0;
                $solicitud->save();
                $asunto = 'tu préstamo ha sido pagado';
                $contenido=Correos::where('pertenece','pago realizado concepto cuota')->first();
            }

            $usuarios=User::where('id',$solicitud->idUserFk)->first();


    if($contenido && $contenido->estatus=='activo'){
                // echo($contenido);
                if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                    $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();



                        if($credito->tipoCredito=="m"){
                            $tipo="Panda meses";
                        }else{
                            $tipo="Panda dias";
                        }
                        $monto=0;
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

          $monto232="$".number_format($totalA);

                $arregloBusqueda=array(
                "{{Nombre}}",
                "{{Apellido}}",
                "{{Email}}",
                "{{Cedula}}",
                "{{Ncredito}}",
                "{{MontoPagar}}",
                "{{Expedicion}}",
                "{{TipoCredito}}",
                "{{FechaPago}}"

                );
                $arregloCambiar=array(
                    $name,
                    $last_name,
                    $email,
                    $cedula,
                    $numerCredito,
                    $monto232,
                    $expedicion,
                    $tipo,
                    $fecha_pago

                );

                $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);




                $data = [
                    'Nombre' => $name,
                    'Email'=>$email,
                    'Apellido'=>$last_name,
                    'Cedula'=>$cedula,
                    'Contenido'=>$cntent2,

                    ];

                Mail::send('Mail.solicitud',$data, function($msj) use ($email,$name,$asunto){
                    $msj->subject($name.', '.$asunto);
                    $msj->to($email);
                 });
    }

            DB::commit(); // Guardamos la transaccion
            return response()->json($pago,201);
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
    function editarPagoIntereses(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $pago = Pagos::find($request->idPago);
            $solicitud = Calculadora::find($pago->idSolicitudFk);
            $co= ContraOferta::where('idCalculadoraFk',$pago->idSolicitudFk)->orderBy('id','desc')->first();
            if($solicitud->tipoCredito == "d"){
                if($solicitud->ofertaEnviada == 2){
                    $montoA = $solicitud->montoSolicitado;
                    $totalA = $solicitud->totalPagar;
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                }
                $pago->montoPagar = $totalA;
            }
            // if($solicitud->tipoCredito=="d"){
            //     $pago->interesMora = $request->interesMora;
            //     $pago->gastosCobranza = $request->gastosCobranza;
            //     $pago->gastosCobranzaSinIva = $request->gastosCobranzaSinIva;
            //     $pago->ivaGastosCobranza = $request->ivaGastosCobranza;
            //     $pago->save();
            // }

            if($request->diasMora){
                $solicitud->diasMora = $request->diasMora;
                $solicitud->save();
            }
            if($request->interesMora > 0 && $request->gastosCobranza > 0){
                if($solicitud->estatus != 'moroso' && $solicitud->estatus != 'pendiente de novacion' ){
                    if($solicitud->estatus != 'restructurado'){
                        $solicitud->estatusAnterior = $solicitud->estatus;
                    }
                    $solicitud->estatus = 'moroso';
                    $solicitud->save();
                }
            }else{
                if($solicitud->estatus == 'moroso'){
                   $solicitud->estatus = $solicitud->estatusAnterior;
                   $solicitud->save();
                }
            }

            if($request->pagos){
                foreach ($request->pagos as $key => $value) {

                    $pag = Pagos::find($value['id_pago']);
                    if($pag->estatusPago == "pendiente"){
                        if($pag->fechaPagado == null){
                           $pag->diasMora = $value['diasMora'];
                            $pag->interesMora = $value['interesMora'];
                            $pag->gastosCobranza = $value['gastosCobranza'];
                            $pag->gastosCobranzaSinIva = $value['gastosCobranzaSinIva'];
                            $pag->ivaGastosCobranza = $value['ivaGastosCobranza'];
                        }

                        $pag->save();
                    }

                }
            }
            DB::commit(); // Guardamos la transaccion
            return response()->json($solicitud,201);
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

    function modificarContratos(Request $request){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $solicitud = Calculadora::find($request->id);
            if($request->contrato!=''){

                $nombre=$solicitud->numero_credito.'_'.time().'.pdf';
                $ruta_contrato = storage_path('app/public/contratos/').$nombre;
                Storage::disk('local')->put('\\public\\contratos\\'.$nombre, base64_decode($request->contrato));

            }
            if($request->pagare!=''){
                $nombrePagare=$solicitud->numero_credito.'_pagare_'.time().'.pdf';
                $ruta_pagare = storage_path('app/public/contratos/').$nombrePagare;
                Storage::disk('local')->put('\\public\\contratos\\'.$nombrePagare, base64_decode($request->pagare));

            }
            if($request->carta!=''){
                $nombreCarta=$solicitud->numero_credito.'_carta_autorizacion_'.time().'.pdf';
                $ruta_carta = storage_path('app/public/contratos/').$nombreCarta;
                Storage::disk('local')->put('\\public\\contratos\\'.$nombreCarta, base64_decode($request->carta));

            }
            $solicitud->documentoCarta = $ruta_carta;
            $solicitud->documentoPagare = $ruta_pagare;
            $solicitud->documentoContrato = $ruta_contrato;


            $solicitud->save();
            DB::commit(); // Guardamos la transaccion

            return response()->json($solicitud,200);
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

    function modificarEstatusIntereses(Request $request){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $solicitud = Calculadora::find($request->id);
            $solicitud->estatusIntereses = $request->estatusIntereses;
            if($request->estatusIntereses == 0 ){
                // $solicitud->estatusAnterior = $solicitud->estatus;
                $solicitud->estatus = 'restructurado';
                $solicitud->diasMora = 0;
            }
            $solicitud->save();
            DB::commit(); // Guardamos la transaccion

            return response()->json($solicitud,200);
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

    function realizarPagoParcial(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $contenido=array();
            $fecha_actual = date("Y-m-d");
            // $pago = PagosParciales::create([
            //     'capital'    => $request->capital,
            //     'interesesMora'     => $request->interesesMora,
            //     'plataforma'  => $request->plataforma,
            //     'aprobacionRapida'  => $request->aprobacionRapida,
            //     'intereses'  => $request->intereses,
            //     'gastosCobranza'  => $request->gastosCobranza,
            //     'iva'  => $request->iva,
            //     'totalNoPago'  => $request->totalNoPago,
            //     'idUsuarioFk'  => $request->idUsuario,
            //     'idSolicitudFk'  => $request->idSolicitud,
            //     'concepto' => $request->concepto,
            //     'nroReferencia' => $request->nroReferencia,
            //     'fecha' =>$request->fecha,
            //     'medioPago'=>$request->medioPago

            // ]);
            $solicitud = Calculadora::find($request->idSolicitud);
            $solicitud->estatus = "castigado";
            // $solicitud->fechaUltimoPago = $fecha_actual;
            // $solicitud->diasMora=0;
            $solicitud->save();
            DB::commit(); // Guardamos la transaccion
            return response()->json($solicitud,200);
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

    function cargarFactura(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $solicitud = Calculadora::find($request->idSolicitud);
            if($request->factura!=''){
                $nombre=$solicitud->numero_credito.'_factura_'.time().'.pdf';
                $ruta_factura = storage_path('app/public/contratos/').$nombre;
                Storage::disk('local')->put('\\public\\contratos\\'.$nombre, base64_decode($request->factura));
                $solicitud->factura = $ruta_factura;
            }
            $solicitud->save();
            DB::commit(); // Guardamos la transaccion
            return response()->json($solicitud,200);
        }catch (\Exception $e) {

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
    public function exportExcelCA(Request $request)
      {
        $fecha_actual = date("Y-m-d");
        $query =Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
        ->leftJoin('basicas', 'users.id', '=', 'basicas.idUserFk')
        ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','users.*','basicas.*');
        $query->where(function($query2) use ($request) {
            $query2->orWhere('calculadoras.estatus','novado')
            ->OrWhere('calculadoras.estatus','pendiente de novacion')
            ->OrWhere('calculadoras.estatus','moroso')
            ->OrWhere('calculadoras.estatus','restructurado')
            ->OrWhere('calculadoras.estatus','abierto');
        });
        $solicitudes = $query->get();
        foreach ($solicitudes as $key => $solicitud) {
            $pago_proximo = Pagos::where('idSolicitudFk', $solicitud->id_solicitud)
            ->where('estatusPago', 'pendiente')
            ->first();
            $pagado = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("totalPagar");
            $pagado_todo = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where(function($query2) use ($request) {
                $query2->orWhere('concepto','!=','Novacion')
                ->orWhereNull('concepto');
            })
            // ->where('concepto','!=','Novacion')
            ->get()
            ->sum("montoPagado");
            $pagado_novacion = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','Novacion')
            ->get()
            ->sum("montoRepagar");
            $cuotas_pagadas = Pagos::where('idSolicitudFk', $solicitud->id_solicitud)
            ->where('estatusPago', 'pagado')
            ->count();
            $suma_interesMora = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("interesMora");
            $suma_gastosCobranza = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->where('estatusPago', 'pagado')
            ->get()
            ->sum("gastosCobranza");
            $suma_montoPagado = DB::table("pagos")->where('idSolicitudFk',$request->id)
            ->get()
            ->sum("montoPagado");
            $contra_oferta = ContraOferta::where('idCalculadoraFk', $solicitud->id_solicitud)->orderBy('id','desc')->first();
            $pagos =Pagos::where('idSolicitudFk', $solicitud->id_solicitud)->get();
            $diasMora=0;
            $interesMora =0;
            $gastosCobranza = 0;
            $interesMoratorio = pow(1+(22/100), (1/360))-1;
            $montoInvertido=0;
            $totalP =0;
            $suma_novacion=0;
            $cuota=0;
            $sumaMora = $suma_gastosCobranza+$suma_interesMora;
            $pagado = $pagado_todo-$sumaMora;
            $fecha_ayer_pago = date("Y-m-d",strtotime($pago_proximo->fechaPago."- 1 days"));
            if(empty($pago_proximo->fechaPago) && $pago_proximo->fechaPago >= $fecha_actual){
                $diasMora= 0;
            }else{
            $date1 = new DateTime($fecha_actual);
            $date2 = new DateTime($pago_proximo->fechaPago);
            $diff = $date1->diff($date2);
            // will output 2 days
                $diasMora= $diff->days;
            }

            if($solicitud->tipoCredito == 'm'){
                $date1 = new DateTime($solicitud->fechaDesembolso);
                $date2 = new DateTime($fecha_actual);
                $diff = $date1->diff($date2);
                // will output 2 days
                    $dvc= $diff->days;
                // $dvc = Moment().diff($solicitud->fechaDesembolso,'days');
            }else{
                if($solicitud->estatus_credito == "novado"){
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
            }
            if($dvc<=0){
                $diasVanCredito = 1;
              }else{
                $diasVanCredito = $dvc;
              }
            if($solicitud->ofertaEnviada != 2){
                $montoInvertido=$contra_oferta->montoAprobado;
                $totalP =$contra_oferta->totalPagar;
                $suma_novacion = $contra_oferta->tasaInteres+$contra_oferta->plataforma+$contra_oferta->aprobacionRapida+$contra_oferta->iva;
                if($solicitud->tipoCredito == 'm'){
                    $cuota=round($contra_oferta->totalPagar/$solicitud->plazo);
                }
            }else{
                if($solicitud->tipoCredito == 'm'){
                    $cuota=round($solicitud->totalPagar/$solicitud->plazo);
                }
                $montoInvertido=$solicitud->montoSolicitado;
                $totalP =$solicitud->totalPagar;
                $suma_novacion = $solicitud->tasaInteres+$solicitud->plataforma+$solicitud->aprobacionRapida+$solicitud->iva;
            }
            $gastosCobranzaSuma =0;
            $interesMoraSuma =0;
            if($diasMora > 1){
                // $im = 0;
                // $gc=0;
                // $ivagc =0;

                // $im = $montoInvertido*pow((1+$interesMoratorio),$diasMora);
                // $interesMora = $im-$montoInvertido;

                // if($solicitud->tipoCredito == 'm'){
                //     $gc = ($cuota*30)/100;
                //     $ivagc = ($gc*19)/100;
                //     $gastosCobranza = $gc+$ivagc;
                //     if($diasMora < 30){
                //         $gastosCobranza = ($gastosCobranza/30)*$diasMora;
                //     }
                // }else{
                //     $gc = ($totalP*30)/100;
                //     $ivagc = ($gc*19)/100;
                //     $gastosCobranza = $gc+$ivagc;
                //     if($diasMora < 60){
                //         $gastosCobranza = ($gastosCobranza/60)*$diasMora;
                //     }
                // }
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
                                $interesMora = ($im-$montoInvertido)+$value->interesMoraPendiente;

                                $interesMoraSuma=$interesMoraSuma + $interesMora;

                                if($solicitud->tipoCredito == 'm'){
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
                                    $gastosCobranzaSuma = $gastosCobranzaSuma + $gastosCobranza + $value->gastosCobranzaPendiente;

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
                                    $gastosCobranzaSuma = $gastosCobranzaSuma + $gastosCobranza + $value->gastosCobranzaPendiente;


                                }
                            }
                        }


                    }

                }
            }

            if($diasMora == 1){
                $monto_pagar = $pago_proximo->montoPagar;
            }else{
                $t=0;
                $tt=0;
                $t = round($totalP+$interesMoraSuma+$gastosCobranzaSuma);
                $tt = round($t-$pagado);
                $monto_pagar = $tt;
                $cuota = round($cuota+$interesMoraSuma+$gastosCobranzaSuma);
            }

            if($solicitud->tipoCredito == 'm'){
                $fecha_pago = date("Y-m-d",strtotime($solicitud->fechaDesembolso."+ ".$solicitud->plazo." month"));
                $diasMesesVencimiento = $solicitud->plazo-$cuotas_pagadas;
            }else{
                $fecha_pago = $pago_proximo->fechaPago;
                if($fecha_actual < $fecha_pago){
                    // $diasMesesVencimiento = Moment(this.pagoProximo.fechaPago).diff(fechaAct,'days')
                    $date1 = new DateTime($fecha_actual);
                    $date2 = new DateTime($pago_proximo->fechaPago);
                    $diff = $date1->diff($date2);
                    // will output 2 days
                    $diasMesesVencimiento= $diff->days;

                  }else{
                    $diasMesesVencimiento = 0;
                  }
            }
            $totalPagar = $totalP-$pagado;
            if($solicitud->tipoCredito == 'm'){
                $saldoDia = self::saldoAlDia($montoInvertido,$solicitud->plazo,$solicitud->tipoCredito,$interesMoraSuma,$gastosCobranzaSuma,$diasVanCredito, $solicitud->id_solicitud);
            }else{
              if($diasMora == 0){
                $saldoDia = self::saldoAlDia($montoInvertido,$solicitud->plazo,$solicitud->tipoCredito,$interesMoraSuma,$gastosCobranzaSuma,$diasVanCredito, $solicitud->id_solicitud);
              }else{
                $saldoDia = round($totalPagar+$interesMoraSuma+$gastosCobranzaSuma);
              }
            }


            $solicitudes[$key]['pago_proximo'] = $pago_proximo;
            $solicitudes[$key]['pagado'] = $pagado;
            $solicitudes[$key]['pagado_todo'] = $pagado_todo;
            $solicitudes[$key]['cuotas_pagadas'] = $cuotas_pagadas;
            $solicitudes[$key]['contra__oferta'] = $contra_oferta;
            $solicitudes[$key]['diasMora'] = $diasMora;
            $solicitudes[$key]['gastos_cobranza'] = $gastosCobranza;
            $solicitudes[$key]['interes_mora'] = $interesMora;
            $solicitudes[$key]['monto_pagar'] = $monto_pagar;
            $solicitudes[$key]['cuota'] = $cuota;
            $solicitudes[$key]['totalPagar'] = $totalPagar;
            $solicitudes[$key]['pagado_novacion'] = $pagado_novacion;
            $solicitudes[$key]['fecha_pago'] = $fecha_pago;
            $solicitudes[$key]['diasMesesVencimiento'] = $diasMesesVencimiento;
            $solicitudes[$key]['montoInvertido'] = $montoInvertido;
            $solicitudes[$key]['saldoDia'] = round($saldoDia-$pagado);
            $solicitudes[$key]['totalPagarInicial'] = $totalP;

        }
        $creditos = $solicitudes;
        // var_dump($creditos);
          $params =  [

              'creditos'      =>  $creditos,
              'view'           => 'reports.excel.creditosAbiertos'
          ];
            // var_dump($usuario);
          return Excel::download(
            new ViewExport (
                $params
            ),
            'creditosActivos.xlsx'
        );
            // return json_encode($creditos);

      }
      public function exportExcelCC(Request $request)
      {
        $fecha_actual = date("Y-m-d");
        $query =Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
        ->leftJoin('basicas', 'users.id', '=', 'basicas.idUserFk')
        ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','calculadoras.*','users.*','basicas.*');
        $query->where(function($query2) use ($request) {
            $query2->orWhere('calculadoras.estatus','pagado')
            ->OrWhere('calculadoras.estatus','castigado');
        });
        $solicitudes = $query->get();
        foreach ($solicitudes as $key => $solicitud) {
            $pago_proximo = Pagos::where('idSolicitudFk', $solicitud->id_solicitud)
            ->orderBy('id','desc')
            ->first();
            $pagos_parciales = PagosParciales::where('idSolicitudFk', $solicitud->id_solicitud)->first();
            $pagado = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("totalPagar");
            $pagado_todo = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            // ->where('concepto','!=','Novacion')
            ->get()
            ->sum("montoRepagar");
            $pagado_novacion = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','Novacion')
            ->get()
            ->sum("montoRepagar");
            $cuotas_pagadas = Pagos::where('idSolicitudFk', $solicitud->id_solicitud)
            ->where('estatusPago', 'pagado')
            ->count();
            $contra_oferta = ContraOferta::where('idCalculadoraFk', $solicitud->id_solicitud)->orderBy('id','desc')->first();
            $pagado_intereses = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("interesesMora");
            $pagado_gastos = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->where('concepto','!=','Novacion')
            ->get()
            ->sum("gastosCobranza");
            $dias_mora = DB::table("repagos")->where('idSolicitudFk',$solicitud->id_solicitud)
            ->get()
            ->sum("diasMora");
            $pagos =Pagos::where('idSolicitudFk', $solicitud->id_solicitud)->get();
            $diasMora=0;
            $interesMora =0;
            $gastosCobranza = 0;
            $interesMoratorio = pow(1+(22/100), (1/360))-1;
            $montoInvertido=0;
            $totalP =0;
            $suma_novacion=0;
            $cuota=0;
            $fecha_ayer_pago = date("Y-m-d",strtotime($pago_proximo->fechaPago."- 1 days"));
            if($pago_proximo->fechaPago >= $fecha_actual){
                $diasMora= 0;
            }else{
            $date1 = new DateTime($fecha_actual);
            $date2 = new DateTime($pago_proximo->fechaPago);
            $diff = $date1->diff($date2);
            // will output 2 days
                $diasMora= $diff->days;
            }

            if($solicitud->tipoCredito == 'm'){
                $date1 = new DateTime($solicitud->fechaDesembolso);
                $date2 = new DateTime($fecha_actual);
                $diff = $date1->diff($date2);
                // will output 2 days
                    $dvc= $diff->days;
                // $dvc = Moment().diff($solicitud->fechaDesembolso,'days');
            }else{
                if($solicitud->estatus_credito == "novado"){
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
            }
            if($dvc<=0){
                $diasVanCredito = 1;
              }else{
                $diasVanCredito = $dvc;
              }
              if($solicitud->ofertaEnviada != 2){
                $montoInvertido=$contra_oferta->montoAprobado;
                $totalP =$contra_oferta->totalPagar;
                $suma_novacion = $contra_oferta->tasaInteres+$contra_oferta->plataforma+$contra_oferta->aprobacionRapida+$contra_oferta->iva;
                if($solicitud->tipoCredito == 'm'){
                    $cuota=round($contra_oferta->totalPagar/$solicitud->plazo);
                }
            }else{
                if($solicitud->tipoCredito == 'm'){
                    $cuota=round($solicitud->totalPagar/$solicitud->plazo);
                }
                $montoInvertido=$solicitud->montoSolicitado;
                $totalP =$solicitud->totalPagar;
                $suma_novacion = $solicitud->tasaInteres+$solicitud->plataforma+$solicitud->aprobacionRapida+$solicitud->iva;
            }

            $gastosCobranzaSuma =0;
            $interesMoraSuma =0;
            if($diasMora > 1){
                // $im = 0;
                // $gc=0;
                // $ivagc =0;

                // $im = $montoInvertido*pow((1+$interesMoratorio),$diasMora);
                // $interesMora = $im-$montoInvertido;

                // if($solicitud->tipoCredito == 'm'){
                //     $gc = ($cuota*30)/100;
                //     $ivagc = ($gc*19)/100;
                //     $gastosCobranza = $gc+$ivagc;
                //     if($diasMora < 30){
                //         $gastosCobranza = ($gastosCobranza/30)*$diasMora;
                //     }
                // }else{
                //     $gc = ($totalP*30)/100;
                //     $ivagc = ($gc*19)/100;
                //     $gastosCobranza = $gc+$ivagc;
                //     if($diasMora < 60){
                //         $gastosCobranza = ($gastosCobranza/60)*$diasMora;
                //     }
                // }
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
                                $interesMora = ($im-$montoInvertido)+$value->interesMoraPendiente;

                                $interesMoraSuma=$interesMoraSuma + $interesMora;

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
                                    $gastosCobranzaSuma = $gastosCobranzaSuma + $gastosCobranza + $value->gastosCobranzaPendiente;

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
                                    $gastosCobranzaSuma = $gastosCobranzaSuma + $gastosCobranza + $value->gastosCobranzaPendiente;


                                }
                            }
                        }


                    }

                }
            }

            if($diasMora == 1){
                $monto_pagar = $pago_proximo->montoPagar;
            }else{
                $t=0;
                $tt=0;
                $t = round($totalP+$interesMoraSuma+$gastosCobranzaSuma);
                $tt = round($t-$pagado);
                $monto_pagar = $tt;
                $cuota = round($cuota+$interesMoraSuma+$gastosCobranzaSuma);
            }

            if($solicitud->tipoCredito == 'm'){
                $fecha_pago = date("Y-m-d",strtotime($solicitud->fechaDesembolso."+ ".$solicitud->plazo." month"));
                $diasMesesVencimiento = $solicitud->plazo-$cuotas_pagadas;
            }else{
                $fecha_pago = $pago_proximo->fechaPago;
                if($fecha_actual < $fecha_pago){
                    // $diasMesesVencimiento = Moment(this.pagoProximo.fechaPago).diff(fechaAct,'days')
                    $date1 = new DateTime($fecha_actual);
                    $date2 = new DateTime($pago_proximo->fechaPago);
                    $diff = $date1->diff($date2);
                    // will output 2 days
                    $diasMesesVencimiento= $diff->days;

                  }else{
                    $diasMesesVencimiento = 0;
                  }
            }
            if($solicitud->estatus_credito == 'castigado'){
                $totalPagar = $totalP-$pagos_parciales->totalNoPago;
            }else{
              $totalPagar = $totalP-$pagado;
            }

            if($solicitud->tipoCredito == 'm'){
                $saldoDia = self::saldoAlDia($montoInvertido,$solicitud->plazo,$solicitud->tipoCredito,$interesMoraSuma,$gastosCobranzaSuma,$diasVanCredito, $solicitud->id_solicitud);
            }else{
              if($diasMora == 0){
                $saldoDia = self::saldoAlDia($montoInvertido,$solicitud->plazo,$solicitud->tipoCredito,$interesMoraSuma,$gastosCobranzaSuma,$diasVanCredito, $solicitud->id_solicitud);
              }else{
                $saldoDia = round($totalPagar+$interesMoraSuma+$gastosCobranzaSuma);
              }
            }
            $solicitudes[$key]['pago_proximo'] = $pago_proximo;
            $solicitudes[$key]['pagado'] = round($pagado);
            $solicitudes[$key]['pagado_todo'] = $pagado_todo;
            $solicitudes[$key]['cuotas_pagadas'] = $cuotas_pagadas;
            $solicitudes[$key]['contra__oferta'] = $contra_oferta;
            $solicitudes[$key]['diasMora'] = $dias_mora;
            $solicitudes[$key]['gastos_cobranza'] = $pagado_gastos;
            $solicitudes[$key]['interes_mora'] = $pagado_intereses;
            $solicitudes[$key]['monto_pagar'] = 0;
            $solicitudes[$key]['cuota'] = $cuota;
            $solicitudes[$key]['totalPagar'] = $totalPagar;
            $solicitudes[$key]['pagado_novacion'] = $pagado_novacion;
            $solicitudes[$key]['fecha_pago'] = $fecha_pago;
            $solicitudes[$key]['diasMesesVencimiento'] = 0;
            $solicitudes[$key]['montoInvertido'] = $montoInvertido;
            $solicitudes[$key]['saldoDia'] = round($saldoDia-$pagado);
            $solicitudes[$key]['totalPagarInicial'] = $totalP;
            $solicitudes[$key]['pagos_parciales'] = $pagos_parciales;

        }
        $creditos = $solicitudes;
          $params =  [

              'creditos'      =>  $creditos,
              'view'           => 'reports.excel.creditosCerrados'
          ];
            // var_dump($usuario);
          return Excel::download(
            new ViewExport (
                $params
            ),
            'creditosCerrados.xlsx'
        );


      }

      function getAllCupones(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);
            $per_page = (!empty($request->per_page)) ? $request->per_page : Cupones::count();
            $result = Cupones::paginate($per_page);
            $response = $result;

            if($result->isEmpty()){
                return response()->json([
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

    public function createCupon(Request $request){
        $user = Cupones::create([
            'valor' => $request->valor,
            'nombre' => $request->nombre,
            'desde' => $request->desde,
            'hasta' => $request->hasta,

             ]);

             return response()->json([

                'message'  => 'Creado correctamente',


            ]);
    }

    public function obtenerCupon(Request $request){
        if(Cupones::where('nombre',$request->nombre)->exists()){
            if(Cupones::where('nombre',$request->nombre)->where('uso',1)->exists()){
                return response()->json([
                    'valor'=>0,
                    'message'  => 'Este codigo ya fue usado',


                ]);
            }else{
                $cupon= Cupones::where('nombre',$request->nombre)->first();
                $cupon2= Cupones::find($cupon->id);
                $cupon2->uso=1;
                $cupon2->save();
                return response()->json([
                    'valor'=>$cupon->valor,
                    'message'  => 'Este codigo es correcto',


                ]);
            }

        }else {
            return response()->json([
                'valor'=>0,
                'message'  => 'Codigo no existe',


            ]);
        }

    }
    function saldoAlDia($montoS,$plazo,$tipoCredito,$interesMora,$gastosCobranza,$diasVanCredito, $id_solicitud){
        $saldoDia = 0;
        $fecha_actual = date("Y-m-d");
        if($tipoCredito == "m"){
            $pagos = Pagos::leftJoin('repagos', 'pagos.id', '=', 'repagos.idPagoFk')
            ->select('pagos.id as id_pago','pagos.*','repagos.concepto','repagos.montoRepagar','repagos.metodoRepago','repagos.fecha')
            ->where('pagos.idSolicitudFk', $id_solicitud)
            ->get();
            $configCal = ConfigCalculadora::where('tipo','=',1)->first();
                    $porPlat = $configCal->porcentaje_plataforma;
                    $porIva = $configCal->porcentaje_iva;
                    $tasa = $configCal->tasa;
                    $porExpUno = $configCal->porcentaje_express;
                    $porExpDos = $configCal->porcentaje_express_dos;
                    $porExpTres = $configCal->porcentaje_express_tres;

                    $montoSolicitado=$montoS;
                    $plazoMeses=$plazo;
                    $plataforma=round($montoSolicitado)*($porPlat/100);
                    $plazoInicialDias = $plazo*30;
                      $cuotas=$plazoMeses;
                      $monto=$montoSolicitado;
                    $dvc=0;
                    $sumIntereses = 0;
                    $interesesAct = 0;
                    $sumdvc = 0;
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

                      $subtotal=round($montoSolicitado+$taxInTotal);
                    //   var_dump($pagos);
                    $ar = [];
                        foreach ($pagos as $index => $element) {

                            $mesantes = date("Y-m-d",strtotime($element->fechaPago."- 1 month"));
                            // dvc =  Moment().diff(mesantes,'days')
                            if($mesantes >= $fecha_actual){
                                $dvc =0;
                            }else{
                                $date1 = new DateTime($fecha_actual);
                                $date2 = new DateTime($mesantes);
                                $diff = $date2->diff($date1);
                                // will output 2 days
                                $dvc= $diff->days;
                            }

                            // array_push($ar,$dvc);
                            $interesesAct = 0;
                            if($dvc<1){
                            if($index > 0){
                                $dvc = 0;
                            }else{
                                $dvc = 1;
                            }

                            }else if($dvc>30){
                            $dvc = 30;
                            }

                            $sumdvc = $sumdvc + $dvc;
                            $interesesAct = ($element->intereses/30)*$dvc;


                            $sumIntereses = $sumIntereses+$interesesAct;
                        }
                      $t_interes_al_dia = ceil($sumIntereses);

                      if($montoS<=1200000){
                        $ap_express=$montoS*$porExpUno/100;
                        // $iva=($plataforma)+$ap_express))*19/100;
                        $plataforma=$plataforma*$plazo;
                        $plataforma_al_dia = ($plataforma/$plazoInicialDias)*$sumdvc;
                        $iva=($plataforma+$ap_express)*$porIva/100;
                        $iva_al_dia=($plataforma_al_dia+$ap_express)*$porIva/100;
                        $total=$subtotal+$plataforma+$ap_express+$iva;
                        $cuotaMensual=round($total/$plazo);
                        $saldoDia = $montoS+$t_interes_al_dia+$plataforma_al_dia+$ap_express+$iva_al_dia+$interesMora+$gastosCobranza;

                      }else
                      if($montoS>1200000 && $montoS<=1700000){
                        $ap_express=$montoS*$porExpDos/100;
                        // $iva=($plataforma)+$ap_express))*19/100;

                        $plataforma=$plataforma*$plazo;
                        $plataforma_al_dia = ($plataforma/$plazoInicialDias)*$sumdvc;
                        $iva=($plataforma+$ap_express)*$porIva/100;
                        $iva_al_dia=($plataforma_al_dia+$ap_express)*$porIva/100;
                        $total=$subtotal+$plataforma+$ap_express+$iva;
                        $cuotaMensual=round($total/$plazo);
                        $saldoDia = $montoS+$t_interes_al_dia+$plataforma_al_dia+$ap_express+$iva_al_dia+$interesMora+$gastosCobranza;

                      }else
                      if($montoS>=1700001){
                        $ap_express=$montoS*$porExpTres/100;
                        // $iva=($plataforma)+$ap_express))*19/100;
                        $plataforma=$plataforma*$plazo;
                        $plataforma_al_dia = ($plataforma/$plazoInicialDias)*$sumdvc;
                        $plata=$plataforma*$plazo;
                        $iva=($plataforma+$ap_express)*$porIva/100;
                        $iva_al_dia=($plataforma_al_dia+$ap_express)*$porIva/100;
                        $total=$subtotal+$plataforma+$ap_express+$iva;
                        $cuotaMensual=round($total/$plazo);
                        $saldoDia = $montoS+$t_interes_al_dia+$plataforma_al_dia+$ap_express+$iva_al_dia+$interesMora+$gastosCobranza;

                      }
        }else{
            $configCal = ConfigCalculadora::where('tipo','=',2)->first();
            $porPlat = $configCal->porcentaje_plataforma;
            $porIva = $configCal->porcentaje_iva;
            $tasa = $configCal->tasa;
            $porExp = $configCal->porcentaje_express;

            $plazoDias=$diasVanCredito;
            $montoSolicitadoDias=$montoS;
            $tasitaNueva=(pow((1+($tasa/100)),($plazoDias/360))-1);
            $t_interesDias=$tasitaNueva*$montoSolicitadoDias;
            // console.log('tasainteres',$t_interesDias)
            $subtotalDias=$montoSolicitadoDias+$t_interesDias;

            $plataformaDias=$porPlat*$plazoDias;
            $ap_expressDias=($montoSolicitadoDias)*$porExp/100;
            $ivaDias=($plataformaDias+$ap_expressDias)*$porIva/100;
            $totalDias=$subtotalDias+$plataformaDias+$ap_expressDias+$ivaDias;
            $saldoDia= round($totalDias)+$interesMora+$gastosCobranza;
        }

        return $saldoDia;
    }



    function deleteCupon(Request $request){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $cupon = Cupones::find($request->id);
            $cupon->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Cupon eliminado",200);
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


    public function obtenerCuponPreview(Request $request){
        if(Cupones::where('nombre',$request->nombre)->exists()){
            if(Cupones::where('nombre',$request->nombre)->where('uso',1)->exists()){
                return response()->json([
                    'valor'=>0,
                    'message'  => 'Este codigo ya fue usado',


                ]);
            }else{
                $cupon= Cupones::where('nombre',$request->nombre)->first();
                $cupon2= Cupones::find($cupon->id);
                // $cupon2->uso=1;
                // $cupon2->save();
                return response()->json([
                    'valor'=>$cupon->valor,
                    'message'  => 'Este codigo es correcto',


                ]);
            }

        }else {
            return response()->json([
                'valor'=>0,
                'message'  => 'Codigo no existe',


            ]);
        }

    }

    function actualizar_desembolso(Request $request){
        try{


            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');
            $query->where(function($query2) use ($request) {
                $query2->orWhere('calculadoras.estatus','novado')
                ->OrWhere('calculadoras.estatus','pendiente de novacion')
                ->OrWhere('calculadoras.estatus','moroso')
                ->OrWhere('calculadoras.estatus','restructurado')
                ->OrWhere('calculadoras.estatus','abierto')
                ->orWhere('calculadoras.estatus','pagado')
                ->OrWhere('calculadoras.estatus','castigado');
            });
            $solicitudes = $query->orderBy('calculadoras.id','desc')->get();
            foreach ($solicitudes as $key => $solicitud) {


                // $solicitud=Calculadora::find($request->idSolicitudFk);
                $co= ContraOferta::where('idCalculadoraFk',$solicitud->id_solicitud)->orderBy('id','desc')->first();
                $pagos= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->get();
                if($solicitud->ofertaEnviada == 2){
                    $montoA = $solicitud->montoSolicitado;
                    $totalA = $solicitud->totalPagar;
                    $plataforma = $solicitud->plataforma;
                    $aprobacion = $solicitud->aprobacionRapida;
                    $iva = $solicitud->iva;
                    $tasaInteres=$solicitud->tasaInteres;
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                    $plataforma = $co->plataforma;
                    $aprobacion = $co->aprobacionRapida;
                    $iva = $co->iva;
                    $tasaInteres=$co->tasaInteres;
                }

                $fechaActual = date('Y-m-d');

                // $solicitud=Calculadora::find($so->idSolicitudFk);
                if($solicitud->tipoCredito == 'm'){
                    $pagos= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->get();
                    $configCal = ConfigCalculadora::where('tipo','=',1)->first();
                    $tasa = $configCal->tasa;
                    $cuotas=$solicitud->plazo;
                    $monto=$montoA;
                    $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );
                    $valor_de_cuota =$t_interes;
                    $saldo_al_capital =$monto;
                    $interesos;
                    $abono_al_capital;
                    $items = array();
                    $sum=0;
                    $resta=1000;
                    foreach ($pagos as $i => $pago) {

                    // for ($i=0; $i <$solicitud->plazo; $i++) {
                        $pagoAct= Pagos::find($pago->id);
                        $interesos = $saldo_al_capital * $tasa;
                        $abono_al_capital = $valor_de_cuota - $interesos;
                        $pagoAct->saldoInicial=round($saldo_al_capital);

                        $saldo_al_capital -= $abono_al_capital;
                        $numero = $i + 1;

                        $interesos = $interesos;
                        $abono_al_capital = $abono_al_capital;
                        $saldo_al_capital = $saldo_al_capital;
                        // $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$numero." month"));
                            // $pagoAct->capital = round($abono_al_capital);
                            // $pagoAct->intereses=round($interesos);
                            // $pagoAct->plataforma=round($plataforma/$solicitud->plazo);
                            // $pagoAct->aprobacionRapida=round($aprobacion/$solicitud->plazo);
                            // $pagoAct->iva=round($iva/$solicitud->plazo);

                        $pagoAct->save();
                        // $pago = Pagos::create([
                        //     'idSolicitudFk' => $request->idSolicitudFk,
                        //     'idUsuarioFk' =>$request->idUserFk,
                        //     'fechaPago' => $fecha,
                        //     'montoPagar'=>round($totalA/$solicitud->plazo),
                        //     'capital' => round($abono_al_capital),
                        //     'intereses'=>round($interesos),
                        //     'plataforma'=>round($plataforma/$solicitud->plazo),
                        //     'aprobacionRapida'=>round($aprobacion/$solicitud->plazo),
                        //     'iva'=>round($iva/$solicitud->plazo)

                        // ]);
                    }
                }else{
                    $pago= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->first();
                    $pago->capital = $montoA;
                    $pago->intereses=round($tasaInteres);
                    $pago->plataforma=round($plataforma);
                    $pago->aprobacionRapida=round($aprobacion);
                    $pago->iva=round($iva);
                    $pago->save();
                    // $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$solicitud->plazo." days"));
                    // $pago = Pagos::create([
                    //     'idSolicitudFk' => $request->idSolicitudFk,
                    //     'idUsuarioFk' =>$solicitud->idUserFk,
                    //     'fechaPago' => $fecha,
                    //     'montoPagar'=>$totalA,
                    //     'capital' => $montoA,
                    //     'intereses'=>round($tasaInteres),
                    //     'plataforma'=>round($plataforma),
                    //     'aprobacionRapida'=>round($aprobacion),
                    //     'iva'=>round($iva)
                    // ]);
                }

            }
            DB::commit(); // Guardamos la transaccion


            return response()->json('Actualizado correctamente!',201);
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

    function actualizar_desembolso_uno(Request $request){
        try{


            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $query = Calculadora::leftJoin('users', 'users.id', '=', 'calculadoras.idUserFk')
            ->select('calculadoras.id as id_solicitud','calculadoras.estatus as estatus_credito','users.*','calculadoras.*');
            $query->where('calculadoras.id',$request->id);
            // $query->where(function($query2) use ($request) {
            //     $query2->orWhere('calculadoras.estatus','novado')
            //     ->OrWhere('calculadoras.estatus','pendiente de novacion')
            //     ->OrWhere('calculadoras.estatus','moroso')
            //     ->OrWhere('calculadoras.estatus','restructurado')
            //     ->OrWhere('calculadoras.estatus','abierto')
            //     ->orWhere('calculadoras.estatus','pagado')
            //     ->OrWhere('calculadoras.estatus','castigado');
            // });
            $solicitud = $query->orderBy('calculadoras.id','desc')->first();
            // foreach ($solicitudes as $key => $solicitud) {


                // $solicitud=Calculadora::find($request->idSolicitudFk);
                $co= ContraOferta::where('idCalculadoraFk',$solicitud->id_solicitud)->orderBy('id','desc')->first();
                $pagos= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->get();
                if($solicitud->ofertaEnviada == 2){
                    $montoA = $solicitud->montoSolicitado;
                    $totalA = $solicitud->totalPagar;
                    $plataforma = $solicitud->plataforma;
                    $aprobacion = $solicitud->aprobacionRapida;
                    $iva = $solicitud->iva;
                    $tasaInteres=$solicitud->tasaInteres;
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                    $plataforma = $co->plataforma;
                    $aprobacion = $co->aprobacionRapida;
                    $iva = $co->iva;
                    $tasaInteres=$co->tasaInteres;
                }

                $fechaActual = date('Y-m-d');

                // $solicitud=Calculadora::find($so->idSolicitudFk);
                if($solicitud->tipoCredito == 'm'){
                    $pagos= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->get();
                    $configCal = ConfigCalculadora::where('tipo','=',1)->first();
                    $tasa = $configCal->tasa;
                    $cuotas=$solicitud->plazo;
                    $monto=$montoA;
                    $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );
                    $valor_de_cuota =$t_interes;
                    $saldo_al_capital =$monto;
                    $interesos;
                    $abono_al_capital;
                    $items = array();
                    $sum=0;
                    $resta=1000;
                    foreach ($pagos as $i => $pago) {

                    // for ($i=0; $i <$solicitud->plazo; $i++) {
                        $pagoAct= Pagos::find($pago->id);
                        $interesos = $saldo_al_capital * $tasa;
                        $abono_al_capital = $valor_de_cuota - $interesos;
                        $saldo_al_capital -= $abono_al_capital;
                        $numero = $i + 1;

                        $interesos = $interesos;
                        $abono_al_capital = $abono_al_capital;
                        $saldo_al_capital = $saldo_al_capital;
                        // $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$numero." month"));
                        $pagoAct->capital = round($abono_al_capital);
                        $pagoAct->intereses=round($interesos);
                        $pagoAct->plataforma=round($plataforma/$solicitud->plazo);
                        $pagoAct->aprobacionRapida=round($aprobacion/$solicitud->plazo);
                        $pagoAct->iva=round($iva/$solicitud->plazo);
                        $pagoAct->save();
                        // $pago = Pagos::create([
                        //     'idSolicitudFk' => $request->idSolicitudFk,
                        //     'idUsuarioFk' =>$request->idUserFk,
                        //     'fechaPago' => $fecha,
                        //     'montoPagar'=>round($totalA/$solicitud->plazo),
                        //     'capital' => round($abono_al_capital),
                        //     'intereses'=>round($interesos),
                        //     'plataforma'=>round($plataforma/$solicitud->plazo),
                        //     'aprobacionRapida'=>round($aprobacion/$solicitud->plazo),
                        //     'iva'=>round($iva/$solicitud->plazo)

                        // ]);
                    }
                }else{
                    $pago= Pagos::where('idSolicitudFk',$solicitud->id_solicitud)->first();
                    $pago->capital = $montoA;
                    $pago->intereses=round($tasaInteres);
                    $pago->plataforma=round($plataforma);
                    $pago->aprobacionRapida=round($aprobacion);
                    $pago->iva=round($iva);
                    $pago->save();
                    // $fecha = date("Y-m-d",strtotime($fechaActual. "+ ".$solicitud->plazo." days"));
                    // $pago = Pagos::create([
                    //     'idSolicitudFk' => $request->idSolicitudFk,
                    //     'idUsuarioFk' =>$solicitud->idUserFk,
                    //     'fechaPago' => $fecha,
                    //     'montoPagar'=>$totalA,
                    //     'capital' => $montoA,
                    //     'intereses'=>round($tasaInteres),
                    //     'plataforma'=>round($plataforma),
                    //     'aprobacionRapida'=>round($aprobacion),
                    //     'iva'=>round($iva)
                    // ]);
                }

            // }
            DB::commit(); // Guardamos la transaccion


            return response()->json('Actualizado correctamente!',201);
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

    function solicitarFirma(Request $request){
        try{
            // $res = self::crearContraOferta($request->idSolicitud, $request->tipo, $request->tipo_monto);
            $calculadora=Calculadora::where('id',$request->idSolicitud)->first();
            $usuario =User::where('id',$calculadora->idUserFk)->first();
            $financiera=Financiera::where('idUserFk',$calculadora->idUserFk)->first();
            $basica=Basica::where('idUserFk',$calculadora->idUserFk)->first();
            $referencia=Referencias::where('idUserFk',$calculadora->idUserFk)->first();
            $co= ContraOferta::where('idCalculadoraFk',$request->idSolicitud)->orderBy('id','desc')->first();
                if($calculadora->ofertaEnviada == 2){
                    $montoA = $calculadora->montoSolicitado;
                    $totalA = $calculadora->totalPagar;
                    $plataforma = $calculadora->plataforma;
                    $aprobacion = $calculadora->aprobacionRapida;
                    $iva = $calculadora->iva;
                    $tasaInteres=$calculadora->tasaInteres;
                    $cuota=round($calculadora->totalPagar/$calculadora->plazo);
                }else{
                    $montoA = $co->montoAprobado;
                    $totalA = $co->totalPagar;
                    $plataforma = $co->plataforma;
                    $aprobacion = $co->aprobacionRapida;
                    $iva = $co->iva;
                    $tasaInteres=$co->tasaInteres;
                    $cuota=round($co->totalPagar/$co->plazo);
                }

            $pathToFile = $calculadora->documentoContrato;

            $pathToFilePagare = $calculadora->documentoPagare;

            $pathToFileCartaAutorizacion = $calculadora->documentoCarta;


          //   return \PDFt::loadView('reports.pdf.passenger', $params)->download('nombre-archivo.pdf');
            if($calculadora->estatus =="aprobado" && $calculadora->estatus_firma =="pendiente" && $calculadora->ofertaEnviada >=0 ){
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
              'idUserFk'     => $usuario->id,
              'idSolicitudFk'  => $calculadora->id,
              'token_firma' => $token
              ]);
              $usuarios=User::where('id',$usuario->id)->first();
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
                          $monto="$".number_format($totalA);
                          $numerCredito=$credito->numero_credito;
                          $expedicion=$credito->created_at;
                          $plazo_pago = $credito->plazo." ".$Lplazo;
                          $monto_aprobado = "$".number_format($montoA);
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
                   $fechita=date('d-m-Y h:i A');
                   $credito->notificadoFirma =0;
                   $credito->save();
                }


            return response()->json($calculadora);
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
    public function matriz($id){
        //calculo matriz

        $ingresoActividad = \DB::table('ingreso_actividad_principal_porcentajes')->get();
        $financieraBase = Financiera::where('idUserFk','=',$id)->first();
        $usuario = Basica::where('idUserFk','=',$id)->first();

        $diasPago = $financieraBase->periodoPagoNomina;
        $tipoContrato = $financieraBase->situacionLaboral;
        $nombreEmpresa = $financieraBase->nombreEmpresa;
        $antiguedadLaboral = $financieraBase->antiguedadLaboral;
        $nombreCargo = $financieraBase->nombreCargo;

        if($usuario->nroPersonasDependenEconomicamente){
            if($usuario->nroPersonasDependenEconomicamente == "Ninguna"){
                $usuario->nroPersonasDependenEconomicamente = 0;
              }else if($usuario->nroPersonasDependenEconomicamente == "Una Persona"){
                $usuario->nroPersonasDependenEconomicamente = 1;
              }else if($usuario->nroPersonasDependenEconomicamente == "Dos Personas"){
                $usuario->nroPersonasDependenEconomicamente = 2;
              }else if($usuario->nroPersonasDependenEconomicamente == "Tres Personas"){
                $usuario->nroPersonasDependenEconomicamente = 3;
              }else if($usuario->nroPersonasDependenEconomicamente == "Mas de Tres Personas"){
                $usuario->nroPersonasDependenEconomicamente = 4;
              }
        }

        if($usuario->personasaCargo){
            if($usuario->personasaCargo == "Ninguna"){
                $usuario->personasaCargo = 0;
              }else if($usuario->personasaCargo == "Una Persona"){
                $usuario->personasaCargo = 1;
              }else if($usuario->personasaCargo == "Dos Personas"){
                $usuario->personasaCargo = 2;
              }else if($usuario->personasaCargo == "Tres Personas"){
                $usuario->personasaCargo = 3;
              }else if($usuario->personasaCargo == "Mas de Tres Personas"){
                $usuario->personasaCargo = 4;
              }
        }
        if($financieraBase){
            $totalIngreso = $financieraBase->ingresoTotalMensual;
        }else{
            $totalIngreso = 0;
        }

        $calculoIngreso = new \stdClass();
        $total = new \stdClass();
        $total->porcentaje = 0;
        $total->total = 0;

        foreach ($ingresoActividad as $actividad) {

            if($actividad->id == 1){
                $alojamiento = new \stdClass();
                $alojamiento->total = ($totalIngreso*$actividad->puntaje)/100;
                $alojamiento->porcentaje = floatval($actividad->puntaje);
                $total->total = $total->total + $alojamiento->total;
                $total->porcentaje = $total->porcentaje + $alojamiento->porcentaje;

                $calculoIngreso->alojamiento = $alojamiento;

              }

              if($actividad->id == 2){
                $alimentos = new \stdClass();
                $alimentos->total = ($totalIngreso*$actividad->puntaje)/100;
                $alimentos->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $alimentos->total;
                $total->porcentaje = $total->porcentaje + $alimentos->porcentaje;

                $calculoIngreso->alimentos = $alimentos;
              }
              if($actividad->id == 3){
                $servicios_publicos = new \stdClass();
                $servicios_publicos->total = ($totalIngreso*$actividad->puntaje)/100;
                $servicios_publicos->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $servicios_publicos->total;
                $total->porcentaje = $total->porcentaje + $servicios_publicos->porcentaje;

                $calculoIngreso->servicios_publicos = $servicios_publicos;
              }
              if($actividad->id == 4){
                $transporte = new \stdClass();
                $transporte->total = ($totalIngreso*$actividad->puntaje)/100;
                $transporte->porcentaje = floatval( $actividad->puntaje);
                $total->total  = $total->total + $transporte->total;
                $total->porcentaje = $total->porcentaje + $transporte->porcentaje;

                $calculoIngreso->transporte = $transporte;
              }
              if($actividad->id == 5){
                $vestido = new \stdClass();
                $vestido->total = ($totalIngreso*$actividad->puntaje)/100;
                $vestido->porcentaje = floatval( $actividad->puntaje);
                $total->total  = $total->total + $vestido->total;
                $total->porcentaje = $total->porcentaje + $vestido->porcentaje;

                $calculoIngreso->vestido = $vestido;
              }
              if($actividad->id == 6){
                $recreacion = new \stdClass();
                $recreacion->total = ($totalIngreso*$actividad->puntaje)/100;
                $recreacion->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $recreacion->total;
                $total->porcentaje =  $total->porcentaje + $recreacion->porcentaje;

                $calculoIngreso->recreacion = $recreacion;
              }
              if($actividad->id == 7){
                $muebles = new \stdClass();
                $muebles->total = ($totalIngreso*$actividad->puntaje)/100;
                $muebles->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $muebles->total;
                $total->porcentaje = $total->porcentaje +$muebles->porcentaje;

                $calculoIngreso->muebles = $muebles;
              }
              if($actividad->id == 8){
                $bebidas_alcoholicas = new \stdClass();
                $bebidas_alcoholicas->total = ($totalIngreso*$actividad->puntaje)/100;
                $bebidas_alcoholicas->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $bebidas_alcoholicas->total;
                $total->porcentaje = $total->porcentaje + $bebidas_alcoholicas->porcentaje;

                $calculoIngreso->bebidas_alcoholicas = $bebidas_alcoholicas;
              }
              if($actividad->id == 9){
                $salud = new \stdClass();
                $salud->total = ($totalIngreso*$actividad->puntaje)/100;
                $salud->porcentaje = floatval( $actividad->puntaje);
                $total->total = $total->total + $salud->total;
                $total->porcentaje = $total->porcentaje + $salud->porcentaje;

                $calculoIngreso->salud = $salud;
              }
              if($actividad->id == 10){
                $calculoIngreso->promedio_composicion_hogar = intval( $actividad->puntaje);
              }
              if($actividad->id == 11){
                $calculoIngreso->aporte_hogar = $actividad->puntaje;
              }
        }

        $calculoIngreso->total = $total;
        // $calculoIngreso->total->porcentaje = $total->porcentaje;

        $calculoString = get_object_vars($calculoIngreso);
        $calculoString = json_encode($calculoString);

        // \DB::table('evaluacions')
        //     ->where('evaluacions.idUserFk',$id)
        //     ->update([
        //         'calculoIngreso'=> $calculoString
        //     ]);

        $dependientes = $usuario->nroPersonasDependenEconomicamente + 1;
        $ciudad = explode("-", $usuario->ciudad);

        if(CannonMensualAlojamiento::
            where('estrato','=',$usuario->estrato)
            ->where('alojamiento','=',$ciudad[0])
            ->exists()){
                $result = CannonMensualAlojamiento::
                where('estrato','=',$usuario->estrato)
                ->where('alojamiento','=',$ciudad[0])
                ->first();
            }else{
                $result = CannonMensualAlojamiento::
                where('estrato','=',$usuario->estrato)
                ->where('alojamiento','=','OTRAS')
                ->first();
            }

        $gastoMonetario = new \stdClass();

        $totalRenta = $result->monto;
        if($usuario->tipoVivienda == "Rentada"){
            $gastoMonetario->renta = 0;
        }


        if($usuario->tipoVivienda == "Rentada"){
            $gastoMonetario->renta = ($totalRenta*100)/100;
          }else if($usuario->tipoVivienda =="Propia"){
            $gastoMonetario->renta  = ($totalRenta*40)/100;
          }else if($usuario->tipoVivienda =="Familiar"){
            $gastoMonetario->renta  = ($totalRenta*20)/100;
          }else if($usuario->tipoVivienda =="Hipotecada"){
            $gastoMonetario->renta  = ($totalRenta*120)/100;
          }

          //alimentacion
          $alimentacionMon = new \stdClass();
          $alimentacionMon->per_capita =  $calculoIngreso->alimentos->total/$calculoIngreso->promedio_composicion_hogar;
          $alimentacionMon->total = ($dependientes*$alimentacionMon->per_capita);

          $gastoMonetario->alimentacion = $alimentacionMon;

        //Servicios
        $serviciosMon = new \stdClass();
        if($usuario->estrato == 1){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*0.6)/1.5;
        }else if($usuario->estrato == 2){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*0.6)/1.5;
        }else if($usuario->estrato == 3){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*0.8)/1.5;
        }else if($usuario->estrato == 4){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*1)/1.5;
        }else if($usuario->estrato == 5){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*1.2)/1.5;
        }else if($usuario->estrato == 6){
            $serviciosMon->per_capita = ($calculoIngreso->servicios_publicos->total*1.5)/1.5;
        }

        $serviciosMon->total = ($dependientes*$serviciosMon->per_capita);
        $gastoMonetario->servicios = $serviciosMon;

        //Transporte
        $transporteMon = new \stdClass();
        $transporteMon->per_capita = $calculoIngreso->transporte->total;
        $transporteMon->total = $transporteMon->per_capita*1.5;

        $gastoMonetario->transporte = $transporteMon;

        //Vestido
        $vestidoMon = new \stdClass();
        $vestidoMon->per_capita = $calculoIngreso->vestido->total/$calculoIngreso->promedio_composicion_hogar;
        $vestidoMon->total = ($dependientes*$vestidoMon->per_capita);

        $gastoMonetario->vestido = $vestidoMon;

        //Cotizacion a salud
        $cotizacion_saludMon = new \stdClass();
        $gastoMonetario->cotizacion_salud = ($totalIngreso*0.08);

        //Total egresos
        $gastoMonetario->totalEgresos = $gastoMonetario->renta + $gastoMonetario->alimentacion->total + $gastoMonetario->servicios->total + $gastoMonetario->transporte->total + $gastoMonetario->vestido->total + $gastoMonetario->cotizacion_salud;


        //Total Egresos con Personas Activas Economicamente
        if($usuario->personasaCargo > 0){
            $gastoMonetario->totalEgresosPAE = $gastoMonetario->totalEgresos-((($gastoMonetario->totalEgresos*$calculoIngreso->aporte_hogar)/100)*$usuario->personasaCargo);
          }else{
            $gastoMonetario->totalEgresosPAE = $gastoMonetario->totalEgresos;
          }


          if( $financieraBase->pagandoActual && $financieraBase->pagandoActual !=" "){
            $todalDeudas =  intval($financieraBase->pagandoActual);
          }else{
            $todalDeudas = 0;
          }

          if($gastoMonetario->totalEgresosPAE > $financieraBase->egresoTotalMensual){
            $gastoMonetario->totalDisponible = $totalIngreso-$gastoMonetario->totalEgresosPAE-$todalDeudas;
            $gastoMonetario->diponibleEndeudamiento = ($gastoMonetario->totalDisponible*70)/100;
          }else{
            $gastoMonetario->totalDisponible = $totalIngreso-$financieraBase->egresoTotalMensual-$todalDeudas;
            $gastoMonetario->diponibleEndeudamiento = ($gastoMonetario->totalDisponible*70)/100;
          }



            // \DB::table('evaluacions')
            //     ->where('evaluacions.idUserFk',$id)
            //     ->update([
            //         'gastoMonetario'=> $calculoGastosString
            //     ]);

            //balance caja
            //configMeses()
            $configCalMeses = ConfigCalculadora::where('tipo','=',1)->first();

            $plazoMeses = intval($configCalMeses->dias_minimo);
            $montoSolicitado = intval($configCalMeses->monto_minimo);
            $montoRestMeses = intval($configCalMeses->monto_restriccion);
            $montoResTooltipMeses = intval($configCalMeses->monto_restriccion_tooltip);
            $diasRestMeses = intval($configCalMeses->dias_restriccion);
            $tasaMeses = intval($configCalMeses->tasa);
            $porExpressMeses = intval($configCalMeses->porcentaje_express);
            $porExpressDosMeses = intval($configCalMeses->porcentaje_express_dos);
            $porExpressTresMeses = intval($configCalMeses->porcentaje_express_tres);
            $porIvaMeses = intval($configCalMeses->porcentaje_iva);
            $porplataformaMeses = intval($configCalMeses->porcentaje_plataforma);

            //configDias()
            $configCalDias = ConfigCalculadora::where('tipo','=',2)->first();

            $plazoDias = intval($configCalDias->dias_minimo);
            $montoSolicitadoDias = intval($configCalDias->monto_minimo);
            $montoRestDias = intval($configCalDias->monto_restriccion);
            $montoResTooltipDias = intval($configCalDias->monto_restriccion_tooltip);
            $diasRestDias = intval($configCalDias->dias_restriccion);
            $tasaDias = intval($configCalDias->tasa);
            $porExpressDias = intval($configCalDias->porcentaje_express);
            $porIvaDias = intval($configCalDias->porcentaje_iva);
            $porplataformaDias = intval($configCalDias->porcentaje_plataforma);

            //getCOMeses
            $configCOMeses = ConfigContraOferta::where('tipo_credito','=',1)->first();

            //getCODias
            $configCODias = ConfigContraOferta::where('tipo_credito','=',2)->first();

           /* $obtenerMonto1 = obtenerMonto($configCOMeses->monto_maximo, 12, 1);*/
            $montoS = $configCOMeses->monto_maximo;
            $cant_mensual = 12;
            $tipo = 1;

            if($porplataformaMeses > 0){
                $porPlat = $porplataformaMeses;
            }else{
                $porPlat = 4;
            }

            if($porIvaMeses > 0){
                $porIva = $porIvaMeses;
            }else{
                $porIva = 19;
            }

            if($tasaMeses > 0){
                $tasa = $tasaMeses;
            }else{
                $tasa = 0.01916667;
            }

            if($porExpressMeses > 0){
                $porExpUno = $porExpressMeses;
            }else{
                $porExpUno = 30;
            }

            if($porExpressDosMeses > 0){
                $porExpUno = $porExpressDosMeses;
            }else{
                $porExpUno = 27.5;
            }

            if($porExpressTresMeses > 0){
                $porExpUno = $porExpressTresMeses;
            }else{
                $porExpUno = 25;
            }


            $plataforma=floatval($montoS)*($porPlat/100);
            $cuotas=$cant_mensual;
            $monto=floatval($montoS);
            $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );

            //Amortizacion
            $valor_de_cuota = $t_interes;
            $saldo_al_capital = floatval($montoS);
            $items = array();
            $sum = 0;


            for ($i=0; $i< $cant_mensual; $i++) {
                $interesos = $saldo_al_capital * $tasa;
                $abono_al_capital = $valor_de_cuota - $interesos;
                $saldo_al_capital = $saldo_al_capital - $abono_al_capital;
                $numero = $i + 1;
                $item = array($numero, $interesos, $abono_al_capital, $valor_de_cuota, round($saldo_al_capital, 2));
                array_push($items, $item);
                $sum = $sum + floatval($interesos);
            }

            $taxInTotal = $sum;
            $presubtotal = floatval($montoS)+floatval($taxInTotal);
            $subtotal = round($presubtotal);

            if(floatval($montoS)<=1200000){

                $ap_express=floatval($montoS)*$porExpUno/100;

                $plataforma=floatval($plataforma)*floatval($cant_mensual);
                $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                $cuotaMensual=round(floatval($total)/floatval($cant_mensual));
              }else
              if($montoS>1200000 && $montoS<=1700000){
                $ap_express=floatval($montoS)*$porExpDos/100;


                $plataforma=floatval($plataforma)*floatval($cant_mensual);
                $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                $cuotaMensual=round(floatval($total)/floatval($cant_mensual));

              }else
              if($montoS>=1700001){
                $ap_express=floatval($montoS)*$porExpTres/100;
                $plataforma=floatval($plataforma)*floatval($cant_mensual);
                $plata=floatval($plataforma)*floatval($cant_mensual);
                $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                $cuotaMensual=round(floatval($total)/floatval($cant_mensual));

              }

              if($tipo == 1){
                $cuotaMensualMax = $cuotaMensual;
              }else{
                $cuotaMensualMin = $cuotaMensual;
              }

              //obtener monto1 minimo

              $montoS = $configCOMeses->monto_minimo;
              $cant_mensual = 12;
              $tipo = 0;


              $plataforma=floatval($montoS)*($porPlat/100);
              $cuotas=$cant_mensual;
              $monto=floatval($montoS);
              $t_interes = $monto *( ($tasa * pow(1 + $tasa, $cuotas)) / (pow(1 + $tasa, $cuotas) - 1) );

              //Amortizacion
              $valor_de_cuota = $t_interes;
              $saldo_al_capital = floatval($montoS);
              $items = array();
              $sum = 0;


              for ($i=0; $i< $cant_mensual; $i++) {
                  $interesos = $saldo_al_capital * $tasa;
                  $abono_al_capital = $valor_de_cuota - $interesos;
                  $saldo_al_capital = $saldo_al_capital - $abono_al_capital;
                  $numero = $i + 1;
                  $item = array($numero, $interesos, $abono_al_capital, $valor_de_cuota, round($saldo_al_capital, 2));
                  array_push($items, $item);
                  $sum = $sum + floatval($interesos);
              }

              $taxInTotal = $sum;
              $presubtotal = floatval($montoS)+floatval($taxInTotal);
              $subtotal = round($presubtotal);

              if(floatval($montoS)<=1200000){

                  $ap_express=floatval($montoS)*$porExpUno/100;

                  $plataforma=floatval($plataforma)*floatval($cant_mensual);
                  $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                  $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                  $cuotaMensual=round(floatval($total)/floatval($cant_mensual));
                }else
                if($montoS>1200000 && $montoS<=1700000){
                  $ap_express=floatval($montoS)*$porExpDos/100;


                  $plataforma=floatval($plataforma)*floatval($cant_mensual);
                  $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                  $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                  $cuotaMensual=round(floatval($total)/floatval($cant_mensual));

                }else
                if($montoS>=1700001){
                  $ap_express=floatval($montoS)*$porExpTres/100;
                  $plataforma=floatval($plataforma)*floatval($cant_mensual);
                  $plata=floatval($plataforma)*floatval($cant_mensual);
                  $iva=(floatval($plataforma)+floatval($ap_express))*$porIva/100;
                  $total=floatval($subtotal)+floatval($plataforma)+floatval($ap_express)+floatval($iva);
                  $cuotaMensual=round(floatval($total)/floatval($cant_mensual));

                }

                if($tipo == 1){
                  $cuotaMensualMax = $cuotaMensual;
                }else{
                  $cuotaMensualMin = $cuotaMensual;
                }


        //obtener monto2 maximo

        $montoSolicitadoDias = $configCODias->monto_maximo;
        $plazo = 15;
        $tipo = 1;

        if($porplataformaDias > 0){
            $porPlat = $porplataformaDias;
        }else{
            $porPlat = 1000;
        }

        if($porIvaDias > 0){
            $porIva = $porIvaDias;
        }else{
            $porIva = 19;
        }

        if($tasaDias > 0){
            $tasa = $tasaDias;
        }else{
            $tasa = 14;
        }

        if($porExpressDias > 0){
            $porExp = $porExpressDias;
        }else{
            $porExp = 12.5;
        }

        $plazoDias=$plazo;

        $tasitaNueva=(pow((1+($tasa/100)),($plazoDias/360))-1);
        $t_interesDias=$tasitaNueva*$montoSolicitadoDias;

        $subtotalDias=floatval($montoSolicitadoDias)+floatval($t_interesDias);

        $plataformaDias=$porPlat*floatval($plazoDias);
        $ap_expressDias=floatval($montoSolicitadoDias)*$porExp/100;
        $ivaDias=(floatval($plataformaDias)+floatval($ap_expressDias))*$porIva/100;
        $totalDias=floatval($subtotalDias)+floatval($plataformaDias)+floatval($ap_expressDias)+floatval($ivaDias);

        if($tipo==1){
          $totalDiasMax = round($totalDias);
        }else{
          $totalDiasMin = $totalDias;
        }

        //obtener monto2 minimo

        $montoSolicitadoDias = $configCODias->monto_minimo;
        $plazo = 15;
        $tipo = 0;

        $plazoDias=$plazo;

        $tasitaNueva=(pow((1+($tasa/100)),($plazoDias/360))-1);
        $t_interesDias=$tasitaNueva*$montoSolicitadoDias;

        $subtotalDias=floatval($montoSolicitadoDias)+floatval($t_interesDias);

        $plataformaDias=$porPlat*floatval($plazoDias);
        $ap_expressDias=floatval($montoSolicitadoDias)*$porExp/100;
        $ivaDias=(floatval($plataformaDias)+floatval($ap_expressDias))*$porIva/100;
        $totalDias=floatval($subtotalDias)+floatval($plataformaDias)+floatval($ap_expressDias)+floatval($ivaDias);

        if($tipo==1){
          $totalDiasMax = round($totalDias);
        }else{
          $totalDiasMin =  round($totalDias);
        }

        if($gastoMonetario->diponibleEndeudamiento >= $totalDiasMin){
            $gastoMonetario->balanceCajaDiasMinimo = 1;
          }else{
            $gastoMonetario->balanceCajaDiasMinimo = 0;
          }

          if($gastoMonetario->diponibleEndeudamiento >= $totalDiasMax){
            $gastoMonetario->balanceCajaDiasMaximo = 1;
          }else{
            $gastoMonetario->balanceCajaDiasMaximo = 0;
          }

          if($gastoMonetario->diponibleEndeudamiento >= $cuotaMensualMin){
            $gastoMonetario->balanceCajaMesesMinimo = 1;
          }else{
            $gastoMonetario->balanceCajaMesesMinimo = 0;
          }

          if($gastoMonetario->diponibleEndeudamiento >= $cuotaMensualMax){
            $gastoMonetario->balanceCajaMesesMaximo = 1;
          }else{
            $gastoMonetario->balanceCajaMesesMaximo = 0;
          }

          if($gastoMonetario->balanceCajaMesesMaximo==1 || $gastoMonetario->balanceCajaMesesMinimo==1 || $gastoMonetario->balanceCajaDiasMaximo==1 || $gastoMonetario->balanceCajaDiasMinimo==1){
            //$estatus_solicitud ='';
            $resultado = true;
          }else{
            // $estatus_solicitud ='negado';
            $resultado = false;
          }
            $calculoGastosString = get_object_vars($gastoMonetario);
            $calculoGastosString = json_encode($calculoGastosString);

          $res = [
              'calculoGastosString' => $calculoGastosString,
              'calculoString'=> $calculoString,
              'resultado' => $resultado

          ];
          return $res;
    }

    public function controlMail(Request $request){
        //    public function ControlMail(Request $request){
             try {

               $user = User::find($request->idUser);
               $evaluacion=Evaluacion::where("idSolicitudFk", $request->idSolicitud)
               ->where('idUserFk',$request->idUser)->first();
                $email=$user->email;


                //aca hay que modificar con la apikey qur contraten, esta puesta una creada free, la free hace una consulta por segundo
                $api_key='ceec0fee17c74c928d40cb211fcf3ebb';
                $AControlar='https://emailvalidation.abstractapi.com/v1?api_key='.$api_key.'&email='.$email;
              //$AControlar='https://emailvalidation.abstractapi.com/v1?api_key=9ce7fd0d85784993bc45bacaef24ce56&email=fernandoezequielnavarro@gmail.com';

             // Initialize cURL.
                    $ch = curl_init();
                    // Check if initialization had gone wrong*
                if ($ch === false) {
                    Notificaciones::create([
                        'idSolicitudFk' => $request->idSolicitud,
                        'idUserFk' =>$request->idUser,
                        'idEvaluacionFk' => $evaluacion->id,
                        'titulo'=>"Error analisis de email",
                        'mensaje' => "Error de conexion con verificaion de email",
                        'codigo' => '001'
                    ]);
                    \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'email'=> 'pendiente'
                        ]);
                    curl_close($ch);
                    self::enviarNotificacion(['titulo'=>"Error analisis de email",
                    'mensaje' => "Error de conexion con verificaion de email, Codigo:001, Email:".$email]);
                    \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'email'=> 'pendiente manual'
                        ]);
                    return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
                }

                    // Set the URL that you want to GET by using the CURLOPT_URL option.
                    curl_setopt($ch, CURLOPT_URL,$AControlar);

                    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                    //para probar de manera local, el sitio tiene control de ssl
                      //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                       // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    // Execute the request.
                    $data = curl_exec($ch);
                    if(curl_errno($ch)){
                        Notificaciones::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUserFk' =>$request->idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de email",
                            'mensaje' => "Error de conexion con verificaion de email",
                            'codigo' => '001'
                        ]);
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'email'=> 'pendiente'
                        ]);
                        $e = curl_errno($ch);
                        curl_close($ch);
                        self::enviarNotificacion(['titulo'=>"Error analisis de email",
                    'mensaje' => "Error de conexion con verificaion de email, Codigo:001, Email:".$email]);

                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'email'=> 'pendiente manual'
                        ]);
                        return response()->json(['estatus'=>'error_conexion','error'=>$e],201);
                        // return ([ 'pendiente','Curl error: ' . curl_error($ch)]);
                    }

                    // Close the cURL handle.
                    curl_close($ch);

                    // Print the data out onto the page.
                    //echo $data;
                   $data=json_decode($data);
                   $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$request->idUser)->find($request->idSolicitud);
                    //return response()->json($data);
                    if(($data->deliverability)!="DELIVERABLE"){
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'negado',
                            'email'=> 'negado',
                            'resultadoEmail' => json_encode($data)
                        ]);
                        $solicitud->estatus = "negado";
                        $solicitud->save();
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
                         $name=$usuarios->first_name;
                         $last_name=$usuarios->last_name;
                         $email=$usuarios->email;
                         $cedula=$usuarios->n_document;
                         $content=$contenido->contenido;
                         $contentInvitacion=$contenido_invitacion->contenido;


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
                     }
                        return response()->json(['estatus' => 'negado','error'=>null],201);

                        // return ['negado',$data]; //email invalido
                    }else{
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente',
                            'email'=> 'aprobado',
                            'resultadoEmail' => json_encode($data)
                        ]);
                        return response()->json(['estatus' => 'aprobado','error'=>null],201);
                        // return ['aprobado',$data];//email valido
                    }

                // return response()->json($res,201);
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

        function controlMailInterno($idUser, $idSolicitud){
            //    public function ControlMail(Request $request){
                 try {
    
                   $user = User::find($idUser);
                   $evaluacion=Evaluacion::where("idSolicitudFk", $idSolicitud)
                   ->where('idUserFk',$idUser)->first();
                    $email=$user->email;
    
    
                    //aca hay que modificar con la apikey qur contraten, esta puesta una creada free, la free hace una consulta por segundo
                    $api_key='ceec0fee17c74c928d40cb211fcf3ebb';
                    $AControlar='https://emailvalidation.abstractapi.com/v1?api_key='.$api_key.'&email='.$email;
                  //$AControlar='https://emailvalidation.abstractapi.com/v1?api_key=9ce7fd0d85784993bc45bacaef24ce56&email=fernandoezequielnavarro@gmail.com';
    
                 // Initialize cURL.
                        $ch = curl_init();
                        // Check if initialization had gone wrong*
                    if ($ch === false) {
                        Notificaciones::create([
                            'idSolicitudFk' => $idSolicitud,
                            'idUserFk' =>$idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de email",
                            'mensaje' => "Error de conexion con verificaion de email",
                            'codigo' => '001'
                        ]);
                        \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente manual',
                                'email'=> 'pendiente'
                            ]);
                        curl_close($ch);
                        self::enviarNotificacion(['titulo'=>"Error analisis de email",
                        'mensaje' => "Error de conexion con verificaion de email, Codigo:001, Email:".$email]);
                        \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente manual',
                                'email'=> 'pendiente manual'
                            ]);
                        return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
                    }
    
                        // Set the URL that you want to GET by using the CURLOPT_URL option.
                        curl_setopt($ch, CURLOPT_URL,$AControlar);
    
                        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
                        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
                        //para probar de manera local, el sitio tiene control de ssl
                          //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                           // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
                        // Execute the request.
                        $data = curl_exec($ch);
                        if(curl_errno($ch)){
                            Notificaciones::create([
                                'idSolicitudFk' => $idSolicitud,
                                'idUserFk' =>$idUser,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de email",
                                'mensaje' => "Error de conexion con verificaion de email",
                                'codigo' => '001'
                            ]);
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente manual',
                                'email'=> 'pendiente'
                            ]);
                            $e = curl_errno($ch);
                            curl_close($ch);
                            self::enviarNotificacion(['titulo'=>"Error analisis de email",
                        'mensaje' => "Error de conexion con verificaion de email, Codigo:001, Email:".$email]);
    
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente manual',
                                'email'=> 'pendiente manual'
                            ]);
                            return ['estatus'=>'error_conexion','error'=>$e];
                            // return ([ 'pendiente','Curl error: ' . curl_error($ch)]);
                        }
    
                        // Close the cURL handle.
                        curl_close($ch);
    
                        // Print the data out onto the page.
                        //echo $data;
                       $data=json_decode($data);
                       $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$idUser)->find($idSolicitud);
                        //return response()->json($data);
                        if(($data->deliverability)!="DELIVERABLE"){
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'negado',
                                'email'=> 'negado',
                                'resultadoEmail' => json_encode($data)
                            ]);
                            $solicitud->estatus = "negado";
                            $solicitud->save();
                            $usuarios=User::where('id',$idUser)->first();
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
                             $name=$usuarios->first_name;
                             $last_name=$usuarios->last_name;
                             $email=$usuarios->email;
                             $cedula=$usuarios->n_document;
                             $content=$contenido->contenido;
                             $contentInvitacion=$contenido_invitacion->contenido;
    
    
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
                         }
                            return ['estatus' => 'negado','error'=>null];
    
                            // return ['negado',$data]; //email invalido
                        }else{
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente',
                                'email'=> 'aprobado',
                                'resultadoEmail' => json_encode($data)
                            ]);
                            return ['estatus' => 'aprobado','error'=>null];
                            // return ['aprobado',$data];//email valido
                        }
    
                    // return response()->json($res,201);
                }catch (\Exception $e) {
                    // if($e instanceof ValidationException) {
                    //     return response()->json($e->errors(),402);
                    // }
                    DB::rollback(); // Retrocedemos la transaccion
                    Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                    return [
                        'estatus' => 'error_interno',
                        "error"=>true,
                        'message' => 'Ha ocurrido un error inesperado.',
                    ];
                }
    
            }

        public function analisisWebScrapping(Request $request){
            //    public function ControlMail(Request $request){

                set_time_limit(120);
                 try {

                   $user = User::find($request->idUser);
                   $evaluacion=Evaluacion::where("idSolicitudFk", $request->idSolicitud)
               ->where('idUserFk',$request->idUser)->first();
                    $document=$user->n_document;

                    $AControlar='http://16.214.194.249:3000/info/'.$document;
                    // $AControlar='http://162.214.194.249:3000/info/12325349';
                  //$AControlar='https://emailvalidation.abstractapi.com/v1?api_key=9ce7fd0d85784993bc45bacaef24ce56&email=fernandoezequielnavarro@gmail.com';

                 // Initialize cURL.
                        $ch = curl_init();
                        // Check if initialization had gone wrong*
                    if ($ch === false) {
                        Notificaciones::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUserFk' =>$request->idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de web scrapping",
                            'mensaje' => "Error de conexion con web scrapping",
                            'codigo' => '002'
                        ]);
                        curl_close($ch);
                        self::enviarNotificacion(['titulo'=>"Error analisis de web scrapping",
                    'mensaje' => "Error de conexion con web scrapping, Codigo:002"]);
                        return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
                    }

                        // Set the URL that you want to GET by using the CURLOPT_URL option.
                        curl_setopt($ch, CURLOPT_URL,$AControlar);

                        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                        //para probar de manera local, el sitio tiene control de ssl
                          //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                           // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                        // Execute the request.
                        $data = curl_exec($ch);
                        if(curl_errno($ch)){
                            Notificaciones::create([
                                'idSolicitudFk' => $request->idSolicitud,
                                'idUserFk' =>$request->idUser,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de web scrapping",
                                'mensaje' => "Error de conexion con web scrapping",
                                'codigo' => '002'
                            ]);
                            $e = curl_errno($ch);
                            curl_close($ch);
                            self::enviarNotificacion(['titulo'=>"Error analisis de web scrapping",
                        'mensaje' => "Error de conexion con web scrapping, Codigo:002"]);
                            return response()->json(['estatus'=>'error_conexion','error'=>$e],201);
                            // return ([ 'pendiente','Curl error: ' . curl_error($ch)]);
                        }

                        // Close the cURL handle.
                        curl_close($ch);

                        // Print the data out onto the page.
                        //echo $data;
                        Log::error('scrapping  '.json_encode($data));
                       $data=json_decode($data);
                       $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$request->idUser)->find($request->idSolicitud);
                        //return response()->json($data);
                        if($data && array_key_exists('statusCode',$data)){
                            Notificaciones::create([
                                'idSolicitudFk' => $request->idSolicitud,
                                'idUserFk' =>$request->idUser,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de web scrapping",
                                'mensaje' => $data->message,
                                'codigo' => '002'
                            ]);
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                            ->where('evaluacions.idUserFk',$request->idUser)
                            ->update([
                                'resultadoScrapping' => json_encode($data)
                            ]);
                            return response()->json(['estatus'=>'error_conexion','error'=>$data->message],201);
                        }else if($data && $data->statusCredito!="Aprobado"){
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                            ->where('evaluacions.idUserFk',$request->idUser)
                            ->update([
                                'estatus'    => 'negado',
                                'scrapping'=> 'negado',
                                'resultadoScrapping' => json_encode($data)
                            ]);
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
                             $name=$usuarios->first_name;
                             $last_name=$usuarios->last_name;
                             $email=$usuarios->email;
                             $cedula=$usuarios->n_document;
                             $content=$contenido->contenido;
                             $contentInvitacion=$contenido_invitacion->contenido;


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
                             /**fin de correo denegado */
                         }
                            return response()->json(['estatus' => 'negado','error'=>null],201);

                            // return ['negado',$data]; //email invalido
                        }else{
                            $usuario=User::where('id',$request->idUser)->first();
                            $tokenResult = $usuario->createToken('Analizer Access Token');
                            $token = $tokenResult->token;
                            // $token->expires_at = Carbon::now()->addMinutes(30);
                            $token->save();
                            $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
                            $evaluacion->tokenAnalizer=$tokenResult->accessToken;
                            $usuario->tokenAnalizer=$tokenResult->accessToken;
                            $usuario->save();
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                            ->where('evaluacions.idUserFk',$request->idUser)
                            ->update([
                                'estatus'    => 'pendiente',
                                'scrapping'=> 'aprobado',
                                'tokenAnalizer'=>$tokenResult->accessToken,
                                'resultadoScrapping' => json_encode($data)
                            ]);
                            return response()->json(
                                [
                                    'tokenAnalizer'=>$tokenResult->accessToken,
                                    'token_type'    => 'Bearer',
                                    'expires_at'    => $expires_at,
                                    'estatus' => 'aprobado',
                                    'error'=>null
                                ],201);
                            // return ['aprobado',$data];//email valido
                        }

                    // return response()->json($res,201);
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
        public function analisisWebScrappingInterno($idUser, $idSolicitud){
            //    public function ControlMail(Request $request){

                set_time_limit(120);
                 try {

                   $user = User::find($idUser);
                   $evaluacion=Evaluacion::where("idSolicitudFk", $idSolicitud)
               ->where('idUserFk',$idUser)->first();
                    $document=$user->n_document;

                    $AControlar='http://162.214.194.249:3000/info/'.$document;
                    // $AControlar='http://162.214.194.249:3000/info/12325349';
                  //$AControlar='https://emailvalidation.abstractapi.com/v1?api_key=9ce7fd0d85784993bc45bacaef24ce56&email=fernandoezequielnavarro@gmail.com';

                 // Initialize cURL.
                        $ch = curl_init();
                        // Check if initialization had gone wrong*
                    if ($ch === false) {
                        Notificaciones::create([
                            'idSolicitudFk' => $idSolicitud,
                            'idUserFk' =>$idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de web scrapping",
                            'mensaje' => "Error de conexion con web scrapping",
                            'codigo' => '002'
                        ]);
                        curl_close($ch);
                        \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'resultadoScrapping' => "Error de conexion con web scrapping, Codigo:002"
                            ]);
                        self::enviarNotificacion(['titulo'=>"Error analisis de web scrapping",
                    'mensaje' => "Error de conexion con web scrapping, Codigo:002"]);
                        return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
                    }

                        // Set the URL that you want to GET by using the CURLOPT_URL option.
                        curl_setopt($ch, CURLOPT_URL,$AControlar);

                        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                        //para probar de manera local, el sitio tiene control de ssl
                          //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                           // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                        // Execute the request.
                        $data = curl_exec($ch);
                        if(curl_errno($ch)){
                            Notificaciones::create([
                                'idSolicitudFk' => $idSolicitud,
                                'idUserFk' =>$idUser,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de web scrapping",
                                'mensaje' => "Error de conexion con web scrapping",
                                'codigo' => '002'
                            ]);
                            $e = curl_errno($ch);
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'resultadoScrapping' => "Error de conexion con web scrapping, Codigo:002".$e
                            ]);
                            curl_close($ch);
                            self::enviarNotificacion(['titulo'=>"Error analisis de web scrapping",
                        'mensaje' => "Error de conexion con web scrapping, Codigo:002"]);
                            return ['estatus'=>'error_conexion','error'=>$e];
                            // return ([ 'pendiente','Curl error: ' . curl_error($ch)]);
                        }

                        // Close the cURL handle.
                        curl_close($ch);

                        // Print the data out onto the page.
                        //echo $data;
                        Log::error('scrapping  '.json_encode($data));
                       $data=json_decode($data);
                       $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$idUser)->find($idSolicitud);
                        //return response()->json($data);
                        if($data && array_key_exists('statusCode',$data)){
                            Notificaciones::create([
                                'idSolicitudFk' => $idSolicitud,
                                'idUserFk' =>$idUser,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de web scrapping",
                                'mensaje' => $data->message,
                                'codigo' => '002'
                            ]);
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'resultadoScrapping' => json_encode($data)
                            ]);
                            return ['estatus'=>'error_conexion','error'=>$data->message];
                        }else if($data && $data->statusCredito!="Aprobado"){
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'negado',
                                'scrapping'=> 'negado',
                                'resultadoScrapping' => json_encode($data)
                            ]);
                            $solicitud->estatus = "negado";
                            $solicitud->save();
                            /**Correo denegado */
                            $usuarios=User::where('id',$idUser)->first();
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
                             $name=$usuarios->first_name;
                             $last_name=$usuarios->last_name;
                             $email=$usuarios->email;
                             $cedula=$usuarios->n_document;
                             $content=$contenido->contenido;
                             $contentInvitacion=$contenido_invitacion->contenido;


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
                             /**fin de correo denegado */
                         }
                            return ['estatus' => 'negado','error'=>null];

                            // return ['negado',$data]; //email invalido
                        }else{
                            $usuario=User::where('id',$idUser)->first();
                            $tokenResult = $usuario->createToken('Analizer Access Token');
                            $token = $tokenResult->token;
                            // $token->expires_at = Carbon::now()->addMinutes(30);
                            $token->save();
                            $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
                            $evaluacion->tokenAnalizer=$tokenResult->accessToken;
                            $usuario->tokenAnalizer=$tokenResult->accessToken;
                            $usuario->save();
                            \DB::table('evaluacions')
                            ->where("evaluacions.idSolicitudFk", $idSolicitud)
                            ->where('evaluacions.idUserFk',$idUser)
                            ->update([
                                'estatus'    => 'pendiente',
                                'scrapping'=> 'aprobado',
                                'tokenAnalizer'=>$tokenResult->accessToken,
                                'resultadoScrapping' => json_encode($data)
                            ]);
                            return
                                [
                                    'tokenAnalizer'=>$tokenResult->accessToken,
                                    'token_type'    => 'Bearer',
                                    'expires_at'    => $expires_at,
                                    'estatus' => 'aprobado',
                                    'error'=>null
                                ];
                            // return ['aprobado',$data];//email valido
                        }

                    // return response()->json($res,201);
                }catch (\Exception $e) {
                    // if($e instanceof ValidationException) {
                    //     return response()->json($e->errors(),402);
                    // }
                    DB::rollback(); // Retrocedemos la transaccion
                    Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                    return [
                        'estatus' => 'error_interno',
                        "error"=>true,
                        'message' => 'Ha ocurrido un error inesperado.',
                    ];
                }

        }
        public function analisisKonivin(Request $request){

            set_time_limit(120);
            $BDUA = null;
            $OFAC = null;
            $estadoCedula = null;
            $antecedentes = null;
            $status = "negado";

            $usuario=User::findOrFail($request->idUser);
            $basica=Basica::where('idUserFk',$usuario->id)->first();
            $evaluacion = Evaluacion::where("idSolicitudFk", $request->idSolicitud)
                ->where('idUserFk', $usuario->id)
                ->first();


                // $usuario=User::where('id',$request->idUser)->first();
                // $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                // $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
                // $usuario->tokenAnalizer=$token;
                // $usuario->save();

            // BDUA
            $curl = curl_init();
            if ($curl === false) {
                Notificaciones::create([
                    'idSolicitudFk' => $request->idSolicitud,
                    'idUserFk' =>$request->idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Error de conexion con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Error de conexion con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
            }
            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST =>false
            ));
            // Set the URL that you want to GET by using the CURLOPT_URL option.
            // http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=PASSWORD&jor=23948865&icf=01&thy=CO&klm=ND1098XX
            Log::error("http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document);
            // curl_setopt($curl, CURLOPT_URL,"https://produccion.konivin.com:28183/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document."");

            // // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
            // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            $BDUA = curl_exec($curl);
            if(curl_errno($curl)){
                Notificaciones::create([
                    'idSolicitudFk' => $request->idSolicitud,
                    'idUserFk' =>$request->idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Error de conexion con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                $e = curl_error($curl);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Error de conexion con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return response()->json(['estatus'=>'error_conexion_no','error'=>$e],201);

            }
            if($BDUA == false){
                Notificaciones::create([
                    'idSolicitudFk' => $request->idSolicitud,
                    'idUserFk' =>$request->idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Respuesta falsa con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Respuesta falsa con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return response()->json(['estatus'=>'error_false','error'=>"BDUA false","token"=>$tokenResult->accessToken],201);

            }else{
             $BDUA_DECODE = json_decode($BDUA);
            }


            curl_close($curl);
            // FIN BDUA

            /**
             * Validar que la respuesta sea json
            */
            if(!is_null($BDUA_DECODE)){
                /**
                 * Validar si el reultado del BDUA cumple con la informacion requerida
                */
                if($BDUA_DECODE->estado === "ACTIVO" && $BDUA_DECODE->tipoAfiliado === "COTIZANTE" && $BDUA_DECODE->regimen === "CONTRIBUTIVO"){
                    //OFAC
                    $curl = curl_init();
                    if ($curl === false) {
                        Notificaciones::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUserFk' =>$request->idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Error de conexion con Verifiquese OFAC",
                            'codigo' => '003'
                        ]);
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Error de conexion con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
                    }
                    // curl_setopt_array($curl, array(
                    // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=28600607&icf=01&thy=CO&klm=".$request->nCedula."",
                    // CURLOPT_RETURNTRANSFER => true,
                    // CURLOPT_ENCODING => "",
                    // CURLOPT_MAXREDIRS => 10,
                    // CURLOPT_TIMEOUT => 0,
                    // CURLOPT_FOLLOWLOCATION => true,
                    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    // CURLOPT_CUSTOMREQUEST => "GET"
                    // ));

                    // Set the URL that you want to GET by using the CURLOPT_URL option.
                    curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=28600607&icf=01&thy=CO&klm=".$usuario->n_document."");

                    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $OFAC = curl_exec($curl);
                    if(curl_errno($curl)){
                        Notificaciones::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUserFk' =>$request->idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Error de conexion con Verifiquese OFAC",
                            'codigo' => '002'
                        ]);
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                        ->where('evaluacions.idUserFk',$request->idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                        $e = curl_errno($curl);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Error de conexion con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return response()->json(['estatus'=>'error_conexion','error'=>$e],201);

                    }
                    if($OFAC == false){
                        Notificaciones::create([
                            'idSolicitudFk' => $request->idSolicitud,
                            'idUserFk' =>$request->idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Respuesta falsa con Verifiquese OFAC",
                            'codigo' => '003'
                        ]);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Respuesta falsa con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return response()->json(['estatus'=>'error_false','error'=>"OFAC false"],201);

                    }else{
                        $OFAC_DECODE = json_decode($OFAC);
                    }

                    curl_close($curl);
                    // FIN OFAC

                    /**
                     * Validar que la respuesta sea json
                     */
                    if(!is_null($OFAC_DECODE)){
                        /**
                         * VALIDAR SI TIENE REGISTRO EN LA OFAC
                         * DE SER ASI NO PERMITIR LA SOLICITUD
                         */
                        if($OFAC_DECODE->tieneRegistro === "NO"){
                            //ESTADO CEDULA
                            $curl = curl_init();
                            if ($curl === false) {
                                Notificaciones::create([
                                    'idSolicitudFk' => $request->idSolicitud,
                                    'idUserFk' =>$request->idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Error de conexion con Verifiquese estado de cedula",
                                    'codigo' => '003'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                ->where('evaluacions.idUserFk',$request->idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Error de conexion con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
                            }
                            // curl_setopt_array($curl, array(
                            // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=91891024&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
                            // CURLOPT_RETURNTRANSFER => true,
                            // CURLOPT_ENCODING => "",
                            // CURLOPT_MAXREDIRS => 10,
                            // CURLOPT_TIMEOUT => 0,
                            // CURLOPT_FOLLOWLOCATION => true,
                            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            // CURLOPT_CUSTOMREQUEST => "GET"
                            // ));
                            // Set the URL that you want to GET by using the CURLOPT_URL option.
                            curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=91891024&icf=01&thy=CO&klm=".$usuario->n_document."&hgu=".$basica->fechaExpedicionCedula."");

                            // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                            // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                            $estadoCedula = curl_exec($curl);
                            if(curl_errno($curl)){
                                Notificaciones::create([
                                    'idSolicitudFk' => $request->idSolicitud,
                                    'idUserFk' =>$request->idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Error de conexion con Verifiquese estado de cedula",
                                    'codigo' => '002'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                ->where('evaluacions.idUserFk',$request->idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                $e = curl_errno($curl);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Error de conexion con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return response()->json(['estatus'=>'error_conexion','error'=>$e],201);

                            }
                            if($estadoCedula == false){
                                Notificaciones::create([
                                    'idSolicitudFk' => $request->idSolicitud,
                                    'idUserFk' =>$request->idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Respuesta falsa con Verifiquese estado de cedula",
                                    'codigo' => '003'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                ->where('evaluacions.idUserFk',$request->idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Respuesta falsa con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return response()->json(['estatus'=>'error_false','error'=>"estado de cedula false"],201);

                            }else{
                                $CEDULA_DECODE = json_decode($estadoCedula);
                            }
                            curl_close($curl);
                            // FIN ESTADO CEDULA

                            /**
                             * validar que la respuesta sea json
                             */
                            if(!is_null($CEDULA_DECODE)){
                                /**
                                 * VERIFICAR QUE LA CEDULA ESTE VIGENTE
                                 */
                                if($CEDULA_DECODE->estado === "VIGENTE"){

                                    //ANTECEDENTES POLICIALES
                                    $curl = curl_init();
                                    if ($curl === false) {
                                        Notificaciones::create([
                                            'idSolicitudFk' => $request->idSolicitud,
                                            'idUserFk' =>$request->idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Error de conexion con Verifiquese antecedentes policiales",
                                            'codigo' => '003'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                        ->where('evaluacions.idUserFk',$request->idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Error de conexion con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return response()->json(['estatus'=>'error_conexion','error'=>'failed to initialize'],201);
                                    }
                                    // curl_setopt_array($curl, array(
                                    // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=42156544&icf=01&thy=CO&klm=".$request->nCedula."",
                                    // CURLOPT_RETURNTRANSFER => true,
                                    // CURLOPT_ENCODING => "",
                                    // CURLOPT_MAXREDIRS => 10,
                                    // CURLOPT_TIMEOUT => 0,
                                    // CURLOPT_FOLLOWLOCATION => true,
                                    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    // CURLOPT_CUSTOMREQUEST => "GET"
                                    // ));
                                    // Set the URL that you want to GET by using the CURLOPT_URL option.
                                    curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=42156544&icf=01&thy=CO&klm=".$usuario->n_document."");

                                    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                                    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                                    $antecedentes = curl_exec($curl);
                                    if(curl_errno($curl)){
                                        Notificaciones::create([
                                            'idSolicitudFk' => $request->idSolicitud,
                                            'idUserFk' =>$request->idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Error de conexion con Verifiquese antecedentes policiales",
                                            'codigo' => '002'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                        ->where('evaluacions.idUserFk',$request->idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        $e = curl_errno($curl);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Error de conexion con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return response()->json(['estatus'=>'error_conexion','error'=>$e],201);

                                    }
                                    if($antecedentes == false){
                                        Notificaciones::create([
                                            'idSolicitudFk' => $request->idSolicitud,
                                            'idUserFk' =>$request->idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Respuesta falsa con Verifiquese antecedentes policiales",
                                            'codigo' => '003'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $request->idSolicitud)
                                        ->where('evaluacions.idUserFk',$request->idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Respuesta falsa con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return response()->json(['estatus'=>'error_false','error'=>"antecedentes policiales false"],201);

                                    }else{
                                    $ANTECEDENTES_DECODE = json_decode($antecedentes);
                                    }
                                    curl_close($curl);

                                    /**
                                     * Validar que la respuesta sea json
                                    */
                                    if(!is_null($ANTECEDENTES_DECODE)){
                                        if($ANTECEDENTES_DECODE->antecedentes === "NO TIENE ASUNTOS PENDIENTES CON LAS AUTORIDADES JUDICIALES" || $ANTECEDENTES_DECODE->antecedentes === "" || $ANTECEDENTES_DECODE->antecedentes === null){
                                            $status = "aprobado";
                                        }
                                    }
                                    // FIN ANTECEDENTES POLICIALES
                                }
                            }
                        }
                    }
                }
            }


            $usuario=User::where('id',$request->idUser)->first();
            // $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            // $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
            // $usuario->tokenAnalizer=$token;
            // $usuario->save();
            // $evaluacion->tokenAnalizer=$token;
            if($status =="aprobado"){
                $tokenResult = $usuario->createToken('Analizer Access Token');
                $token = $tokenResult->token;
                // $token->expires_at = Carbon::now()->addMinutes(30);
                $token->save();
                $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
                $res = [
                    'result' => [
                        "bdua"=>$BDUA,
                        "ofac" => $OFAC,
                        "estadoCedula" => $estadoCedula,
                        "antecedentes" => $antecedentes,
                        "estatus" => $status,
                        'tokenAnalizer'=>$tokenResult->accessToken,
                        'token_type'    => 'Bearer',
                        'expires_at'    => $expires_at,
                    ],
                    'msj' => 'Hay registro.',
                ];
                $usuario->tokenAnalizer=$tokenResult->accessToken;
                $usuario->save();
                $evaluacion->tokenAnalizer=$tokenResult->accessToken;
                $evaluacion->informacion_identidad = json_encode($res);
                $evaluacion->verifiquese = $status;
                $evaluacion->save();

            }else{
                $res = [
                    'result' => [
                        "bdua"=>$BDUA,
                        "ofac" => $OFAC,
                        "estadoCedula" => $estadoCedula,
                        "antecedentes" => $antecedentes,
                        "estatus" => $status,
                        'tokenAnalizer'=>null,
                        'token_type'    => null,
                        'expires_at'    => null,
                    ],
                    'msj' => 'Hay registro.',
                ];
                $evaluacion->informacion_identidad = json_encode($res);
                $evaluacion->verifiquese = $status;
                $evaluacion->save();

                $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$request->idUser)->find($request->idSolicitud);
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
                    $name=$usuarios->first_name;
                    $last_name=$usuarios->last_name;
                    $email=$usuarios->email;
                    $cedula=$usuarios->n_document;
                    $content=$contenido->contenido;
                    $contentInvitacion=$contenido_invitacion->contenido;


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
                    /**fin de correo denegado */
                }
            }

            return response()->json($res, 200);
        }

        public function analisisKonivinInterno($idUser, $idSolicitud){

            set_time_limit(120);
            try{
            $BDUA = null;
            $OFAC = null;
            $estadoCedula = null;
            $antecedentes = null;
            $status = "negado";

            $usuario=User::findOrFail($idUser);
            $basica=Basica::where('idUserFk',$usuario->id)->first();
            $evaluacion = Evaluacion::where("idSolicitudFk", $idSolicitud)
                ->where('idUserFk', $usuario->id)
                ->first();


                // $usuario=User::where('id',$request->idUser)->first();
                // $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                // $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
                // $usuario->tokenAnalizer=$token;
                // $usuario->save();

            // BDUA
            $curl = curl_init();
            if ($curl === false) {
                Notificaciones::create([
                    'idSolicitudFk' => $idSolicitud,
                    'idUserFk' =>$idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Error de conexion con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                        ->where('evaluacions.idUserFk',$idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Error de conexion con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
            }
            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST =>false
            ));
            // Set the URL that you want to GET by using the CURLOPT_URL option.
            // http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=PASSWORD&jor=23948865&icf=01&thy=CO&klm=ND1098XX
            Log::error("http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document);
            // curl_setopt($curl, CURLOPT_URL,"https://produccion.konivin.com:28183/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=23948865&icf=01&thy=CO&klm=".$usuario->n_document."");

            // // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
            // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            $BDUA = curl_exec($curl);
            if(curl_errno($curl)){
                Notificaciones::create([
                    'idSolicitudFk' => $idSolicitud,
                    'idUserFk' =>$idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Error de conexion con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                        ->where('evaluacions.idUserFk',$idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                $e = curl_error($curl);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Error de conexion con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return ['estatus'=>'error_conexion_no','error'=>$e];

            }
            if($BDUA == false){
                Notificaciones::create([
                    'idSolicitudFk' => $idSolicitud,
                    'idUserFk' =>$idUser,
                    'idEvaluacionFk' => $evaluacion->id,
                    'titulo'=>"Error analisis de Verifiquese BDUA",
                    'mensaje' => "Respuesta falsa con Verifiquese BDUA",
                    'codigo' => '003'
                ]);
                \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                        ->where('evaluacions.idUserFk',$idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                curl_close($curl);
                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese BDUA",
            'mensaje' => "Respuesta falsa con Verifiquese BDUA, Codigo:003"]);
                $evaluacion->verifiquese = 'pendiente manual';
                $evaluacion->save();
                return ['estatus'=>'error_false','error'=>"BDUA false"];

            }else{
             $BDUA_DECODE = json_decode($BDUA);
            }


            curl_close($curl);
            // FIN BDUA

            /**
             * Validar que la respuesta sea json
            */
            if(!is_null($BDUA_DECODE)){
                /**
                 * Validar si el reultado del BDUA cumple con la informacion requerida
                */
                if($BDUA_DECODE->estado === "ACTIVO" && $BDUA_DECODE->tipoAfiliado === "COTIZANTE" && $BDUA_DECODE->regimen === "CONTRIBUTIVO"){
                    //OFAC
                    $curl = curl_init();
                    if ($curl === false) {
                        Notificaciones::create([
                            'idSolicitudFk' => $idSolicitud,
                            'idUserFk' =>$idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Error de conexion con Verifiquese OFAC",
                            'codigo' => '003'
                        ]);
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                        ->where('evaluacions.idUserFk',$idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Error de conexion con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
                    }
                    // curl_setopt_array($curl, array(
                    // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=28600607&icf=01&thy=CO&klm=".$request->nCedula."",
                    // CURLOPT_RETURNTRANSFER => true,
                    // CURLOPT_ENCODING => "",
                    // CURLOPT_MAXREDIRS => 10,
                    // CURLOPT_TIMEOUT => 0,
                    // CURLOPT_FOLLOWLOCATION => true,
                    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    // CURLOPT_CUSTOMREQUEST => "GET"
                    // ));

                    // Set the URL that you want to GET by using the CURLOPT_URL option.
                    curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=28600607&icf=01&thy=CO&klm=".$usuario->n_document."");

                    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $OFAC = curl_exec($curl);
                    if(curl_errno($curl)){
                        Notificaciones::create([
                            'idSolicitudFk' => $idSolicitud,
                            'idUserFk' =>$idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Error de conexion con Verifiquese OFAC",
                            'codigo' => '002'
                        ]);
                        \DB::table('evaluacions')
                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                        ->where('evaluacions.idUserFk',$idUser)
                        ->update([
                            'estatus'    => 'pendiente manual',
                            'verifiquese'=> 'pendiente'
                        ]);
                        $e = curl_errno($curl);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Error de conexion con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return ['estatus'=>'error_conexion','error'=>$e];

                    }
                    if($OFAC == false){
                        Notificaciones::create([
                            'idSolicitudFk' => $idSolicitud,
                            'idUserFk' =>$idUser,
                            'idEvaluacionFk' => $evaluacion->id,
                            'titulo'=>"Error analisis de Verifiquese OFAC",
                            'mensaje' => "Respuesta falsa con Verifiquese OFAC",
                            'codigo' => '003'
                        ]);
                        curl_close($curl);
                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese OFAC",
                    'mensaje' => "Respuesta falsa con Verifiquese OFAC, Codigo:003"]);
                        $evaluacion->verifiquese = 'pendiente manual';
                        $evaluacion->save();
                        return ['estatus'=>'error_false','error'=>"OFAC false"];

                    }else{
                        $OFAC_DECODE = json_decode($OFAC);
                    }

                    curl_close($curl);
                    // FIN OFAC

                    /**
                     * Validar que la respuesta sea json
                     */
                    if(!is_null($OFAC_DECODE)){
                        /**
                         * VALIDAR SI TIENE REGISTRO EN LA OFAC
                         * DE SER ASI NO PERMITIR LA SOLICITUD
                         */
                        if($OFAC_DECODE->tieneRegistro === "NO"){
                            //ESTADO CEDULA
                            $curl = curl_init();
                            if ($curl === false) {
                                Notificaciones::create([
                                    'idSolicitudFk' => $idSolicitud,
                                    'idUserFk' =>$idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Error de conexion con Verifiquese estado de cedula",
                                    'codigo' => '003'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                ->where('evaluacions.idUserFk',$idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Error de conexion con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
                            }
                            // curl_setopt_array($curl, array(
                            // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=91891024&icf=01&thy=CO&klm=".$request->nCedula."&hgu=".$basica->fechaExpedicionCedula."",
                            // CURLOPT_RETURNTRANSFER => true,
                            // CURLOPT_ENCODING => "",
                            // CURLOPT_MAXREDIRS => 10,
                            // CURLOPT_TIMEOUT => 0,
                            // CURLOPT_FOLLOWLOCATION => true,
                            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            // CURLOPT_CUSTOMREQUEST => "GET"
                            // ));
                            // Set the URL that you want to GET by using the CURLOPT_URL option.
                            curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=91891024&icf=01&thy=CO&klm=".$usuario->n_document."&hgu=".$basica->fechaExpedicionCedula."");

                            // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                            // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                            $estadoCedula = curl_exec($curl);
                            if(curl_errno($curl)){
                                Notificaciones::create([
                                    'idSolicitudFk' => $idSolicitud,
                                    'idUserFk' =>$idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Error de conexion con Verifiquese estado de cedula",
                                    'codigo' => '002'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                ->where('evaluacions.idUserFk',$idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                $e = curl_errno($curl);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Error de conexion con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return ['estatus'=>'error_conexion','error'=>$e];

                            }
                            if($estadoCedula == false){
                                Notificaciones::create([
                                    'idSolicitudFk' => $idSolicitud,
                                    'idUserFk' =>$idUser,
                                    'idEvaluacionFk' => $evaluacion->id,
                                    'titulo'=>"Error analisis de Verifiquese estado de cedula",
                                    'mensaje' => "Respuesta falsa con Verifiquese estado de cedula",
                                    'codigo' => '003'
                                ]);
                                \DB::table('evaluacions')
                                ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                ->where('evaluacions.idUserFk',$idUser)
                                ->update([
                                    'estatus'    => 'pendiente manual',
                                    'verifiquese'=> 'pendiente'
                                ]);
                                curl_close($curl);
                                self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese estado de cedula",
                            'mensaje' => "Respuesta falsa con Verifiquese estado de cedula, Codigo:003"]);
                                $evaluacion->verifiquese = 'pendiente manual';
                                $evaluacion->save();
                                return ['estatus'=>'error_false','error'=>"estado de cedula false"];

                            }else{
                                $CEDULA_DECODE = json_decode($estadoCedula);
                            }
                            curl_close($curl);
                            // FIN ESTADO CEDULA

                            /**
                             * validar que la respuesta sea json
                             */
                            if(!is_null($CEDULA_DECODE)){
                                /**
                                 * VERIFICAR QUE LA CEDULA ESTE VIGENTE
                                 */
                                if($CEDULA_DECODE->estado === "VIGENTE"){

                                    //ANTECEDENTES POLICIALES
                                    $curl = curl_init();
                                    if ($curl === false) {
                                        Notificaciones::create([
                                            'idSolicitudFk' => $idSolicitud,
                                            'idUserFk' =>$idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Error de conexion con Verifiquese antecedentes policiales",
                                            'codigo' => '003'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                        ->where('evaluacions.idUserFk',$idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Error de conexion con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return ['estatus'=>'error_conexion','error'=>'failed to initialize'];
                                    }
                                    // curl_setopt_array($curl, array(
                                    // CURLOPT_URL => "http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=42156544&icf=01&thy=CO&klm=".$request->nCedula."",
                                    // CURLOPT_RETURNTRANSFER => true,
                                    // CURLOPT_ENCODING => "",
                                    // CURLOPT_MAXREDIRS => 10,
                                    // CURLOPT_TIMEOUT => 0,
                                    // CURLOPT_FOLLOWLOCATION => true,
                                    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    // CURLOPT_CUSTOMREQUEST => "GET"
                                    // ));
                                    // Set the URL that you want to GET by using the CURLOPT_URL option.
                                    curl_setopt($curl, CURLOPT_URL,"http://produccion.konivin.com:32564/konivin/servicio/persona/consultar?lcy=creditospanda&vpv=6pv3wr&jor=42156544&icf=01&thy=CO&klm=".$usuario->n_document."");

                                    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                                    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                                    $antecedentes = curl_exec($curl);
                                    if(curl_errno($curl)){
                                        Notificaciones::create([
                                            'idSolicitudFk' => $idSolicitud,
                                            'idUserFk' =>$idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Error de conexion con Verifiquese antecedentes policiales",
                                            'codigo' => '002'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                        ->where('evaluacions.idUserFk',$idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        $e = curl_errno($curl);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Error de conexion con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return ['estatus'=>'error_conexion','error'=>$e];

                                    }
                                    if($antecedentes == false){
                                        Notificaciones::create([
                                            'idSolicitudFk' => $idSolicitud,
                                            'idUserFk' =>$idUser,
                                            'idEvaluacionFk' => $evaluacion->id,
                                            'titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                            'mensaje' => "Respuesta falsa con Verifiquese antecedentes policiales",
                                            'codigo' => '003'
                                        ]);
                                        \DB::table('evaluacions')
                                        ->where("evaluacions.idSolicitudFk", $idSolicitud)
                                        ->where('evaluacions.idUserFk',$idUser)
                                        ->update([
                                            'estatus'    => 'pendiente manual',
                                            'verifiquese'=> 'pendiente'
                                        ]);
                                        curl_close($curl);
                                        self::enviarNotificacion(['titulo'=>"Error analisis de Verifiquese antecedentes policiales",
                                    'mensaje' => "Respuesta falsa con Verifiquese antecedentes policiales, Codigo:003"]);
                                        $evaluacion->verifiquese = 'pendiente manual';
                                        $evaluacion->save();
                                        return ['estatus'=>'error_false','error'=>"antecedentes policiales false"];

                                    }else{
                                    $ANTECEDENTES_DECODE = json_decode($antecedentes);
                                    }
                                    curl_close($curl);

                                    /**
                                     * Validar que la respuesta sea json
                                    */
                                    if(!is_null($ANTECEDENTES_DECODE)){
                                        if($ANTECEDENTES_DECODE->antecedentes === "NO TIENE ASUNTOS PENDIENTES CON LAS AUTORIDADES JUDICIALES" || $ANTECEDENTES_DECODE->antecedentes === "" || $ANTECEDENTES_DECODE->antecedentes === null){
                                            $status = "aprobado";
                                        }
                                    }
                                    // FIN ANTECEDENTES POLICIALES
                                }
                            }
                        }
                    }
                }
            }


            $usuario=User::where('id',$idUser)->first();
            // $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            // $token=substr(str_shuffle(str_repeat($pool, 5)), 0, 32);
            // $usuario->tokenAnalizer=$token;
            // $usuario->save();
            // $evaluacion->tokenAnalizer=$token;
            if($status =="aprobado"){
                $tokenResult = $usuario->createToken('Analizer Access Token');
                $token = $tokenResult->token;
                // $token->expires_at = Carbon::now()->addMinutes(30);
                $token->save();
                $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
                $res = [
                    'result' => [
                        "bdua"=>$BDUA,
                        "ofac" => $OFAC,
                        "estadoCedula" => $estadoCedula,
                        "antecedentes" => $antecedentes,
                        "estatus" => $status,
                        'tokenAnalizer'=>$tokenResult->accessToken,
                        'token_type'    => 'Bearer',
                        'expires_at'    => $expires_at,
                    ],
                    'msj' => 'Hay registro.',
                ];
                $usuario->tokenAnalizer=$tokenResult->accessToken;
                $usuario->save();
                $evaluacion->tokenAnalizer=$tokenResult->accessToken;
                $evaluacion->informacion_identidad = json_encode($res);
                $evaluacion->verifiquese = $status;
                $evaluacion->save();

            }else{
                $res = [
                    'result' => [
                        "bdua"=>$BDUA,
                        "ofac" => $OFAC,
                        "estadoCedula" => $estadoCedula,
                        "antecedentes" => $antecedentes,
                        "estatus" => $status,
                        'tokenAnalizer'=>null,
                        'token_type'    => null,
                        'expires_at'    => null,
                    ],
                    'msj' => 'Hay registro.',
                ];
                $evaluacion->informacion_identidad = json_encode($res);
                $evaluacion->verifiquese = $status;
                $evaluacion->save();

                $solicitud=Calculadora::where('estatus', 'pendiente')->where('idUserFk',$idUser)->find($idSolicitud);
                $solicitud->estatus = "negado";
                $solicitud->save();
                /**Correo denegado */
                $usuarios=User::where('id',$idUser)->first();
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
                    $name=$usuarios->first_name;
                    $last_name=$usuarios->last_name;
                    $email=$usuarios->email;
                    $cedula=$usuarios->n_document;
                    $content=$contenido->contenido;
                    $contentInvitacion=$contenido_invitacion->contenido;


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
                    /**fin de correo denegado */
                }
            }

            return $res;
        }catch (\Exception $e) {
            // if($e instanceof ValidationException) {
            //     return response()->json($e->errors(),402);
            // }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return [
                'estatus' => 'error_interno',
                "error"=>true,
                'message' => 'Ha ocurrido un error inesperado.',
            ];
        }
        }

        public function enviarNotificacion($notificacion){
            try {
                $usersAdmin = User::where('estatus','activo')->orderBy('users.id','desc')->with('roles')->whereHas('roles',function($q){
                    $q->where('name', 'Administrador');})->get();
                    $registrationIds = array();
                    Log::error('firebase '.count($usersAdmin));
                if(count($usersAdmin)>0){
                    foreach ($usersAdmin as $key => $value) {
                        if(!empty($value->tokenFb)){
                          array_push($registrationIds,$value->tokenFb);
                        }
                    }
                    Log::error('firebase '.count($registrationIds));
                    if(count($registrationIds)>0){
                        $msg = array
                    (
                    'body'  => $notificacion['mensaje'],
                    'title' => $notificacion['titulo'],
                    'sound' => 'default',
                    "click_action"=>"FCM_PLUGIN_ACTIVITY",
                    "icon"=>"fcm_push_icon"
                    );
                    $fields = array
                        (
                        'registration_ids'=> $registrationIds,
                        'notification'  => $msg,
                        "priority"=>"high",
                        "restricted_package_name"=>""
                        );
                    $headers = array
                        (
                        'Authorization: key=' . "AAAApLwtojs:APA91bFmTwk5nydgei-wxAUQUE3JskOCzSbwvFYoCceTqq6PIwzq5MT-kAh8qMCccLtnkT33mehjm_3z5uLjh8dncLzJ31ITv8XOndFMhFozRcHvu5YmVEskc4-ctYkpk-ewpjaF0wtw",
                        'Content-Type: application/json'
                        );
                    $AControlar='https://fcm.googleapis.com/fcm/send';
                    Log::error('firebase '.json_encode($fields));
                       // Initialize cURL.
                        $ch = curl_init();
                        // Check if initialization had gone wrong*
                          if ($ch === false) {
                              return false;
                          }

                              // Set the URL that you want to GET by using the CURLOPT_URL option.
                              curl_setopt($ch, CURLOPT_URL,$AControlar);

                              // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                              // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
                              curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                              curl_setopt( $ch,CURLOPT_POST, true );
                              curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                              curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode( $fields ));
                              // Execute the request.
                              $data = curl_exec($ch);
                              if(curl_errno($ch)){
                                  return false;
                              }
                              Log::error('firebase '.json_encode($data));
                              // Close the cURL handle.
                              curl_close($ch);
                    }

                }
                return true;
            } catch (\Exception $e) {
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
        public function analisisIdAnalizer(Request $request){
            //    public function ControlMail(Request $request){
                // Log::error('Request analizer =>'.json_encode($request->getContent()));
                try {
                    
                    if ($request->user()) {
                        $user = $request->user();
                        $evaluacion=Evaluacion::where('tokenAnalizer',$user->tokenAnalizer)->first();
                        $req = json_decode($request->getContent(),true);
                        if ($req['error'] === true) {
                            Notificaciones::create([
                                'idSolicitudFk' => $evaluacion->idSolicitudFk,
                                'idUserFk' =>$evaluacion->idUserFk,
                                'idEvaluacionFk' => $evaluacion->id,
                                'titulo'=>"Error analisis de analizer",
                                'mensaje' => "Error de conexion con verificaion de analizer",
                                'codigo' => '004'
                            ]);

                            self::enviarNotificacion(['titulo'=>"Error verificaion de analizer",
                            'mensaje' => "Error de conexion con verificaion de analizer, Codigo:004"]);
                            return response()->json(['url'=>'https://www.creditospanda.com/test/error','message' => 'success','error'=>false],201);
                        }

                       $solicitud=Calculadora::
                       where(function($query2) use($request) {
                            $query2->orWhere('calculadoras.estatus','pendiente')
                            ->OrWhere('calculadoras.estatus','incompleto');
                        })
                       ->where('idUserFk',$evaluacion->idUserFk)->where('id',$evaluacion->idSolicitudFk)->first();
                        //return response()->json($data);
                        if(($req['status'])!="aprobado"){
                            \DB::table('evaluacions')
                            ->where("evaluacions.id", $evaluacion->id)
                            ->update([
                                'estatus'    => 'negado',
                                'analizer'=> 'negado',
                                'resultadoAnalizer' => json_encode($req['result'])
                            ]);
                            $solicitud->estatus = "negado";
                            $solicitud->save();
                            $usuarios=User::where('id',$user->id)->first();
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
                             $name=$usuarios->first_name;
                             $last_name=$usuarios->last_name;
                             $email=$usuarios->email;
                             $cedula=$usuarios->n_document;
                             $content=$contenido->contenido;
                             $contentInvitacion=$contenido_invitacion->contenido;


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
                         }
                            return response()->json(['url'=>'https://www.creditospanda.com/test/error','message' => 'success','error'=>false],201);

                            // return ['negado',$data]; //email invalido
                        }else{
                            \DB::table('evaluacions')
                            ->where("evaluacions.id", $evaluacion->id)
                            ->update([
                                'estatus'    => 'pendiente',
                                'analizer'=> 'aprobado',
                                'resultadoAnalizer' => json_encode($req['result'])
                            ]);
                            $solicitud->estatus = "pendiente";
                            $solicitud->save();
                            return response()->json(['url'=>'https://www.creditospanda.com/test/correcto','message' => 'success','error'=>false],201);
                            // return ['aprobado',$data];//email valido
                        }
                    }else{
                        return response()->json([
                            'error'=>true,
                            'message' => 'Invalid token.',
                        ],201);
                    }
                    // return response()->json($res,201);
                }catch (\Exception $e) {
                    if($e instanceof ValidationException) {
                        return response()->json($e->errors(),402);
                    }
                    DB::rollback(); // Retrocedemos la transaccion
                    Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
                    return response()->json([
                        'error'=>true,
                        'message' => 'Unexpected error.',
                    ], 500);
                }

            }
}
