<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Country;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Variables;
use App\Models\Atributos;
use App\Models\ContraOferta;
use Exception;
use Illuminate\Http\Request;
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
class ContraOfertaController extends Controller
{
    private $NAME_CONTROLLER = 'ContraOfertaController';

    public function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ContraOferta::count();
        $result = ContraOferta::paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'contra_ofertas' => [],
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

    public function aprobados(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::where('estatus', 'aprobado')->where('idUserFk', $request->idUser)->count();
        $result = Calculadora::where('estatus', 'aprobado')->where('idUserFk', $request->idUser)->paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'contra_ofertas' => [],
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

    public function preaprobados(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : Calculadora::where('estatus', 'preaprobado')->where('idUserFk', $request->idUser)->count();
        $result = Calculadora::where('estatus', 'preaprobado')->where('idUserFk', $request->idUser)->paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'contra_ofertas' => [],
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
            $result = ContraOferta::where('id','=',$request->id)->first();
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

    public function allUser(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ContraOferta::count();
        $result = ContraOferta::where('idUserFK','=',$request->idUser)->paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'data' => [],
                'total'=>0,
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
    public function allEstatus(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ContraOferta::count();
        $result = ContraOferta::where('idUserFK','=',$request->idUser)->where('estatus','=',$request->estatus)->paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'data' => [],
                'total'=>0,
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

    public function updateEstatus(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
          
            $contraOferta = ContraOferta::find($request->id);
            $contraOferta->estatus  = $request->estatus;
            if($request->estatus == 'aceptado'){
                if($request->fechaAceptado){
                    
                    $contraOferta->fechaAceptado = $request->fechaAceptado;
                }else{
                    $contraOferta->fechaAceptado = date('Y-m-d');
                }
            }
            $contraOferta->save();

            $solicitud = Calculadora::find($contraOferta->idCalculadoraFk);
            $solicitud->estatus_contraOferta  = $request->estatus;
            $solicitud->save();

            DB::commit(); // Guardamos la transaccion
 
            return response()->json($contraOferta,200);

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


    public function contraOfertaPendiente(Request $request){
        try{  
        // $per_page = (!empty($request->per_page)) ? $request->per_page : ContraOferta::count();
        if(ContraOferta::where('idUserFK','=',$request->id)->where('estatus','rechazado')->count() > 0){
            $co = ContraOferta::where('idUserFK','=',$request->id)->where('estatus','rechazado')->orderby('id','DESC')->first();
            $response = [
                'data' => $co,
                'total'=>1,
                'msj' => 'Hay una contraoferta pendiente',
            ];
        }else{
            $response = [
                'data' => [],
                'total'=>0,
                'msj' => 'no hay una contraoferta pendiente',
            ];
        }
         

   
        return response()->json($response, 200);
    }catch (\Exception $e) {
        Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
        ], 500);
    }
    }
    
}
