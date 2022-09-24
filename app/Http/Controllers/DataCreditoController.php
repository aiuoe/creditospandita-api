<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DcAlertas;
use App\Models\DcAnalisisIngresos;
use App\Models\DcEndeudamiento;
use App\Models\DcIndicadorPago;
use App\Models\DcPorSector;
use App\Models\DcSintesis;
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

class DataCreditoController extends Controller
{
	private $NAME_CONTROLLER = 'DataCreditoController';

	public function all(Request $request){
		try{
			$request->validate([
				'per_page'      =>  'nullable|integer',
				'page'          =>  'nullable|integer'
			]);  
			$per_page = (!empty($request->per_page)) ? $request->per_page : DcSintesis::count();
			$result = DcSintesis::paginate($per_page);
				 $response = $result;

			if($result->isEmpty())
			{
					$response = [
							'data' => [],
							'total' => 0,
							'msj' => 'No se encontraron registros.',
					];
					return response()->json($response, 200); 
			}
			return response()->json($response, 200);
		}
		catch (\Exception $e)
		{
			Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
			return response()->json([
				'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
			], 500);
		}
	}

	public function allAlertas(Request $request){
		try{
			$request->validate([
			'per_page'      =>  'nullable|integer',
			'page'          =>  'nullable|integer'
			]);  
			$per_page = (!empty($request->per_page)) ? $request->per_page : DcAlertas::count();
			$result = DcAlertas::where('idEvaluacion',$request->id)->paginate($per_page);
			$response = $result;

			if($result->isEmpty())
			{
				$response = [
				'data' => [],
				'total' => 0,
				'msj' => 'No se encontraron registros.',
				];
				return response()->json($response, 200); 
			}
			return response()->json($response, 200);
		}catch (\Exception $e)
		{
			Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
			return response()->json([
			'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
			], 500);
		}
	}

	public function allEndeudamientos(Request $request){
		try{
			$request->validate([
			'per_page'      =>  'nullable|integer',
			'page'          =>  'nullable|integer'
			]);  
			$per_page = (!empty($request->per_page)) ? $request->per_page : DcEndeudamiento::count();
			$result = DcEndeudamiento::where('idEvaluacion',$request->id)->paginate($per_page);
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
		}catch (\Exception $e)
		{
			Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
			return response()->json([
			'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
			], 500);
		}
	}

	public function allPorSector(Request $request){
			try{
					$request->validate([
							'per_page'      =>  'nullable|integer',
							'page'          =>  'nullable|integer'
					]);  
			$per_page = (!empty($request->per_page)) ? $request->per_page : DcPorSector::count();
			$result = DcPorSector::where('idEvaluacion',$request->id)->paginate($per_page);
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
					$result['sintesis'] = DcSintesis::where('idEvaluacion','=',$request->id)->first();
					$result['analisisIngresos'] = DcAnalisisIngresos::where('idEvaluacion','=',$request->id)->first();
					$result['indicadorPago'] = DcIndicadorPago::where('idEvaluacion','=',$request->id)->first();
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
					$result['sintesis'] = DcSintesis::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'estadoDocumento'     => $req['estadoDocumento'],
							'nroCedula'    => $req['nroCedula'],
							'fechaExpedicion'     => $req['fechaExpedicion'],
							'genero' => $req['genero'],
							'rangoEdad' => $req['rangoEdad']
					]);

					$result['analisisIngresos'] = DcAnalisisIngresos::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'situacionLaboral'     => $req['situacionLaboral'],
							'ingresoVsCuota'    => $req['ingresoVsCuota'],
							'ingresoEstimado'     => $req['ingresoEstimado']
					]);

					$result['indicadorPago'] = DcIndicadorPago::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'bajo'     => $req['bajo'],
							'medio'     => $req['medio'],
							'alto'     => $req['alto'],
							'puntajeBajo' => $req['puntajeBajo'],
							'puntajeMedio' => $req['puntajeMedio'],
							'puntajeAlto' => $req['puntajeAlto']
					]);


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

	public function storeAlerta(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$req = json_decode($request->getContent(), true);

					$result = DcAlertas::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'fuente'     => $req['fuente'],
							'fecha'    => $req['fecha'],
							'novedad'     => $req['novedad'],
							'descripcion' => $req['descripcion']
					]);

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

	public function storeEndeudamiento(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$req = json_decode($request->getContent(), true);

					$result = DcEndeudamiento::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'mes'     => $req['mes'],
							'mora'    => $req['mora']
					]);

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

	public function storePorSector(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$req = json_decode($request->getContent(), true);

					$result = DcPorSector::create([
							'idEvaluacion'    => $req['idEvaluacion'],
							'cupoInicial'     => $req['cupoInicial'],
							'saldoActual'    => $req['saldoActual'],
							'cuotaMensual'     => $req['cuotaMensual'],
							'gastosFamiliares' => $req['gastosFamiliares'],
							'saldoMora' => $req['saldoMora'],
							'disponibleMensual' => $req['disponibleMensual'],
							'disponibleEndeudamiento' => $req['disponibleEndeudamiento'],
							'ingresoMensual' => $req['ingresoMensual']
					]);

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
				
					$sintesis = DcSintesis::where('idEvaluacion',$request->idEvaluacion)->first();
					$sintesis->estadoDocumento  = $request->estadoDocumento;
					$sintesis->nroCedula=$request->nroCedula;
					$sintesis->fechaExpedicion=$request->fechaExpedicion;
					$sintesis->genero=$request->genero;
					$sintesis->rangoEdad=$request->rangoEdad;
					$sintesis->save();

					$analisisIngresos = DcAnalisisIngresos::where('idEvaluacion',$request->idEvaluacion)->first();
					$analisisIngresos->situacionLaboral  = $request->situacionLaboral;
					$analisisIngresos->ingresoVsCuota=$request->ingresoVsCuota;
					$analisisIngresos->ingresoEstimado=$request->ingresoEstimado;
					$analisisIngresos->save();

					$indicadorPago = DcIndicadorPago::where('idEvaluacion',$request->idEvaluacion)->first();
					$indicadorPago->bajo  = $request->bajo;
					$indicadorPago->medio  = $request->medio;
					$indicadorPago->alto  = $request->alto;
					$indicadorPago->puntajeBajo  = $request->puntajeBajo;
					$indicadorPago->puntajeMedio  = $request->puntajeMedio;
					$indicadorPago->puntajeAlto  = $request->puntajeAlto;
					$indicadorPago->save();

					DB::commit(); // Guardamos la transaccion

					return response()->json($sintesis,200);

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

	public function deleteAlerta(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$config = DcAlertas::find($request->id);
					$config->delete();
					DB::commit(); // Guardamos la transaccion
					return response()->json("Configuracion eliminada",200);
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

	public function deleteEndeudamiento(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$config = DcEndeudamiento::find($request->id);
					$config->delete();
					DB::commit(); // Guardamos la transaccion
					return response()->json("Configuracion eliminada",200);
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

	public function deletePorSector(Request $request){
			try{
					DB::beginTransaction(); // Iniciar transaccion de la base de datos
					$config = DcPorSector::find($request->id);
					$config->delete();
					DB::commit(); // Guardamos la transaccion
					return response()->json("Configuracion eliminada",200);
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

	public function soapconnection()
	{
		$rq = ["userName" => '2-901369753@demo.datacredito.com.co',
       "passWord" => 'Moshiach1870$$$$'];

		$url = "https://demo-servicesb.datacredito.com.co/wss/dhws3/services/DHServicePlus?wsdl";

		$context = stream_context_create(array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)
		));

		$client = new \SoapClient($url, array('stream_context' => $context));
		$client->RequestTransaction($rq);
		
		/*
		$context = stream_context_create(['ssl' => [
			'local_cert' => storage_path('app/private/privatekey.txt'),
			'local_pk' => storage_path('app/private/keypair.p12')
		],
		'http' => [
			'user_agent' => 'PHPSoapClient'
		]]);

		$wsdlUrl = 'https://demo-servicesb.datacredito.com.co/wss/dhws3/services/DHServicePlus?wsdl';
		$soapClientOptions = array(
			'stream_context' => $context,
			'cache_wsdl' => WSDL_CACHE_NONE
		);

		$client = new \SoapClient($wsdlUrl, $soapClientOptions);*/
	}
}
