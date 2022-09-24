<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluacion;
use App\Models\User;
use App\Models\ExtractosBancarios;
use App\Models\ExtractosBancariosCreditos;
use App\Models\ExtractosBancariosPagos;
use App\Models\Basica;
use App\Models\Financiera;
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
use PDFt;
use Maatwebsite\Excel\Facades\Excel;

class ExtractosBancariosController extends Controller
{
    private $NAME_CONTROLLER = 'ExtractosBancariosController';

    public function get(Request $request){
        try{
            $result['extractosBancarios'] = ExtractosBancarios::where('idEvaluacion','=',$request->id)->first();
            
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

    public function getPagos(Request $request){
        try{
            
            $result['extractosBancariosPagos'] = ExtractosBancariosPagos::where('idEvaluacion','=',$request->id)->get();
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
    public function getCreditos(Request $request){
        try{
            
            $result['extractosBancariosCreditos'] = ExtractosBancariosCreditos::where('idEvaluacion','=',$request->id)->get();
            
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

    public function storeEB(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $req = json_decode($request->getContent(), true);
            if($req['id'] > 0){
                $query = ExtractosBancarios::find($req['id']);
                $query->idEvaluacion = $req['idEvaluacion'];
                $query->saldoAnterior = $req['saldoAnterior'];
                $query->totalAbonos = $req['totalAbonos'];
                $query->totalCargos = $req['totalCargos'];
                $query->saldoActual = $req['saldoActual'];
                $query->saldoPromedio = $req['saldoPromedio'];
                $query->salario = $req['salario'];
                $query->diasPago = $req['diasPago'];
                $query->nombreEmpresa = $req['nombreEmpresa'];
                $query->tipoContrato = $req['tipoContrato'];
                $query->antiguedadLaboral = $req['antiguedadLaboral'];
                $query->nombreCargo = $req['nombreCargo'];
                $query->valorTotalMensualCreditosActuales = $req['valorTotalMensualCreditosActuales'];
                $query->save();
                $result = $query;

            }else{
                $result = ExtractosBancarios::create([
                    'idEvaluacion'    => $req['idEvaluacion'],
                    'saldoAnterior'     => $req['saldoAnterior'],
                    'totalAbonos'    => $req['totalAbonos'],
                    'totalCargos'     => $req['totalCargos'],
                    'saldoActual' => $req['saldoActual'],
                    'saldoPromedio' => $req['saldoPromedio'],
                    'salario' => $req['salario'],
                    'diasPago' => $req['diasPago'],
                    'nombreEmpresa' => $req['nombreEmpresa'],
                    'tipoContrato' => $req['tipoContrato'],
                    'antiguedadLaboral' => $req['antiguedadLaboral'],
                    'nombreCargo' => $req['nombreCargo'],
                    'valorTotalMensualCreditosActuales'=>$req['valorTotalMensualCreditosActuales']
                ]);
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
        public function storeEBC(Request $request){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $req = json_decode($request->getContent(), true);
                    $result = ExtractosBancariosCreditos::create([
                        'idEvaluacion'    => $req['idEvaluacion'],
                        'fecha'     => $req['fecha'],
                        'empresa'    => $req['empresa'],
                        'ingresoPrestamo'     => $req['ingresoPrestamo'],
                        'cuotaCredito' => $req['cuotaCredito']
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

        public function storeEBP(Request $request){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $req = json_decode($request->getContent(), true);
                    $result = ExtractosBancariosPagos::create([
                        'idEvaluacion'    => $req['idEvaluacion'],
                        'fecha'     => $req['fecha'],
                        'concepto'    => $req['concepto'],
                        'valorIngreso'     => $req['valorIngreso'],
                        'totalMensual' => $req['totalMensual'],
                        'promedio' => $req['promedio']
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

        public function deletePagos(Request $request){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $config = ExtractosBancariosPagos::find($request->id);
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
        public function deleteCreditos(Request $request){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $config = ExtractosBancariosCreditos::find($request->id);
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
        public function updateEB(Request $request){
            try{
                DB::beginTransaction(); // Iniciar transaccion de la base de datos
                $req = json_decode($request->getContent(), true);
                if($req['id'] > 0){
                    $query = ExtractosBancarios::find($req['id']);
                    $query->tipoPagoNomina = $req['tipoPagoNomina'];
                    $query->save();
                    $result = $query;
    
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
}
